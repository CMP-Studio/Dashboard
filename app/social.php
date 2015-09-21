<?php
require_once 'utils/api.php';
require_once 'utils/sql.php';

require_once 'twitter.php';
require_once 'facebook.php';
require_once 'ganalytics.php';
require_once 'instagram.php';
require_once 'events.php';


/* This file is used to generate social media 'badges' unlike other api files this one will simply output the HTML for simplicity*/


function generateSocialBadge()
{
	if(isset($_GET['location']))
	{
		$accts = getAccountsFromLocation($_GET['location']);

		$html = '';

		foreach ($accts as $key => $a)
		{

			if($a['type'] != 'google analytics')
			{
				if($a['import'])
				{
					$html .= getMultiBadgeHTML($a);
				}
				else {
					$html .= getBadgeHTML($a);
				}

			}

		}

		$result = array('html' =>  $html);
		return $result;

	}
	else
	{
		return null;
	}





}



function getAccountsFromLocation($loc)
{

	//Get accounts
	$accounts = json_decode(file_get_contents('config/accounts.json'), true);

	//if(!isset($accounts['location'][$loc])) return null;

	$locAccounts = $accounts['location'][$loc]['accounts'];

	return $locAccounts;

}

function getFollowers($id, $timestamp)
{
	$date = sqlSafe(date('Y-m-d', $timestamp));
	$id = sqlSafe($id);

	$query = "SELECT followers FROM account_stats WHERE user_id = $id AND record_date = $date";

	$result = readQuery($query);
	if($result)
	{
		if($row = $result->fetch_row())
		{
			return $row[0];
		}
	}
	return null;
}
function getFollowerChange($id, $timestamp, $current)
{
	if(isset($current))
	{
		$date = sqlSafe(date('Y-m-d', $timestamp));
		$id = sqlSafe($id);

		$query = "SELECT followers FROM account_stats WHERE user_id = $id AND record_date = $date";
		$result = readQuery($query);
		if($row = $result->fetch_row())
		{
			return $current - $row[0];
		}
	}
	return null;

}

function getTopRefferalPagesByType($type)
{
	switch($type)
	{
		case 'twitter':
			$refFilter = "(^twitter|^t.co)";
			break;
		case 'facebook':
			$refFilter = "^(m\.|l\.|lm\.|.\.|)facebook";
			break;
		case 'instagram':
			$refFilter = "instagram";
			break;
		default:
			$refFilter = null;
	}

	$refs = getReferrals(5, $refFilter);

	return $refs;


}
function getTopPostEmbedded($id, $type)
{
	switch($type)
	{
		case 'twitter':
			//Twitter
			$post = topTweets($id, 1);
			if(isset($post[0]))
			{
				$embed = tweetEmbeed($post[0]->id_str);
				if(isset($embed->html))
				{
					return $embed->html;
				}
				return "<script>console.warn('Twit: Can't embed tweet')</script>";
			}
			else
			{
				return "<script>console.warn('Twit: Top post not found')</script>";
			}
		break;

		case 'facebook':
			//FB
			$post = getTopFBPosts($id, 1);
			if(isset($post[0]))
			{
				return FBembeed(getFBlink($post[0]));
			}
			else
			{
				return "<script>console.warn('FB: Top post not found')</script>";
			}
		break;

		case 'instagram':
			$post = getTopIGMedia($id, 1);
			if(isset($post[0]))
			{
				$embed = igEmbed($post[0]->link);
				return $embed->html;
			}
			else
			{
				return "<script>console.warn('IG: Top post not found')</script>";
			}

		break;
	}




	return null;
}

