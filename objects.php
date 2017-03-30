<?php
require('Tweet.php');

// Tweetクラスからインスタンス化されたTweetオブジェクトを生成
$Tweet = new Tweet();
$tweets = $Tweet->findAll();
// echo '<pre>';
// var_dump($tweets);
// echo '</pre>';
// for ($i=0; $i < count($tweets); $i++) {
//     echo $tweets[$i]['tweet'] . '<br>';
// }

$tweet = $Tweet->findById(10);
echo $tweet['tweet'];
?>














