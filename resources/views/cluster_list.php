<div class="popup_cluster_top pd_b16 ta_c">
	<span>Spot 목록</span>
	<div class="popup_cluster_close">
		<a onclick="closeSpotList();"><i class="fa fa-times-circle tw_color1"></i></a>
	</div>
</div>
<div class="popup_cluster_content pd_t16">
<?php if ($list['code'] == 0) :?>
	empty cluster
<?php else :?>
	<table class="popup_cluster_table">
	<?php foreach ($list['data'] as $i) :?>
		<tr>
			<td><img src="<?=$adr_s3?>spot/<?=$i->img?>" width="75" height="75" onerror="this.src='<?=$adr_s3?>spot/default.png'"/></td>
			<td><a href="<?=$adr_ctr?>Spot/index?idx=<?=$i->idx?>"><?=$i->name?></a></td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>
</div>