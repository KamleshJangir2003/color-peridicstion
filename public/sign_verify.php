<?php
// Test with documentation example
$params = [
    'merchant_id' => 'M10001',
    'amount'      => '1000',
    'no'          => '20250808123456',
    'sign'        => 'ignore_this',
];
$secretKey = 'abcDEF123456';

// Step 1: Remove sign, skip empty values, trim
$filtered = [];
foreach ($params as $k => $v) {
    if ($k === 'sign') continue;
    $v = trim($v);
    if ($v === '' || $v === null) continue;
    $filtered[$k] = $v;
}

// Step 2: ASCII sort
ksort($filtered);

// Step 3: key=value& concat
$str = '';
foreach ($filtered as $k => $v) {
    $str .= $k . '=' . $v . '&';
}

// Step 4: append key
$str .= 'key=' . $secretKey;

// Step 5: MD5 lowercase
$sign = strtolower(md5($str));

echo "String: $str\n";
echo "Sign:   $sign\n";
echo "Expected: e4b2fd2e2c1817adfa398c8de89fef96\n";
echo "Match: " . ($sign === 'e4b2fd2e2c1817adfa398c8de89fef96' ? 'YES ✓' : 'NO ✗') . "\n";
