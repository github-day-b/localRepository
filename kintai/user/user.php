<?php
/*
 * @author  Ryo Mashima
 * FileName /user/index.php
 * @create  2014/05/07
 * Remark   従業員側のユーザページ
            2015/01/05
            グループ名を表示するようにした。
*/

session_start();
require_once("../util/util.php");
require_once("../util/constant.php");
require_once("./makeTimeSheet.php");
require_once("./attendance.php");

//複数タブで別のユーザがログインするとタブ間でセッション情報が上書きされる。
//GETでIDを渡してセッションと一致するかチェックする
//管理者は見れなくする。
if(!isset($_SESSION["me"]) || $_SESSION["me"]["admin"]) {
    die("不正なログインです");
}

$disabled  = "disabled";

$_SESSION["token"] = util::setToken();

if(isset($_SESSION["kintaiGrpId"]) && isset($_SESSION["kintaiUserId"]) && isset($_SESSION["kintaiPass"])){
    setcookie("kintaiGrpId","", time() -1800);
    setcookie("kintaiUserId","", time() -1800);
    setcookie("kintaiPass","", time() -1800);

    setcookie("kintaiGrpId", $_SESSION["kintaiGrpId"],time()+60*60*24*7,"/kintai/");
    setcookie("kintaiUserId", $_SESSION["kintaiUserId"],time()+60*60*24*7,"/kintai/");
    setcookie("kintaiPass", $_SESSION["kintaiPass"],time()+60*60*24*7,"/kintai/");
}

$deviceType = util::getDeviceType($_SERVER['HTTP_USER_AGENT']);

?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
    <?php
    define("title" ,"ユーザー画面");
    define("cssPath","../");
    include_once("../include/header.php");
    ?>
</head>
<body id="user">
<a href="http://day-b.jp">
    <img id="logo" border="0" src="../images/header_logo.png" alt="" width="140" height="70">
</a>
<hr id="linetop">
<div id="contents">
    <div id="date"></div>
    <div id="time"></div>
        <img src="../images/human.jpg" alt="" width="42" height="40"><br />
        <?php echo $_SESSION["me"]["groupName"] ?><br />
        [<?php echo util::h($_SESSION['me']['emp_name']);?>]
        <div id="form">
    <?php if (!$_SESSION['me']['start']) :?>
            <input type="button" id="workin" value="出勤">
            <input type="button" id="workout" value="退勤" <?php echo $disabled; ?>>
    <?php endif ;?>
    <?php if (!$_SESSION['me']['end'] && $_SESSION['me']['start']) :?>
            <input type="button" id="workin" value="出勤" <?php echo $disabled; ?>>
            <input type="button" id="workout" value="退勤">
    <?php endif ;?>
    <?php if ($_SESSION['me']['end']  && $_SESSION['me']['start']) :?>
            <input type="button" id="workin" value="出勤" <?php echo $disabled; ?>>
            <input type="button" id="workout" value="退勤" <?php echo $disabled; ?>>
    <?php endif ;?>
            <input type="hidden" id="token" value="<?php echo $_SESSION["token"]; ?>">
            <input type="hidden" id="location" value="<?php echo $_SESSION["me"]["location"]; ?>">
        </div>

    <form action="timesheet/timesheet.php" method="GET">
        <input type="submit" id="userRef" value="勤務表参照"><br><span style="color:DarkGray;">勤務表</span>
        <input type="hidden" name="month" value="<?php echo date('n');?>">
        <input type="hidden" name="year" value="<?php echo date('Y');?>">
    </form>
    <div id="mapField"></div>
    <p><input type="button" id="logoutBtn" value="ログアウト"></p>
</div>
    <?php include_once("../include/footer.php"); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="./js/nowTime.js" type="text/javascript"></script>
    <script src="../util/confLogout.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script src="./js/user3.js"></script>
</body>
</html>