<?php
namespace App\Http\models;
use DB;
use AWS;

include_once dirname(__FILE__)."/Common.php";

class CommunityModel{
	function create($acc_idx, $cate, $title, $content){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$comm_idx = DB::table('community')->insertGetId(
				array(
						'account_idx'	=> $acc_idx,
						'title'			=> $title,
						'content'		=> '',
						'writedate'		=> DB::raw('now()'),
						'lastdate'		=> DB::raw('now()'),
						'hit_cnt'		=> 0,
						'reply_cnt'		=> 0,
						'category_idx'	=> $cate
				)
		);
		
		if ($comm_idx <= 0)
			return array('code' => 500, 'msg' => 'failure in \'create(1)\'');
		
		$s3pfxAdr = 'https://s3-ap-northeast-2.amazonaws.com/locawiki/community/';
		$s3 = AWS::createClient('s3');
		
		$img_str = $content;
		
		while (strpos($img_str, "<img") !== false){
			$img_str = substr($img_str, strpos($img_str, "<img"));
				
			$img_str = substr($img_str, strpos($img_str, "src=\"") + 5);
			$img = substr($img_str, 0, strpos($img_str, "\""));
			//$img_ext = substr($img, strrpos($img, ".") + 1);
				
			$img_o_name = substr($img, strrpos($img, "/") + 1);
			$imgTemp = 'img/temp/'.$img_o_name;
				
			$imgPostName = substr($img, strpos($img, "_"));
			$img_name = $comm_idx.$imgPostName;
			$imgS3 = $s3pfxAdr.$img_name;
			
			$content = str_replace($img, $imgS3, $content);
			
			if (is_file($imgTemp)){
				$s3->putObject(array(
						'Bucket'		=> 'locawiki',
						'Key'			=> 'community/'.$img_name,
						'SourceFile'	=> $imgTemp
				));
			}
		}
		
		$result = DB::update('update community set content=? where idx=?', array($content, $comm_idx));
		
		if ($result == true)
			return array('code' => 200, 'msg' => 'create', 'data' => $comm_idx);
		else
			return array('code' => 500, 'msg' => 'failure in \'create(2)\'');
	}
	
	function delete(/*impl.*/){
		// impl.
	}
	
