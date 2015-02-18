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

    //CSRF対策
    if (!isset($_SESSION["me"]) || !$_SESSION["me"]["admin"] || 
        !isset($_SESSION["token"]) || $_SESSION["token"] !== util::h($_POST["token"])) {
        die("不正なアクセスです。");
    }else {
        ;//DoNothing
    }

    $empId        = util::h($_POST["empId"]);
    $startTime    = util::h($_POST["startTime"]);
    $workDate     = util::h($_POST["workDate"]);
    $endTime      = util::h($_POST["endTime"]);
    $intervalTime = util::h($_POST["intervalTime"]);
    $remark       = util::h($_POST["remark"]);
    $db           = new TimeSheetDB();

    //エラーチェック。入力されたものが時間形式と一致しているか判定する。
    if(!preg_match("/^((2[0-3])|([0-1]?[0-9])):([0-5][0-9])$|^((2[0-3])|([0-1][0-9]))([0-5][0-9])$/", $startTime) && !empty($startTime)) {
        die("始業時間がおかしいです。");
    }else if(!preg_match("/^[0-2]?\d{1,2}:[0-5]\d$|^[0-2]?\d{1,2}[0-5]\d$/", $endTime) && !empty($endTime)) {
        die("終業時間がおかしいです。");
    }else if(!preg_match("/^((2[0-3])|([0-1]?[0-9])):([0-5][0-9])$|^((2[0-3])|([0-1]?[0-9]))([0-5][0-9])$/", $intervalTime) && !empty($intervalTime)) {
        die("休憩時間がおかしいです。");
    }

    //もし":"がなかったら下から二けた目で分ける ex) 1030 → 10:30
    if(strpos($startTime, ":") == false) {
        $startTime = wordwrap($startTime, 2, ":", true);
    }

    if(strpos($intervalTime, ":") == false) {
        $intervalTime = wordwrap($intervalTime, 2, ":", true);
    }

    if(strpos($endTime, ":") == false) {
        $endTime = wordwrap($endTime, 2, ":", true);
    }
	//成功true 失敗false
	echo $db->updateEmpTimeSheet($startTime, $endTime, $intervalTime, $remark, $empId, $workDate);//@1勤務表の更新
//EOF