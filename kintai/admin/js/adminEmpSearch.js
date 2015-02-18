/*
 *  @author YukiKawasaki
 *  FileName adminEmpSearch.js
 *  @create   2014/04/24
 *  Remain エラーチェックしてAjaxで従業員を検索させる
 *         05/07CSRF用トークンを追加
 *         06/19エンターキーで検索できるようにした
 *              pushStateを利用して検索した状態を保持できるようにした
*/
$(function() {
    $(document).ready(function(){
        var param = location.hash;
        if((param.split("#"))[1] != null) {
            param = ((param.split("#"))[1].split("search="))[1];
            param = decodeURIComponent(param);
            $("#searchWord").val(param);
            searchEmp();
        }
    });

    //エンターキーで検索できるようにした。
    $(window).keypress(function(e) {
        if(e.which == 13) {
            if(check()) {
                searchEmp();//@1検索処理
            }else {
                return false;
            }
        }
    });
    //検索ボタンクリック
    $("#search").click(function() {
        //@8キーワードが30バイト以内なら検索
        if(check()) {
            searchEmp();//@1検索処理
        }else {
            return false;
        }
    });

    //@1検索処理
    function searchEmp() {
        var url   = "./admin.php";
        $.ajax({
            type:     "POST",
            url:      "./ajax_emp_search.php",
            datatype: "json",
            data:     (function() {return initData(); })(),
            success:  function (res) {//該当あればJSONが返ってくる。
                            showResult(res);//@2検索結果を元にテーブルづくり
                            //他の画面に遷移しても検索結果を表示できるようにした。
                            history.pushState("", "", "./admin.php#search=" + $("#searchWord").val());
                        },
            error:    function(XMLHttpRequest, textStatus, errorThrown) {
                            showError("検索結果が取得できませんでした。");//@3通信エラー表示
                        }
        });
    }

    //@2検索結果を元にテーブルづくり
    function showResult(res) {
        $("#msg").hide();
        //前回に検索した結果を削除
        $("#result").children().remove();
        try {
            if ((data = JSON.parse(res)) != 0) {
                $table = $("<table>");
                $table.attr("id", "resultTable").attr("border", "1px").attr("align", "center");
                
                $(data).each(function () {
                    $nameTd = makeRoster(this);//@4勤務表へのリンク
                    $confTd = makeConf(this);//@5編集画面へのリンク
                    makeTable($nameTd, $confTd, $table);//@6検索結果テーブルへ追加
                });

                $("#result").append($table);
            }else {
                showError("該当がありませんでした");
            }
        }catch(e) {
            showError("検索結果が取得できませんでした。");
        }
            //表示
            $("#result").fadeIn("slow");
    }

    //@3通信エラー表示
    function showError(msg) {
        $("#result").hide();
        $("#msg").text(msg);
        $("#msg").fadeIn("slow");
    }

    //@4勤務表へのリンク
    function makeRoster(empData) {
        var year  = new Date().getFullYear();
        var month = new Date().getMonth() + 1;

        $nameTd = $("<td>");
        $empTd = $("<td>");

        //管理者でなかったら勤務表へのリンクを張る
        if(empData.admin != 1) {
            $roster = $("<a>")
            .attr("href", "./emptimesheet/emptimesheet.php?" 
                    + "year=" + year
                    + "&month=" + month 
                    + "&empId=" + empData.empId
                    + "&search=" + $("#searchWord").val())
            .text(empData.empName);

            //削除された人は赤文字表示
            if(empData.deleteFlg == 1) {
                $roster.css("color", "red");
            }
        }else {
            $roster = $("<span>").text(empData.empName);
        }

        $nameTd.append($roster);
        return $nameTd;
    }

    //@5編集画面へのリンク
    function makeConf(empData) {
        $confTd       = $("<td>");
        if(empData.deleteFlg != 1) {
            $confEmployee = $("<a>")
            .attr("href", "./confEmployee/confEmployee.php?empId=" + empData.empId + "&search=" + $("#searchWord").val())
            .text("編集");
        }else {
            $confEmployee = $("<span>").text("編集");
        }
        $confTd.append($confEmployee);
        return $confTd;
    }

    //@6検索結果テーブルへ追加
    function makeTable($nameTd, $confTd, $table) {
        $tr = $("<tr>");
        $tr.append($nameTd);
        $tr.append($confTd);
        $table.append($tr);
    }

    //@7送信するデータを設定
    function initData() {
        return {
            name:  $("#searchWord").val(),
            groupId: $("#groupId").val(),
            token: $("#token").val()
        }
    }

    //@8キーワード入っているか30バイト以内なら検索
    function check() {
        if($("#searchWord").val().length > 30) {
            $("#error").text("名前が長いです");
            return false;
        }

        return true;
    }
});
//EOF