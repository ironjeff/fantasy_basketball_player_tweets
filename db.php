<?php

include("./include/dbinfo.inc.php");
include("./include/player.php");

$dbcnx = mysql_connect("localhost",$username,$password);
@mysql_select_db($database);

// DISPLAY Recipes
$query="SELECT * FROM players";
$result=mysql_query($query);

$row_count=mysql_numrows($result);
echo 'num rows'.$num;

$players = array();
for ($i = 0; $i < $row_count; $i += 1) {
  $player = new Player($i, $result);
  echo $player->toString().'<br>';
  $players[] = $player;
}
?>