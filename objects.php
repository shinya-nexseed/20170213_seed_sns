<?php
session_start();
require('Tweet.php');

// Tweetクラスからインスタンス化されたTweetオブジェクトを生成
$Tweet = new Tweet();
$tweets = $Tweet->findAll();
// echo '<pre>';
// var_dump($tweets);
// echo '</pre>';


// $tweet = $Tweet->findById(10);
// echo $tweet['tweet'];

// $tweets = $Tweet->findByMemberId(1);
// foreach ($tweets as $tweet) {
//     echo $tweet['tweet'] . '<br>';
// }

if (!empty($_POST['tweet'])) {
    $Tweet->insert($_POST['tweet'], $_SESSION['login_member_id'], 0);
}

$Tweet->delete(13);

for ($i=0; $i < count($tweets); $i++) {
    echo $tweets[$i]['tweet'] . '<br>';
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title></title>
</head>
<body>
  <form action="" method="POST">
    <textarea name="tweet"></textarea>
    <br>
    <input type="submit" value="つぶやく">
  </form>
</body>
</html>














