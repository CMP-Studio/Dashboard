<?php
require_once "utils/api.php";
require_once "config/igConfig.php";
require_once "utils/errors.php";

function getTopIGMedia($userID, $count=10)
{
	$start = tryGET('start');
	$end = tryGET('end');

	$url = "https://api.instagram.com/v1/users/$userID/media/recent";

	$params = array("min_timestamp" => $start, "max_timestamp" => $end, "count" => "200", "client_id" => getIGClientID());

	$res = getAPI($url, $params);

	if(!isset($res->data)); return null; // return DoNotCache("Couldn't get Instagram Media: " . json_encode($res));
	//TODO: Figure out how we can rework IG to work.
	$media = $res->data;

	usort($media, "igSort");

	$result = array();

	foreach ($media as $key => $p) {
	  if($key < $count)
	  {
	  	array_push($result,$p);
	  }
	  else {
	    break;
	  }
	}

	return $result;




}

function igEmbed($mediaurl)
{
  $data = array("html" => "<style>.embed-container {position: relative; padding-bottom: 120%; height: 0; overflow: hidden;} .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='" . $mediaurl ."embed/' frameborder='0' scrolling='no' allowtransparency='true'></iframe></div>");

	$object = json_decode(json_encode($data), FALSE);
  return $object;
}


function igSort($a, $b)
{
  $likesA = $a->likes->count;
  $likesB = $b->likes->count;

  if($likesA == $likesB)
  {
    return 0;
  }

	$a->score = $likesA;
	$b->score = $likesB;

  return ($likesA > $likesB) ? -1 : 1;

}



?>
