<div class="header_wrap">
	<div class="menu">
		<a href="<?=$adr_ctr?>"><div class="f_l mg_r11"><img class="logo_img" src="<?=$adr_img?>title.png"/></div></a>
		<a onclick="toggleCateSelector()"><div id="dropdown_menu" class="menu_item f_l">맛지도 ▼</div></a>
		<a href="<?=$adr_ctr?>Community/index"><div class="menu_item f_l">커뮤니티</div></a>
		<!-- li><a href="#">내게딱맛</a></li -->
		<!-- li><a href="#">마이맛집</a></li -->
		
		<?php if ($logined) :?>
		<a onclick="logout();"><div class="menu_item f_r">로그아웃</div></a>
		<a href="<?=$adr_ctr?>Mypage/index"><div class="header_nick menu_item f_r"><img class="img_16" src="<?=$adr_s3?>profile/<?=$session_img?>"/>&nbsp;<?=$session_nickname?>님</div></a>
		<?php else :?>
		<a href="<?=$adr_ctr?>Account/loginIndex"><div class="menu_item f_r">로그인</div></a>
		<?php endif;?>
	</div>
</div>
<div id="dropdown_cate_selector" hidden></div>