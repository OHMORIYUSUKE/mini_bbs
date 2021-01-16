<?php
error_reporting(E_ALL & ~ E_DEPRECATED & ~ E_USER_DEPRECATED & ~ E_NOTICE);
?>

<?php
//session_startはセッションを使う場合は必ず書く
session_start();
require('dbconenect.php');

//ログインした場合はセッションにidとtimeがあるのでifに入る
if(isset($_SESSION['id']) && $_SESSION['time']+3600>time()){
  //ログイン中に行動したらセッションタイムを更新
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  //ログインしたときセッションに保存したidを使う
  $members->execute(array($_SESSION['id']));
  //$memberに$member['id']と$member['name']が入っている
  $member = $members->fetch();
}else{
  header('Location: login.php');
  exit();
}
//if 投稿するボタンが押されたとき
if(!empty($_POST)){
  //下のテキストエリアがname="message"のため($_POST['message']である
  if($_POST['message'] !== ''){
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, created=NOW()');
    $message->execute(array(
      $member['id'],
      $_POST['message']
    ));
    //messageをdbに保存したら再読み込みして$_POST['message']を削除する
    //リロードされて同じメッセージが誤送信されることを防ぐ
    header('Location: index.php');
    exit();
  }
}

$posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC');

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php print(htmlspecialchars($member['name'],ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"></textarea>
          <input type="hidden" name="reply_post_id" value="" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

<?php foreach($posts as $post): ?>
    <div class="msg">
    <?php //画像を表示?>
    <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" width="48" height="48" alt="" />
    <?php //メッセージを表示?>
    <p><span class="name"><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?>
    <?php //名前を表示?>
    （<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>）
    </span>[<a href="index.php?res=">Re</a>]</p>
    <?php //投稿された時刻を表示?>
    <p class="day"><a href="view.php?id="><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>
<a href="view.php?id=">
返信元のメッセージ</a>
[<a href="delete.php?id="
style="color: #F33;">削除</a>]
    </p>
    </div>
<?php endforeach; ?>

<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
