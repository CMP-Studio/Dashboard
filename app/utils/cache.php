<?php
require_once 'sqlConfig.php';

$cacheLimit = 10*24*60*60; //10 days



function loadFromCache($datasetName)
{

	$sql = getSql();
	if($sql != null)
	{
		if(checkCache($datasetName))
		{
			$query = "SELECT data FROM datacache WHERE dataset = '$datasetName'";
			if ($result = $sql->query($query))
			{
				$row = $result->fetch_assoc();
				return $row['data'];
			}
		}
	}
	return null;
}

function storeInCache($datasetName, $data)
{
	$sql = getSql();
	if($sql != null)
	{
		if(cacheExists($datasetName))
		{
			//Update
			if($sql->query("UPDATE datacache SET data = '$data' WHERE dataset = '$datasetName'") == TRUE)
			{
				return true;
			}
		}
		else
		{
			//Insert
			if($sql->query("Insert into datacache (dataset, data) values ('$datasetName', '$data')") == TRUE)
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
	cleanCache();
	$sql = getSql();
	if($sql != null)
	{
		$query = "SELECT updated FROM datacache WHERE dataset = '$datasetName'";
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
		$query = "SELECT * FROM datacache WHERE dataset = '$datasetName'";
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

function cleanCache()
{
	global $cacheLimit;
	$sql = getSql();
	if($sql != null)
	{
		$date = date("Y-m-d H:i:s" ,time() - $cacheLimit);
		$query = "delete from datacache where updated < '$date'";

		if($sql->query($query))
		{
			return true;
		}

	}
	return false;


}



































?>
