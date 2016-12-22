<?php
namespace App\Http\models;
use DB;

class RecommendModel{
	private static $std_z = 0.675;			// for Normal Distribution, 25%
	private static $std_wishlist = 3.00;	// for Wishlist Rate
	
// 	function getRatingData($acc_idx){
// 		// TODO: inputErrorCheck
// 		$result = DB::Select('select spot_idx, rating from review where account_idx=? order by spot_idx asc', array($acc_idx));
		
// 		return array('code' => 1, 'msg' => 'success', 'data' => $result);
// 	}
	
// 	function getAnotherList($acc_idx){
// 		$result = DB::select('select	distinct account_idx as idx from review
// 										where spot_idx in (select spot_idx from review where account_idx=?) and account_idx!=?
// 										order by account_idx asc', array($acc_idx, $acc_idx));
		
// 		return array('code' => 1, 'msg' => 'success', 'data' => $result);
// 	}

	function getAnotherList($acc_idx){
		// TODO: inputErrorCheck
		
		// class 1: A&U review
		// class 2: A wishlist & U review
		// class 3: A&U wishlist
		// class 4: A review & U wishlist
		$result = DB::select('select account_idx as idx, 1 as class from review
										where spot_idx in (select spot_idx from review where account_idx=?) and account_idx!=?
								union
								select account_idx as idx, 2 as class from review
										where spot_idx in (select spot_idx from wishlist where account_idx=?) and account_idx!=?
								union
								select account_idx as idx, 3 as class from wishlist
										where spot_idx in (select spot_idx from wishlist where account_idx=?) and account_idx!=?
								union
								select account_idx as idx, 4 as class from wishlist
										where spot_idx in (select spot_idx from review where account_idx=?) and account_idx!=?
								order by idx asc, class asc',
								array($acc_idx, $acc_idx, $acc_idx, $acc_idx, $acc_idx, $acc_idx, $acc_idx, $acc_idx));
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
	
// 	function getRelative($a_idx, $ratingListU){
// 		// TODO: inputErrorCheck
// 		$relative = array();
		
// 		foreach ($ratingListU as $i){
// 			$resultA = DB::select('select 	spot_idx, rating from review
// 											where spot_idx in (select spot_idx from review where account_idx=?) and account_idx=?
// 											order by spot_idx asc', array($i->idx, $a_idx));
			
// 			$resultU = DB::select('select 	spot_idx, rating from review
// 											where spot_idx in (select spot_idx from review where account_idx=?) and account_idx=?
// 											order by spot_idx asc', array($a_idx, $i->idx));
			
// 			array_push($relative, array('u_idx' => $i->idx, 'active' => $resultA, 'another' => $resultU));
// 		}
		
// 		return array('code' => 1, 'msg' => 'success', 'data' => $relative);
// 	}
		
	function getRelative($a_idx, $g_ratingListU){
		// TODO: inputErrorCheck
		$relative = array();
		
		$wdataA = DB::select('select wish_rating, wish_num from member where idx=?', array($a_idx));
		
		if ($wdataA[0]->wish_num > 0)
			$implicitA = $wdataA[0]->wish_rating;
		else{
			$implicitA = DB::select('select format(('.$this::$std_z.'*std(rating))+avg(rating),2) as implicit from review where account_idx=?', array($a_idx));
			$implicitA = ($implicitA[0]->implicit == 0)? $this::$std_wishlist: $implicitA[0]->implicit;
		}
		
		foreach ($g_ratingListU as $i){
			$tempArr = array();
			
			foreach ($i as $j){
				$resultA = array();
				$resultU = array();
				
				switch ($j->class){
					case 1:		// A&U review
						$resultA = DB::select('select 	spot_idx, rating, '.$j->class.' as class from review
								where spot_idx in (select spot_idx from review where account_idx=?) and account_idx=?
								order by spot_idx asc', array($j->idx, $a_idx));
						
						$resultU = DB::select('select 	spot_idx, rating, '.$j->class.' as class from review
								where spot_idx in (select spot_idx from review where account_idx=?) and account_idx=?
								order by spot_idx asc', array($a_idx, $j->idx));
						break;
						
					case 2:		// A wishlist & U review
						$tempA = DB::select('select	spot_idx from wishlist
									where spot_idx in (select spot_idx from review where account_idx=?) and account_idx=?
									order by spot_idx asc', array($j->idx, $a_idx));
						
						foreach ($tempA as $k)
							array_push($resultA, (object)array('spot_idx' => $k->spot_idx, 'rating' => $implicitA, 'class' => $j->class));
						
						$resultU = DB::select('select	spot_idx, rating, '.$j->class.' as class from review
									where spot_idx in (select spot_idx from wishlist where account_idx=?) and account_idx=?
									order by spot_idx asc', array($a_idx, $j->idx));
						break;
						
					case 3:		// A&U wishlist
						$tempA = DB::select('select	spot_idx from wishlist
									where spot_idx in (select spot_idx from wishlist where account_idx=?) and account_idx=?
									order by spot_idx asc', array($j->idx, $a_idx));
						
						foreach ($tempA as $k)
							array_push($resultA, (object)array('spot_idx' => $k->spot_idx, 'rating' => $implicitA, 'class' => $j->class));
						
						$tempU = DB::select('select spot_idx from wishlist
								where spot_idx in (select spot_idx from wishlist where account_idx=?) and account_idx=?
								order by spot_idx asc', array($a_idx, $j->idx));
						
// 						foreach ($tempU as $k){
// 							$implicitU = DB::select('select format(('.$this::$std_z.'*std(rating))+avg(rating),2) as implicit from review where account_idx=?', array($j->idx));
// 							$implicitU = ($implicitU[0]->implicit == 0)? $this::$std_wishlist: $implicitU[0]->implicit;
							
// 							array_push($resultU, (object)array('spot_idx' => $k->spot_idx, 'rating' => $implicitU, 'class' => $j->class));
// 						}
						
						foreach ($tempU as $k){
							$wdataU = DB::select('select wish_rating, wish_num from member where idx=?', array($j->idx));
							
							if ($wdataU[0]->wish_num > 0)
								$implicitU = $wdataU[0]->wish_rating;
							else{
								$implicitU = DB::select('select format(('.$this::$std_z.'*std(rating))+avg(rating),2) as implicit from review where account_idx=?', array($j->idx));
								$implicitU = ($implicitU[0]->implicit == 0)? $this::$std_wishlist: $implicitU[0]->implicit;
							}
							
							array_push($resultU, (object)array('spot_idx' => $k->spot_idx, 'rating' => $implicitU, 'class' => $j->class));
						}
						
						break;
						
					case 4:		// A review & U wishlist
						$resultA = DB::select('select	spot_idx, rating, '.$j->class.' as class from review
										where spot_idx in (select spot_idx from wishlist where account_idx=?) and account_idx=?
										order by spot_idx asc', array($j->idx, $a_idx));
						
						$tempU = DB::select('select	spot_idx from wishlist
									where spot_idx in (select spot_idx from review where account_idx=?) and account_idx=?
									order by spot_idx asc', array($a_idx, $j->idx));
						
// 						foreach ($tempU as $k){
// 							$implicitU = DB::select('select format(('.$this::$std_z.'*std(rating))+avg(rating),2) as implicit from review where account_idx=?', array($j->idx));
// 							$implicitU = ($implicitU[0]->implicit == 0)? $this::$std_wishlist: $implicitU[0]->implicit;
							
// 							array_push($resultU, (object)array('spot_idx' => $k->spot_idx, 'rating' => $implicitU, 'class' => $j->class));
// 						}

						foreach ($tempU as $k){
							$wdataU = DB::select('select wish_rating, wish_num from member where idx=?', array($j->idx));
							
							if ($wdataU[0]->wish_num > 0)
								$implicitU = $wdataU[0]->wish_rating;
							else{
								$implicitU = DB::select('select format(('.$this::$std_z.'*std(rating))+avg(rating),2) as implicit from review where account_idx=?', array($j->idx));
								$implicitU = ($implicitU[0]->implicit == 0)? $this::$std_wishlist: $implicitU[0]->implicit;
							}
							
							array_push($resultU, (object)array('spot_idx' => $k->spot_idx, 'rating' => $implicitU, 'class' => $j->class));
						}
						
						break;
						
					default:
						break;
				}
				
				array_push($tempArr, (object)array('u_idx' => $j->idx, 'active' => $resultA, 'another' => $resultU));
			}
			
			$i_active = array();
			$i_another = array();
			
			$order = array(0, 3, 2, 1, 2);	// idx means class, and number means priority. (large number means higher priority)
			
			foreach ($tempArr as $j){
				foreach ($j->active as $kter => $k){
					$duplicate = false;
					
					foreach ($i_active as $lter => $l){
						if ($l->spot_idx == $k->spot_idx){
							if ($order[$l->class] < $order[$k->class]){
								$i_active[$lter] = $j->active[$kter];
								$i_another[$lter] = $j->another[$kter];
							}
							
							$duplicate = true;
							break;
						}
					}
					
					if ($duplicate == false){
						array_push($i_active, $j->active[$kter]);
						array_push($i_another, $j->another[$kter]);
					}
				}
			}
			
			array_push($relative, array('u_idx' => $tempArr[0]->u_idx, 'active' => $i_active, 'another' => $i_another));
		}
	
		return array('code' => 1, 'msg' => 'success', 'data' => $relative);
	}
	
	function getUnratedList($a_idx){
		// TODO: inputErrorCheck
		$result = DB::select('select	idx from spot
										where idx not in (select spot_idx from review where account_idx=? and spot_idx!=0)', array($a_idx));
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
	
// 	function getRatingAvg($acc_idx){
// 		// TODO: inputErrorCheck
// 		$result = DB::select('select avg(rating) as avg from review where account_idx=?', array($acc_idx));
		
// 		return array('code' => 1, 'msg' => 'success', 'data' => $result);
// 	}

	function getAvgAnotherList($wns_proportion, $unratedList){
		// TODO: inputErrorCheck
		$query_piece = '';
		
		foreach ($wns_proportion as $idx => $i){
			if ($idx != 0)
				$query_piece .= ',';
			
			$query_piece .= $i['u_idx'];
		}
		
		$avgAnotherList = array();
		
		foreach ($unratedList as $i){
			$result = DB::select('select	s.idx as spot_idx,
											s.latitude as latitude,
											s.longitude as longitude,
											avg(r.rating) as avg
											from spot as s, review as r
											where r.account_idx in ('.$query_piece.') and s.idx=? and r.spot_idx=s.idx', array($i->idx));
			
			if ($result[0]->avg != NULL)
				array_push($avgAnotherList, $result[0]);
		}
		
		return array('code' => 1, 'msg' => 'success', 'data' => $avgAnotherList);
	}
	
	function getRecommendSpot($recommend){
		// TODO: inputErrorCheck
		$result = array();
		
		foreach ($recommend as $i){
			$data = DB::select('select idx, img, name from spot where idx=?', array($i->spot_idx));
			
			array_push($result, array('data' => $data[0], 'rating' => $i->avg));
		}
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
}