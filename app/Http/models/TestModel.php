<?php
namespace App\Http\models;
use DB;

class TestModel{
	// Method name: Same with Controller's method name.
	
	/*
	function migrate(){
		// no input
		
		$temp_arr = array();
		$result = DB::select('select account_idx, idx, latitude, longitude, img, name, content, lastdate, category_idx from spot');
		
		foreach ($result as $i){
			$temp_idx = DB::table('spot_log')->insertGetId(
					array(
							'account_idx'	=> NULL,
							'spot_idx'		=> $i->idx,
							'latitude'		=> $i->latitude,
							'longitude'		=> $i->longitude,
							'img'			=> $i->img,
							'name'			=> $i->name,
							'content'		=> $i->content,
							'modifydate'	=> $i->lastdate,
							'category_idx'	=> $i->category_idx,
							'description'	=> 'Log 생성'
					)
			);
			
			array_push($temp_arr, $temp_idx);
		}
		
		return $temp_arr;
	}
	*/
	
	/*
	// PHP overloading
	function __call($method, $param){
		switch($method){
			case 'test':
				switch (count($param)){
					case 1:
						$this->getMyReviewByA($param[0]);
						return;
					case 2:
						$this->getMyReviewByB($param[0], $param[1]);
						return;
					case 4:
						$this->paramTest($method, $param);
					default:
						break;
				}
				
				break;
			default:
				break;
		}
		
		return array('code' => 0, 'msg' => 'failure: __call('.$method.', #'.count($param).'...)');
	}
	
	function getMyReviewByA($a){
		print_r($a." * 2 = ".(2 * $a));
	}
		
	function getMyReviewByB($a, $b){
		print_r($a." * ".$b." = ".($a * $b));
	}
	*/
}