<?php
require_once "../app/utils/analytics.php";


  $unifiedTracker = "ga:105579677";

  $otherTrackers = ["ga:72605316","ga:53193816", "ga:19917087", "ga:30663551","ga:43375288", "ga:35111744"];



$unifiedData = array();
$otherData = array();

$start = date('Y-m-d', strtotime('-2 days'));
$end =   date('Y-m-d', strtotime('-1 day'));

$ana = getAnalytics();

  foreach ($otherTrackers as $key => $t)
  {
    $gaRes = runQuery($ana, $t, $start, $end, "ga:pageviews","ga:hostname,ga:pagePath",'-ga:pageviews','10000',"ga:pageviews>10");
    if(isset($gaRes->ga_error))
    {
      continue;
    }
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
    if(isset($unifiedData[$fullPath]))
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
    $other2 = 0;
    $uni = 0;
    $uni2 = 0;
    $nonDefUrl = str_replace("default.aspx","",$url); //Fix for warhol weirdness
    if(isset($unifiedData[$url]))
    {
      $uni = $unifiedData[$url];
    }
    if (isset($unifiedData[$nonDefUrl])) {
      $uni2 = $unifiedData[$nonDefUrl];
    }
    if(isset($otherData[$nonDefUrl]))
    {
      $other2 = $otherData[$nonDefUrl];
    }

    $uni += $uni2;
    $other += $other2;

    $compare[$url]['other'] = $other;
    $compare[$url]['unified'] = $uni;
    //$compare[$url]['warhol'] = $uni2;

    $diff = $uni - $other;

    $compare[$url]['diff'] = $diff;

    $pcent = floatval($diff/$other)*100.0;

    $compare[$url]['pcent'] = $pcent;

  }

  foreach ($unifiedData as $url => $pv)
  {
    $uni = $pv;
    $other = 0;
    if(isset($otherData[$url]))
    {
      continue; //already recorded
    }
    else if (isset($otherData[$url . "default.aspx"])) {
      continue; //already recorded
    }

    $compare[$url]['other'] = $other;
    $compare[$url]['unified'] = $uni;

    $diff = $uni - $other;

    $compare[$url]['diff'] = $diff;

    $pcent = 100;

    $compare[$url]['pcent'] = $pcent;
      $compare[$url]['warhol'] = 0;

  }

  $totalUni = 0;
  $totalOther = 0;
  $totalDiff = 0;
  foreach ($compare as $key => $c)
  {
    $totalUni += $c["unified"];
    $totalOther += $c["other"];
    $totalDiff += $c["diff"];
  }
  $totalPcent = round(floatval($totalDiff/$totalOther)*100.0,2);



  uasort($compare,'diffSort');

  print "<table><tr><th>URL</th><th>Unified Pageviews</th><th>Other Pageviews</th><th>Difference</th><th>Percentage</th></tr>";

  print "<tr><td>Total</td><td>$totalUni</td><td>$totalOther</td><td>$totalDiff</td><td>$totalPcent%</td></tr>";

  foreach ($compare as $url => $row)
  {
    print "\n<tr>\n";
    print "<td><a href='http://$url'>$url</a></td>\n";
    print "<td>" . $row["unified"] . "</td>\n";
    print "<td>" . $row["other"] . "</td>\n";
    print "<td>" . $row["diff"] . "</td>\n";
    print "<td>" . round($row["pcent"],2) . "%</td>\n";
    print "</tr>\n";
  }

  print "</table>\n";



  function getAnalytics()
  {
    $client = getClient();
    $token = Authenticate($client);
    $analytics = new Google_Service_Analytics($client);
    return $analytics;
  }

  function diffSort($a, $b)
  {
    $adiff = abs($a['diff']);
    $bdiff = abs($b['diff']);

    if($adiff == $bdiff) return 0;
    if($adiff > $bdiff) return -1;
    return 1;
  }
 ?>
