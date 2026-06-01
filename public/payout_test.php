<?php
$merchantId = 'M258';
$secretKey  = '9e66e751433ef9dec6096a4dfaaddbc7';
$baseUrl    = 'https://www.mv-pay.org';

$no = 'WD' . time();

// Sign: only merchant_id + no + amount + account + name + ifsc_code
$signFields = [
    'merchant_id' => $merchantId,
    'no'          => $no,
    'amount'      => '200',
    'account'     => 'test@upi',
    'name'        => 'Test User',
    'ifsc_code'   => 'UPI',
];
ksort($signFields);
$str = '';
foreach ($signFields as $k => $v) { $str .= $k . '=' . $v . '&'; }
$str .= 'key=' . $secretKey;
$sign = strtolower(md5($str));

$payload = array_merge($signFields, [
    'notify_url' => 'https://nighty1games.shop/api/payout/callback',
    'remark'     => 'Withdrawal test',
    'sign'       => $sign,
]);

echo "Sign string: $str\n";
echo "Sign: $sign\n";
echo "Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n";

$ch = curl_init($baseUrl . '/Transfer/replay');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
echo "Response: $response\n";
