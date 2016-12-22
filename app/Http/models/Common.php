<?php
//use AWS;

/**
 * 161006
 * Deprecated by J.Style.
 * Please use checkParam(array) instead.
 */
function inputErrorCheck($param, $columnName){
	if (isset($param) && ($param != ''))
		return true;
	else{
		echo json_encode(array('code' => 400, 'msg' => 'Invalid input at '.$columnName));
		return false;
	}
}

function checkParam($arr){
	foreach ($arr as $idx => $i){
		if (isset($i) && ($i != ''))
			continue;
		else
			return $idx + 1;
	}

	return 0;
}

function insertImage($type/*impl...*/){
	// inputErrorCheck
	
	//$s3 = AWS::createClient('s3');
	
	// impl.
}