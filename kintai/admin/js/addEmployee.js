/*
 *  @author YukiKawasaki
 *  FileName addEmployee.js
 *  @create   2014/04/24
 *  Remain エラーチェックしてAjaxで登録させる
 *         05/07 CSRF用のトークンを設定。
 *         05/07CSRF用トークンを追加
           05/13課題一覧表の入力チェック項目に基づいて入力チェック。
*/
$(function() {
    //$.ajaxメソッドから帰ってくるjqXHRオブジェクトを利用して二重実行防止
    var jqxhr;

    //登録ボタンをクリックしてエラーチェック通ったら登録処理
    $("#submit").click(function() {
        //@1エラーチェック
        if (errorChk()) {
            jqxhr = addEmp();//@2登録処理
        }else {
            ;//DoNothing
        }
    });

    //@2登録処理
    function addEmp() {
        return $.ajax({
            type: "POST",
            url: "ajax_addEmployee.php",
            data: (function(){
                        return initData();//@3送信するデータを設定
                    })(),
            success: function(res) {//成功true 失敗false
                        if(res === "1") {
                            $("#msg").text("登録しました。");
                        }else {
                            $("#msg").text("登録できませんでした。");
                        }
                    },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#error").text("通信エラー");
                    }
        });
    }

    //@1エラーチェック
    function errorChk() {
        if($("#name").val() == "") {
            $("#msg").text("氏名が入力されてません。");
            return false;
        }else if($("#email").val() == "") {
            $("#msg").text("メールアドレスが入力されてません。");
            return false;
        }else if($("#pass1").val() == "") {
            $("#msg").text("パスワードが入力されてません。");
            return false;
        }else  if($("#name").val().length > 30) {//varchar(30)
            $("#msg").text("名前長くないですか?");
            return false;
        }else if($("#email").val().length > 255) {//varchar(255)
            $("#msg").text("email長くないですか?");
            return false;
        }else if($("#pass1").val().length > 100) {//varchar(100)
            $("#msg").text("パスワード長くないですか?");
            return false;
        }else if(!$("#email").val().match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) {
            $("#msg").text("それメールアドレスですか?");//メールアドレスの形式
            return false;
        } else if(jqxhr) {
            $("#msg").text("二度押しできません。串カツならマナー違反。");
            return false;
        }else {
            ;//DoNothing
        }

        return confirm("登録しますか");
    }
    
    //@3送信するデータを設定
    function initData() {
        return {
                "name":      $("#name").val(),
                "email":     $("#email").val(),
                "pass":      $("#pass1").val(),
                "startHour": $("#startHour").val(),
                "startMin":  $("#startMin").val(),
                "endHour":   $("#endHour").val(),
                "endMin":    $("#endMin").val(),
                "token":     $("#token").val()
        };
    }
});
//EOF