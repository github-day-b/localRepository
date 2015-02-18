/*
 *  @author YukiKawasaki
 *  FileName changeEmployee.js
 *  @create   2014/05/01
 *  Remain: エラーチェックしてAjaxで変更させる
 *          2014/05/08多重起動防止追加
 *          05/13課題一覧表の入力チェック項目に基づいて入力チェック。
*/
$(function() {
    //$.ajaxメソッドから帰ってくるjqXHRオブジェクトを利用して二重実行防止
    var jqxhr;
    //変更ボタンをクリック
    $("#change").click(function() {
        //@1エラーチェック
        if (errorChk()) {
            jqxhr = changeEmp();//@2変更処理をajaxで
        }else {
            ;//DoNothing
        }
    });

    //@1エラーチェック
    function errorChk() {
        if ($("#name").val() == "") {
            $("#msg").text("氏名が入力されてません。");
            return false;
        }else if ($("#email").val() == "") {
            $("#msg").text("メールアドレスが入力されてません。");
            return false;
        }else if(jqxhr){
            $("#msg").text("二度押しできません。串カツならマナー違反。");
            return false;
        }else if($("#name").val().length > 30) {//varchar(30)
            $("#msg").text("名前長くないですか?");
            return false;
        }else if($("#email").val().length > 255) {//varchar(255)
            $("#msg").text("email長くないですか?");
            return false;
        }else if($("#pass").val().length > 100) {//varchar(100)
            $("#msg").text("パスワード長くないですか?");
            return false;
        }else if(!$("#email").val().match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) {
            $("#msg").text("それメールアドレスですか?");//メールアドレスの形式
            return false;
        }else {
            ;//DoNothing
        }

        return confirm("変更しますか");
    }

    //@2登録処理
    function changeEmp() {
        return $.ajax({
            type: "POST",
            url: "ajax_changeEmployee.php",
            data: (function() {
                        return initData()//@3送信するデータを設定
                    })(),
            success: function(res) {//成功true 失敗false
                        if(res == 1) {
                            $("#msg").text("変更しました。");
                        }else {
                            $("#msg").text(res);
                        }
                    },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#error").text("通信エラー");
                    }
        });
    }
    
    //@3送信するデータを設定
    function initData() {
        return {
                "empId":        $("#empId").val(), 
                "name":      $("#name").val(),
                "email":     $("#email").val(),
                "pass":      $("#pass").val(),
                "startHour": $("#startHour").val(),
                "startMin":  $("#startMin").val(),
                "endHour":   $("#endHour").val(),
                "endMin":    $("#endMin").val(),
                "token":     $("#token").val()
        };
    }
});
//EOF