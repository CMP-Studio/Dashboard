<?php
require_once __DIR__ . "/sql.php";

function error_logger($name="Error",$message="")
{
	$sName = sqlSafe($name);
	$sMsg = sqlSafe($message);
	$sDate = sqlSafe(date("Y-m-d H:i:s"));
	$query = "INSERT INTO error_log (`timestamp`,`error_name`,`error_description`) VALUES ($sDate,$sName,$sMsg);";
	writeQuery($query);

}

function DoNotCache()
{
  if(isset($_SESSION["do-not-cache"]))
  {
    $_SESSION["do-not-cache"] += 1;
  }
  else {
    $_SESSION["do-not-cache"] = 1;
  }

  return null;
}



?>
