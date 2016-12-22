<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\RecommendModel;
use Request;

// Recommendation System Controller
class RecommendationController extends Controller{
	private static $min_co_rate = 5;		// for Significance Weighting
	private static $max_neighbor = 5;		// for Neighbor Selection
	private static $max_recommend = 3;		// for Rating(ranking) Prediction
	private static $max_distance = 45;		// for Distance Weighting
	
	// Collaborative Filtering
	public function collaborative(){
		// TODO_1: referer check
		// TODO_2: neighbor rating이 아닌 실제 rating 보여줘야 한다??
		if (Common::loginStateCheck() != 1){
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
		
		$rcModel = new RecommendModel();
		
		$acc_idx = $_SESSION['idx'];
		
		$latitude = Request::input('lat');
		$longitude = Request::input('lng');
		
		$anotherList = $rcModel->getAnotherList($acc_idx)['data'];
		
		if ($anotherList == NULL){
			$page = 'recommend';
			return view($page, array('page' => $page, 'cnt' => 0));
		}
		
		
		
		// Binding
		$g_anotherList = array();
		$g_temp = array();
		
		$prev = $anotherList[0]->idx;
		
		foreach ($anotherList as $i){
			if ($prev != $i->idx){
				array_push($g_anotherList, $g_temp);
				
				$prev = $i->idx;
				$g_temp = [];
			}
			
			array_push($g_temp, $i);
		}
		
		array_push($g_anotherList, $g_temp);
		// Binding end
		
		
		
		$relative = $rcModel->getRelative($acc_idx, $g_anotherList)['data'];
		
		
		
		// Correlation Coefficient
		$proportion = array();
		
		foreach ($relative as $i){
			// Average & Deviation
			$num = count($i['active']);
			
			$devArrA = array();
			$devArrU = array();
			
			$avgA = $avgU = 0;
			
			foreach ($i['active'] as $j)
				$avgA += $j->rating;
			
			$avgA /= $num;
			
			foreach ($i['active'] as $j){
				$deviationA = $j->rating - $avgA;
				array_push($devArrA, $deviationA);
			}
			
			foreach ($i['another'] as $j)
				$avgU += $j->rating;
			
			$avgU /= $num;
			
			foreach ($i['another'] as $j){
				$deviationU = $j->rating - $avgU;
				array_push($devArrU, $deviationU);
			}
			
			// Variance & Standard Deviation
			$varA = $varU = 0;
				
			for ($j = 0; $j < $num; ++$j)
				$varA += pow($devArrA[$j], 2);
			
			$varA /= $num;
			
			if ($varA == 0)
				continue;
			
			$stdDevA = pow($varA, 1/2);
			
			for ($j = 0; $j < $num; ++$j)
				$varU += pow($devArrU[$j], 2);
			
			$varU /= $num;
			
			if ($varU == 0)
				continue;
			
			$stdDevU = pow($varU, 1/2);
			
			// Covariance & Correlation Coefficient
			$covar = 0;
			
			for ($j = 0; $j < $num; ++$j)
				$covar += ($devArrA[$j] * $devArrU[$j]);
			
			$covar /= $num;
			
			if ($covar <= 0)
				continue;
			
			$corr = $covar / ($stdDevA * $stdDevU);
			
			array_push($proportion, array('u_idx' => $i['u_idx'], 'num' => $num, 'corr' => $corr));
			
// 			print("u_idx: ".$i['u_idx']."<br>");
// 			print("num: ".$num."<br>");
// 			print("avgA: ".$avgA."<br>");
// 			print("avgU: ".$avgU."<br>");
// 			print("varA: ".$varA."<br>");
// 			print("varU: ".$varU."<br>");
// 			print("stdDevA: ".$stdDevA."<br>");
// 			print("stdDevU: ".$stdDevU."<br>");
// 			print("covar: ".$covar."<br>");
// 			print("corr: ".$corr."<br>");
// 			print("<br>");
		}
		
// 		print_r($proportion); print("<br>");	// temp
		
		$proportion_cnt = count($proportion);
		
		if ($proportion_cnt == 0){
			$page = 'recommend';
			return view($page, array('page' => $page, 'cnt' => 0));
		}

		// Significance Weighting
		for ($i = 0; $i < $proportion_cnt; ++$i){
			if ($proportion[$i]['num'] < $this::$min_co_rate){
				$sw = $proportion[$i]['num'] / $this::$min_co_rate;
				$proportion[$i]['corr'] *= $sw;
			}
		}
		
		// Neighbor Selection
		$wns_proportion = array();	// weighted and selected proportion
		
		if ($this::$max_neighbor < $proportion_cnt){
			// TODO: Need test for this branch.
			$wns_cnt = $this::$max_neighbor;
			
			for ($i = 0; $i < $wns_cnt; ++$i){
				$temp_idx = $temp_max = -1;
				
				for ($j = 0; $j < $proportion_cnt; ++$j){
					if ($proportion[$j]['corr'] > $temp_max){
						$temp_idx = $j;
						$temp_max = $proportion[$temp_idx]['corr'];
					}
				}
				
				array_push($wns_proportion, $proportion[$temp_idx]);
				
				$proportion[$temp_idx]['corr'] = -1;
			}
		}
		else{
			$wns_cnt = $proportion_cnt;
			
			for ($i = 0; $i < $wns_cnt; ++$i)
				array_push($wns_proportion, $proportion[$i]);
		}
		
		// Rating(ranking) Prediction
		$unratedList = $rcModel->getUnratedList($acc_idx)['data'];
		$avgAnotherList = $rcModel->getAvgAnotherList($wns_proportion, $unratedList)['data'];
		$avgAnotherList_cnt = count($avgAnotherList);
		
		$recommend_max = ($this::$max_recommend < $avgAnotherList_cnt)? $this::$max_recommend: $avgAnotherList_cnt;
		
		
		
		// Distance Weighting
		$dw_avgAnotherList = array();
		
		foreach ($avgAnotherList as $i){
			$demp = 10000 * sqrt(pow($latitude - $i->latitude, 2) + pow($longitude - $i->longitude, 2));
			
			if ($demp > $this::$max_distance){
				$dw = $this::$max_distance / $demp;
				$i->avg *= $dw;
			}
			
			array_push($dw_avgAnotherList, (object)array('spot_idx' => $i->spot_idx, 'avg' => $i->avg));
		}
		// Distance Weighting end
		
		
		
		$recommend = array();
		
		for ($i = 0; $i < $recommend_max; ++$i){
			$temp_idx = $temp_max = -1;
			
			for ($j = 0; $j < $avgAnotherList_cnt; ++$j){
				if ($dw_avgAnotherList[$j]->avg > $temp_max){
					$temp_idx = $j;
					$temp_max = $dw_avgAnotherList[$temp_idx]->avg;
				}
			}
			
			array_push($recommend, clone $dw_avgAnotherList[$temp_idx]);
			
			$dw_avgAnotherList[$temp_idx]->avg = -1;
		}
		
		$result = $rcModel->getRecommendSpot($recommend)['data'];
		
		$page = 'recommend';
		return view($page, array('page' => $page, 'cnt' => $recommend_max, 'spot_info' => $result));
	}
}