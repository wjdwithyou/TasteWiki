<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script>
			document.title += '로그인';
	
			$('meta[name="og:title"]').attr('content', document.title);
			$('meta[name="twitter:title"]').attr('content', document.title);
		</script>
		<!-- link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous" -->
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
						<h1 class="ta_c">로그인</h1>
					</div>
					<div class="core">
						<input type="hidden" id="prev" value="<?=$prev?>"/>
						<table>
							<tr>
								<td class="title"><label for="id" class="control-label">ID</label></td>
								<td><input type="text" class="form-control" id="id" name="id" placeholder="" tabindex="1"/></td>
								<td rowspan="2" class="tabl_btn"><button type="button" class="btn btn-default" onclick="justLogin();" tabindex="3">Login</button></td>
								<td rowspan="2" class="tabl_btn"><button type="button" class="btn btn-default" onclick="join();" tabindex="4">Join</button></td>
							</tr>
							<tr>
								<td class="title"><label for="inputPassword3" class="control-label">Password</label></td>
								<td><input type="password" class="form-control" id="pw" name="passwd" placeholder="" tabindex="2"/></td>		<!--onkeydown='enterCheck();'-->
							</tr>
							<tr>
								<td></td>
								<td colspan="3">
									<a onclick="naverLogin()"><img src="<?=$adr_img?>naver.png"/></a>
									<a onclick="katalkLogin()"><img src="<?=$adr_img?>katalk.png"/></a>
									<a onclick="facebookLogin()"><img src="<?=$adr_img?>facebook.png"/></a>
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