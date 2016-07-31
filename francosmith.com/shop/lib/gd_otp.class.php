<?
class gd_otp {

	var $db;

	function _generate($type=0,$length=8) {

		switch ((int)$type) {
			case 1:	// ������
				$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','e','x','y','z');
				break;
			case 2:	// ����,����
				$chars = array('1','2','3','4','5','6','7','8','9','0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','e','x','y','z');
				break;
			case 0:	// ���ڸ�
			default:
				$chars = array('1','2','3','4','5','6','7','8','9','0');
				break;
		}

		shuffle($chars);

		$chars_size = sizeof($chars);

		$str = '';

		do {

			$_k = mt_rand(0, $chars_size);
			$str .= trim($chars[$_k]);

		} while (strlen($str) < $length);

		return $str;

	}

	function getToken($reset=false) {

		if ($reset == true) {
			session_regenerate_id();
		}

		$ssid = session_id();
		if (!$ssid) exit;	// ������ ���ų� id �� �޾ƿ��� ���ϸ� ����� �� ����.

		$tmp = array();
		$tmp[] = $ssid;
		$tmp[] = $_SERVER['REMOTE_ADDR'];
		$tmp[] = $_SERVER['HTTP_USER_AGENT'];

		$str = implode('|',$tmp);

		if (function_exists('hash')) {
			$hash = hash("sha256", $str, false);
		}
		elseif (function_exists('mhash')) {
			$hash = bin2hex( mhash(constant('MHASH_SHA256'), $str) );
		}
		else {
			$hash = md5($str).md5( crypt($str, $ssid) );
		}

		return $hash;

	}

	function getOTP() {
		$otp = $this->_generate(0,8);
		return $otp;
	}

}
?>