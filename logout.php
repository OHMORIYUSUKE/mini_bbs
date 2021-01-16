<?php
session_start();

$_SESSION = array();
if(ini_set('session.use_coolies')){
    $params = session_get_cookie_params();
    setcookie(session_name() . '', time() - 42000, 
            $params['path'], $params['domain'], 
            $params['secure'], $params['httponly']
            );
}

//セッションを削除  
session_destroy();
//クッキーを削除
setcookie('email','',time() - 3600);

header('Location: login.php');
exit();
?>