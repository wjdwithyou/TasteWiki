$(document).ready(function(){
	// mail
	$.ajax({
		url: adr_ctr + 'Account/sendVerify',
		async: false,
		type: 'post',
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200)
				$("#desc_text").text('이메일 인증 코드가 발송되었습니다.');
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert('인증 메일 전송 중에 오류가 발생했습니다.\n서버 관리자에게 문의하세요.');
			}
		},
		error: function(request, response, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
	
	
	
	// timer
	var time = 300;
	
	var timerID = setInterval(function(){
		--time;
		
		var m = parseInt(time / 60);
		var s = time % 60;
		
		$("#remain_time").text('(남은 시간: ' + m + '분 ' + s + '초)');
		
		if (time <= 0){
			clearInterval(timerID);
			alert('시간이 만료되어 이메일 인증을 취소합니다.');
			location.href = adr_ctr + 'Mypage/profileIndex';
		}
	}, 1000);
});