	function getCommunityList($cate, $searchType, $searchText, $page_num/*, $pageType*/){
		if ($_ = checkParam(array($cate, $searchType/*, $searchText*/, $page_num/*, $pageType*/)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$query = '';
		
		if ($cate != 0)
			$query .= 'cm.category_idx='.$cate.' and ';
		
		if ($searchText == '')
			$result = DB::select("select cm.idx, cc.idx as cate_idx, cc.name as cate_name, cm.title, cm.reply_cnt, mb.nickname, cm.writedate, cm.hit_cnt from community as cm, member as mb, category_community as cc where ".$query."cm.account_idx=mb.idx and cm.category_idx=cc.idx order by cm.idx desc");
		else{
			switch ($searchType){
				case 1:		// title
					$result = DB::select("select cm.idx, cc.idx as cate_idx, cc.name as cate_name, cm.title, cm.reply_cnt, mb.nickname, cm.writedate, cm.hit_cnt from community as cm, member as mb, category_community as cc where ".$query."cm.account_idx=mb.idx and cm.category_idx=cc.idx and cm.title like '%".$searchText."%' order by cm.idx desc");
					break;
					
				case 2:		// title + content
					$pre = DB::select("select cm.idx, cc.idx as cate_idx, cc.name as cate_name, cm.title, cm.content, cm.reply_cnt, mb.nickname, cm.writedate, cm.hit_cnt from community as cm, member as mb, category_community as cc where ".$query."cm.account_idx=mb.idx and cm.category_idx=cc.idx and (cm.title like '%".$searchText."%' or cm.content like '%".$searchText."%') order by cm.idx desc");
					
					$result = array();
					
					foreach($pre as $i){
						if (strpos(''.$i->title, $searchText) !== false || strpos(''.$i->content, $searchText) !== false)
							array_push($result, $i);
					}
					
					break;
					
				case 3:		// writer
					$result = DB::select("select cm.idx, cc.idx as cate_idx, cc.name as cate_name, cm.title, cm.reply_cnt, mb.nickname, cm.writedate, cm.hit_cnt from community as cm, member as mb, category_community as cc where ".$query."cm.account_idx=mb.idx and cm.category_idx=cc.idx and mb.nickname like '%".$searchText."%' order by cm.idx desc");
					break;
					
				default:
					return array('code' => 500, 'msg' => 'unexpected searchType');
					break;
			}
		}
		
		if (count($result) == 0)
			return array('code' => 240, 'msg' => 'no matched data');
		
		
		
		// Paging
		$page_max = ceil(count($result) / 20);
		
		if ($page_num < 1)
			$page_num = 1;
		
		if ($page_num > $page_max)
			$page_num = $page_max;
		
		$page_start = ($page_num - 1) * 20;
		
		$result = array_slice($result, $page_start, 20);
		
		return array('code' => 200, 'msg' => 'success', 'data' => $result, 'page_num' => $page_num, 'page_max' => $page_max);
	}
	
	function getCommData($comm_idx){	// detail
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select cm.idx as idx, mb.idx as account_idx, mb.nickname as nickname, cm.title as title, cm.content as content, cm.lastdate as lastdate, cm.hit_cnt as hit_cnt from community as cm, member as mb where cm.idx=? and cm.account_idx=mb.idx', array($comm_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 500, 'msg' => 'failure');
	}
	
	function getCommData2($comm_idx, $acc_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select idx, title, content, category_idx from community where idx=?', array($comm_idx));
		
		if (count($result) <= 0)
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
		
		$s3pfxAdr = 'https://s3-ap-northeast-2.amazonaws.com/locawiki/community/';
		$s3 = AWS::createClient('s3');
		
		$img_str = $result[0]->content;
		$img_num = 0;
		$prev_img = array();
		
		while (strpos($img_str, "<img") !== false){
			$img_str = substr($img_str, strpos($img_str, "<img"));
			
			$img_str = substr($img_str, strpos($img_str, "src=\"") + 5);
			$img = substr($img_str, 0, strpos($img_str, "\""));
			
			$img_o_name = substr($img, strrpos($img, "/") + 1);
			//$imgTemp = 'img/temp/'.$img_o_name;
			
			array_push($prev_img, $img_o_name);
			
			$imgPostName = substr($img, strpos($img, "_"));
			$img_name = $acc_idx.$imgPostName;
			$imgTemp = 'img/temp/'.$img_name;
			
			++$img_num;
			
			if ($s3->doesObjectExist('locawiki', 'community/'.$img_o_name)){
				$s3->getObject(array(
						'Bucket'	=> 'locawiki',
						'Key'		=> 'community/'.$img_o_name,
						'SaveAs'	=> $imgTemp
				));
				
				$result[0]->content = str_replace($img, '/'.$imgTemp, $result[0]->content);
			}
		}
		
		return array('code' => 200, 'msg' => 'success', 'data' => $result[0], 'num' => $img_num, 'prev_img' => $prev_img);
	}
	
	function getWriter($comm_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select account_idx from community where idx=?', array($comm_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]->account_idx);
		else
			return array('code' => 500, 'msg' => 'failure in \'getWriter\'');
	}
	
	function checkExistComm($comm_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select count(*) as cnt from community where idx=?', array($comm_idx));
		
		if ($result[0]->cnt > 0)
			return array('code' => 1, 'msg' => 'exist comm');
		else
			return array('code' => 0, 'msg' => 'not exist');
	}
	
	function increaseHit($comm_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::update('update community set hit_cnt=hit_cnt+1 where idx=?', array($comm_idx));
		
		if (count($result) > 0)
			return array('code' => 1, 'msg' => 'success');
		else
			return array('code' => 0, 'msg' => 'failure');
	}
	
	function increaseReply($comm_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::update('update community set reply_cnt=reply_cnt+1 where idx=?', array($comm_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success');
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function update($acc_idx, $comm_idx, $cate, $title, $content, $prev_img){
		if ($_ = checkParam(array($acc_idx, $comm_idx, $cate, $title, $content/*, $prev_img*/)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$s3pfxAdr = 'https://s3-ap-northeast-2.amazonaws.com/locawiki/community/';
		$s3 = AWS::createClient('s3');
		
		$date = date("YmdHis", time());
		
		// attached image
		
		// Remove previous attached image from S3
		/*
		// Access Denied..
		$prevList = $s3->getIterator('ListObjects', array(
				'Bucket'	=> 'locawiki',
				'Prefix'	=> 'community/'.$comm_idx.'_img'
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
					'Key'		=> 'community/'.$prev_arr[$i]
			));
		}
		
		$img_str = $content;
		$cnt = 0;
		
		while (strpos($img_str, "<img") !== false){
			$img_str = substr($img_str, strpos($img_str, "<img"));
			
			$img_str = substr($img_str, strpos($img_str, "src=\"") + 5);
			$img = substr($img_str, 0, strpos($img_str, "\""));
			$img_ext = substr($img, strrpos($img, ".") + 1);
			
			$img_o_name = substr($img, strrpos($img, "/") + 1);
			$imgTemp = 'img/temp/'.$img_o_name;
			
			$img_name = $comm_idx.'_img'.$cnt.'_'.$date.'.'.$img_ext;
			$imgS3 = $s3pfxAdr.$img_name;
			
			$content = str_replace($img, $imgS3, $content);
			
			// Add new attached image to S3
			if (is_file($imgTemp)){
				$s3->putObject(array(
						'Bucket'		=> 'locawiki',
						'Key'			=> 'community/'.$img_name,
						'SourceFile'	=> $imgTemp
				));
			}
			
			++$cnt;
		}
		
		$result = DB::update('update community set title=?, content=?, lastdate=now(), category_idx=? where idx=? and account_idx=?', array($title, $content, $cate, $comm_idx, $acc_idx));
		
		if ($result == 1)
			return array('code' => 200, 'msg' => 'update success');
		else
			return array('code' => 500, 'msg' => 'update failure');
	}
	
	function createReply($acc_idx, $comm_idx, $content){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$reply_idx = DB::table('community_reply')->insertGetId(
				array(
						'account_idx'	=> $acc_idx,
						'community_idx'	=> $comm_idx,
						'writedate'		=> DB::raw('now()'),
						'lastdate'		=> DB::raw('now()'),
						'content'		=> $content,
						'rereply_idx'	=> NULL
				)
		);
		
		if ($reply_idx > 0)
			return array('code' => 200, 'msg' => 'create');
		else
			return array('code' => 500, 'msg' => 'failure');
	}
	
	function getReplyList($comm_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select	cr.idx, m.idx as account_idx, m.nickname, m.img, cr.content, cr.lastdate
								from community_reply as cr, member as m
								where cr.community_idx=? and cr.account_idx=m.idx order by cr.writedate desc', array($comm_idx));
		
		return array('code' => 1, 'msg' => 'success', 'data' => $result);
	}
}
