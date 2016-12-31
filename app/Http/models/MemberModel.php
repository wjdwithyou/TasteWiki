<?php
namespace App\Http\models;
use DB;
use Hash;
use AWS;

include_once dirname(__FILE__)."/Common.php";

class MemberModel{
	function create($kind, $ad_chk, $id, $pw, $nickname, $email, $name, $sex, $age, $img){
		if ($_ = checkParam(array($kind, $ad_chk, $id, $pw, $nickname/*, $email, $name, $sex, $age, $img*/)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$encrypt = Hash::make($pw);
		
		if (!(Hash::check($pw, $encrypt))){
			if (Hash::needsRehash($encrypt))
				$encrypt = Hash::make($pw);
		}
		
		$acc_idx = DB::table('member')->insertGetId(
				array(
						'kind'			=> $kind,
						'ad_chk'		=> $ad_chk,
						'id'			=> $id,
						'pw'			=> $encrypt,
						'nickname'		=> $nickname,
						'email'			=> $email,
						'email_chk'		=> 0,
						'name'			=> $name,
						'sex'			=> $sex,
						'age'			=> $age,
						'joindate'		=> DB::raw('now()'),
						'wish_rating'	=> 0,
						'wish_num'		=> 0
				)
		);
		
		if ($img){
			$s3 = AWS::createClient('s3');
			
			$date = date("YmdHis", time());
			$ext = $img->getClientOriginalExtension();
			
			$img_name = $acc_idx."_".$date.".".$ext;			
			
			$s3->putObject(array(
					'Bucket'		=> 'locawiki',
					'Key'			=> 'profile/'.$img_name,
					'SourceFile'	=> $img
			));
		}
		else
			$img_name = 'default.png';
		
		$result = DB::update('update member set img=? where idx=?', array($img_name, $acc_idx));
		
		if ($acc_idx > 0)
			return array('code' => 200, 'msg' => 'create'/*, 'data' => $acc_idx*/, 'data' => $ad_chk);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function login($kind, $id, $pw){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$member = DB::select('select idx, pw, nickname, img from member where kind=? and id=?', array($kind, $id));
		
		if (count($member) > 0){
			if (Hash::check($pw, $member[0]->pw)){
				DB::update('update member set lastdate=now() where idx=?', array($member[0]->idx));
				
				return array(
						'code' => 1,
						'msg' => 'success',
						'data' => (object)array(
											'idx' => $member[0]->idx,
											'nickname' => $member[0]->nickname,
											'img' => $member[0]->img
						)
				);
			}
			else
				return array('code' => 0, 'msg' => 'failure');
		}
		else
			return array('code' => 0, 'msg' => 'failure');
	}
	
	function checkAvailableId($id){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select * from member where id=?', array($id));
		
		if ($result == NULL)
			return array('code' => 1, 'msg' => 'available');
		else
			return array('code' => 0, 'msg' => 'exist id');
	}
	
	function checkAvailableNickname($nickname){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select * from member where nickname=?', array($nickname));
		
		if ($result == NULL)
			return array('code' => 200, 'msg' => 'available');
		else
			return array('code' => 250, 'msg' => 'exist nickname');
	}
	
	function update($acc_idx, $ad_chk, $pw, $nickname, $email, $name, $sex, $age, $img, $prev_img){
		if ($_ = checkParam(array($acc_idx, $ad_chk, $pw, $nickname/*, $email, $name, $sex, $age, $img, $prev_img*/)))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$query_pw = '';
		
		if ($pw != ''){
			$encrypt = Hash::make($pw);
			
			if (!(Hash::check($pw, $encrypt))){
				if (Hash::needsRehash($encrypt))
					$encrypt = Hash::make($pw);
			}
			
			$query_pw .= ' pw=\''.$encrypt.'\',';
		}
		
		$query_img = '';
		
		if ($img){
			$s3 = AWS::createClient('s3');
			
			/*
			// Access Denied..
			$prev = $s3->getIterator('ListObjects', array(
					'Bucket'	=> 'locawiki',
					'Prefix'	=> 'profile/'.$acc_idx
			));
			
			foreach ($prev as $i){
				$s3->deleteObject(array(
						'Bucket'	=> 'locawiki',
						'Key'		=> $i['Key']
				));
			}
			*/
			
			if ($prev_img !== 'default.png'){
				$s3->deleteObject(array(
						'Bucket'	=> 'locawiki',
						'Key'		=> 'profile/'.$prev_img
				));
			}
			
			//$date = date("YmdHis", strtotime(date("YmdHis", time()).'+9 hour'));
			$date = date("YmdHis", time());
			
			$ext = $img->getClientOriginalExtension();
			$img_name = $acc_idx.'_'.$date.'.'.$ext;
			
			$s3->putObject(array(
					'Bucket'		=> 'locawiki',
					'Key'			=> 'profile/'.$img_name,
					'SourceFile'	=> $img
			));
			
			$query_img .= ', img=\''.$img_name.'\'';
		}
		else
			$img_name = $prev_img;
		
		$result = DB::update('update member set ad_chk=?,'.$query_pw.' nickname=?, email=?, name=?, sex=?, age=?'.$query_img.' where idx=?', array($ad_chk, $nickname, $email, $name, $sex, $age, $acc_idx));
		
		if ($result == 1)
			return array('code' => 200, 'msg' => 'success', 'n_img' => $img_name);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function getAccountInfo($acc_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select id, nickname, email, email_chk, name, img, sex, age, ad_chk from member where idx=?', array($acc_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function setTempCode($acc_idx, $code){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::update('update member set temp_code=? where idx=?', array($code, $acc_idx));
		
		if ($result == 1)
			return array('code' => 200, 'msg' => 'success');
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
	
	function getTempCode($acc_idx){
		if ($_ = checkParam(func_get_args()))
			return array('code' => 400, 'msg' => 'invalid input at ['.--$_.'] in '.__FUNCTION__);
		
		$result = DB::select('select temp_code from member where idx=?', array($acc_idx));
		
		if (count($result) > 0)
			return array('code' => 200, 'msg' => 'success', 'data' => $result[0]->temp_code);
		else
			return array('code' => 500, 'msg' => 'failure in '.__FUNCTION__);
	}
}
