function showPrev(ver){
	if (loginStateCheck() != 1){
		alert("로그인이 필요한 기능입니다.");
		return;
	}
	
	var spot_idx = $("#spot_idx").val();
	
	location.href = adr_ctr + "Spot/historyDetail?idx=" + spot_idx + "&ver=" + ver;
}