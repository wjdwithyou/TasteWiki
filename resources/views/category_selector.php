<div class="ta_c">
	<div>
		<label>목적</label>
		<select id="cate_search_purpose">
		<?php foreach ($purposeList as $i) :?>
			<option value="<?=$i->idx?>"><?=$i->name?></option>
		<?php endforeach;?>
		</select>
		<br/>
		<label>종류</label>
		<select id="cate_search_kind">
		<?php foreach ($kindList as $i) :?>
			<option value="<?=$i->idx?>"><?=$i->name?></option>
		<?php endforeach;?>
		</select>
		<br/>
	</div>
	<div class="mg_a8">
		<button type="button" class="tw_btn48" onclick='showCategorizedSpot();'>확인</button>
		<button type="button" class="tw_btn48" onclick='toggleCateSelector();'>취소</button>
	<div>
</div>