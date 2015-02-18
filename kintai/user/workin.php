<?php
/*
 * @author  Ryo Mashima
 * FileName workin.php
 * @create  2014/05/07
 * Remark   出勤処理
            2014/05/15出勤が定時より早ければ定時の時刻で登録されるようにした。
            2014/05/16
            sql操作をtimesheetDB.phpに追い出した。
            2014/06/26
            JavaScript生成するコードをなくした。
*/
session_start();

require_once("../database/timesheetDB.php");
require_once("../util/util.php");
date_default_timezone_set('Asia/Tokyo');//タイムゾーンの設定
//複数タブで別のユーザがログインするとタブ間でセッション情報が上書きされる。
//管理者は見れなくする。
if(!isset($_SESSION["me"]) || $_SESSION["me"]["admin"] ||
    !isset($_SESSION["token"])
    //|| $_SESSION["token"] !== util::h($_POST["token"]) 挙動が怪しいのでひとまずコメントアウト
    ) {
    die("不正なログインです");
}

$empId = $_SESSION['me']['emp_id'];
$location = util::h($_POST["location"]);
$db = new TimeSheetDB();

//正しくデータが挿入できたらアラート表示
//@8出勤処理
if ($db->workIn($empId,$location)) {
    echo true;
} else {
    echo "データの追加を完了出来ませんでした";
}
//EOF