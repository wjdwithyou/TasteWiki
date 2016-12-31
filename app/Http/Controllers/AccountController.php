<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\MemberModel;
use Request;

class AccountController extends Controller{
	private function checkProfileInfo($kind, $id, $pw, $nickname, $sex, $age){
		$pattern_arr = array();
		
		array_push($pattern_arr, "/^(naver|katalk|facebook|just)$/");	// kind
		array_push($pattern_arr, "/^[[:alnum:]]{5,13}$/");				// id
		array_push($pattern_arr, "/^[0-9a-zA-Z!@#$%^&*]{8,15}$/");		// pw
		array_push($pattern_arr, "/^[0-9a-zA-Zㄱ-ㅎㅏ-ㅣ가-힣]{2,10}$/u");	// nickname
		array_push($pattern_arr, "/^(man|woman)$/");					// sex
		array_push($pattern_arr, "/^(10|20s|20m|20l|30)$/");			// age
		
		foreach ($pattern_arr as $idx => $i){
			if ($kind != 'just'){
				if ($idx == 1 || $idx == 2)
					continue;
			}
			
			if (!preg_match($i, func_get_arg($idx))){
				echo json_encode(array('code' => 400, 'msg' => 'Invalid input at ['.$idx.'] in '.__FUNCTION__));
				return false;
			}
		}
		
		return true;
	}
	
	public function loginIndex(){
		$host = 'http://'.$_SERVER['HTTP_HOST'].'/';
		$prev = '';
		
		if (isset($_SERVER['HTTP_REFERER'])){
			$prev = $_SERVER['HTTP_REFERER'];
			
			$temp = array();
			
			array_push($temp, $host.'Account/loginIndex');
			array_push($temp, $host.'Account/joinIndex');
			
			foreach ($temp as $i){
				if ($prev == $i)
					$prev = $host;
			}
		}
		else
			$prev = $host;
		
		$page = 'login';
		return view($page, array('page' => $page, 'prev' => $prev));
	}
	
	public function login(){
		$mbModel = new MemberModel();
		
		$kind = Request::input('kind');
		$id = Request::input('id');
		$pw = Request::input('pw');
		
		$result = $mbModel->login($kind, $id, $pw);
		
		if ($result['code'] == 1){
			if (session_id() == '')
				session_start();
			
			$_SESSION['idx'] = $result['data']->idx;
			$_SESSION['nickname'] = $result['data']->nickname;
			$_SESSION['img'] = $result['data']->img;
			$_SESSION['kind'] = $kind;
		}
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	
	/*
	public function index(){
// 		 20160120 J.Style
// 			If session exist, redirect to myPage.
// 		   Else,
// 		  			If prev page(REFERER) exist, 	and has 'prev', redirect to 'prev' page.
// 		 											and no 'prev', redirect to prev page(HTTP_REFERER).
// 		  			Else, redirect to mainPage.
// 		 
		if (session_id() == '')
			session_start();
	
			if (isset($_SESSION['idx'])){
				$mbModel = new MemberModel();
	
				$nickname = $_SESSION['nickname'];
				$result = $mbModel->getInfoByNickname($nickname);
					
				$page = 'mypage';
				return view($page, array('page' => $page, 'result' => $result['data'][0]));
			}
			else{
				if (isset($_SERVER['HTTP_REFERER'])){
					if (Request::has('prev'))
						$prev_url = Request::input('prev');
						else
							$prev_url = $_SERVER['HTTP_REFERER'];
				}
				else
					$prev_url = 'http://'.$_SERVER['HTTP_HOST'];
						
					$page = 'login';
					return view($page, array('page' => $page, 'prev_url' => $prev_url));
			}
	}
	*/
	public function naverCallback(){
		if (!empty(Request::input('error'))){
			$data = array(
					'result' => "로그인을 취소하셨습니다."
			);
		}
		else{
			$grant_type = "authorization_code";
			$client_id = "TZzqmjuDCQgNvjijIGJP";
			$client_secret = "CiPwfNfd7L";
			
			$code = Request::input('code');
			$state = Request::input('state');
				
			$url = "https://nid.naver.com/oauth2.0/token";
			$url .= "?grant_type=".$grant_type;
			$url .= "&client_id=".$client_id;
			$url .= "&client_secret=".$client_secret;
			$url .= "&code=".$code;
			$url .= "&state=".$state;
				
			$file = json_decode(file_get_contents($url));
			
			if (!empty($file->error)){
				$data = array(
						'result'=>'잠시 후에 다시 시도해 주세요.\ntoken get error : '.$file->error
				);
			}
			else{
				$access_token = $file->access_token;
				$opts = array(
						'http' => array(
								'method' => "GET",
								'header' => 'Authorization: bearer '.$access_token
						)
				);
				$context = stream_context_create($opts);
				$file = json_decode(file_get_contents('https://apis.naver.com/nidlogin/nid/getUserProfile.json?response_type=json', false, $context));
		
				if ($file->message == "success"){
					$data = array(
							'result'	=> "success",
							'kind'		=> "naver",
							//'type'		=> "1",
							'no'		=> $file->response->id,
							//'nickname'	=> $file->response->nickname,
							//'email'		=> $file->response->email,
							//'img'		=> $file->response->profile_image
					);
				}
				else{
					$data = array(
							'result'=>"잠시 후에 다시 시도해 주세요.\nprofile get error : ".Request::input('error')
					);
				}
				//print_r($data);
			}
		}
		
		$page = 'naverMain';
		return view($page, $data);
							// array(result, kind, no)
	}
	
	public function joinIndex(){
		$page = "join";
		return view($page, array('page' => $page));
	}
	
	public function join(){
		header('Content-Type: application/json');
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			echo json_encode(array('code' => 400, 'msg' => 'Invalid Access'));
			return;
		}
		
		$mbModel = new MemberModel();
		
		$kind = Request::input('kind');
		
		if ($kind == 'just'){
			$id = Request::input('id');
			$pw = Request::input('pw');
		}
		else
			$id = $pw = Request::input('no');
		
		$nickname = Request::input('nickname');
		$sex = Request::input('sex');
		$age = Request::input('age');
		
		$img = Request::file('img');
		
		if (!$this::checkProfileInfo($kind, $id, $pw, $nickname, $sex, $age))
			return;
		
		$result = $mbModel->create($kind, $id, $pw, $nickname, $sex, $age, $img);
		
		echo json_encode($result);
	}
	
