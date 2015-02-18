/*
 * @author  Ryo Mashima
 * FileName /user/js/updateRemark.js
 * @create  2014/05/07
 * Remark   備考欄の更新
            2014/06/26
            書き換え
*/

$(function(){
    $(".remark > input").keyup(function() {
        $("#" + this.id).parent().parent().addClass("changed");
    });

    $("#updateTop").click(function() {
        update();
    });

    $("#updateBottom").click(function() {
        update();
    });

    function update() {
        var flag = true;
        $(".changed").each(function() { 
            data = {
                empId:        $("#empId").val(), 
                workDate:     $(this).find(".workDate").val(), 
                remark:       $(this).find(".remark > input").val(),
                token:        $("#token").val()
            };
            $.ajax({
                type: "POST",
                url: "./ajax_update_timesheet.php",
                data: data,
                success: function(res) {
                    if(res == 1) {
                        ;//DoNothing
                    }else {
                        flag = false;
                    }
                    return flag;
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    throw(textStatus);
                }
            }); 
        });
        if(flag) {
            alert("更新しました");
        }else {
            alert("更新できませんでした。");
        }
    }
});
//EOF