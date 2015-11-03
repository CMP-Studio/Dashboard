<?php

/* returns an array of colors for use in series plots */
function getColorScheme()
{
	$colors = array();

	//Orange
	array_push($colors, "rgb(222,73,47)");
	
	//Dark blue
	array_push($colors, "rgb(77,102,132)");

	//Orange
	array_push($colors, "rgb(222,73,47)");

	//Orange
	array_push($colors, "rgb(222,73,47)");
	
	//Orange
	array_push($colors, "rgb(222,73,47)");

	
	return $colors;

}

function insertScripts()
{
?>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

<script src="jquery/jquery-1.11.2.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="highstock/js/highstock.js"></script>
<script src="highstock/js/modules/drilldown.js"></script>

<?php
}




?>