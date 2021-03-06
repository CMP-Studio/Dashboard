<script>
$(document).ready(function (){


  //Defaults
  var timespan = getLastMonth();
  var museum = 'cmp';
  var lastrequest = null;
  //globals
  var event_data = null;
  var event_srcs = null;
  //main
  setupChartResize();
  loadAnalytics(museum, timespan.start, timespan.end);

  //Social timespan
  /*
  $("#social-start").text(moment().subtract(1, 'month').startOf('month').format("MMMM D, YYYY"));
  $("#social-end").text(moment().startOf('month').format("MMMM D, YYYY"));
  */

  $('#timespan').change(function ()
  {
    var t = null;
    switch($( this ).val())
    {

      case 'lw':
      t = getLastWeek();
      break;
      case 'l3m':
      t= getLast3Months();
      break;
      case 'lm':
      t = getLastMonth();
      break;
      case 'ly':
      t= getLastYear();
      break;
    }
    timespan = t;
    loadAnalytics(museum, timespan.start, timespan.end);
  });

  //Select museum
  $('#cmp').click(function()
  {
    if($(this).hasClass('active')) { return; }
    museum='cmp';
    setActiveMuseum(museum);
    loadAnalytics(museum, timespan.start, timespan.end);
  });
  $('#cmoa').click(function()
  {
    if($(this).hasClass('active')) { return; }
    museum='cmoa';
    setActiveMuseum(museum);
    loadAnalytics(museum, timespan.start, timespan.end);
  });
  $('#cmnh').click(function()
  {
    if($(this).hasClass('active')) { return; }
    museum='cmnh';
    setActiveMuseum(museum);
    loadAnalytics(museum, timespan.start, timespan.end);
  });
  $('#warhol').click(function()
  {
    if($(this).hasClass('active')) { return; }
    museum='warhol';
    setActiveMuseum(museum);
    loadAnalytics(museum, timespan.start, timespan.end);
  });
  $('#csc').click(function()
  {
    if($(this).hasClass('active')) { return; }
    museum='csc';
    setActiveMuseum(museum);
    loadAnalytics(museum, timespan.start, timespan.end);
  });

  function setActiveMuseum(loc)
  {

    $('#cmp').removeClass('active');
    $('#csc').removeClass('active');
    $('#cmoa').removeClass('active');
    $('#cmnh').removeClass('active');
    $('#warhol').removeClass('active');

    $('#m-cmp').removeClass('active');
    $('#m-csc').removeClass('active');
    $('#m-cmoa').removeClass('active');
    $('#m-cmnh').removeClass('active');
    $('#m-warhol').removeClass('active');

    $('#' + loc).addClass('active');
    $('#m-' + loc).addClass('active');

  }


  //Load the data

  function loadAnalytics(loc, start, end)
  {
    //These two should equal unless another AJAX is called
    lastrequest = Date.now();
    var myrequest = Date.now();

    $('#chart').html("<img src='/resources/img/loader.gif' class='loader'>");
    var adata = getActs();

    var srcs = getSources(adata, loc);

    var Curl = "./app/ajax.php?action=chart&chart=dashboard&location="  + loc + "&end=" + end + "&start=" + start;

    var social = true;


    if(moment.unix(start).isBefore( moment.unix(end).subtract('32','days') )) social = false;

    if(social)
    {
      if('twitter' in srcs)
      {
        Curl += "&twitter=1";
      }
      if('facebook' in srcs)
      {
        Curl += "&fb=1";
      }
      if('instagram' in srcs)
      {
        Curl += "&ig=1";
      }
    }
    if('google analytics' in srcs)
    {
      Curl += "&ga=1";
    }

    $.getJSON(Curl, function(cdata)
    {

     //console.log(cdata);


      var Eurl = "./app/ajax.php?action=events&location=" + loc + "&end=" + end + "&start=" + start;

      if(!social) Eurl += "&longterm=1";

      $.getJSON(Eurl, function(edata)
      {
        if(lastrequest == myrequest)
        {
          $('.loader').remove();
          $('#chart').highcharts(cdata);
          //setupTooltip();
          setupLegend();
          toggleChartSize();
          event_data = edata;
          event_srcs = srcs;
          events(edata, srcs);
          //console.log(edata);
        }
        else
        {
          console.info('AJAX load canceled: Not most recent call');
        }

      })
      .fail(function() {
        console.error("Failure - Events: " + Eurl);
        $('.loader').remove();
      });
})
.fail(function() {
  console.error("Failure - Chart: " + Curl);
  $('.loader').remove();
});
$('#infotext').hide();
var url = "./app/ajax.php?action=stats&location=" + loc + "&end=" + end + "&start=" + start;
$.getJSON(url).done(function (data){

  if(lastrequest != myrequest)
  {
    console.info('AJAX load canceled: Not most recent call');
    return;
  }

  //console.log(data);


  var start_s = moment.unix(start).format("MMMM D, YYYY");
  var end_s = moment.unix(end).format("MMMM D, YYYY");
  var mus_s = museumTxt(loc);
  var users = numeral(data.users).format('0,0');
  var pv = numeral(data.pageviews).format('0,0');
  var secs = numeral(data.tos).format('0,0.00');
  var pps = numeral(data.pps).format('0,0.00');

  var pages = data.toppages;

  $('#start-date').text(start_s);
  $('#end-date').text(end_s);
  $('#museum-text').text(mus_s);
  $('#museum-users').text(users);
  $('#pageviews').text(pv);
  $('#time-on-site').text(secs);
  $('#pages-per-visit').text(pps);


  $("#topPages").empty();
  for (var i = pages.length - 1; i >= 0; i--) {
    var p = pages[i];
    $("#topPages").prepend("<li><a href='//" + p.url + "' target='_blank'>" + p.title + "</a></li>" )
  }

  $('#infotext').show();


});

var s_start = moment().subtract(1, 'month').startOf('month').unix();
var s_end = moment().startOf('month').unix();
var url = "./app/ajax.php?action=social&location=" + loc + "&start=" + start + "&end=" + end;
$('#social-holder').empty();
$.getJSON(url).done(function(data)
{
  if(lastrequest != myrequest)
  {
    console.info('AJAX load canceled: Not most recent call');
    return;
  }

  $('#social-holder').html(data.html);
}).fail(function()
{
  console.error("Failure - Social: " + url);
});
}

function getSources(acts, loc)
{
  var locaccts = acts.location[loc].accounts;
  var srcs = {};
  var c = 0;

  for (var i = locaccts.length - 1; i >= 0; i--) {
    var a = locaccts[i];
    if(a.type in srcs)
    {
    }
    else
    {
      srcs[a.type] = c;
      c++;
    }
  };

  srcs['total-length'] = c;

  return srcs;
}

function museumTxt(loc)
{
  switch(loc)
  {
    case 'cmp':
    return 'Carnegie Museums of Pittsburgh';
    break;
    case 'cmoa':
    return 'Carnegie Museum of Art';
    break;
    case 'warhol':
    return 'Andy Warhol Museum';
    break;
    case 'csc':
    return 'Carnegie Science Center';
    break;
    case 'cmnh':
    return 'Carnegie Museum of Natural History';
    break;
  }
}

function maxScore(events)
{
  var max = -999999;
  if(events)
  {
    for (var i = events.length - 1; i >= 0; i--) {
      var e = events[i];

      if(e.score > max) max = e.score;
    };
  }
  return max;
}

function setupTooltip()
{
  var tt = d3.select('.highcharts-tooltip');
  tt.style('display','none');

  var markers = d3.selectAll('.highcharts-markers').selectAll('path');
  markers.style('cursor','hand');
  markers.on('click', function()
  {
    tt.style('display',null);
  })
  .on('mouseleave', function()
  {
    tt.style('display','none');
  })
}

function getLastYear()
{
  var start = moment().subtract(1,'years').startOf('year');
  var end = moment().subtract(1,'years').endOf('year');

  var time = {};
  time['start'] = start.unix();
  time['end'] = end.unix();

  return time;

}
function getLast3Months()
{
  var start = moment().subtract(4,'months').startOf('month');
  var end = moment().subtract(1,'months').endOf('month');

  var time = {};
  time['start'] = start.unix();
  time['end'] = end.unix();

  return time;

}

function getLastMonth()
{
  var start = new Date();
  start.setDate(1);
  start.setMonth(start.getMonth() - 1);
  start.setHours(0);
  start.setMinutes(0);
  start.setSeconds(0);
  start.setMilliseconds(0);

  var end = new Date();
  end.setDate(0);
  end.setHours(0);
  end.setMinutes(0);
  end.setSeconds(0);
  end.setMilliseconds(0);

  var time = {};
  time['start'] = start.getTime() / 1000;
  time['end'] = end.getTime() / 1000;

  return time;
}
function getLastWeek()
{

  var start = moment().subtract(7,'days');
  var end = moment().subtract(1,'days');

  end.hours(0);
  end.minutes(0);
  end.seconds(0);
  end.milliseconds(0);

  start.hours(0);
  start.minutes(0);
  start.seconds(0);
  start.milliseconds(0);


  var time = {};
  time['start'] = start.unix();
  time['end'] = end.unix();

  return time;

}

function setupLegend()
{
  //Legend toggle
  $(".legend-toggle").unbind("click");
  loadToggles();

  $(".legend-toggle").click(function() {


    var series = $(this).attr("data-series");

    if($(this).hasClass("active"))
    {
      //console.log(series + " off");
      $(this).removeClass("active");
      toggleSeries(series, false);
    }
    else
    {
      //console.log(series + " on");
      $(this).addClass("active");
      toggleSeries(series, true);
    }
  });
}
function loadToggles()
{
  $(".legend-toggle").each(function(i)
  {
    var series = $(this).attr("data-series");

    if($(this).hasClass("active"))
    {
      //console.log(series + " on");
      toggleSeries(series, true);
    }
    else
    {
      //console.log(series + " off");
      toggleSeries(series, false);
    }
  });

  /*
  //Default ON
  toggleSeries("views", true);
  toggleSeries("users", true);
  toggleSeries("admissions", true);
  $(".legend-views").addClass("active");
  $(".legend-users").addClass("active");
  $(".legend-admissions").addClass("active");

  //Default OFF
  toggleSeries('anomolies', false);
  toggleSeries('twitter', false);
  toggleSeries('facebook', false);
  toggleSeries('instagram', false);
  $(".legend-anomolies").removeClass("active");
  $(".legend-twitter").removeClass("active");
  $(".legend-facebook").removeClass("active");
  $(".legend-instagram").removeClass("active");
  */
}

function toggleSeries(name, show)
{

  switch(name)
  {
    case "views":
      toggleHighchart(0, show);
      break;
    case "users":
      toggleHighchart(1, show);
      break;
    case "admissions":
      toggleHighchart(2, show);
      break;
    case "anomolies":
      toggleSocial("Google-Analytics", show);
      break;
    case "twitter":
      toggleSocial("Twitter", show);
      break;
    case "facebook":
      toggleSocial("Facebook", show);
      break;
    case "instagram":
      toggleSocial("Instagram", show);
      break;
  }
}
function toggleHighchart(series, show)
{
  var chart = $("#chart").highcharts();
  var series = chart.series[series];
  if(show)
  {
    series.show();
  }
  else {
    series.hide();
  }
}
function toggleSocial(name, show)
{

  var sClass = '.' + name;

  if(show)
  {
    $(sClass).attr("display",null);
  }
  else
  {

    $(sClass).attr("display","none");
  }
}

function toggleChartSize()
{
  //
  $(".legend-social .legend-toggle").click(getChartSize);
}

function getChartSize()
{
  //See if any of the social toggles are active
  var largeScreen = true;
  if(screen.width <= 768)
  {
    largeScreen = false;
  }
  var small = false;
  $(".legend-social .legend-toggle").each(function(i)
  {
    if($(this).hasClass("active"))
    {
      small = true;
    }
  });


  var smallsize = $("#chart").attr("small-size");

  var wasSmall = false;

  if (typeof smallsize !== typeof undefined && smallsize !== false)
  {
    wasSmall = true;
  }
  if(small && largeScreen)
  {
    $("#chart").css("width","75%");
    $("#chart").attr("small-size", true);
    $("#socialmedia").show();

  }
  else
  {
    $("#chart").css("width","");
    $("#chart").removeAttr("small-size");
    $("#socialmedia").hide();
  }
  //console.log("current: " + wasSmall + " next: " + small);
  if(small != wasSmall) //If the sizes are not equal
  {
    //console.log("Resize: " + (small != wasSmall));
    $("#socialmedia").text("Roll over the bars to see more details");
    chartResize();
  }

}

function chartResize()
{
  var w = $("#chart").width();
  var h = $("#chart").height();
  try {
    $("#chart").highcharts().setSize(w, h, true);
    $("#socialmedia").css("height", h);
    setTimeout(function()
  {
    events(event_data, event_srcs);
  }, 450);


  } catch (e) {

  } finally {

  }

}

function setupChartResize()
{
  $(window).resize(function()
  {
    chartResize();
  })
}

function insertSocial(html)
{
  $("#socialmedia").html(html);

  var w = $("#socialmedia").width();
  var h = $("#socialmedia").height();
  $("#socialmedia iframe").css({
    "width":w,
    "height":h
  });
  $("#socialmedia iframe").attr("width", w);
  $("#socialmedia iframe").attr("height", h);
}



function events(data, srcs)
{

  var svg = d3.select(".highcharts-container svg");
  //var eSvg = d3.select('#events-svg');

  svg.select("#events").remove();

  var eSvg = svg.insert("g",".highcharts-tooltip").attr("id","events");

  var events = data.events;
  var start = data.start;
  var end = data.end;

 if(!events) return;

  var sbox = svg.select('.highcharts-series-group').node().getBBox();

  var h = sbox.y + sbox.height;
  //$('.highcharts-series-group').position().left;

  var t = 0;


  var axis = d3.select(".highcharts-series-0.highcharts-tracker").node();
  var box = axis.getBBox();

  var l = $(".highcharts-series-0.highcharts-tracker").position().left;

  var p = $(".highcharts-markers.highcharts-tracker").position();

  var w = box.width;
  if(w <= 0)
  {
    axis = d3.select(".highcharts-series-2.highcharts-tracker").node();
    box = axis.getBBox();
    w = box.width;

    l = $(".highcharts-series-2.highcharts-tracker").position().left;
  }



  var max = maxScore(events);

  //console.log('Event width: ' + w);



  var xS = d3.scale.linear().domain([start, end - 1]).range([l,l+w]);
  var yS = d3.scale.linear().domain([0, 1]).range([t,h+t]);


  //eSvg.append("line").attr('x1',xS(start)).attr('x2',xS(start)).attr('y1',yS(0)).attr('y2',yS(srcs['total-length'] - 1)).attr('stroke','black').attr('stroke-width',1);
  //eSvg.append("line").attr('x1',xS(end)).attr('x2',xS(end)).attr('y1',yS(0)).attr('y2',yS(srcs['total-length'] - 1)).attr('stroke','black').attr('stroke-width',1);

  var circles =  eSvg.selectAll("circle")
  .data(events)
  .enter()
  .append("line")
  .attr("x1", function(d) {
    //.log(getDate(d.timestamp));
    return xS(d.timestamp);
  })
  .attr("x2", function(d) {
    //.log(getDate(d.timestamp));
    return xS(d.timestamp);
  })
  .attr("y1", function(d)
  {

      return yS(0);
  }).attr("y2", function(d)
  {
      return yS(1);
  })
  .attr("stroke-width", "1")
  .attr("class", function(d)
  {
    return d.source.replace(" ","-");
  })
  .style("stroke",function(d)
  {
    switch(d.source)
    {
      case 'Twitter':
      return "rgba(80,171,241,1)"; //Twitter blue
      case 'Google Analytics':
      return "rgba(247,153,28, 1)"; //GA orange
      case 'Facebook':
      return "rgba(68,97,157, 1)"; //FB blue
      case 'Instagram':
      return "rgb(158, 129, 97)"  //IG brown
      default:
      return "rgba(255,255,255,1)";
    }

  })
  .on('click',function(d)
  {
  })
  .on('mouseenter', function(d){
    insertSocial(d.html)

  })
  .on('mouseleave', function(d)
  {
  });

  $('.Google-Analytics').attr('display','none');
  $('.Instagram').attr('display','none');
  $('.Facebook').attr('display','none');
  $('.Twitter').attr('display','none');

  loadToggles();

}




function getDate(d) {
  return new Date(d);
}




function getActs()
{
  var json = <?php print file_get_contents('./app/config/accounts.json'); ?>;

  return json;
}





});
</script>
