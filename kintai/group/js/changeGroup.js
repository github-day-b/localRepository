/*
 *  @author YukiMori
 *  FileName changeGroup.js
 *  @create   2015/01/05
 *  Remain: 従業員情報変更をベースに作成
 *
*/
$(function() {
    //$.ajaxメソッドから帰ってくるjqXHRオブジェクトを利用して二重実行防止
    var jqxhr;
    //変更ボタンをクリック
    $("#change").click(function() {
        //@1エラーチェック
        if (errorChk()) {
            jqxhr = changeGroup();//@2変更処理をajaxで
        }else {
            ;//DoNothing
        }
    });

    //@1エラーチェック
    function errorChk() {
        if ($("#groupName").val() == "") {
            $("#msg").text("氏名が入力されてません。");
            return false;
        }else if(jqxhr){
            $("#msg").text("二度押しできません。串カツならマナー違反。");
            return false;
        }else if($("#groupName").val().length > 30) {//varchar(30)
            $("#msg").text("名前長くないですか?");
            return false;
        }else {
            ;//DoNothing
        }

        return confirm("変更しますか");
    }

    //@2更新処理
    function changeGroup() {
        return $.ajax({
            type: "POST",
            url: "ajax_changeGroup.php",
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
                        $("#msg").text("通信エラー");
                    }
        });
    }
    
    //@3送信するデータを設定
    function initData() {
        return {
                "groupId":     $("#groupId").val(),
                "groupName":   $("#groupName").val(),
                "groupLocation" : ($("#locationFlg").prop("checked") ? 1 : 0),
                "token":     $("#token").val()
        };
    }
});
//EOF