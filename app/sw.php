<?php
print "<pre>";
$xml = file_get_contents("test.xml");
parseResult($xml);


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
  var_dump($rows);
}

 ?>
