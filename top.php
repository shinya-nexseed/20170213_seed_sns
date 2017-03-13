<?php
// $_SESSIONに保存されたログインユーザーのIDを使ってDBから
// ログインユーザーの情報を取得し、名前と画像を画面に出力する

session_start();
require('dbconnect.php');

if (isset($_SESSION['login_member_id'])) {
		// ログインしている
		
} else {
	  // ログインしていない
		header('Location: login.php');
		exit();
}
echo $_SESSION['login_member_id'] . '<br>';

// ツイートボタンが押された際
if (!empty($_POST)) {
		if ($_POST['tweet'] != '') {
				// DBへの登録処理
				$sql = 'INSERT INTO `tweets` SET `tweet`=?,
																				 `member_id`=?,
																				 `reply_tweet_id`=0,
																				 `created`=NOW()';
				$data = array($_POST['tweet'], $_SESSION['login_member_id']);
				$stmt = $dbh->prepare($sql);
				$stmt->execute($data);

				header('Location: top.php');
				exit();
		}
}

$sql = 'SELECT * FROM `members` WHERE `member_id`=?';
$data = array($_SESSION['login_member_id']);

$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$login_member = $stmt->fetch(PDO::FETCH_ASSOC);

// ツイートデータ全件取得
$sql = 'SELECT * FROM `tweets`';
$data = array();
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h2>ようこそ<?php echo $login_member['nick_name']; ?>さん</h2>
	<p>あなたのユーザーIDは<?php echo $login_member['member_id']; ?>です [<a style="color: red; text-decoration: none;" href="logout.php">ログアウト</a>]</p>
	<img src="member_picture/<?php echo $login_member['picture_path']; ?>" width="100">
	<br>
	<form method="POST" action="">
		<textarea name="tweet" placeholder="ほげほげ" cols="50" row="5"></textarea><br>
		<input type="submit" value="ツイート">
	</form>
	<!-- ツイートの一覧表示 -->
	<div>
		<?php while($tweet = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
			<!-- $tweetにツイートのデータ一件が入っている -->
			<?php echo $tweet['tweet_id']; ?> : <?php echo $tweet['tweet']; ?><br>
		<?php endwhile; ?>
	</div>
</body>
</html>










