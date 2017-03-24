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
						<h1>메일 인증 완료</h1>
					</div>
					<div class="core">
						<p>
							<?=$msg?>
						</p>
                        <button type="button" class="tw_btn48" onclick="location.href='https://www.tastewiki.xyz/Main/index'">확인</button>
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
