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
						<input type="hidden" id="ad_chk" value="<?=$ad?>"/>
						<table>
							<tr>
								<td colspan="2"><span class="red">*</span> 표시는 필수 입력 항목입니다.</td>
							</tr>
							<tr>
								<td class="fw_b">프로필사진</td>
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
								<td class="fw_b"><span class="red">*</span>아이디</td>
								<td><input type="text" id="id"/><span id="id_msg" class="msg"></span></td>
							</tr>
							<tr>
								<td class="fw_b"><span class="red">*</span>비밀번호</td>
								<td><input type="password" id="pw"/><span id="pw_msg" class="msg"></span></td>
							</tr>
							<tr>
								<td class="fw_b"><span class="red">*</span>비밀번호 확인</td>
								<td><input type="password" id="pwc"/><span id="pwc_msg" class="msg"></span></td>
							</tr>
							<tr>
								<td class="fw_b"><span class="red">*</span>닉네임</td>
								<td><input type="text" id="nickname"/><span id="nickname_msg" class="msg"></span></td>
							</tr>
							<tr>
								<td class="fw_b">이메일</td>
								<td>
									<input type="text" id="email1"/>
									@
									<input type="text" id="email2"/>
									<select id="email_selector">
										<option value="hanyang.ac.kr">hanyang.ac.kr</option>
										<option value="gmail.com">gmail.com</option>
										<option value="nate.com">nate.com</option>
										<option value="naver.com">naver.com</option>
										<option value="" selected>직접입력</option>
									</select>
									<span id="email_msg" class="msg"></span>
								</td>
							</tr>
							<tr>
								<td class="fw_b">이름</td>
								<td><input type="text" id="name"/></td>
							</tr>
							<tr>
								<td class="fw_b">성별</td>
								<td>
									<input type="radio" name="sex" value="man"/>남자&nbsp;&nbsp;
									<input type="radio" name="sex" value="woman"/>여자&nbsp;&nbsp;
								</td>
							</tr>
							<tr>
								<td class="fw_b">연령대</td>
								<td>
									<input type="radio" name="age" value="10"/>10대&nbsp;&nbsp;
									<input type="radio" name="age" value="20s"/>20대 초반&nbsp;&nbsp;
									<input type="radio" name="age" value="20m"/>20대 중반&nbsp;&nbsp;
									<input type="radio" name="age" value="20l"/>20대 후반&nbsp;&nbsp;
									<input type="radio" name="age" value="30"/>30대 이상&nbsp;&nbsp;
								</td>
							</tr>
							<tr class="ta_c">
								<td colspan="2">
									<button type="button" class="tw_btn48" onclick="justJoin()">확인</button>
									<button type="button" class="tw_btn48" onclick="history.back()">취소</button>
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
