<?php
/**
 * abstract
 * 验证码
 * */
//require_once(PHPLIB_ROOT.'lib/Config.php');
//require_once(PHPLIB_ROOT.'lib/PicCode.php');
//require_once(PHPLIB_ROOT.'lib/Logger.php');

class VerifyCode{
	const CODE_KEY = '$#fd3!';
	public static $errCode = 0;
	public static $errMsg = '';

	/**
	 * 设置出错代码和出错信息
	 * @param int $code
	 * @param string $msg
	 */
	private static function setErrMsg($code, $msg='') {
		self::$errCode = $code;
		self::$errMsg = $msg;
	}

	/**
	 * 验证
	 * */
	public static function verifycodeNum($codeNum){
		if (!isset($_COOKIE['vcode'])) {
			self::setErrMsg(404, 'vcode is null');
			return false;
		}

		$codeNum = strtolower(trim($codeNum));
		$vcode = strtolower($_COOKIE['vcode']);
		$vcode = pack("H*", $vcode);
		$vcode = mcrypt_ecb(MCRYPT_3DES, self::CODE_KEY, $vcode, MCRYPT_DECRYPT);

		$vcode = strtolower(trim($vcode));

		$vcode = explode(',', $vcode);

		if(count($vcode) != 2 || $vcode[0] != $codeNum || time() - $vcode[1] > 5 * 60){
			self::setErrMsg(500, 'vcode is invalid');
			return false;
		}
		else {
			unset($_COOKIE['vcode']);
			return true;
		}
	}

	/**
	 * 生成
	 * */
	public static function generateCodeNum(){
		$vc = new PicCode();

		$vc->setWidth(100);
		$vc->setHeight(26);
		$vc->setFontSize(16);
		$vc->setTextNumber(4);
		$vc->setNoisePoint(80);
		$vc->setNoiseLine(4);
		$vc->setDistortion(TRUE);

		@ob_clean();
		@ob_start();
		$v = $vc->createImage();
		if(strlen($v) != 4) {
			return false;
		}

		$time = time();
		$vcode = mcrypt_ecb(MCRYPT_3DES, self::CODE_KEY, strtolower($v . ',' . $time), MCRYPT_ENCRYPT);
		$vcode = strtoupper(bin2hex($vcode));
		setcookie('vcode', $vcode);
		@ob_end_flush();
	}
}