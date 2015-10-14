<?php

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
  var_dump($result);
  $xml = simplexml_load_string($result);
  var_dump($xml);
}

 ?>
