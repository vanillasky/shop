<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */

/**
 * GODO_helper_string
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_string extends GODO_helper {


	/**
	 * ����ǥ ó���� ���ڿ��� Ǳ�ϴ�
	 * @param mixed $var
	 * @return void
	 */
	public function stripslashes(&$var) {

		if (is_array($var)) {
			foreach($var as $k=>$v) {
				if(is_array($var[$k])) $this->stripslashes($var[$k]);
				else $var[$k]=stripslashes($var[$k]);
			}
		}
		else {
			$var = stripslashes($var);
		}

		return $var;

	}

	/**
	 * �����ڵ�� ���ڵ��� �ѱ��� ������� ���� (&#46736; -> ���ˤ� �����°� �ƴϰ� ��ģ����. ������;;)
	 * @param string $str
	 * @return string
	 */
	public function toHan($str) {

		$_fr = array(
			'~&#([0-9]+);~e',
			'~&#([0-9]+)~e',
		);
		$_to = array(
			'sprintf("&#x%X;",\\1)',
			'sprintf("&#x%X;",\\1)',
		);
		$str = preg_replace( $_fr, $_to, $str, -1, $_cnt );

		if ($_cnt > 0) {

			$charset = strtoupper( Core::config('global', 'charset') );

			if ($charset == 'EUC-KR')
				$charset = 'CP949';	// Ȯ�� �ѱ۶����� cp949

			if ($charset != 'UTF-8')
				$str = iconv($charset,'UTF-8',$str);

			$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');

			if ($charset != 'UTF-8')
				$str = iconv('UTF-8', $charset, $str);

		}

		return $str;

	}

	/**
	 * �빮�ڸ� �ҹ��ڷ� ����
	 * @param string $str
	 * @return
	 */
	public function strtolower($str) {

		$A = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$a = 'abcdefghijklmnopqrstuvwxyz';

		return strtr($str, $A, $a);

	}


	/**
	 * �ѱ� �ڼ� �и� (�Ʒ� ��ũ���� �и� ���� ����)
	 * @see http://dream.ahboom.net/entry/�ѱ�-�����ڵ�-�ڼ�-�и�-���
	 * @param string $str
	 * @return string
	 */
	public function splitKorean($str) {

		$_chars = array(
			'cho'  => array('��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��'),
			'jung' => array('��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��'),
			'jong' => array('', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��', '��')
		);

		$charset = strtoupper( Core::config('global', 'charset') );

		if ($charset == 'EUC-KR')
			$charset = 'CP949';

		// 16��Ʈ �����ڵ�� ��ȯ
		$str = iconv($charset, "UTF-16LE", $str);

		$words = array();

		for ($i=0,$m=strlen($str);$i<$m;$i+=2) {	// 2 byte �̹Ƿ�.

			$a = ord($str[$i+1]);
			$b = ord($str[$i]);

			$unicode = sprintf('0x%02X%02X', $a, $b);

			if ($unicode >= 0xAC00 && $unicode <= 0xD7A3) {	// '����' ���� '���Ӥ�' ����)

				// �и�
				$temp = $unicode - 0xAC00;

				$jong = $temp % 28;
				$jung = (($temp - $jong) / 28) % 21;
				$cho  = ((($temp - $jong) / 28) - $jung) / 21;

				$words[] = $_chars['cho'][$cho].$_chars['jung'][$jung].$_chars['jong'][$jong];

			}
			else {
				$words[] = iconv("UTF-16LE", $charset, $str[$i].($str[$i + 1] ? $str[$i + 1] : ''));
			}

		}

		unset($_chars, $a, $b, $unicode);

		return implode('',$words);

	}

}
?>
