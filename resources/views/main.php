<!DOCTYPE html>
<html>
	<head>
	<?php include ("libraries.php");?>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCakbOCGKi4pdXWxsnGdFLcxntyasmG4Zg&region=KR"></script>
		<!--script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdEGLrk_lEg1a-69i1_V6gl9qEvxMR4hQ&sensor=true&region=KR"></script-->
		<!--script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=true"></script-->
		<script src="<?=$adr_js?>markerclusterer.js?v=<?=$timestamp?>"></script>
		<script>
			document.title += '메인';
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
					<input type="hidden" id="purpose" value="<?=$purpose?>"/>
					<input type="hidden" id="kind" value="<?=$kind?>"/>
					<div id="map_canvas"></div>
					<div id="popup_background" hidden></div>
					<div id="popup_cluster" hidden></div>
				</div>
				<aside></aside>
			</div>
			<footer>
			<?php include ("footer.php");?>
			</footer>
		</div>
	</body>
</html>
