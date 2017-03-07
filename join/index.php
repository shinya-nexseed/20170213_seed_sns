<?php

// 各入力値を保持する変数を用意
	$nick_name = '';
$email     = '';
$password  = '';

// エラー格納用の配列を用意
$errors = array();

// 確認画面へボタンが押されたとき
if (!empty($_POST)) {
		$nick_name = $_POST['nick_name'];
		$email 		 = $_POST['email'];
		$password  = $_POST['password'];

		// ページ内バリデーション
		if ($nick_name == '') {
				// ニックネームのフォームが空のため、画面にエラーを出力
				$errors['nick_name'] = 'blank'; // blank部分はどんな文字列でも良い
		}
		if ($email == '') {
				$errors['email'] = 'blank';
		}
		if ($password == '') {
				$errors['password'] = 'blank';
		} elseif (strlen($password) < 4) {
				$errors['password'] = 'length';
		}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h1>会員登録</h1>
	<form method="POST" action="">
		<div>
			<label>ニックネーム</label><br>
			<input type="text" name="nick_name" value="<?php echo $nick_name; ?>">
			<?php if(isset($errors['nick_name']) && $errors['nick_name'] == 'blank'): ?> <!-- コロン構文 -->
				<p style="color: red; font-size: 10px; margin-top: 2px;">
					ニックネームを入力してください
				</p>
			<?php endif; ?>
		</div>
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
		<div>
			<label>プロフィール画像</label><br>
			<input type="file" name="picture_path">
		</div>
		<input type="submit" value="確認画面へ">
	</form>
</body>
</html>
















