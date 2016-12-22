var tempImgNum = 0;
var prevImg;
var checkWrite = false;

function sendFile(file, type){
	var data = new FormData();
	
	data.append("file", file);
	data.append("type", type);
	data.append("num", tempImgNum++);
	
	$.ajax({
		url: adr_ctr + 'Upload/addTempImage',	// temp
		async: false,
		data: data,
		type: 'post',
		cache: false,
		contentType: false,
		processData: false,
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 1)
				$("#summernote").summernote('insertImage', adr_ctr + 'img/temp/' + result.name);
			else
				alert("파일 전송 과정에 문제가 발생했습니다.\n서버 관리자에게 문의하세요.");
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}