<?php
session_start(); // $_SESSIONの使用条件
// echo substr('abcdefg', 0) . '<br>';
// echo substr('abcdefg', 1) . '<br>';
// echo substr('abcdefg', 2) . '<br>';
// echo substr('abcdefg', 3) . '<br>';
// echo substr('abcdefg', -3) . '<br>';
// echo substr('abcdefg', -2) . '<br>';
// echo substr('abcdefg', -1) . '<br>';
// echo substr('abcdefg', 0, 1) . '<br>';
// echo substr('abcdefg', 2, 2) . '<br>';
// echo substr('abcdefg', -4, 3) . '<br>';

// 各入力値を保持する変数を用意
$nick_name = '';
$email     = '';
$password  = '';

// エラー格納用の配列を用意
$errors = array();

// 書き直し処理
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'rewrite') {
		// パラメータ = URLの最後に?から始まる部分のデータ
		// ?key1=value1&key2=value2&key3=value3...
		// 上記が基本フォーマット、keyとvalueの組み合わせで記述し、&でつなげていく

		echo '<pre>';
		var_dump($_REQUEST);
		echo '</pre>';
		echo $_REQUEST['action'];

		$_POST = $_SESSION['join'];
		$errors['rewrite'] = true; // 書き直し用にエラーを作成
}

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

		if (empty($errors)) {
				// 画像のバリデーション
				$file_name = $_FILES['picture_path']['name'];
				// name部分は固定、picture_path部分はinputタグのtype="file"のname部分
				if (!empty($file_name)) {
						// 画像が選択されていた場合
						$ext = substr($file_name, -3); // ファイル名から拡張子部分取得
						$ext = strtolower($ext); // 大文字対応
						if ($ext != 'jpg' && $ext != 'png' && $ext != 'gif') {
								$errors['picture_path'] = 'type';
						}
				} else {
						// 画像が未選択の場合
						$errors['picture_path'] = 'blank';
				}
		}
		

		// エラーがなかった場合の処理
		if (empty($errors)) {
				// 画像アップロード処理
				$picture_name = date('YmdHis') . $file_name;
					// 20170308152500shinya.jpg ←画像ファイル名作成
				move_uploaded_file($_FILES['picture_path']['tmp_name'], 
					'../member_picture/' . $picture_name);

				$_SESSION['join'] = $_POST; // joinは何でも良い
				$_SESSION['join']['picture_path'] = $picture_name;
				header('Location: check.php');
				exit();
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
	<form method="POST" action="index.php" enctype="multipart/form-data">
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
			<?php if(isset($errors['picture_path']) && $errors['picture_path'] == 'blank'): ?> <!-- コロン構文 -->
				<p style="color: red; font-size: 10px; margin-top: 2px;">
					プロフィール画像を選択してください
				</p>
			<?php endif; ?>

			<?php if(isset($errors['picture_path']) && $errors['picture_path'] == 'type'): ?> <!-- コロン構文 -->
				<p style="color: red; font-size: 10px; margin-top: 2px;">
					プロフィール画像は「.gif」「.jpg」「.png」の画像を指定してください
				</p>
			<?php endif; ?>

			<?php if(!empty($errors)): ?> <!-- コロン構文 -->
				<p style="color: red; font-size: 10px; margin-top: 2px;">
					プロフィール画像を再度指定してください
				</p>
			<?php endif; ?>
		</div>
		<input type="submit" value="確認画面へ">
	</form>
</body>
</html>
















