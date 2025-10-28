<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class VNPayController extends Controller
{
    // 1) User bấm Thanh toán
    public function createPayment(Request $request)
    {
        // Ví dụ: lấy thông tin khóa học và user hiện tại
        $userId    = auth()->id();
        $courseId  = $request->input('course_id');
        $amountVnd = $request->input('amount'); // vd 499000 (VND)

        // Tạo giao dịch pending trong DB
        $txnRef = strtoupper(Str::random(12)); // mã duy nhất gửi sang VNPay
        $order = PaymentTransaction::create([
            'user_id'    => $userId,
            'course_id'  => $courseId,
            'amount'     => $amountVnd,
            'txn_ref'    => $txnRef,
            'status'     => 'pending',
        ]);

        // Build params gửi sang VNPay
        $vnp_TmnCode    = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url        = config('vnpay.payment_url');
        $vnp_ReturnUrl  = config('vnpay.return_url');

        $inputData = [
            'vnp_Version'    => config('vnpay.version', '2.1.0'),
            'vnp_Command'    => config('vnpay.command', 'pay'),
            'vnp_TmnCode'    => $vnp_TmnCode,
            // VNPay yêu cầu số tiền nhân 100 để bỏ phần thập phân
            // Ví dụ 499000 VND -> 49900000
            // (đây là yêu cầu chính thức trong tài liệu VNPay) :contentReference[oaicite:14]{index=14}
            'vnp_Amount'     => $amountVnd * 100,
            'vnp_CurrCode'   => config('vnpay.currency_code', 'VND'),
            'vnp_TxnRef'     => $txnRef, // phải duy nhất
            'vnp_OrderInfo'  => 'Thanh toan khoa hoc #' . $courseId,
            'vnp_OrderType'  => 'edu', // hoặc "other", VNPay dùng để phân loại hàng hóa/dịch vụ
            'vnp_Locale'     => 'vn',
            'vnp_IpAddr'     => $request->ip(),
            'vnp_ReturnUrl'  => $vnp_ReturnUrl,
            'vnp_CreateDate' => now()->format('YmdHis'),
            // có thể thêm vnp_ExpireDate (yyyyMMddHHmmss) nếu muốn set timeout thanh toán
        ];

        // Ký HMAC SHA512 theo hướng dẫn VNPay:
        // - sort key theo thứ tự tăng dần
        // - nối "key=value" bằng "&"
        // - dùng hash_hmac('sha512', data, secret)
        ksort($inputData);
        $query = [];
        foreach ($inputData as $key => $value) {
            $query[] = $key . '=' . urlencode($value);
        }
        $hashData = implode('&', $query);
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $paymentUrl = $vnp_Url . '?' . $hashData . '&vnp_SecureHash=' . $vnp_SecureHash;

        // Redirect user qua VNPay để thanh toán
        return redirect($paymentUrl);
    }

    // 2) VNPay redirect người dùng về sau thanh toán xong
    //    -> dùng để hiển thị "Cảm ơn, thanh toán thành công/thất bại"
    //    KHÔNG update trạng thái đơn ở đây (chỉ đọc từ DB)
    //    VNPay tài liệu khuyến cáo update trạng thái ở IPN vì ReturnUrl nằm phía user nên dễ bị can thiệp. :contentReference[oaicite:15]{index=15}
    public function return(Request $request)
    {
        $txnRef = $request->input('vnp_TxnRef');
        $order = PaymentTransaction::where('txn_ref', $txnRef)->first();

        if (!$order) {
            $message = 'Không tìm thấy giao dịch.';
            $status  = 'error';
        } else {
            if ($order->status === 'paid') {
                $message = 'Thanh toán thành công. Khóa học đã được mở.';
                $status  = 'success';
            } elseif ($order->status === 'failed') {
                $message = 'Thanh toán thất bại hoặc bị hủy.';
                $status  = 'error';
            } else {
                // pending -> IPN có thể chưa callback kịp
                $message = 'Hệ thống đang xác nhận thanh toán. Vui lòng kiểm tra lịch sử khóa học.';
                $status  = 'pending';
            }
        }

        // Trả view blade tùy bạn
        return view('payment.result', compact('status', 'message', 'order'));
    }

    // 3) IPN: VNPay gọi thẳng vào server bạn để báo kết quả thanh toán chính thức
    //    Bạn phải:
    //    - verify chữ ký (vnp_SecureHash)
    //    - check đúng số tiền, đúng txn_ref
    //    - nếu vnp_ResponseCode == '00' thì mark paid
    //    - trả JSON {RspCode:"00","Message":"Confirm Success"} để VNPay dừng retry. :contentReference[oaicite:16]{index=16}
    public function ipn(Request $request)
    {
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        // Lấy toàn bộ query trừ SecureHash để verify
        $data = $request->except('vnp_SecureHash', 'vnp_SecureHashType');
        ksort($data);

        $hashPieces = [];
        foreach ($data as $key => $value) {
            $hashPieces[] = $key . '=' . urlencode($value);
        }
        $hashData = implode('&', $hashPieces);

        $calcHash = hash_hmac('sha512', $hashData, config('vnpay.hash_secret'));

        if ($calcHash !== $vnp_SecureHash) {
            // Sai chữ ký => báo VNPay biết là "97" (checksum sai)
            // VNPay định nghĩa rspCode 97 là chữ ký không hợp lệ. :contentReference[oaicite:17]{index=17}
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid signature',
            ]);
        }

        $txnRef           = $request->input('vnp_TxnRef');
        $vnpAmount        = (int) $request->input('vnp_Amount'); // nhớ là đã *100
        $vnpResponseCode  = $request->input('vnp_ResponseCode'); // '00' = success
        $vnpTransactionNo = $request->input('vnp_TransactionNo'); // mã giao dịch bên VNPay

        $order = PaymentTransaction::where('txn_ref', $txnRef)->first();

        if (!$order) {
            // 01: không tìm thấy mã đơn hàng (VNPay docs liệt kê lỗi 01 / 04 / 97... cho IPN) :contentReference[oaicite:18]{index=18}
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Order not found',
            ]);
        }

        // Kiểm tra số tiền: VNPay gửi amount *100
        if ($vnpAmount != $order->amount * 100) {
            return response()->json([
                'RspCode' => '04',
                'Message' => 'Invalid amount',
            ]);
        }

        // Nếu đơn chưa xử lý
        if ($order->status === 'pending') {
            if ($vnpResponseCode === '00') {
                // Thanh toán thành công
                $order->status = 'paid';
                $order->paid_at = Carbon::now();
            } else {
                // Thất bại
                $order->status = 'failed';
            }

            $order->vnp_response_code = $vnpResponseCode;
            $order->vnp_transaction_no = $vnpTransactionNo;
            $order->save();

            // TODO: mở khóa khóa học cho user ở đây
            // ví dụ: Enroll user vào course_id của order
        }

        // Báo lại cho VNPay là đã ghi nhận. RspCode "00" = thành công ghi nhận IPN.
        // VNPay dùng mã phản hồi IPN để quyết định có retry nữa hay không. :contentReference[oaicite:19]{index=19}
        return response()->json([
            'RspCode' => '00',
            'Message' => 'Confirm Success',
        ]);
    }
}
