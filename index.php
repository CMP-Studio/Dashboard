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
  <link rel="stylesheet" type="text/css" href="/resources/css/bootstrap.min.css"  />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <!-- Studio Theme -->
  <link rel="stylesheet" type="text/css" href="/resources/css/bootstrap-theme.min.css" />
  <link rel="stylesheet" type="text/css" href="/resources/css/bootstrap-studio.css" />
  <link rel="stylesheet" type="text/css" href="/resources/css/main.css" />

  <!-- Google Analytics -->
  <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  //Unified tracker
  ga('create', 'UA-65401320-1', 'auto');
  ga('send', 'pageview');
  </script>



</head>
<body class='tk-merriweather'>
  <div id='container'>
    <?php print getFBEmbeedScript(); ?>
    <div id='dialog'></div>

    <!-- Header and navigation -->
    <header class='navbar-fixed-top'>
      <nav id="primary-nav" class='navbar navbar-inverse navbar-no-margin navbar-no-borderradius page-padding'>
        <div class="container-fluid row">

          <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
            <h1 class="title">Digital Metric Dashboard</h1>
          </div>
          <div class="col-xs-3 hidden-sm hidden-md hidden-lg">
            <button id="museum-bar-toggle" type="button" class="navbar-toggle collapsed btn btn-default" data-toggle="collapse" data-target="#museum-bar" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-9">
            <div class="timespan">
              <h3 class='sr-only'>Stats for the selected timespan</h3>
              <select id='timespan'>
                <option value='lw'>Last 7 Days</option>
                <option value='lm' selected="selected">Last Month</option>
                <option value='l3m'>Previous 3 Months</option>
                <option value='ly'>Last Year</option>
              </select>
            </div>
          </div>
        </div>
      </nav>
      <nav id="secondary-nav" class="navbar navbar-inverse navbar-no-margin navbar-no-border navbar-no-borderradius museum-bar page-padding">
        <div id="museum-bar" class="container-fluid row collapse navbar-collapse">

          <div id="cmp" class="museum active col-sm-5ths">
            <img class='logo inactive' alt='Carnegie Museums of Pittsburgh' src='/resources/img/CMP_SIG_Grey.png'>
            <img class='logo active' alt='Carnegie Museums of Pittsburgh (Active)' src='/resources/img/CMP_SIG_White.png'>
          </div>

          <div id="cmoa" class="museum col-sm-5ths">
            <img class='logo inactive' alt='Carnegie Museum of Art' src='/resources/img/cmoa_black.png'>
            <img class='logo active' alt='Carnegie Museum of Art (Active)' src='/resources/img/cmoa_white.png'>
          </div>

          <div id="cmnh" class="museum col-sm-5ths">
            <img class='logo inactive' alt='Carnegie Museum of Natural History' src='/resources/img/cmnh_black.png'>
            <img class='logo active' alt='Carnegie Museum of Natural History (Active)' src='/resources/img/cmnh_white.png'>
          </div>

          <div id="csc" class="museum col-sm-5ths">
            <img class='logo inactive' alt='Carnegie Science Center' src='/resources/img/csc_black.png'>
            <img class='logo active' alt='Carnegie Science Center (Active)' src='/resources/img/csc_white.png'>
          </div>

          <div id="warhol" class="museum col-sm-5ths">
            <img class='logo inactive' alt='Andy Warhol Museum' src='/resources/img/warhol.png'>
            <img class='logo active' alt='Andy Warhol Museum (Active)' src='/resources/img/warhol_white.png'>
          </div>

        </div>
        <div class="hidden-sm hidden-md hidden-lg active-museum">
          <img id="m-warhol" class='logo' alt='Andy Warhol Museum (Active)' src='/resources/img/warhol_white.png'>
          <img id="m-csc" class='logo' alt='Carnegie Science Center (Active)' src='/resources/img/csc_white.png'>
          <img id="m-cmnh" class='logo' alt='Carnegie Museum of Natural History (Active)' src='/resources/img/cmnh_white.png'>
          <img id="m-cmoa" class='logo' alt='Carnegie Museum of Art (Active)' src='/resources/img/cmoa_white.png'>
          <img id="m-cmp" class='logo active' alt='Carnegie Museums of Pittsburgh (Active)' src='/resources/img/CMP_SIG_White.png'>
        </div>
      </nav>

    </header>

