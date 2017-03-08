<?php
namespace App\Http\models;
use DB;
use AWS;

include_once dirname(__FILE__)."/Common.php";

class SpotModel{
	function create($acc_idx, $cate, $latitude, $longitude, $thumb, $name, $content){
		if ($_ = checkParam(func_get_args()))
		//if ($_ = checkParam(array($acc_idx, $cate, $latitude, $longitude, $name, $content)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		// $thumb is necessary!

		$spot_idx = DB::table('spot')->insertGetId(
				array(
						'account_idx'		=> $acc_idx,
						'latitude'			=> $latitude,
						'longitude'			=> $longitude,
						//'img'				=> $thumb,
						'rating'			=> 0,
						'name'				=> $name,
						//'content'			=> $content,
						'writedate'			=> DB::raw('now()'),
						'lastdate'			=> DB::raw('now()'),
						'category_idx'		=> $cate,
						'hit_cnt'			=> 0,
						'wishlist_cnt'		=> 0,
						'rating_taste'		=> 0,
						'rating_price'		=> 0,
						'rating_service'	=> 0,
						'rating_access'		=> 0,
						'is_cluster'		=> 0,
						'cluster_idx'		=> NULL
				)
		);

		if ($spot_idx <= 0)
			return array('code' => 500, 'msg' => 'failure in \'create(1)\'');

		$s3pfxAdr = 'https://s3-ap-northeast-2.amazonaws.com/locawiki/spot/';
		$s3 = AWS::createClient('s3');

		$date = date("YmdHis", time());

		$thumb_ext = $thumb->getClientOriginalExtension();
		$thumb_name = $spot_idx.'_thumb_'.$date.'.'.$thumb_ext;

		$result_img = DB::update('update spot set img=? where idx=?', array($thumb_name, $spot_idx));

		if ($result_img != 1)
			return array('code' => 500, 'msg' => 'failure in \'create(2)\'');

		$s3->putObject(array(
				'Bucket'		=> 'locawiki',
				'Key'			=> 'spot/'.$thumb_name,
				'SourceFile'	=> $thumb
		));

		$img_str = $content;
		//$img_num = 0;

		while (strpos($img_str, "<img") !== false){
			$img_str = substr($img_str, strpos($img_str, "<img"));

			$img_str = substr($img_str, strpos($img_str, "src=\"") + 5);
			$img = substr($img_str, 0, strpos($img_str, "\""));				// http://localhost:8000/img/temp/1_img0_20160324165646.png
			//$img_ext = substr($img, strrpos($img, ".") + 1);

			$img_o_name = substr($img, strrpos($img, "/") + 1);				// 1_img0_... + ext
			$imgTemp = 'img/temp/'.$img_o_name;

			$imgPostName = substr($img, strpos($img, "_"));					// _img0_... + ext
			$img_name = $spot_idx.$imgPostName;								// 24_img0_... + ext
			$imgS3 = $s3pfxAdr.$img_name;

			$content = str_replace($img, $imgS3, $content);

			if (is_file($imgTemp)){
				$s3->putObject(array(
						'Bucket'		=> 'locawiki',
						'Key'			=> 'spot/'.$img_name,
						'SourceFile'	=> $imgTemp
				));
			}
		}

		$result_content = DB::update('update spot set content=? where idx=?', array($content, $spot_idx));

		if ($result_content != 1)
			return array('code' => 500, 'msg' => 'failure in \'create(3)\'');

		return array('code' => 200, 'msg' => 'create', 'data' => $spot_idx, 'content' => $content);
	}

