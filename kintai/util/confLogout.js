/*
 * @author  Ryo Mashima
 * FileName /user/confLogout.js
 * @create  2014/05/07
 * Remark   ログアウトボタン押下時にダイアログ表示
*/

$(function() {
    $("#logoutBtn").click(function() {
        if(confirm("ログアウトしますか")) {
            location.href = "../util/logout.php";
        }else {
            return false;
        }
    });
});
//EOF