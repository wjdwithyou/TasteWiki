<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
	</head>
	<body>
		<div class="wrap">
			<header>
			<?php include ("header.php");?>
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
								<td>
									<!--span id="desc_text"></span-->
									<p>
										인증 메일이 <strong><?=$mail?></strong>으로 발송되었습니다.<br/>
										메일을 확인하시고 메일 내용 중 <strong>[이메일 인증하기]</strong> 버튼을 클릭해 주시기 바랍니다.<br/>
										메일 계정에 따라 메일 발송시간이 지연될 수 있습니다.
									</p>
								</td>
							</tr>
							<tr>
								<td class="ta_c">
									<button type="button" class="tw_btn48" onclick="history.back();">확인</button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<aside></aside>
			</div>
			<footer>
			<?php include ("footer.php");?>
			</footer>
		</div>
	</body>
</html>
