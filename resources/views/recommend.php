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
						<h1>추천 Spot</h1>
					</div>
					<div>
					<?php if ($cnt == 0) :?>
						<?php for ($i = 0; $i < 3; ++$i) :?>
							<td>
								<table>
									<tr>
										<td rowspan="3">정보가 부족해요!</td>
									</tr>
								</table>
							</td>
						<?php endfor;?>
					<?php else :?>
						<table>
							<tr>
							<?php for ($i = 0; $i < count($spot_info); ++$i) :?>
								<td>
									<table>
										<tr>
											<td><img src="<?=$adr_s3?>spot/<?=$spot_info[$i]['data']->img?>" height="100" width="100"/></td>
										</tr>
										<tr>
											<td><a href="<?=$adr_ctr?>Spot/index?idx=<?=$spot_info[$i]['data']->idx?>"><?=$spot_info[$i]['data']->name?></a></td>
										</tr>
										<tr>
											<td><?php echo sprintf("%.2f", $spot_info[$i]['rating'])?></td>
										</tr>
									</table>
								</td>
							<?php endfor;?>
							<?php for ($i = count($spot_info); $i < 3; ++$i) :?>
								<td>
									<table>
										<tr>
											<td rowspan="3">정보가 부족해요!</td>
										</tr>
									</table>
								</td>
							<?php endfor;?>
							</tr>
						</table>
					<?php endif;?>
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