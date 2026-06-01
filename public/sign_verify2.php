<?php
$secretKey = 'abcDEF123456';

// Doc example has "merchant_id= M10001" with space - test without trim
$params = [
    'merchant_id' => ' M10001',  // space as in docs
    'amount'      => '1000',
    'no'          => '20250808123456',
];

ksort($params);
$str = '';
foreach ($params as $k => $v) {
    $str .= $k . '=' . $v . '&';
}
$str .= 'key=' . $secretKey;
$sign = strtolower(md5($str));
echo "With space - String: $str\n";
echo "Sign: $sign\n\n";

// Without space, no trim
$params2 = [
    'merchant_id' => 'M10001',
    'amount'      => '1000',
    'no'          => '20250808123456',
];
ksort($params2);
$str2 = '';
foreach ($params2 as $k => $v) {
    $str2 .= $k . '=' . $v . '&';
}
$str2 .= 'key=' . $secretKey;
$sign2 = strtolower(md5($str2));
echo "Without space - String: $str2\n";
echo "Sign: $sign2\n\n";

echo "Expected: e4b2fd2e2c1817adfa398c8de89fef96\n";
