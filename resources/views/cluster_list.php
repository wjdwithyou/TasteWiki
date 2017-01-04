<div class="popup_cluster_top pd_b16 ta_c">
	<span class="fw_b">Spot 목록</span>
	<div class="popup_cluster_close">
		<a onclick="closeSpotList();"><i class="fa fa-times-circle tw_color1"></i></a>
	</div>
</div>
<div class="popup_cluster_content">
<?php if ($list['code'] == 0) :?>
	empty cluster
<?php else :?>
	<table class="popup_cluster_table">
	<?php foreach ($list['data'] as $i) :?>
		<tr>
			<td>
				<a href="<?=$adr_ctr?>Spot/index?idx=<?=$i->idx?>">
					<span class="mg_r16"><img src="<?=$adr_s3?>spot/<?=$i->img?>" onerror="this.src='<?=$adr_s3?>spot/default.png'"/></span>
					<span><?=$i->name?></span>
				</a>
			</td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>
</div>