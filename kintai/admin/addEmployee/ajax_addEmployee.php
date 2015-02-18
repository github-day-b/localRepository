<?php
    /*
     *  @author YukiKawasaki
     *  FileName ajax_addEmployee.php
     *  @create 2014/05/01
     *  Remark 従業員を登録するだけ
     *         05/13サーバサイドのチェック追加。
               05/16
               sql操作をempinfoDBに追い出した。
               2015/01/05
               グループIDを追加
    */
    require_once("../../database/empinfoDB.php");
    require_once("../../util/util.php");
    session_start();
    //CSRF
    if(!isset($_SESSION["me"]) || !$_SESSION["me"]["admin"] || 
        $_SESSION["token"] != util::h($_POST["token"])) {
        die("不正なアクセスです。");
    }else {
        ;//DoNothing
    }

    $name         = util::h($_POST["name"]);
    $email        = util::h($_POST["email"]);
    $rawPassword  = util::h($_POST["pass"]);
    $startHour    = util::h($_POST["startHour"]);
    $startMin     = util::h($_POST["startMin"]);
    $endHour      = util::h($_POST["endHour"]);
    $endMin       = util::h($_POST["endMin"]);
    $startTime    = util::makeTime($startHour, $startMin);
    $endTime      = util::makeTime($endHour, $endMin);
    $password     = util::makePass($rawPassword);
    $empDB        = new EmpInfoDB();

    //入力チェック
    if(empty($name)) {
        die("名前が入ってません");
    }else if(empty($email)) {
        die("emailが入ってません");
    }else if(strlen($name) > 30) {
        die("名前長くないですか");
    }else if(strlen($email) > 255) {
        die("email長くないですか");
    }else if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i", $email)) {
        //emailチェック。不勉強で正規表現ができない。
        die("それメールアドレスですか?");
    }else if(strlen($rawPassword) > 100) {
                die("パスワード長くないですか?");
    }else {
        ;//DoNothing
    }
    $addResult = $empDB->addEmployee($name, $email, $_SESSION["me"]["group_id"], $password, $startTime, $endTime);//@3従業員を登録

    header('Content-type: text/javascript; charset=utf-8');
    echo $addResult; 
//EOF