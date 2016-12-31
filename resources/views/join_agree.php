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
						<h1>회원가입</h1>
					</div>
					<div class="core">
						<input type="hidden" id="prev" value="<?=$prev?>"/>
						<input type="hidden" id="kind" value="<?=$kind?>"/>
						<input type="hidden" id="no" value="<?=$no?>"/>
						<table>
							<tr>
								<td><a href="<?=$adr_ctr?>Main/terms" target="_blank" class="fw_b tw_color1">맛위키 이용약관</a> 동의(필수)</td>
								<td><input type="checkbox" id="terms_chk"/></td>
							</tr>
							<tr>
								<td><a href="<?=$adr_ctr?>Main/privacyPolicy" target="_blank" class="fw_b tw_color1">맛위키 개인정보 수집 및 이용</a> 동의(필수)</td>
								<td><input type="checkbox" id="pp_chk"/></td>
							</tr>
							<tr>
								<td>이벤트 등 정보성 메일 수신(선택)</td>
								<td><input type="checkbox" id="ad_chk"/></td>
							</tr>
						</table>
						<div class="mg_t16 ta_c">
							<button type="button" class="tw_btn48 mg_a8" onclick="checkAgree();">확인</button>
							<button type="button" class="tw_btn48" onclick="history.back();">취소</button>
						</div>
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