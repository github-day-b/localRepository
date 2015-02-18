<?php
/*
        @author:  YukiKawasaki
        FileName: ajax_update_timesheet.php
        @create:  2014/05/13
        Remark:   勤務表を更新する。
                  2014/05/13
                  エラーチェック
                  2014/05/16
                  sql操作をtimesheetDBに追い出した。
    */
    require_once("../../util/util.php");
    require_once("../../database/timesheetDB.php");
    session_start();
    //複数タブで別のユーザがログインするとタブ間でセッション情報が上書きされる。
    //管理者以外は操作不可。
    if(!isset($_SESSION["me"]) || is_null($_SESSION["me"]["admin"]) || $_SESSION["me"]["admin"] || !isset($_SESSION["token"]) || $_SESSION["token"] !== util::h($_POST["token"])) {
        die("不正なログインです");
    }

    //POSTで受け取ったものをエスケープ処理
    $empId        = util::h($_POST["empId"]);
    $workDate     = util::h($_POST["workDate"]);
    $remark       = util::h($_POST["remark"]);

    $db = new TimeSheetDB();

    //成功true 失敗false
    echo $db->updateTimeSheet($empId, $workDate, $remark);//@7勤務表を更新
//EOF