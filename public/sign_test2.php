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

$no = 'DEP' . time();
$baseParams = [
    'merchant_id' => $merchantId,
    'no'          => $no,
    'amount'      => '200',
    'notify_url'  => 'https://nighty1games.shop/api/payment/callback',
    'return_url'  => 'https://nighty1games.shop',
    'remark'      => 'test',
];

// Try Method A: raw key=value (no url encoding)
ksort($baseParams);
$parts = [];
foreach ($baseParams as $k => $v) { $parts[] = $k . '=' . $v; }
$strA = implode('&', $parts) . '&key=' . $secretKey;
$paramsA = $baseParams;
$paramsA['sign'] = strtolower(md5($strA));
echo "=== Method A (raw concat) ===\n";
echo "Sign string: $strA\n";
echo "Sign: " . $paramsA['sign'] . "\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $paramsA) . "\n\n";

// Try Method B: URL encoded
ksort($baseParams);
$strB = http_build_query($baseParams) . '&key=' . $secretKey;
$paramsB = $baseParams;
$paramsB['sign'] = strtolower(md5($strB));
echo "=== Method B (url encoded) ===\n";
echo "Sign string: $strB\n";
echo "Sign: " . $paramsB['sign'] . "\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $paramsB) . "\n";
