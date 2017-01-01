<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
	</head>
	<body>
		<div class="wrap">
			<header>
			<?php include ("header.php")?>
			</header>
			<div class="contents">
				<nav></nav>
				<div class="content">
					<div class="top">
						<h1>메일 인증</h1>
					</div>
					<div class="core">
						<input type="hidden" id="email" value="<?=$mail?>"/>
						<table>
							<tr>
								<td colspan="2"><span id="desc_text"></span></td>
							</tr>
							<tr>
								<td><input type="text" id="verify_code"/></td>
								<td><a href="<?=$adr_ctr?>Account/mailVerifyIndex?mail=<?=$mail?>"><i class='fa fa-repeat'></i></a><span id="remain_time" class="gray mg_l8"></span></td>
							</tr>
							<tr>
								<td class="ta_c">
									<button type="button" class="tw_btn48" onclick="checkVerify();">확인</button>
									<button type="button" class="tw_btn48" onclick="history.back();">취소</button>
								</td>
								<td></td>
							</tr>
						</table>
					</div>
				</div>
				<aside></aside>
			</div>
			<footer>
			<?php include ("footer.php")?>
			</footer>
		</div>
	</body>
</html>