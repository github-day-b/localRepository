<?php
/*
 *  @author YukiMori
 *  FileName ajax_changeGroup.php
 *  @create 2015/01/05
 *  Remark グループ情報更新
*/
require_once("../database/groupinfoDB.php");
require_once("../util/util.php");
session_start();
ini_set( 'display_errors', 1 );
//CSRF
if($_SESSION["token"] != util::h($_POST["token"])) {
    die("不正なアクセスです。");
}else {
    ;//DoNothing
}
$db        =  new GroupInfoDB();
$groupId   = util::h($_POST["groupId"]);
$groupName = util::h($_POST["groupName"]);
$groupLocation = util::h($_POST["groupLocation"]);

//入力チェック
if(empty($groupName)) {
    die("名前が入ってません");
}else if(strlen($groupName) > 30) {
    die("名前長くないですか?");
}else {
    ;//DoNothing
}

header('Content-type: text/javascript; charset=utf-8');
echo $db->changeGroup($groupId, $groupName, $groupLocation);
$_SESSION['me']['groupName'] = $groupName;