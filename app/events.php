<?php
require_once 'utils/api.php';
require_once 'twitter.php';

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
	
	$events = array();

	$te = topTweetEvents();
	$events = array_merge($events, $te);

	usort($events, "sortEvents");
	$json = array("events" => $events);
	$json["start"] = $start * 1000;
	$json["end"] = $end * 1000;

	return $json;
}

function topTweetEvents()
{

	$tweets = topTweets(19412366, 10);

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

function sortEvents($a, $b)
{
	$tA = $a['timestamp'];
	$tB = $b['timestamp'];


	if($tA == $tB) return 0;

  	return ($tA < $tB) ? -1 : 1;
}

?>