<?php
/*
 *  @author YukiMori
 *  FileName confGroup.php
 *  @create 2014:05:01
 *  Remark 従業員情報変更をベースに作成
*/
require_once("../util/util.php");
require_once("../database/groupinfoDB.php");
session_start();

//不正なアクセスを除外
if (!isset($_SESSION["me"]) || !$_SESSION["me"]["admin"]) {
    die("不正なログインです。");
}else {
    ;//DoNothing
}

$groupId  = $_SESSION["me"]["group_id"];
$db     = new GroupInfoDB();
$data   = $db->getGroup($groupId);

//CSRF対策のトークン
$_SESSION["token"] = util::setToken();
$deviceType = util::getDeviceType($_SERVER['HTTP_USER_AGENT']);
?>
<!DOCTYPE html>
<html>
<head>
    <?php
    define("title" ,"グループ情報変更");
    define("cssPath","../");
    include_once("../include/header.php");
    ?>
</head>
<body>
<div id="container">
    <img id="logo" src="../images/header_logo.png" alt="" width="140" height="70">
    <hr id="linetop">
    <fieldset id="groupInfo">
        <legend>グループ情報変更</legend>
        <table>
            <tr>
                <td>グループID</td><td><input type="text" id="groupId" value="<?php echo $data[0]["group_id"]; ?>" readonly></td>
            </tr>
            <tr>
                <td>グループ名</td><td><input type="text" id="groupName" value="<?php echo $data[0]["group_name"]; ?>"></td>
            </tr>
            <tr>
                <td>位置情報を取得する</td><td><input type="checkbox" id="locationFlg" value="1" <?php if($data[0]["location_flg"] == 1){ ?>  checked="checked" <?php } ?>></td>
            </tr>
        </table>
        <br>
        <input type="hidden" id="groupId" value="<?php echo $groupId; ?>">
        <input type="hidden" id="token" value="<?php echo $_SESSION["token"]; ?>">
        <input type="button" value="変更" id="change"><br />
        <span id="msg"></span>
        <br>
    </fieldset>
    <br>
</div>
    <a  class="back" href="../admin/admin.php">戻る</a>

<?php include_once("../include/footer.php"); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/JavaScript" src="./js/changeGroup.js"></script>

</body>
</html>