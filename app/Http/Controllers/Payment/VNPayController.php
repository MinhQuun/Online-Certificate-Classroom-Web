<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\CheckoutController;
use App\Models\Combo;
use App\Models\Course;
use App\Models\PaymentTransaction;
use App\Services\CheckoutOrderService;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class VNPayController extends Controller
{
    public function __construct(
        private readonly VNPayService $vnPayService,
        private readonly CheckoutOrderService $orderService
    ) {
    }

    public function return(Request $request)
    {
        $payload = $this->vnPayService->normalizeRequestData($request);
        $secureHash = $request->input('vnp_SecureHash');

        if (!$this->vnPayService->verifySignature($payload, $secureHash)) {
            return redirect()
                ->route('student.checkout.index', ['stage' => 2])
                ->with('error', 'VNPay xác thực thất bại. Vui lòng thử lại.');
        }

        $txnRef = $payload['vnp_TxnRef'] ?? null;
        $transaction = PaymentTransaction::with(['student.user'])
            ->where('txn_ref', $txnRef)
            ->first();

        if (!$transaction) {
            return redirect()
                ->route('student.checkout.index', ['stage' => 2])
                ->with('error', 'Không tìm thấy giao dịch VNPay tương ứng.');
        }

        if (($payload['vnp_ResponseCode'] ?? null) !== '00') {
            $this->markTransactionFailed($transaction, $payload);

            return redirect()
                ->route('student.checkout.index', ['stage' => 2])
                ->with('error', 'VNPay đã từ chối giao dịch (mã ' . ($payload['vnp_ResponseCode'] ?? 'N/A') . ').');
        }

        try {
            $result = DB::transaction(function () use ($transaction, $payload) {
                $locked = PaymentTransaction::with(['student.user'])
                    ->lockForUpdate()
                    ->find($transaction->id);

                if (!$locked) {
                    throw new \RuntimeException('Không thể khoá giao dịch VNPay.');
                }

                return $this->finalizePaidTransaction($locked, $payload);
            });
        } catch (Throwable $exception) {
            Log::error('VNPay return finalize failed', [
                'transaction_id' => $transaction->id,
                'message' => $exception->getMessage(),
            ]);
            report($exception);

            return redirect()
                ->route('student.checkout.index', ['stage' => 2])
                ->with('error', 'Hệ thống đang xử lý giao dịch. Vui lòng kiểm tra lại sau ít phút.');
        }

        session()->forget(CheckoutController::SESSION_SELECTION);
        session()->put(CheckoutController::SESSION_SUCCESS, $result['success_payload']);

        return redirect()
            ->route('student.checkout.index', ['stage' => 3])
            ->with('success', 'Thanh toán VNPay thành công.');
    }

    public function ipn(Request $request)
    {
        $payload = $this->vnPayService->normalizeRequestData($request);
        $secureHash = $request->input('vnp_SecureHash');

        if (!$this->vnPayService->verifySignature($payload, $secureHash)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $txnRef = $payload['vnp_TxnRef'] ?? null;

        try {
            return DB::transaction(function () use ($txnRef, $payload) {
                $transaction = PaymentTransaction::with(['student.user'])
                    ->lockForUpdate()
                    ->where('txn_ref', $txnRef)
                    ->first();

                if (!$transaction) {
                    return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
                }

                if ((int) ($payload['vnp_Amount'] ?? 0) !== (int) round($transaction->soTien * 100)) {
                    return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
                }

                if (($payload['vnp_ResponseCode'] ?? null) === '00') {
                    $this->finalizePaidTransaction($transaction, $payload);

                    return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
                }

                $this->markTransactionFailed($transaction, $payload);

                return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
            });
        } catch (Throwable $exception) {
            Log::error('VNPay IPN finalize failed', [
                'txn_ref' => $txnRef,
                'message' => $exception->getMessage(),
            ]);
            report($exception);

            return response()->json(['RspCode' => '99', 'Message' => 'Unknown error']);
        }
    }

    private function finalizePaidTransaction(PaymentTransaction $transaction, array $payload): array
    {
        $snapshot = $transaction->order_snapshot ?? [];

        $hydrated = $this->hydrateSnapshotCollections($snapshot);
        $courses = $hydrated['courses'];
        $combos = $hydrated['combos'];
        $overrides = $hydrated['overrides'];

        if ($transaction->trangThai !== PaymentTransaction::STATUS_PAID) {
            $finalized = $this->orderService->finalize(
                $transaction->student->user,
                $courses,
                $combos,
                'vnpay',
                $overrides
            );

            $transaction->trangThai = PaymentTransaction::STATUS_PAID;
            $transaction->paid_at = now();
            $transaction->maHD = $finalized['invoice']->maHD ?? null;
            $transaction->vnp_response_code = $payload['vnp_ResponseCode'] ?? null;
            $transaction->vnp_transaction_no = $payload['vnp_TransactionNo'] ?? null;

            $snapshot['already_active_courses'] = $finalized['already_active_courses'] ?? [];
            $snapshot['course_total'] = $snapshot['course_total'] ?? ($finalized['course_total'] ?? 0);
            $snapshot['combo_total'] = $snapshot['combo_total'] ?? ($finalized['combo_total'] ?? 0);

            $transaction->order_snapshot = $snapshot;
            $transaction->save();
        } else {
            $finalized = [
                'invoice' => $transaction->invoice,
                'already_active_courses' => $snapshot['already_active_courses'] ?? [],
                'course_total' => $snapshot['course_total'] ?? 0,
                'combo_total' => $snapshot['combo_total'] ?? 0,
                'user' => $transaction->student->user,
                'student' => $transaction->student,
            ];
        }

        $courseTotal = (int) ($snapshot['course_total'] ?? $finalized['course_total'] ?? 0);
        $comboTotal = (int) ($snapshot['combo_total'] ?? $finalized['combo_total'] ?? 0);

        $successPayload = CheckoutController::createSuccessPayload(
            $courses,
            $combos,
            $courseTotal,
            $comboTotal,
            'vnpay',
            $finalized
        );

        return [
            'status' => PaymentTransaction::STATUS_PAID,
            'success_payload' => $successPayload,
        ];
    }

    private function markTransactionFailed(PaymentTransaction $transaction, array $payload): void
    {
        if ($transaction->trangThai === PaymentTransaction::STATUS_PAID) {
            return;
        }

        $transaction->update([
            'trangThai' => PaymentTransaction::STATUS_FAILED,
            'vnp_response_code' => $payload['vnp_ResponseCode'] ?? null,
            'vnp_transaction_no' => $payload['vnp_TransactionNo'] ?? null,
        ]);
    }

    private function hydrateSnapshotCollections(array $snapshot): array
    {
        $courseEntries = collect($snapshot['courses'] ?? []);
        $comboEntries = collect($snapshot['combos'] ?? []);

        $comboCourseMap = $comboEntries
            ->mapWithKeys(function ($entry) {
                $comboId = $entry['maGoi'] ?? null;
                if (!$comboId) {
                    return [];
                }

                return [
                    (int) $comboId => array_map('intval', $entry['course_ids'] ?? []),
                ];
            });

        $courseIds = $courseEntries->pluck('maKH')
            ->merge($comboCourseMap->flatten())
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $courseModels = Course::query()
            ->whereIn('maKH', $courseIds)
            ->get()
            ->keyBy('maKH');

        $courseCollection = collect();
        foreach ($courseIds as $courseId) {
            if ($model = $courseModels->get($courseId)) {
                $courseCollection->push($model);
            }
        }

        $comboIds = $comboEntries->pluck('maGoi')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $comboModels = Combo::query()
            ->with(['courses' => function ($query) use ($courseIds) {
                if ($courseIds->isNotEmpty()) {
                    $query->whereIn('maKH', $courseIds);
                }
            }])
            ->whereIn('maGoi', $comboIds)
            ->get()
            ->keyBy('maGoi');

        $combos = collect();

        foreach ($comboEntries as $entry) {
            $comboId = isset($entry['maGoi']) ? (int) $entry['maGoi'] : null;
            if (!$comboId) {
                continue;
            }

            $combo = $comboModels->get($comboId) ?? new Combo([
                'maGoi' => $comboId,
                'tenGoi' => $entry['tenGoi'] ?? 'Combo #' . $comboId,
                'slug' => $entry['slug'] ?? null,
            ]);

            $courseIdsForCombo = array_map('intval', $entry['course_ids'] ?? []);
            $coursesForCombo = collect($courseIdsForCombo)
                ->map(fn ($courseId) => $courseModels->get($courseId))
                ->filter()
                ->values();

            $combo->setRelation('courses', $coursesForCombo);

            $combos->push($combo);
        }

        $coursePriceOverrides = $courseEntries
            ->filter(fn ($item) => isset($item['maKH']))
            ->mapWithKeys(fn ($item) => [(int) $item['maKH'] => (int) ($item['hocPhi'] ?? 0)])
            ->all();

        $comboPriceOverrides = $comboEntries
            ->filter(fn ($item) => isset($item['maGoi']))
            ->mapWithKeys(fn ($item) => [(int) $item['maGoi'] => (int) ($item['sale_price'] ?? 0)])
            ->all();

        $comboPromotionOverrides = $comboEntries
            ->filter(fn ($item) => isset($item['maGoi']))
            ->mapWithKeys(fn ($item) => [(int) $item['maGoi'] => $item['promotion_id'] ?? null])
            ->all();

        return [
            'courses' => $courseCollection,
            'combos' => $combos,
            'overrides' => [
                'course_price_overrides' => $coursePriceOverrides,
                'combo_price_overrides' => $comboPriceOverrides,
                'combo_promotion_overrides' => $comboPromotionOverrides,
            ],
        ];
    }
}
