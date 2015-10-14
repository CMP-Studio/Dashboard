<?php
print "<pre>";
$xml = file_get_contents("test.xml");
$data = parseResult($xml);

$start = 1441065600;
$end = 1443657600;
$intv = 24*60*60; //1 day;
$data = toHighcharts($data, $start, $end, $intv);

var_dump($data);

function toHighcharts($data, $start, $end, $intv)
{
  $return = array();

  for ($t=$start; $t <= $end ; $t += $intv)
  {
    $val = 0;
    foreach ($data as $date => $attend)
    {
      $ts = strtotime($date);
      //Find right value
      if($ts >= $t && $ts < $t + $intv)
      {
        //We found it
        $val = intval($attend);
      }
      else if($ts >= $t + $intv)
      {
        break;
      }
    }
    $return[] = $val;
  }

  return $return;
}


function getAttendQuery($loc, $start, $end)
{
  $query = "cast(start_date as date) as AttendDate, cast(sum(t.quantity * i.admissions) as int) as Admissions
 from transact t
 left join items i on (i.department + i.category + i.item = t.department + t.category + t.item)
 where t.salespoint LIKE $loc + '%'
 and (start_date BETWEEN $start AND $end)
 and i.admprconly = 0
 group by cast(start_date as date)
 order by cast(start_date as date)";
}

function parseResult($result)
{
  //Format result;
  $res = str_replace(array("\n", "\r", "\t"), '', $result);
  $res = trim(str_replace('"', "'", $res));

  $doc = new DOMDocument;
  $doc->loadXML($res);
  $rows = $doc->getElementsByTagNameNS('#RowsetSchema', 'row');
  $data = array();
  foreach ($rows as $row)
  {
    $date = $row->getAttribute('attenddate');
    $attend = $row->getAttribute('admissions');
    $data[$date] = $attend;
  }
  return $data;

}

 ?>
