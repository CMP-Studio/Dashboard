<?php
require_once "utils/api.php";
require_once "config/igConfig.php";

function getTopIGMedia($userID, $count=10)
{
	$start = tryGET('start');
	$end = tryGET('end');

	$url = "https://api.instagram.com/v1/users/$userID/media/recent";

	$params = array("min_timestamp" => $start, "max_timestamp" => $end, "count" => "200", "client_id" => getIGClientID());

	$media = getAPI($url, $params)->data;

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
  $data = array("html" => "<iframe src='" . $mediaurl . "embed/' frameborder='0' scrolling='no' allowtransparency='true'></iframe>");

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
