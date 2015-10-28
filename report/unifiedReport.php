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
    $gaRes = runQuery($ana, $t, $start, $end, "ga:pageviews","ga:hostname,ga:pagePath",'-ga:pageviews','10000',"ga:pageviews>10");

    $rows = $gaRes->getRows();
    foreach ($rows as $key => $r)
    {
      $fullPath = $r[0] . $r[1];
      $views = $r[2];
      if(isset($otherData[$fullPath]))
      {
        $otherData[$fullPath] += intval($views);
      }
      else {
        $otherData[$fullPath] = intval($views);
      }
    }
  }
  arsort($otherData);

  $gaRes = runQuery($ana, $unifiedTracker, $start, $end, "ga:pageviews","ga:hostname,ga:pagePath",'-ga:pageviews','10000',"ga:pageviews>10");
  $rows = $gaRes->getRows();
  foreach ($rows as $key => $r)
  {
    $fullPath = $r[0] . $r[1];
    $views = $r[2];
    if(isset($otherData[$fullPath]))
    {
      $unifiedData[$fullPath] += intval($views);
    }
    else {
      $unifiedData[$fullPath] = intval($views);
    }
  }
  arsort($unifiedData);

  $compare = array();
  foreach ($otherData as $url => $pv)
  {
    $other = $pv;
    $uni = 0;
    if(isset($unifiedData[$url]))
    {
      $uni = $unifiedData[$url];
    }

    $compare[$url]['other'] = $other;
    $compare[$url]['unified'] = $uni;

    $diff = $uni - $other;

    $compare[$url]['diff'] = $diff;

    $pcent = floatval($diff/$other)*100.0;

    $compare[$url]['pcent'] = $pcent;


  }



  uasort($compare,'diffSort');
  var_dump($compare);



  function getAnalytics()
  {
    $client = getClient();
    $token = Authenticate($client);
    $analytics = new Google_Service_Analytics($client);
    return $analytics;
  }

  function diffSort($a, $b)
  {
    $adiff = $a['diff'];
    $bdiff = $b['diff'];

    if($adiff == $bdiff) return 0;
    if($adiff > $bdiff) return -1;
    return 1;
  }
 ?>
