<div>
	<h3>My Review</h3>
</div>
<table>
	<tr>
		<td width="175px" rowspan="3">
			<img src="<?=$adr_s3?>profile/<?=$_SESSION['img']?>" height="50" width="50"/>
			<br/>
			<a onclick=""><?=$_SESSION['nickname']?></a>
		</td>
		<td width="550px">
		<?php foreach ($rating_kind as $i) :?>
			<?php $iter = 'rating_'.$i->eng_name;?>
			<?=$i->name?>
			<input type="hidden" id="review_<?=$i->eng_name?>_num" value="<?=$data->$iter?>"/>
			<?php for ($j = 1; $j <= 5; ++$j) :?>
				<?php echo ($data->$iter >= $j)? '★': '☆';?>
			<?php endfor;?>
		<?php endforeach;?>
		</td>
		<td class="ta_c">
		<?=$data->lastdate?>
		</td>
	</tr>
	<tr>
		<td>
		<?=$data->content?>
		</td>
		<td class="ta_c">
			<button type="button" class="tw_btn64" onclick="iModifyReview(<?=$data->idx?>);">수정</button>
			<!-- button type="button" onclick="deleteReview();">삭제</button -->
		</td>
	</tr>
	<tr>
		<td>
		<?php foreach ($myreview_img as $i) :?>
			<div class="review_img">
				<img src="<?=$adr_s3?>review/<?=$i?>"/>
			</div>
		<?php endforeach;?>
		</td>
		<td></td>
	</tr>
</table>
