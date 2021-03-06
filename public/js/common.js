var adr_ctr = $("#adr_ctr").val();
var adr_s3 = $("#adr_s3").val();

window.onload = function(){
	var window_height = $(window).height();
	var wrap_height = $(".wrap").height();

	if (wrap_height >= window_height)
		return;

	var header_height = $(".header_wrap").parent().outerHeight();
	var footer_height = $(".footer_wrap").parent().outerHeight();

	var contents_height = window_height - (header_height + footer_height);

	$(".contents").height(contents_height + "px");
};

function toggleCateSelector() {
	if ($("#dropdown_cate_selector").is(":visible")) {
		$(".dropdown_menu").removeClass("fixed");
		$("#dropdown_cate_selector").slideUp();
	} else{
		$.ajax({
			url: adr_ctr + 'Main/cateSelector',
			async: false,
			type: 'post',
			success: function(result) {
				if ($("#dropdown_user_menu").is(":visible")) {
					toggleUserMenu();
				}

				$(".dropdown_menu").addClass("fixed");
				$("#dropdown_cate_selector").html(result);
				$("#dropdown_cate_selector").slideDown();
			},
			error: function(request, response, error) {
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}

function showCategorizedSpot() {
	var purpose = $("#cate_search_purpose").val();
	var kind = $("#cate_search_kind").val();

	location.href = adr_ctr + "Main/index?p=" + purpose + "&k=" + kind;
}

function toggleUserMenu() {
	if ($("#dropdown_user_menu").is(":visible")) {
		$("#dropdown_user").removeClass("fixed");
		$("#dropdown_user_menu").slideUp();
	} else {
		var logined = loginStateCheck();

		$.ajax({
			url: adr_ctr + 'Main/userMenu',
			async: false,
			data:{
				logined: logined,
				adr_ctr: adr_ctr
			},
			type: 'post',
			success: function(result) {
				if ($("#dropdown_cate_selector").is(":visible")) {
					toggleCateSelector();
				}

				$("#dropdown_user").addClass("fixed");
				$("#dropdown_user_menu").html(result);
				$("#dropdown_user_menu").slideDown();
			},
			error: function(request, response, error) {
				console.log(request.responseText);
			    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
}

function commonLogin(kind, id, pw){
	var prev = $("#prev").val();

	$.ajax({
		url: adr_ctr + 'Account/login',
		async: false,
		data: {
			kind: kind,
			id: id,
			pw: pw
		},
		type: 'post',
		success: function(result){
			try{
				result = JSON.parse(result);
			} catch (exception){}

			if (result.code == 200){
				location.href = prev;
			}
			else if(result.code == 240){
				if (kind == 'just'){
					alert(result.msg);

					$('#pw').val('');
				}
				else{
					location.href = adr_ctr + 'Account/agreeTerms?kind=' + kind + '&no=' + id + '&prev=' + prev;
				}
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

function logout(){
	$.ajax({
		url: adr_ctr + 'Account/logout',
		async: false,
		type:'post',
		success: function(result){
			location.href = adr_ctr + 'Main/index';
		},
		error: function(request, response, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function loginStateCheck(){
	var logined = $("#logined").val();

	return logined;
}

function getError(code){
	switch (code){
		case 400: return 'Bad Request';
		case 401: return 'Unauthorized';
		case 403: return 'Forbidden';
		case 404: return 'Not Found';
		case 500: return 'Internal Server Error';
		default: return 'Unknown';
	}
}

/*
function moveLogin(){
	var prev_page = $("#page").val();

	// if login exception impl

	location.href = adr_ctr + 'Account/loginIndex?prev=' + prev_page;
}
*/

/*
function moveLogin(page){
	var query = '';

	if (page == 'login')
		query += '?prev=' + $('#prev_url').val();

	var url = adr_ctr + 'Login/index' + query;
	$(location).attr('href',url);
}
*/
