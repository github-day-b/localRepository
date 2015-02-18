<?php

/*
 * @author  Ryo Mashima
 * FileName /user/makeTimeSheet.php
 * @create  2014/05/07
 * Remark   1か月分の勤務表作成関数
            2014/05/16
            sql操作をtimesheetDBに追い出した。
*/
require_once("../database/timesheetDB.php");

$empId = $_SESSION['me']['emp_id'];
$nowYear  = date('Y');
$nowMonth = date('m');
$lastDay  = date('t');
$monthFirst = $nowYear.'-'.$nowMonth.'-01';
$monthLast = $nowYear.'-'.$nowMonth.'-'.$lastDay;
$db = new TimeSheetDB();

//もし当月分の勤務表がなければ作成する。
if ($db->checkMonthTimeSheet($monthFirst, $monthLast, $empId)) {
    for($i = 1; $i <= $lastDay; $i++) {
        $workDate = $nowYear.'-'.$nowMonth.'-'.$i;
        $db->makeTimeSheet($empId, $workDate);//@6勤務表を作成
    }
} else {
    return ;//既に勤務表作成済み
}
//EOF