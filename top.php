<?php
// $_SESSIONに保存されたログインユーザーのIDを使ってDBから
// ログインユーザーの情報を取得し、名前と画像を画面に出力する
session_start();
require('dbconnect.php');

// デバッグ用
echo '<br>';
echo '<br>';

// echo $_SESSION['time'] . '<br>';
// echo time() . '<br>';
// echo time() - $_SESSION['time'];
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
    $sql  = 'SELECT * FROM `members` WHERE `member_id`=?';
    $data = array($_SESSION['login_member_id']);

    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $login_member = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // ログインしていない
    header('Location: login.php');
    exit();
}

// ツイートボタンが押された際
if (!empty($_POST['tweet'])) {
    if ($_POST['tweet'] != '') {
        // DBへの登録処理
        $sql = 'INSERT INTO `tweets` SET `tweet`=?,
                                         `member_id`=?,
                                         `reply_tweet_id`=?,
                                         `created`=NOW()';
        $data = array($_POST['tweet'], $_SESSION['login_member_id'], $_POST['reply_tweet_id']);
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

        header('Location: top.php');
        exit();
    }
}





// すべてのスーパーグローバル変数は、連想配列です。
// 連想配列の一要素を取得するには？
// $hoge = array('hoge', 'fuga');
// echo $hoge[1];
// $fuga = array('id' => 'hoge', 'name' => 'fuga');
// echo $fuga['id'];

// 返信の場合（パラメータが存在するとき）
$re_str = '';
if (isset($_REQUEST['tweet_id'])) {
    // /top.php?tweet_id=3
    // $_REQUEST = array('tweet_id' => '3');
    // $_REQUESTは$_GET, $_POST, $_COOKIEの値をすべて持つ変数

    // Reが押されたツイートデータをDBから取得
    $sql = 'SELECT * FROM `tweets` LEFT JOIN `members` ON tweets.member_id=members.member_id WHERE `tweet_id`=?';
    // ？に入れるデータは配列で用意
    $data = array($_REQUEST['tweet_id']);
    $re_stmt = $dbh->prepare($sql);
    $re_stmt->execute($data);

    // $stmtはobject型なのでarray型に変換（fetch）
    $re_tweet = $re_stmt->fetch(PDO::FETCH_ASSOC);
    // テキストエリアに表示する文字列を作成
    $re_str = '@' . $re_tweet['tweet'] . ' (' . $re_tweet['nick_name'] . ') -> ';
}

