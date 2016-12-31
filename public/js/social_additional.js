$(document).ready(function(){
	$("#nickname_msg").text("미입력");
	
	$("#nickname").focusout(function(){
		checkNickname();
	});
});

function socialJoin(){
	var sex = $("input[name=sex]:checked").val();
	var age = $("input[name=age]:checked").val();
	
	if ($("#nickname_msg").text() != "사용가능")
		alert("입력한 정보를 다시 확인해 주세요.");
	else if (sex === undefined)
		alert("성별을 입력해 주세요.");
	else if (age === undefined)
		alert("연령대를 입력해 주세요.");
	else{
		var prev = $("#prev").val();
		
		var kind = $("#kind").val();
		var no = $("#no").val();
		var nickname = $("#nickname").val();
		
		var imgFile = $("#profile_file");
		var img = "";
		
		if (imgFile[0].files && imgFile[0].files[0])
			img = imgFile[0].files[0];
		
		var data = new FormData();
		
		data.append("kind", kind);
		data.append("no", no);
		data.append("nickname", nickname);
		data.append("img", img);
		data.append("sex", sex);
		data.append("age", age);
		
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
				
				if (result.code == 200)
					commonLogin(kind, no, no);
				else{
					alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
					alert("로그인에 실패했습니다.\n서버 관리자에게 문의하세요.");
					location.href = adr_ctr + 'Account/loginIndex';
				}
			},
			error: function(request, response, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}