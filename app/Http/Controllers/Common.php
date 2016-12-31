<?php
namespace App\Http\Controllers;

class Common{
	static function managerCheck(){
		if (session_id() == '')
			session_start();
		
		switch ($_SESSION['idx']){
			case 1:
			case 2:
				return 1;
			default:
				return 0;
		}
	}
	
	static function loginStateCheck(){
		if (session_id() == '')
			session_start();
		
		$logined = !empty($_SESSION['idx']);
		
		return $logined;
	}
	
	static function getMapLimit(){
		return array('n' => 37.594, 'e' => 127.108, 's' => 37.505, 'w' => 126.995, '_e' => 0.000001);
	}
	
	static function replaceStr($str){
		$symbol_set = array();
		
		array_push($symbol_set, (object)array('raw' => '&', 'fix' => '&amp;'));
		array_push($symbol_set, (object)array('raw' => '<', 'fix' => '&lt;'));
		array_push($symbol_set, (object)array('raw' => '>', 'fix' => '&gt;'));
		
		foreach ($symbol_set as $i)
			$str = str_replace($i->raw, $i->fix, $str);
		
		return $str;
	}
	
	static function originalStr($str){
		$symbol_set = array();
		
		array_push($symbol_set, (object)array('raw' => '&', 'fix' => '&amp;'));
		array_push($symbol_set, (object)array('raw' => '<', 'fix' => '&lt;'));
		array_push($symbol_set, (object)array('raw' => '>', 'fix' => '&gt;'));
		
		foreach ($symbol_set as $i)
			$str = str_replace($i->fix, $i->raw, $str);
		
		return $str;
	}
}