	function getMarkerData($purpose, $kind){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$query = '';

		if ($purpose != 0)
			$query .= ' and category_idx like \'%p'.$purpose.',%\'';

		if ($kind != 0)
			$query .= ' and category_idx like \'%k'.$kind.',%\'';

		//$result = DB::select('select idx, latitude, longitude, img, name, is_cluster from spot where cluster_idx is NULL'.$query);

		$result = DB::select('SELECT idx, latitude, longitude, img, name, is_cluster FROM spot WHERE idx IN (
								SELECT DISTINCT cluster_idx FROM spot WHERE cluster_idx IS NOT NULL'.$query.
								') OR (cluster_idx IS NULL'.$query.')');

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result);
		else
			return array('code' => 240, 'msg' => 'no result');
	}

	function getSpotData($spot_idx, $rating_kind){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$query = '';

		foreach ($rating_kind as $i)
			$query .= ', rating_'.$i->eng_name;

		$result = DB::select('select idx, latitude, longitude, img'.$query.', name, content, lastdate, category_idx, hit_cnt from spot where is_cluster=0 and idx=?', array($spot_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 500, 'msg' => 'failure in \'getSpotData\'');
	}

	function getSpotData2($spot_idx, $acc_idx){		// temp, for modifyIndex
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select idx, img, name, content, category_idx from spot where idx=?', array($spot_idx));

		if (count($result) <= 0)
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);

		$s3pfxAdr = 'https://s3-ap-northeast-2.amazonaws.com/locawiki/spot/';
		$s3 = AWS::createClient('s3');

		$img_str = $result[0]->content;
		$img_num = 0;
		$prev_img = array();

		while (strpos($img_str, "<img") !== false){
			$img_str = substr($img_str, strpos($img_str, "<img"));

			$img_str = substr($img_str, strpos($img_str, "src=\"") + 5);
			$img = substr($img_str, 0, strpos($img_str, "\""));				// http://s3.../locawiki/spot/1_img0_20160324165646.png

			$img_o_name = substr($img, strrpos($img, "/") + 1);				// 1_img0_20160324165646.png
			//$imgTemp = 'img/temp/'.$img_o_name;

			array_push($prev_img, $img_o_name);

			$imgPostName = substr($img, strpos($img, "_"));					// _img0_20160324165646.png
			$img_name = $acc_idx.$imgPostName;								// 24_img0_20160324165646.png
			$imgTemp = 'img/temp/'.$img_name;								// host?

			++$img_num;

			if ($s3->doesObjectExist('locawiki', 'spot/'.$img_o_name)){
				$s3->getObject(array(
						'Bucket'	=> 'locawiki',
						'Key'		=> 'spot/'.$img_o_name,
						'SaveAs'	=> $imgTemp
				));

				$result[0]->content = str_replace($img, '/'.$imgTemp, $result[0]->content);
			}
		}

		return array('code' => 200, 'msg' => 'success', 'data' => $result[0], 'num' => $img_num, 'prev_img' => $prev_img);
	}

	function getNearSpot($spot_idx, $lat, $lng) {
		if ($_ = checkParam(func_get_args())) {
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		}

		$result = DB::select('SELECT idx, img, name, 10000*SQRT(POW(latitude-?,2)+POW(longitude-?,2)) AS distance FROM spot WHERE idx!=? AND is_cluster!=1 HAVING distance<45 ORDER BY distance ASC LIMIT 4', array($lat, $lng, $spot_idx));

		return array('code' => 200, 'msg' => 'success', 'data' => $result);
	}

	function checkExistSpot($spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select count(*) as cnt from spot where idx=?', array($spot_idx));

		if ($result[0]->cnt > 0)
			return array('code' => 200, 'msg' => 'exist spot');
		else
			return array('code' => 240, 'msg' => 'not exist');
	}

	function increaseHit($spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::update('update spot set hit_cnt=hit_cnt+1 where idx=?', array($spot_idx));

		if ($result == 1)
      		return array('code' => 1, 'msg' => 'success');
      	else
      		return array('code' => 0, 'msg' => 'failure: increaseHit()');
	}

	function increaseWishlist($spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::update('update spot set wishlist_cnt=wishlist_cnt+1 where idx=?', array($spot_idx));

		if ($result == 1)
			return array('code' => 200, 'msg' => 'success');
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}

	function decreaseWishlist($spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::update('update spot set wishlist_cnt=wishlist_cnt-1 where idx=?', array($spot_idx));

		if ($result == 1)
			return array('code' => 200, 'msg' => 'success');
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}

	function update($spot_idx, $cate, $thumb, $name, $content, $prev_thumb, $prev_img){
		if ($_ = checkParam(array($spot_idx, $cate, $name, $content)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$s3pfxAdr = 'https://s3-ap-northeast-2.amazonaws.com/locawiki/spot/';
		$s3 = AWS::createClient('s3');

		$date = date("YmdHis", time());

		// thumbnail image
		if ($thumb){
			// Remove previous thumbnail from S3
			/*
			// Access Denied..
			$prev = $s3->getIterator('ListObjects', array(
					'Bucket'	=> 'locawiki',
					'Prefix'	=> 'spot/'.$spot_idx.'_thumb',
			));

			foreach ($prev as $i){
				$s3->deleteObject(array(
						'Bucket'	=> 'locawiki',
						'Key'		=> $i['Key']
				));
			}
			*/

			$prev_thumb = substr($prev_thumb, strpos($prev_thumb, 'spot/'));

			$s3->deleteObject(array(
					'Bucket'	=> 'locawiki',
					'Key'		=> $prev_thumb
			));

			// Add new thumbnail to S3
			$thumb_ext = $thumb->getClientOriginalExtension();

			$thumb_name = $spot_idx.'_thumb_'.$date.'.'.$thumb_ext;

			$s3->putObject(array(
					'Bucket'		=> 'locawiki',
					'Key'			=> 'spot/'.$thumb_name,
					'SourceFile'	=> $thumb
			));

			$result_img = DB::update('update spot set img=? where idx=?', array($thumb_name, $spot_idx));

			if ($result_img != 1)
				return array('code' => 500, 'msg' => 'failure in \'update(1)\'');
		}

		// attached image

		// Remove previous attached image from S3
		/*
		// Access Denied..
		$prevList = $s3->getIterator('ListObjects', array(
				'Bucket'	=> 'locawiki',
				'Prefix'	=> 'spot/'.$spot_idx.'_img'
		));

		foreach ($prevList as $i){
			$s3->deleteObject(array(
					'Bucket'	=> 'locawiki',
					'Key'		=> $i['Key']
			));
		}
		*/

		$prev_arr = array();
		$prev_img .= ',';

		while (strpos($prev_img, ",") !== false){
			$prev_temp = substr($prev_img, 0, strpos($prev_img, ","));

			array_push($prev_arr, $prev_temp);

			$prev_img = substr($prev_img, strpos($prev_img, ",") + 1);
		}

		for ($i = 0; $i < count($prev_arr); ++$i){
			$s3->deleteObject(array(
					'Bucket'	=> 'locawiki',
					'Key'		=> 'spot/'.$prev_arr[$i]
			));
		}

		$img_str = $content;
		$cnt = 0;

		while (strpos($img_str, "<img") !== false){
			$img_str = substr($img_str, strpos($img_str, "<img"));

			$img_str = substr($img_str, strpos($img_str, "src=\"") + 5);
			$img = substr($img_str, 0, strpos($img_str, "\""));				// localhost... /img/temp/1_img0_20160420103424.png
			$img_ext = substr($img, strrpos($img, ".") + 1);				// png

			$img_o_name = substr($img, strrpos($img, "/") + 1);				// 1_img0_20160324165646.png
			$imgTemp = 'img/temp/'.$img_o_name;

			$img_name = $spot_idx.'_img'.$cnt.'_'.$date.'.'.$img_ext;
			$imgS3 = $s3pfxAdr.$img_name;

			$content = str_replace($img, $imgS3, $content);

			// Add new attached image to S3
			if (is_file($imgTemp)){
				$s3->putObject(array(
						'Bucket'		=> 'locawiki',
						'Key'			=> 'spot/'.$img_name,
						'SourceFile'	=> $imgTemp
				));
			}

			++$cnt;
		}

		$result = DB::update('update spot set name=?, content=?, lastdate=now(), category_idx=? where idx=?', array($name, $content, $cate, $spot_idx));

		if ($result == 1)
			return array('code' => 200, 'msg' => 'update success', 'content' => $content);
		else
			return array('code' => 500, 'msg' => 'failure in \'update(2)\'');
	}

	function updateLatLng($spot_idx, $latitude, $longitude){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::update('update spot set latitude=?, longitude=?, lastdate=now() where idx=?', array($latitude, $longitude, $spot_idx));

		if ($result == 1)
			return array('code' => 1, 'msg' => 'update success');
		else
			return array('code' => 1, 'msg' => 'failure: updateLatLng()');
	}

	function createReview($acc_idx, $spot_idx, $rating, $content, $img, $rating_kind){
		if ($_ = checkParam(array($acc_idx, $spot_idx, $rating, $content, /*$img, */$rating_kind)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$review_idx = DB::table('review')->insertGetId(
				array(
						'account_idx'		=> $acc_idx,
						'spot_idx'			=> $spot_idx,
						'writedate'			=> DB::raw('now()'),
						'lastdate'			=> DB::raw('now()'),
						'content'			=> $content
				)
		);

		if ($review_idx <= 0)
			return array('code' => 500, 'msg' => 'failure in \'createReview(1)\'');

		$average = 0;

		foreach ($rating as $i)
			$average += $i;

		$average /= count($rating);

		$rating_query = '';

		foreach ($rating_kind as $idx => $i)
			$rating_query .= ", rating_".$i->eng_name."=".$rating[$i->eng_name];

		$s3 = AWS::createClient('s3');
		$date = date("YmdHis", time());

		$img_query = '';

		foreach ($img as $idx => $i){
			if ($i == '')
				continue;

			$ext = $i->getClientOriginalExtension();
			$name = $review_idx."_img".$idx."_".$date.".".$ext;

			$img_query .= ", img".$idx."='".$name."'";

			$s3->putObject(array(
					'Bucket'		=> 'locawiki',
					'Key'			=> 'review/'.$name,
					'SourceFile'	=> $i
			));
		}

		$result = DB::update('update review set rating=?'.$rating_query.$img_query.' where idx=?', array($average, $review_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'create', 'avg' => $average);
		else
			return array('code' => 500, 'msg' => 'failure in \'createReview(2)\'');
	}

	function updateRating($spot_idx, $rating_kind){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$get_query = '';

		foreach ($rating_kind as $idx => $i){
			if ($idx != 0)
				$get_query .= ', ';

			$get_query .= 'format(avg(rating_'.$i->eng_name.'), 2) as fa_'.$i->eng_name;
		}

		$temp = DB::select('select '.$get_query.' from review where spot_idx=?', array($spot_idx));

		$rating = $temp[0];

// 		$rating = $temp[0]->faRating;

// 		if ($rating == NULL)
// 			return array('code' => 0, 'msg' => 'get faRating failure');

		$set_query = '';

		foreach ($rating_kind as $idx => $i){
			if ($idx != 0)
				$set_query .= ', ';

			$iter = 'fa_'.$i->eng_name;

			$set_query .= 'rating_'.$i->eng_name.'='.$rating->$iter;
		}

		$result = DB::update('update spot set '.$set_query.' where idx=?', array($spot_idx));

		if ($result == true)
			return array('code' => 1, 'msg' => 'update success');
		else
			return array('code' => 0, 'msg' => 'failure: updateRating()');
	}

	function getRatingList(){
		$result = DB::select('select name, eng_name, fa_name from rating_kind');

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result);
		else
			return array('code' => 500, 'msg' => 'failure in getRatingList');
	}

	function getReviewList($spot_idx, $rating_kind){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$rating_query = '';

		foreach ($rating_kind as $i)
			$rating_query .= ', r.rating_'.$i->eng_name.' as rating_'.$i->eng_name;

		$img_query = '';

		for ($i = 0; $i < 3; ++$i)
			$img_query .= ', r.img'.$i.' as img'.$i;

		$result = DB::select('select	r.idx as idx,
										m.idx as acc_idx,
										m.nickname as nickname,
										m.img as p_img,
										r.lastdate as lastdate'
										.$rating_query.',
										r.content as content'
										.$img_query.'
								from review as r, member as m where r.spot_idx=? and m.idx=r.account_idx order by r.writedate desc', array($spot_idx));

		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}

	function getMyReview($acc_idx, $spot_idx, $rating_kind){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$rating_query = '';

		foreach ($rating_kind as $i)
			$rating_query .= ', rating_'.$i->eng_name;

		$img_query = '';

		for ($i = 0; $i < 3; ++$i)
			$img_query .= ', img'.$i;

		$result = DB::select('select idx, lastdate'.$rating_query.', content'.$img_query.' from review where account_idx=? and spot_idx=?', array($acc_idx, $spot_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 240, 'msg' => 'no review');
	}

	function getMyReview2($review_idx, $rating_kind){		// for modify
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$rating_query = '';

		foreach ($rating_kind as $i)
			$rating_query .= ', rating_'.$i->eng_name;

		$img_query = '';

		for ($i = 0; $i < 3; ++$i)
			$img_query .= ', img'.$i;

		$result = DB::select('select idx'.$rating_query.', content'.$img_query.' from review where idx=?', array($review_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}

	function updateReview($review_idx, $rating, $content, $img, $prev_img, $rating_kind){
		if ($_ = checkParam(array($review_idx, $rating, $content, /*$img, $prev_img, */$rating_kind)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$average = 0;

		foreach ($rating as $i)
			$average += $i;

		$average /= count($rating);

		$rating_query = '';

		foreach ($rating_kind as $i)
			$rating_query .= ", rating_".$i->eng_name."=".$rating[$i->eng_name];

		$s3 = AWS::createClient('s3');

		foreach ($prev_img as $i){
			$temp = substr($i, strpos($i, 'review/'));

			if ($temp !== 'review/default.png'){
				$s3->deleteObject(array(
						'Bucket'	=> 'locawiki',
						'Key'		=> $temp
				));
			}
		}

		$img_query = '';
		$date = date("YmdHis", time());

		foreach ($img as $idx => $i){
			if ($i == '')
				continue;

			$ext = $i->getClientOriginalExtension();
			$name = $review_idx."_img".$idx."_".$date.".".$ext;

			$img_query .= ", img".$idx."='".$name."'";

			$s3->putObject(array(
					'Bucket'		=> 'locawiki',
					'Key'			=> 'review/'.$name,
					'SourceFile'	=> $i
			));
		}

		$result = DB::update('update review set lastdate=now(), rating=?'.$rating_query.', content=?'.$img_query.' where idx=?', array($average, $content, $review_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'avg' => $average);
		else
			return array('code' => 500, 'msg' => 'failure in \'updateReview\'');
	}

	function getReviewWriter($review_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select account_idx from review where idx=?', array($review_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]->account_idx);
		else
			return array('code' => 500, 'msg' => 'failure in \'getReviewWriter\'');
	}

	function getAlreadyReview($acc_idx, $spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select rating from review where account_idx=? and spot_idx=?', array($acc_idx, $spot_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'already review', 'data' => $result[0]);
		else
			return array('code' => 240, 'msg' => 'no review');
	}

	function createCluster($acc_idx, $latitude, $longitude/*, $thumb, $name, $content*/){
		if (!( inputErrorCheck($acc_idx, 'acc_idx')
			&& inputErrorCheck($latitude, 'latitude')
			&& inputErrorCheck($longitude, 'longitude') ))
			return;

		$clst_idx = DB::table('spot')->insertGetId(
				array(
						'account_idx'		=> $acc_idx,
						'latitude'			=> $latitude,
						'longitude'			=> $longitude,
						'img'				=> 'cluster.jpg',
						'rating'			=> 0.00,
						'name'				=> '',
						'content'			=> '',
						'writedate'			=> DB::raw('now()'),
						'lastdate'			=> DB::raw('now()'),
						'category_idx'		=> '',
						'hit_cnt'			=> 0,
						'wishlist_cnt'		=> 0,
						'rating_taste'		=> 0.00,
						'rating_price'		=> 0.00,
						'rating_service'	=> 0.00,
						'rating_access'		=> 0.00,
						'is_cluster'		=> 1,
						'cluster_idx'		=> NULL
				)
		);

		if ($clst_idx > 0)
			return array('code' => 200, 'msg' => 'create', 'data' => $clst_idx/*, 'content' => $content*/);
		else
			return array('code' => 500, 'msg' => 'failure in \'createCluster\'');
	}

	function setClustered($spot_idx, $clst_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::update('update spot set cluster_idx=? where idx=?', array($clst_idx, $spot_idx));

		if ($result == true)
			return array('code' => 200, 'msg' => 'clustered');
		else
			return array('code' => 500, 'msg' => 'failure in \'setClustered\'');
	}

	function getIsCluster($idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select is_cluster from spot where idx=?', array($idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]->is_cluster);
		else
			return array('code' => 500, 'msg' => 'failure in \'getIsCluster\'');
	}

	function getClusterData($clst_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select idx, img, name, content, lastdate, hit_cnt from spot where idx=?', array($clst_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}

	function getClusteredSpot($clst_idx, $purpose, $kind){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$query = '';

		if ($purpose != 0)
			$query .= ' and category_idx like \'%p'.$purpose.',%\'';

		if ($kind != 0)
			$query .= ' and category_idx like \'%k'.$kind.',%\'';

		$result = DB::select('select idx, img, name from spot where cluster_idx=?'.$query, array($clst_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}

	function makeLog($acc_idx, $spot_idx, $latitude, $longitude, /*$thumb, */$name, $content, $cate, $description){
		if (!( inputErrorCheck($acc_idx, 'acc_idx')
			&& inputErrorCheck($spot_idx, 'spot_idx')
			/*&& inputErrorCheck($latitude, 'latitude')*/
			/*&& inputErrorCheck($longitude, 'longitude')*/
			/*&& inputErrorCheck($thumb, 'thumb')*/
			/*&& inputErrorCheck($name, 'name')*/
			/*&& inputErrorCheck($content, 'content')*/
			/*&& inputErrorCheck($cate, 'cate')*/
			/*&& inputErrorCheck($description, 'description')*/ ))
			return;

		// Images will not be saved.
		$spot_log_idx = DB::table('spot_log')->insertGetId(
				array(
						'account_idx'	=> $acc_idx,
						'spot_idx'		=> $spot_idx,
						'latitude'		=> $latitude,
						'longitude'		=> $longitude,
						'img'			=> NULL,				//$thumb,
						'name'			=> $name,
						'content'		=> $content,
						'modifydate'	=> DB::raw('now()'),
						'category_idx'	=> $cate,
						'description'	=> $description
				)
		);

		// TODO: thumbnail/attached image impl.
		// TODO: lat/lng modify impl.
		//
		//

		if ($spot_log_idx > 0)
			return array('code' => 200, 'msg' => 'create');
		else
			return array('code' => 500, 'msg' => 'failure in \'makeLog\'');
	}

	function getLogList($spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select sl.idx, m.idx as account_idx, m.nickname, sl.modifydate, sl.description
								from spot_log as sl inner join member as m on sl.account_idx=m.idx
								where spot_idx=? order by modifydate asc', array($spot_idx));

		if (count($result) > 0)
			return array('code' => 1, 'msg' => 'success', 'data' => $result);
		else
			return array('code' => 0, 'msg' => 'failure: getLogList()');
	}

	function getLog($spot_idx, $ver){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select name, content, category_idx from spot_log where spot_idx=? order by idx asc', array($spot_idx));

		if (count($result) >= $ver)
			return array('code' => 1, 'msg' => 'success', 'data' => $result[$ver - 1]);
		else
			return array('code' => 0, 'msg' => 'failure: getLog()');
	}

	function getLogRecent($spot_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);

		$result = DB::select('select * from spot_log where spot_idx=? order by idx desc limit 1', array($spot_idx));

		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 500, 'msg' => 'failure in \'getLogRecent\'');
	}
}
