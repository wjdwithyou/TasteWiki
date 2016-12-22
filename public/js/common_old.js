$(document).ready(function(){
	if ($("#need_login").val() == "1")
		moveLogin();

	$(".menu_hover").mouseover(function() {
		$("#hover_menu_wrap").show();
		var temp_menuId = $(this).attr("id");
		temp_menuId = "#sb_menu" + temp_menuId.substring(9,10);
		$("#hover_menu_wrap").html($(temp_menuId).html());
	})
	.mouseout(function() {
		$("#hover_menu_wrap").hide();
	});

	$("#integrated_search").keyup(function(e){
		if (e.keyCode == 13)
			integrateSearch();
	});
});

var adr_ctr = $("#adr_ctr").val();
var adr_img = $("#adr_img").val();

//chrome, firefox 스크롤바 width 무시하기
function checkScrollBars() {
	var b = $('body');
	var normalw = 0;
	var scrollw = 0;
	if (b.prop('scrollHeight') > b.height()) {
		normalw = window.innerWidth;
		scrollw = normalw - b.width();
		$('body').css({
			marginRight : '-' + scrollw + 'px'
		});
	}
}

function toggleDialog(addr, n){
	$("#bo_dialog").css({"left": 0, "right": ""});
	$(".bo_dialog_arrow").css({"left": 0, "right": ""});
	$(".bo_dialog_arrow").css({"left": n});

	if($("#bo_dialog").is(":visible")){
		$("#bo_dialog_content").children().remove();
		$("#bo_dialog").hide();
	}
	else{
		loadDialog(addr);
		$("#bo_dialog").show();
	}
}

function loadDialog(addr){
	$("#bo_dialog_content").load(adr_ctr + "Main/" + addr, function(){
		$.ajax({
			url: adr_ctr + "js/" + addr + ".js",
			dataType: "script",
			cache: true
		});
	});
}

function toggleSearch(){
	$("#header_search").toggle();
}

function moveLogin(page){
	var query = '';

	if (page == 'login')
		query += '?prev=' + $('#prev_url').val();

	var url = adr_ctr + 'Login/index' + query;
	$(location).attr('href',url);
}

function moveMain(){
	$(location).attr('href', adr_ctr);
}

/*
 * 2015.11.17
 * 작성자 : 박용호
 * 로그아웃 -> 세션 파괴..
 */
function logout()
{
	$.ajax
	({
		url: adr_ctr+'Login/logout',
		type: 'post',
		async: false,
		success: function(result)
		{
			//console.log(result);
			//alert (result);
			//alert ("안녕히가세요 빠빠");
			location.reload(false);
		},
		error:function(request,status,error)
		{
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function toggleAsidemenu(){
	if($("#aside_menu_wrap").css("right") == "0px"){
		$("#aside_menu_wrap").css("right", -60);
		$("#aside_arrow img").attr("src", adr_img + "left_arrow.png");

	}
	else{
		$("#aside_menu_wrap").css("right", 0);
		$("#aside_arrow img").attr("src", adr_img + "right_arrow.png");
	}
}

function goBack(){
	window.history.back();
}

function integrateSearch()
{
	if (typeof getPrdt !== 'undefined' && $.isFunction(getPrdt))
		getPrdt('','',"1");
	else
	{
		var searchText = $("#integrated_search").val();
		location.href = adr_ctr + "Shoppingbox/index?search=" + searchText;
	}
}

function moveTop(){
	$("html, body").animate({scrollTop: 0}, 100);
}

function toggleExpand(e, t){
	if($("#aside_expand").is(":visible")){
		$("#aside_expand").hide();
		$("#mob_aside_bg").hide();
		$(".aside_div").children("i").css("color", "#8c8b8b");
		$(".aside_div").not(".aside_div_2").css("background-color", "#eee");
		e.children("i").css("color", "#8c8b8b");
	}
	else{
		if(t == 1){
			if ($("#logined").val() == 0){
				alert('로그인 후 이용해주세요.');
				return;
			}
			else
				expandBookmark();
		}
		else
			expandRecently();

		$("#aside_expand").show();
		$("#mob_aside_bg").height($("body").height());
		$("#mob_aside_bg").show();
		$(".aside_div").children("i").css("color", "#8c8b8b");
		e.css("background-color", "#fff");
		e.children("i").css("color", "#f15a63");
	}
}

// 160203 Modified by J.Style
// No comment.
function expandBookmark(){
	$.ajax({
		async: false,
		type: 'post',
		url: adr_ctr + 'Main/bookmark',
		success: function(result){
			$("#aside_expand").children().html(result);
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

// 160202 Modified by J.Style
// No comment.
function expandRecently(){
	$.ajax({
		async: false,
		type: 'post',
		url: adr_ctr + "Main/recently",
		success: function(result){
			$("#aside_expand").children().html(result);
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function clickLink(idx, url)
{
	$.ajax
	({
		url: adr_ctr+"Bestranking/hitCountPlus",
		type: 'post',
		async: false,
		data:{
			idx: idx
		},
		success: function(result)
		{
			window.open(url, "_blank");
			//alert (JSON.stringify(result));
			//result = JSON.parse(result);
		},
		error: function(request,status,error)
		{
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

// 160203 J.Style
// No comment.
function deleteBookmark(idx){
	//confirm("삭제하시겠습니까?");

	$.ajax({
		async: false,
		data: {
			idx: idx
		},
		type: 'post',
		url: adr_ctr + 'Main/deleteBookmark',
		success: function(result){
			result = JSON.parse(result);

			if (result.code == 1){
				alert('삭제되었습니다.');
				expandBookmark();
			}
			else
				alert('잘못된 접근입니다.');
		},
		error: function(request, status, error){
			console.log(request.responseText);
		    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function deleteRecently(){
	//confirm("삭제하시겠습니까?");
}