	public function socialAdditionalIndex(){
		$kind = Request::input('kind');
		$no = Request::input('no');
		
		$prev = Request::input('prev');
		
		$page = "social_additional";
		return view($page, array('page' => $page, 'kind' => $kind, 'no' => $no, 'prev' => $prev));
	}
	
	public function checkId(){
		// TODO: Referer Check
		$mbModel = new MemberModel();
		
		$id = Request::input('id');
		
		$result = $mbModel->checkAvailableId($id);
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	
	public function checkNickname(){
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		$mbModel = new MemberModel();
		
		$nickname = Request::input('nickname');
		
		if (Common::loginStateCheck() == 1){
			if ($nickname == $_SESSION['nickname']){
				echo json_encode(array('code' => 200, 'msg' => 'available'));
				return;
			}
		}
		
		$result = $mbModel->checkAvailableNickname($nickname);
		
		echo json_encode($result);
	}
	
	public function modify(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			echo json_encode(array('code' => 400, 'msg' => 'Invalid Access'));
			return;
		}
		
		$mbModel = new MemberModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$pw = Request::input('pw', '');
		$nickname = Request::input('nickname');
		$sex = Request::input('sex');
		$age = Request::input('age');
		$prev_img = Request::input('prev_img');
		
		$img = Request::file('img');
		
		if (!$this::checkProfileInfo('just', 'availableID', $pw, $nickname, $sex, $age))
			return;
		
		$result = $mbModel->update($acc_idx, $pw, $nickname, $sex, $age, $img, $prev_img);
		
		if ($result['code'] == 200){
			if (session_id() == '')
				session_start();
			
			$_SESSION['nickname'] = $nickname;
			$_SESSION['img'] = $result['n_img'];
		}
		
		echo json_encode($result);
	}
	
	public function getAccountInfo(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			echo json_encode(array('code' => 400, 'msg' => 'Invalid Access'));
			return;
		}
		
		$mbModel = new MemberModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$result = $mbModel->getAccountInfo($acc_idx);
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	
	public function logout(){
		if (session_id() == ''){
			session_start();
			session_destroy();
		}
		
		if (isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time() - 3600, '/');
	}
	
	public function iOSgetAccountInfo(){
		header('Content-Type: application/json');
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			echo json_encode(array('code' => 400, 'msg' => 'Invalid Access'));
			return;
		}
		
		$mbModel = new MemberModel();
		
		$acc_idx = Request::input('idx');
		
		$result = $mbModel->getAccountInfo($acc_idx);
		
		echo json_encode($result);//, JSON_UNESCAPED_UNICODE);
	}
}