<?php
$merchantId = 'M258';
$secretKey  = '9e66e751433ef9dec6096a4dfaaddbc7';
$baseUrl    = 'https://www.mv-pay.org';

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

function sign($params, $secretKey) {
    ksort($params);
    $parts = [];
    foreach ($params as $k => $v) { $parts[] = $k . '=' . $v; }
    return strtolower(md5(implode('&', $parts) . '&key=' . $secretKey));
}

$no = 'DEP' . time();

// Test 1: Only required fields in sign (no notify/return url)
$signParams = ['merchant_id' => $merchantId, 'no' => $no, 'amount' => '200', 'remark' => 'test'];
$allParams = array_merge($signParams, [
    'notify_url' => 'https://nighty1games.shop/api/payment/callback',
    'return_url' => 'https://nighty1games.shop',
    'sign' => sign($signParams, $secretKey)
]);
echo "=== Test1: sign without URLs ===\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $allParams) . "\n\n";

// Test 2: All fields in sign
$allFields = ['merchant_id' => $merchantId, 'no' => $no.'A', 'amount' => '200', 'remark' => 'test',
    'notify_url' => 'https://nighty1games.shop/api/payment/callback',
    'return_url' => 'https://nighty1games.shop'];
$allFields['sign'] = sign($allFields, $secretKey);
echo "=== Test2: all fields in sign ===\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $allFields) . "\n\n";

// Test 3: Only merchant_id + no + amount
$minParams = ['merchant_id' => $merchantId, 'no' => $no.'B', 'amount' => '200'];
$minParams2 = array_merge($minParams, [
    'notify_url' => 'https://nighty1games.shop/api/payment/callback',
    'return_url' => 'https://nighty1games.shop',
    'remark' => 'test',
    'sign' => sign($minParams, $secretKey)
]);
echo "=== Test3: only merchant+no+amount in sign ===\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $minParams2) . "\n";
