<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VNPayService
{
    public function buildPaymentUrl(PaymentTransaction $transaction, array $meta = []): string
    {
        $baseUrl = rtrim((string) config('vnpay.payment_url'), '/');
        $tmnCode = config('vnpay.tmn_code');
        $hashSecret = config('vnpay.hash_secret');

        if (!$baseUrl || !$tmnCode || !$hashSecret) {
            throw new \RuntimeException('Thiếu cấu hình VNPay (payment_url / tmn_code / hash_secret).');
        }

        $amount = (int) round($transaction->soTien * 100);

        $ip = $meta['ip_address'] ?? request()->ip();
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = '127.0.0.1';
        }

        $params = [
            'vnp_Version' => config('vnpay.version', '2.1.0'),
            'vnp_Command' => config('vnpay.command', 'pay'),
            'vnp_TmnCode' => $tmnCode,
            'vnp_Amount' => $amount,
            'vnp_CurrCode' => config('vnpay.currency_code', 'VND'),
            'vnp_TxnRef' => $transaction->txn_ref,
            'vnp_OrderInfo' => $meta['order_info'] ?? ('Thanh toán đơn #' . ($transaction->maHD ?? $transaction->id)),
            'vnp_OrderType' => $meta['order_type'] ?? config('vnpay.order_type', 'other'),
            'vnp_Locale' => $meta['locale'] ?? config('vnpay.locale', 'vn'),
            'vnp_ReturnUrl' => $meta['return_url'] ?? $this->resolveReturnUrl(),
            'vnp_IpAddr' => $meta['ip_address'] ?? request()->ip(),
            'vnp_CreateDate' => Carbon::now()->format('YmdHis'),
        ];

        if (!empty($meta['bank_code'])) {
            $params['vnp_BankCode'] = $meta['bank_code'];
        }

        $expireMinutes = (int) ($meta['expire_minutes'] ?? config('vnpay.expire_minutes', 15));
        if ($expireMinutes > 0) {
            $params['vnp_ExpireDate'] = Carbon::now()->addMinutes($expireMinutes)->format('YmdHis');
        }

        ksort($params);

        [$hashData, $queryString] = $this->buildHashAndQuery($params);
        $secureHash = hash_hmac('sha512', $hashData, (string) $hashSecret);

        return $baseUrl . '?' . $queryString . '&vnp_SecureHash=' . $secureHash;
    }

    public function normalizeRequestData(Request $request): array
    {
        return $request->except('vnp_SecureHash', 'vnp_SecureHashType');
    }
    
    private function buildHashData(array $params): string
    {
        $pairs = [];

        foreach ($params as $key => $value) {
            $pairs[] = $key . '=' . urlencode((string) $value);
        }

        return implode('&', $pairs);
    }

    public function verifySignature(array $data, ?string $providedHash): bool
    {
        if (!$providedHash) {
            return false;
        }

        $hashSecret = config('vnpay.hash_secret');

        if (!$hashSecret) {
            return false;
        }

        ksort($data);

        $hashData = $this->buildHashData($data);
        $calculated = hash_hmac('sha512', $hashData, $hashSecret);

        return hash_equals($calculated, $providedHash);
    }

    public function resolveReturnUrl(): string
    {
        $configured = config('vnpay.return_url');

        if ($configured) {
            return $configured;
        }

        return route('payment.vnpay.return');
    }

    private function buildHashAndQuery(array $params): array
    {
        $hashParts = [];
        $queryParts = [];

        foreach ($params as $key => $value) {
            $encodedKey = urlencode((string) $key);
            $encodedValue = urlencode((string) $value);

            $hashParts[] = $encodedKey . '=' . $encodedValue;
            $queryParts[] = $encodedKey . '=' . $encodedValue;
        }

        return [
            implode('&', $hashParts),
            implode('&', $queryParts),
        ];
    }
}
