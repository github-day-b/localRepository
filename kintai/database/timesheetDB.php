<?php
/*
    @author: YukiKawasaki
    FileName: timesheetDB.php
    @create: 2014/05/15
    Remark: 勤務表関連のDB操作クラス。
            2014/05/19課題より、挿入されていないレコードでも編集できるようにした。
                      出勤時にinsert-duplicateでレコードがなくても挿入できるようにした。
*/
require_once("dataaccess.php");
class TimeSheetDB
{
    const START_TIME = true;//勤務開始時間と比較
    const END_TIME   = false;//勤務終了時間と比較
    private $dbh;
    private $db;

    //@0コンストラクタ。DB接続。
    public function __construct() {
        $this->db = new DataAccess();
        $this->db->PDOOpen();
        $this->dbh = $this->db->getdbh();
    }

    //@1一日の勤務表を更新する。
    public function updateEmpTimeSheet($startTime, $endTime, $intervalTime, $remark, $empId, $workDate) {

      if($startTime == "") {
        $startTime = null;
      }
      if($endTime == "") {
        $endTime   = null;
      }
      if($intervalTime == "") {
        $intervalTime = null;
      }
       $sql = "INSERT INTO time_sheet (emp_id, work_date, start_time, interval_time, end_time, remark) "
            . "VALUES (:empId, :workDate, :startTime, :intervalTime, :endTime, :remark) "
            . "ON DUPLICATE KEY UPDATE start_time = :startTime, interval_time = :intervalTime, end_time = :endTime, remark = :remark";
        $data = array(":startTime"    => $startTime,
                      ":endTime"      => $endTime,
                      ":intervalTime" => $intervalTime,
                      ':remark'       => $remark,
                      ':empId'        => $empId, 
                      ':workDate'     => $workDate);

       return $this->db->execute($sql, $data);
    }

    //@2$workDateの勤務表を取得。
    public function timeSheet($empId, $workDate) {
        //関数のいり子構造が複雑化したため、MySQLのユーザ定義関数を利用して、三つのSQLに分割。
        //定時の勤務時間を計算しておきユーザ変数@std_time_outに代入
        $setStdTimeOutSql    =  "select TIME_TO_SEC(TIMEDIFF(TIMEDIFF(std_end_time, std_start_time), '1:00')) ".
                                "into @std_time_out ".
                                "from emp_info ".
                                "where emp_id = :emp_id ".
                                "limit 1";
        $statement = $this->dbh->prepare($setStdTimeOutSql);
        $statement->execute(array(":emp_id"=>$empId));

        //勤務時間を計算しておきユーザ変数@time_outに代入。
        $setTimeOutSql = "select TIME_TO_SEC(IF(end_time > start_time, TIMEDIFF(TIMEDIFF(end_time, start_time), interval_time), null)) ".
                         "into @time_out ".
                         "from time_sheet S1 ".
                         "inner join emp_info S2 ".
                         "on S1.emp_id     = S2.emp_id ".
                         "where S1.emp_id  = :emp_id ".
                         "and S1.work_date = :work_date ".
                         "limit 1";
        $statement = $this->dbh->prepare($setTimeOutSql);
        $statement->execute(array(":emp_id"=>$empId, ":work_date"=>$workDate));

        //超過時間を計算しておきユーザ変数@over_timeに代入。
        $setOverTimeSql = "SELECT IF(@std_time_out < @time_out, @time_out - @std_time_out, null) ". 
                          "INTO @over_time ".
                          "FROM time_sheet S1 ".
                          "INNER JOIN emp_info S2 ".
                          "ON S1.emp_id     = S2.emp_id ".
                          "WHERE S1.emp_id  = :emp_id ".
                          "AND S1.work_date = :work_date ".
                          "LIMIT 1";
        $statement = $this->dbh->prepare($setOverTimeSql);
        $statement->execute(array(":emp_id"=>$empId, ":work_date"=>$workDate));

        //これまでのユーザ変数を利用して表示
        $sql = "select  time_format(start_time, '%H:%i') as startTime, ".
                       "time_format(end_time, '%H:%i') as endTime, ".
                       "time_format(interval_time, '%H:%i') as intervalTime, ".
                       "time_format(sec_to_time(@over_time), '%H:%i') as over, ".
                       "time_format(sec_to_time(@time_out), '%H:%i') as timeOut, ".
                       "S1.start_location as startLoc, ".
                       "S1.end_location as endLoc, ".
                       "remark ".
               "from time_sheet S1 ".
               "inner join emp_info S2 ".
               "on S1.emp_id     = S2.emp_id ".
               "where S1.emp_id  = :emp_id ".
               "and S1.work_date = :work_date ".
               "limit 1";
               
        $statement = $this->dbh->prepare($sql);
        
        $statement->execute(array(":emp_id"=>$empId, ":work_date"=>$workDate));
        
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $dbh = null;
        return $result;
    }

    //@3指定月の合計実働時間を計算
    public function totalTime($empId, $year, $month) {
        //指定月の合計実働時間を計算
        $sql = "select time_format(sec_to_time(sum(time_to_sec(if(timediff(timediff(end_time, start_time), interval_time) > sec_to_time(0), ". 
                                                                  "timediff(timediff(end_time, start_time), interval_time),". 
                                                                  " '0:00')))), '%H:%i') as TOTAL from time_sheet ".
               "where (work_date between '". date($year."-".$month."-01")."'".
               " and ".
               "'".date($year."-".$month."-t")."')".
               " and (emp_id = :emp_id) ";
        
        $result = $this->db->fetchAll($sql, array(":emp_id"=>$empId));

        if(isset($result[0]['TOTAL'])) {
          return $result[0]['TOTAL'];
        }else {
          return "00:00";
        }
    }

