/*
 *  @author YukiKawasaki
 *  FileName changeEmployee.js
 *  @create   2014/05/02
 *  Remain: エラーチェックしてAjaxで削除させる
 *          2014/05/08多重起動防止追加
*/
$(function() {
    //$.ajaxメソッドから帰ってくるjqXHRオブジェクトを利用して二重実行防止
    var jqxhr;

    //削除ボタンをクリック
    $("#del").click(function() {
        if (confirm("本当に削除しますか?") && !jqxhr) {
                jqxhr = deleteEmp();//@1削除処理
        }else if(jqxhr){
            $("#error").text("二度押しできません。串カツならマナー違反。");
        }
    });

    //@1削除処理
    function deleteEmp() {
        return $.ajax({
            type: "POST",
            url:  "ajax_deleteEmployee.php",
            data: (function() { return initData(); })(),//@2送信するデータを設定
            success: function(res){//成功 true 失敗 false
                        if(res == 1) {
                            alert("削除しました");
                            location.href = "../admin.php";
                        }else {
                            alert("削除できませんでした。");
                        }
                    },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#error").text("通信エラー");
                    }
        });
    }

    //@2送信するデータを設定
    function initData() {
        return {
                id:    $("#empId").val(),
                token: $("#token").val()
        };
    }

});
//EOF