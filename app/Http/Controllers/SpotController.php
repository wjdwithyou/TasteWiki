<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\CategoryModel;
use App\Http\models\SpotModel;
use App\Http\models\WishlistModel;
use Request;

class SpotController extends Controller{
	private function getCateKorean($stack){
		$ctModel = new CategoryModel();
		
		$cate_arr = array();
	
		while (strlen($stack)){
			$cate = substr($stack, 0, strpos($stack, ","));
	
			$large = $cate[0];
			$small = substr($cate, 1);
	
			if ($large == 'p')
				$temp = $ctModel->getPurpose($small)['data'][0]->name;
			elseif ($large == 'k')
				$temp = $ctModel->getKind($small)['data'][0]->name;
			else;	// impl.
			
			array_push($cate_arr, $temp);
			
			$stack = substr($stack, strpos($stack, ",") + 1);
		}
	
		return $cate_arr;
	}
	
	private function checkInput($cate, $name, $content){
		if ($cate == ""){
			echo json_encode(array('code' => 240, 'msg' => '카테고리를 하나 이상 선택해주세요.'));
			return false;
		}
		
		$patternCate = "/^(((p[1-7])|(k([1-9]|1[0-2]))),)+$/";	// p:1-7, k:1-12
		
		if (!preg_match($patternCate, $cate)){
			echo json_encode(array('code' => 400, 'msg' => "Invalid Category"));
			return false;
		}
		
		$patternName = '/\s/';
		
		if (preg_replace($patternName, '', $name) == ''){
			echo json_encode(array('code' => 240, 'msg' => 'Spot의 이름을 작성해주세요.'));
			return false;
		}
		
		$patternContent = '/(\s|&nbsp;|<(br|\/?p)>)/';
		
		if (preg_replace($patternContent, '', $content) == ''){
			echo json_encode(array('code' => 240, 'msg' => '내용을 작성해주세요.'));
			return false;
		}
		
		return true;
	}
	
