<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
<meta name="keywords" content="맛집 왕십리 리뷰 위키 review wiki"/>
<meta name="description" content="왕십리 맛집의 모든 것"/>
<meta name="author" content=""/>
<meta name="robots" content="index,follow"/>
<meta name="csrf-token" content="<?php echo csrf_token();?>"/>
<meta name="naver-site-verification" content="4a3869f0154ebabcd002132de51d1e22358b56a4"/>

<?php
	$host = $_SERVER['SERVER_NAME'];
	$port = '8000';
	$protocol = ($host === 'localhost')? 'http://': 'https://';
	
	$host .= ($host === 'localhost')? ':'.$port: '';

	$adr_ctr = $protocol.$host.'/';
	$adr_js = $adr_ctr.'js/';
	$adr_css = $adr_ctr.'css/';
	$adr_img = $adr_ctr.'img/';
	$adr_s3 = 'https://s3-ap-northeast-2.amazonaws.com/locawiki/';
	$adr_btstrp = $adr_ctr.'bootstrap/';
?>

<?php if (preg_match('/(facebook|kakaotalk)/', $_SERVER['HTTP_USER_AGENT']) == true) :?>
<meta property="og:type" content="website"/>
<meta property="og:title" content="맛위키 - 메인"/>
<meta property="og:url" content="<?=$adr_ctr?>"/>
<meta property="og:description" content="왕십리 맛집의 모든 것"/>
<meta property="og:image" content="<?=$adr_img?>thumbnail.png">
<meta property="og:site_name" content="맛위키"/>
<meta property="fb:app_id" content="591019087733073"/>
<?php elseif (preg_match('/Twitter/', $_SERVER['HTTP_USER_AGENT']) == true) :?>
<meta property="twitter:card" content="summary"/>
<meta property="twitter:title" content="맛위키 - 메인"/>
<meta property="twitter:url" content="<?=$adr_ctr?>"/>
<meta property="twitter:description" content="왕십리 맛집의 모든 것"/>
<meta property="twitter:image" content="<?=$adr_img?>thumbnail.png"/>
<?php else :?>
<!-- impl. -->
<?php endif;?>

<?php if (!isset($_SERVER['HTTP_REFERER'])) :?>
<input type="hidden" id="t_ref" value="NULL"/>
<?php else :?>
<input type="hidden" id="t_ref" value="<?=$_SERVER['HTTP_REFERER']?>"/>
<?php endif;?>

<input type="hidden" id="adr_js" value="<?=$adr_js?>"/>
<input type="hidden" id="adr_css" value="<?=$adr_css?>"/>
<input type="hidden" id="adr_img" value="<?=$adr_img?>"/>
<input type="hidden" id="adr_s3" value="<?=$adr_s3?>"/>
<input type="hidden" id="adr_ctr" value="<?=$adr_ctr?>"/>

<!-- 모바일 환경 확인 -->
<?php 
	$is_mobile = preg_match("/(android|avantgo|iphone|ipad|ipod|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
?>

<?php if ($is_mobile) :?>
<input type="hidden" id="is_mobile" value="1"/>
<?php else :?>
<input type="hidden" id="is_mobile" value="0"/>
<?php endif;?>
  	
<!-- 로그인 관련 세션  -->
<?php
	if (session_id() == '')
		session_start();
	
	$logined = !empty($_SESSION['idx']);
	
	if ($logined){
		$session_nickname = $_SESSION['nickname'];
		$session_img = $_SESSION['img'];
	}
?>

<?php if ($logined) :?>
<input type="hidden" id="logined" value="1"/>
<?php else :?>
<input type="hidden" id="logined" value="0"/>
<?php endif;?>

<link rel="stylesheet" href="<?=$adr_btstrp?>css/bootstrap.css">
<link rel="stylesheet" href="<?=$adr_btstrp?>css/bootstrap-theme.css">
<link rel="stylesheet" href="<?=$adr_css?>carousel.css">

<script type="text/javascript" src="<?=$adr_btstrp?>js/jquery-1.11.3.min.js"></script>

<script type="text/javascript">
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});
</script>

<script type="text/javascript" src="<?=$adr_btstrp?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$adr_btstrp?>js/ie-emulation-modes-warning.js"></script>
<script type="text/javascript" src="<?=$adr_btstrp?>js/ie10-viewport-bug-workaround.js"></script>

<!-- login 관련 js -->
<script type="text/javascript" src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.2.js"></script>
<script type="text/javascript" src="<?=$adr_js?>naverLogin.js"></script>
<script type="text/javascript" src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript" src="//connect.facebook.net/ko_KR/sdk.js"></script>

<!-- 전체 공통 js, css -->
<link rel="stylesheet" href="<?=$adr_css?>common.css">
<script type="text/javascript" src="<?=$adr_js?>common.js"></script>

<!-- Account 관련 css, js ; Account 관련 페이지일 때 사용-->
<?php if ($page == 'login' || $page == 'join' || $page == 'social_additional' || $page == 'mypage_profile') :?>
<link rel="stylesheet" href="<?=$adr_css?>account_common.css">
<script type="text/javascript" src="<?=$adr_js?>account_common.js"></script>
<?php endif;?>

<!-- page 관련 css, js -->
<input type="hidden" id="page" value="<?=$page?>"/>
<link rel="stylesheet" href="<?=$adr_css?><?=$page?>.css">
<script type="text/javascript" src="<?=$adr_js?><?=$page?>.js"></script>

<!-- header,footer 관련 css, js -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

<!-- brower_logo.png" -->
<title>맛위키 -&nbsp;</title>
