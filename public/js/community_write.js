$(document).ready(function(){
	// Init Summernote
	if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
		$('#summernote').summernote({
			height : 400,
			lang: 'ko-KR',
			callbacks: {
				onImageUpload: function(files){
					if (files[0].size > 10485760)
						alert("10MB 이하의 사진만 첨부할 수 있습니다.");
					else if (tempImgNum > 9)
						alert("파일은 최대 10개까지 첨부할 수 있습니다.");
					else
						sendFile(files[0], 'comm');
				},
			},
			toolbar: [
				['style', ['fontsize', 'bold']],
				['insert', ['picture']],
			]
		});
	}
	else{
		$('#summernote').summernote({
			height : 600,
			lang: 'ko-KR',
			callbacks: {
				onImageUpload: function(files){
					if (files[0].size > 10485760)
						alert("10MB 이하의 사진만 첨부할 수 있습니다.");
					else if (tempImgNum > 9)
						alert("파일은 최대 10개까지 첨부할 수 있습니다.");
					else
						sendFile(files[0], 'comm');
				},
			},
			toolbar: [
				['style', ['fontsize', 'bold', 'underline', 'strikethrough']],
				['color', ['color']],
				['para', ['paragraph']],
				['insert', ['picture'/*, 'video', 'link'*/]]
			]
		});
	}
	
	// init APM
	var is_mobile = $('#is_mobile').val();
	
	if (is_mobile == 1);
	else{
		POWERMODE.colorful = true;
		document.body.addEventListener('input', POWERMODE);
	}
	
	// if modify
	var comm_idx = $('#comm_idx').val();
	
	if (comm_idx){
		$.ajax({
			url: adr_ctr + 'Community/getPrevData',
			async: false,
			data: {
				idx: comm_idx
			},
			type: 'post',
			success: function(result){
				try{
					result = JSON.parse(result);
				} catch (exception){} 
				
				if (result.code == 200){
					$('#community_cate').val(result.data.category_idx);
					$('#comm_title').val(result.data.title);
					$('.panel-body').html(result.data.content);
					
					tempImgNum = result.num;
					prevImg = result.prev_img;
				}
				else{
					alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
					alert("게시글을 수정할 수 없습니다.\n서버 관리자에게 문의하세요.");
					location.href = adr_ctr + 'Community/detail?idx=' + comm_idx;
				}
			},
			error: function(request, status, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
	
	$(window).on("beforeunload", function(){
		if (checkWrite === true)
			return;
		
		var length = $("#comm_title").val().length + $("#summernote").val().length;
		
		if (length > 0)
			return "작성중인 내용은 저장되지 않습니다.";
		else
			return;
	});
	
	$(window).on("unload", function(){
		$.ajax({
			url: adr_ctr + "Upload/deleteTempImage",
			async: false,
			type: 'post',
			success: function(result){
				// impl.
			},
			error: function(request, status, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	});

	
	
	/*
	$(window).resize(function() {
		$('#preview_dialog').width($('#cmw_content').width());	
	});
	*/
});

function writeComm(){
	var cate = $("#community_cate").val();
	var title = $("#comm_title").val();
	var content = $("#summernote").val();
	
	$.ajax({
		url: adr_ctr + "Community/write",
		async: false,
		data: {
			cate: cate,
			title: title,
			content: content
		},
		type: 'post',
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200){
				alert('게시물 작성을 완료했습니다.');
				checkWrite = true;
				location.href = adr_ctr + 'Community/detail?idx=' + result.data;
			}
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert('게시물 작성에 실패했습니다.\n서버 관리자에게 문의하세요.');
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function modifyComm(){
	var cate = $("#community_cate").val();
	var title = $("#comm_title").val();
	var content = $("#summernote").val();
	
	var comm_idx = $("#comm_idx").val();
	
	var data = new FormData();
	
	data.append("cate", cate);
	data.append("idx", comm_idx);
	data.append("title", title);
	data.append("content", content);
	data.append("prev_img", prevImg);
	
	$.ajax({
		url: adr_ctr + "Community/update",
		async: false,
		data: data,
		type: 'post',
		cache: false,
		contentType: false,
		processData: false,
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200){
				alert("게시물이 수정되었습니다.");
				checkWrite = true;
				location.href = adr_ctr + 'Community/detail?idx=' + comm_idx;
			}
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert('게시물 수정에 실패했습니다.\n서버 관리자에게 문의하세요.');
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}