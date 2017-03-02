<div>
	<h3>My Review</h3>
</div>
<table>
	<tr>
		<td class="ta_c">
		<?php if (!$logined) :?>
			<img src="<?=$adr_s3?>profile/default.png" height="50" width="50"/>
			<br/>
			guest
		<?php else :?>
			<img src="<?=$adr_s3?>profile/<?=$_SESSION['img']?>" height="50" width="50"/>
			<br/>
			<a onclick=""><?=$_SESSION['nickname']?></a>
		<?php endif;?>
		</td>
		<td rowspan="2">
			<div class="mg_b16">
				<textarea id="review_content" rows="3" placeholder="<?php echo ($logined)? "Spot을 평가해주세요.": "로그인 후에 이용해주세요.";?>" <?php echo (!$logined)? "disabled": ""?>><?php echo (!$is_modify)? "": $data->content;?></textarea>
			</div>
			<div>
			<?php for ($i = 0; $i < 3; ++$i) :?>
				<div class="filebox">
					<div class="upload_display">
						<div class="upload_thumb_wrap">
						<?php if (!$is_modify) :?>
							<img src="<?=$adr_s3?>review/default.png"/>
						<?php else :?>
							<?php if (!isset($myreview_img[$i])) :?>
								<img src="<?=$adr_s3?>review/default.png" id="review_img<?=$i?>"/>
							<?php else :?>
								<img src="<?=$adr_s3?>review/<?=$myreview_img[$i]?>" id="review_img<?=$i?>"/>
							<?php endif;?>
						<?php endif;?>
						</div>
					</div>
					<input type="text" id="file_name<?=$i?>" disabled/>
					<div class="review_file_wrap dp_ib">
						<label class="tw_btn80" for="review_file<?=$i?>">파일 선택</label>
					</div>
					<input type="file" id="review_file<?=$i?>" accept="image/*" onchange="showThumbnail(<?=$i?>);" <?php echo (!$logined)? "disabled": ""?>/>
				</div>
			<?php endfor;?>
			</div>
			<div id="review_img_prev"></div>
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
						<a id="review_<?=$i->eng_name?>_star<?=$j?>" onclick="modifyStar('<?=$i->eng_name?>', <?=$j?>)"><?php echo (!$is_modify)? '☆': (($data->$iter < $j)? '☆': '★');?></a>
					<?php endfor;?>
					</div>
				<?php endforeach;?>
				</div>
			</div>
			<div class="ta_c">
			<?php if (!$is_modify) :?>
				<button type="button" class="tw_btn64" onclick="writeReview();">등록</button>
			<?php else :?>
				<button type="button" class="tw_btn64" onclick="modifyReview(<?=$data->idx?>);">등록</button>
			<?php endif;?>
			</div>
		</td>
	</tr>
</table>
