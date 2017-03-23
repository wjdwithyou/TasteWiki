<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\WishlistModel;
use App\Http\models\SpotModel;
use Request;

class MypageController extends Controller{
	public function index(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		$page = 'mypage';
		return view($page, array('page' => $page));
	}

	public function profileIndex(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		$kind = $_SESSION['kind'];

		$page = 'mypage_profile';
		return view($page, array('page' => $page, 'kind' => $kind));
	}

	public function wishlistIndex(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		$wlModel = new WishlistModel();

		$acc_idx = $_SESSION['idx'];

		$data = $wlModel->getWishlist($acc_idx);

		$page = 'mypage_wishlist';
		return view($page, array('page' => $page, 'data' => $data));
	}

	public function reviewIndex(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		// impl.

		$page = 'mypage_review';
		return view($page, array('page' => $page));
	}

	public function addWishlist(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		header('Content-Type: application/json');

		$wlModel = new WishlistModel();
		$spModel = new SpotModel();

		$acc_idx = $_SESSION['idx'];

		$spot_idx = Request::input('idx');

		$result_checkExistSpot = $spModel->CheckExistSpot($spot_idx);

		if ($result_checkExistSpot['code'] != 200){
			if ($result_checkExistSpot['code'] == 240)
				echo json_encode(array('code' => 400, 'msg' => 'Invalid index'));
			else
				echo json_encode($result_checkExistSpot);

			return;
		}

		$result_getIsCluster = $spModel->getIsCluster($spot_idx);

		if ($result_getIsCluster['code'] != 200){
			echo json_encode($result_getIsCluster);
			return;
		}

		$is_cluster = $result_getIsCluster['data'];

		if ($is_cluster != 0){
			echo json_encode(array('code' => 400, 'msg' => 'Cannot add cluster'));
			return;
		}

		$result_getWishlistCnt = $wlModel->getWishlistCnt($acc_idx);

		if ($result_getWishlistCnt['code'] != 200){
			echo json_encode($result_getWishlistCnt);
			return;
		}

		$cnt = $result_getWishlistCnt['data'];

		if ($cnt >= 10){
			echo json_encode(array('code' => 240, 'msg' => 'Wishlist는 최대 10개까지 저장할 수 있습니다.'));
			return;
		}

		$result_checkExist = $wlModel->checkExist($acc_idx, $spot_idx);

		if ($result_checkExist['code'] != 240){
			if ($result_checkExist['code'] == 200)
				echo json_encode(array('code' => 240, 'msg' => '이미 Wishlist에 저장된 Spot입니다.'));
			else
				echo json_encode($result_checkExist);

			return;
		}

		$result_create = $wlModel->create($acc_idx, $spot_idx);

		if ($result_create['code'] == 200){
			$spModel->increaseWishlist($spot_idx);

			$result_getAlreadyReview = $spModel->getAlreadyReview($acc_idx, $spot_idx);		// review

			if ($result_getAlreadyReview['code'] == 200){
				$result_getWishData = $wlModel->getWishData($acc_idx);						// wish_num, pSum

				if ($result_getWishData['code'] != 200){
					//echo json_encode($result_getWishData);
					echo json_encode($result_create);
					return;
				}

				$w_check = $result_getWishData['data'];

				$new_num = $w_check->wish_num + 1;
				$new_rating = ($w_check->pSum + $result_getAlreadyReview['data']->rating) / $new_num;

				$wlModel->setWishData($acc_idx, $new_rating, $new_num);
			}
		}

		echo json_encode($result_create);
	}

	public function deleteWishlist(){
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		if (!isset($_SERVER['HTTP_REFERER'])){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}

		header('Content-Type: application/json');

		$wlModel = new WishlistModel();
		$spModel = new SpotModel();

		$acc_idx = $_SESSION['idx'];

		$wish_idx = Request::input('idx');

		$result_getSpotIdx = $wlModel->getSpotIdx($wish_idx);

		if ($result_getSpotIdx['code'] != 200){
			echo json_encode($result_getSpotIdx);
			return;
		}

		$spot_idx = $result_getSpotIdx['data'];

		$result_delete = $wlModel->delete($acc_idx, $wish_idx);

		if ($result_delete['code'] == 200)
			$spModel->decreaseWishlist($spot_idx);

		echo json_encode($result_delete);
	}
}
