<?php

//Database configuration for MySQLi



function getSql()
{

	$sqlUser = 'USER';
	$sqlPass = 'PASS';
	$host = 'localhost';
	$database = 'DATABASE';

	$mysqli = new mysqli($host, $sqlUser, $sqlPass, $database);

	if ($mysqli->connect_errno)
	{

		return null;
	}

	return $mysqli;
}










?>
