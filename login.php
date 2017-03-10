<?php
session_start();
require('dbconnect.php');


// ログインボタンが押されたとき
if (!empty($_POST)) {
		$email = $_POST['email'];
		$password = $_POST['password'];
		if ($email != '' && $password != '') {
				// 入力されたメールアドレスとパスワードの組み合わせがデータベースに登録されているかチェック
				$sql = 'SELECT * FROM `members` WHERE `email`=? AND `password`=?';
				$data = array($email, sha1($password));
				$stmt = $dbh->prepare($sql);
				$stmt->execute($data); // データが1件か0件か

				$record = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($record == false) {
						// そうでなければエラーメッセージ
						echo 'ログイン処理失敗';
				} else {
						// されていればログイン処理
						echo 'ログイン処理成功';
						$_SESSION['login_member_id'] = $record['member_id'];
						header('Location: top.php');
						exit();
				}
		}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h1>ログイン</h1>
	<form method="POST" action="">
	$_POST['email']
		
		<div>
			<label>メールアドレス</label><br>
			<input type="email" name="email" value="<?php echo $email; ?>">
			<?php if(isset($errors['email']) && $errors['email'] == 'blank'): ?> <!-- コロン構文 -->
				<p style="color: red; font-size: 10px; margin-top: 2px;">
					メールアドレスを入力してください
				</p>
			<?php endif; ?>
		</div>
		<div>
			<label>パスワード</label><br>
			<input type="password" name="password" value="<?php echo $password; ?>">
			<?php if(isset($errors['password']) && $errors['password'] == 'blank'): ?> <!-- コロン構文 -->
				<p style="color: red; font-size: 10px; margin-top: 2px;">
					パスワードを入力してください
				</p>
			<?php endif; ?>
			<?php if(isset($errors['password']) && $errors['password'] == 'length'): ?> <!-- コロン構文 -->
				<p style="color: red; font-size: 10px; margin-top: 2px;">
					パスワードは4文字以上で入力してください
				</p>
			<?php endif; ?>
		</div>
		
		<input type="submit" value="ログイン">
	</form>
</body>
</html>













