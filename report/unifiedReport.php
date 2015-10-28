<?php
require_once "../app/utils/analytics.php";


  $unifiedTracker = "ga:105579677";

  $otherTrackers = ["ga:72605316","ga:53193816", "ga:19917087", "ga:30663551"];



$unifiedData = array();
$otherData = array();

$start = date('Y-m-d', strtotime('-7 days'));
$end =   date('Y-m-d', strtotime('-1 day'));

$ana = getAnalytics();

  foreach ($otherTrackers as $key => $t)
  {
    $gaRes = runQuery($ana, $t, $start, $end, "ga:pageviews","ga:hostname,ga:pagePath",'','','10000');
    $rows = $gaRes->getRows();
    var_dump($rows);
  }



  function getAnalytics()
  {
    $client = getClient();
    $token = Authenticate($client);
    $analytics = new Google_Service_Analytics($client);
    return $analytics;
  }
 ?>
