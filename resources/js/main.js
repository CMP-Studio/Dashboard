$(document).ready(function()
{
  //varialbes
  var smallHeight = 500; //A small height in px (changes when sticky nav is active)

  //main
stickyNav(smallHeight);

  function chartResize()
  {
    var w = $("#chart").width();
    var h = $("#chart").height();
    $("#chart").highcharts().setSize(w, h, true);
  }

  function stickyNav(maxHeight)
  {
    //Determine when stickynave should happen
    var enabled = false;

    if(screen.height <= maxHeight) enabled = true;
    $(window).resize(function() {
        if(screen.height <= maxHeight)
        {
          enabled = true;
        }
        else {
          $("header").css({top : ""});
          enabled = false;
        }
    });
    //Now we get to the good stuff
    $(window).scroll(function() {
      if(!enabled) return;
      var mainH = $(".main-body").offset().top - $("body").scrollTop();
      var headH = $("header").height();
      var relTop =  mainH - headH;
      var minHeight = $('.active-museum').height();

      console.log(relTop);
      if(relTop + headH <= minHeight)
      {
        relTop = minHeight - headH;
      }
      else if (relTop >= 0) //Don't change anything if relTop would prevent the header being below the top
      {
          $("header").css({top : ""});

          return;
      }
      $("header").css({top : relTop + "px"});
    })


  }




});
