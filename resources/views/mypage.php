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
						<h1>MyPage</h1>
					</div>
					<div class="list">
						<div class="element">
							<a href="<?=$adr_ctr?>Mypage/profileIndex">
								<h3><strong>Profile</strong>내 정보</h3>
								<p>회원님의 개인정보를 관리할 수 있습니다.</p>
							</a>
						</div>
						<div class="element">
							<a href="<?=$adr_ctr?>Mypage/wishlistIndex">
								<h3><strong>Wishlist</strong>관심 Spot</h3>
								<p>회원님께서 찜하신 Spot의 목록을 확인할 수 있습니다.</p>
							</a>
						</div>
						<div class="element">
							<a href="<?=$adr_ctr?>Mypage/reviewIndex">
								<h3><strong>My Review</strong>내가 쓴 리뷰</h3>
								<p>회원님께서 작성하신 리뷰를 한 눈에 확인할 수 있습니다.</p>
							</a>
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