<?php

class Player {

  private
    $id,
    $first_name,
    $last_name,
    $num_tweets,
    $last_update;

  public function __construct($index, $rows) {
    $this->id = mysql_result($rows, $index, "id");
    $this->first_name = mysql_result($rows, $index, "first");
    $this->last_name = mysql_result($rows, $index, "last");
    $this->num_tweets = mysql_result($rows, $index, "tweetcount");
    $this->last_update = mysql_result($rows, $index, "lastupdate");
  }

  public function toString() {
    return printf('%d %s %s %d %d',
      $this->id, $this->first_name, $this->last_name, $this->num_tweets, $this->last_update);
  }
}
?>