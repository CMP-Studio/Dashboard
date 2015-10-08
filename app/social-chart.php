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
  $data = getFollowerData($act,$from,$to);

  var_dump($data);


}



function getFollowerData($act, $from, $to)
{
  $start = sqlSafe(date('Y-m-d H:i:s', $from));
  $end = sqlSafe(date('Y-m-d H:i:s', $to));
  $user = sqlSafe($act);

  $query = "SELECT followers, record_date, act_type FROM account_stats WHERE (record_date BETWEEN $start AND $end) AND user_id = $user ORDER BY record_date ASC;";

 print $query;
  $results = readQuery($query);

  return $results;
}

function getCombinedFollowerData($type, $from, $to)
{
  $start = sqlSafe(date('Y-m-d H:i:s', $from));
  $end = sqlSafe(date('Y-m-d H:i:s', $to));
  $atype = sqlSafe($type);

  $query = "SELECT sum(followers) as followers, record_date FROM account_stats WHERE (record_date BETWEEN $start AND $end) AND act_type = $atype GROUP BY record_date ORDER BY record_date ASC;";

  $results = readQuery($query);

  return $results;
}

 ?>
