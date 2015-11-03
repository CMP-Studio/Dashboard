<?php
//Twitter configuration



	function getBearerCred()
	{
		$apiKey = "Key";
		$apiSecret = "Secret";

		$ret = base64_encode($apiKey . ":" . $apiSecret);

		return $ret;

	}


?>
