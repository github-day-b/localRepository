<?php
/*
 *  @author  YukiKawasaki
 *  FileName empTimeSheet.php
 *  @create  2014/05/09
 *  Remark    出勤した人の勤務時間を変更できる。
              05/09真島君からもらって改造
              05/13 年月の範囲チェック (真島)
              05/14 SQLの引数の入れ子を改善(川崎)
 */
session_start();
require_once('../../database/timesheetDB.php');
require_once('../../util/util.php');

if(!isset($_SESSION["me"]["admin"])) {
    header("location: ../index.php");
}

$year      = util::h($_GET['year']); //年を取得
$month     = util::h($_GET['month']); //月を取得
//年と月の範囲チェック
if (($year > date('Y') || $year < 2010) || ($month < 1 || $month > 12)) {
    $year = date('Y');
    $month= date('m');
}

$empId     = $_SESSION['me']['emp_id'];//セッションから従業員IDの取得
$empName   = $_SESSION['me']['emp_name'];
$nowYear   = date("Y");
$nowDay    = date("j");
$countDate = date("t", mktime(0, 0, 0, $month, 1, $year));//日数を取得
$weekDay   = array("日","月","火","水","木","金","土"); //曜日の配列作成
$timesheetDB        = new TimeSheetDB();
$totalTime     = $timesheetDB->totalTime($empId, $year, $month);
$totalOverTime = $timesheetDB->totalOverTime($empId, $year, $month);

$_SESSION["token"] = util::setToken();//ワンタイムトークン
$deviceType = util::getDeviceType($_SERVER['HTTP_USER_AGENT']);
?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
    <?php
    define("title" ,"タイムシート");
    define("cssPath","../../");
    include_once("../../include/header.php");
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="../js/updateRemark.js" type="text/javascript"></script>
    <script src="../../util/pageTop.js" type="text/javascript"></script>
