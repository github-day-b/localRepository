<?php
/*
 * @author  Ryo Mashima
 * FileName workout.php
 * @create  2014/05/07
 * Remark   退勤処理
            2014/05/16
            DB処理をtimesheetDB.phpに追い出した。
            2014/06/26
            JavaScript生成するコードをなくした。
*/
session_start();

require_once("../database/timesheetDB.php");
require_once("../util/util.php");
date_default_timezone_set('Asia/Tokyo');//タイムゾーンの設定
//複数タブで別のユーザがログインするとタブ間でセッション情報が上書きされる。
//無理みたいなのでせめて管理者は操作できないようにする。
if(!isset($_SESSION["me"]) || $_SESSION["me"]["admin"] || 
    !isset($_SESSION["token"])
    //|| $_SESSION["token"] !== util::h($_POST["token"]) 挙動が怪しいのでひとまずコメントアウト
    ) {
    die("不正なログインです");
}

$db = new TimeSheetDB();

$empId = $_SESSION['me']['emp_id'];//ログインユーザの従業員IDを取得
$location = util::h($_POST["location"]);

//データが正常に追加できたか
//@9退勤処理
if ($db->workOut($empId,$location)) {
    echo true;
} else {
    echo 'データの追加に失敗しました';
}
//EOF