<?php
    /*
     *  @author YukiKawasaki
     *  FileName ajax_emp_search.php
     *  @create 2014/05/01
     *  Remark 従業員を検索してjsonをAjax通信で送りつける。
                2014/05/02: WHERE句をLIKEに変更。
                2014/05/07: CSRF対策、セッションで管理者かどうか識別できるようにした。
                2014/05/13: エラーチェック
                2014/05/16
                sql操作をempinfoDBに追い出した。
    */
    session_start();
    require_once('../database/empinfoDB.php');
    require_once('../util/util.php');
    //CSRF対策
    if (empty($_SESSION["token"]) || $_SESSION["token"] != $_POST["token"] || !$_SESSION["me"]["admin"]) {
        echo "不正なアクセスです。";
        die;
    }else {
        ;//DoNothing
    }

    $groupId = util::h($_POST["groupId"]);
    $name = util::h($_POST["name"]);
    $db   = new EmpInfoDB();

    //エラーチェック。DBサイズより大きいものがないかチェック。
    if(strlen($name) > 30) {
        die("名前が長いです。");
    }

    //JSON送信
    header('Content-type: text/javascript; charset=utf-8'); 
    echo json_encode($db->empSearch($groupId,$name));//@2従業員を検索して結果表示
//EOF