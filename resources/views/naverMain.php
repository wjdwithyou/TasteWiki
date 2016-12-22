<!DOCTYPE html>
<html lang="ko">
  	<head>
  	</head>	  	
  	<body>
  		<input type="hidden" id="result" value="<?=$result?>"/>
  		<?php if ($result == "success") :?>
  			<input type="hidden" id="kind" value="<?=$kind?>"/>
		  	<input type="hidden" id="no" value="<?=$no?>"/>		
  		<?php endif;?>
  		<script>
  			var result = document.getElementById("result").value;
  			
  			if (result == "success"){
		  		var kind = document.getElementById("kind").value;
		  		var no = document.getElementById("no").value;
		  		
		  		window.opener.commonLogin(kind, no, no);
  			}
  			else
  	  			alert (result);
	  		
	  		window.close(); 
  		</script>
  	</body>
</html>
