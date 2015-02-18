/*
 *  @author YukiKawasaki
 *  FileName timeSheet.js
 *  @create   2014/05/09
 *  Remain 勤務表の更新フラグ付け、勤務時間、超過時間の計算
           05/09 合計時間の計算が終わらなかった。
           05/12 合計時間再計算終了。
           05/26 3桁の時間が入った時に100の位が切り捨てられてるバグを修正。
           05/26 正規表現を書き換え。今度こそ299:59まで対応。文字も入れられないように。
           06/25 正規表現を書き換え。終了時間を99:59に変更
*/
$(function() {
    //テキストフォームのフォーカスが外れるとchangeクラスが付加され、更新対象になる。
    var start, end, interval;
    var pregStartTime = /^((2[0-3])|([0-1][0-9])):([0-5][0-9])$|^((2[0-3])|([0-1][0-9]))([0-5][0-9])$|^$///23:59までしか入力できない。
    var pregEndTime = /^\d\d:[0-5]\d$|^\d\d[0-5]\d$|^$/;//99:59まで対応。

    //フォーカスが当たった時の値を保持させる
    $(".startTime > input").focus(function() {
        start = $(this).val();
    });
    $(".endTime > input").focus(function() {
        end = $(this).val();
    });
    $(".intervalTime > input").focus(function() {
        interval = $(this).val();
    });

    //フォーカスが外れたとき値をチェックして不正な値ならば変更前に戻す。
    $(".startTime > input").blur(function() {
        eventInput(this, pregStartTime, start);
    });
    $(".endTime > input").blur(function() {
        eventInput(this, pregEndTime, end);
    });
    $(".intervalTime > input").blur(function() {
        eventInput(this, pregEndTime, interval);
    });
    $(".remark > input").blur(function() {
        changeFlg(this);//@2変更されたものにchangedクラスを付加
    });

    //@1フォーカスが外れた際に勤務時間超過時間の更新、changeクラスの付加を行う。
    function eventInput(input, preg, prevTime) {
        if($(input).val().match(preg)){
            $(input).val(timeFormat($(input)));
            dispTime(input, $(input).parent().attr("class"));//@1勤務時間、超過時間を表示
            changeFlg(input);//@2変更されたものにchangedクラスを付加
        }else {
            $(input).val(prevTime);
        }
    }
    
    //@2":"がなかったら":"つける
    function timeFormat($input) {
        return $input.val().replace(/(\d)(?=(\d\d)+$)/g, "$1:");
    }

    //@3勤務時間、超過時間を表示
    function dispTime(ele, className) {
        var workDate      = ele.id.replace(className, "");
        var $startTime    = $("#" + workDate + "startTime");
        var $endTime      = $("#" + workDate + "endTime");
        var $intervalTime = $("#" + workDate + "intervalTime");
        var $workTime     = $("#" + workDate + "workTime");
        var $diffStdTime  = $("#diffStdTime");
        var $overTime     = $("#" + workDate + "overTime");
        
        if($startTime.val() != "" && $endTime.val() != "" && $intervalTime.val() != "") {
            var diff = workTime($startTime, $endTime, $intervalTime);//@3勤務時間を計算
            $workTime.text(makeTime(diff));

            var diffStrTime = overTime(diff, $diffStdTime, $intervalTime);//@4超過時間を計算
            $overTime.text(makeTime(diffStrTime));//@5分を0埋めで時刻形式に整形して返す。

            var sum = totalTime();//@6合計時間を計算
            $("#sumTime").text(makeTotalTime(sum));

            var sum = totalOverTime();
            $("#sumOverTime").text(makeTotalTime(sum));
        }else {
            return false;
        }
    }

    //@4勤務時間を計算
    function workTime($startTime, $endTime, $intervalTime) {
        var start    = splitTime($startTime);
        var end      = splitTime($endTime);
        var interval = splitTime($intervalTime);

        var startHour    = parseInt(start[0],10)    * 60 + parseInt(start[1], 10);
        var endHour      = parseInt(end[0], 10)      * 60 + parseInt(end[1], 10);
        var intervalHour = parseInt(interval[0], 10) * 60 + parseInt(interval[1], 10);

        diff = endHour - startHour - intervalHour;
        
        if(diff < 0) {
            return 0;
        }

        return diff;
    }

    //@5":"がなくても計算できるように
    function splitTime($time) {
        if($time.val().indexOf(":") != -1) {
            var time = $time.val().split(":");
        }else {
            var time = timeFormat($time);
            time = time.split(":");
        }

        return time;
    }

    //@6超過時間を計算
    function overTime(diff, $diffStdTime, $intervalTime) {
        var std   = $diffStdTime.val().split(":");
            std   = parseInt(std[0]) * 60 + parseInt(std[1]);

        result = diff - std ;

        if(result < 0) {
            result = 0;
        }
        return result;
    }

    //@6 分を0埋めで時刻形式に整形して返す。
    function makeTime(minits) {
        if(minits !== 0) {
            var hour = Math.floor(minits / 60);
            var min  = minits % 60;

            if(hour < 100) {
                var result = ("0" +hour).slice(-2) + ":" + ("0" + min).slice(-2);
            }else {
                var result = ("0" +hour).slice(-3) + ":" + ("0" + min).slice(-2);
            }
            
        }else {
            var result = '';
        }

        return result;
    }

    //@7合計の勤務時間、超過時間は0埋めが望ましいので
    function makeTotalTime(minits) {
        var time = makeTime(minits);
        if(time != '') {
            return time;
        }else {
            return '00:00';
        }
    }

    //@8合計時間の計算
    function totalTime() {
        var sum = 0;
        $(".workTime").each(function() {
            if($(this).html().replace(/\s|&nbsp;/, '').length != 0) {
                var time = $(this).text().split(":");
                var min  = parseInt(time[0], 10) * 60 + parseInt(time[1], 10);
                sum += min;
            }
        });

        return sum;
    }

    //@9超過時間の合計の計算
    function totalOverTime() {
        var sum = 0;
        $(".overTime").each(function() {
            if($(this).html().replace(/\s|&nbsp;/, '').length != 0) {
                var time = $(this).text().split(":");
                var min  = parseInt(time[0], 10) * 60 + parseInt(time[1], 10);
                sum += min;
            }
        });

        return sum;
    }

    //@10変更されたものにchangedクラスを付加
    function changeFlg(ele) {
        $("#" + ele.id).parent().parent().parent().addClass("changed");
    }
});
//EOF