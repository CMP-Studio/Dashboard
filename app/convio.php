<?php




function getLumEvents()
{

	$start = tryGet('start');
	$end = tryGet('end');

	$sMonth = date('m',$start);
	$sYear = date('Y',$start);

	$eMonth = date('m',$end);
	$eYear = date('Y',$end);

	$events = array();
	for ($y=$sYear; $y <= $eYear ; $y++) 
	{ 
		for ($m=$sMonth; ($y * 100 + $m)  <= ($eYear * 100 + $eMonth) ; $m++) 
		{ 

		}
	}

}



/* I thought I was going to use this until I found how to output to JSON from convio API */

function parseXML($xml)
{
	$parser = xml_parser_create();
	$output = array();
	xml_parse_into_struct($parser, $xml, $output);

	return $output;
}



?>