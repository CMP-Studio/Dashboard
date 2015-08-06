<?php
require_once 'app/facebook.php';
?>

<!DOCTYPE html>
<html lang="en" class='merriweather'>
   <head>
      <title>Digital Dashboard</title>
   	<meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta charset="utf-8">
   	<meta http-equiv="content-type" content="text/html; charset=UTF8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />

      <!-- Fonts -->
      <script src="//use.typekit.net/bft4gbb.js"></script>
      <script>try{Typekit.load({ async: true });}catch(e){}</script>

      <!-- CSS -->
      <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css"/>
      <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"  />
      <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
      <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
      <link rel="stylesheet" type="text/css" href="/resources/css/main.css" />


   	

   </head>
   <body class='tk-merriweather'>
      <div id='container'>
         <?php print getFBEmbeedScript(); ?>
        <div id='dialog'></div>

        <header id='topbar'>
           <div id='studiobar' class='clearfix'>
               <h1>Innovation Studio Digital Dahsboard</h1>
            </div>
            <div id='musebar'>
               <div id='cmp' class='fifth'>

               </div>
               <a href='#' id='cmoa' class='fifth active'>
                  <img class='logo' alt='Carnegie Museum of Art' src='/resources/img/cmoa.png'>
                  <img class='active' alt='Carnegie Museum of Art (Active)' src='/resources/img/warhol_white.png'>
               </a>
               <a href='#' id='cmnh' class='fifth'>
                  <img class='logo' alt='Carnegie Museum of Natural History' src='/resources/img/cmnh.png'>
                  <img class='active' alt='Carnegie Museum of Natural History (Active)' src='/resources/img/warhol_white.png'>
               </a>
               <a href='#' id='csc' class='fifth'>
                  <img class='logo' alt='Carnegie Science Center' src='/resources/img/csc.png'>
                  <img class='active' alt='Carnegie Science Center (Active)' src='/resources/img/warhol_white.png'>
               </a>
               <a href='#' id='warhol' class='fifth'>
                  <img class='logo' alt='Andy Warhol Museum' src='/resources/img/warhol.png'>
                  <img class='active' alt='Andy Warhol Museum (Active)' src='/resources/img/warhol_white.png'>
               </a>
            </div>
        </header>

        <div id='content'>
           <div id='pane1' class='clearfix'>
               <h2 class='title'>Web Traffic</h2>
               <div id='chart'>
                  
               </div>
                  

             <div id='infopane'>
             <div id='timespan-picker'>
               <label class='left-label' for='timespan'>Stats for </label>
                  <select id='timespan'>
                     <option value='ly'>Last Year</option>
                     <option value='l3m'>Previous 3 Months</option>
                     <option value='lm' selected="selected">Last Month</option>
                     <option value='lw'>Last Week</option>
                  </select>
               </div>
                  <div id='infotext'>
                     <p><!-- Spacer --></p>
                     <p>From <span id='start-date'>July 1</span> to <span id='end-date'>July 31</span> the <span id='museum-text'>Carnegie Museum of Art</span> had <span id='museum-users'>86,234</span> people visit their websites.
                       Those users viewed <span id='pageviews'>212,345</span> pages.
                     </p>
                     <p>During that time, visitors spent an average of <span id='time-on-site'>74</span> seconds using the website, 
                     viewing an average of <span id='pages-per-visit'>2.4</span> pages per visit.
                     </p>
                     <p><!-- Spacer --></p>
                     <p>The most popular pages during that period were:</p>
                     <p>
                     <ol id='topPages'>
                     </ol>
                     </p>
                  </div>
               </div>
            </div>
            <div id='pane2' class='clearfix'>
               <h2 class='title'>Social Media Last Month</h2>
               <div id='social-holder'>
               <!-- Auto generate -->
                  <div class=' col-md-4 col-xs-12'>
                     <div id='first' class='twitter social-pane panel panel-default'>
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
               <!-- End auto generate -->
            </div>
        </div>
      </div>

      <!-- Javascript -->

      <!-- jQuery -->
      <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
      

      <!-- Bootstrap -->
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
      

      <!-- jQuery UI -->
      <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>


      <!-- Highcharts -->
      <script src="http://code.highcharts.com/stock/highstock.js"></script>


      <!-- D3 -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.js"></script>


      <!--Select2-->
      
      <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

      <!--Moment-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>

      <!--Numeral-->
      <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>
      
      <!--Load account info-->
      <?php require_once 'load.php' ?>

   </body>
</html>