// ページング機能
$page = '';
// パラメータのページ番号を取得
if (isset($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
}

// パラメータが存在しない場合はページ番号を1とする
if ($page == '') {
    $page = 1;
}

// 1以下のイレギュラーな数値が入ってきた場合はページ番号を1とする
$page = max($page, 1);
// echo max(1,10,-100,600,600.001) . '<br>';

// データの件数から最大ページ数を計算する
$sql = 'SELECT COUNT(*) AS `cnt` FROM `tweets`';
$stmt = $dbh->prepare($sql);
$stmt->execute();
$record = $stmt->fetch(PDO::FETCH_ASSOC);
$max_page = ceil($record['cnt'] / 5); // 小数点以下切り上げ

// パラメータのページ番号が最大ページ数を超えていれば、最後のページ数とする
$page = min($page, $max_page);

// 1ページに表示する件数分だけデータを取得する
$page = ceil($page);
echo '現在のページ数 : ' . $page;

$start = ($page - 1) * 5;


// ツイートデータ全件取得
// LEFT JOINを使用して複数テーブルからデータをまとめて取得
// SELECT * FROM `基準テーブル` LEFT JOIN `連結テーブル` ON 基準テーブルの外部キー=連結テーブルの主キー
// SELECT * FROM `tweets` LEFT JOIN `members` ON tweets.member_id=members.member_id
// $sql = 'SELECT * FROM `tweets` ORDER BY created DESC LIMIT 0, 3';

$search_word = '';
if (isset($_GET['search_word']) && !empty($_GET['search_word'])) {
    // 検索の場合の処理
    $search_word = $_GET['search_word'];
    $sql = sprintf('SELECT t.*, m.nick_name, m.picture_path FROM `tweets` AS t LEFT JOIN `members` AS m ON t.member_id=m.member_id WHERE t.tweet LIKE "%%%s%%" ORDER BY t.created DESC LIMIT %d, 5', $_GET['search_word'], $start);
} else {
    // 通常の処理
    $sql = sprintf('SELECT t.*, m.nick_name, m.picture_path FROM `tweets` AS t LEFT JOIN `members` AS m ON t.member_id=m.member_id ORDER BY t.created DESC LIMIT %d, 5', $start);
}


// $sql = 'SELECT t.*, m.nick_name, m.picture_path FROM `tweets` t, `members` m WHERE t.member_id=m.member_id';

// $data = array($start);
$stmt = $dbh->prepare($sql);
$stmt->execute();


// いいね！機能のロジック実装
if (!empty($_POST)) {
    if ($_POST['like'] == 'like') {
        // いいね！されたときの処理
        $sql = 'INSERT INTO `likes` SET `member_id`=?, `tweet_id`=?';
        $data = array($_SESSION['login_member_id'], $_POST['like_tweet_id']);
        $like_stmt = $dbh->prepare($sql);
        $like_stmt->execute($data);
        header('Location: top.php');
        exit();
    } else {
        // いいね！取り消しされたときの処理
        $sql = 'DELETE FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
        $data = array($_SESSION['login_member_id'], $_POST['like_tweet_id']);
        $like_stmt = $dbh->prepare($sql);
        $like_stmt->execute($data);
        header('Location: top.php');
        exit();
    }
}
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
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo $login_member['nick_name']; ?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo $re_str; ?></textarea>
                <input type="hidden" name="reply_tweet_id" value="<?php echo $_REQUEST['tweet_id']; ?>">
              </div>
            </div>
          <ul class="paging">
            <?php 
                $word = '';
                if (isset($_GET['search_word'])) {
                    $word = '&search_word=' . $_GET['search_word'];
               }
             ?>
            <input type="submit" class="btn btn-info" value="つぶやく">
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php if($page > 1): ?>
                <li><a href="top.php?page=<?php echo $page - 1; ?><?php echo $word; ?>" class="btn btn-default">前</a></li>
            <?php else: ?>
              <li>
                前
              </li>
            <?php endif; ?>

            &nbsp;&nbsp;|&nbsp;&nbsp;
            <?php if($page < $max_page): ?>
                <li><a href="top.php?page=<?php echo $page + 1; ?><?php echo $word; ?>" class="btn btn-default">次</a></li>
            <?php else: ?>
              <li>
                次
              </li>
            <?php endif; ?>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
        <!-- 検索窓 -->
        <form method="GET" action="" class="form-horizontal" role="form">
          <input type="text" name="search_word" value="<?php echo $search_word; ?>">
          <input type="submit" value="検索" class="btn btn-success btn-xs">
        </form>
        <?php while($tweet = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <?php
                // いいね！済みかどうかの判定処理
                $sql = 'SELECT * FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
                $data = array($_SESSION['login_member_id'], $tweet['tweet_id']);
                $is_like_stmt = $dbh->prepare($sql);
                $is_like_stmt->execute($data);

                // いいね！数カウント処理
                $sql = 'SELECT COUNT(*) AS total FROM `likes` WHERE `tweet_id`=?';
                $data = array($tweet['tweet_id']);
                $count_stmt = $dbh->prepare($sql);
                $count_stmt->execute($data);
                $count = $count_stmt->fetch(PDO::FETCH_ASSOC);
            ?>
          <div class="msg">
            <img src="member_picture/<?php echo $tweet['picture_path']; ?>" width="48" height="48">
            <p>
              <?php echo $tweet['tweet']; ?><span class="name"> (<?php echo $tweet['nick_name']; ?>) </span>
              [<a href="top.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">Re</a>]
            </p>
            <p class="day">
              <a href="view.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">
                <?php echo $tweet['created']; ?>
              </a>
              <?php if($tweet['reply_tweet_id'] > 0): ?>
                <a href="view.php?tweet_id=<?php echo $tweet['reply_tweet_id']; ?>">返信元のつぶやき</a>
              <?php endif; ?>
              <?php if($_SESSION['login_member_id'] == $tweet['member_id']): ?>
                [<a href="edit.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #00994C;">編集</a>]
                [<a href="delete.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #F33;">削除</a>]
              <?php endif; ?>
              <form method="POST" action="">
                いいね！数 : <?php echo $count['total']; ?> 
                <?php if($is_like = $is_like_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                  <!-- いいね！データが存在する（削除ボタン表示） -->
                  <input type="hidden" name="like" value="unlike">
                  <input type="hidden" name="like_tweet_id" value="<?php echo $tweet['tweet_id']; ?>">
                  <input type="submit" value="いいね！取り消し" class="btn btn-danger btn-xs">
                <?php else: ?>
                  <!-- いいね！データが存在しない（いいねボタン表示） -->
                  <input type="hidden" name="like" value="like">
                  <input type="hidden" name="like_tweet_id" value="<?php echo $tweet['tweet_id']; ?>">
                  <input type="submit" value="いいね！" class="btn btn-primary btn-xs">
                <?php endif; ?>
              </form>
            </p>
          </div>
        <?php endwhile; ?>
      </div>

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>








