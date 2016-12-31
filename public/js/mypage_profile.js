var prev_img = '';
var kind;

$(document).ready(function(){
	kind = $('#kind').val();
	
	$.ajax({
		url: adr_ctr + 'Account/getAccountInfo',
		async: false,
		type: 'post',
		success: function(result){
			try{
				result = JSON.parse(result);
			} catch (exception){}
			
			if (result.code == 200){
				var data = result.data;
				
				prev_img = data.img;
				$('#profile_img').attr('src', adr_s3 + 'profile/' + prev_img);
				
				if (kind == 'just')
					$('#id').text(data.id);
				
				$('#nickname').val(data.nickname);
				$('input[name=sex]').filter('input[value=' + data.sex + ']').attr('checked', 'checked');
				$('input[name=age]').filter('input[value=' + data.age + ']').attr('checked', 'checked');
			}
			else{
				alert("프로필을 수정할 수 없습니다.\n관리자에게 문의하세요.");
				location.href = adr_ctr + 'Mypage/index';
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
	
	if (kind == 'just'){
		$("#pw_msg").text("미입력");
		$("#pwc_msg").text("미입력");
	}
	
	$("#nickname_msg").text("사용가능");
	
	if (kind == 'just'){
		$("#pw").focusout(function(){
			checkPw();
		});
		$("#pwc").focusout(function(){
			checkPwc();
		});
	}
	
	$("#nickname").focusout(function(){
		checkNickname();
	});
});

function modifyJust(){
	if ($("#pw_msg").text() != "사용가능" || $("#pwc_msg").text() != "일치" || $("#nickname_msg").text() != "사용가능")
		alert("입력한 정보를 다시 확인해 주세요.");
	else{
		var pw = $("#pw").val();
		var nickname = $("#nickname").val();
		var sex = $("input[name=sex]:checked").val();
		var age = $("input[name=age]:checked").val();
		
		var imgFile = $("#profile_file");
		var img = "";
		
		if (imgFile[0].files && imgFile[0].files[0])
			img = imgFile[0].files[0];
		
		var data = new FormData();
		
		data.append("pw", pw);
		data.append("nickname", nickname);
		data.append("img", img);
		data.append("sex", sex);
		data.append("age", age);
		data.append("prev_img", prev_img);
		
		$.ajax({
			url: adr_ctr + 'Account/modify',
			async: false,
			data: data,
			type: 'post',
			cache: false,
			processData: false,
			contentType: false,
			success: function(result){
				result = JSON.parse(result);
				
				if (result.code == 200){
					alert('프로필이 수정되었습니다.');
					location.href = adr_ctr + 'Mypage/index';
				}
				else{
					alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
					alert('프로필 수정에 실패했습니다.\n서버 관리자에게 문의하세요.');
					location.href = adr_ctr + 'Mypage/profileIndex';
				}
			},
			error: function(request, response, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}

function modifySocial(){
	if ($('#nickname_msg').text() != '사용가능')
		alert('입력한 정보를 다시 확인해 주세요.');
	else{
		var nickname = $('#nickname').val();
		var sex = $('input[name=sex]:checked').val();
		var age = $('input[name=age]:checked').val();
		
		var imgFile = $('#profile_file');
		var img = '';
		
		if (imgFile[0].files && imgFile[0].files[0])
			img = imgFile[0].files[0];
		
		var data = new FormData();
		
		data.append('nickname', nickname);
		data.append('img', img);
		data.append('sex', sex);
		data.append('age', age);
		data.append('prev_img', prev_img);
	}
	
	$.ajax({
		url: adr_ctr + 'Account/modify',
		async: false,
		data: data,
		type: 'post',
		cache: false,
		processData: false,
		contentType: false,
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 1){
				alert('프로필이 수정되었습니다.');
				location.href = adr_ctr + 'Mypage/index';
			}
			else{
				alert('프로필 수정에 실패했습니다.\n서버 관리자에게 문의하세요.');
				location.href = adr_ctr + 'Mypage/profileIndex';
			}
		},
		error: function(request, response, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}
