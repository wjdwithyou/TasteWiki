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
						<h1>Wishlist</h1>
					</div>
					<div>
						<table>
						<?php if ($data['code'] === 1) :?>
							<?php foreach($data['data'] as $idx => $i) :?>
								<tr>
									<td><?=$idx + 1?></td>
									<td><img src="<?=$adr_s3?>spot/<?=$i->img?>" width="75" height="75"/></td>
									<td><a href="<?=$adr_ctr?>Spot/index?idx=<?=$i->spot_idx?>"><?=$i->name?></a></td>
									<td><button type="button" onclick="deleteWishlist(<?=$i->idx?>)">삭제</button></td>
								</tr>
							<?php endforeach?>
						<?php else :?>
							<tr>
								<td>
									Wishlist가 없습니다.
								</td>
							</tr>
						<?php endif;?>
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