function deleteWishlist(idx){
	$.ajax({
		url: adr_ctr + 'Mypage/deleteWishlist',
		async: false,
		data: {
			idx: idx
		},
		type: 'post',
		success: function(result){
			result = JSON.parse(result);
			
			if (result.code == 200){
				alert('삭제되었습니다.');
				location.href = adr_ctr + 'Mypage/wishlistIndex';
			}
			else{
				alert("code: " + result.code + "\nmessage: " + result.msg + "\nerror: " + getError(result.code));
				alert('Wishlist 삭제에 실패했습니다.\n서버 관리자에게 문의하세요.');
			}
		},
		error: function(request, status, error){
			console.log(request.responseText);
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}
