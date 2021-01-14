<?php
try{
    $db = new PDO('mysql:dbname=mini_bbs;host=localhost;charset=utf8','root','' );
}catch(PDOException $e){
    print('データベース接続エラー:'.$e->getMessage());
}