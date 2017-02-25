<div class="header_wrap">
	<div class="menu">
		<a href="<?=$adr_ctr?>"><div class="f_l mg_r11"><img class="logo_img" src="<?=$adr_img?>title.png"/></div></a>
		<a onclick="toggleCateSelector()"><div id="dropdown_menu" class="menu_item f_l">맛지도 ▼</div></a>
		<a href="<?=$adr_ctr?>Community/index"><div class="menu_item f_l">커뮤니티</div></a>
		<!-- li><a href="#">내게딱맛</a></li -->
		<!-- li><a href="#">마이맛집</a></li -->

		<a onclick="toggleUserMenu()"><div id="dropdown_user" class="menu_user f_r"><img class="img_32" src="<?=$adr_s3?>profile/<?php echo (($logined) ? $session_img : "default.png")?>"/></div></a>
	</div>
</div>
<div id="dropdown_cate_selector" hidden></div>
<div id="dropdown_user_menu" hidden></div>
