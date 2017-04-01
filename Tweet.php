<?php


class Tweet {
    private $dbh = '';

    function __construct() {
        require('dbconnect.php');
        $this->dbh = $dbh;
    }

    // １テーブルに対するCRUD処理をまとめる

    // tweetsデータ全件取得メソッド
    public function findAll() {
        $sql = 'SELECT * FROM `tweets`';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $tweets = array();
        while ($tweet = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tweets[] = $tweet;
        }
        return $tweets;
    }

    // tweetsデータ一件取得メソッド（id指定）
    public function findById($id) {
        $sql = 'SELECT * FROM `tweets` WHERE `tweet_id`=?';
        $data = array($id);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($data);
        $tweet = $stmt->fetch(PDO::FETCH_ASSOC);
        return $tweet;
    }

    // 課題
    // ①自分が投稿したツイートのみ取得するメソッドfindByMemberId()作成・実行
    // ②ツイートデータを投稿する機能を持ったメソッドinsert()を作成・実行
    // ③指定したidのツイートデータを削除するメソッドdelete()を作成・実行
    // ④membersテーブル用のクラスを作成し、下記メソッド作成（応用課題）
      // findAll()
      // findById()
      // getLoginUser()
      // isLogin() ← ログインしてるかどうかの判定
        // ログインしていればユーザーの情報を、していなければfalseを返す

    public function findByMemberId($id) {
        $sql = 'SELECT * FROM `tweets` WHERE `member_id`=?';
        $data = array($id);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($data);
        $tweets = array();
        while ($tweet = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tweets[] = $tweet;
        }
        return $tweets;
    }

    public function insert($tweet, $member_id, $reply_id) {
        $sql = 'INSERT INTO `tweets` SET `tweet`=?, `member_id`=?, `reply_tweet_id`=?';
        $data = array($tweet, $member_id, $reply_id);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($data);

        header('Location: objects.php');
        exit();
        // header問題
        // header関数を使用するより前に出力をするとエラーになる
        // Cannot modify header informationエラー
        // 回避策l : 出力を切る
        // 回避策2 : JSのコードで遷移する
        echo '<script>location.href = "objects.php";</script>';
    }

    public function delete($id) {
        $sql = 'DELETE FROM `tweets` WHERE `tweet_id`=?';
        $data = array($id);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($data);
        
        // header('Location: objects.php');
        // exit();
    }


}
?>








