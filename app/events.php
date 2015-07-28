<?php
require_once 'utils/api.php';
require_once 'twitter.php';
require_once 'facebook.php';
require_once 'ganalytics.php';

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
				timestamp: time in ms since epoch
				score: an arbitrary number of rank (lower is better)
				html: html code (+ JS and CSS) to render the event
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
	$loc = strtolower (tryGET('location'));

	//Get accounts
	$accounts = json_decode(file_get_contents('config/accounts.json'), true);
	$locAccounts = $accounts['location'][$loc]['accounts'];
	//print_r($accounts);

	$events = array();
	
	foreach ($locAccounts as $key => $act) 
	{
		$type = $act['type'];
		$id = $act["id"];
		//print $id;
		$te = array();
		switch($type)
		{
			case 'twitter': 
				//print "Twitter!";
				$te = topTweetEvents($id);
				break;

			case 'google analytics':
				$te = gaEvents($id);
				break;

			case 'facebook' :
				$te = fbEvents($id);
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

function topTweetEvents($account)
{

	$tweets = topTweets($account, 10);

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
		$teve['html'] = $ebed->html;
		$teve['score'] = $key;
		$teve['timestamp'] = $time*1000;


		array_push($tevents, $teve);
	}

	return $tevents;

}
function gaEvents($account)
{
	$events = topSources($account);

	$tevents = array();

	foreach ($events as $key => $d) {
		$teve = array();
		//Form timestamp
		$time = $d[0] . " " . $d[1] .":00";
		$ts = strtotime($time);
		$sdate = date("l F jS",$ts);

		$teve['timestamp'] = $ts*1000;
		$teve['title'] = "High traffic point on $sdate";
		$teve['type'] = "Web Traffic";
		$teve['source'] = "Google Analytics";
		$teve['score'] = $key;


		$source = $d[2];
		$url = 'http://' . $d[3] . $d[4];
		$users = $d[6];
		$title = $d[5];
		//Now generate the html
		$teve['html'] = "<div class='ga-event'><table class='refTbl'><tr class='source'><th>Source</th><td>$source</td></tr><tr class='url'><th>Page</th><td><a href='$url'>$title</a></td></tr><tr class='views'><th>Pageviews</th><td>$users</td></tr></table></div>";

		array_push($tevents, $teve);

	}

	return $tevents;
}

function fbEvents($account)
{
	$events = getTopFBPosts($account);
	$tevents = array();

	foreach ($events as $key => $d) 
	{
		$teve = array();

		$time = strtotime($d->created_time);
		$sdate = date("l F jS",$time);

		$teve['title'] = "Post from $sdate";
		$teve['type'] = "Top Facebook Posts";
		$teve['source'] = "Facebook";
		$teve['html'] = FBembeed($d->actions[0]->link);
		$teve['score'] = $key;
		$teve['timestamp'] = $time*1000;

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