<div class="header_wrap">
	<div class="menu">
		<a href="<?=$adr_ctr?>"><div class="f_l mg_r11"><img class="logo_img" src="<?=$adr_img?>title.png"/></div></a>
		<div class="pc_header">
			<a onclick="toggleCateSelector()"><div class="menu_item96 f_l dropdown_menu"><i class="fa fa-globe" aria-hidden="true"></i>&nbsp;맛지도&nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></div></a>
			<a href="<?=$adr_ctr?>Community/index"><div class="menu_item96 f_l"><i class="fa fa-comments-o" aria-hidden="true"></i>&nbsp;커뮤니티</div></a>
		</div>
		<div class="m_header">
			<a onclick="toggleCateSelector()"><div class="menu_item40 f_l dropdown_menu"><i class="fa fa-globe fa-lg" aria-hidden="true"></i>&nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i></div></a>
			<a href="<?=$adr_ctr?>Community/index"><div class="menu_item40 f_l"><i class="fa fa-comments-o fa-lg" aria-hidden="true"></i></div></a>
		</div>
		<!-- li><a href="#">내게딱맛</a></li -->
		<!-- li><a href="#">마이맛집</a></li -->
		<a onclick="toggleUserMenu()"><div id="dropdown_user" class="menu_item40 f_r"><img class="img_32" src="<?=$adr_s3?>profile/<?php echo (($logined) ? $session_img : "default.png")?>"/></div></a>
	</div>
</div>
<div id="dropdown_cate_selector" hidden></div>
<div id="dropdown_user_menu" hidden></div>
