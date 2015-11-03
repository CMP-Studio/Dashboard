<?php



//From Google Dev (https://console.developers.google.com)
function getGAClientID()
{
	return 'Client ID';
}
//Service email address
function getGAAcctName()
{
	return 'Service email address';
}
//Application Name
function getGAAppName()
{
	return "Application Name";
}


//File locations
//.p12 file location
function getKeyFile()
{
	return __DIR__ . '/key-file-location.p12';
}
//Google API path (include trailing /)
function getAPIPath()
{
	return __DIR__ . '/../plugins/google-api/';
}




?>
