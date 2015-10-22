<?php
require_once "utils/highcharts.php";
require_once "utils/api.php";
require_once 'utils/sql.php';

generateSocialChart();

function generateSocialChart()
{
  $from = 1443657600;
  $to = 1446249600;
  $intv = 24*60*60; //1 day


  $acts = getAccountsByLoc("cmnh");

  $chart = new Highchart('areaspline');
  $chart->addLegend();
  $chart->addPlotOption('fillOpacity',0.2);
  $chart->addTimestamps($from*1000,$intv*1000);

  foreach ($acts as $key => $act)
  {
    $type = $act['type'];
    if($type ==  "google analytics") continue;
    $color = "white";
    switch($type)
    {
      case "twitter": $color = "rgb(80,171,241)"; break;
      case "facebook": $color = "rgb(68,97,157)"; break;
      case "instagram": $color = "rgb(185,163,140)"; break;
    }
    $name = $act['username'];
    $id = $act['id'];
    $data = getFollowerData($id,$from,$to);
    $fData = formatData($from, $intv, $to, $data);

    $chart->addSeries($fData, $name, $color);
  }





  var_dump($chart->toJson());


}

function getAccountsByLoc($loc)
{
  $accounts = json_decode(file_get_contents('config/accounts.json'), true);
  $locAccounts = $accounts['location'][$loc]['accounts'];
  return $locAccounts;
}

function formatData($start, $intv, $end, $data)
{
  $dataPt = array();
  for ($i=$start; $i <= $end; $i+= $intv)
  {
    $min = $i;
    $max = $i + $intv - 1;

    $value = null;
    foreach ($data as $key => &$row)
    {
       $ts = strtotime($row["record_date"]);
       if($ts >= $min && $ts <= $max)
       {
         //In range
         $value = intval($row["followers"]);
         unset($row); //Remove from pool
         break;
       }
       else if ($ts >= $max)
       {
         //Since the data is sorted by date asc then if the timestamp is more than the max we won't find a value
         $value = null;
         break;
       }
    }
    $dataPt[] = $value;
  }
  return $dataPt;
}



function getFollowerData($act, $from, $to)
{
  $start = sqlSafe(date('Y-m-d H:i:s', $from));
  $end = sqlSafe(date('Y-m-d H:i:s', $to));
  $user = sqlSafe($act);

  $query = "SELECT followers, record_date, act_type FROM account_stats WHERE (record_date BETWEEN $start AND $end) AND user_id = $user ORDER BY record_date ASC;";

  $results = readQuery($query);

  $data = array();
  while ($row = $results->fetch_assoc())
  {
    $data[] = $row;
  }

  return $data;
}

function getCombinedFollowerData($type, $from, $to)
{
  $start = sqlSafe(date('Y-m-d H:i:s', $from));
  $end = sqlSafe(date('Y-m-d H:i:s', $to));
  $atype = sqlSafe($type);

  $query = "SELECT sum(followers) as followers, record_date FROM account_stats WHERE (record_date BETWEEN $start AND $end) AND act_type = $atype GROUP BY record_date ORDER BY record_date ASC;";

  $results = readQuery($query);

  $data = array();
  while ($row = $results->fetch_assoc())
  {
    $data[] = $row;
  }

  return $results;
}

 ?>
