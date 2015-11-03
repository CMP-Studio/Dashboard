# Digital Metrics Dashboard

## About

[A description about the project in detail can be found here](http://studio.carnegiemuseums.org/digital-dashboard/)

The gist of the project is that we wanted to create an easy to use way to display Google Analytics data along with social media and admission data.

## Details

This project is a single page website designed to run off of a LAMP (or LEMP) server.

#### Structure

* App - This is the folder where the "code behind" lives, it pulls and formats all the data that goes into making the dashboard.
	* config.sample - Sample configuration files, rename folder to config to use and to keep these details secret
	* cron - Cron job files.  These will have to be manually setup to run at whatever frequency you wish.
	* utils - Utility files which are used to preform actions like calling APIs, Caching, or running SQL queries.
*  Resources - static files: CSS, JS, Img, etc...
*  index.php - The website.
*  load.php - This is loaded by index.php it essentially is a javascript file that loads all of the data 	  dynamically.  It is only in a different file to sperate out th Javascript portion from the HTML of index.php


## Future Features

* Disabled Selectors - Grey out selections which do not have any data associated with them.  For example, Warhol does not have an Instagram account so Instagram should always be greyed out.

* Data export - The abillity to export data into a CSV file or similar

* Percent change - show percent change on social badges. 