function getBadgeHTML($acctInfo)
{
	// Gather variables
	$a = $acctInfo;
	$typeclass = str_replace(" ","-",$a['type']);

	$start = tryGET('start');
	$end = tryGET('end');


	$followers = getFollowers($a['id'], $end);
	$change = getFollowerChange($a['id'], $start, $followers);
	$toppages = getTopRefferalPagesByType($a['type']);
	$embeedHtml = getTopPostEmbedded($a['id'], $a['type']);

	if(!isset($change))
	{
		$dirclass = '';
		$change = '?';
	}
	else if($change > 0)
	{
		$dirclass = 'fa-chevron-up';
		$change = number_format($change);
	}
	else if($change < 0)
	{
		$dirclass = 'fa-chevron-down';
		$change *= -1;
		$change = number_format($change);
	}
	else
	{
		$dirclass = 'fa-minus';
		$change = 'No Change';
	}

	if(!isset($followers))
	{
		$followers = '?';
	}
	else
	{
		$followers = number_format($followers);
	}




	switch($a['type'])
	{
		case 'twitter':
			$postname = "Tweet";
			break;
		case 'facebook':
			$postname = "Post";
			break;
		case 'instagram':
			$postname = "Image";
			break;
		default:
			$postname = "Post";
	}



	//Create the html

	$html = "\n\n<!-- Start Social Badge -->\n\n";

	$html .= "<div class=' col-md-4 col-xs-12'>\n";
	$html .= "\t<div class='" . $typeclass . " social-pane panel panel-default'>\n";
	$html .= "\t\t<a class='social-title' target='_blank' href='" . $a['url'] . "'>\n";
	$html .= "\t\t\t<div class='panel-heading clearfix'>\n";
	$html .= "\t\t\t\t<div class='logo' title='" . $a['type'] . "'></div>\n";
	$html .= "\t\t\t\t<h3 class='panel-title'>" . $a['username'] . "</h3>\n";
	$html .= "\t\t\t</div>\n";
	$html .= "\t\t</a>\n";
	$html .= "\t\t<div class='social-body panel-body'>\n";
	$html .= "\t\t\t<h4>Total Followers: <b>$followers</b></h4>\n";
	$html .= "\t\t\t<h4>Change in Followers: <i class='fa $dirclass'></i> <b>$change</b></h4>\n";
	$html .= "\t\t\t<h4>Top Pages Visited From " . ucfirst($a['type']) . "</h4>\n";
	$html .= "\t\t\t<div class='social-urls'>\n";
	$html .= "\t\t\t\t<ol>\n";

	foreach ($toppages as $key => $u)
	{
		$html .= "\t\t\t\t\t<li><a target='_blank' href='//$u'>$u</a></li>\n";
	}
	$html .= "\t\t\t\t</ol>\n";
	$html .= "\t\t\t</div>\n";
	if(count($toppages) <= 0)
	{
		$html .= "\t\t\t<h5>None :(</h5>\n";
	}
	$html .= "\t\t\t<h4>Top $postname</h4>\n";
	$html .= "\t\t\t<div class='social-embeed'>\n";
	$html .= "\t\t\t\t$embeedHtml\n";
	$html .= "\t\t\t</div>\n";
	$html .= "\t\t</div>\n";
	$html .= "\t</div>\n";
	$html .= "</div>\n";


	$html .= "\n\n<!-- End Social Badge -->\n\n";

	return $html;

}

