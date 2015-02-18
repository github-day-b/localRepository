<?php
/*
    @author: YukiKawasaki
    FileName: timesheetDB.php
    @create: 2014/06/17
    Remark: 勤務表をPDFで出力してダウンロード
*/
    require_once('../../util/util.php');
    require_once('../../database/timesheetDB.php');
    require_once('../../database/empinfoDB.php');
    include("../../MPDF/mpdf.php"); 
    session_start();

    if(!$_SESSION["me"]["admin"] || !isset($_SESSION["me"]["admin"])) {
        die("不正なアクセスです。");
    }
    $year  = util::h($_GET['year']); //年を取得
    $month = util::h($_GET['month']); //月を取得

    //年と月の範囲チェック
    if (($year > date('Y') || $year < 2010) || ($month < 1 || $month > 12)) {
        $year = date('Y');
        $month= date('m');
    }

    $groupName     = $_SESSION["me"]["groupName"];
    $empId       = util::h($_GET['empId']);//セッションから従業員IDの取得

    $nowYear     = date("Y");
    $nowDay      = date("j"); //現在の日を取得
    $countdate   = date("t", mktime(0, 0, 0, $month, 1, $year));
    $weekday     = array("日","月","火","水","木","金","土"); //曜日の配列作成
    $timesheetDB = new TimeSheetDB();
    $empinfoDB   = new EmpInfoDB();
    $empData     = $empinfoDB->detailEmployee($empId);
    $totalTime     = $timesheetDB->totalTime($empId, $year, $month);
    $totalOverTime = $timesheetDB->totalOverTime($empId, $year, $month);
    $token       = util::setToken();

//勤務表を生成して、変数に代入
ob_start(); 
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>勤務表</title>
</head>
<body>
    <p style="text-align: center">
        <?php echo $groupName;?><br />
        <?php echo $empData["empName"];?><br />
        勤務表 (<?php echo $year. '年 ' .$month .'月)' ;?>
    </p>
    <table id="timesheet_admin" border="1" style="margin:0 auto;">
    <tr>
        <th>日</th><th>曜日</th><th>出勤時間</th><th>退勤時間</th><th>休憩時間</th><th>実働時間</th><th>超過時間</th>
        <th>出勤場所</th><th>退勤場所</th><th>備考</th>
    </tr>
<?php
    for ($day = 1; $day <= $countdate; $day++) :
        $w = date("w", mktime(0, 0, 0, $month, $day, $year));
 
        $workDate = $year.'-'.$month.'-'.$day;//その日の日付

        //勤務表のレコードを一つ取得。
        $result = $timesheetDB->timeSheet($empId, $workDate);

        //出勤退勤時間が入っていない、または勤務時間がマイナスになるなら非表示にする。
        if ($result['endTime'] != null && $result['startTime'] && strpos($result["timeOut"], '-') !== 0 && $result["timeOut"] !== '00:00') {
            $workTime = $result["timeOut"];
        } else {
            $workTime = null;
        }

        //超過時間にマイナスが文字列に含まれるか
        if (strpos($result['over'], '-') === 0) {
            $result['over'] = null;//マイナスならNULLを代入して非表示にさせる
        }

        switch ($w) {
            case 0://日曜日
                $style = "color:red;";
                break;
            case 6://土曜日
                $style = "color:blue;";
                break;
            default :
                $style = "color:#333;";
        }

        //今日の日付に色指定
        if ($day == $nowDay && $month == date('n') && $year == date('Y')) {
            $style .= "background-color:#00BFFF;color:white;";
        }
?>

        <tr>
            <td class="day" style="<?php echo $style; ?>"><?php echo $day; ?></td>
            <td class="weekday" style="<?php echo $style; ?>"><?php echo $weekday[$w]; ?></td>
            <td class="startTime">
                <?php if(isset($result["startTime"])) {echo $result['startTime']; }?>
            </td>
            <td class="endTime">
                <?php if(isset($result["endTime"])) {echo $result['endTime']; }?>
            </td>
            <td class="intervalTime">
                <?php if(isset($result["intervalTime"])) {echo $result['intervalTime']; }?>
            </td>
            <td class="workTime" id="<?php echo $workDate. "workTime"; ?>"><?php echo $workTime; ?></td>
            <td class="overTime" id="<?php echo $workDate. "overTime"; ?>"><?php echo $result['over']; ?></td>
            <td class="startLoc"><?php if(isset($result["startLoc"])) echo $result['startLoc']; ?></td>
            <td class="endLoc"><?php if(isset($result["endLoc"])) echo $result['endLoc']; ?></td>
            <td class="remark">
                <?php if(isset($result["remark"])) {echo $result['remark'];} ?>
            </td>
        </tr> 
    <?php endfor; ?>
        </table>
        <div style="text-align: center">
            <p>合計時間: <b><span id="sumTime"><?php echo $totalTime; ?></span></b></p>
            <p>超過時間: <b><span id="totalOverTime"><?php echo $totalOverTime; ?></span></b></p>
        </div>
</body>
</html>
<?php 

//勤務表の内容を代入
$html = ob_get_contents(); 
ob_end_clean();


//PDF生成
$mpdf=new mPDF('ja', 'A4');
$mpdf->WriteHTML($html); 
$mpdf->Output($groupName."_".$empData["empName"]."_time_sheet_".$year."_".$month, 'D');
//EOF