<?php


?>

<!DOCTYPE html>
<head>

	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
   <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />

   <script src="http://code.highcharts.com/stock/highstock.js"></script>

<!--Select2-->
   <!--<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
   <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>-->

   <link rel="stylesheet" type="text/css" href="/resources/css/main.css" />
   <script>
   	$(document).ready(function (){ 
   		var url = "./app/ajax.php?action=chart&account=ga:53193816&chart=pageviews";
   		$.getJSON(url, function(data) 
   		{
   			console.log(data);
   			$('#main-chart .chart').highcharts(data);
   		})
   		.fail(function() {
   			alert("Failure");
   		});
   	});
   </script>
</head>
<body>
  <div id='topbar'>

  </div>
  <div id='content'>
    <div id='chart-container'>
      <div id='main-chart'>

        <div class='chart'>

        </div>
      </div>
    </div>
  </div>

</body>