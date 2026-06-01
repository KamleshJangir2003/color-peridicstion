<?php

$merchantId = 'M258';
$secretKey  = '9e66e751433ef9dec6096a4dfaaddbc7';
$baseUrl    = 'https://www.mv-pay.org';

function generateSign(array $params, string $secretKey): string {
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
    $str .= 'key=' . $secretKey;
    return strtolower(md5($str));
}

function mvpayPost(string $url, array $params): string {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Test Payout
$params = [
    'merchant_id' => $merchantId,
    'no'          => 'TEST' . time(),
    'amount'      => '200',
    'account'     => '7780101437677',
    'name'        => 'kamlesh kumar',
    'ifsc_code'   => 'FDRL0007778',
    'notify_url'  => 'https://nighty1games.shop/api/payout/callback',
    'remark'      => 'test',
];
$params['sign'] = generateSign($params, $secretKey);

echo "=== PAYOUT TEST ===\n";
echo "Request: " . json_encode($params, JSON_PRETTY_PRINT) . "\n";
$response = mvpayPost($baseUrl . '/Transfer/replay', $params);
echo "Response: " . $response . "\n\n";

// Test Payment
$params2 = [
    'merchant_id' => $merchantId,
    'no'          => 'DEP' . time(),
    'amount'      => '200',
    'notify_url'  => 'https://nighty1games.shop/api/payment/callback',
    'return_url'  => 'https://nighty1games.shop',
    'remark'      => 'test deposit',
];
$params2['sign'] = generateSign($params2, $secretKey);

echo "=== PAYMENT TEST ===\n";
echo "Request: " . json_encode($params2, JSON_PRETTY_PRINT) . "\n";
$response2 = mvpayPost($baseUrl . '/Transfer/index', $params2);
echo "Response: " . $response2 . "\n";
