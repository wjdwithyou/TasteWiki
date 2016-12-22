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
						sendFile(files[0], 'spot');
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
						sendFile(files[0], 'spot');
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
	var spot_idx = $('#spot_idx').val();
	
	if (spot_idx){
		$.ajax({
			url: adr_ctr + 'Spot/getPrevData',
			async: false,
			data: {
				idx: spot_idx
			},
			type: 'post',
			success: function(result){
				result = JSON.parse(result);
				
				if (result.code == 1){
					var adr_s3 = $('#adr_s3').val();
					
					var cate = result.cate;
					
					for (var i = 0; i < cate.length; ++i)
						addStack(cate[i][0], cate[i][1], cate[i][2]);
					
					$('#thumbnail_img').html('<img src="' + adr_s3 + 'spot/' + result.n_data.img + '" id="spot_img" height="100" width="100"/>');
					$('#spot_name').val(result.n_data.name);
					$('.panel-body').html(result.n_data.content);
					
					tempImgNum = result.img_num;
					prevImg = result.prev_img;
				}
				else{
					alert("Spot을 수정할 수 없습니다.\n서버 관리자에게 문의하세요.");
					location.href = adr_ctr + 'Spot/index?idx=' + spot_idx;
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
		
		var length = $("#spot_name").val().length + $("#summernote").val().length;
		
		if (length > 0)
			return "작성중인 내용은 저장되지 않습니다.";
		else
			return;
	});
	
	$(window).on("unload", function(){
		$.ajax({
			url: adr_ctr + "upload/deleteTempImage",
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

function addStack(cate, idx, name){
	var cnt = 0;
	
	$(".cate_stack").each(function(){
		if ($(this).val() === cate + idx){
			++cnt;
			return;
		}
	});
	
	if (cnt == 0){
		var temp = "<button type='button' class='cate_stack' onclick='deleteStack($(this))' value='" + cate + idx + "'>" + name + "&nbsp;&nbsp;X</button>";
		$("#stack_wrap").append(temp);
	}
	else
		alert("이미 선택한 항목입니다.");
}

function deleteStack(e){
	e.remove();
}

function showPreview(file){
	var img = $('#spot_img');
	
	if (window.FileReader && file[0].files && file[0].files[0]){
		var reader = new FileReader();
		
		reader.onload = function(e){
			var prev = img.attr('src');
			var temp = "<input type='hidden' id='prev_spot_img' value='" + prev + "'/>";
			$("#thumbnail_prev").append(temp);
			
			img.attr('src', e.target.result);
		}
		
		reader.readAsDataURL(file[0].files[0]);
	}
	
	//img.attr("src", file.val());
}

function writeSpot(){
	var cate = '';
	
	$(".cate_stack").each(function(){
		cate += $(this).val() + ','
	});
	
	var name = $("#spot_name").val();
	var content = $("#summernote").val();
	
	var imgFile = $("#spot_file");
	var img = "";
	
	if (imgFile[0].files && imgFile[0].files[0])
		img = imgFile[0].files[0];
	
	if (img == '')
		alert("대표 이미지를 등록해주세요.");
	else{
		var latitude = $("#spot_latitude").val();
		var longitude = $("#spot_longitude").val();
		var base_idx = $("#base_idx").val();
		
		var data = new FormData();
		
		data.append("img", img);
		data.append("cate", cate);
		data.append("latitude", latitude);
		data.append("longitude", longitude);
		data.append("base", base_idx);
		data.append("name", name);
		data.append("content", content);
		
		$.ajax({
			url: adr_ctr + "Spot/write",
			async: false,
			data: data,
			type: 'post',
			cache: false,
			processData: false,
			contentType: false,
			success: function(result){
				result = JSON.parse(result);
				
				if (result.code == 200){
					alert("Spot이 생성되었습니다.");
					checkWrite = true;
					location.href = adr_ctr + 'Spot/index?idx=' + result.data;
				}
				else if (result.code == 240)
					alert(result.msg);
				else{
					alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
					alert('Spot 생성 중에 오류가 발생했습니다.\n서버 관리자에게 문의하세요.');
				}
			},
			error: function(request, status, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}

function updateSpot(){
	var cate = '';
	
	$(".cate_stack").each(function(){
		cate += $(this).val() + ','
	});
	
	var name = $("#spot_name").val();
	var content = $("#summernote").val();
	
	var imgFile = $("#spot_file");
	var img = "";
	
	if (imgFile[0].files && imgFile[0].files[0])
		img = imgFile[0].files[0];
	
	var description = $("#spot_description").val();
	
	var prev_thumb = $("#prev_spot_img").val();
	var spot_idx = $("#spot_idx").val();
	
	var data = new FormData();
	
	data.append("prev_thumb", prev_thumb);
	data.append("img", img);
	data.append("cate", cate);
	data.append("idx", spot_idx);
	data.append("name", name);
	data.append("content", content);
	data.append("description", description);
	data.append("prev_img", prevImg);
	
	$.ajax({
		url: adr_ctr + "Spot/update",
		async: false,
		data: data,
		type: 'post',
		cache: false,
		contentType: false,
		processData: false,
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200){
				alert("Spot이 수정되었습니다.");
				checkWrite = true;
				location.href = adr_ctr + 'Spot/index?idx=' + spot_idx;
			}
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert("Spot 수정에 실패했습니다.\n서버 관리자에게 문의하세요.");
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}