<!-- All content  should be in this div -->
    <div class="main-body ">
      <!-- White box -->
      <div class="white-bg page-padding">
        <div class="row">
          <div id='infotext' class="col-xs-12 col-sm-12">
            <p>From <b><span id='start-date'></span> to <span id='end-date'></span></b> the <b><span id='museum-text'></span></b> had <b><span id='museum-users'></span></b> people visit their websites.
              Those users viewed <b><span id='pageviews'></span></b> pages.
              During that time, visitors spent an average of <b><span id='time-on-site'></span></b> seconds using the website,
              viewing an average of <b><span id='pages-per-visit'></span></b> pages per visit.
            </p>
          </div>
        </div>
        <div class="row">
          <div id='chart' class="col-xs-12 col-sm-12">
          </div>

          <div id='socialmedia' class="social-display hidden-xs col-sm-3">
          </div>
        </div>
        <div class="col-center">
          <div class="row legend legend-stats">
            <div class="col-sm-4 col-xs-6">
              <div class="legend-toggle legend-views active" data-series="views">Views  <i class="fa fa-check"></i></div>
            </div>
            <div class="col-sm-4 col-xs-6">
              <div class="legend-toggle legend-users active" data-series="users">Users  <i class="fa fa-check"></i></div>
            </div>
            <div class="col-sm-4 col-xs-6">
              <div class="legend-toggle legend-admissions active" data-series="admissions">Admission  <i class="fa fa-check"></i></div>
            </div>
          </div>
        </div>
        <div class="">
          <div class="row legend legend-social">
            <div class="col-sm-3 col-xs-6">
              <div class="legend-toggle legend-anomolies" data-series="anomolies">Anomolies  <i class="fa fa-check"></i></div>
            </div>
            <div class="col-sm-3 col-xs-6">
              <div class="legend-toggle legend-twitter" data-series="twitter">Twitter  <i class="fa fa-check"></i></div>
            </div>
            <div class="col-sm-3 col-xs-6">
              <div class="legend-toggle legend-facebook" data-series="facebook">Facebook  <i class="fa fa-check"></i></div>
            </div>
            <div class="col-sm-3 col-xs-6">
              <div class="legend-toggle legend-instagram" data-series="instagram">Instagram  <i class="fa fa-check"></i></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Grey box -->

      <div class="grey-bg page-padding grey-box">
        <div class="row">
          <div id='social-holder'>
          </div>
        </div>
      </div>

      <!-- Black box -->
      <div class="darkgrey-bg page-padding black-box">
        <div class="row footer">
          <a href="http://studio.carnegiemuseums.org" target="_blank">
            <img class='footer-logo' alt="Innovation Studio Logo" src="/resources/img/Innovation-Studio-logo-footer1.png">
          </a>
          <p>
            Carnegie Museums of Pittsburgh <br/>
            Innovation Studio <br/>
            4400 Forbes Avenue <br/>
            Pittsburgh, PA 15213 <br/>
            <a href="http://www.carnegiemuseums.org/interior.php?pageID=100">Privacy Policy</a>
          </p>
          <p>
            Made with ♥ in the City of Champions
          </p>
        </div>
      </div>

<!-- Where oh where should these go?



<div id='pop-pages' class="col-xs-12 col-sm-12">
  <p class='no-pg-space'>The most popular pages were:</p>
  <ol id='topPages'>
  </ol>
</div>

-->

      <!-- end main content -->
    </div>




    <div id="help-panel" title="About this Dashboard">
      <p>This dashboard includes a summary of information from the Carnegie Museums of Pittsburgh various web assets.</p>
      <p>Web activity shows a timeline of events that occured during the selected timespan including how many people viewed pages on our websites, popular social media posts, and pages that recived a spike in traffic.</p>
      <p>The Social Media section shows the post(s) viewers responded to the most and the pages that they went to after seeing a post.  It also keeps a track of our followers and the change of followers month to month.</p>
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

    <!-- Main JS script -->
    <script src="/resources/js/main.js"></script>

    <!--Load account info-->
    <?php require_once 'load.php' ?>

  </body>
  </html>
