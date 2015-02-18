<?php
/* @author YukiKawasaki
 *  FileName makeGroup.php
 *  @create 2014/01/05
 *  Remark グループを新規登録
 */
    require_once("../util/util.php");
    session_start();
    $_SESSION["token"] = util::setToken();

    $deviceType = util::getDeviceType($_SERVER['HTTP_USER_AGENT']);
?>
<!DOCTYPE html>
<HTML>
    <head>
        <?php
        define("title" ,"グループ新規登録");
        define("cssPath","../");
        include_once("../include/header.php");
        ?>
    </head>
    <body>
    <div id="container">
         <img id="logo" src="../images/header_logo.png" alt="" width="140" height="70">
        <hr id="linetop">
        <fieldset id="groupInfo">
            <legend>グループ新規登録</legend>
            <table id="form">
                <tr>
                    <td>グループID</td>
                    <td><input type="text" id="groupID" placeholder="必須"></td>
                </tr>
                <tr>
                    <td>グループ名</td>
                    <td><input type="text" id="groupName" placeholder="必須"></td>
                </tr> 
               <tr>
                    <td>管理者名</td>
                    <td><input type="text" id="adminName" placeholder="必須"></td>
                </tr>
 
                <tr>
                    <td>管理者email</td>
                    <td><input type="text" id="adminMail" placeholder="必須"></td>
                </tr>
                <tr>
                    <td>パスワード</td>
                    <td><input type="password" id="password"></td>
                </tr>
                <tr>
                    <td>位置情報を取得する</td>
                    <td><input type="checkbox" id="locationFlg" value="1"></td>
                </tr>
           </table>
            <input type="hidden" value="<?php echo $_SESSION["token"]; ?>" id="token">
        	<div class"btn">
            	<input type="button" value="登録" id="submit"><br>
        	</div>
            <span id="msg"></span><br>
        </fieldset>
        <br>
    </div>
        <a class="back" href="../index.php">戻る</a>
        <?php include_once("../include/footer.php"); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/JavaScript" src="../util/checkForm.js"></script>
        <script type="text/JavaScript" src="./js/registGroup.js"></script>
    </body>
</html>
