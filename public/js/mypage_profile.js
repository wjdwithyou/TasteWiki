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

				var email_arr = data.email.split('@');

				$('#email1').val(email_arr[0]);
				$("#email_selector").val(email_arr[1]).attr('selected', 'selected');

				if ($("#email_selector").val() == undefined){
					$("#email_selector").val('').attr('selected', 'selected');

					$("#email2").attr("readonly", false);
					$("#email2").css("background", '#FFFFFF');
				}
				else{
					$("#email2").attr("readonly", true);
					$("#email2").css("background", '#CCCCCC');
				}

				$("#email2").val(email_arr[1]);

				$("#email_chk").val(data.email_chk);

				if (data.email_chk == 1){
					$("#verify_icon").append("<i class='fa fa-check verify_green'></i>");
					$("#verify_msg").addClass("verify_green");
					$("#verify_msg").text("인증됨");
				}
				else{
					$("#verify_icon").append("<i class='fa fa-times verify_red'></i>");
					$("#verify_msg").addClass("verify_red");
					$("#verify_msg").text("인증되지 않음");
				}

				$('#name').val(data.name);

				$('input[name=sex]').filter('input[value=' + data.sex + ']').attr('checked', 'checked');
				$('input[name=age]').filter('input[value=' + data.age + ']').attr('checked', 'checked');

				data.ad_chk = (data.ad_chk == 1)? true: false;

				$('input[name=ad]').filter('input[value=' + data.ad_chk + ']').attr('checked', 'checked');
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
		$("#pw_msg").addClass("red");
		$("#pw_msg").text("미입력");

		$("#pwc_msg").addClass("red");
		$("#pwc_msg").text("미입력");
	}

	$("#nickname_msg").addClass("blue");
	$("#nickname_msg").text("사용가능");

	$("#email_msg").addClass("red");
	$("#email_msg").text("");

	$('#email1').focusin(confirmAbandonment);
	$('#email2').focusin(confirmAbandonment);
	$('#email_selector').focusin(confirmAbandonment);

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

	$("#email1").focusout(function(){
		checkEmail();
	});

	$("#email2").focusout(function(){
		checkEmail();
	});

	$('#email_selector').change(function(){
		setEmail2();
		checkEmail();
	});
});

function confirmAbandonment(){
	var email_chk = $('#email_chk').val();

	if (email_chk == 0)
		return;

	var abandonment = confirm('이메일을 수정하면 다시 인증 과정을 거쳐야 합니다.\n계속하시겠습니까?');

	if (abandonment){
		$.ajax({
			url: adr_ctr + 'Mail/abandonVerify',
			async: false,
			type: 'post',
			success: function(result){
				result = JSON.parse(result);

				if (result.code == 200){
					$('#email_chk').val('0');

					$("#verify_icon").html("<i class='fa fa-times verify_red'></i>");
					$("#verify_msg").addClass("verify_red");
					$("#verify_msg").text("인증되지 않음");
				}
				else if (result.code == 240)
					alert(result.msg);
				else{
					alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
					alert('서버 오류가 발생했습니다.\n서버 관리자에게 문의하세요.');
					location.href = adr_ctr + 'Mypage/index';
				}
			},
			error: function(request, status, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
	else{
		$('#email1').blur();
		$('#email2').blur();
		$('#email_selector').blur();
	}
}

function verifyEmail() {
	var email = getEmail();
	var email_chk = $('#email_chk').val();

	if (email_chk == 1) {
		alert('이미 인증이 완료된 메일입니다.');
	} else if ($('#email_msg').text() != '' || email == '@') {
		alert('올바른 이메일 양식을 입력해 주세요.');
	} else {
		location.href = adr_ctr + 'Mail/verifyIndex?mail=' + email;
	}
}

function modifyJust(){
	if ($("#pw_msg").text() != "사용가능" || $("#pwc_msg").text() != "일치" || $("#nickname_msg").text() != "사용가능" || $("#email_msg").text() != "")
		alert("입력한 정보를 다시 확인해 주세요.");
	else{
		var pw = $("#pw").val();
		var nickname = $("#nickname").val();
		var email = getEmail();
		var name = $("#name").val();
		var sex = $("input[name=sex]:checked").val();
		var age = $("input[name=age]:checked").val();
		var ad = $("input[name=ad]:checked").val();

		var imgFile = $("#profile_file");
		var img = "";

		if (imgFile[0].files && imgFile[0].files[0])
			img = imgFile[0].files[0];

		var data = new FormData();

		data.append("pw", pw);
		data.append("nickname", nickname);
		data.append("email", email);
		data.append("name", name);
		data.append("sex", sex);
		data.append("age", age);
		data.append("ad", ad);
		data.append("img", img);
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
				else if (result.code == 240)
					alert(result.msg);
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
	if ($('#nickname_msg').text() != '사용가능' || $("#email_msg").text() != "")
		alert('입력한 정보를 다시 확인해 주세요.');
	else{
		var nickname = $('#nickname').val();
		var email = getEmail();
		var name = $('#name').val();
		var sex = $('input[name=sex]:checked').val();
		var age = $('input[name=age]:checked').val();
		var ad = $('input[name=ad]:checked').val();

		var imgFile = $('#profile_file');
		var img = '';

		if (imgFile[0].files && imgFile[0].files[0])
			img = imgFile[0].files[0];

		var data = new FormData();

		data.append('nickname', nickname);
		data.append('email', email);
		data.append('name', name);
		data.append('sex', sex);
		data.append('age', age);
		data.append('ad', ad);
		data.append('img', img);
		data.append('prev_img', prev_img);

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
				else if (result.code == 240)
					alert(result.msg);
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
