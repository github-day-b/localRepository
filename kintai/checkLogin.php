<?php
/*
 * @author  RyoMashima
 * FileName checkLogin.php
 * @create  2014/05/07
 * Remark   ログインできるかチェックして可能なら従業員側、管理者側それぞれに遷移
            05/07 index.phpからログイン処理をこちらへ分割。
            05/15 getUser()を削除してクロージャに
*/
ini_set( 'display_errors', 1 ); 
session_start();
require_once('./util/util.php');
require_once('./database/empinfoDB.php');
    $groupid = util::h($_POST["groupid"]);
    $id = util::h($_POST['userid']);
    $pass = util::h($_POST["pass"]);

    $saveflg = $_POST["saveflg"];
    if("on" == $saveflg){
        $_SESSION["kintaiGrpId"] = $groupid;
        $_SESSION["kintaiUserId"] = $id;
        $_SESSION["kintaiPass"] = $pass;
    }

    $me = call_user_func(function($groupid,$id) {
                            $db = new empInfoDB();
                            $user = $db->fetchUser($groupid,$id);//@7email、グループIDを条件にユーザを一件だけ取り出す。

                            return $user ? $user : false;
                        }, $groupid,$id);

    
    $password = util::makePass($pass);

    $_SESSION["msg"] = "";
    //ログイン処理
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($groupid == '') {
            $_SESSION["msg"] = 'グループIDを入力してください';
        }
        //メールアドレスが空？
        if ($id == '') {
            $_SESSION["msg"] = 'ユーザIDを入力してください';
        }
        //メールアドレスとパスワードが正しくない
        //パスワードが空？
        if (empty($pass)) {
            $_SESSION["msg"] = 'パスワードを入力してください';
        }else if ($password != $me[0]["password"]) {
            $_SESSION["msg"] = "IDかパスワードが違います。";
        }else {
            ;//DoNothing
        }

        
        //$_SESSION["msg"]が空ならログイン成功
        //管理者ならばadimin.phpへ、そうでなければuser.phpへ
        if (empty($_SESSION["msg"])) {
            $_SESSION["msg"] = "";
            $_SESSION["me"] = $me[0];
            if($_SESSION["me"]["admin"]) {
                header("location: ./admin/admin.php");
                exit;
            }else {
                header('Location: ./user/user.php');
                exit;
            }
        } else {
            header("location: ./index.php");
        }
    }
//EOF