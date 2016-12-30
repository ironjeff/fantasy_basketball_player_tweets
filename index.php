<html>
<head>
<title>trendingnbaplayers.com</title>
</head>
<body>

<?php

include_once('players.php');
include_once('util.php');

$filter = $_GET['letter'];
echo 'filter: '.$filter;

$players = getPlayers();

if ($filter) {
  foreach ($players as $index => $player) {
    if (!startsWith($player['first'], $filter)) {
      unset($players[$index]);
    }
  }
}

$tweets_to_print = array();
foreach ($players as $player) {
  list($total, $first_page) = getTotalAndPreview($player);
  $tweets_to_print[] = $first_page;
  echo '<div class="player">'.$player['first'].' '.$player['last'].' total tweets: '.$total;
  echo '</div>';
}

foreach ($tweets_to_print as $tweets) {
  printTweets($tweets);
}

function getTotalAndPreview($player) {
  $TWEETS_PER_PAGE_LIMIT = 90;

  $first_name = $player['first'];
  $last_name = $player['last'];

  $today = date('Y-m-d');
  $type = 'recent'; //'mixed'; // 'popular'; // 'recent'
  $query = 'http://search.twitter.com/search.json?q='.$first_name.'%20'.$last_name.'&result_type='.$type.'&rpp=200&since='.$today;
  //echo ('query '.$query);
  $num = 200;

  $tweets = @file_get_contents($query);

  $tweets = json_decode($tweets);

  $all_tweets = array($tweets);
  $first_page = $tweets;
  //echo ('result count '.count($tweets->results));
  $count = count($tweets->results);

  $total_count = $count;
  $next_count = $count;
  $page = 2;

  while ($next_count > $TWEETS_PER_PAGE_LIMIT) {
    $next_query = $query.'&page='.$page;
    $page = $page + 1;
    $next_tweets = @file_get_contents($next_query);
    $next_tweets = json_decode($next_tweets);
    $all_tweets[] = $next_tweets;
    $next_count = count($next_tweets->results);
    //echo ('\n page '.$page.' count: '.$next_count);
    $total_count += $next_count;

    if ($page > 50) {
      break;
    }
  }

  //echo ('total tweets '.$total_count);

  // foreach ($all_tweets as $tweets) {
  //   printTweets($tweets);
  // }

  return array($total_count, $first_page);
}

/*
{"created_at":"Mon, 20 Feb 2012 08:19:04 +0000",
"from_user":"Born_Mega",
"from_user_id":182283096,
"from_user_id_str":"182283096","from_user_name":"Mega ",
"geo":null,
"id":171509158712061954,
"id_str":"171509158712061954",
"iso_language_code":"en",
"metadata":{"result_type":"recent"},
"profile_image_url":"http://a2.twimg.com/profile_images/1610361315/image_normal.jpg","profile_image_url_https":"https://si0.twimg.com/profile_images/1610361315/image_normal.jpg","source":"&lt;a href=&quot;http://twitter.com/&quot;&gt;web&lt;/a&gt;","text":"RT @Religion_IsTrue: They WIll Do a Movie On Jeremy LIn In 20 Years Called \" The Underdog \"","to_user":null,"to_user_id":null,"to_user_id_str":null,"to_user_name":null},
*/

function printTweets($tweets) {
  $num = count($tweets->results);
  echo "<ul>";
  for($x=0;$x<$num;$x++) {
    $str = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\">\\0</a>", $tweets->results[$x]->text);
    $pattern = '/[#|@][^\s]*/';
    preg_match_all($pattern, $str, $matches);

    foreach($matches[0] as $keyword) {
      $keyword = str_replace(")","",$keyword);
      $link = str_replace("#","%23",$keyword);
      $link = str_replace("@","",$keyword);
      if(strstr($keyword,"@")) {
        $search = "<a href=\"http://twitter.com/$link\">$keyword</a>";
      } else {
        $link = urlencode($link);
        $search = "<a href=\"http://twitter.com/#search?q=$link\" class=\"grey\">$keyword</a>";
      }
      $str = str_replace($keyword, $search, $str);
    }

    echo "<li>".$tweets->results[$x]->id.' '.'at: '.$tweets->results[$x]->created_at.' '.$str."</li>\n";
  }
  echo "</ul>";
}


?>
</body>