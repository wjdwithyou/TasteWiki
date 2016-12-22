<?php
namespace App\Http\models;
use DB;

class CategoryModel{
	function getPurposeList(){
		$result = DB::select('select * from category_purpose order by idx asc');
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
	
	function getKindList(){
		$result = DB::select('select * from category_kind order by idx asc');
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
	
	function getPurpose($idx){
		$result = DB::select('select name from category_purpose where idx=?', array($idx));
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
	
	function getKind($idx){
		$result = DB::select('select name from category_kind where idx=?', array($idx));
	
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
	
	function getCommunityList(){
		$result = DB::select('select * from category_community order by idx asc');
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
}