$(document).ready(function() {
	// mail
	var email = $("#email").val();
	
	$.ajax({
		url: adr_ctr + 'Mail/sendVerify',
		async: false,
		data: {
			mail: email
		},
		type: 'post',
		success: function(result) {
			result = JSON.parse(result);
			
			if (result.code == 200);
			else if (result.code == 240) {
				alert(result.msg);
			} else {
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert('인증 메일 전송 중에 오류가 발생했습니다.\n서버 관리자에게 문의하세요.');
				location.href = adr_ctr + 'Mypage/profileIndex';
			}
		},
		error: function(request, response, error) {
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
	
	
	
	// timer
	/*
	var time = 600;

	var timerID = setInterval(function(){
		--time;
		
		var m = parseInt(time / 60);
		var s = time % 60;
		
		$("#remain_time").text('(남은 시간: ' + m + '분 ' + s + '초)');
		
		if (time <= 0){
			clearInterval(timerID);
			alert('시간이 만료되어 이메일 인증이 취소되었습니다.');
			location.href = adr_ctr + 'Mypage/profileIndex';
		}
	}, 1000);
	*/
});

/*
function checkVerify() {
	var code = $("#verify_code").val();
	var email = $("#email").val();
	
	$.ajax({
		url: adr_ctr + "Mail/checkVerify",
		async: false,
		data: {
			code: code,
			mail: email
		},
		type: 'post',
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200) {
				alert('메일 인증이 완료되었습니다.');
				location.href = adr_ctr + 'Mypage/profileIndex';
			} else if (result.code == 240) {
				alert(result.msg);
			} else {
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert("인증 과정에서 문제가 발생했습니다.\n서버 관리자에게 문의하세요.");
			}
		},
		error: function(request, status, error) {
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}
*/
