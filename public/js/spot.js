$(document).ready(function(){
	getMyReview(0);
	
	$(".review_file_wrap").on('click', function(){
		if (loginStateCheck() != 1)
			alert('로그인이 필요한 기능입니다.');
	});
});

function getMyReview(idx){
	var logined = loginStateCheck();
	
	var spot_idx = $("#spot_idx").val();
	
	$.ajax({
		url: adr_ctr + 'Spot/getMyReview',
		async: false,
		data:{
			logined: logined,
			adr_s3: adr_s3,
			spot_idx: spot_idx,
			review_idx: idx
		},
		type: 'post',
		success: function(result){
			$("#my_review").html(result);
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function addWishlist(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	var spot_idx = $("#spot_idx").val();
	
	$.ajax({
		url: adr_ctr + 'Mypage/addWishlist',
		async: false,
		data: {
			idx: spot_idx
		},
		type: 'post',
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200)
				alert('Wishlist에 추가되었습니다.');
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert('Wishlist 추가에 실패했습니다.\n서버 관리자에게 문의하세요.');
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function showHistory(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	var spot_idx = $("#spot_idx").val();
	
	location.href = adr_ctr + "Spot/historyIndex?idx=" + spot_idx;
}

function modifySpot(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	var spot_idx = $("#spot_idx").val();
	
	location.href = adr_ctr + "Spot/writeIndex?idx=" + spot_idx;
}

function deleteSpot(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	alert('그럴 순 없어요!');		// temp
	
	// impl.
}

function modifyStar(kind, num){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	for (var i = 1; i <= num; ++i)
		$("#review_" + kind + "_star" + i).text("★");
	for (var i = num + 1; i <= 5; ++i)
		$("#review_" + kind + "_star" + i).text("☆");
	
	$("#review_" + kind + "_num").val(num);
}

function showThumbnail(num){
	var img = $('#review_img' + num);
	var text = $('#file_name' + num);
	var file = $('#review_file' + num);
	
	if (window.FileReader && file[0].files && file[0].files[0]){
		var reader = new FileReader();
		
		reader.onload = function(e){
			var prev = img.attr('src');
			var temp = "<input type='hidden' id='prev_review_img" + num +"' value='" + prev + "'/>";
			$("#review_img_prev").append(temp);
			
			img.attr('src', e.target.result);
			text.val(file[0].files[0].name);
		}
		
		reader.readAsDataURL(file[0].files[0]);
	}
}

function writeReview(){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	var spot_idx = $("#spot_idx").val();
	var rating_taste = $("#review_taste_num").val();
	var rating_price = $("#review_price_num").val();
	var rating_service = $("#review_service_num").val();
	var rating_access = $("#review_access_num").val();
	var content = $("#review_content").val();
	
	var img = new Array("", "", "");
	
	for (var i = 0; i < 3; ++i){
		var imgFile = $("#review_file" + i);
		
		if (imgFile[0].files && imgFile[0].files[0])
			img[i] = imgFile[0].files[0];
	}
	
	var data = new FormData();
	
	data.append("idx", spot_idx);
	data.append("taste", rating_taste);
	data.append("price", rating_price);
	data.append("service", rating_service);
	data.append("access", rating_access);
	data.append("content", content);
	
	for (var i = 0; i < 3; ++i)
		data.append("img" + i, img[i]);
	
	$.ajax({
		url: adr_ctr + "Spot/writeReview",
		async: false,
		data: data,
		type: 'post',
		cache: false,
		processData: false,
		contentType: false,
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200){
				alert("리뷰 작성 완료!");
				location.href = adr_ctr + 'Spot/index?idx=' + spot_idx;
			}
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert("리뷰 작성에 실패했습니다.\n서버 관리자에게 문의하세요.");
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function iModifyReview(idx){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	getMyReview(idx);
}

function modifyReview(idx){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	var rating_taste = $("#review_taste_num").val();
	var rating_price = $("#review_price_num").val();
	var rating_service = $("#review_service_num").val();
	var rating_access = $("#review_access_num").val();
	var content = $("#review_content").val();
	
	var review_idx = idx;
	var spot_idx = $("#spot_idx").val();
	
	var img = new Array("", "", "");
	
	for (var i = 0; i < 3; ++i){
		var imgFile = $("#review_file" + i);
		
		if (imgFile[0].files && imgFile[0].files[0])
			img[i] = imgFile[0].files[0];
	}
	
	var prev_img0 = $("#prev_review_img0").val();
	var prev_img1 = $("#prev_review_img1").val();
	var prev_img2 = $("#prev_review_img2").val();
	
	var prev_img = new Array(prev_img0, prev_img1, prev_img2);
	
	var data = new FormData();
	
	data.append("review_idx", review_idx);
	data.append("spot_idx", spot_idx);
	data.append("taste", rating_taste);
	data.append("price", rating_price);
	data.append("service", rating_service);
	data.append("access", rating_access);
	data.append("content", content);
	
	for (var i = 0; i < 3; ++i){
		data.append("img" + i, img[i]);
		data.append("prev_img" + i, prev_img[i]);
	}
	
	$.ajax({
		url: adr_ctr + "Spot/updateReview",
		async: false,
		data: data,
		type: 'post',
		cache: false,
		processData: false,
		contentType: false,
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200){
				alert("리뷰 수정 완료!");
				location.href = adr_ctr + 'Spot/index?idx=' + spot_idx;
			}
			else if (result.code == 240)
				alert(result.msg);
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert("리뷰 수정에 실패했습니다.\n서버 관리자에게 문의하세요.");
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}