    //@10超過時間の合計を計算
    public function totalOverTime($empId, $year, $month) {
        $sql = "select time_format(".
                          "sec_to_time(".
                            "sum(".
                              "if(time_to_sec(".
                                "timediff(timediff(timediff(end_time, start_time), interval_time), ".
                                  "timediff(timediff(std_end_time, std_start_time), '01:00')".
                                ")) > 0, ".
                                  "time_to_sec(".
                                "timediff(timediff(timediff(end_time, start_time), interval_time),". 
                                  "timediff(timediff(std_end_time, std_start_time), '01:00')".
                                ")), 0)".
                              
                            ")".
                          "), ".
                      "'%H:%i') as totalover ".
               "from emp_info S1 ".
               "inner join time_sheet S2 ".
               "on S1.emp_id = S2.emp_id ".
               "where S1.emp_id = :emp_id ".
               "and (work_date between '". date($year. "-". $month. "-01")."' and '".date($year."-".$month."-t")."')";
        $result = $this->db->fetchAll($sql, array(":emp_id"=>$empId));
        if(isset($result[0]['totalover'])) {
          return $result[0]['totalover'];
        }else {
          return "00:00";
        }
    }

    //@4引数の日付に勤務情報があるかどうかを確認
    public function checkTimeSheet($workDate, $place, $Time) {
        $sql  = "SELECT * FROM time_sheet WHERE emp_id = :emp_id AND work_date = :work_date";
        if ($Time) {
             $sql .= " AND start_time ";
        }else {
            $sql .= " AND end_time ";
        }
        $sql .= "is NULL LIMIT 1";

        //PHP5.3ではempty()の中に式は入れられない。
        $result = $this->db->fetchAll($sql, $place);

        return empty($result);
    }

    //@5その月に勤務表レコードがあるか確認する。
    public function checkMonthTimeSheet($monthFirst, $monthLast, $empId) {
        $sql = "select count(*) from time_sheet where work_date between '". 
                ":monthFirst and :monthLast ". 
                " and emp_id = :empId";
        $place = array(":monthFirst" => $monthFirst, 
                       ":monthLast"  => $monthLast, 
                       ":empId"      => $empId);

        //PHP5.3ではempty()の中に式は入れられない。
        $result = $this->db->fetchAll($sql, $place);

        return empty($result);
    }

    //@6一日分の勤務表を作成する。
    public function makeTimeSheet($empId, $workDate) {
        $sql = "insert into time_sheet (emp_id, work_date) values (:emp_id, :work_date)";
        $place = array(":emp_id" => $empId, ":work_date" => $workDate);

        return $this->db->execute($sql, $place);
    }

    //@7従業員の勤務表を更新。
    public function updateTimeSheet($empId, $workDate, $remark) {
       $sql = "INSERT INTO time_sheet (emp_id, work_date, remark) "
            . "VALUES (:empId, :workDate, :remark) "
            . "ON DUPLICATE KEY UPDATE remark = :remark";

        $place = array(':remark'       => $remark,
                       ':empId'        => $empId, 
                       ':workDate'     => $workDate);

        return $this->db->execute($sql, $place);
    }

    //@8出勤処理
    public function workIn($empId,$location) {
        //出勤時間が定時より早ければ定時の時刻で登録される。
        try {
            $sql = "select if(:now_time < std_start_time, std_start_time, :now_time) "
             ."into @start from emp_info "
             ."where emp_id = :emp_id ";

            $statement = $this->dbh->prepare($sql);
            $statement->execute(array(':emp_id'=>$empId, 
                                ':now_time'=>date('G:i')));

            $sql = "INSERT INTO time_sheet (emp_id, work_date, start_time, start_location, start_stamp) ".
                   "VALUES (:empId, :workDate, @start, :location, :startStamp) ".
                   "ON DUPLICATE KEY UPDATE start_time = @start, start_stamp = :startStamp, start_location = :location ";

            $statement = $this->dbh->prepare($sql);
            $result = $statement->execute(array(':empId'      => $empId, 
                                                ':workDate'   => date('Y/m/d'),
                                                ':location'   => $location,
                                                ':startStamp' => date('G:i')));
            
            $dbh = null;

        }catch(PDOException $e) {
            die($e->getMessage);
        }

            return $result;
    }

    //@9退勤処理
    public function workOut($empId,$location) {
        //データベースの退勤時間を更新(初期値はNULL)
        $sql  = "UPDATE time_sheet ".
                "SET end_time = :endTime, end_stamp = :endStamp, end_location = :location, ".
                "interval_time = '01:00',  interval_stamp = '01:00' ".
                "WHERE work_date = :work_date ". 
                "AND   emp_id    = :emp_id";
        
        return $this->db->execute($sql, array(':emp_id'  => $empId,
                                              ':work_date' => date('Y/m/d'),
                                              ':endTime'   => date('G:i'), 
                                              ':endStamp'  => date('G:i'),
                                              ':location'  => $location));
    }
}
//EOF