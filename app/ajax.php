<?php

require_once 'twitter.php';
require_once 'ganalytics.php';
require_once 'events.php';
require_once "utils/cache.php";
/* 
This is the main application file
It handles all the AJAX calls from other web pages
Most of the functions should be handled in other files but this will delegate calls to those files.
*/

/* source: https://jonsuh.com/blog/jquery-ajax-call-to-php-script-with-json-return/ */
if (is_ajax() || true) //Remove TRUE when done testing
{

  if (isset($_GET["action"]) && !empty($_GET["action"])) //Checks if action value exists
  	{
  		
  		header('Content-type: application/x-javascript');
    	delegate($_GET["action"]);

	}
}
function is_ajax()
{
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
/* end */



/* Main function to delegate actions */
function delegate($action)
{
	//cleanCache();
	
	$ds = dataSetName($_GET);
	

    	
    $data = loadFromCache($ds);
    
    
    
    if($data)
    {
    	echo $data;
    	exit(0);
    }

    

	switch($action)
	{
		//Twitter
		case 'topTweets': $data = topTweets(); break;

		//Google Analytics
		case 'chart' :  $data = getChart(); break;

		//Events
		case 'events' : $data = getEvents(); break;
	}

	if(!empty($data))
	{

		$val = storeInCache($ds, output($data));

	}

	echo output($data);
	exit(0);


}



function output($data)
{
	//Formats output of various types into JSON

	if(is_array ($data))
	{
		return json_encode($data);
	}
	if(json_decode($data))
	{
		return $data;
	}
	else
	{
		return json_encode($data);
	}
	
}

function dataSetName($settings)
{
  $res = "";
  foreach ($settings as $k => $s) {
    $res .= "$k=$s;";
  }

  return $res;
}


?>
