<?php
    /*
     *  @author YukiKawasaki
     *  FileName ajax_makeGroup.php
     *  @create 2015/01/05
     *  Remark グループ情報登録
    */
    require_once("../database/groupinfoDB.php");
    require_once("../database/empinfoDB.php");
    require_once("../util/util.php");
    session_start();
    ini_set( 'display_errors', 1 ); 
    //CSRF
    if($_SESSION["token"] != util::h($_POST["token"])) {
        die("不正なアクセスです。");
    }else {
        ;//DoNothing
    }

    $groupID        = util::h($_POST["groupID"]);
    $groupName      = util::h($_POST["groupName"]);
    $adminName      = util::h($_POST["adminName"]);
    $adminMail      = util::h($_POST["adminMail"]);
    $rawPassword    = util::h($_POST["password"]);
    $groupLocation  = util::h($_POST["location"]);
    $password       = util::makePass($rawPassword);
    
    //入力チェック
    if(empty($groupID)) {
        die("グループIDが入力されていません");
    }else if(strlen($groupID) > 30) {
        die("グループIDが長すぎます");
    }else if(!preg_match("/^[A-Za-z0-9\+_\-]+$/", $groupID)) {
        die("グループ名は半角英数字で入力してください");
    }else if(empty($groupName)) {
        die("グループ名が入力されていません");
    }else if(strlen($groupName) > 30) {
        die("グループ名が長すぎます");
    }else if(empty($adminName)) {
        die("管理者名が入力されていません");
    }else if(strlen($adminName) > 30) {
        die("管理者名が長すぎます");
    }else if(empty($adminMail)) {
        die("管理者emailが入力されていません");
    }else if(strlen($adminMail) > 255) {
        die("アドレスが長すぎます");
    }else if(!preg_match("/^[A-Za-z0-9\.]+[\w-]+@[\w\.-]+\.\w{2,}$/", $adminMail)) {
        die("emailと形式が異なります");
    }else if(empty($rawPassword)) {
        die("パスワードを入力してください");
    }else {
        ;//DoNothing
    }

    /*存在チェックと登録*/
    $groupDB   = new GroupInfoDB();
    $empinfoDB = new EmpInfoDB();
    $groupData = $groupDB->getGroup($groupID);
    $adminData = $empinfoDB->fetchAdmin($adminMail, $groupID);
    if(empty($groupData) && empty($adminData) 
    	&& $groupDB->addGroup($groupID, $groupName, $groupLocation)
    	&& $empinfoDB->addAdmin($adminName, $adminMail, $password, $groupID)) {
    	die("OK");
    }else if(!empty($adminData)) {
    	die("入力したグループID内で既に登録されているメールアドレスです");
    }else if(!empty($groupData)){
    	die("入力されたグループIDは既に使用されています");
    }else {
    	;//DoNothing
    }
//EOF