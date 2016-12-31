<?php 
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\CategoryModel;
use Request;

class MainController extends Controller{
	public function index(){
		$purpose = Request::input('p', 0);
		$kind = Request::input('k', 0);
		
		$page = 'main';
		return view($page, array('page' => $page, 'purpose' => $purpose, 'kind' => $kind));
	}
	
	public function getMapLimit(){
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$limit = Common::getMapLimit();
		
		header('Content-Type: application/json');
		echo json_encode($limit);
	}
	
	public function cateSelector(){
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$ctModel = new CategoryModel();
		
		$purposeList = $ctModel->getPurposeList()['data'];
		$kindList = $ctModel->getKindList()['data'];
		
		$temp = (object)array('idx' => 0, 'name' => '전체');
		
		array_unshift($purposeList, $temp);
		array_unshift($kindList, $temp);
		
		$page = 'category_selector';
		return view($page, array(/*'page' => $page, */'purposeList' => $purposeList, 'kindList' => $kindList));
	}
}