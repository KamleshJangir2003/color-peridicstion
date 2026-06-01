<?php
$merchantId = 'M258';
$secretKey  = '9e66e751433ef9dec6096a4dfaaddbc7';
$baseUrl    = 'https://www.mv-pay.org';

function sign($fields, $key) {
    ksort($fields);
    $str = '';
    foreach ($fields as $k => $v) { $str .= $k . '=' . $v . '&'; }
    $str .= 'key=' . $key;
    return [strtolower(md5($str)), $str];
}

function post($url, $params) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $r = curl_exec($ch); curl_close($ch);
    return $r;
}

$no = 'T' . time();

// PAYOUT - Test1: only merchant_id+no+amount
echo "=== PAYOUT Test1: merchant_id+no+amount ===\n";
$s = ['merchant_id'=>$merchantId,'no'=>$no,'amount'=>'200'];
$p = array_merge($s, ['account'=>'7780101437677','name'=>'Test User','ifsc_code'=>'FDRL0007778','notify_url'=>'https://nighty1games.shop/api/payout/callback','remark'=>'test']);
[$sign,$str] = sign($s, $secretKey);
$p['sign'] = $sign;
echo "Sign str: $str\nSign: $sign\n";
echo "Response: " . post($baseUrl.'/Transfer/replay', $p) . "\n\n";

// PAYOUT - Test2: merchant_id+no+amount+account+name+ifsc_code
echo "=== PAYOUT Test2: +account+name+ifsc_code ===\n";
$s2 = ['merchant_id'=>$merchantId,'no'=>$no.'B','amount'=>'200','account'=>'7780101437677','name'=>'Test User','ifsc_code'=>'FDRL0007778'];
$p2 = array_merge($s2, ['notify_url'=>'https://nighty1games.shop/api/payout/callback','remark'=>'test']);
[$sign2,$str2] = sign($s2, $secretKey);
$p2['sign'] = $sign2;
echo "Sign str: $str2\nSign: $sign2\n";
echo "Response: " . post($baseUrl.'/Transfer/replay', $p2) . "\n\n";

// PAYOUT - Test3: all fields
echo "=== PAYOUT Test3: all fields ===\n";
$s3 = ['merchant_id'=>$merchantId,'no'=>$no.'C','amount'=>'200','account'=>'7780101437677','name'=>'Test User','ifsc_code'=>'FDRL0007778','notify_url'=>'https://nighty1games.shop/api/payout/callback','remark'=>'test'];
[$sign3,$str3] = sign($s3, $secretKey);
$s3['sign'] = $sign3;
echo "Sign str: $str3\nSign: $sign3\n";
echo "Response: " . post($baseUrl.'/Transfer/replay', $s3) . "\n\n";

// PAYMENT - Test1: only merchant_id+no+amount
echo "=== PAYMENT Test1: merchant_id+no+amount ===\n";
$ps = ['merchant_id'=>$merchantId,'no'=>$no.'D','amount'=>'200'];
$pp = array_merge($ps, ['notify_url'=>'https://nighty1games.shop/api/payment/callback','return_url'=>'https://nighty1games.shop','remark'=>'test']);
[$psign,$pstr] = sign($ps, $secretKey);
$pp['sign'] = $psign;
echo "Sign str: $pstr\nSign: $psign\n";
echo "Response: " . post($baseUrl.'/Transfer/index', $pp) . "\n\n";

// PAYMENT - Test2: merchant_id+no+amount+remark
echo "=== PAYMENT Test2: +remark ===\n";
$ps2 = ['merchant_id'=>$merchantId,'no'=>$no.'E','amount'=>'200','remark'=>'test'];
$pp2 = array_merge($ps2, ['notify_url'=>'https://nighty1games.shop/api/payment/callback','return_url'=>'https://nighty1games.shop']);
[$psign2,$pstr2] = sign($ps2, $secretKey);
$pp2['sign'] = $psign2;
echo "Sign str: $pstr2\nSign: $psign2\n";
echo "Response: " . post($baseUrl.'/Transfer/index', $pp2) . "\n";
