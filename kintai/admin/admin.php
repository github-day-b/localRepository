<?php
    /*
     * @author YukiKawasaki
     * FileName admin.php
     * @create 2014/05/01
     * Remark 管理者画面。出勤した人の一覧を取得、従業員検索ができる。
     * 05/07セッションで管理者か否か識別できるように
     * 05/14 ファイル名変更(真島)
     * 2015/01/05
     * グループ情報変更ページの仮リンク追加
    */
    session_start();
    require_once("../util/util.php");
    require_once("../util/constant.php");

    //管理者としてログインしたかどうかチェック
    if (!isset($_SESSION["me"]["admin"])) {
         die("不正なアクセスです。");
    }else {
        ;//DoNothing
    }
    //検索フォームのワンタイムパスワード
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
<!DOCTYPE html>
<html>
    <head>
        <?php
        define("title" ,"管理者画面");
        define("cssPath","../");
        include_once("../include/header.php");
        ?>
    </head>
    <body>
    <img id="logo" src="../images/header_logo.png" alt="" width="140" height="70">
    <hr id="linetop">
        <div>
            <h2>従業員検索</h2>
            <input type="text" id="searchWord"><input type="button" value="検索" id="search"><br>
            <div id="result">
                <?php 
                    /*
                        ここに検索結果が表示される。
                        adminEmpSearch.jsがajax_emp_search.phpの結果を取得し、テーブルを作成する。
                    */
                ?>
            </div>
            <input type="hidden" value="<?php echo $_SESSION["me"]["group_id"]; ?>" id="groupId">
            <input type="hidden" value="<?php echo $_SESSION["token"]; ?>" id="token">
        </div>
            <span id="msg"></span>
        <div id="empList">
            <h2>出勤者一覧</h2>
            <table border="1px" id="empData" align="center">
                <thead>
                    <tr>
                        <th>従業員</th><th>出勤時間</th>
                        <?php
                            /*
                                ここに出勤した人一覧が表示される。
                                adminEmpList.jsがajax_emp_list.phpの結果を取得し、テーブルを作成する。
                            */
                         ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        
        <p><a href="./addEmployee/addEmployee.php">従業員新規登録</a> </p>
        <p><a href="../group/confGroup.php">グループ情報変更</a> </p>
        <p><a id="logoutBtn" href="#">ログアウト</a></p>

        <?php include_once("../include/footer.php"); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/JavaScript" src="./js/adminEmpList.js"></script>
        <script type="text/JavaScript" src="./js/adminEmpSearch.js"></script>
        <script type="text/JavaScript" src="../util/confLogout.js"></script>
    </body>
</html>