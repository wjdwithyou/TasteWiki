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
						<h1>Spot 수정내역</h1>
					</div>
					<div>
						<input type="hidden" id="spot_idx" value="<?=$spot_idx?>"/>
						<table class="history_list_table">
						<?php for ($i = count($data) - 1; $i >= 0; --$i) :?>
							<tr>
								<td class="history_list_td" width="50px"><?=$i + 1?></td>
								<td class="history_list_td" width="150px"><?=$data[$i]->modifydate?></td>
								<td class="history_list_td" width="150px"><a href="#"><?=$data[$i]->nickname?></a></td>
								<td class="history_list_description" width="550px"><?=$data[$i]->description?></td>
								<td class="history_list_td" width="50px"><a onclick="showPrev(<?=$i + 1?>);">보기</a></td>
							</tr>
						<?php endfor;?>
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