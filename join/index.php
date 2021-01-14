<?php
error_reporting(E_ALL & ~ E_DEPRECATED & ~ E_USER_DEPRECATED & ~ E_NOTICE);
?>

<?php
session_start();
require('../dbconenect.php');

if(!empty($_POST)){

	if($_POST['name'] === ''){
		$errer['name']='blank';
	}
	if($_POST['email'] === ''){
		$errer['email']='blank';
	}
	if(strlen($_POST['password']) <4 ){
		$errer['password']='length';
	}
	if($_POST['password'] === ''){
		$errer['password']='blank';
	}
	//画像拡張子判定
	$fileName = $_FILES['image']['name'];
	if(!empty($fileName)){
		$ext = substr($fileName, -3);
		if($ext != 'jpg' && $ext != 'png' && $ext != 'gif'){
			$errer['image'] = 'type';
		}
	}

	//アカウント重複防止
	if(empty($error)){
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$recode = $member->fetch();
		//$recodeは0,1を返す
		if($recode['cnt'] > 0){
			$errer['email'] = 'duplicate';
		}
	}


	if(empty($errer)){
		//画像保存//現在時刻を付加して名前がかぶらないようにする
		$image = date('YmdHis').$_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image);

		$_SESSION['join'] = $_POST;

		$_SESSION['join']['image'] = $image;

		header('Location:check.php');
		exit();
	}
}
//rewriteがurlで渡されたらフォームに入力しておく
if($_REQUEST['action'] == 'rewrite'){
	$_POST = $_SESSION['join'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
		</dd>
		<?php
		if ($errer['name'] === 'blank'):
		?>
		<p class="error">*ニックネームを入力してください。</p>
		<?php endif;?>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['email'],ENT_QUOTES)); ?>" />
		<?php
		if ($errer['email'] === 'blank'):
		?>
		<p class="error">*メールアドレスを入力してください。</p>
		<?php endif;?>
		<?php
		if ($errer['email'] === 'duplicate'):
		?>
		<p class="error">*メールアドレスはすでに登録されています。</p>
		<?php endif;?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES)); ?>" />
        </dd>
		<?php
		if ($errer['password'] === 'blank'):
		?>
		<p class="error">*パスワードを入力してください。</p>
		<?php endif;?>
		<?php
		if ($errer['password'] === 'length'):
		?>
		<p class="error">*パスワードが短すぎます。</p>
		<?php endif;?>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />

			<?php 
			if($errer['image'] === 'type'):?>
			<p class="error">画像のみアップロードできます</p>
			<?php endif; ?>
			<?php 
			if(!empty($errer)):
			?>
			<p class="error">画像を再度アップロードしてください</p>
			<?php endif;?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
