<?php
/*
 *  @author YukiKawasaki
 *  FileName addEmployee.php
 *  @create 2014/05/01
 *  Remark 従業員を新規登録
 *         05/02始業時間終業時間をプルダウンに変更。
 *         05/07CSRF、管理者かどうか識別できるように。
 *         05/14 ファイル名変更 (真島)
*/
    require_once("../../util/util.php");
    session_start();
    
    //不正なアクセスを除外
    if (!isset($_SESSION["me"]) || !$_SESSION["me"]["admin"]) {
        die("不正なログインです。");
    }else {
        ;//DoNothing
    }

$deviceType = util::getDeviceType($_SERVER['HTTP_USER_AGENT']);

?>
<!DOCTYPE html>
<HTML>
    <head>
        <?php
        define("title" ,"ユーザ新規登録");
        define("cssPath","../../");
        include_once("../../include/header.php");
        ?>
    </head>
    <body>
     <img id="logo" src="../../images/header_logo.png" alt="" width="140" height="70">
     <hr id="linetop">
    <fieldset id="emp">
        <legend>ユーザ新規登録</legend>
        <table>
            <tr>
                <td>氏名 </td>
                <td><input type="text" id="name"></td>
            </tr>
            <tr>
                <td>email(IDの代わりになります)</td>
                <td><input type="text" id="email"></td>
            </tr>
            <tr>
                <td>パスワード</td>
                <td><input type="password" id="pass1"></td>
            </tr>
            <tr>
                <td>始業時間-就業時間: </td>
                <td>
                    <select id="startHour">
                        <?php for ($i = 1; $i <= 24; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="startMin">
                        <?php for ($i = 0; $i <= 3; $i++): ?>
                        <option value="<?php echo $i * 15; ?>"><?php echo $i * 15; ?></option>
                        <?php endfor; ?>
                    </select>
                    -
                    <select id="endHour">
                        <?php for ($i = 1; $i <= 24; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="endMin">
                        <?php for ($i = 0; $i <= 3; $i++): ?>
                        <option value="<?php echo $i * 15; ?>"><?php echo $i * 15; ?></option>
                        <?php endfor; ?>
                    </select>
            </tr>
        </table>
        <input type="hidden" value="<?php echo $_SESSION["token"]; ?>" id="token">
        <input type="button" value="登録" id="submit"><br>
        <span id="msg"></span><br>
        </fieldset>
        <br>
        <a  class="back" href="../admin.php">戻る</a>
        <?php include_once("../../include/footer.php"); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/JavaScript" src="../js/addEmployee.js"></script>
    </body>
</html>