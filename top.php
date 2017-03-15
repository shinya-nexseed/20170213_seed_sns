<?php
// $_SESSIONに保存されたログインユーザーのIDを使ってDBから
// ログインユーザーの情報を取得し、名前と画像を画面に出力する

session_start();
require('dbconnect.php');

echo $_SESSION['time'] . '<br>';
echo time() . '<br>';
echo time() - $_SESSION['time'];
// 60 * 60 = 3600 (１時間)
// 60 * 60 * 24 = 86400 (１日)

// Unixタイムスタンプとして取得します。Unixタイムスタンプとは1970年1月1日 00:00:00 GMTからの経過秒数です。PHP内部での日付や時刻の処理はUnixタイムスタンプで行われています。

// ログイン判定プログラム
// ①$_SESSION['login_member_id']が存在している
// ②最後のアクション（ページの読み込み）から1時間以内である
// セッションに保存した時間に１時間足した時間が今の時間より大きいと、１時間以上経過としてログインページへとばす
if (isset($_SESSION['login_member_id']) && $_SESSION['time'] + 3600 > time()) {

		$_SESSION['time'] = time();

		// ログインしている
		$sql = 'SELECT * FROM `members` WHERE `member_id`=?';
		$data = array($_SESSION['login_member_id']);

		$stmt = $dbh->prepare($sql);
		$stmt->execute($data);
		$login_member = $stmt->fetch(PDO::FETCH_ASSOC);
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



// ツイートデータ全件取得
// LEFT JOINを使用して複数テーブルからデータをまとめて取得
// SELECT * FROM `基準テーブル` LEFT JOIN `連結テーブル` ON 基準テーブルの外部キー=連結テーブルの主キー
// SELECT * FROM `tweets` LEFT JOIN `members` ON tweets.member_id=members.member_id
$sql = 'SELECT t.*, m.nick_name, m.picture_path FROM `tweets` AS t LEFT JOIN `members` AS m ON t.member_id=m.member_id ORDER BY t.created DESC';

// $sql = 'SELECT t.*, m.nick_name, m.picture_path FROM `tweets` t, `members` m WHERE t.member_id=m.member_id';

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
			<?php echo $tweet['tweet_id']; ?> : <?php echo $tweet['tweet']; ?> (<img width="20" src="member_picture/<?php echo $tweet['picture_path']; ?>"><?php echo $tweet['nick_name']; ?>)[<a href="view.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">詳細</a>]<br>
		<?php endwhile; ?>
	</div>
</body>
</html>










