<?php

function getAPI($url, $params=null, $headers=null, $ssl=true)
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
  curl_setopt($curl,	CURLOPT_SSL_VERIFYPEER	, $ssl); //Because API SSLs are sometimes broken


  if( ! $result = curl_exec($curl))
    {
        var_dump(curl_error($curl));
        return NULL;
    }

  curl_close($curl);

  $data = json_decode($result);
  if($data == NULL)
  {
    return $result;
  }
  return $data;

}

function postAPI($url, $params=null, $headers=null, $ssl=true)
{
  	$curl = curl_init();

    $params = http_build_query($params);

  	curl_setopt($curl,	CURLOPT_URL				, $url);
  	curl_setopt($curl,	CURLOPT_POST			, 1);
  	curl_setopt($curl,	CURLOPT_POSTFIELDS		, $params);
  	curl_setopt($curl,	CURLOPT_HTTPHEADER		, $headers);
  	curl_setopt($curl,	CURLOPT_RETURNTRANSFER	, true);
  	curl_setopt($curl,	CURLOPT_ENCODING 		, "gzip");
  	curl_setopt($curl,	CURLOPT_SSL_VERIFYPEER	, $ssl); //Because API SSLs are sometimes broken


  	if( ! $result = curl_exec($curl))
      {
          var_dump(curl_error($curl));
  		      return NULL;
      }

  	curl_close($curl);
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






 ?>
