<?php
require_once __DIR__ . '/../config/googleConfig.php';

require_once getAPIPath() . 'autoload.php';





function getClient()
{
	$client = new Google_Client();
	$client->setApplicationName(getAppName());
	$client->setClientId(getClientID());
	
	return $client;
	
}

/* Checks for previous authentication then runs AUth if needed  returns auth token*/
function Authenticate($client)
{
	if (isset($_SESSION['service_token'])) 
	{
		$token = $_SESSION['service_token'];
		
		if ($client->getAuth()->isAccessTokenExpired())
		{
			$token = Auth($client);
			$_SESSION['service_token'] = $token;
		}
		else
		{
			$client->setAccessToken($_SESSION['service_token']);
		}
	}
	else
	{
		$token = Auth($client);
		$_SESSION['service_token'] = $token;
	}
	
	return $token;

}

/* Generates new token for the client */
function Auth($client)
{

	$key = file_get_contents(getKeyFile());


	$scope = array('https://www.googleapis.com/auth/analytics');

	$cred = new Google_Auth_AssertionCredentials(
					getAcctName(),
					$scope,
					$key
					);
	$client->setAssertionCredentials($cred);

	if ($client->getAuth()->isAccessTokenExpired()) {

		try {

		$client->getAuth()->refreshTokenWithAssertion($cred);
} catch (Exception $e) {
}

	}

	return $client->getAccessToken();
}

function runQuery(&$analytics, $tableId, $startDate, $endDate, $metrics, $dimmensions = '', $sort = '', $maxResults = '1000',  $filters = '', $segment = '') {
   try{
   $params = array();
   if(!empty($dimmensions))
   {
		$params['dimensions'] =  $dimmensions;
   }
   if(!empty($sort))
   {
		$params['sort'] =  $sort;
   }
    if(!empty($filters))
   {
		$params['filters'] =  $filters;
   }
	if(!empty($segment))
   {
		$params['segment'] =  $segment;
   }
   if($maxResults != '1000'  && !empty($maxResults))
   {
		$params['max-results'] =  $maxResults;
   }
   
   
   
   $results = $analytics->data_ga->get(
		$tableId,
		$startDate,
		$endDate,
		$metrics,
		$params
		);
		
	return $results;
	}
	catch (apiServiceException $e)
	{
		return $e->getMessage();
	}
}

?>