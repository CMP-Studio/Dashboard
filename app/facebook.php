<?php

require_once "utils/api.php";
require_once "config/fbConfig.php";

 function getFBToken()
{
  $url = "https://graph.facebook.com/oauth/access_token";
  $params = array(
    "client_id" => getFBClientID(),
    "client_secret" => getFBClientSecret(),
    "grant_type" => "client_credentials"
  );

  $token = getAPI($url,$params);
  $token = explode('=', $token);

  return $token;
}
function getFBEmbeedScript()
{
    $clientID = getFBClientID();

  $fbScript = "
  <div id=\"fb-root\"></div>
  <script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=$clientID&version=v2.3\";
  fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));


    try
    {
      FB.init({
        appID: '$clientID',
        version : 'v2.3',
        status : true,
        xfbml : true
      });
    }
    catch(e)
    {}


</script>";

//&version=v2.2

  return $fbScript;
}
function getFBlink($post)
{
  $fullID = $post->id;
  $ids = explode("_", $fullID);
  return "https://www.facebook.com/" . $ids[0] . "/posts/" . $ids[1];

}
function FBEmbeed($link)
{



  $post = "<div class='fb-post' data-width='300' data-href='$link'></div><script type=\"text/javascript\">
  try{

    var w = $(\"div[data-href='$link']\").parent().width();
    $(\"div[data-href='$link']\").attr('data-width',w);
    FB.XFBML.parse();

    $(\"div[data-href='$link']\").parent().on('resize', function(){
      var w = $(\"div[data-href='$link']\").parent().width();
      $(\"div[data-href='$link']\").attr('data-width',w);
      FB.XFBML.parse();
    });

  } catch(e) {console.warn(e.message);} </script>";

  return  $post;

}

function getTopFBPosts($account, $count=10)
{
  $start = tryGET('start');
  $end = tryGet('end');
  $token = getFBToken();

  if(!isset($token[0])) return null;  //No token

  $url = "https://graph.facebook.com/$account/posts";

      $params = array(
      $token[0] => $token[1],
      "limit" => 100,
      "fields" => "likes.limit(1).summary(true),shares,actions",
      "since" => $start,
      "until" => $end
    );

    $result = getAPI($url,$params);
    if(!isset($result->data)) return array();
    $rdata = $result->data;

    usort($rdata, 'fbSort');

     $posts = array();
    foreach ($rdata as $key => $p)
    {
      if($key < $count)
      {
        array_push($posts, $p);
      }
      else
      {
        break;
      }

    }

    return $posts;

}

 function fbSort($a, $b)
  {
    $share = 2;
    $likes = 1;

    $likesB = 0;
    $likesA = 0;

    $shareA = 0;
    $shareB = 0;

    if(isset($a->likes))
    {
      $likesA = $a->likes->summary->total_count;
    }
    if(isset($b->likes))
    {
      $likesB = $b->likes->summary->total_count;
    }
    if(isset($a->shares))
    {
      $shareA = $a->shares->count;
    }
    if(isset($b->shares))
    {
      $shareB = $b->shares->count;
    }

    $pointA = $likesA*$likes + $shareA*$share;
    $pointB = $likesB*$likes + $shareB*$share;

    $a->score = $pointA;
    $b->score = $pointB;

    if($pointA == $pointB)
    {
      return 0;
    }

    return ($pointA > $pointB) ? -1 : 1;

  }






?>
