<?php
/*
 * @author  Ryo Mashima
 * FileName logout.php
 * @create  2014/05/07
 * Remark   ログアウト処理
*/

session_start();
// セッション変数を解除
$_SESSION = array();

// セッションcookieを削除
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// セッションを破棄
session_destroy();

header('Location: ../index.php');

//EOF