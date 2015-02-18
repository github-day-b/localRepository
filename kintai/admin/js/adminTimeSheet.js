/*
 *  @author YukiKawasaki
 *  FileName adminTimeSheet.js
 *  @create   2014/05/09
 *  Remain 勤務表の更新
*/
$(function(){
    //@1更新ボタンが押された時の処理。changedクラスが付加されたものをPOSTで送りつける。
    $("#updateTop").click(function() {
        update();
    });

    $("#updateBottom").click(function() {
        update();
    });

    function update() {
        $(".changed").each(function() {
            var workdate = $(this).find(".workDate").val();
            var data = {
                empId:        $("#empId").val(),
                workDate:     $(this).find(".workDate").val(), 
                startTime:    $(this).find(".startTime > input").val(),
                endTime:      $(this).find(".endTime > input").val(), 
                intervalTime: $(this).find(".intervalTime > input").val(),
                remark:       $(this).find(".remark > input").val(),
                token:        $("#token").val()
            };
            $.ajax({
                type: "POST",
                url: "./ajax_update_emptimesheet.php",
                data: data,
                success: function(res) {
                    if(res == 1) {
                        ;//DoNothing
                    }else {
                        alert("更新できませんでした。"+workdate);
                        return false;
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    console.log(textStatus);
                }
            }); 
           
        });
        alert("更新しました");
    }
});
//EOF