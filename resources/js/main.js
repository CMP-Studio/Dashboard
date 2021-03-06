$(document).ready(function()
{
  //varialbes
  var smallHeight = 640; //A small height in px (changes when sticky nav is active)

  //main
  //Mobile menu
  $("#museum-bar .museum, .active-museum").click(function()
  {
    if($("#museum-bar-toggle").attr("aria-expanded") == "true")
    {
      $("#museum-bar").collapse('hide');
    }
    else
    {
      $("#museum-bar").collapse('show');
    }
  });

  //Select timespan
  $("#timespan").select2({
    width: '100%',
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



stickyNav(smallHeight);





  function stickyNav(maxHeight)
  {
    //Determine when stickynave should happen
    var enabled = false;

    if(screen.height <= maxHeight) enabled = true;
    $(window).resize(function() {
        if(screen.height <= maxHeight)
        {
          reposHeader();
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
      reposHeader();
    });
  }
  function reposHeader(min)
  {
    var mainH = $(".main-body").offset().top - $("body").scrollTop();
    var headH = $("header").height();
    var relTop =  mainH - headH;
    var minOffset = -1*$("#primary-nav").outerHeight(false);

    if(relTop < minOffset)
    {
      relTop = minOffset;
    }
    else if (relTop >= 0) //Don't change anything if relTop would prevent the header being below the top
    {
        $("header").css({top : ""});

        return;
    }
    $("header").css({top : relTop + "px"});
  }




});
