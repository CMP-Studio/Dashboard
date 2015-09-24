<script>
$(document).ready(function (){


  //Defaults
  var timespan = getLastMonth();
  var museum = 'cmp';
  var lastrequest = null;

  mobile_menu();



  loadAnalytics(museum, timespan.start, timespan.end);

  //Select timespan
  $("#timespan").select2({
    width: '40%',
    minimumResultsForSearch: 50
  });

  //Dialog
  $("#dialog").dialog({
    autoOpen: false,
    minWidth: 500,
    position: { my: "center top", at: "center center", of: '#musebar' }
  });

  //Help dialog
  $("#help-panel").dialog({
    autoOpen: false
  });
  $(".help-btn").click(function(){
    $("#help-panel").dialog("open");
  })


  //Social timespan
  $("#social-start").text(moment().subtract(1, 'month').startOf('month').format("MMMM D, YYYY"));
  $("#social-end").text(moment().startOf('month').format("MMMM D, YYYY"));

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
    /*
    if (location.href.indexOf("#") > -1) {
        location.assign(location.href.replace(/\/?#/, "/"));
    }*/

    $('#cmp').removeClass('active');
    $('#csc').removeClass('active');
    $('#cmoa').removeClass('active');
    $('#cmnh').removeClass('active');
    $('#warhol').removeClass('active');

    $('#' + loc).addClass('active');

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

      console.log(cdata);


      var Eurl = "./app/ajax.php?action=events&location=" + loc + "&end=" + end + "&start=" + start;

      if(!social) Eurl += "&longterm=1";

      $.getJSON(Eurl, function(edata)
      {
        if(lastrequest == myrequest)
        {
          $('.loader').remove();
          $('#chart').highcharts(cdata);
          setupTooltip();
          setupLegend();
          events(edata, srcs);
          console.log(edata);
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
      /*}
      else
      {
      if(lastrequest == myrequest)
      {
      $('.loader').remove();
      $('#chart').highcharts(cdata);
      setupLegend();
    }
    else
    {
    console.info('AJAX load canceled: Not most recent call');
  }
}*/

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
    $("#topPages").prepend("<li><a href='//" + p + "' target='_blank'>" + p + "</a></li>" )
  }

  $('#infotext').show();


});

var s_start = moment().subtract(1, 'month').startOf('month').unix();
var s_end = moment().startOf('month').unix();
var url = "./app/ajax.php?action=social&location=" + loc + "&start=" + s_start + "&end=" + s_end;
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
    return 'Carnegie Museum of Pittsburgh';
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

function setupLegend()
{
  var analytics = false;
  var twit = false;
  var fb = false;
  var ig = false;

  $(".highcharts-legend-item text:contains(High Traffic)").parent().click(function()
  {
    if(analytics)
    {
      $('.Google-Analytics').attr('display','none');
    }
    else
    {
      $('.Google-Analytics').attr('display',null);
    }
    analytics = !analytics;
  });

  $(".highcharts-legend-item text:contains(Instagram)").parent().click(function()
  {
    if(ig)
    {
      $('.Instagram').attr('display','none');
    }
    else
    {
      $('.Instagram').attr('display',null);
    }
    ig = !ig;
  });

  $(".highcharts-legend-item text:contains(Facebook)").parent().click(function()
  {
    if(fb)
    {
      $('.Facebook').attr('display','none');
    }
    else
    {
      $('.Facebook').attr('display',null);
    }
    fb = !fb;
  });

  $(".highcharts-legend-item text:contains(Twitter)").parent().click(function()
  {
    if(twit)
    {
      $('.Twitter').attr('display','none');
    }
    else
    {
      $('.Twitter').attr('display',null);
    }
    twit = !twit;
  });


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

  var start = moment().subtract(8,'days');
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



function events(data, srcs)
{
  var svg = d3.select(".highcharts-container svg");
  //var eSvg = d3.select('#events-svg');

  var eSvg = svg.insert("g",".highcharts-tooltip").attr("id","events");

  var events = data.events;
  var start = data.start;
  var end = data.end;

 if(!events) return;

  var sbox = svg.select('.highcharts-series-group').node().getBBox();

  var h = sbox.height;
  var l = $('.highcharts-series-group').position().left;

  var t = 0;


  var axis = d3.select(".highcharts-markers.highcharts-tracker").node();
  var box = axis.getBBox();

  var p = $(".highcharts-markers.highcharts-tracker").position();

  var w = box.width;

  var max = maxScore(events);





  var xS = d3.scale.linear().domain([start, end]).range([l,w+l]);
  var yS = d3.scale.linear().domain([0, 1]).range([t,h-t]);
console.log(yS(0));
console.log(yS(1));


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
  .attr("stroke-width", "2")
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
      return "rgba(185,163,140, 1)"  //IG brown
      default:
      return "rgba(255,255,255,1)";
    }

  })
  //.style("cursor","hand")
  .on('click',function(d)
  {
    //.log(d);
    //$("#dialog").html(d.html + "<a target='_blank' href='" + d.url + "'>Permalink</a>");
    //$("#dialog").dialog("option","title",d.title);
    //$("#dialog").dialog("open");
  })
  .on('mouseenter', function(d){
  //  d3.select(this).attr("r","6");
    $("#social-holder").html(d.html + "<a target='_blank' href='" + d.url + "'>Permalink</a>")
    $("#social-holder").show();
    $("#infotext").hide();

  })
  .on('mouseleave', function(d)
  {
    $("#social-holder").hide();
    $("#infotext").show();
  //  d3.select(this).attr("r","4");
  });

  $('.Google-Analytics').attr('display','none');
  $('.Instagram').attr('display','none');
  $('.Facebook').attr('display','none');
  $('.Twitter').attr('display','none');

}




function getDate(d) {
  return new Date(d);
}


function mobile_menu()
{


  //$(".fifth.museum.active").click(menu_toggle);
  $(".fifth.museum").click(menu_toggle);

}
var shown = false;
function menu_toggle()
{
  if($(".fifth.museum").attr("display-toggle") == "show")
  {
    $(".fifth.museum").removeAttr('style');
    $(".fifth.museum").attr("display-toggle", null)
  }
  else {
    {
        $(".fifth.museum").css("display","block");
        $(".fifth.museum").attr("display-toggle", "show")
    }
  }
}

function getActs()
{
  var json = <?php print file_get_contents('./app/config/accounts.json'); ?>;

  return json;
}





});
</script>
