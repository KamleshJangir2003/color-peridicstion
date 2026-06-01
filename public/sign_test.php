<?php
$secretKey = '9e66e751433ef9dec6096a4dfaaddbc7';
$params = [
    'merchant_id' => 'M258',
    'no'          => 'DEP123',
    'amount'      => '200',
    'notify_url'  => 'https://nighty1games.shop/api/payment/callback',
    'return_url'  => 'https://nighty1games.shop',
    'remark'      => 'test deposit',
];

ksort($params);

// Method 1: key=value concat
$parts = [];
foreach ($params as $k => $v) { $parts[] = $k . '=' . $v; }
$str1 = implode('&', $parts) . '&key=' . $secretKey;
echo 'M1 str: ' . $str1 . "\n";
echo 'M1 sign: ' . strtolower(md5($str1)) . "\n\n";

// Method 2: urldecode http_build_query
$str2 = urldecode(http_build_query($params)) . '&key=' . $secretKey;
echo 'M2 str: ' . $str2 . "\n";
echo 'M2 sign: ' . strtolower(md5($str2)) . "\n\n";

// Method 3: http_build_query raw
$str3 = http_build_query($params) . '&key=' . $secretKey;
echo 'M3 str: ' . $str3 . "\n";
echo 'M3 sign: ' . strtolower(md5($str3)) . "\n";
