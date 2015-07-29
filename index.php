<?php
require_once 'app/facebook.php';
   $start = strtotime("-1 month -1 day 12:00am");
   $end = strtotime("-1 day 12:00am");
  // print "Start $start\nEnd $end";
?>

<!DOCTYPE html>
<head>

	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta http-equiv="content-type" content="text/html; charset=UTF8">


	<!-- jQuery -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

	<!-- Bootstrap -->
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
   <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />

   <!-- Highcharts -->
   <script src="http://code.highcharts.com/stock/highstock.js"></script>


   <!-- D3 -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.js"></script>

<!--Select2-->
   <!--<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
   <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>-->

   <link rel="stylesheet" type="text/css" href="/resources/css/main.css" />
   <script>
   	$(document).ready(function (){ 

   		var loc = "csc";


   		var url = "./app/ajax.php?action=chart&location=" + loc + "&chart=pageviews&end=<?php print $end; ?>&start=<?php print $start; ?>";
   		$.getJSON(url, function(cdata) 
   		{
   			console.log(cdata);
   			
            //setupTooltip();

		   		url = "./app/ajax.php?action=events&location=" + loc + "&end=<?php print $end; ?>&start=<?php print $start; ?>";
               console.log(url);
				$.getJSON(url, function(edata) 
		   		{
                  $('#main-chart .chart').highcharts(cdata);
		   			console.log(edata);
		   			events(edata);
		   		})
		   		.fail(function() {
		   			alert("Failure - Events");
		   		});
	   		})
	   		.fail(function() {
	   			alert("Failure - Chart");
	   		});

   		$("#tweet").dialog({
   			autoOpen: false,
   			minWidth: 600,
   			position: { my: "center top", at: "center top", of: window }
   		})


   		function maxScore(events)
   		{
   			var max = -999999;
   			for (var i = events.length - 1; i >= 0; i--) {
   				var e = events[i];

   				if(e.score > max) max = e.score;
   			};
   			return max;
   		}

         function setupTooltip()
         {
            var tt = d3.select('.highcharts-tooltip');
            tt.style('display','none');

            var markers = d3.select('.highcharts-markers').selectAll('path');
            markers.style('cursor','hand');
            markers.on('click', function()
            {
               tt.style('display',null);
            })
            .on('mouseleave', function()
            {
               tt.style('display','none');
            })


         }
   		
   		function events(data)
   		{
   			var svg = d3.select("#highcharts-0 svg");

   			var layer = svg.insert("g",".highcharts-tooltip").attr("id","events");

   			var events = data.events;
   			var start = data.start;
   			var end = data.end;

   			var axis = d3.select(".highcharts-yaxis-labels").node();
   			var box = axis.getBBox();

            var h = box.height;
            var t = box.y;

            var axis = d3.select(".highcharts-series-group").node();
            var box = axis.getBBox();

   			var w = box.width;
   			var l = box.x;



   			var max = maxScore(events);

            var seg = h/12;


   			var xS = d3.time.scale().domain([getDate(start), getDate(end)]).range([l,w]);
   			var yS = d3.scale.linear().domain([0, 4]).range([h - seg/2, h-(4*seg) - seg/2]);
            var subYs = d3.scale.linear().domain([0,max]).range([15, seg-15]);
   			//console.log(max);
   			//.log(xS);
   			//.log(start);

   			var circles =  layer.selectAll("circle")
   								.data(events)
   								.enter()
   								.append("circle")
   								.attr("cx", function(d) {
   									//.log(getDate(d.timestamp));
   									return xS(getDate(d.timestamp))
   								})
   								.attr("cy", function(d)
   								{
   									 switch(d.source)
                              {
                                 case 'Twitter':
                                    return yS(1); //Twitter blue
                                 case 'Google Analytics':
                                    return yS(0); //GA orange
                                 case 'Facebook':
                                    return yS(2); //FB blue
                                 case 'Instagram':
                                    return yS(3);  //IG brown
                                 default:
                                    return yS(4);
                              }

   								})
   								.attr("r", "4")
   								.style("fill",function(d)
                           {
                              switch(d.source)
                              {
                                 case 'Twitter':
                                    return "rgba(80,171,241,1)"; //Twitter blue
                                 case 'Google Analytics':
                                    return "rgba(247,153,28, 1)"; //GA orange
                                 case 'Facebook':
                                    return "rgba(68,97,157, 1)"; //FB blue
                                 case 'Instagram':
                                    return "rgba(185,163,140, 1)"  //IG brown
                                 default:
                                    return "rgba(255,255,255,1)";
                              }

                           })
   								.style("cursor","hand")
   								.on('click',function(d)
   									{
   										//.log(d);
   										$("#tweet").html(d.html);
   										$("#tweet").dialog("option","title",d.title);
   										$("#tweet").dialog("open");
   									})
   								.on('mouseenter', function(d){
   									//console.log(this);
   									d3.select(this).attr("r","6");
   								})
   								.on('mouseleave', function(d)
   								{
   									d3.select(this).attr("r","4");
   								})
   								;
   		}

   		function getDate(d) {
		    return new Date(d);
		}



   	});
   </script>
</head>
<body>
<?php print getFBEmbeedScript(); ?>
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
<div id='tweet'>

</div>
</body>