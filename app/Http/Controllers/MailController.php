<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\models\MemberModel;
use Request;
use Mail;

class MailController extends Controller {
    private static $code_length = 32;
    private static $mbModel;
    
    function __construct() {
        $this::$mbModel = new MemberModel();
    }
    
    public function verifyIndex() {
		if (Common::loginStateCheck() != 1) {
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
        
		if (!isset($_SERVER['HTTP_REFERER'])) {
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
        
		$email = Request::input('mail');
        
		$page = "mail_verify";
		return view($page, array('page' => $page, 'mail' => $email));
	}
    
    public function sendVerify() {
		if (Common::loginStateCheck() != 1) {
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
        
		if (!isset($_SERVER['HTTP_REFERER'])) {
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
        
		header('Content-Type: application/json');
        
		$acc_idx = $_SESSION['idx'];
		$nickname = $_SESSION['nickname'];
        
		$email = Request::input('mail');
        
		$pattern = "/^[0-9a-z\.\-_]+@([0-9a-z\-]+\.)+[a-z]{2,6}$/";
        
		if (!preg_match($pattern, $email)) {
			echo json_encode(array('code' => 400, 'msg' => 'Invalid Email Address'));
			return;
		}
        
		$code = '';
        
		for ($i = 0; $i < $this::$code_length; ++$i) {
			$rand = mt_rand(48, 90);
            
			if ($rand > 57 && $rand < 65) {
				--$i;
            } else {
				$code .= chr($rand);
            }
		}
        
		$temp_code = $email.$code;
        
		$result_setTempCode = $this::$mbModel->setTempCode($acc_idx, $temp_code);
        
		if ($result_setTempCode['code'] != 200) {
			echo json_encode($result_setTempCode);
			return;
		}
        
		$data = (object)array('code' => $code, 'nickname' => $nickname, 'email' => $email);
        
		Mail::send(
				'mail/verify',
				compact('data'),
				function ($message) use ($data) {
					$message->from(config('mail.from.address'), config('mail.from.name'));
					$message->to($data->email);
					$message->subject('['.config('mail.from.name').'] '.$data->nickname.'님의 이메일 인증 확인 메일입니다.');
				}
		);
        
		header('Content-Type: application/json');
		echo json_encode(array('code' => 200, 'msg' => 'success'));
	}
    
    public function checkVerify() {
        /*
		if (!isset($_SERVER['HTTP_REFERER'])) {
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
        */
        
		header('Content-Type: application/json');
        
        $email = Request::input('mail');
		$code = Request::input('code');
        
        // New
        
        // exception?
        // email, code
        
        $result_getIdxByEmail = $this::$mbModel->getIdxByEmail($email);
        
        if ($result_getIdxByEmail['code'] != 200) {
            $page = 'errors/400';
            return view($page, array('page' => $page));
        }
        
        $acc_idx = $result_getIdxByEmail['data'];
        
        $result_getTempCode = $this::$mbModel->getTempCode($acc_idx);
        
        if ($result_getTempCode['code'] != 200) {
            echo json_encode($result_getTempCode);
            return;
        }
        
        if ($result_getTempCode['data'] != $email.$code) {    // code not matched
            $page = 'errors/400';
    		return view($page, array('page' => $page));
        }
        
        $result_setVerifiedMail = $this::$mbModel->setVerifiedMail($acc_idx);
        
        // begin additional process
        $result_getVerifiedIdxByEmail = $this::$mbModel->getVerifiedIdxByEmail($acc_idx, $email);
        
        if ($result_getVerifiedIdxByEmail['code'] == 250) {
            $this::$mbModel->addVerifiedMail($acc_idx, $email);
        }
        // end additional process
        
        //echo json_encode($result_setVerifiedMail);
        $page = 'test';
        return view($page, array('page' => $page));
	}
    
    public function abandonVerify() {
		if (Common::loginStateCheck() != 1) {
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
        
		if (!isset($_SERVER['HTTP_REFERER'])) {
			header("Location: http://".$_SERVER['HTTP_HOST']);
			die();
		}
        
		$acc_idx = $_SESSION['idx'];
        
		$result_unsetVerifiedMail = $this::$mbModel->unsetVerifiedMail($acc_idx);
        
		header('Content-Type: application/json');
		echo json_encode($result_unsetVerifiedMail);
	}
}
