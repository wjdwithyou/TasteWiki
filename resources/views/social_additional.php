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
					<div id="top">
						<h1>추가정보 입력</h1>
					</div>
					<div>
						<input type="hidden" id="prev" value="<?=$prev?>"/>
						<input type="hidden" id="kind" value="<?=$kind?>"/>
						<input type="hidden" id="no" value="<?=$no?>"/>
						<table>
							<tr>
								<td>프로필사진</td>
								<td>
									<table>
										<tr>
											<td><img src="<?=$adr_s3?>profile/default.png" id="profile_img" height="100" width="100"/>
										</tr>
									</table>
									<input type="file" id="profile_file" accept="image/*" onchange="showPreview($(this));"/>
								</td>
							</tr>
							<tr>
								<td>닉네임</td>
								<td><input type="text" id="nickname"/><span id="nickname_msg"></span></td>
							</tr>
							<tr>
								<td>성별</td>
								<td>
									<input type="radio" name="sex" value="man"/>남자&nbsp;&nbsp;
									<input type="radio" name="sex" value="woman"/>여자&nbsp;&nbsp;
								</td>
							</tr>
							<tr>
								<td>연령대</td>
								<td>
									<input type="radio" name="age" value="10"/>10대&nbsp;&nbsp;
									<input type="radio" name="age" value="20s"/>20대 초반&nbsp;&nbsp;
									<input type="radio" name="age" value="20m"/>20대 중반&nbsp;&nbsp;
									<input type="radio" name="age" value="20l"/>20대 후반&nbsp;&nbsp;
									<input type="radio" name="age" value="30"/>30대 이상&nbsp;&nbsp;
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<button type="button" onclick="socialJoin()">확인</button>
									<button type="button" onclick="history.back()">취소</button>
								</td>
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