/*
 *  @author  YukiKawasaki
 *  FileName adminEmpList.js
 *  @create  2014/05/07
 *  Remark   出勤した人一覧を取得
 *
*/
$(function () {
    $("#empList").hide();
    $("#msg").text("従業員取得中...");
    $.ajax({
        type: "POST",
        url: "./ajax_emp_list.php",
        datatype: "json",
        success: function(res) {
            $("#msg").hide();
            showEmpTable(res);//@1出勤者
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showError("出勤者一覧が取得できませんでした。");//@2通信エラー表示
        }
    });
    
    //@1出勤した人の一覧を取得してテーブル作成
    function showEmpTable(res) {
        try {
            if((data = JSON.parse(res)) != 0) {
                $(data).each(function () {
                    $empTd  = makeRoster(this);//@3従業員名に勤務表のリンクを張って表示
                    $timeTd = makeTime(this);//@4出勤時間退勤時間を表示
                    makeTable($empTd, $timeTd);//@5テーブルを作成して表示
                });
                $("#empList").fadeIn("slow");
                $("#msg").fadeOut("slow");
            }else {
                ;//DoNothing
            }
        }catch(e) {
            showError("出勤者一覧が取得できませんでした。");
        }
    };

    //@2エラー表示
    function showError(msg) {
        $("#msg").text(msg);
    }

    //@3従業員名に勤務表のリンクを張って表示
    function makeRoster(empData) {
        var year  = new Date().getFullYear();
        var month = new Date().getMonth() + 1;
        $empTd = $("<td>");
        $roster = $("<a>")
        .attr("href", "./emptimesheet/emptimesheet.php?" 
            + "year=" + year
            + "&month=" + month 
            + "&empId=" + empData.empId)
        .text(empData.empName);
        $empTd.append($roster);

        return $empTd;
    }

    //@4出勤時間退勤時間を表示
    function makeTime(empData) {
        var time = empData.startTime;
        if(time !== null) {
            if(empData.endTime !== null) {
                time += "-" + empData.endTime;
            }else {
                time += "~"
            }
            $timeTd = $("<td>");
            $timeTd.text(time);
            return $timeTd;
        }else {
            ;//DoNothing
        }
    }

    //@5テーブルを作成して表示
    function makeTable($empTd, $timeTd) {
        $tr = $("<tr>");
        $tr.append($empTd);
        $tr.append($timeTd);
        $("#empData").append($tr);
    }
});
//EOF