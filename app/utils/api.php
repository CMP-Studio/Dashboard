<?php
require_once "cache.php";


function getAPI($url, $params=null, $headers=null, $ssl=true)
{

  $cache = APIcache($url, $params);

  if(isset($cache))
  {
    $result = $cache;
  }
  else
  {

    $curl = curl_init();

    $params = http_build_query($params);

    if(isset($params))
    {
      $url = $url . "?" . $params;
    }


    curl_setopt($curl,	CURLOPT_URL				, $url);
    if(isset($headers))  curl_setopt($curl,	CURLOPT_HTTPHEADER		, $headers);
    curl_setopt($curl,	CURLOPT_RETURNTRANSFER	, true);
    curl_setopt($curl,	CURLOPT_ENCODING 		, "gzip");
    curl_setopt($curl,	CURLOPT_SSL_VERIFYPEER	, $ssl);


    if( ! $result = curl_exec($curl))
    {
      return array("curl_error" => curl_error($curl));
    }

    curl_close($curl);
    // APIstore($url, $params, $result);
  }

  $data = json_decode($result);
  if($data == NULL)
  {
    return $result;
  }

  return $data;

}

function postAPI($url, $params=null, $headers=null, $ssl=true)
{
  $cache = APIcache($url, $params);

  if(isset($cache))
  {
    $result = $cache;
  }
  else
  {

    $curl = curl_init();

    $params = http_build_query($params);

    curl_setopt($curl,	CURLOPT_URL				, $url);
    curl_setopt($curl,	CURLOPT_POST			, 1);
    curl_setopt($curl,	CURLOPT_POSTFIELDS		, $params);
    if(isset($headers))
    {
      curl_setopt($curl,	CURLOPT_HTTPHEADER		, $headers);
    }
    curl_setopt($curl,	CURLOPT_RETURNTRANSFER	, true);
    curl_setopt($curl,	CURLOPT_ENCODING 		, "gzip");
    curl_setopt($curl,	CURLOPT_SSL_VERIFYPEER	, $ssl);


    if( ! $result = curl_exec($curl))
    {
      return array("curl_error" => curl_error($curl));
    }

    curl_close($curl);
  //  APIstore($url, $params, $result);
  }
  $data = json_decode($result);
  if($data == NULL)
  {
    return $result;
  }

  return $data;
}

function tryGET($var)
{
  if(isset($_GET[$var])) return $_GET[$var];

  return null;
}

function tryPOST($var)
{
  if(isset($_POST[$var])) return $_POST[$var];

  return null;
}

function error($error, $from = null)

{

  $err = array("error" => $error);

  if(isset($from)) $err["From"] = $from;

  print json_encode($err);

  exit();
}
//Cache all API calls
function APIdsName($url, $params)
{
  $dataset = "type=api;url=$url;";
  if(isset($params) && is_array($params))
  {
    foreach ($params as $key => $value) {
      $dataset .= "KEY:$key=$value;";
    }
  }

  return $dataset;
}

function APIcache($url, $params)
{
  $ds = APIdsName($url, $params);
  if(checkCache($ds))
  {
    return loadFromCache($ds);
  }

  return null;
}

function APIstore($url, $params, $data)
{
  $ds = APIdsName($url, $params);
  storeInCache($ds, $data);
}






?>
