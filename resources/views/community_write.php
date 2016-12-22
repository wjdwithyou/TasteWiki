<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script type="text/javascript" src="<?=$adr_js?>write_common.js"></script>
		
		<link href="<?=$adr_ctr?>summernote/summernote.css" rel="stylesheet">
		<link href="<?=$adr_ctr?>font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<script src="<?=$adr_ctr?>summernote/summernote.min.js"></script>
		<script src="<?=$adr_ctr?>summernote/summernote-ko-KR.js"></script>
		<script src="<?=$adr_js?>activate-power-mode.js"></script>
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
					<?php if (!$comm_idx) :?>
						<h1>Community Write</h1>
					<?php else :?>
						<h1>Community Modify</h1>
					<?php endif;?>
					</div>
					<div id="content">
					<?php if (!$comm_idx) :	// nothing?>
					<?php else :?>
						<input type="hidden" id="comm_idx" value="<?=$comm_idx?>"/>
					<?php endif;?>
						<select id="community_cate">
							<option value="0">분류를 선택해주세요</option>
							<?php foreach ($commList as $i) :?>
								<?php if ($i->idx != 1) :?>
								<option value="<?=$i->idx?>"><?=$i->name?></option>
								<?php endif;?>
							<?php endforeach;?>
						</select>
						<input type="text" id="comm_title" maxlength="40" placeholder="제목을 입력하세요."/>
						<textarea id="summernote" placeholder="Spot을 설명해주세요."></textarea>
					</div>
					<div class="cmw_btnset ta_c">
					<?php if (!$comm_idx) :?>
						<button type="button" class="cmw_btn" onclick="writeComm()">작성하기</button>
					<?php else :?>
						<button type="button" class="cmw_btn" onclick="modifyComm()">수정하기</button>
					<?php endif;?>
					<button type="button" class="cmw_btn" onclick='history.go(-1);'>취소</button>
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
