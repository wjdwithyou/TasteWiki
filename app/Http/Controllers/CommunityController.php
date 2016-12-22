<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\CategoryModel;
use App\Http\models\CommunityModel;
use Request;

class CommunityController extends Controller{
	private function checkInput($cate, $title, $content){
		if (Common::managerCheck() != 1)
			$patternCate = '/^[2-3]$/';
		else
			$patternCate = '/^[1-3]$/';
		
		if ($cate == 0){
			echo json_encode(array('code' => 240, 'msg' => '분류를 선택해주세요.'));
			return false;
		}
		
		if (!preg_match($patternCate, $cate)){
			echo json_encode(array('code' => 403, 'msg' => 'Disallowed access'));
			return false;
		}
		
		$patternTitle = '/\s/';
		
		if (preg_replace($patternTitle, '', $title) == ''){
			echo json_encode(array('code' => 240, 'msg' => '제목을 작성해주세요.'));
			return false;
		}
		
		$patternContent = '/(\s|&nbsp;|<(br|\/?p)>)/';
		
		if (preg_replace($patternContent, '', $content) == ''){
			echo json_encode(array('code' => 240, 'msg' => '내용을 작성해주세요.'));
			return false;
		}
		
		return true;
	}
	
	public function index(){
		$ctModel = new CategoryModel();
		
		$commList = $ctModel->getCommunityList();
		
		$page_num = Request::input('page_num', 1);
		
		$page = 'community';
		return view($page, array('page' => $page, 'commList' => $commList['data'], 'page_num' => $page_num));
	}
	
	public function getList(){
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$cmModel = new CommunityModel();
		
		$cate = Request::input('cate', 0);
		
		$searchType = Request::input('searchType', 1);
		$searchText = trim(Request::input('searchText', ''));
		
		$page_num = Request::input('page_num', 1);
		
		$result = $cmModel->getCommunityList($cate, $searchType, $searchText, $page_num);
		
		if ($result['code'] != 200){
			if ($result['code'] == 240)
				return "No Result";		// temp
			else
				return $result;
		}
		
		$searchTypeArr = array('제목', '제목+내용', '작성자');
		
		$page = 'community_list';
		return view($page, array(
							/*'page'		=> $page,*/
							'result'		=> $result['data'],
							'searchTypeArr'	=> $searchTypeArr,
							'searchType'	=> $searchType,
							'searchText'	=> $searchText,
							'page_num'		=> $result['page_num'],
							'page_max'		=> $result['page_max']		
		));
	}
	
	public function detail(){
		$cmModel = new CommunityModel();
		
		$comm_idx = Request::input('idx');
		
		if (isset($_SERVER['HTTP_REFERER']))
			$cmModel->increaseHit($comm_idx);
		
		$data = $cmModel->getCommData($comm_idx);
		
		if ($data['code'] != 200){
			// 400, 500...
			
			// TODO: Not found page
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$reply = $cmModel->getReplyList($comm_idx)['data'];
		
		$page = 'community_detail';
		return view($page, array('page' => $page, 'data' => $data['data'], 'reply' => $reply));
	}
	
	public function writeIndex(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$comm_idx = Request::input('idx');
		
		$ctModel = new CategoryModel();
		
		if ($comm_idx){
			$cmModel = new CommunityModel();
			
			$result = $cmModel->checkExistComm($comm_idx);
			
			if ($result['code'] == 0){
				// not found
				header("Location: http://".$_SERVER['HTTP_HOST']);
				die();
			}
			
			$acc_idx = $_SESSION['idx'];
			
			$writer = $cmModel->getWriter($comm_idx)['data'];
			
			if ($acc_idx != $writer){
				// incorrect access..
				header("Location: http://".$_SERVER['HTTP_HOST']);
				die();
			}
		}
		
		$commList = $ctModel->getCommunityList();
		
		$page = 'community_write';
		return view($page, array('page' => $page, 'comm_idx' => $comm_idx, 'commList' => $commList['data']));
	}
	
	public function getPrevData(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		$cmModel = new CommunityModel();
		
		$acc_idx = $_SESSION['idx'];
		// writer-session matching check?
		
		$comm_idx = Request::input('idx');
		
		$result_getCommData2 = $cmModel->getCommData2($comm_idx, $acc_idx);
		
		if ($result_getCommData2['code'] != 200){
			echo json_encode($result_getCommData2);
			return;
		}
		
		$result_getCommData2['data']->title = Common::originalStr($result_getCommData2['data']->title);
		
		echo json_encode($result_getCommData2);
	}
	
	public function write(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		$cmModel = new CommunityModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$cate = Request::input('cate');
		$title = Request::input('title');
		$content = Request::input('content');
		
		if (!$this->checkInput($cate, $title, $content))
			return;
		
		$title = Common::replaceStr($title);
		
		$result = $cmModel->create($acc_idx, $cate, $title, $content);
		
		echo json_encode($result);
	}
	
	public function update(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		$cmModel = new CommunityModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$comm_idx = Request::input('idx');
		
		$result_getWriter = $cmModel->getWriter($comm_idx);
		
		if ($result_getWriter['code'] != 200){
			echo json_encode($result_getWriter);
			return;
		}
		
		$writer = $result_getWriter['data'];
			
		if ($acc_idx != $writer){
			echo json_encode(array('code' => 403, 'msg' => 'Disallowed access'));
			return;
		}
		
		$cate = Request::input('cate');
		$title = Request::input('title');
		$content = Request::input('content');
		$prev_img = Request::input('prev_img');
		
		if (!$this->checkInput($cate, $title, $content))
			return;
		
		$title = Common::replaceStr($title);
		
		$result = $cmModel->update($acc_idx, $comm_idx, $cate, $title, $content, $prev_img);
		
		echo json_encode($result);
	}
	
	public function remove(){
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		// impl.
		
		return "remove";	// temp
	}
	
	public function writeReply(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		$cmModel = new CommunityModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$comm_idx = Request::input('idx');
		$content = Request::input('content');
		
		$patternContent = '/\s/';
		
		if (preg_replace($patternContent, '', $content) == ''){
			echo json_encode(array('code' => 240, 'msg' => '내용을 작성해주세요.'));
			return;
		}
		
		$content = Common::replaceStr($content);
		
		$result_createReply = $cmModel->createReply($acc_idx, $comm_idx, $content);
		
		if ($result_createReply['code'] == 200){
			// If reply created, additional process
			$cmModel->increaseReply($comm_idx);
		}
		
		echo json_encode($result_createReply);
	}
}