<?php
// $_SESSIONに保存されたログインユーザーのIDを使ってDBから
// ログインユーザーの情報を取得し、名前と画像を画面に出力する

session_start();
require('dbconnect.php');

echo $_SESSION['login_member_id'] . '<br>';

$sql = 'SELECT * FROM `members` WHERE `member_id`=?';
$data = array($_SESSION['login_member_id']);

$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$login_member = $stmt->fetch(PDO::FETCH_ASSOC);
echo $login_member['nick_name'];
?>
<img src="member_picture/<?php echo $login_member['picture_path']; ?>" width="100">