<?php
require_once 'utils/api.php';
require_once 'twitter.php';
require_once 'facebook.php';
require_once 'ganalytics.php';
require_once 'instagram.php';

/* This file contains functions that are across the different APIs and other data sources to generate 'event' items for timelines. */



/* Event structure
	{
	start: time in ms since epoch
	end: time in ms since epoch
	events:
		[
			{
				title: event title
				type: event type
				source: e.g. twitter
				username: The account username
				source_url: e.g. twitter.com/id/post*
				timestamp: time in ms since epoch
				points: an arbitrary number for ranking (higher is better)
				score: default rank (lower is better)
				html: html code (+ JS and CSS) to render the event
				url: A link to the post/image/etc.
			}

		]

	}


*/



//getEvents();


/* Central function that pulls in events from the other functions in this file */

function getEvents()
{
	$start = tryGET('start');
	$end = tryGET('end');
	$lt = false;
	if(tryGet('longterm')) $lt = true;
	$loc = strtolower (tryGET('location'));

	//Get accounts
	$accounts = json_decode(file_get_contents('config/accounts.json'), true);
	$locAccounts = $accounts['location'][$loc]['accounts'];
	//print_r($accounts);

	$events = array();

	foreach ($locAccounts as $key => $act)
	{
		$type = $act['type'];
		$import = false;
		if(isset($act["import"]))
		{
			$locArr = $act["locations"];
			$import = true;
		}
		else {
			$id = $act["id"];
		}
		//print $id;
		$te = array();
		switch($type)
		{
			case 'twitter':
				if(!$lt)
				{
					if($import)
					{
						$te = getMultipleEvents($locArr, $type);
					}
					else
					{
						$te = topTweetEvents($id);
					}
				}
				break;

			case 'google analytics':
				$te = gaEvents($id);
				break;

			case 'facebook' :
				if(!$lt)
				{
					if($import)
					{
						$te = getMultipleEvents($locArr, $type);
					}
					else
					{
						$te = fbEvents($id);
					}
				}
				break;

			case 'instagram' :
				if(!$lt)
				{
					if($import)
					{
						$te = getMultipleEvents($locArr, $type);
					}
					else
					{
						$te = igEvents($id);
					}
				}
				break;
		}
		if(isset($te))
		{
			$events = array_merge($events, $te);
		}
	}


	usort($events, "sortEvents");
	$json = array("events" => $events);
	$json["start"] = $start * 1000;
	$json["end"] = $end * 1000;

	return $json;
}

function getMultipleEvents($locations, $type, $count = 10)
{
	$result = array();
	$accounts = json_decode(file_get_contents('config/accounts.json'), true);
	foreach ($locations as $key => $loc)
	{
		if(!isset($accounts['location'][$loc])) continue;
		$locAccounts = $accounts['location'][$loc]['accounts'];

		foreach ($locAccounts as $key => $act)
		{
				if($act["type"] != $type) continue;

				$id = $act["id"];

				$temp = array();
				switch ($type)
				{
					case 'twitter':
						$temp = topTweetEvents($id, $count);
						break;

					case 'facebook' :
						$temp = fbEvents($id, $count);
						break;

					case 'instagram' :
						$temp = igEvents($id, $count);
						break;
				}
				$result = array_merge($result, $temp);





		}

	}

	usort($result, "scoreSort");

	$slice = array_slice($result, 0, $count);

	return $slice;
}

function scoreSort($a, $b)
{
	$pointA = $a['points'];
	$pointB = $b['points'];

	if($pointA == $pointB)
	{
		return 0;
	}

	return ($pointA > $pointB) ? -1 : 1;

}

