<?php
session_start();
require('_Tweet.php');

$Tweet = new Tweet();
$tweets = array();
$tweets = $Tweet->findAll();
// var_dump($tweets);
foreach ($tweets as $tweet) {
    echo $tweet['tweet'] . '<br>';
}

$tweet = $Tweet->findById(2);
// var_dump($tweet);
echo $tweet['tweet'];

if (!empty($_POST['tweet'])) {
    if ($_POST['tweet'] != '') {
        $Tweet->insert($_POST['tweet'], $_SESSION['login_member_id'], $_POST['reply_tweet_id']);
    }
}

if($Tweet->isLogin()){
  echo 'hogehoge';
} else {
  echo 'fugafuga';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title></title>
</head>
<body>
  <form method="post" action="">
    <label>つぶやき</label>
    <textarea name="tweet" cols="50" rows="5" placeholder="例：Hello World!"></textarea>
    <input type="hidden" name="reply_tweet_id" value="0">
    <br>
    <input type="submit" value="つぶやく">
  </form>
</body>
</html>