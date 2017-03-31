<?php
class Tweet {

    private $dbh = '';

    function __construct() {
        require('dbconnect.php');
        $this->dbh = $dbh;
    }

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

    public function findById($id) {
        $sql = 'SELECT * FROM `tweets` WHERE `tweet_id`=?';
        $data = array($id);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($data);
        $tweet = $stmt->fetch(PDO::FETCH_ASSOC);
        return $tweet;
    }

    public function insert($tweet, $member_id, $reply_tweet_id) {
        $sql = 'INSERT INTO `tweets` SET `tweet`=?,
                                         `member_id`=?,
                                         `reply_tweet_id`=?,
                                         `created`=NOW()';
        $data = array($tweet, $member_id, $reply_tweet_id);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($data);

        header('Location: top_object.php');
        exit();
    }

    public function isLogin() {
        if (1) {
            return array('hoge');
        } else {
            return false;
        }
    }

}
?>









