<?php
$input = getenv('POPCLIP_TEXT');
$ipaddr = trim($input);
if(filter_var($ipaddr, FILTER_VALIDATE_IP)){ //, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
  haoip_cn($ipaddr);
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
  haoip_cn($ipaddr);
}else{
  echo $ipaddr;
}

function ip_cn($ipaddr, $fail=''){
  if(!$ipaddr) return;

  $data = exec("curl -sL 'https://ip.cn/$ipaddr' -H 'User-Agent: curl/7.43.0'");

  if (!empty($data)) {
    echo $json;
  } else {
    echo 'ip.cn empty: ' .$ipaddr;
  }
}

function ipip_net($ipaddr){
  if(!$ipaddr) return;
  $curl = "curl -sL 'https://freeapi.ipip.net/$ipaddr' -H 'User-Agent: curl/7.43.0'";
  $data = exec($curl);

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
}

function haoip_cn($ipaddr){
  if(!$ipaddr) return;

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, "https://haoip.cn/ip/$ipaddr");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


  $headers = array();
  $headers[] = 'User-Agent: curl/7.78.0';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $data = curl_exec($ch);
  if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
  }
  curl_close($ch);

  if (!empty($data)) {
    $json = json_decode($data,true);
    if(is_array($json)){
      echo 'IP: '.$ipaddr . ' 来自: '. implode(' ', $json). '「haoip.cn」';
    }else{
      echo str_replace(array("\r", "\n"), ' ', $data);
    }
  } else {
      echo 'haoip.cn empty: ' .$ipaddr;
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
