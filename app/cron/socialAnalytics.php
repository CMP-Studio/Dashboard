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
  $acts = loadAccounts();
  $fb = $acts['facebook'];
  $ig = $acts['instagram'];
  $twit = $acts['twitter'];

 print "<pre>FB:\n";
  foreach ($fb as $key => $id)
  {
    var_dump(fbStats($id));
  }
  print "\nTwit:\n";
  foreach ($twit as $key => $id)
  {
    var_dump(twitStats($id));
  }
  print "\nIG:\n";
  foreach ($ig as $key => $id)
  {
    var_dump(igStats($id));
  }
  print "</pre>";
}

function loadAccounts()
{
  $fb = array();
  $ig = array();
  $twit = array();
  $accounts = json_decode(file_get_contents('../config/accounts.json'));
  $locations = $accounts->location;

  foreach ($locations as $key => $loc)
  {
    $accts = $loc->accounts;
    foreach ($accts as $key => $act)
    {
      if(isset($act->import)) continue; //Skip imports
      switch($act->type)
      {
        case "instagram":
          $ig[] = $act->id;
        break;
        case "facebook":
          $fb[] = $act->id;
        break;
        case "twitter":
          $twit[] = $act->id;
        break;
      }
    }
  }
  $accounts = array("facebook" => $fb, "instagram" => $ig, "twitter" => $twit);
  return $accounts;

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

function fbStats($id)
{
  $token = getFBToken();
  $url = "https://graph.facebook.com/$id";
  $params = array($token[0] => $token[1]);
  $info = getAPI($url,$params);

  return array("followers" => $info->likes);


}

function igStats($id)
{
  $client = getIGClientID();
  $url = "https://api.instagram.com/v1/users/$id";
  $params = array("client_id" => $client);
  $info = getAPI($url, $params);

  return array("followers" => $info->data->counts->followed_by);

}

/******************************* Tokens! ****************************/

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

function getFBToken()
{
 $url = "https://graph.facebook.com/oauth/access_token";
 $params = array(
   "client_id" => getFBClientID(),
   "client_secret" => getFBClientSecret(),
   "grant_type" => "client_credentials"
 );

 $token = getAPI($url,$params);
 $token = explode('=', $token);

 return $token;
}





 ?>
