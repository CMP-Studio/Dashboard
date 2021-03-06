<?php
require_once 'utils/api.php';
require_once 'utils/sql.php';
require_once 'utils/errors.php';

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
				if(isset($a['import']))
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
	$day = 24*60*60;
	$sdate = sqlSafe(date('Y-m-d H:i:s', $timestamp - ($day/2)));
	$edate = sqlSafe(date('Y-m-d H:i:s', $timestamp + ($day/2)));
	$id = sqlSafe($id);

	$query = "SELECT followers FROM account_stats WHERE user_id = $id AND (record_date BETWEEN $sdate AND $edate)";
  //print $query;
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
function getFollowerChange($id, $start, $end)
{


	$countS = getFollowers($id, $start);

	if(!isset($countS)) return null;

	$countE = getFollowers($id, $end);

	if(!isset($countE)) return null;



	return $countE - $countS;

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
				return "<script>console.warn('Twit: " . serialize($embed) . "')</script>";
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

//Parse act info
if(isset($a['id']))
{
	$id = $a['id'];
}
else {
	return '';  //Can't continue without an ID
}
if(isset($a['url'] ))
{
	$url = $a['url'];
}
else {
	$url = '';
}
if(isset($a['type'] ))
{
	$type = $a['type'];
}
else {
	$type = '';
}
if(isset($a['username'] ))
{
	$username = $a['username'];
}
else {
	$username = '';
}


	$typeclass = str_replace(" ","-",$type);

	$start = tryGET('start');
	$end = tryGET('end');


	$followers = getFollowers($id, $end);
	$change = getFollowerChange($id, $start, $end);
	$toppages = getTopRefferalPagesByType($type);
	$embeedHtml = getTopPostEmbedded($id, $type);

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




	switch($type)
	{
		case 'twitter':
			$postname = "Tweet";
			$faicon = "fa-twitter";
			break;
		case 'facebook':
			$postname = "Post";
			$faicon = "fa-facebook";
			break;
		case 'instagram':
			$postname = "Image";
			$faicon = "fa-instagram";
			break;
		default:
			$postname = "Post";
			$faicon = "";
	}






	//Create the html

	$html = "\n\n<!-- Start Social Badge -->\n\n";

	$html .= "<div class=' col-md-4 col-xs-12'>\n";
	$html .= "\t<div class='$typeclass social-pane panel'>\n";
	$html .= "\t\t\t<div class='panel-heading'>\n";
	$html .= "\t\t<a target='_blank' href='$url'>\n";
	$html .= "\t\t\t\t<h3 class='panel-title'><i class='fa $faicon'></i> $username</h3>\n";
	$html .= "\t\t</a>\n";
	$html .= "\t\t\t</div>\n";
	$html .= "\t\t<div class='panel-body'>\n";
	$html .= "\t\t\t<h4>Total Followers: <b>$followers</b></h4>\n";
	$html .= "\t\t\t<h4>Change in Followers: <i class='fa $dirclass'></i> <b>$change</b></h4>\n";
	if(count($toppages) > 0)
	{
		$html .= "\t\t\t<h4>Top Pages Visited From " . ucfirst($type) . "</h4>\n";
		$html .= "\t\t\t<div class='social-urls'>\n";
		$html .= "\t\t\t\t<ol>\n";

		foreach ($toppages as $key => $u)
		{
			if(isset($u['url']))
			{
				$purl = $u['url'];
			}
			else {
				continue;
			}
			if(isset($u['title']))
			{
				$ptitle = $u['title'];
			}
			else
			{
				$ptitle = $purl;
			}
			$html .= "\t\t\t\t\t<li><a target='_blank' href='//$purl'>$ptitle</a></li>\n";
		}
		$html .= "\t\t\t\t</ol>\n";
		$html .= "\t\t\t</div>\n";
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
		$tFollow = getFollowers($act["id"], $end);
		if(isset($tFollow))
		{
			$followers += $tFollow;
			$hasFollowers = true;
		}
		// Change in followers (delta)
		$tChange = getFollowerChange($act["id"], $start, $end);
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
	else {
		$followers = number_format($followers);
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
			$postname = "Tweet";
			$faicon = "fa-twitter";
			break;
		case 'facebook':
			$postname = "Post";
			$faicon = "fa-facebook";
			break;
		case 'instagram':
			$postname = "Image";
			$faicon = "fa-instagram";
			break;
		default:
			$postname = "Post";
			$faicon = "";
	}


	$html = "\n\n<!-- Start Social Badge -->\n\n";

	$html .= "<div class=' col-md-4 col-xs-12'>\n";
	$html .= "\t<div class='" . $typeclass . " social-pane panel'>\n";
	$html .= "\t\t<div class='panel-heading'>\n";
  $html .= "\t\t\t<h3 class='panel-title social-title'><i class='fa $faicon'></i> " . ucfirst($type) . "</h3>\n";
	$html .= "\t\t</div>\n";
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
			$url = $u['url'];
			$title = $u['title'];
			$html .= "\t\t\t\t\t<li><a target='_blank' href='//$url'>$title</a></li>\n";
		}
		$html .= "\t\t\t\t</ol>\n";
		$html .= "\t\t\t</div>\n";

	/*	$html .= "\t\t\t<h5>None :(</h5>\n"; */
	}
	if(count($topposts) > 0)
	{
	$html .= "\t\t\t<h4>Top " . $postname ."s</h4>\n";
	$html .= "\t\t\t<div class='social-urls'>\n";
	$html .= "\t\t\t\t<ol>\n";
		foreach ($topposts as $key => $p) {
			$url = $p["url"];
			$user = $p['username'];
			$html .= "\t\t\t\t\t<li><a target='_blank' href='$url'>$user's $postname</a></li>\n";
		}
		$html .= "\t\t\t\t</ol>\n";
	$html .= "\t\t\t</div>\n";
	}
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
		foreach ($tPosts as $key => &$p) {
			$p['username'] = $a['username'];
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