function topTweetEvents($account, $count = 10)
{

	$tweets = topTweets($account, $count);
if(!isset($tweets)) return array();
	$tevents = array();

	foreach ($tweets as $key => $t)
	{
		$teve = array();

		$time = strtotime($t->created_at);
		$sdate = date("l F jS",$time);

		$ebed = tweetEmbeed($t->id_str);

		$teve['title'] = "Tweet from $sdate";
		$teve['type'] = "Top Tweets";
		$teve['source'] = "Twitter";
		if(isset($ebed->html))
		{
			$teve['html'] = $ebed->html;
		}
		else {
			$teve['html'] = "<script>console.warn('Twit: " . serialize($ebed) . "')</script>";
		}
		$teve['score'] = $key;
		$teve['url'] = "https://twitter.com/statuses/" . $t->id_str;
		$teve['points'] = $t->score;

		$teve['timestamp'] = $time*1000;
	//	$teve['username'] = '@' . $t->user->screen_name;


		array_push($tevents, $teve);
	}

	return $tevents;

}
function gaEvents($account, $count = 10)
{
	$events = getTopDeviations($account, $count);

	$tevents = array();

if(!isset($events)) return array();

	foreach ($events as $key => $d) {
		$teve = array();
		//Form timestamp
		$ts = $d['timestamp'];

		$sdate = date("l F jS g:i a",$ts);
		$url = 'http://' . $d['path'];

		$teve['timestamp'] = $ts*1000;
		$teve['title'] = "High traffic point on $sdate";
		$teve['type'] = "Web Traffic";
		$teve['source'] = "Google Analytics";
		$teve['score'] = $key;
		$teve['url'] = $url;

		//Now generate the html
		$title = $d['title'];
		$pv = number_format($d['pageviews']);
		$m = number_format($d['mean']);
		$z = $d['z'];
		$sd = $d['stdev'];
		$time = $d['time'];
		$teve['html'] = "<p>$time: The page <a href='$url' target='_blank'>$title</a> has an unusually high number of views at $pv views at $sdate.  The page unusually has $m views/hour. [The Z-Score is $z / Standard Deviation is $sd]</p>";

		array_push($tevents, $teve);

	}

	return $tevents;
}

function fbEvents($account, $count = 10)
{
	$events = getTopFBPosts($account, $count);
	$tevents = array();
	if(!isset($events)) return array();

	foreach ($events as $key => $d)
	{
		$teve = array();

		$time = strtotime($d->created_time);
		$sdate = date("l F jS",$time);

		$teve['title'] = "Post from $sdate";
		$teve['type'] = "Top Facebook Posts";
		$teve['source'] = "Facebook";

		$link = getFBlink($d);
		if($link)
		{
			$teve['html'] = FBembeed($link);
			$teve['url'] = $link;
		}
		else
		{

		}
		$teve['score'] = $key;
		$teve['points'] = $d->score;
		$teve['timestamp'] = $time*1000;
		//$teve['username'] = $d->from->name;

		array_push($tevents, $teve);

	}
	return $tevents;


}

function igEvents($account, $count = 10)
{
	$events = getTopIGMedia($account, $count);
	$tevents = array();
	if(!isset($events)) return array();

	foreach ($events as $key => $d)
	{
		$teve = array();



		$time = $d->created_time;
		$sdate = date("l F jS",$time);

		$teve['title'] = "Photo from $sdate";
		$teve['type'] = "Top Instagram Posts";
		$teve['source'] = "Instagram";
		$teve['score'] = $key;
		if(isset($d->score))
		{
			$teve['points'] = $d->score;
		}
		else {
			$teve['points'] = 0;
		}
		$teve['timestamp'] = $time*1000;
		$teve['url'] = $d->link;

		$embed = igEmbed($d->link);

		$teve['html'] = $embed->html;
	//	$teve["username"] = $d->from->username;


		array_push($tevents, $teve);
	}

	return $tevents;
}

function sortEvents($a, $b)
{
	$tA = $a['timestamp'];
	$tB = $b['timestamp'];


	if($tA == $tB) return 0;

  	return ($tA < $tB) ? -1 : 1;
}


?>
