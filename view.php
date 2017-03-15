<?php
session_start();
require('dbconnect.php');

// 閲覧条件
if (!isset($_REQUEST['tweet_id'])) {
		header('Location: top.php');
		exit();
}

// view.php?tweet_id=7
// ?より後のデータをパラメータ
// パラメータを取得するのが$_REQUEST
// $_REQUEST = array('tweet_id'=>'7');

var_dump($_REQUEST);
echo '<br>';
echo $_REQUEST['tweet_id'];
// 投稿一件取得
$sql = 'SELECT * FROM `tweets` WHERE `tweet_id`=?';
$data = array($_REQUEST['tweet_id']);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$tweet = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title></title>
</head>
<body>
	<h2>ツイートID:<?php echo $_REQUEST['tweet_id']; ?>  の詳細</h2>
	ツイートデータ：<?php echo $tweet['tweet']; ?><br>

</body>
</html>

