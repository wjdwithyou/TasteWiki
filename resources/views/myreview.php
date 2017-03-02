<div>
	<h3>My Review</h3>
</div>
<table>
	<tr>
		<td class="ta_c">
			<img src="<?=$adr_s3?>profile/<?=$_SESSION['img']?>" height="50" width="50"/>
			<br/>
			<a onclick=""><?=$_SESSION['nickname']?></a>
		</td>
		<td rowspan="2">
			<div class="mg_b16">
				<p>
					<?=$data->content?>
				</p>
				<p class="ta_r tw_colorg">
					<?=$data->lastdate?>
				</p>
			</div>
			<div>
			<?php foreach ($myreview_img as $i) :?>
				<div class="review_img">
					<img src="<?=$adr_s3?>review/<?=$i?>"/>
				</div>
			<?php endforeach;?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="p-rating">
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
					<?php for ($j = 1; $j <= 5; ++$j) :?>
						<?php echo ($data->$iter >= $j)? '★': '☆';?>
					<?php endfor;?>
					</div>
				<?php endforeach;?>
				</div>
			</div>
			<div class="ta_c">
				<button type="button" class="tw_btn64" onclick="iModifyReview(<?=$data->idx?>);">수정</button>
				<!-- button type="button" onclick="deleteReview();">삭제</button -->
			</div>
		</td>
	</tr>
</table>
