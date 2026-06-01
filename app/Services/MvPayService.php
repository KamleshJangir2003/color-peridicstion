<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MvPayService
{
    private string $merchantId;
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.mvpay.merchant_id');
        $this->secretKey  = config('services.mvpay.secret_key');
        $this->baseUrl    = config('services.mvpay.base_url');
    }

    // Sign Rule: skip empty+sign, trim, ASCII sort, key=value& concat, append key=SECRET, MD5 lowercase
    public function generateSign(array $params): string
    {
        $filtered = [];
        foreach ($params as $k => $v) {
            if ($k === 'sign') continue;
            $v = trim((string) $v);
            if ($v === '') continue;
            $filtered[$k] = $v;
        }
        ksort($filtered);
        $str = '';
        foreach ($filtered as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $str .= 'key=' . $this->secretKey;
        return strtolower(md5($str));
    }

    public function verifySign(array $params): bool
    {
        $receivedSign = $params['sign'] ?? '';
        unset($params['sign']);
        return $this->generateSign($params) === strtolower($receivedSign);
    }

    // POST /Transfer/index - Create Payment (Deposit)
    // Sign fields: merchant_id, no, amount ONLY
    public function createPayment(array $data): array
    {
        $signParams = [
            'merchant_id' => $this->merchantId,
            'no'          => (string) $data['order_id'],
            'amount'      => (string) (int) $data['amount'],
        ];
        $params = array_merge($signParams, [
            'notify_url' => url('/api/payment/callback'),
            'return_url' => $data['return_url'] ?? url('/deposit'),
            'remark'     => $data['remark'] ?? 'Deposit',
        ]);
        $params['sign'] = $this->generateSign($signParams);

        return $this->post('/Transfer/index', $params);
    }

    // POST /Transfer/getorderstatus - Query Payment Status
    public function queryPayment(string $orderId): array
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'no'          => $orderId,
        ];
        $params['sign'] = $this->generateSign($params);

        return $this->post('/Transfer/getorderstatus', $params);
    }

    // POST /Transfer/replay - Create Payout (Withdrawal)
    // Sign fields: merchant_id, no, amount ONLY
    public function createPayout(array $data): array
    {
        $signParams = [
            'merchant_id' => $this->merchantId,
            'no'          => (string) $data['order_id'],
            'amount'      => (string) (int) $data['amount'],
        ];
        $params = array_merge($signParams, [
            'account'    => $data['bank_account'],
            'ifsc'       => $data['bank_ifsc'] ?? '',
            'name'       => $data['account_name'],
            'notify_url' => url('/api/payout/callback'),
            'remark'     => $data['remark'] ?? 'Withdrawal',
        ]);
        $params['sign'] = $this->generateSign($signParams);

        return $this->post('/Transfer/replay', $params);
    }

    // POST /Transfer/repayorderstatus - Query Payout Status
    public function queryPayout(string $orderId): array
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'no'          => $orderId,
        ];
        $params['sign'] = $this->generateSign($params);

        return $this->post('/Transfer/repayorderstatus', $params);
    }

    // POST /Transfer/balance - Check Merchant Balance
    public function getBalance(): array
    {
        $params = [
            'merchant_id' => $this->merchantId,
        ];
        $params['sign'] = $this->generateSign($params);

        return $this->post('/Transfer/balance', $params);
    }

    // POST /Transfer/matchbyutr - UTR Match
    public function matchByUtr(string $utr, string $amount): array
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'utr'         => $utr,
            'amount'      => $amount,
        ];
        $params['sign'] = $this->generateSign($params);

        return $this->post('/Transfer/matchbyutr', $params);
    }

    private function post(string $endpoint, array $params): array
    {
        try {
            $response = Http::timeout(30)
                ->asForm()
                ->post($this->baseUrl . $endpoint, $params);

            Log::info('MvPay Request', ['endpoint' => $endpoint, 'params' => $params]);
            Log::info('MvPay Response', ['body' => $response->body()]);

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('MvPay Error', ['endpoint' => $endpoint, 'error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
