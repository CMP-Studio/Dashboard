<?php

require_once 'utils/errors.php';

require_once 'utils/api.php';
require_once 'config/twitConfig.php';

/* This file will handle the twitter API calls */
topTweets();

/*

Endpoints

*/


function topTweets()
{
	$user = tryGET('user');
	$start = tryGET('start');
	$end = tryGET('end');
	//$count = tryGET('count');

	if(!isset($count)) $count = 5;



	$tweets = getTweetsByDate($user, $start, $end);


	if(isset($tweets))
	{

		usort($tweets, "tweetSort");



		array_splice($tweets, 0, $count);


		return $tweets;
	}
}

/*

Functions

*/

function tweetSort($a, $b)
{
  //Sort higher score to lower

  $rtVal = 2;
  $favVal = 1;

  $rtA = $a->retweet_count;
  $favsA = $a->favorite_count;

  $scoreA = $rtA*$rtVal + $favsA*$favVal;

  $rtB = $b->retweet_count;
  $favsB = $b->favorite_count;

  $scoreB = $rtB*$rtVal + $favsB*$favVal;

  if($scoreA == $scoreB) return 0;

  return ($scoreA > $scoreB) ? -1 : 1;

}

function getTweetsByDate($user, $start=0, $end=0)
{
	if(empty($user)) return null;

	$token = getToken();
	$headers = array("Authorization: Bearer " . $token);

	$getTweets = array();

	if($start == 0) $start = time();
	if($end == 0) $end= time();

	$max_id = null;


	//First get each tweet segment needed
	do
	{

	  	$params = array("count" => 200, "trim_user" => 1, "exclude_replies" => 1, "include_rts" => 0, "user_id" => $user, "max_id" => $max_id);
	  	$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

	  	$tweets = getAPI($url, $params, $headers);


	  	if(isset($tweets->errors)) error($tweets->errors, "getTweetsByDate");
	  	if(isset($tweets->error)) error($tweets->error, "getTweetsByDate");

	  	$len = count($tweets);

	  		$last = $tweets[$len - 1];
	


	  	$lastDate = strtotime($last->created_at);
	  	$max_id = $last->id_str;

	  	$getTweets = array_merge($getTweets, $tweets);

	 } while ($lastDate > $start);



	 //Trim the rear

	 foreach(array_reverse($getTweets, TRUE) as $k=>$t)
	 {
	 	if(strtotime($t->created_at) >= $start)
	 	{
	 		break;
	 	}
	 	else
	 	{
	 		unset($getTweets[$k]);
	 	}

	 }


	 //Trim the front

	 foreach($getTweets as $k=>$t)
	 {
	 	if(strtotime($t->created_at) <= $end)
	 	{
	 		break;
	 	}
	 	else
	 	{
	 		unset($getTweets[$k]);
	 	}

	 }


	 $getTweets = array_values($getTweets);

	 return $getTweets;

}


function getToken()
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