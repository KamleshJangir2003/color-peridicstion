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
        $str = urldecode(http_build_query($filtered));
        $str .= '&key=' . $this->secretKey;
        return strtolower(md5($str));
    }

    public function verifySign(array $params, string $receivedSign = ''): bool
    {
        return $this->generateSign($params) === strtolower($receivedSign);
    }

    // POST /Transfer/index - Create Payment (Deposit)
    public function createPayment(array $data): array
    {
        $no     = (string) $data['order_id'];
        $amount = (string) (int) $data['amount'];

        $sign = $this->generateSign([
            'merchant_id' => $this->merchantId,
            'no'          => $no,
            'amount'      => $amount,
        ]);

        return $this->post('/Transfer/index', [
            'merchant_id' => $this->merchantId,
            'no'          => $no,
            'amount'      => $amount,
            'notify_url'  => url('/api/payment/callback'),
            'return_url'  => $data['return_url'] ?? url('/deposit'),
            'remark'      => $data['remark'] ?? 'Deposit',
            'sign'        => $sign,
        ]);
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
    public function createPayout(array $data): array
    {
        $no     = (string) $data['order_id'];
        $amount = (string) (int) $data['amount'];

        $sign = $this->generateSign([
            'merchant_id' => $this->merchantId,
            'no'          => $no,
            'amount'      => $amount,
        ]);

        return $this->post('/Transfer/replay', [
            'merchant_id' => $this->merchantId,
            'no'          => $no,
            'amount'      => $amount,
            'account'     => $data['bank_account'],
            'name'        => $data['account_name'],
            'ifsc_code'   => $data['bank_ifsc'],
            'notify_url'  => url('/api/payout/callback'),
            'remark'      => $data['remark'] ?? 'Withdrawal',
            'sign'        => $sign,
        ]);
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
