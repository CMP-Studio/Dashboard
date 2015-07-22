<?php
/* 
This is the main application file
It handles all the AJAX calls from other web pages
Most of the functions should be handled in other files but this will delegate calls to those files.
*/


/* source: https://jonsuh.com/blog/jquery-ajax-call-to-php-script-with-json-return/ */
if (is_ajax() || true) //Remove TRUE when done testing
{
  if (isset($_GET["action"]) && !empty($_GET["action"])) //Checks if action value exists
  	{
    	delegate($_GET["action"]);

	}
}
function is_ajax()
{
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
/* end */



/* Main function to delegate actions */
function delegate($action)
{

}



?>
