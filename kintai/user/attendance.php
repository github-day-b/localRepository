<?php
/*
 * @author  Ryo Mashima
 * FileName attendance.php
 * @create  2014/05/07
 * Remark   勤務状況を紹介
 *            2014/05/13 余分な変数を無くした(真島)
             2014/05/16
            sql操作をtimesheetDBに追い出した。
*/
    require_once("../database/timesheetDB.php");
    $db    = new TimeSheetDB();
    $empId = $_SESSION['me']['emp_id'];

    $workDate = date('Y-m-d');//現在日付（xxxx-xx-xx）の取得

    
    //出勤されているか確認
    $flag = $db->checkTimeSheet($workDate, array(":emp_id"=>$empId, ":work_date"=>$workDate), TimeSheetDB::START_TIME);

    $_SESSION['me']['start'] = $flag;

    //退勤されているか確認
    $flag = $db->checkTimeSheet($workDate, array(":emp_id"=>$empId, ":work_date"=>$workDate), TimeSheetDB::END_TIME);
    
    $_SESSION['me']['end'] = $flag;
//EOF