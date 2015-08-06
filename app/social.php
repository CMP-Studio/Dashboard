<?php
require_once 'utils/api.php';
require_once 'twitter.php';
require_once 'facebook.php';
require_once 'ganalytics.php';
require_once 'instagram.php'; 


/* This file is used to generate social media 'badges' unlike other api files this one will simply output the HTML for simplicity*/



/* Goal is to create this automatically

<div class=' col-md-4 col-xs-12'>
     <div class='twitter social-pane panel panel-default'>
        <div class='social-title panel-heading clearfix'>
           <div class='logo' title='Twitter'></div>
           <h3 class='panel-title'>@Dippy_the_Dino</h3>
        </div> 
        <div class='social-body panel-body'>
           <h4>Total Followers: <b>895,435</b></h4>
           <h4>Change in Followers: <i class='fa fa-chevron-up'></i> <b>2,201</b></h4>
           <h4>Top Pages Visited From Twitter</h4>
           <div class='social-urls'>
              <ol>
                 <li>www.carnegiemnh.org/dippy</li>
                 <li>www.carnegiemnh.org/dippy2</li>
                 <li>www.carnegiemnh.org/dippy3</li>
                 <li>www.carnegiemnh.org/dippy4</li>
                 <li>www.carnegiemnh.org/dippy5</li>
              </ol>
           </div>
           <h4>Top Tweet</h4>
           <div class='social-embeed'>
              <blockquote class="twitter-tweet" lang="en"><p lang="en" dir="ltr">Happy birthday <a href="https://twitter.com/hashtag/AndyWarhol?src=hash">#AndyWarhol</a>! In honor of the pop icon&#39;s 87th b-day I &quot;Warholized&quot; this pic of myself. <a href="https://twitter.com/TheWarholMuseum">@TheWarholMuseum</a> <a href="http://t.co/6FNisBLGGo">pic.twitter.com/6FNisBLGGo</a></p>&mdash; Dippy the Dinosaur (@Dippy_the_Dino) <a href="https://twitter.com/Dippy_the_Dino/status/629312367545982976">August 6, 2015</a></blockquote>
              <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
           </div>
        </div>
     </div>
  </div>
</div>

*/

function generateBadge()
{


	



}

function getAccountsFromLocation($loc)
{
	
	//Get accounts
	$accounts = json_decode(file_get_contents('config/accounts.json'), true);
	$locAccounts = $accounts['location'][$loc]['accounts'];

	$html = '';

	foreach ($locAccounts as $key => $a) 
	{
		$html += getBadgeHTML($a);
	}
	
	return $html;

}

function getBadgeHTML($acctInfo)
{
	$html = "<div class=' col-md-4 col-xs-12'>\n";
}