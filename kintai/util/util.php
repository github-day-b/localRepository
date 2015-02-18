<?php
    /*
     *   @author YukiKawasaki, RyoMashima
     *   FileName util.php
     *   @create 2014/05/01
     *   Remark 便利そうな関数寄せ集め。
     *          05/07 真島君のfunction.phpとマージ。使えそうなものをこっちへ。
     */

class util {
    //PDOインスタンスを返す。DB環境に応じてコメント使い分け。
    public static function getPDOInstance() 
    {
        //ローカルDB設定
        $conf = json_decode(file_get_contents(dirname(__FILE__)."/../conf/localDB.json"), true);

        return new PDO($conf["hostName"].$conf["dbName"], $conf["userName"], $conf["pass"]);
    }

    //エスケープ処理
    public static function h($str) 
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    //Time型にする
    public static function makeTime($hour, $min) 
    {
        return $hour. ":". $min;
    }

    //パスワードハッシュ化
    public static function makePass($rawPassword) 
    {
        $salt = "siosalt010jf94923013n23302jafkafjoaifr94r924rjwo9fae4ru34rf3f3394";
        return sha1($rawPassword.$salt);
    }

    //CSRF対策
    public static function setToken() {
        $token = sha1(uniqid(mt_rand(), true));//sha1で暗号化
        return $token;
    }

    //PC、SP、アプリ判定
    public static function getDeviceType($ua){
        $type = "PC";
        if(strpos($ua, 'kintaiApp') == true) {
            $type = "APP";
        }elseif(strpos($ua, 'Android') !== false || strpos($ua, 'iPhone') !== false){
            $type = "SP";
        }
        return $type;
    }
}
//EOF