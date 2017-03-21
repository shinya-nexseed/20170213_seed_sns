<?php
// ①「seedくん」という文字列を出力する「nexseed」という名前の関数を作成してみましょう。（引数はなし）

// 実行と定義は順が逆でも関係ない
nexseed();

// 定義
function nexseed() {
  // まとめたい処理
  echo 'seedくん';
  echo '<br>';
}

// 実行
nexseed();
nexseed();
nexseed();


// 引数
function greeting($name) {
  // $name = 'けんすけ';
  echo 'こんにちは、' . $name . 'さん！';
  echo 'お元気ですか？';
  echo '<br>';
}


greeting('けんすけ');
greeting('ちゃんゆか');

// 定数
// 定数は変数の逆で、一度定義したら内容を書き換えられない
// 定数名は基本大文字で定義
// define('定数名', 値);
define('DEBUG', true); // DEBUGという定数を定義

// echoをオリジナル化
// echoのめんどくさいところ
// デバッグ時に出力した際、改行がデフォルトでついていない
// デバッグが終了した際、いちいち手動で全echoを削除する必要がある
function own_echo($value) {
    // 改行つきでechoする処理をまとめる
    if (DEBUG) {
        echo $value . '<br>';
    }
}

own_echo('ほげほげ');
own_echo('ふがふが');
own_echo('Hello');

?>
















