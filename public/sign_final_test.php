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

$params = [
    'merchant_id' => $merchantId,
    'no'          => 'DEP' . time(),
    'amount'      => '200',
    'notify_url'  => 'https://nighty1games.shop/api/payment/callback',
    'return_url'  => 'https://nighty1games.shop',
    'remark'      => 'test',
];
$params['sign'] = generateSign($params, $secretKey);

echo "Sign string check:\n";
$tmp = $params; unset($tmp['sign']);
ksort($tmp);
$s = '';
foreach ($tmp as $k => $v) { $s .= $k . '=' . $v . '&'; }
$s .= 'key=' . $secretKey;
echo $s . "\n\n";

echo "Sign: " . $params['sign'] . "\n";
echo "Response: " . mvpayPost($baseUrl . '/Transfer/index', $params) . "\n";
