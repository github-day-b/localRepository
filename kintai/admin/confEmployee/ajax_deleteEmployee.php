<?php
    /*
     *  @author YukiKawasaki
     *  FileName ajax_deleteEmployee.php
     *  @create 2014/05/01
     *  Remark 従業員を削除する
     *         05/07 CSRF、管理者化どうか識別できるように
               2014/05/16
               sql操作をempinfoDBに追い出した。
    */
    require_once("../../util/util.php");
    require_once("../../database/empinfoDB.php");
    session_start();

    //CSRF対策
    if (empty($_SESSION["token"]) || $_SESSION["token"] != $_POST["token"] 
        || !isset($_SESSION["me"]) || !$_SESSION["me"]["admin"]) {
        die("不正なアクセスです。");
    }else {
        ;//DoNothing
    }

    $empId = util::h($_POST["id"]);
    $db     = new EmpInfoDB();

    header('Content-type: text/javascript; charset=utf-8'); 
    echo $db->deleteEmployee($empId);//従業員を削除。
//EOF