<input type="hidden" id="cm_page_num" value="<?=$page_num?>"/>
<div class="cm-list-inner">
<?php foreach($result as $i) :?>
	<div class="cm-list-pc">
		<div class="cm-list-first">
			<div>
				<?=$i->idx?>
			</div>
			<div class="mg_l8">
				<a class="<?php echo ($i->cate_idx == 1)? "fw_b": "" ?>" onclick="openComm(<?=$i->idx?>);">[<?=$i->cate_name?>]&nbsp;<?=$i->title?></a>&nbsp;[<?=$i->reply_cnt?>]
			</div>
		</div>
		<div class="cm-list-second">
			<div>
				<?=$i->nickname?>
			</div>
			<div>
				<?=$i->writedate?>
			</div>
			<div>
				<?=$i->hit_cnt?>
			</div>
		</div>
	</div>
	<div class="cm-list-mobile">
		<div class="cm-list-first">
			<div class="cm-list-mob-title">
				<a class="<?php echo ($i->cate_idx == 1)? "fw_b": "" ?>" onclick="openComm(<?=$i->idx?>);">[<?=$i->cate_name?>]&nbsp;<?=$i->title?></a>
			</div>
		</div>
		<a onclick="openComm(<?=$i->idx?>);">
			<div class="cm-list-replycnt">
				<?=$i->reply_cnt?>
			</div>
		</a>
		<div class="cm-list-second">
			<div>
				<?=$i->nickname?>&nbsp;|&nbsp;<?=$i->writedate?>&nbsp;|&nbsp;<?=$i->hit_cnt?>
			</div>
		</div>
	</div>
<?php endforeach;?>
</div>
<div class="cm_pagination_wrap mg_b8">
	<div class="cm_pagination ta_c">
	<a onclick="getCommList(<?=$page_num - 1?>);">left</a>
	<?php if ($page_num > 3) :?>
		<a onclick="getCommList(1);">1</a>
		...
		<a onclick="getCommList(<?=$page_num - 1?>);"><?=$page_num - 1?></a>
	<?php else :?>
		<?php for ($i = 1; $i < $page_num; ++$i) :?>
			<a onclick="getCommList(<?=$i?>);"><?=$i?></a>
		<?php endfor;?>
	<?php endif;?>
	<span class="cm_pagination_current"><?=$page_num?></span>
	<?php if ($page_max - $page_num > 2) :?>
		<a onclick="getCommList(<?=$page_num + 1?>);"><?=$page_num + 1?></a>
		...
		<a onclick="getCommList(<?=$page_max?>);"><?=$page_max?></a>
	<?php else :?>
		<?php for ($i = $page_num + 1; $i <= $page_max; ++$i) :?>
			<a onclick="getCommList(<?=$i?>);"><?=$i?></a>
		<?php endfor;?>
	<?php endif;?>
	<a onclick="getCommList(<?=$page_num + 1?>);">right</a>
	</div>
</div>

<div class="cm_search_wrap ta_c">
	<select id="cm_search_type">
	<?php for ($i = 1; $i <= count($searchTypeArr); ++$i) :?>
		<?php if ($searchType == $i) :?>
			<option value="<?=$i?>" selected="selected"><?=$searchTypeArr[$i - 1]?></option>
		<?php else :?>
			<option value="<?=$i?>"><?=$searchTypeArr[$i - 1]?></option>
		<?php endif;?>
	<?php endfor;?>
	</select>
	<input type="text" id="cm_search_text" value="<?=$searchText?>" onkeypress="if(event.keyCode==13){getCommList(1)}"/>
	<button type="button" class="tw_btn48" onclick="getCommList(1)">검색</button>
</div>
