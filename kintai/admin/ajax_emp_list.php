<?php
    /*
     *  @author YukiKawasaki
     *  FileName ajax_emp_list.php
     *  @create 2014/05/01
     *  Remark  出勤した人の一覧を返す。 
                2014/05/16
                sql操作をempinfoDBに追い出した。
    */

    session_start();
    require_once("../database/empinfoDB.php");
    require_once('../util/util.php');

    $groupId = util::h($_SESSION["me"]["group_id"]);
    $db = new EmpInfoDB();
    $emp = $db->empList($groupId);//従業員の出勤情報を取得
    $emp = json_encode($emp);

    //JSON送信
    header("Content-type: text/JavaScript; charset=UTF-8");
    echo $emp;
//EOF