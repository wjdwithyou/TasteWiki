/*
 * naver
 */
var naver = NaverAuthorize({
	client_id		: "TZzqmjuDCQgNvjijIGJP",
	redirect_uri	: adr_ctr + "Account/naverCallback",
	client_secret	: "CiPwfNfd7L"
});

function naverLogin(){	// temp
	if (adr_ctr === 'https://www.tastewiki.xyz/')
		naver.login("stateChk");
	else
		alert("네이버 로그인 점검 중입니다.\n다른 로그인 방식을 이용해주세요.");
}



/*
 * katalk 
 */
Kakao.init('19ed7ee367680cac83be7b780f1f06fe');

function katalkLogin(){
	Kakao.Auth.login({
    	success: function(authObj){
    		Kakao.API.request({
    	        url: '/v1/user/me',
    	        success: function(res){
    	        	//alert(JSON.stringify(res));
    	        	var kind = 'katalk';
    	        	var id = res.id;
    	        	var pw = res.id;
    	        	//var nickname = res.properties.nickname;
    	        	//var email = "";
    	        	//var img = res.properties.profile_image;
    	        	
    	        	commonLogin(kind, id, pw);
    	        	//socialLogin(type, id, email, nickname, img);
    	        },
    	        fail: function(error){
    	        	alert(JSON.stringify(error));
    	        }
    	    });
    	},
    	fail: function(err){
      		alert(JSON.stringify(err));
    	},
  	});
}



/*
 * facebook
 */
FB.init({
	appId      : '591019087733073',
	cookie     : true,
	xfbml      : true,
	version    : 'v2.5',
	language   : 'ko_KR'
});

function facebookLogin(){
	FB.getAuthResponse();
	
	FB.login(function(res){
		if (res.status == 'connected'){
			FB.api('/me', {locale : 'ko_KR'}, function(res){
				var kind = 'facebook';
	        	var id = res.id;
	        	var pw = res.id;
	        	//var nickname = "";
	        	//var email = res.email;
	        	
	        	commonLogin(kind, id, pw);
	        	
	        	/*
	        	FB.api('/me/picture', function(res){
	        		var img = res.data.url;
					socialLogin(type, id, email, nickname, img);
				});
				*/
			});
		}
	}/*, {scope: 'public_profile, email'}*/);
}



/*
 * just
 */
function justLogin(){
	var kind = 'just';
	
	var id = $("#id").val();
	var pw = $("#pw").val();
	
	if (id.length == 0)
		alert("아이디를 입력해주세요.");
	else if (pw.length == 0)
		alert("비밀번호를 입력해주세요.");
	else
		commonLogin(kind, id, pw);
}

function join(){
	location.href = adr_ctr + 'Account/agreeTerms?kind=just';
}
