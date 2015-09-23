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

    <header id='topbar'>
      <div id='studiobar' class='clearfix'>

        <a href='//studio.carnegiemuseums.org' class='fifth'>
          <img class='studio-logo' src='/resources/img/Innovation-Studio-text.png' alt='Innovation Studio Logo'/>
        </a>
        <div class='three-fifth'>
          <h1 class='header-title'>Digital Dashboard</h1>
        </div>
        <div class='fifth'>

        </div>
      </div>
      <div id='musebar'>
        <div class='fifth museum active'>
          <a href='#' id='cmp'>
            <i class="fa fa-bars"></i>
            <img class='logo' alt='Carnegie Museums of Pittsburgh' src='/resources/img/CMP_SIG_Grey.png'>
          </a>
          <img class='active' alt='Carnegie Museums of Pittsburgh (Active)' src='/resources/img/CMP_SIG_White.png'>
        </div>
        <div class='fifth museum'>
          <div id='cmoa'>
            <i class="fa fa-bars"></i>
            <img class='logo' alt='Carnegie Museum of Art' src='/resources/img/cmoa.png'>
          </div>
          <img class='active' alt='Carnegie Museum of Art (Active)' src='/resources/img/warhol_white.png'>
        </div>
        <div class='fifth museum'>
          <div id='cmnh'>
            <i class="fa fa-bars"></i>
            <img class='logo' alt='Carnegie Museum of Natural History' src='/resources/img/cmnh.png'>
          </div>
          <img class='active' alt='Carnegie Museum of Natural History (Active)' src='/resources/img/warhol_white.png'>
        </div>
        <div class='fifth museum'>
          <div id='csc'>
            <i class="fa fa-bars"></i>
            <img class='logo' alt='Carnegie Science Center' src='/resources/img/csc.png'>
          </div>
          <img class='active' alt='Carnegie Science Center (Active)' src='/resources/img/warhol_white.png'>
        </div>
        <div class='fifth museum'>
          <div id='warhol'>
            <i class="fa fa-bars"></i>
            <img class='logo' alt='Andy Warhol Museum' src='/resources/img/warhol.png'>
          </div>
          <img class='active' alt='Andy Warhol Museum (Active)' src='/resources/img/warhol_white.png'>
        </div>

        <div class='clearfix'></div>
      </div>
    </header>

    <div id='content'>
      <div id='pane1' class='clearfix'>
        <h2 class='title'>Web Traffic</h2>
        <div id='chart'>

        </div>


        <div id='infopane'>
          <div id='timespan-picker'>
            <h3 class='sr-only'>Stats for the selected timespan</h3>
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
            <p>From <span id='start-date'></span> to <span id='end-date'></span> the <span id='museum-text'></span> had <span id='museum-users'></span> people visit their websites.
              Those users viewed <span id='pageviews'>212,345</span> pages.
            </p>
            <p>During that time, visitors spent an average of <span id='time-on-site'></span> seconds using the website,
              viewing an average of <span id='pages-per-visit'></span> pages per visit.
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
        <h2 class='title'>Social Media From <span id='social-start'></span> To <span id='social-end'></span></h2>
        <div id='social-holder'>
        </div>
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
