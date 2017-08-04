<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script>
			document.title += '<?=$data->title?>';

			$('meta[name="og:title"]').attr('content', document.title);
			$('meta[name="og:description"]').attr('content', '<?=$data->content?>');

			$('meta[name="twitter:title"]').attr('content', document.title);
			$('meta[name="twitter:description"]').attr('content', '<?=$data->content?>');
		</script>
	</head>
	<body>
		<div class="wrap">
			<header>
			<?php include ("header.php")?>
			</header>
			<div class="contents">
				<nav></nav>
				<div class="content">
					<div class="top">
						<h1><?=$data->title?></h1>
						<?php if ($logined && $data->account_idx == $_SESSION['idx']) :?>
							<p align="right">
								<button type="button" class="tw_btn48" onclick="iModifyComm()">수정</button>
								<button type="button" class="tw_btn48" onclick="deleteComm">삭제</button>
							</p>
						<?php endif;?>
						<p align="right">작성자: <?=$data->nickname?><br/>마지막 수정: <?=$data->lastdate?><br/>hit: <?=$data->hit_cnt?></p>
					</div>
					<div class="core">
						<input type="hidden" id="comm_idx" value="<?=$data->idx?>"/>
						<?=$data->content?>
					</div>
					<div class="reply_wrap">
						<div class="reply_write">
							<hr/>
							<table class="reply_write_table">
								<tr>
								<?php if ($logined)	:?>
									<td class="reply_write_td" width="50px"><img src="<?=$adr_s3?>profile/<?=$_SESSION['img']?>" height="20" width="20"/></td>
									<td class="reply_write_td" width="150px"><a onclick=""><?=$_SESSION['nickname']?></a></td>
									<td class="reply_write_td" width="600px"><textarea id="cm_reply_content" class="cm_reply_content" rows="3"></textarea></td>
									<td class="reply_write_td" width="150px"><button type="button" class="tw_btn48" onclick="writeReply();">등록</button></td>
								<?php else :?>
									<td class="reply_write_td" width="50px"><img src="<?=$adr_s3?>profile/default.png" height="20" width="20"/></td>
									<td class="reply_write_td" width="150px">GUEST</td>
									<td class="reply_write_td" width="600px"><textarea class="cm_reply_content" rows="3" placeholder="로그인 후에 이용해주세요." rows="3" disabled></textarea></td>
									<td class="reply_write_td" width="150px"><button type="button" class="tw_btn48" onclick="alert('로그인 후에 이용해주세요.');">등록</button></td>
								<?php endif;?>
								</tr>
							</table>
						</div>
						<div class="reply_list">
							<hr/>
							<table class="reply_list_table">
							<?php foreach ($reply as $i) :?>
								<tr>
									<td class="reply_list_td" width="50px"><img src="<?=$adr_s3?>profile/<?=$i->img?>" height="20" width="20"/></td>
									<td class="reply_list_td" width="150px"><?=$i->nickname?></td>
									<td class="reply_list_content" width="600px"><?=$i->content?></td>
									<td class="reply_list_td" width="150px"><?=$i->lastdate?></td>
								</tr>
							<?php endforeach;?>
							</table>
						</div>
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
