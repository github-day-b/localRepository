<?php
/*
 * @author  Ryo Mashima
 * FileName index.php
 * @create  2014/05/07
 * Remark   ログインできるかチェックして可能なら従業員側、管理者側それぞれに遷移
            05/07 index.phpからログイン処理をcheckLogin.phpへ分割。
            2015/01/05
            グループ新規登録(registGroup.php)へのリンク追加
*/

session_start();
require_once("./util/util.php");
require_once("./util/constant.php");

$kintaiGrpId = "";
$kintaiUserId = "";
$kintaiPass = "";

if(isset($_COOKIE["kintaiGrpId"])){
    $kintaiGrpId = $_COOKIE["kintaiGrpId"];
}
if(isset($_COOKIE["kintaiUserId"])){
    $kintaiUserId = $_COOKIE["kintaiUserId"];
}
if(isset($_COOKIE["kintaiPass"])){
    $kintaiPass = $_COOKIE["kintaiPass"];
}

$deviceType = util::getDeviceType($_SERVER['HTTP_USER_AGENT']);

?>
<!doctype html>
<html lang="ja">
<head>
    <?php
    define("title" ,"ログイン画面");
    define("cssPath","./");
    include_once("./include/header.php");
    ?>
    <script type="text/javascript" src="./checkForm.js"></script>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.17.2/build/cssreset/cssreset-min.css">
</head>

<body id="loginPage">
<div id="contents">
    <img id="logo" src="./images/header_logo.png" alt="" width="140" height="70">
    <hr id="linetop">
        <form action="./checkLogin.php" method="POST" name="loginForm" onSubmit="return checkForm()">
        <fieldset id="login">
            <legend>ログイン</legend>
                <p><label>グループID</label><input type="text" name="groupid" value="<?php echo $kintaiGrpId ?>"></p>
                <p><label>ユーザID</label><input type="text" name="userid"  value="<?php echo $kintaiUserId ?>"></p>
                <p><label>パスワード</label><input type="password" name="pass"  value="<?php echo $kintaiPass ?>"></p>
                <label>入力内容を保存する</label><input type="checkbox" name="saveflg" value="on" /><br/>
                <input type="submit" name="login" id="loginBtn" value="ログイン"><br />
                <p><a href="./group/registGroup.php">グループ新規登録</a></p>
                <span id="msg"><?php if(!empty($_SESSION["msg"])){ echo util::h($_SESSION["msg"]); } ?></span>
        </fieldset>
        </form>

</div><!--#contents -->
<?php include_once("./include/footer.php"); ?>
</body>
</html>