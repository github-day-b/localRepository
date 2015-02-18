<?php
    /*
     *  @author YukiKawasaki
     *  FileName confEmployee.php
     *  @create 2014:05:01
     *  Remark 従業員情報を変更。表示の時に従業員情報をテキストボックスに表示しておく。
     *         パスワードは任意。
     *         05/02始業時間終業時間をプルダウンに変更。
     *         05/07CSRF、管理者かどうか識別できるように
               2014/05/16
               sql操作をempinfoDBに追い出した。
    */
    require_once("../../util/util.php");
    require_once("../../database/empinfoDB.php");
    session_start();
    
    //不正なアクセスを除外
    if (!isset($_SESSION["me"]) || !$_SESSION["me"]["admin"]) {
        die("不正なログインです。");
    }else {
        ;//DoNothing
    }

    if(isset($_GET["search"])) {
        $search = util::h($_GET["search"]);
    }

    $empId  = util::h($_GET["empId"]);
    $db     = new EmpInfoDB();
    $data   = $db->detailEmployee($empId);
    
    //時分を分解
    $startTime = explode(":", $data["startTime"]);
    $endTime = explode(":", $data["endTime"]);

    //CSRF対策のトークン
    $_SESSION["token"] = util::setToken();

    $deviceType = util::getDeviceType($_SERVER['HTTP_USER_AGENT']);

?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        define("title" ,"ユーザ情報変更");
        define("cssPath","../../");
        include_once("../../include/header.php");
        ?>
    </head>
    <body>
    <img id="logo" src="../../images/header_logo.png" alt="" width="140" height="70">
    <hr id="linetop">
    <fieldset id="emp">
        <legend>ユーザ情報変更</legend>
        <table>
            <tr>
                <td>氏名 </td><td><input type="text" id="name" value="<?php echo $data["empName"]; ?>"></td>
            </tr>
            <tr>
                <td>email</td><td><input type="text" id="email" value="<?php echo $data["email"]; ?>"></td>
            </tr>
            <tr>
                <td>新しいパスワード</td><td><input type="password" id="pass"></td>
            </tr>
            <tr>
                <td>始業時間-就業時間: </td>
                <td><select id = "startHour">
                        <?php for($i = 0; $i <= 24; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php if($i == $startTime[0]) { echo "selected"; } ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id = "startMin">
                        <?php for($i = 0; $i <= 3; $i++): ?>
                        <option value="<?php echo $i * 15; ?>" <?php if($i * 15 == $startTime[1]) { echo "selected"; } ?>><?php echo $i * 15; ?></option>
                        <?php endfor; ?>
                    </select>
                    -
                    <select id = "endHour">
                        <?php for($i = 0; $i <= 24; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php if($i == $endTime[0]) { echo "selected"; } ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id = "endMin">
                        <?php for($i = 0; $i <= 3; $i++): ?>
                        <option value="<?php echo $i * 15; ?>" <?php if($i * 15 == $endTime[1]) { echo "selected"; } ?>><?php echo $i * 15; ?></option>
                        <?php endfor; ?>
                    </select></td>
            </tr>
        </table><br>
        <input type="hidden" id="empId" value="<?php echo $empId; ?>">
        <input type="hidden" id="token" value="<?php echo $_SESSION["token"]; ?>">
        <input type="button" value="変更" id="change">
        <input type="button" value="削除" id="del"><br>
        <span id="msg"></span><br>
    </fieldset>
    <br>
        <a  class="back" href="../admin.php<?php if(isset($search)){ echo "#search=".$search; }; ?>">戻る</a>
        <?php include_once("../../include/footer.php"); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/JavaScript" src="../js/changeEmployee.js"></script>
        <script type="text/JavaScript" src="../js/deleteEmployee.js"></script>
    </body>
</html>