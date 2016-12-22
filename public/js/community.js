$(document).ready(function(){
	var page_num = $("#page_num").val();
	
	getCommList(page_num);
	
	
	
	// TODO: search enter 13
});

function changeCate(num){
	$("#cm_cate").val(num);
	
	for (var i = 0; i < $("#cm_cate_btn_num").val(); ++i){
		if (i == num)
			$("#cm_cate_btn_" + i).addClass('clicked');
		else
			$("#cm_cate_btn_" + i).removeClass('clicked');
	}
	
	getCommList(1);
}

function getCommList(page_num){
	var pattern = /[^0-9a-zA-Zㄱ-ㅎ|ㅏ-ㅣ|가-힇]/;
	
	var cate = $("#cm_cate").val();
	
	var searchType = $("#cm_search_type").val();
	var searchText = $("#cm_search_text").val();
	
	if (pattern.test(searchText))
		alert("한글, 영문, 숫자로만 검색가능합니다.");
	else{
		$.ajax({
			url: adr_ctr + 'Community/getList',
			async: false,
			data: {
				cate: cate,
				searchType: searchType,
				searchText: searchText,
				page_num: page_num,
			},
			type: 'post',
			success: function(result){
				$("#cm_list").html(result);
			},
			error: function(request, status, error){
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}

function iWriteComm(){
	if (loginStateCheck() != 1)
		alert("로그인이 필요한 기능입니다.");
	else
		location.href = adr_ctr + "Community/writeIndex";
}

function openComm(idx){
	// impl.
	
	location.href = adr_ctr + "Community/detail?idx=" + idx;
}