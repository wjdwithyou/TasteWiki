<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script>
			document.title += '<?=$data->name?>';

			$('meta[name="og:title"]').attr('content', document.title);
			$('meta[name="og:description"]').attr('content', '<?=$data->content?>');
			
			$('meta[name="twitter:title"]').attr('content', document.title);
			$('meta[name="twitter:description"]').attr('content', '<?=$data->content?>');

			<?php if (!$is_history) :?>
				$('meta[name="og:image"]').attr('content', '<?=$adr_s3?>spot/<?=$data->img?>');
				$('meta[name="twitter:image"]').attr('content', '<?=$adr_s3?>spot/<?=$data->img?>');
			<?php endif;?>
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
					<div class="top">
					<?php if (!$is_history) :?>
						<h1><?=$data->name?></h1>
						<p align="right">
							<button type="button" onclick="addWishlist();">찜하기</button>
							<button type="button" onclick="showHistory();">역사</button>
							<button type="button" onclick="modifySpot();">수정</button>
							<!-- button type="button" onclick="deleteSpot();">삭제</button -->
						</p>
						<p align="right">마지막 수정: <?=$data->lastdate?><br/>hit: <?=$data->hit_cnt?></p>
					<?php else :?>
						<div class="notice mg_b8">최신 버전이 아닌 이미지 파일은 따로 저장하지 않으므로 본 항목에 표시되지 않을 수 있습니다.</div>
						<h1><?=$data->name?> (<?=$ver?>번째 버전)</h1>
					<?php endif;?>
					</div>
					<?php if (!$is_history) :?>
						<div id="thumbnail">
							<img src="<?=$adr_s3?>spot/<?=$data->img?>" id="spot_img" height="100" width="100"/>
						</div>
						<div id="rating">
						<?php foreach($rating_kind as $i) :?>
							<?php $iter = 'rating_'.$i->eng_name;?>
							<img src="<?=$adr_img?>star_head.png" height="50" width="50"/><?=$i->name?>: <?=sprintf("%.2f",$data->$iter)?>
						<?php endforeach;?>
						</div>
					<?php endif;?>
					<div id="stack_wrap">
					<?php foreach($cate as $i) :?>
						#<?=$i?>&nbsp;
					<?php endforeach;?>
					</div>
					<div class="core">
					<?php if (!$is_history) :?>
						<input type="hidden" id="spot_idx" value="<?=$data->idx?>"/>
					<?php endif;?>
						<?=$data->content?>
					</div>
					<?php if (!$is_history) :?>
						<div class="review_wrap">
							<hr/>
							<div id="my_review" class="pd_a16"></div>
							<hr/>
							<div class="review_list pd_a16">
							<?php foreach ($review as $idx => $i) :?>
								<table>
									<tr>
										<td width="175px" rowspan="3">
											<img src="<?=$adr_s3?>profile/<?=$i->p_img?>" height="50" width="50"/>
											<br/>
											<a onclick=""><?=$i->nickname?></a>
										</td>
										<td width="550px">
										<?php foreach ($rating_kind as $j) :?>
											<?php $iter = 'rating_'.$j->eng_name;?>
											<?=$j->name?>
											<?php for ($k = 1; $k <= 5; ++$k) :?>
												<?php echo ($i->$iter >= $k)? '★': '☆';?>
											<?php endfor;?>
										<?php endforeach;?>
										</td>
										<td class="ta_c">
											<?=$i->lastdate?>
										</td>
									</tr>
									<tr>
										<td>
										<?=$i->content?>
										</td>
										<td></td>
									</tr>
									<tr>
										<td colspan="2">
										<?php foreach ($review_img[$idx] as $j) :?>
											<div class="review_img">
												<img src="<?=$adr_s3?>review/<?=$j?>"/>
											</div>
										<?php endforeach;?>
										</td>
									</tr>
								</table>
								<br/>
							<?php endforeach;?>
							</div>
						</div>
					<?php endif;?>
				</div>
				<aside></aside>
			</div>
			<footer>
			<?php include ("footer.php")?>
			</footer>
		</div>
	</body>
</html>