function getMultiBadgeHTML($act)
{
	$imports = $act["locations"];
	$type = $act["type"];

	$start = tryGET('start');
	$end = tryGET('end');

	$followers = 0;
	$hasFollowers = false;

	$change = 0;
	$hasChange = false;

	$acts = array();

	foreach ($imports as $key => $imp)
	{
		$act = getImportAct($imp, $type);

		if(!isset($act)) continue;
		$acts[] = $act;
		//Followers
		$tFollow = getFollowers($act["id"], $start);
		if(isset($tFollow))
		{
			$followers += $tFollow;
			$hasFollowers = true;
		}
		// Change in followers (delta)
		$tChange = getFollowerChange($act["id"], $end, $tFollow);
		if(isset($tChange))
		{
			$change += $tChange;
			$hasChange = true;
		}
	}

	if(!$hasFollowers)
	{
		$followers = '?';
	}
	if(!$hasChange)
	{
		$change = '?';
		$dirclass = '';
	}
	else if($change > 0)
	{
		$dirclass = 'fa-chevron-up';
		$change = number_format($change);
	}
	else if($change < 0)
	{
		$dirclass = 'fa-chevron-down';
		$change *= -1;
		$change = number_format($change);
	}
	else
	{
		$dirclass = 'fa-minus';
		$change = 'No Change';
	}


	$toppages = getTopRefferalPagesByType($type);
	$topposts = getMultiTopPosts($acts, $type);


	$typeclass = str_replace(" ","-",$type);
	switch($type)
	{
		case 'twitter':
			$postname = "Tweets";
			break;
		case 'facebook':
			$postname = "Posts";
			break;
		case 'instagram':
			$postname = "Images";
			break;
		default:
			$postname = "Posts";
	}


	$html = "\n\n<!-- Start Social Badge -->\n\n";

	$html .= "<div class=' col-md-4 col-xs-12'>\n";
	$html .= "\t<div class='" . $typeclass . " social-pane panel panel-default'>\n";

	$html .= "\t\t\t<div class='panel-heading clearfix'>\n";
	$html .= "\t\t\t\t<div class='logo' title='" . $type . "'></div>\n";
	$html .= "\t\t\t\t<h3 class='panel-title'>" . ucfirst($type) . "</h3>\n";
	$html .= "\t\t\t</div>\n";

	$html .= "\t\t<div class='social-body panel-body'>\n";
	$html .= "\t\t\t<h4>Total Followers: <b>$followers</b></h4>\n";
	$html .= "\t\t\t<h4>Change in Followers: <i class='fa $dirclass'></i> <b>$change</b></h4>\n";
	if(count($toppages) > 0)
	{
		$html .= "\t\t\t<h4>Top Pages Visited From " . ucfirst($type) . "</h4>\n";
		$html .= "\t\t\t<div class='social-urls'>\n";
		$html .= "\t\t\t\t<ol>\n";

		foreach ($toppages as $key => $u)
		{
			$html .= "\t\t\t\t\t<li><a target='_blank' href='//$u'>$u</a></li>\n";
		}
		$html .= "\t\t\t\t</ol>\n";
		$html .= "\t\t\t</div>\n";

	/*	$html .= "\t\t\t<h5>None :(</h5>\n"; */
	}
	$html .= "\t\t\t<h4>Top $postname</h4>\n";
	$html .= "\t\t\t<div class='top-posts'>\n";
	$html .= "\t\t\t\t<ol>\n";
		foreach ($topposts as $key => $p) {
			$url = $p["url"];
			$html .= "\t\t\t\t\t<li><a target='_blank' href='//$url'>$url</a></li>\n";
		}
		$html .= "\t\t\t\t</ol>\n";
	$html .= "\t\t\t</div>\n";
	$html .= "\t\t</div>\n";
	$html .= "\t</div>\n";
	$html .= "</div>\n";


	$html .= "\n\n<!-- End Social Badge -->\n\n";

	return $html;

}

function getMultiTopPosts($acts, $type, $count=5)
{
	$posts = array();
	foreach ($acts as $key => $a)
	{
		$tPosts = array();
		$id = $a["id"];
		switch($type)
		{
			case 'twitter':
				$tPosts = topTweetEvents($id, $count);
				break;
			case 'facebook':
				$tPosts = fbEvents($id, $count);
				break;
			case 'instagram':
				$tPosts = igEvents($id, $count);
				break;
		}
		$posts = array_merge($posts, $tPosts);
	}
	usort($posts, "scoreSortSocial");

	return array_splice($posts, 0, $count);
	/*
	$rPosts = array();
	for ($i=0; $i < $count ; $i++)
	{
		$rPosts[$i] = $posts[$i];
	}
	return $rPosts;
	*/
}


function scoreSortSocial($a, $b)
{
	$pointA = $a["points"];
	$pointB = $b["points"];

	if($pointA == $pointB)
	{
		return 0;
	}

	return ($pointA > $pointB) ? -1 : 1;

}

function getImportAct($loc, $type)
{
	$accounts = json_decode(file_get_contents('config/accounts.json'), true);

	if(!isset($accounts["location"][$loc])) return null;
	$iacts = $accounts["location"][$loc]["accounts"];

	foreach ($iacts as $key => $a)
	{
		if($a['type'] == $type)
		{
			return $a;
		}
	}
	return null;
}
