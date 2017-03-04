<div>
	<h3>My Review</h3>
</div>
<div class="pd_tb16">
	<div class="review_first mg_b16">
		<div class="p-profile">
			<img src="<?=$adr_s3?>profile/<?=$_SESSION['img']?>"/>
			<br/>
			<a onclick=""><?=$_SESSION['nickname']?></a>
		</div>
		<div class="p-rating f_l">
			<div class="f_l">
			<?php foreach($rating_kind as $i) :?>
				<div class="pd_tb4">
					<i class="fa <?=$i->fa_name?>" aria-hidden="true"></i>
				</div>
			<?php endforeach;?>
			</div>
			<div class="f_l ta_c">
			<?php foreach($rating_kind as $i) :?>
				<div class="pd_a4">
					<?=$i->name?>
				</div>
			<?php endforeach;?>
			</div>
			<div class="f_l">
			<?php foreach($rating_kind as $i) :?>
				<?php $iter = 'rating_'.$i->eng_name;?>
				<div class="pd_tb4">
				<?php for ($j = 1; $j <= 5; ++$j) :?>
					<?php echo ($data->$iter >= $j)? '★': '☆';?>
				<?php endfor;?>
				</div>
			<?php endforeach;?>
			</div>
		</div>
	</div>
	<div class="review_second mg_b16">
		<p>
			<?=$data->content?>
		</p>
		<p class="tw_colorg">
			<?=$data->lastdate?>
		</p>
	</div>
	<div class="review_third mg_b16">
	<?php foreach ($myreview_img as $i) :?>
		<div class="review_img">
			<img src="<?=$adr_s3?>review/<?=$i?>"/>
		</div>
	<?php endforeach;?>
	</div>
	<div class="review_forth ta_c">
		<button type="button" class="tw_btn64" onclick="iModifyReview(<?=$data->idx?>);">수정</button>
		<!-- button type="button" onclick="deleteReview();">삭제</button -->
	</div>
</div>
