<?php
    /*
     *  @author YukiKawasaki
     *  FileName ajax_confEmployee.php
     *  @create 2014/05/01
     *  Remark 従業員を変更するだけ。
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

    $empId        = util::h($_POST["empId"]);
    $name         = util::h($_POST["name"]);
    $email        = util::h($_POST["email"]);
    $startHour    = util::h($_POST["startHour"]);
    $startMin     = util::h($_POST["startMin"]);
    $endHour      = util::h($_POST["endHour"]);
    $endMin       = util::h($_POST["endMin"]);
    $startTime    = util::makeTime($startHour, $startMin);
    $endTime      = util::makeTime($endHour, $endMin);
    $db           = new EmpInfoDB();
    
    //入力チェック
    if(empty($name)) {
        die("名前が入ってません");
    }else if(empty($email)) {
        die("emailが入ってません");
    }else if(strlen($name) > 30) {
        die("名前長くないですか?");
    }else if(strlen($email) > 255) {
        die("email長くないですか");
    }else if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i", $email)) {
        //emailチェック。不勉強で正規表現ができない。
        die("それメールアドレスですか?");
    }else {
        ;//DoNothing
    }
    
    $password = "";

    if (!empty($_POST["pass"])) {
        $rawPassword  = util::h($_POST["pass"]);
        $password     = util::makePass($rawPassword);
    }

    header('Content-type: text/javascript; charset=utf-8');
    echo $db->changeEmployee($empId, $name, $email, $password, $startTime, $endTime, $password);//従業員情報の変更
//EOF