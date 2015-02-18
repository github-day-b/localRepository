/*
 * @author  Ryo Mashima
 * FileName /user/nowTime.js
 * @create  2014/05/07
 * Remark   ブラウザに現在時刻表示
            2014/06/26
            onloadをなくすので即時関数に
            時間の0埋めをif文なしに
            jqueryに書き換え
            日付はsettimeoutを使う必要ないので書き換え
*/
;$(function() {
    $(document).ready(function(){
        showDate();
        showTime();
    });

    //日付の表示
    function showDate(){
        var week = ["日","月","火","水","木","金","土"];
        var dd   = new Date();
        var year = dd.getFullYear();
        var mon  = ("0" + (dd.getMonth() + 1)).slice(-2);
        var date = ("0" + (dd.getDate())).slice(-2);
        var weekDay = week[dd.getDay()];
        $("#date").text(year + "/" + mon + "/" + date + "(" + weekDay + ")");
    }

    //時間の表示
    function showTime() {
        var dd   = new Date();
        var hour = ("0" + (dd.getHours())).slice(-2);
        var min  = ("0" + (dd.getMinutes())).slice(-2);
        var sec  = ("0" + (dd.getSeconds())).slice(-2);

        /* 1秒毎にコロンを点滅 */
        if (sec % 2 == 0) {
            var visible = ':';
        } else {
            var visible = ' ';
        }

        $("#time").text(hour + visible  + min);
        setTimeout(arguments.callee, 1000);
    }
});
//EOF