$(function() {
	$("#submit").on("click", function() {
		var groupID = $("#groupID").val();
		var groupName = $("#groupName").val();
		var adminName = $("#adminName").val();
		var adminMail = $("#adminMail").val();
		var password  = $("#password").val();
		var location  = ($("#locationFlg").prop("checked") ? 1 : 0);
		var token     = $("#token").val();
		var url = "./ajax_makeGroup.php";
		var data = {
						"groupID"  : groupID,
						"groupName": groupName,
						"adminName": adminName,
						"adminMail": adminMail,
						"password" : password,
			            "location" : location,
						"token"    : token
					};

		if(isCheck()) {
			$.ajax({
				url: url,
				type:"POST",
				data: data,
				async: false,
				success: function(res) {
					if(res == "OK") {
						$("#msg").text("登録が完了しました");
					}else {
						$("#msg").text(res);
					}
				}
			});
		}

		function isCheck() {
			if(!isRequire(groupID)) {
				$("#msg").text("グループIDが入力されていません");
				return false;
			}else if(!isLength(groupID, 30)) {
				$("#msg").text("グループIDが長すぎます");
				return false;
			}else if(!isAlphabet(groupID)) {
				$("#msg").text("グループIDは半角英数字で入力してください");
				return false;
			}else if(!isRequire(groupName)){
				$("#msg").text("グループ名が入力されていません");
				return false;
			}else if(!isLength(groupName, 30)) {
				$("#msg").text("グループ名が長すぎます");
				return false;
			}else if(!isRequire(adminName)) {
				$("#msg").text("管理者名が入力されていません");
				return false;
			}else if(!isLength(adminName, 30)) {
				$("#msg").text("管理者名が長すぎます");
				return false;
			}else if(!isRequire(adminMail)) {
				$("#msg").text("管理者emailが入力されていません");
				return false;
			}else if(!isEmail(adminMail)) {
				$("#msg").text("emailと形式が異なります");
				return false;
			}else if(!isLength(adminMail, 255)) {
				$("#msg").text("アドレスが長すぎます");
				return false;
			}else if(!isRequire(password)) {
				$("#msg").text("パスワードを入力してください");
				return false;
			}else {
				;//DoNothing
			}

			return true;
		}
	})
});

