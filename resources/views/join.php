<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script>
			document.title += '회원가입';
	
			$('meta[name="og:title"]').attr('content', document.title);
			$('meta[name="twitter:title"]').attr('content', document.title);
		</script>
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
						<h1>회원가입</h1>
					</div>
					<div>
						<input type="hidden" id="kind" value="just"/>
						<table>
							<tr>
								<td>프로필사진</td>
								<td>
									<table>
										<tr>
											<td><img src="<?=$adr_s3?>profile/default.png" id="profile_img" height="100" width="100"/></td>
										</tr>
									</table>
									<input type="file" id="profile_file" accept="image/*" onchange="showPreview($(this));"/>
								</td>
							</tr>
							<tr>
								<td>아이디</td>
								<td><input type="text" id="id"/><span id="id_msg"></span></td>
							</tr>
							<tr>
								<td>비밀번호</td>
								<td><input type="password" id="pw"/><span id="pw_msg"></span></td>
							</tr>
							<tr>
								<td>비밀번호 확인</td>
								<td><input type="password" id="pwc"/><span id="pwc_msg"></span></td>
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
									<button type="button" onclick="justJoin()">가입하기</button>
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