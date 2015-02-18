<?php
/*
    author: YukiKawasaki
    FileName: dataaccess.php
    @create: 2014/05/15
    remark: DB接続クラス。
*/

class DataAccess 
{
    private $dbh;
    private $conf;

    //コンストラクタ、設計ファイル読み込み
    public function __construct() {
        //PDO接続。DB環境に応じてコメント使い分け。
        //ローカルDB設定
        // $this->conf = json_decode(file_get_contents(dirname(__FILE__)."/../conf/localDB.json"), true);
        //1番テスト用DB設定ファイル
        $this->conf = json_decode(file_get_contents(dirname(__FILE__)."/../conf/test01DB.json"), true);
        //2番テスト用DB設定ファイル
        //$this->conf = json_decode(file_get_contents(dirname(__FILE__)."/../conf/test02DB.json"), true);
        //本番環境DB設定ファイル
        //$this->conf = json_decode(file_get_contents(dirname(__FILE__)."/../conf/DB.json"), true);
    }

    //DB接続
    public function PDOOpen() 
    {
        $this->dbh = new PDO($this->conf["hostName"].$this->conf["dbName"], $this->conf["userName"], $this->conf["pass"]);
    }

    //DB切断
    public function PDOClose() {
        $dbh = null;
    }

    //データベースハンドラのgetter、ユーザ変数を使うときに
    public function getdbh() {
        return $this->dbh;
    }

    //PDO::execute
    public function execute($sql, $place) {
        try {
            $this->PDOOpen();
            $statement = $this->dbh->prepare($sql);
            $result = $statement->execute($place);
            $this->PDOClose();
        }catch(PDOException $e) {
            die(false);
        }

        return $result;
    }

    //SELECTで抽出したものすべてを連想配列で返す。
    public function fetchAll($sql, $place) {
        try {
            $this->PDOOpen();
            $statement = $this->dbh->prepare($sql);
            $statement->execute($place);
            $this->PDOClose();
        }catch(PDOException $e) {
            $this->PDOClose();
            echo false;
            die(false);
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}
//EOF
