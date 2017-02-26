<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script type="text/javascript" src="<?=$adr_js?>write_common.js"></script>

		<link href="<?=$adr_ctr?>summernote/summernote.css" rel="stylesheet">
		<link href="<?=$adr_ctr?>font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<script src="<?=$adr_ctr?>summernote/summernote.min.js"></script>
		<script src="<?=$adr_ctr?>summernote/summernote-ko-KR.js"></script>
		<script src="<?=$adr_js?>activate-power-mode.js?v=<?=$timestamp?>"></script>
	</head>
	<body>
		<div class="wrap">
			<header>
			<?php include ("header.php")?>
			</header>
			<div class="contents">
				<nav></nav>
				<div class="content">
					<div id="top">
					<?php if (!$spot_idx) :?>
						<h1>Spot 새로 만들기</h1>
					<?php else :?>
						<h1>Spot 수정하기</h1>
					<?php endif;?>
					</div>
					<div id="thumbnail" class="mg_b16">
						<div id="thumbnail_prev"></div>
						<div id="thumbnail_img">
							<img src="<?=$adr_s3?>spot/default.png" id="spot_img" height="100" width="100"/>
						</div>
						<input type="file" id="spot_file" accept="image/*" onchange="showPreview($(this));"/>
					</div>
					<div id="category" class="mg_b16">
						<strong>목적</strong>
						<?php foreach ($purposeList as $i) :?>
						<a onclick="addStack('p', <?=$i->idx?>, '<?=$i->name?>')"><?=$i->name?></a>
						<?php endforeach;?>
						<br>
						<strong>종류</strong>
						<?php foreach ($kindList as $i) :?>
						<a onclick="addStack('k', <?=$i->idx?>, '<?=$i->name?>')"><?=$i->name?></a>
						<?php endforeach;?>
					</div>
					<div id="stack_wrap" class="mg_b16">
					</div>
					<div id="content" class="mg_b16">
					<?php if (!$spot_idx) :?>
						<input type="hidden" id="spot_latitude" value="<?=$latitude?>"/>
						<input type="hidden" id="spot_longitude" value="<?=$longitude?>"/>
						<input type="hidden" id="base_idx" value="<?=$base_idx?>"/>
					<?php else :?>
						<input type="hidden" id="spot_idx" value="<?=$spot_idx?>"/>
					<?php endif;?>
						<input type="text" id="spot_name" class="mg_b8" maxlength="40" placeholder="Spot의 이름을 입력해 주세요."/>
						<textarea id="summernote" placeholder="Spot을 설명해 주세요."></textarea>
						<?php if (!$spot_idx) :		// nothing?>
						<?php else :?>
							<input type="text" id="spot_description" maxlength="250" placeholder="(선택사항)수정 이유를 작성해 주세요."/>
						<?php endif;?>
					</div>
					<div class="btn_wrap ta_c">
						<?php if (!$spot_idx) :?>
							<button type="button" class="tw_btn80" onclick="writeSpot()">작성하기</button>
						<?php else :?>
							<button type="button" class="tw_btn80" onclick="updateSpot()">수정하기</button>
						<?php endif;?>
						<button type="button" class="tw_btn80" onclick="history.back()">취소</button>
					</div>
				</div>
				<aside></aside>
			</div>
			<footer>
			<?php include ("footer.php")?>
			</footer>
		</div>
	</body>
</html>
