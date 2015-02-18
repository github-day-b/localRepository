/*
 * @author  Ryo Mashima
 * FileName checkForm.js
 * @create  2014/05/07
 * Remark   ログインフォームの未入力チェック
*/

function checkForm() {
    if (document.loginForm.groupid.value == ""){
        document.getElementById("msg").innerHTML = "グループIDが未入力です";
        return false;
    }
    else if (document.loginForm.userid.value == ""){
        document.getElementById("msg").innerHTML = "ユーザIDが未入力です";
        return false;
    }
    else if(document.loginForm.pass.value == ""){ // 「パスワード」の入力をチェック
        document.getElementById("msg").innerHTML = "パスワードが未入力です";
        return false;
    }
    else {
        ;//DoNothing
    }

    return true;
}
//EOF