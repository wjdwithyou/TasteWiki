<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Request;

class UploadController extends Controller{
	public function addTempImage(){
		// TODO: referer check
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$acc_idx = $_SESSION['idx'];
		
		$file = Request::file('file');
		$type = Request::input('type');
		$num = Request::input('num');
		
		$path = "img/temp/";
		$ext = $file->getClientOriginalExtension();
		
		$date = date("YmdHis", time());
		$name = $acc_idx."_".$type.$num."_".$date.".".$ext;
		
		$file->move($path, $name);
		
		header('Content-Type: application/json');
		echo json_encode(array('code' => 1, 'msg' => 'success', 'name' => $name));
	}
	
	public function deleteTempImage(){
		// TODO: referer check
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$acc_idx = $_SESSION['idx'];
		
		$file = glob('img/temp/'.$acc_idx.'*');
		
		foreach($file as $i){
			if (is_file($i))
				unlink($i);
		}
		
		header('Content-Type: application/json');
		echo json_encode(array('code' => 1, 'msg' => 'success'));
	}
}
