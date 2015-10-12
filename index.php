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
      <nav class='navbar navbar-inverse navbar-no-margin navbar-no-borderradius'>
        <div class="container-fluid row">
          <div class="col-xs-3 hidden-sm hidden-md hidden-lg">
            <button type="button" class="navbar-toggle collapsed btn btn-default" data-toggle="collapse" data-target="#side-nav" aria-expanded="true">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          </div>
          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
            <a class="navbar-brand " href="http://studio.carnegiemuseums.org/">
              <img alt="Innovation Studio Logo" src="/resources/img/Innovation-Studio-text.png" class="logo">
            </a>
          </div>
          <div class="col-lg-9 col-md-8 col-sm-7 hidden-xs">
            &nbsp;
          </div>
          <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3">
            <button class="btn help-btn">About</button>
          </div>
        </div>
      </nav>
    </header>

    <div class="spacer">
      &nbsp;
    </div>

    <div class="container-fluid row">
      <div  id="options-spacer"  class="col-lg-2 col-md-3 col-sm-4 col-xs-12" >
        <!-- A spacing fixer for floating -->
        <div class="hidden-xs">
          &nbsp;
        </div>
      </div>
      <!-- Options  (floating)-->
      <div class="nav-float col-lg-2 col-md-3 col-sm-4 col-xs-12 collapse in" id="side-nav">
        <ul id="options-panel" class="nav nav-pills nav-stacked">
          <li role="presentation" id= "cmp" class="active museum">
            <img class='logo inactive' alt='Carnegie Museums of Pittsburgh' src='/resources/img/CMP_SIG_Grey.png'>
            <img class='logo active' alt='Carnegie Museums of Pittsburgh (Active)' src='/resources/img/CMP_SIG_White.png'>
          </li>
          <li role="presentation" id="cmoa" class="museum" >
            <img class='logo inactive' alt='Carnegie Museum of Art' src='/resources/img/cmoa_black.png'>
            <img class='logo active' alt='Carnegie Museum of Art (Active)' src='/resources/img/cmoa_white.png'>
          </li>
          <li role="presentation" id="cmnh" class="museum">
            <img class='logo inactive' alt='Carnegie Museum of Natural History' src='/resources/img/cmnh_black.png'>
            <img class='logo active' alt='Carnegie Museum of Natural History (Active)' src='/resources/img/cmnh_white.png'>
          </li>
          <li role="presentation" id="csc" class="museum">
            <img class='logo inactive' alt='Carnegie Science Center' src='/resources/img/csc_black.png'>
            <img class='logo active' alt='Carnegie Science Center (Active)' src='/resources/img/csc_white.png'>
          </li>
          <li role="presentation" id="warhol" class="museum">
            <img class='logo inactive' alt='Andy Warhol Museum' src='/resources/img/warhol.png'>
            <img class='logo active' alt='Andy Warhol Museum (Active)' src='/resources/img/warhol_white.png'>
          </li>
          <li role="presentation" class="nav-spacer">
            <p>Timespan</p>
          </li>
          <li role="presentation">
            <div class='timespan-picker'>
              <select id='timespan'>
                <option value='lw'>Last Week</option>
                <option value='lm' selected="selected">Last Month</option>
                <option value='l3m'>Previous 3 Months</option>
                <option value='ly'>Last Year</option>
              </select>
            </div>
          </li>
          <li role="presentation" class="nav-spacer">
            <p>Social Media</p>
          </li>
          <li class="twitter social-select" role="presentation">
            Twitter <i class="fa fa-check"></i>
          </li>
          <li class="facebook social-select" role="presentation">
            Facebook <i class="fa fa-check"></i>
          </li>
          <li class="instagram social-select" role="presentation">
            Instagram <i class="fa fa-check"></i>
          </li>
        </ul>
      </div>

    <div id="main-content" class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
      <!-- All content  should be in this div -->
      <div id='infotext'>
        <p>From <b><span id='start-date'></span> to <span id='end-date'></span></b> the <b><span id='museum-text'></span></b> had <b><span id='museum-users'></span></b> people visit their websites.
          Those users viewed <b><span id='pageviews'></span></b> pages.
          During that time, visitors spent an average of <b><span id='time-on-site'></span></b> seconds using the website,
          viewing an average of <b><span id='pages-per-visit'></span></b> pages per visit.
        </p>
      </div>
      <div id='chart-holder'>
        <div id='chart'>
        </div>
      </div>
      <div id='pop-pages'>
        <p class='no-pg-space'>The most popular pages were:</p>
        <ol id='topPages'>
        </ol>
      </div>
      <div id='socialmedia'>
      </div>
      <h2 class='title'>Social Media From <span id='social-start'></span> To <span id='social-end'></span></h2>
      <div id='social-holder'>
      </div>
      <!-- end main content -->
    </div>
  </div>
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

<!--Load account info-->
<?php require_once 'load.php' ?>

</body>
</html>
