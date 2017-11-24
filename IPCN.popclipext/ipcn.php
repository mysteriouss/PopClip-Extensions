<?php
$input = getenv('POPCLIP_TEXT');
$ipaddr = $input;
if(filter_var($ipaddr, FILTER_VALIDATE_IP)){ //, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
  ip_cn($ipaddr);
  return;
}

$host = parse($input)['host'];
if($host == null){
  echo 'error';
  return;
}

$ipaddr = getAddrByHost($host);
if($ipaddr == null){
  echo 'dns error';
  return;
}

if(filter_var($ipaddr, FILTER_VALIDATE_IP)){ //, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
  ip_cn($ipaddr);
}else{
  ip_cn('');
}

function ip_cn($ipaddr, $fail=''){
  $api = 'http://ip.cn/';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api . $ipaddr);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.43.0');
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
  curl_setopt($ch, CURLOPT_TIMEOUT,5);
  $data = curl_exec($ch);
  $status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
  curl_close($ch);
  if($status_code == 200 && !is_null($data)){ // && false
    if (!empty($data)) {
        echo $data;
    } else {
        echo 'ip.cn empty: '.$ipaddr;
    }
  }else{
    //echo 'invalid: ' . $ipaddr;
    if(strlen($ipaddr)){
      ipip_net($ipaddr);
    }else{
      echo 'invalid';
    }
  }
}

function ipip_net($ipaddr){
  if(!$ipaddr) return;

  $api = 'http://freeapi.ipip.net/';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$api . $ipaddr);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.43.0');
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
  curl_setopt($ch, CURLOPT_TIMEOUT,5);
  $data = curl_exec($ch);
  $status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
  curl_close($ch);
  if($status_code === 200 && !is_null($data)){
    if (!empty($data)) {
      $json = json_decode($data,true);
      if(is_array($json)){
        echo 'IP: '.$ipaddr . ' 来自: '. implode(' ', $json). '「ipip.net」';
      }else{
        echo $json;
      }
    } else {
        echo 'ipip.net empty: ' .$ipaddr;
    }
  }else{
    echo 'current invalid: ' .$ipaddr;
  }
}

function parse($url){
  $url = trim($url);
  if(strpos($url,"://")===false && substr($url,0,1)!="/")
    $url = "http://".$url;
  $info = parse_url($url);
  return $info;
}

function getAddrByHost($host, $timeout = 3) {
   $query = `nslookup -timeout=$timeout -retry=2 $host`;
   if(preg_match('/\nAddress: (.*)\n/', $query, $matches))
      return trim($matches[1]);
   return $host;
}
