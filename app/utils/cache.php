<?php
require_once __DIR__ . '/../config/sqlConfig.php';

$cacheLimit = 40*24*60*60; //40 days



function loadFromCache($datasetName)
{
	global $cacheLimit;

	$sql = getSql();




	if($sql != null)
	{

		$dsn = $sql->real_escape_string($datasetName);



		$query = "SELECT data, updated FROM datacache WHERE dataset = '$dsn'";
		if ($result = $sql->query($query))
		{
			$row = $result->fetch_assoc();
			if($row == NULL) return null;

			$updated = strtotime($row['updated']);
			$now = time();


			//Check for cache expiry
			if(($now-$updated) > $cacheLimit) return null;

			//valid!
			return $row['data'];
		}

	}
	return null;
}
function escape($data)
{
	return $sql->real_escape_string($data);
}



function storeInCache($datasetName, $data)
{
	if(isset($_SESSION['do-not-cache']))
	{
		if($_SESSION['do-not-cache'] == 1)
		{
			unset($_SESSION['do-not-cache']);
		}
		else {
			$_SESSION['do-not-cache'] -= 1;
		}
		return false;
	}
	$sql = getSql();
	$now = date("Y-m-d H:i:s");


	if($sql != null)
	{
		$dsn = $sql->real_escape_string($datasetName);
		$data = $sql->real_escape_string($data);
		if(cacheExists($datasetName))
		{
			//Update
			if($sql->query("UPDATE datacache SET data = '$data', updated = '$now'  WHERE dataset = '$dsn'") == TRUE)
			{
				return true;
			}
		}
		else
		{
			//Insert
			if($sql->query("INSERT into datacache (dataset, data, updated) values ('$dsn', '$data', '$now')") == TRUE)
			{
				return true;
			}
		}
	}

	return false;
}

// Check if cache is valid - run automatically with load
function checkCache($datasetName)
{

	global $cacheLimit;
	//cleanCache();

	$sql = getSql();


	if($sql != null)
	{
		$dsn = $sql->real_escape_string($datasetName);
		$query = "SELECT updated FROM datacache WHERE dataset = '$dsn'";
		if ($result = $sql->query($query))
		{
			$row = $result->fetch_assoc();
			if($row == NULL) return false;

			$updated = strtotime($row['updated']);
			$now = time();

			//Check for cache expiry
			if(($now-$updated) > $cacheLimit) return false;
			//valid!
			return true;
		}

	}
	return false;
}

// Check if cache exists (does not check validity) used in store to determine update vs. insert for store
function cacheExists($datasetName)
{

	$sql = getSql();

	if($sql != null)
	{
		$dsn = $sql->real_escape_string($datasetName);
		$query = "SELECT * FROM datacache WHERE dataset = '$dsn'";
		if ($result = $sql->query($query))
		{
			$row = $result->fetch_assoc();
			if($row != NULL) return true;
		}

	}
	return false;
}

function dropCache()
{
	$sql = getSql();
	if($sql != null)
	{
		$query = "truncate table datacache";
		if($sql->query($query) == TRUE)
		{
			return true;
		}

	}
	return false;

}

function cleanCache($timespan = null)
{
	global $cacheLimit;
	if(!isset($timespan)) $timespan = $cacheLimit;
	$sql = getSql();
	if($sql != null)
	{
		$date = date("Y-m-d H:i:s" ,time() - $timespan);
		$query = "delete from datacache where updated < '$date'";

		if($sql->query($query))
		{
			return true;
		}

	}
	return false;


}



































?>
