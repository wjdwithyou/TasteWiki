function checkAgree(){
	var kind = $("#kind").val();
	
	var terms_chk = $("input:checkbox[id='terms_chk']").is(":checked");
	var pp_chk = $("input:checkbox[id='pp_chk']").is(":checked");
	var ad_chk = $("input:checkbox[id='ad_chk']").is(":checked");
	
	$.ajax({
		url: adr_ctr + 'Account/checkAgree',
		async: false,
		data: {
			terms: terms_chk,
			pp: pp_chk,
		},
		type: 'post',
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200){
				if (kind == 'just'){
					location.href = adr_ctr + 'Account/joinIndex?ad=' + ad_chk;
				}
				else{
					socialJoin(kind, ad_chk);
				}
			}
			else if (result.code == 240){
				alert(result.msg);
			}
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert("서버오류로 인해 회원가입을 진행할 수 없습니다.\n서버 관리자에게 문의하세요.");
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function socialJoin(kind, ad){
	var no = $("#no").val();
	
	var data = new FormData();
	
	data.append("kind", kind);
	data.append("ad", ad);
	data.append("no", no);
	
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
				commonLogin(kind, no, no);
			}
			else if (result.code == 240){
				alert(result.msg);
			}
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
