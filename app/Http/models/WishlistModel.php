<?php
namespace App\Http\models;
use DB;

include_once dirname(__FILE__)."/Common.php";

class WishlistModel{
	function create($acc_idx, $spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$wish_idx = DB::table('wishlist')->insertGetId(
				array(
						'account_idx'	=> $acc_idx,
						'spot_idx'		=> $spot_idx,
						'savedate'		=> DB::raw('now()')
				)
		);
		
		if ($wish_idx > 0)
			return array('code' => 200, 'msg' => 'create');
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function getWishlistCnt($acc_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select count(*) as cnt from wishlist where account_idx=?', array($acc_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]->cnt);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function checkExist($acc_idx, $spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select * from wishlist where account_idx=? and spot_idx=?', array($acc_idx, $spot_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'exist');
		else
			return array('code' => 240, 'msg' => 'not in wishlist');
	}
	
// 	function checkExistByIdx($wish_idx){
// 		if ($_ = checkParam(func_get_args()))
// 			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
// 		$result = DB::select('select * from wishlist where idx=?', array($wish_idx));
		
// 		if (count($result) > 0)
// 			return array('code' => 1, 'msg' => 'exist');
// 		else
// 			return array('code' => 0, 'msg' => 'not in wishlist');
// 	}
	
	function getWishlist($acc_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select	w.idx as idx,
										s.idx as spot_idx,
										s.img as img,
										s.name as name
								from wishlist as w, spot as s
								where s.idx in (select spot_idx from wishlist where w.account_idx=?) and w.spot_idx=s.idx order by w.savedate desc', array($acc_idx));
		
		if (count($result) > 0)
			return array('code' => 1, 'msg' => 'success', 'data' => $result);
		else
			return array('code' => 0, 'msg' => 'empty');
	}
	
	function delete($acc_idx, $wish_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::delete('delete from wishlist where idx=? and account_idx=?', array($wish_idx, $acc_idx));
		
		if ($result == true)
			return array('code' => 200, 'msg' => 'success');
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function getSpotIdx($wish_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select spot_idx from wishlist where idx=?', array($wish_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]->spot_idx);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function getWishData($acc_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select wish_num, wish_rating*wish_num as pSum from member where idx=?', array($acc_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function setWishData($acc_idx, $wish_rating, $wish_num){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::update('update member set wish_rating=?, wish_num=? where idx=?', array($wish_rating, $wish_num, $acc_idx));
		
		if ($result == true)
			return array('code' => 1, 'msg' => 'update success');
		else
			return array('code' => 500, 'msg' => 'update failure');
	}
}
