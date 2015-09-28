<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

//Utils
require_once "../utils/sql.php";
require_once "../utils/api.php";
//Configs
require_once "../config/igConfig.php";
require_once "../config/fbConfig.php";
require_once "../config/twitConfig.php";

main();

function main()
{
  var_dump(twitStats("30018600"));
}


function twitStats($id)
{
  $token = getTwitterToken();
  $headers = array("Authorization: Bearer " . $token);
  $params = array("user_id" => $id);
  $url = "https://api.twitter.com/1.1/users/show.json";
  $info = getAPI($url, $params, $headers);

  return array("followers" => $info->followers_count);

}

function getTwitterToken()
{
	$url = "https://api.twitter.com/oauth2/token";
	$cred = getBearerCred();

	$headers = array(
	"Authorization: Basic $cred",
	"Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
	"Accept-Encoding: gzip");

	$params = array("grant_type" => "client_credentials");

	$data = postAPI($url, $params, $headers);


	if(isset($data->access_token))
	{
		return $data->access_token;
	}

	return NULL;
}





 ?>