	private function checkReviewInput($rating, $content){
		foreach ($rating as $i){
			if ($i == 0){
				echo json_encode(array('code' => 240, 'msg' => '별점을 모두 선택해주세요.'));
				return;
			}
		}
		
		$patternRating = "/^[1-5]$/";
		
		foreach ($rating as $idx => $i){
			if (!preg_match($patternRating, $i)){
				echo json_encode(array('code' => 400, 'msg' => 'Invalid input at ['.$idx.'] in '.__FUNCTION__));
				return false;
			}
		}
		
		$patternContent = '/\s/';
		
		if (preg_replace($patternContent, '', $content) == ''){
			echo json_encode(array('code' => 240, 'msg' => '내용을 작성해주세요.'));
			return false;
		}
		
		return true;
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
		
		$spModel = new SpotModel();
		$ctModel = new CategoryModel();
		
		$base_idx = Request::input('base');
			
		$latitude = Request::input('lat');
		$longitude = Request::input('lng');
		
		$spot_idx = Request::input('idx');		// if modify, exist
		
		if (!$spot_idx){
			$limit = (object)Common::getMapLimit();
			
			if ($latitude > $limit->n || $longitude > $limit->e || $latitude < $limit->s || $longitude < $limit->w){
				header("Location: http://".$_SERVER['HTTP_HOST']);
				die();
			}
		}
		else{
			$result = $spModel->checkExistSpot($spot_idx);
			
			if ($result['code'] != 200){
				// not found
				header("Location: http://".$_SERVER['HTTP_HOST']);
				die();
			}
		}
		
		$purposeList = $ctModel->getPurposeList();
		$kindList = $ctModel->getKindList();
		
		$page = 'spot_write';
		return view($page, array(
				'page'			=> $page,
				'latitude'		=> $latitude,
				'longitude'		=> $longitude,
				'base_idx'		=> $base_idx,
				'spot_idx'		=> $spot_idx,
				'purposeList'	=> $purposeList['data'],
				'kindList'		=> $kindList['data']
		));
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
		
		$limit = (object)Common::getMapLimit();
		
		$latitude = Request::input('latitude');
		$longitude = Request::input('longitude');
		
		if ($latitude > $limit->n || $longitude > $limit->e || $latitude < $limit->s || $longitude < $limit->w){
			echo json_encode(array('code' => 400, 'msg' => 'Out of range'));
			return;
		}
		
		$spModel = new SpotModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$cate = Request::input('cate');
		$base_idx = Request::input('base');
		$name = Request::input('name');
		$content = Request::input('content');
		
		$img = Request::file('img');
		
		if (!$this::checkInput($cate, $name, $content))
			return;
		
		$name = Common::replaceStr($name);
		
		$result_create = $spModel->create($acc_idx, $cate, $latitude, $longitude, $img, $name, $content);
		
		if ($result_create['code'] != 200){
			echo json_encode($result_create);
			return;
		}
		
		// additional process
		$spot_idx = $result_create['data'];
		$modified_content = $result_create['content'];
		
		$spot_description = "Spot 생성";
		$spModel->makeLog($acc_idx, $spot_idx, $latitude, $longitude,/*$thumb, */$name, $modified_content, $cate, $spot_description);
		
		if ($base_idx != 0){
			$result_getIsCluster = $spModel->getIsCluster($base_idx);
			
			if ($result_getIsCluster['code'] != 200){
				echo json_encode($result_getIsCluster);
				return;
			}
			
			$clst = $result_getIsCluster['data'];
			
			if ($clst == 0){	// spot + spot
				$result_createCluster = $spModel->createCluster($acc_idx, $latitude, $longitude/*, 'thumb', 'name', 'content'*/);
				
				if ($result_createCluster['code'] != 200){
					echo json_encode($result_createCluster);
					return;
				}
				
				$new_clst = $result_createCluster['data'];
				
				$clst_description = 'Cluster 생성';
				$spModel->makeLog($acc_idx, $new_clst, $latitude, $longitude,/*$thumb, */'', '', '', $clst_description);
				
				$result_setClustered1 = $spModel->setClustered($base_idx, $new_clst);
				
				if ($result_setClustered1['code'] != 200){
					echo json_encode($result_setClustered1);
					return;
				}
				
				$result_setClustered2 = $spModel->setClustered($spot_idx, $new_clst);
				
				if ($result_setClustered2['code'] != 200){
					echo json_encode($result_setClustered2);
					return;
				}
				
				$spot_description2 = 'Cluster에 병합';
				
				$result_getLogRecent = $spModel->getLogRecent($base_idx);
				
				if ($result_getLogRecent['code'] != 200){
					echo json_encode($result_getLogRecent);
					return;
				}
				
				$spModel->makeLog(	
						$result_getLogRecent['data']->account_idx,
						$result_getLogRecent['data']->spot_idx,
						$result_getLogRecent['data']->latitude,
						$result_getLogRecent['data']->longitude,
						//$result_getLogRecent['data']->img,
						$result_getLogRecent['data']->name,
						$result_getLogRecent['data']->content,
						$result_getLogRecent['data']->category_idx,
						$spot_description2
				);
						
				$spModel->makeLog($acc_idx, $spot_idx, $latitude, $longitude,/*$thumb, */$name, $modified_content, $cate, $spot_description2);
				
				$clst_description2 = 'Spot 추가: '.$result_getLogRecent['data']->name.', '.$name;
				
				$spModel->makeLog($acc_idx, $new_clst, $latitude, $longitude,/*$thumb, */'', '', '', $clst_description2);
			}
			else{		// cluster + spot
				$result_setClustered = $spModel->setClustered($spot_idx, $base_idx);
				
				if ($result_setClustered['code'] != 200){
					echo json_encode($result_setClustered);
					return;
				}
				
				$spot_description2 = 'Cluster에 병합';
				
				$spModel->makeLog($acc_idx, $spot_idx, $latitude, $longitude,/*$thumb, */$name, $modified_content, $cate, $spot_description2);
				
				$clst_description2 = 'Spot 추가: '.$name;
				
				$spModel->makeLog($acc_idx, $base_idx, $latitude, $longitude,/*$thumb, */'', '', '', $clst_description2);
			}
		}
		
		echo json_encode($result_create);
	}
	
