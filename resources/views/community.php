<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script>
			document.title += '커뮤니티';

			$('meta[name="og:title"]').attr('content', document.title);
			$('meta[name="twitter:title"]').attr('content', document.title);
		</script>
	</head>
	<body>
		<div class="wrap">
			<header>
			<?php include ("header.php");?>
			</header>
			<div class="contents">
				<nav></nav>
				<div class="content">
					<div id="top">
						<h1>Community</h1>
						<div class="cm-top-btn-wrap">
							<div class="f_l mg_b8">
								<input type="hidden" id="cm_cate_btn_num" value="<?=count($commList) + 1?>"/>
								<input type="hidden" id="cm_cate" value="0"/>
								<button type="button" id="cm_cate_btn_0" class="tw_btn48 clicked" onclick="changeCate(0);">전체</button>
								<?php foreach ($commList as $i) :?>
								<button type="button" id="cm_cate_btn_<?=$i->idx?>" class="tw_btn48" onclick="changeCate(<?=$i->idx?>);"><?=$i->name?></button>
								<?php endforeach;?>
							</div>
							<div class="f_r mg_b8">
								<button type="button" class="tw_btn64" onclick="iWriteComm()">글쓰기</button>
							</div>
						</div>
					</div>
					<div id="cm_list"></div>
				</div>
				<aside></aside>
			</div>
			<footer>
			<?php include ("footer.php");?>
			</footer>
		</div>
	</body>
</html>
