<?php
/*
    @author: YukiKawasaki
    FileName: groupinfoDB.php
    @create: 2015/01/05
    Remark: グループ関連のDB操作クラス。
 */
 
require_once("dataaccess.php");
class GroupInfoDB 
{
    private $db;

    //@0コンストラクタ。データベース接続をする。
    public function __construct() {
        $this->db = new DataAccess();
    }

    //@1IDをもとにデータを取り出す
    public function getGroup($groupID) {
        $sql = "SELECT group_id, group_name, location_flg, created, modified
                FROM group_info
                WHERE group_id = :groupID ";
        $result = $this->db->fetchAll($sql, array(":groupID" => $groupID));

        return $result;
    }

    //@3グループを登録
    public function addGroup($id, $name, $location) {
        $sql = "INSERT INTO group_info". 
                "(group_id, group_name, location_flg, created, modified)".
                "VALUES (:groupID, :groupName, :groupLocation, now(), now())";

        return $this->db->execute($sql, array(":groupID" => $id, ":groupName" => $name, ":groupLocation" => $location));
    }

    //@4グループ情報を更新
    public function changeGroup($id, $name, $location){
        $sql = "UPDATE group_info
                SET group_name = :groupName,
                    location_flg = :groupLocation
                WHERE group_id = :groupID ";

        return $this->db->execute($sql, array(":groupName" => $name, ":groupLocation" => $location, ":groupID" => $id));
    }
}