<?php
//session_startはセッションを使う場合は必ず書く
session_start();
require('dbconenect.php');

//$_SESSION['id']を確認して誰かがログインしていることを確認する
if(isset($_SESSION['id'])){
    //$post['id']、投稿の管理idがindex.phpから $_REQUEST['id']で渡ってくるので受け取る
    $id = $_REQUEST['id'];
    //コメント削除で必要なのはpostsテーブルのみ
    $messages = $db->prepare('SELECT * FROM posts WHERE id=?'); //SQL
    $messages->execute(array($id)); //SQLの?に入れたいもの
    $message = $messages->fetch(); //SQLを完成させる

    //ログインしているユーザーが削除しようとしているコメントは
    //本当にログインしているユーザーが投稿したコメントなのか確認
    //-> 削除
    if($message['member_id'] === $_SESSION['id']){
        $del = $db->prepare('DELETE FROM posts WHERE id=?');
        $del->execute(array($id));
    }
}
header('Location: index.php');
exit();
?>