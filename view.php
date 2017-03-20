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
$sql = 'SELECT t.*, m.nick_name, m.picture_path 
								FROM `tweets` AS t LEFT JOIN `members` AS m 
				 				ON t.member_id=m.member_id
				 				WHERE `tweet_id`=?';
$data = array($_REQUEST['tweet_id']);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$tweet = $stmt->fetch(PDO::FETCH_ASSOC);


// いいね！機能のロジック実装
if (!empty($_POST)) {
		if ($_POST['like'] == 'like') {
				// いいね！されたときの処理
				$sql = 'INSERT INTO `likes` SET `member_id`=?, `tweet_id`=?';
				$data = array($_SESSION['login_member_id'], $_REQUEST['tweet_id']);
				$like_stmt = $dbh->prepare($sql);
				$like_stmt->execute($data);
		} else {
				// いいね！取り消しされたときの処理
				$sql = 'DELETE FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
				$data = array($_SESSION['login_member_id'], $_REQUEST['tweet_id']);
				$like_stmt = $dbh->prepare($sql);
				$like_stmt->execute($data);
		}
}

// いいね！済みかどうかの判定処理
$sql = 'SELECT * FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
$data = array($_SESSION['login_member_id'], $_REQUEST['tweet_id']);
$is_like_stmt = $dbh->prepare($sql);
$is_like_stmt->execute($data);

// いいね！数カウント処理
$sql = 'SELECT COUNT(*) AS total FROM `likes` WHERE `tweet_id`=?';
$data = array($_REQUEST['tweet_id']);
$count_stmt = $dbh->prepare($sql);
$count_stmt->execute($data);
$count = $count_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.html">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <div class="msg">
          <img src="member_picture/<?php echo $tweet['picture_path']; ?>" width="100" height="100">
          <p>投稿者 : <span class="name"> <?php echo $tweet['nick_name']; ?> </span></p>
          <p>
            つぶやき : <br>
            <?php echo $tweet['tweet']; ?>
          </p>
          <p class="day">
            <?php echo $tweet['created']; ?>
            <?php if($_SESSION['login_member_id'] == $tweet['member_id']): ?>
            	[<a href="delete.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #F33;">削除</a>]
          	<?php endif; ?>
          </p>
          <form method="POST" action="">
          	いいね！数 : <?php echo $count['total']; ?> 
          	<?php if($is_like = $is_like_stmt->fetch(PDO::FETCH_ASSOC)): ?>
          		<!-- いいね！データが存在する（削除ボタン表示） -->
          		<input type="hidden" name="like" value="unlike">
          		<input type="submit" value="いいね！取り消し" class="btn btn-danger btn-xs">
          	<?php else: ?>
          		<!-- いいね！データが存在しない（いいねボタン表示） -->
	          	<input type="hidden" name="like" value="like">
	          	<input type="submit" value="いいね！" class="btn btn-primary btn-xs">
          	<?php endif; ?>
          </form>
        </div>
        <a href="index.html">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>




