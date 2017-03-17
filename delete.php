<?php
session_start();
require('dbconnect.php');

// ログインチェック
if (isset($_SESSION['login_member_id'])) {
	echo $_REQUEST['tweet_id'];

	// 指定されたIDのツイートデータが、ログインユーザー本人のものかチェック
	$sql = 'SELECT * FROM `tweets` WHERE `tweet_id`=?';
	$data = array($_REQUEST['tweet_id']);
	$stmt = $dbh->prepare($sql);
	$stmt->execute($data);
	$record = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($record['member_id'] == $_SESSION['login_member_id']) {
		// 削除処理
		$sql = 'DELETE FROM `tweets` WHERE `tweet_id`=?';
		$data = array($_REQUEST['tweet_id']);
		$stmt = $dbh->prepare($sql);
		$stmt->execute($data);
	}
}

header('Location: top.php');
exit();
?>