</head>
<body>
<img id="logo" src="../../images/header_logo.png" alt="" width="140" height="70">
<hr id="linetop">
<p id="pageTop">↑TOP</p>
    <img src="../../images/human.jpg" alt="" width="42" height="40"><br />
    [<?php echo $_SESSION['me']['groupName'];?>]<br />
    [<?php echo util::h($_SESSION['me']['emp_name']);?>]<br />
    勤務表 (<?php echo $year. '年 ' .$month .'月)' ;?><br />
 
    <a  class="back" href="../user.php">TOP画面へ</a><br /><br />
    <form name="timesheet_ref" action="./timesheet.php" method="GET">
     <div id="year_month"> 
        <select name="year">
            <?php
            for($y = 2010; $y <= date("Y"); $y++):?>
                <?php if($y == $year) :?>
                    <option value="<?php echo $y;?>" selected><?php echo $y;?></option>
                <?php else :?>
                    <option value="<?php echo $y;?>"><?php echo $y;?></option>
                <?php endif;?>
            <?php endfor;?>
            </select>年
            <select name="month">
            <?php for($m = 1; $m <= 12; $m++) : ?>
                 <?php if($m == $month) :?>
                    <option value="<?php echo $m;?>" selected><?php echo $m;?></option>
                <?php else :?>
                    <option value="<?php echo $m;?>"><?php echo $m;?></option>
                <?php endif;?> 
            <?php endfor; ?>
        </select>月
        <input type="submit" id="reference" value="参照">
     </form>
     </div><!-- year_month -->
    <div id="updateDiv">
         <input type="button" id="updateTop" value="更新"><br />
         <input type="hidden" value="<?php echo $month; ?>">
         <input type="hidden" value="<?php echo $year; ?>">
         <input type="hidden" id="empId" value = "<?php echo $empId;?>"> 
         <input type="hidden" id= "token" value="<?php echo $_SESSION["token"]; ?>">
        <?php if($deviceType == "PC"){ ?>
            <a  class="pdf" href="./download_pdf.php?year=<?php echo $year; ?>&month=<?php echo $month; ?>&empId=<?php echo $empId; ?>">pdfでダウンロード</a>
        <?php } ?>
     </div><!--updateDiv -->

    <table id="timesheet">

        <?php if($deviceType == "PC") { ?>
            <tr>
                <th>日</th><th>曜日</th><th>出勤時間</th><th>退勤時間</th><th>休憩時間</th><th>実働時間</th><th>超過時間</th>
                <th>出勤場所</th><th>退勤場所</th><th>備考</th>
            </tr>
        <?php }else{ ?>
            <tr class="ttl">
                <th colspan="5">日付</th>
            </tr>
            <tr class="ttl">
                <th class="subttl">出勤</th>
                <th class="subttl">退勤</th>
                <th class="subttl">休憩</th>
                <th class="subttl">実働</th>
                <th class="subttl">超過</th>
            </tr>
            <tr class="ttl">
                <th colspan="5">出勤場所</th>
            </tr>
            <tr class="ttl">
                <th colspan="5">退勤場所</th>
            </tr>
            <tr class="ttl">
                <th colspan="5" class="lastTtl">備考</th>
            </tr>
        <?php } ?>

<?php
    for ($day = 1; $day <= $countDate; $day++) :
        $w = date("w", mktime(0, 0, 0, $month, $day, $year));
         
        $workDate = $year.'-'.$month.'-'.$day;//その日の日付

        //勤務表のレコードを一つ取得。
        $result = $timesheetDB->timeSheet($empId, $workDate);

        //出勤退勤時間が入っていない、または勤務時間がマイナスになるなら合計時間を非表示にする。
        if ($result['endTime'] != null && $result['startTime'] && strpos($result["timeOut"], '-') !== 0 && $result["timeOut"] !== '00:00') {
            $workTime = $result["timeOut"];
        } else {
            $workTime = null;
        }

        //マイナスが文字列に含まれるか
        if (strpos($result['over'], '-') === 0) {
            $result['over'] = null;//マイナスならNULLを代入して非表示にさせる
        }

        //平日と休日の色分け
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
        <?php if($deviceType == "PC") { ?>
            <tbody>
            <tr>
                <td class="day" style="<?php echo $style; ?>"><?php echo $day; ?></td>
                <td class="weekday" style="<?php echo $style; ?>"><?php echo $weekDay[$w]; ?></td>
                <td class="startTime">
                    <?php if(isset($result["startTime"])) {echo $result['startTime']; }?>
                </td>
                <td class="endTime">
                    <?php if(isset($result["endTime"])) {echo $result['endTime']; }?>
                </td>
                <td class="intervalTime">
                    <?php if(isset($result["intervalTime"])) {echo $result['intervalTime']; }?>
                </td>
                <td class="workTime"><?php echo $workTime; ?></td>
                <td class="overTime"><?php if(isset($result["over"])) echo $result['over']; ?></td>
                <td class="startLoc"><?php if(isset($result["startLoc"])) echo $result['startLoc']; ?></td>
                <td class="endLoc"><?php if(isset($result["endLoc"])) echo $result['endLoc']; ?></td>
                <td class="remark"><input type="text" value="<?php if(isset($result["remark"])) echo $result['remark']; ?>" id="<?php echo $workDate; ?>"><input type="hidden" class="workDate" value="<?php echo $workDate; ?>"></td>
            </tr>
            </tbody>

        <?php }else{ ?>
            <tr>
                <td class="day" colspan="5" style="<?php echo $style; ?>"><?php echo $day; ?><?php echo "(".$weekDay[$w].")"; ?></td>
            </tr>
            <tbody>
            <tr>
                <td class="startTime"><?php if(isset($result["startTime"])) {echo $result['startTime']; }?></td>
                <td class="endTime"><?php if(isset($result["endTime"])) {echo $result['endTime']; }?></td>
                <td class="intervalTime"><?php if(isset($result["intervalTime"])) {echo $result['intervalTime']; }?></td>
                <td class="workTime"><?php echo $workTime; ?></td>
                <td class="overTime"><?php if(isset($result["over"])) echo $result['over']; ?></td>
            </tr>
            <tr>
                <td class="startLoc" colspan="5"><?php if(isset($result["startLoc"])) echo $result['startLoc']; ?></td>
            </tr>
            <tr>
                <td class="endLoc" colspan="5"><?php if(isset($result["endLoc"])) echo $result['endLoc']; ?></td>
            </tr>
            <tr>
                <td class="remark" colspan="5">
                    <input type="text" value="<?php if(isset($result["remark"])) echo $result['remark']; ?>" id="<?php echo $workDate; ?>">
                    <input type="hidden" class="workDate" value="<?php echo $workDate; ?>">
                </td>
            </tr>
            </tbody>
        <?php } ?>
    <?php endfor; ?>
    </table>
        <p>合計時間: <b><span id="sumTime"><?php echo $totalTime; ?></span></b></p>
        <p>超過時間: <b><span id="totalOverTime"><?php echo $totalOverTime; ?></span></b></p>

    <?php include_once("../../include/footer.php"); ?>
</body>
</html>