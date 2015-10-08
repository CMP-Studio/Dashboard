<?php
require_once "utils/highcharts.php";
require_once "utils/api.php";
require_once 'utils/sql.php';

generateSocialChart();

function generateSocialChart()
{
  $from = 1443657600;
  $to = 1446249600;
  $act = "19412366";
  $intv = 24*60*60; //1 day
  $data = getFollowerData($act,$from,$to);

  $nData = formatData($from, $intv, $to, $data);

  var_dump($nData);


}

function formatData($start, $intv, $end, $data)
{
  $dataPt = array();
  for ($i=$start; $i <= $end; $i+= $intv)
  {
    $min = $i;
    $max = $i + $intv - 1;

    $value = -1;
    foreach (&$data as $key => $row)
    {
       $ts = strtotime($row["record_date"]);
       if($ts >= $min && $ts <= $max)
       {
         //In range
         $value = $row["followers"];
         unset($row); //Remove from pool
         break;
       }
       else if ($ts >= $max)
       {
         //Since the data is sorted by date asc then if the timestamp is more than the max we won't find a value
         $value = 0;
         break;
       }
    }
    if($value < 0)
    {
      $value = 0;
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

 print $query;
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
