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
			<?php include ("header.php");?>
			</header>
			<div class="contents">
				<nav></nav>
				<div class="content">
					<div class="top">
					<?php if (!$is_history) :?>
						<h1><?=$data->name?></h1>
						<p align="right">
							<button type="button" class="tw_btn64" onclick="addWishlist();">찜하기</button>
							<button type="button" class="tw_btn64" onclick="showHistory();">역사</button>
							<button type="button" class="tw_btn64" onclick="modifySpot();">수정</button>
							<!-- button type="button" onclick="deleteSpot();">삭제</button -->
						</p>
						<p align="right">마지막 수정: <?=$data->lastdate?><br/>hit: <?=$data->hit_cnt?></p>
					<?php else :?>
						<div class="notice mg_b8">최신 버전이 아닌 이미지 파일은 따로 저장하지 않으므로 본 항목에 표시되지 않을 수 있습니다.</div>
						<h1><?=$data->name?> (<?=$ver?>번째 버전)</h1>
					<?php endif;?>
					</div>
					<?php if (!$is_history) :?>
						<div class="thumb-rating mg_b16">
							<div id="thumbnail" class="f_l">
								<img id="spot_img" src="<?=$adr_s3?>spot/<?=$data->img?>"/>
							</div>
							<div class="rating">
								<div class="f_l">
								<?php foreach($rating_kind as $i) :?>
									<div class="pd_tb8">
										<i class="fa <?=$i->fa_name?>" aria-hidden="true"></i>
									</div>
								<?php endforeach;?>
								</div>
								<div class="f_l ta_c">
								<?php foreach($rating_kind as $i) :?>
									<div class="pd_a8">
										<?=$i->name?>
									</div>
								<?php endforeach;?>
								</div>
								<div class="f_l">
								<?php foreach($rating_kind as $i) :?>
									<?php $iter = 'rating_'.$i->eng_name;?>
									<div class="pd_tb8">
										:&nbsp;<?=sprintf("%.2f",$data->$iter)?>
									</div>
								<?php endforeach;?>
								</div>
							</div>
						</div>
					<?php endif;?>
					<div id="stack_wrap" class="mg_b16">
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
							<div id="my_review" class="pd_a16"></div>
							<div class="review_list">
							<?php foreach ($review as $idx => $i) :?>
								<div class="pd_tb16">
									<div class="review_first mg_b16">
										<div class="p-profile">
											<img src="<?=$adr_s3?>profile/<?=$i->p_img?>"/>
											<br/>
											<a onclick=""><?=$i->nickname?></a>
										</div>
										<div class="p-rating f_l">
											<div class="f_l">
											<?php foreach($rating_kind as $j) :?>
												<div class="pd_tb4">
													<i class="fa <?=$j->fa_name?>" aria-hidden="true"></i>
												</div>
											<?php endforeach;?>
											</div>
											<div class="f_l ta_c">
											<?php foreach($rating_kind as $j) :?>
												<div class="pd_a4">
													<?=$j->name?>
												</div>
											<?php endforeach;?>
											</div>
											<div class="f_l">
											<?php foreach($rating_kind as $j) :?>
												<?php $iter = 'rating_'.$j->eng_name;?>
												<div class="pd_tb4">
												<?php for ($k = 1; $k <= 5; ++$k) :?>
													<?php echo ($i->$iter >= $k)? '★': '☆';?>
												<?php endfor;?>
												</div>
											<?php endforeach;?>
											</div>
										</div>
									</div>
									<div class="review_second mg_b16">
										<p>
											<?=$i->content?>
										</p>
										<p class="tw_colorg">
											<?=$i->lastdate?>
										</p>
									</div>
									<div class="review_third">
									<?php foreach ($review_img[$idx] as $j) :?>
										<div class="review_img">
											<img src="<?=$adr_s3?>review/<?=$j?>"/>
										</div>
									<?php endforeach;?>
									</div>
								</div>
								<hr/>
							<?php endforeach;?>
							</div>
						</div>
						<div class="near-spot-list mg_t16">
						<?php if ($nearSpotList['code'] != 200) :?>
							/* error! */
						<?php else :?>
							<?php if (count($nearSpotList['data']) > 0) :?>
								<div>
									근처의 다른 Spot
								</div>
								<?php foreach ($nearSpotList['data'] as $i) :?>
									<a href="<?=$adr_ctr?>Spot/index?idx=<?=$i->idx?>">
										<div class="near-spot">
											<img src="<?=$adr_s3?>spot/<?=$i->img?>"/>
											<br/>
											<?=$i->name?>
										</div>
									</a>
								<?php endforeach;?>
							<?php endif;?>
						<?php endif;?>
						</div>
					<?php endif;?>
				</div>
				<aside></aside>
			</div>
			<footer>
			<?php include ("footer.php");?>
			</footer>
		</div>
	</body>
</html>
