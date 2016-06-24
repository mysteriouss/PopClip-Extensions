<?php
$input=getenv('POPCLIP_TEXT');
$host = parse($input)['host'];
if($host == null){
  echo 'error';
  return;
}

$ipaddr = gethostbyname($host);
if($ipaddr == null){
  echo 'dns error';
  return;
}

$api = 'http://ip.cn/';

if(filter_var($ipaddr, FILTER_VALIDATE_IP)){ //, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api . $ipaddr);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.43.0');
  $data = curl_exec($ch);
  $status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
  curl_close($ch);
  if($status_code === 200 && !is_null($data)){
    if (!empty($data)) {
        echo $data;
    } else {
        echo 'empty: '.$ipaddr;
    }
  }else{
    echo 'invalid: ' . $ipaddr;
  }
}else{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.43.0');
  $data = curl_exec($ch);
  curl_close($ch);
  if (!empty($data)) {
      echo $data;
  } else {
      echo $host;
  }
}

function parse($url){
  $url = trim($url);
  if(strpos($url,"://")===false && substr($url,0,1)!="/")
    $url = "http://".$url;
  $info = parse_url($url);
  return $info;
}
