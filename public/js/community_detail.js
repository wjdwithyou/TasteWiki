function iModifyComm(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	var comm_idx = $("#comm_idx").val();
	
	location.href = adr_ctr + "Community/writeIndex?idx=" + comm_idx;
}

function deleteComm(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	alert('그럴 순 없어요!');		// temp
	
	// impl.
}

function writeReply(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능힙니다.");
		return;
	}
	
	var comm_idx = $("#comm_idx").val();
	var content = $("#cm_reply_content").val();
	
	$.ajax({
		url: adr_ctr + 'Community/writeReply',
		async: false,
		data: {
			idx: comm_idx,
			content: content
		},
		type: 'post',
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200)
				location.href = adr_ctr + 'Community/detail?idx=' + comm_idx;
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert("댓글 작성에 실패했습니다.\n서버 관리자에게 문의하세요.");
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}