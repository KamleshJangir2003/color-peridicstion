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
    foreach ($filtered as $k => $v) { $str .= $k . '=' . $v . '&'; }
    $str .= 'key=' . $secretKey;
    return strtolower(md5($str));
}

function mvpayPost($url, $params) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}

$no = 'DEP' . time();

// Test A: sign only core fields, send all
$signFields = ['merchant_id' => $merchantId, 'no' => $no, 'amount' => '200', 'remark' => 'test'];
$allParams = array_merge($signFields, [
    'notify_url' => 'https://nighty1games.shop/api/payment/callback',
    'return_url' => 'https://nighty1games.shop',
]);
$allParams['sign'] = generateSign($signFields, $secretKey);
echo "=== A: sign without notify/return url ===\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $allParams) . "\n\n";

// Test B: sign only merchant_id + no + amount
$signFields2 = ['merchant_id' => $merchantId, 'no' => $no.'B', 'amount' => '200'];
$allParams2 = array_merge($signFields2, [
    'notify_url' => 'https://nighty1games.shop/api/payment/callback',
    'return_url' => 'https://nighty1games.shop',
    'remark'     => 'test',
]);
$allParams2['sign'] = generateSign($signFields2, $secretKey);
echo "=== B: sign only merchant+no+amount ===\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $allParams2) . "\n\n";

// Test C: all fields including urls in sign
$allFields = ['merchant_id' => $merchantId, 'no' => $no.'C', 'amount' => '200', 'remark' => 'test',
    'notify_url' => 'https://nighty1games.shop/api/payment/callback',
    'return_url' => 'https://nighty1games.shop'];
$allFields['sign'] = generateSign($allFields, $secretKey);
echo "=== C: all fields in sign ===\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $allFields) . "\n";
