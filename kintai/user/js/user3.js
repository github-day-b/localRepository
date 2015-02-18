/*
 * @author  YukiKawasaki
 * FileName /user/js/user.js
 * @create  2014/06/26
 * Remark   出勤ボタン退勤ボタンを押されたときの動作
 *          2015/01/28 位置情報を取得できるように修正
*/

(function (window, $) {
    var geolocation = {};
    var geocoder;

    geolocation.initialize = function (aEvent) {
        var target = aEvent.target.id;

        var is_success = function (position) {
            var gpsLat = position.coords.latitude;
            var gpsLng = position.coords.longitude;
            gmap_init(gpsLat,gpsLng);
        }

        var is_error = function (error) {
            var result = "";
            switch(error.code) {
                case 1:
                    result = '位置情報の取得が許可されていません';
                    break;
                case 2:
                    result = '位置情報の取得に失敗';
                    break;
                case 3:
                    result = 'タイムアウト';
                    break;
            }
            //document.getElementById('message').innerHTML = result;
            if(target == 'workin') {
                geolocation.toggleWorkin(result);
            } else if (target == 'workout') {
                geolocation.toggleWorkout(result);
            }
        }

        var gmap_init = function (gpsLat, gpsLng) {
            var geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(gpsLat,gpsLng);
            var result = "";

            geocoder.geocode({'latLng':latlng}, function(results,status){
                if (status == google.maps.GeocoderStatus.OK) {
                    //result = results[2].formatted_address + '付近';
                    result = results[0].address_components[5].long_name
                    + results[0].address_components[4].long_name
                    + results[0].address_components[3].long_name
                    + results[0].address_components[2].long_name
                    + '付近';
                    //document.getElementById('message').innerHTML = result;
                    geolocation.showMap(latlng);
                } else {
                    console.log(status);
                }

                if(target == 'workin') {
                    geolocation.toggleWorkin(result);
                } else if (target == 'workout') {
                    geolocation.toggleWorkout(result);
                }
            });
        }

        var checkBrowser = function () {
            //位置情報を取得するグループ、かつ、geolocation利用可能の場合は位置情報を取って登録処理をする
            var code = $(':hidden[id="location"]').val();
            if (navigator.geolocation && code == 1) {
                navigator.geolocation.getCurrentPosition(is_success, is_error, {enableHighAccuracy: true});
            }else{
                var result = null;
                if(target == 'workin') {
                    geolocation.toggleWorkin(result);
                } else if (target == 'workout') {
                    geolocation.toggleWorkout(result);
                }
            }
        }

        var bind = function () {
            checkBrowser();
        }
        bind();
    }

    geolocation.showMap = function (aLatlng) {
        var options = {
            zoom: 15,
            center: aLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl : false,
            scaleControl: true,
            streetViewControl : false
        };
        var gmap = new google.maps.Map(document.getElementById('mapField'), options);
        new google.maps.Marker({map: gmap, position: aLatlng});
        $('#mapField').css({'width':'200px','height':'200px'});
    }

    geolocation.toggleWorkout = function (aResult) {
        var token = $("#token").val();
        var geolocation = aResult;
        console.log(geolocation);
        $.ajax({
            type: "POST",
            url : "./workout.php",
            data: {token: token,location : geolocation},
            success: function(res) {
                if(res == "1") {
                    alert("退勤しました。");
                    $("#workout").attr("disabled", true);
                }else {
                    alert(res);
                }
            }
        });
    }

    geolocation.toggleWorkin = function (aResult) {
        var token = $("#token").val();
        var geolocation = aResult;
        console.log(geolocation);
        $.ajax({
            type: "POST",
            url : "./workin.php",
            data: {token: token,location : geolocation},
            success: function(res) {
                if(res == "1") {
                    alert("出勤しました。");
                    $("#workin").attr("disabled", true);
                    $("#workout").attr("disabled", false);
                }else {
                    alert(res);
                }
            }
        });
    }

    window.aGeolocation = geolocation;


    $(function() {
        $("#workin").click(aGeolocation.initialize);
        $("#workout").click(aGeolocation.initialize);
    });

})(window, jQuery);
//EOF