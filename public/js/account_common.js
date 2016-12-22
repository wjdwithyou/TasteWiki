function checkId(){
	var patternId = /^[0-9A-Za-z]+$/;
	var idMin = 5;
	var idMax = 13;
	
	var id = $("#id").val();
	var len = id.length;
	
	if (len == 0){
		$("#id_msg").removeClass("blue");
		$("#id_msg").addClass("red");
		$("#id_msg").text("미입력");
	}
	else if (!patternId.test(id)){
		$("#id_msg").removeClass("blue");
		$("#id_msg").addClass("red");
		$("#id_msg").text("영문, 숫자 외 사용불가");
	}
	else if (len < idMin || len > idMax){
		$("#id_msg").removeClass("blue");
		$("#id_msg").addClass("red");
		$("#id_msg").text(idMin + "~" + idMax + "자까지 사용가능");
	}
	else{
		$.ajax({
			url: adr_ctr + 'Account/checkId',
			async: false,
			data: {
				id: id
			},
			type: 'post',
			success: function(result){
				result = JSON.parse(result);
				
				if (result.code == 1){
					$("#id_msg").removeClass("red");
					$("#id_msg").addClass("blue");
					$("#id_msg").text("사용가능");
				}
				else{
					$("#id_msg").removeClass("blue");
					$("#id_msg").addClass("red");
					$("#id_msg").text("중복으로 인해 사용불가");
				}
			},
			error: function(request, status, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}

function checkPw(){
	var patternPw = /^[0-9A-Za-z!@#\$%\^&\*]+$/;
	var pwMin = 8;
	var pwMax = 15;
	
	var pw = $("#pw").val();
	var len = pw.length;
	
	if (len == 0){
		$("#pw_msg").removeClass("blue");
		$("#pw_msg").addClass("red");
		$("#pw_msg").text("미입력");
	}
	else if (!patternPw.test(pw)){
		$("#pw_msg").removeClass("blue");
		$("#pw_msg").addClass("red");
		$("#pw_msg").text("영문, 숫자, !@#$%^&* 외 사용불가");
	}
	else if (len < pwMin || len > pwMax){
		$("#pw_msg").removeClass("blue");
		$("#pw_msg").addClass("red");
		$("#pw_msg").text(pwMin + "~" + pwMax + "자까지 사용가능");
	}
	else{
		$("#pw_msg").removeClass("red");
		$("#pw_msg").addClass("blue");
		$("#pw_msg").text("사용가능");
	}
	
	checkPwc();
}

function checkPwc(){
	var pw = $("#pw").val();
	var pwc = $("#pwc").val();
	
	if (pwc.length == 0){
		$("#pwc_msg").removeClass("blue");
		$("#pwc_msg").addClass("red");
		$("#pwc_msg").text("미입력");
	}
	else if (pwc != pw){
		$("#pwc_msg").removeClass("blue");
		$("#pwc_msg").addClass("red");
		$("#pwc_msg").text("미일치");
	}
	else{
		$("#pwc_msg").removeClass("red");
		$("#pwc_msg").addClass("blue");
		$("#pwc_msg").text("일치");
	}
}

function checkNickname(){
	var patternNickname = /^[0-9A-Za-zㄱ-ㅎㅏ-ㅣ가-힣]+$/;
	var nicknameMin = 2;
	var nicknameMax = 10;
	
	var nickname = $("#nickname").val();
	var len = nickname.length;
	
	if (len == 0){
		$("#nickname_msg").removeClass("blue");
		$("#nickname_msg").addClass("red");
		$("#nickname_msg").text("미입력");
	}
	else if (!patternNickname.test(nickname)){
		$("#nickname_msg").removeClass("blue");
		$("#nickname_msg").addClass("red");
		$("#nickname_msg").text("한글, 영문, 숫자 외 사용불가");
	}
	else if (len < nicknameMin || len > nicknameMax){
		$("#nickname_msg").removeClass("blue");
		$("#nickname_msg").addClass("red");
		$("#nickname_msg").text(nicknameMin + "~" + nicknameMax + "자까지 사용가능");
	}
	else{
		$.ajax({
			url: adr_ctr + 'Account/checkNickname',
			async: false,
			data: {
				nickname: nickname
			},
			type: 'post',
			success: function(result){
				result = JSON.parse(result);
				
				if (result.code == 200){
					$("#nickname_msg").removeClass("red");
					$("#nickname_msg").addClass("blue");
					$("#nickname_msg").text("사용가능");
				}
				else{
					$("#nickname_msg").removeClass("blue");
					$("#nickname_msg").addClass("red");
					$("#nickname_msg").text("중복으로 인해 사용불가");
				}
			},
			error: function(request, status, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}

function setEmail2(){
	var email2 = $('#email_selector').val();
	
	if (email2 == ''){
		$("#email2").attr("readonly", false);
		$("#email2").css("background", '#FFFFFF');
	}
	else{
		$("#email2").attr("readonly", true);
		$("#email2").css("background", '#CCCCCC');
	}
	
	$("#email2").val(email2);
}

function showPreview(file){
	var img = $("#profile_img");
	
	if (window.FileReader && file[0].files && file[0].files[0]){
		var reader = new FileReader();
		
		reader.onload = function(e){
			img.attr("src", e.target.result);
		}
		
		reader.readAsDataURL(file[0].files[0]);
	}
	
	//img.attr("src", file.val());
}

function commonLogin(kind, id, pw){
	var prev = $("#prev").val();
	
	$.ajax({
		url: adr_ctr + 'Account/login',
		async: false,
		data: {
			kind: kind,
			id: id,
			pw: pw
		},
		type: 'post',
		success: function(result){
			try{
				result = JSON.parse(result);
			} catch (exception){}
			
			if (result.code == 1)
				location.href = prev;
			else if(result.code == 0){
				if (kind == 'just')
					alert("입력한 정보를 다시 확인해주세요.");
				else{
					alert("첫 로그인이시네요.\n추가 정보를 입력해주세요.");
					location.href = adr_ctr + 'Account/socialAdditionalIndex?kind=' + kind + '&no=' + id + '&prev=' + prev;
				}
			}
			else{
				alert(result.code+"로그인에 실패했습니다.\n서버 관리자에게 문의하세요.");
				location.href = adr_ctr + 'Account/loginIndex';
			}
		},
		error: function(request, response, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}