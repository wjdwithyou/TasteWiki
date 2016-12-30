<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\TestModel;
use App\Http\models\SpotModel;
use Request;

class TestController extends Controller{
	// for manually update spot rating
	// use: .../Test/updateRatingM?idx=XX
	public function updateRatingM(){
		if (Common::managerCheck() != 1){
			header("Location:http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$spModel = new SpotModel();
		
		$spot_idx = Request::input('idx');
		$rating_kind = $spModel->getRatingList()['data'];
		
		$spModel->updateRating($spot_idx, $rating_kind);
		
		return $spot_idx;
	}
	
	// for create empty cluster
	// use: .../Test/createClusterM?lat=xx.xxxxx&lng=xx.xxxxx
	public function createClusterM(){
		if (Common::managerCheck() != 1){
			header("Location:http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$spModel = new SpotModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$latitude = Request::input('lat');
		$longitude = Request::input('lng');
		
		$result_createCluster = $spModel->createCluster($acc_idx, $latitude, $longitude/*, $thumb, $name, $content*/);
		
		if ($result_createCluster['code'] == 0){
			echo json_encode($result_createCluster);
			return;
		}
		
		$clst_idx = $result_createCluster['data'];
		
		$description = 'Cluster 생성';
		$spModel->makeLog($acc_idx, $clst_idx, $latitude, $longitude,/*thumb, */'', '', '', $description);
		
		echo json_encode($result_createCluster);
		return;
	}
	
	// for add already existing spot to cluster
	// use: .../Test/setClusteredM?spot_idx=XX&clst_idx=XX
	public function setClusteredM(){
		if (Common::managerCheck() != 1){
			header("Location:http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$spModel = new SpotModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$spot_idx = Request::input('spot_idx');
		$clst_idx = Request::input('clst_idx');
		
		
		
		// check
		$result_getIsCluster1 = $spModel->getIsCluster($spot_idx);
		
		if ($result_getIsCluster1['code'] == 0){
			echo json_encode($result_getIsCluster1);
			return;
		}
		
		if ($result_getIsCluster1['data'] == 1){
			echo json_encode(array('code' => 0, 'msg' => $spot_idx.' is Cluster!'));
			return;
		}
		
		$result_getIsCluster2 = $spModel->getIsCluster($clst_idx);
		
		if ($result_getIsCluster2['code'] == 0){
			echo json_encode($result_getIsCluster2);
			return;
		}
		
		if ($result_getIsCluster2['data'] == 0){
			echo json_encode(array('code' => 0, 'msg' => $clst_idx.' is Spot!'));
			return;
		}
		
		
		
		// set clustered
		$result_setClustered = $spModel->setClustered($spot_idx, $clst_idx);
		
		if ($result_setClustered['code'] == 0){
			echo json_encode($result_setClustered);
			return;
		}
		
		
		
		// get recent log
		$result_getLogRecent1 = $spModel->getLogRecent($spot_idx);
		
		if ($result_getLogRecent1['code'] == 0){
			echo json_encode($result_getLogRecent1);
			return;
		}
		
		$result_getLogRecent2 = $spModel->getLogRecent($clst_idx);
		
		if ($result_getLogRecent2['code'] == 0){
			echo json_encode($result_getLogRecent2);
			return;
		}
		
		
		
		// update spot lat/lng to cluster lat/lng
		$result_updateLatLng = $spModel->updateLatLng(
				$spot_idx,
				$result_getLogRecent2['data']->latitude,
				$result_getLogRecent2['data']->longitude
		);
		
		if ($result_updateLatLng['code'] == 0){
			echo json_encode($result_updateLatLng);
			return;
		}
		
		
		
		// clst log
		$clst_description = 'Spot 추가: '.$result_getLogRecent1['data']->name;
		
		$spModel->makeLog(	
				$acc_idx,
				$result_getLogRecent2['data']->spot_idx,
				$result_getLogRecent2['data']->latitude,
				$result_getLogRecent2['data']->longitude,
				//$result_getLogRecent2['data']->img,
				$result_getLogRecent2['data']->name,
				$result_getLogRecent2['data']->content,
				$result_getLogRecent2['data']->category_idx,
				$clst_description
		);
		
		// spot log
		$spot_description = 'Cluster에 병합';
		
		$spModel->makeLog(
				$acc_idx,
				$result_getLogRecent1['data']->spot_idx,
				$result_getLogRecent2['data']->latitude,
				$result_getLogRecent2['data']->longitude,
				//$result_getLogRecent1['data']->img,
				$result_getLogRecent1['data']->name,
				$result_getLogRecent1['data']->content,
				$result_getLogRecent1['data']->category_idx,
				$spot_description
		);
		
		echo json_encode($result_setClustered);
		return;
	}
	
	/*
	public function test(){
		$s = getimagesize('ball.bmp');		// [0]: width
		// [1]: height
		// [2]: 1-gif, 2-jpg, 3-png, 6-bmp, 7-tiff...
		// [3]: string for html tag
		
		print_r($s);
		print ("<br>");
		
		// IMAGETYPE-XXX
		// 			1.gif
		// 			2.jpeg
		// 			3.png
		// 			4.swf
		// 			5.psd
		// 			6.bmp
		// 			7.tiff
		// 			8.tiff
		// 			9.jpc
		// 			10.jp2
		// 			11.jpx
		// 			12.jb2
		// 			13.swf
		// 			14.iff
		// 			15.bmp
		// 			16.xbm
		// 			17.ico
		
		
		
		//$a = imagecreatefromjpeg('testsmallimage.jpg');
		$a = imagecreatefromstring('testAAAAA.png');
		imagejpeg($a, 'modified.jpg');
	}
	*/
	
	/*
	// for spot_log init.
	// already exist spot -> make log
	public function migrate(){
		if (Common::managerCheck() != 1){
			header("Location:http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$testModel = new TestModel();
		
		print_r ($testModel->migrate());
		
		return;
	}
	*/
	
	/*
	// PHP overloading test
	public function test1(){
		$testModel = new TestModel();
		
		$testModel->test(1);
	}
	
	public function test2(){
	 	$testModel = new TestModel();
		
	 	$testModel->test(50, 300);
	}
	
	public function test3(){
		$testModel = new TestModel();
		
	 	$testModel->test(22, 300, 200);
	}
	*/
}
