$(document).ready(function(){
	checkAd();
	
	$("#id_msg").addClass("red");
	$("#id_msg").text("미입력");
	
	$("#pw_msg").addClass("red");
	$("#pw_msg").text("미입력");
	
	$("#pwc_msg").addClass("red");
	$("#pwc_msg").text("미입력");
	
	$("#nickname_msg").addClass("red");
	$("#nickname_msg").text("미입력");
	
	$("#id").focusout(function(){
		checkId();
	});
	$("#pw").focusout(function(){
		checkPw();
	});
	$("#pwc").focusout(function(){
		checkPwc();
	});
	$("#nickname").focusout(function(){
		checkNickname();
	});
	
	$("#email_selector").change(function(){
		setEmail2();
	});
});

function justJoin(){
	if ($("#id_msg").text() != "사용가능" || $("#pw_msg").text() != "사용가능" || $("#pwc_msg").text() != "일치" || $("#nickname_msg").text() != "사용가능")
		alert("입력한 정보를 다시 확인해 주세요.");
	else{
		var kind = 'just';
		var ad_chk = $("#ad_chk").val();
		var id = $("#id").val();
		var pw = $("#pw").val();
		var nickname = $("#nickname").val();
		var email = $('#email1').val() + '@' + $('#email2').val();
		var name = $("#name").val();
		var sex = $("input[name=sex]:checked").val();
		var age = $("input[name=age]:checked").val();
		
		var imgFile = $("#profile_file");
		var img = "";
		
		if (imgFile[0].files && imgFile[0].files[0])
			img = imgFile[0].files[0];
		
		var data = new FormData();
		
		data.append("kind", kind);
		data.append("ad", ad_chk);
		data.append("id", id);
		data.append("pw", pw);
		data.append("nickname", nickname);
		data.append("email", email);
		data.append("name", name);
		data.append("sex", sex);
		data.append("age", age);
		data.append("img", img);
		
		$.ajax({
			url: adr_ctr + 'Account/join',
			async: false,
			data: data,
			type: 'post',
			cache: false,
			processData: false,
			contentType: false,
			success: function(result){
				result = JSON.parse(result);
				
				if (result.code == 200){
					alert("계정이 생성되었습니다.");
					location.href = adr_ctr + 'Account/loginIndex';
				}
				else{
					alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
					alert("계정 생성에 실패했습니다.\n서버 관리자에게 문의하세요.");
					location.href = adr_ctr + 'Account/joinIndex';
				}
			},
			error: function(request, response, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}