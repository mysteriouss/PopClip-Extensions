<?php
$input = getenv('POPCLIP_TEXT');

function from62to10($num) {
    $from = 62;
    $num = strval($num);
    $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($num);
    $dec = 0;
    for($i = 0; $i < $len; $i++) {
        $pos = strpos($dict, $num[$i]);
        $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
    }
    return $dec;
}

function decode($url) {
    $file = basename($url,'.jpg');
    $code = substr($file, 0, 8);
    if (substr($code, 0, 2) == '00') {
        return from62to10($code);
    } else {
        return hexdec($code);
    }
}

$weibo = 'http://weibo.com/u/' . decode($input);
echo $weibo;