	public function getMarkerData(){
		header('Content-Type: application/json');
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			echo json_encode(array('code' => 400, 'msg' => 'Invalid Access'));
			return;
		}
		
		$spModel = new SpotModel();
		
		$purpose = Request::input('purpose', 0);
		$kind = Request::input('kind', 0);
		
		$result = $spModel->getMarkerData($purpose, $kind);
		
		if ($result['code'] == 240)
			$result['msg'] = '해당 조건의 Spot 검색 결과가 없습니다.';
		
		echo json_encode($result);//, JSON_UNESCAPED_UNICODE);
	}
	
	/*
	public function getSpotData(){
		// TODO: referer check
		$spModel = new SpotModel();
		
		$spot_idx = Request::input('idx');
		
		$result = $spModel->getSpotData($spot_idx);
		
		header('Content-Type: application/json');
		echo json_encode($result, JSON_UNESCAPED_SLASHES);
	}
	*/
	
	public function index(){
		//header('Content-Type: application/json');
		
		$spModel = new SpotModel();
		$ctModel = new CategoryModel();
	
		$spot_idx = Request::input('idx');
		
		$result_getRatingList = $spModel->getRatingList();
		
		if ($result_getRatingList['code'] == 500){
			echo json_encode($result_getRatingList);
			return;
		}
		
		$rating_kind = $result_getRatingList['data'];
		
		$result_getSpotData = $spModel->getSpotData($spot_idx, $rating_kind);
		
		if ($result_getSpotData['code'] != 200){
			if ($result_getSpotData['code'] == 500)
				echo json_encode(array('code' => 404, 'msg' => 'Page not found'));
			else
				echo json_encode($result_getSpotData);
			
			return;
		}
		
		$data = $result_getSpotData['data'];
		
		if (isset($_SERVER['HTTP_REFERER']))
			$spModel->increaseHit($spot_idx);
		
		$review = $spModel->getReviewList($spot_idx, $rating_kind)['data'];
		
		$review_img = array();
		
		for ($i = 0; $i < count($review); ++$i){
			$temp = array();
			
			for ($j = 0; $j < 3; ++$j){
				$iter = 'img'.$j;
				
				if ($review[$i]->$iter == '')
					continue;
				
				array_push($temp, $review[$i]->$iter);
			}
			
			array_push($review_img, $temp);
		}
		
		$stack = $data->category_idx;
		
		$cate_arr = $this::getCateKorean($stack);
		
		$page = 'spot';
		return view($page, array(
				'page'			=> $page,
				'data'			=> $data,
				'cate'			=> $cate_arr,
				'review' 		=> $review,
				'review_img'	=> $review_img,
				'rating_kind'	=> $rating_kind,
				'is_history'	=> false
		));
	}
	
	public function getMyReview(){
		Common::loginStateCheck();		// for session
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$spModel = new SpotModel();
		
		$logined = Request::input('logined');
		$adr_s3 = Request::input('adr_s3');
		
		$rating_kind = $spModel->getRatingList()['data'];
		
		if ($logined == 0){
			$page = 'myreview_write';
			return view($page, array(/*'page' => $page, */'rating_kind' => $rating_kind, 'logined' => $logined, 'adr_s3' => $adr_s3, 'is_modify' => false));
		}
		
		$review_idx = Request::input('review_idx');
		
		if ($review_idx == 0){
			$acc_idx = $_SESSION['idx'];
		
			$spot_idx = Request::input('spot_idx');
			
			$result = $spModel->getMyReview($acc_idx, $spot_idx, $rating_kind);
			
			if ($result['code'] != 200){
				$page = 'myreview_write';
				return view($page, array(/*'page' => $page, */'rating_kind' => $rating_kind, 'logined' => $logined, 'adr_s3' => $adr_s3, 'is_modify' => false));
			}
		}
		else{
			$result = $spModel->getMyReview2($review_idx, $rating_kind);
		
			if ($result['code'] != 200){	// temp
				// error page
				return;
			}
		}
		
		$img = array();
		
		for ($i = 0; $i < 3; ++$i){
			$iter = 'img'.$i;
			
// 			if ($result['data']->$iter == '')
// 				continue;
			if ($review_idx == 0){
				if ($result['data']->$iter == '')
					continue;
			}
			
			array_push($img, $result['data']->$iter);
		}
		
		$page = 'myreview';
		
		if ($review_idx != 0)
			$page .= '_write';
		
		return view($page, array(
				/*'page'		=> $page,*/
				'is_modify'		=> true,
				'logined'		=> $logined,
				'data'			=> $result['data'],
				'myreview_img'	=> $img,
				'rating_kind'	=> $rating_kind,
				'adr_s3'		=> $adr_s3
		));
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
		
		$spModel = new SpotModel();
		$ctModel = new CategoryModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$spot_idx = Request::input('idx');
		
		$result_getSpotData2 = $spModel->getSpotData2($spot_idx, $acc_idx);
		
		if ($result_getSpotData2['code'] != 200){
			echo json_encode($result_getSpotData2);
			return;
		}
		
		$result_getSpotData2['data']->name = Common::originalStr($result_getSpotData2['data']->name);
		
		$stack = $result_getSpotData2['data']->category_idx;
		
		$cate_arr = array();
		
		while (strlen($stack)){
			$cate = substr($stack, 0, strpos($stack, ","));
			
			$large = $cate[0];
			$small = substr($cate, 1);
			
			if ($large == 'p')
				$temp = $ctModel->getPurpose($small)['data'][0]->name;
			elseif ($large == 'k')
				$temp = $ctModel->getKind($small)['data'][0]->name;
			else;
			
			array_push($cate_arr, array($large, $small, $temp));
			
			$stack = substr($stack, strpos($stack, ",") + 1);
		}
		
		echo json_encode(array(
				'code'		=> 1,
				'msg'		=> 'success',
				'n_data'	=> $result_getSpotData2['data'],
				'img_num'	=> $result_getSpotData2['num'],
				'prev_img'	=> $result_getSpotData2['prev_img'],
				'cate'		=> $cate_arr
		));
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
		
		$spModel = new SpotModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$prev_thumb = Request::input('prev_thumb');
		$cate = Request::input('cate');
		$spot_idx = Request::input('idx');
		$name = Request::input('name');
		$content = Request::input('content');
		$description = Request::input('description');
 		$prev_img = Request::input('prev_img');
		
		$img = Request::file('img');
		
		if (!$this::checkInput($cate, $name, $content))
			return;
		
		$name = Common::replaceStr($name);
		$description = Common::replaceStr($description);
		
		$result = $spModel->update($spot_idx, $cate, $img, $name, $content, $prev_thumb, $prev_img);
		
		if ($result['code'] == 200){
			// If spot updated, additional process
			$spModel->makeLog($acc_idx, $spot_idx, NULL, NULL, /*$img, */$name, $result['content'], $cate, $description);
		}
		
 		header('Content-Type: application/json');
		echo json_encode($result);
	}
	
	public function historyIndex(){
		$spModel = new SpotModel();
	
		$spot_idx = Request::input('idx');
		
		$logList = $spModel->getLogList($spot_idx);
		
		if ($logList['code'] == 0){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$page = 'spot_history';
		return view($page, array('page' => $page, 'spot_idx' => $spot_idx, 'data' => $logList['data']));
	}
	
	public function historyDetail(){
		$spModel = new SpotModel();
		
		$spot_idx = Request::input('idx');
		$version = Request::input('ver');
		
		$data = $spModel->getLog($spot_idx, $version);
		
		if ($data['code'] == 0){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$stack = $data['data']->category_idx;
		
		$cate_arr = $this::getCateKorean($stack);
		
		$page = 'spot';
		return view($page, array('page' => $page, 'data' => $data['data'], 'cate' => $cate_arr, 'is_history' => true, 'ver' => $version));
	}
	
	public function writeReview(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		$spModel = new SpotModel();
		$wlModel = new WishlistModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$spot_idx = Request::input('idx');
		$content = Request::input('content');
		
		$result_checkExistSpot = $spModel->checkExistSpot($spot_idx);
		
		if ($result_checkExistSpot['code'] != 200){
			if ($result_checkExistSpot['code'] == 240)
				echo json_encode(array('code' => 404, 'msg' => 'Spot Not Found'));
			else
				echo json_encode($result_checkExistSpot);
			
			return;
		}
			
		$rating = array();
		$rating_kind = $spModel->getRatingList()['data'];
		
		$result_getMyReview = $spModel->getMyReview($acc_idx, $spot_idx, $rating_kind);
		
		if ($result_getMyReview['code'] == 200){
			echo json_encode(array('code' => 240, 'msg' => '이미 리뷰룰 작성한 Spot입니다.'));
			return;
		}
		
		foreach ($rating_kind as $i)
			$rating[$i->eng_name] = Request::input($i->eng_name);
		
		$img = array();
		
		for ($i = 0; $i < 3; ++$i)
			$img[$i] = Request::file('img'.$i);
		
		if (!$this::checkReviewInput($rating, $content))
			return;
		
		$content = Common::replaceStr($content);
		
		$result_createReview = $spModel->createReview($acc_idx, $spot_idx, $rating, $content, $img, $rating_kind);
		
		if ($result_createReview['code'] != 200){
			echo json_encode($result_createReview);
			return;
		}
		
		// additional process
		$spModel->updateRating($spot_idx, $rating_kind);
		
		$result_checkExist = $wlModel->checkExist($acc_idx, $spot_idx);
		
		if ($result_checkExist['code'] != 200){		// escape
			echo json_encode($result_createReview);
			return;
		}
		
		$result_getWishData = $wlModel->getWishData($acc_idx);
		
		if ($result_getWishData['code'] != 200){
			echo json_encode($result_getWishData);
			return;
		}
		
		$w_check = $result_getWishData['data'];		// wish_num, pSum
		
		$new_num = $w_check->wish_num + 1;
		$new_rating = ($w_check->pSum + $result_createReview['avg']) / $new_num;
		
		$wlModel->setWishData($acc_idx, $new_rating, $new_num);
		
		echo json_encode($result_createReview);
	}
	
	public function updateReview(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		header('Content-Type: application/json');
		
		$spModel = new SpotModel();
		$wlModel = new WishlistModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$review_idx = Request::input('review_idx');
		$spot_idx = Request::input('spot_idx');
		$content = Request::input('content');
		
		$result_getReviewWriter = $spModel->getReviewWriter($review_idx);
		
		if ($result_getReviewWriter['code'] != 200){
			echo json_encode($result_getReviewWriter);
			return;
		}
		
		$writer = $result_getReviewWriter['data'];
		
		if ($acc_idx != $writer){
			echo json_encode(array('code' => 403, 'msg' => 'Disallowed access'));
			return;
		}
		
		$result_checkExistSpot = $spModel->checkExistSpot($spot_idx);
		
		if ($result_checkExistSpot['code'] != 200){
			if ($result_checkExistSpot['code'] == 250)
				echo json_encode(array('code' => 404, 'msg' => 'Spot Not Found'));
			else
				echo json_encode($result_checkExistSpot);
			
			return;
		}
			
		$rating = array();
		$rating_kind = $spModel->getRatingList()['data'];
		
		foreach ($rating_kind as $i)
			$rating[$i->eng_name] = Request::input($i->eng_name);
		
		$img = array();
		$prev_img = array();
		
		for ($i = 0; $i < 3; ++$i){
			$img[$i] = Request::file('img'.$i);
			$prev_img[$i] = Request::input('prev_img'.$i);
		}
		
		if (!$this::checkReviewInput($rating, $content))
			return;
		
		$content = Common::replaceStr($content);
		
		$result_updateReview = $spModel->updateReview($review_idx, $rating, $content, $img, $prev_img, $rating_kind);
		
		if ($result_updateReview['code'] != 200){
			echo json_encode($result_updateReview);
			return;
		}
		
		// additional process
		$spModel->updateRating($spot_idx, $rating_kind);
		
		$result_checkExist = $wlModel->checkExist($acc_idx, $spot_idx);
		
		if ($result_checkExist['code'] != 200){
			echo json_encode($result_updateReview);
			return;
		}
		
		$result_getWishData = $wlModel->getWishData($acc_idx);
		
		if ($result_getWishData['code'] != 200){
			echo json_encode($result_getWishData);
			return;
		}
		
		$w_check = $result_getWishData['data'];
		
		$new_num = $w_check->wish_num + 1;
		$new_rating = ($w_check->pSum + $result_updateReview['avg']) / $new_num;
		
		$wlModel->setWishData($acc_idx, $new_rating, $new_num);
		
		echo json_encode($result_updateReview);
	}
	
	public function clusterIndex(){
		$spModel = new SpotModel();
		
		$clst_idx = Request::input('idx');
		
		$purpose = Request::input('purpose', 0);
		$kind = Request::input('kind', 0);
		
		$adr_ctr = Request::input('adr_ctr');
		$adr_s3 = Request::input('adr_s3');
		
		$result_getClusterData = $spModel->getClusterData($clst_idx);
		
		if ($result_getClusterData['code'] != 200){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$result_getClusteredSpot = $spModel->getClusteredSpot($clst_idx, $purpose, $kind);
		
		if (isset($_SERVER['HTTP_REFERER']))
			$spModel->increaseHit($clst_idx);
		
		$page = 'cluster_list';
		return view($page, array(
				/*'page'	=> $page,*/
				'data'		=> $result_getClusterData['data'],
				'list'		=> $result_getClusteredSpot,
				//'is_history'	=> false	// temp
				'adr_ctr'	=> $adr_ctr,
				'adr_s3'	=> $adr_s3
		));
	}
	
	public function iOSgetSpotData(){
		header('Content-Type: application/json');
		
		if (!isset($_SERVER['HTTP_REFERER'])){
			echo json_encode(array('code' => 400, 'msg' => 'Invalid Access'));
			return;
		}
		
		$spModel = new SpotModel();
		$ctModel = new CategoryModel();
		
		$spot_idx = Request::input('idx');
		
		$result_getRatingList = $spModel->getRatingList();
		
		if ($result_getRatingList['code'] != 200){
			echo json_encode($result_getRatingList);
			return;
		}
		
		$rating_kind = $result_getRatingList['data'];
		
		$result_getSpotData = $spModel->getSpotData($spot_idx, $rating_kind);
		
		if ($result_getSpotData['code'] != 200){
			if ($result_getSpotData['code'] == 500)
				echo json_encode(array('code' => 404, 'msg' => 'Page not found'));
			else
				echo json_encode($result_getSpotData);
			
			return;
		}
		
		$spot_data = $result_getSpotData['data'];
		
		$spModel->increaseHit($spot_idx);
	
		$review = $spModel->getReviewList($spot_idx, $rating_kind)['data'];
	
		$review_img = array();
		
		for ($i = 0; $i < count($review); ++$i){
			$temp = array();
			
			for ($j = 0; $j < 3; ++$j){
				$iter = 'img'.$j;
			
				if ($review[$i]->$iter == '')
					continue;
				
				array_push($temp, $review[$i]->$iter);
			}
		
			array_push($review_img, $temp);
		}
	
		$stack = $spot_data->category_idx;
	
		$cate_arr = $this::getCateKorean($stack);
		
		$return_data = (object)array(
				'spot_data'		=> $spot_data,
				'cate'			=> $cate_arr,
				'review' 		=> $review,
				'review_img'	=> $review_img,
				'rating_kind'	=> $rating_kind,
				'is_history'	=> false
		);
		
		echo json_encode(array('code' => 200, 'msg' => 'success', 'data' => $return_data));//, JSON_UNESCAPED_UNICODE);
	}
}
