<?php
/*
    @author: YukiKawasaki
    FileName: empinfoDB.php
    @create: 2014/05/16
    Remark: 従業員関連のDB操作クラス。
            2014/05/19課題より従業員検索の条件で削除された従業員の情報も表示できるようにした。
            2015/01/05管理者の登録、グループ情報カラム追加
*/
 
require_once("dataaccess.php");
class EmpInfoDB 
{
    private $db;

    //@0コンストラクタ。データベース接続をする。
    public function __construct() {
        $this->db = new DataAccess();
    }

    //@1出勤した従業員の一覧を取得
    public function empList($groupid) {
        $sql = "SELECT S1.emp_id AS empId, emp_name AS empName, ".
        "TIME_FORMAT(start_time, '%H:%i') AS startTime, ".
        "TIME_FORMAT(end_time, '%H:%i') AS endTime ".
        "FROM emp_info S1 ".
        "LEFT OUTER JOIN time_sheet S2 ".
        "ON S1.emp_id = S2.emp_id ".
        "AND S2.work_date = '".date("Y-m-d")."' ".
        "WHERE S1.group_id = :groupid ".
        "AND S1.delete_flg <> true ".
        "AND S1.admin <> true ".
        "ORDER BY S1.emp_id ASC; ";

        $result = $this->db->fetchAll($sql, array(":groupid" => $groupid));

        return $result;
    }

    //@2従業員を検索
    public function empSearch($groupid,$name) {
        $sql = "SELECT emp_id AS empId, emp_name AS empName, delete_flg AS deleteFlg, admin FROM emp_info ".
                "WHERE group_id = :groupid ".
                "AND emp_name LIKE concat('%', :name, '%') ".
                "ORDER BY delete_flg, emp_id";
        $result = $this->db->fetchAll($sql, array(":groupid"=>$groupid,":name" => $name));

        return $result;
    }

    //@3従業員を登録
    public function addEmployee($name, $email, $groupID, $password, $startTime, $endTime) {
        $sql = "INSERT INTO emp_info". 
                "(emp_name, email, group_id, password, ". 
                "std_start_time, std_end_time, ". 
                "created, modified)". 
                "VALUES (:name, :email, :groupID, :password, ". 
                        ":startTime, :endTime,".
                        " now(), now())";

        $data = array(":name"      => $name, 
                      ":email"     => $email, 
                      ":groupID"   => $groupID,
                      ":password"  => $password, 
                      ":startTime" => $startTime, 
                      ":endTime"   => $endTime);

        return $this->db->execute($sql, $data);
    }

    //@4従業員の詳細情報を抽出
    public function detailEmployee($empId) {
        $sql = "SELECT emp_name AS empName, email, ". 
               "TIME_FORMAT(TIMEDIFF(TIMEDIFF(std_end_time, std_start_time), '1:00'), '%H:%i') AS diffStdTime, ". 
               "DATE_FORMAT(std_start_time, '%H:%i') AS startTime, ". 
               "DATE_FORMAT(std_end_time, '%H:%i') AS endTime ".
               "FROM emp_info ". 
               "WHERE emp_id = :emp_id ";
        $result = $this->db->fetchAll($sql, array(":emp_id" => $empId));

        return $result[0];
    }

    //@5従業員を削除
    public function deleteEmployee($empId) {
        $sql = "UPDATE emp_info SET ". 
        "delete_flg     = true ". 
        "WHERE emp_id   = :emp_id ".
        "AND delete_flg <> true";

        return $this->db->execute($sql, array(":emp_id" => $empId));
    }

    //@6従業員を変更
    public function changeEmployee($empId, $name, $email, $password, $startTime, $endTime, $pass) {
        $sql = "UPDATE emp_info ". 
               "SET emp_name       = :name, ". 
                   "email          = :email , ". 
                   "std_start_time = :startTime, ". 
                   "std_end_time   = :endTime, ". 
                   "modified       = now()";

        $data = Array( ":name"      => $name, 
               ":email"     => $email, 
               ":startTime" => $startTime, 
               ":endTime"   => $endTime, 
               ":emp_id"    => $empId);

        //もしパスワードがフォームに入っていたらSQLに書き加え
        if (!empty($pass)) {
            $sql         .= ", password = :password";
            $data        += Array(":password" => $pass);        }
        
        $sql .= " WHERE emp_id = :emp_id ". 
                " AND delete_flg <> true";

        return $this->db->execute($sql, $data);
    }

    ////@7email、グループIDを条件にユーザを一件だけ取り出す。
    public function fetchUser($groupid,$id) {
        $sql = "SELECT S1.emp_id,
                        S1.emp_name,
                        S1.password,
                        S1.group_id AS group_id,
                        S2.group_name AS groupName,
                        S2.location_flg AS location,
                        S1.email,
                        S1.admin
                FROM emp_info S1
                INNER JOIN group_info S2
                ON S1.group_id = S2.group_id
                WHERE S1.group_id = :groupid
                AND S1.email = :id
                AND S1.delete_flg <> true";
        $place = array(":id"=>$id , ":groupid"=>$groupid);
        return $this->db->fetchAll($sql, $place);
    }
    //@8管理者を登録
    public function addAdmin($name, $email, $password, $groupID) {
        $sql = "INSERT INTO emp_info". 
                "(emp_name, email, group_id, password, ". 
                "created, modified, admin)". 
                "VALUES (:name, :email, :groupID, :password, ".
                        " now(), now(), true)";

        $data = array(":name"      => $name, 
                      ":email"     => $email, 
                      ":groupID"   => $groupID,
                      ":password"  => $password);

        return $this->db->execute($sql, $data);
    }
    ////@9グループID、管理者フラグ、emailで管理者のデータを取り出す。
    public function fetchAdmin($email, $groupID) {
        $sql = "SELECT emp_name FROM emp_info 
                WHERE email = :email 
                AND group_id = :group_id
                AND admin = true
                AND delete_flg <> true";
        $place = array(
                        ":email"=>$email,
                        ":group_id"=>$groupID
                    );
        return $this->db->fetchAll($sql, $place);
    }
}
//EOF