<?php
/**
 * Clib_IApi_Validate_Validate
 * @author extacy @ godosoft development team.
 */
class Clib_IApi_Validate_Validate
{
	const TYPE_INTEGER = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_NUMBER = 'number';
	const TYPE_STRING = 'string';
	const TYPE_BOOL = 'bool';
	const TYPE_ARRAY = 'array';
	const TYPE_OBJECT = 'object';
	const TYPE_URL = 'url';
	const TYPE_EMAIL = 'email';
	const TYPE_ENGLISH_NUMBER = 'english_number';
	const TYPE_ENGLISH = 'english';
	const TYPE_DATE = 'date';
	const TYPE_DATETIME = 'datetime';

	private $_conf_param = Array();
	private $_conf_charset = Array(
		0 => 'UTF-8',
		1 => 'EUC-KR',
	);

	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
		/*
		 if (is_array($conf_param)) {
		 $this->_conf_param = $conf_param;
		 }

		 if ($conf_param instanceof Clib_Model_Abstract) {
		 $this->_conf_param = $conf_param->toArray();
		 }

		 if ($conf_param instanceof Clib_Form_Abstract) {
		 $this->_conf_param =$conf_param->getElementsValidation();
		 }
		 */
		/* Array ���¸� �Ʒ��� ���� ���缭
		 $this->conf_param['goodsno'] = Array(
		 'goods_search' => Array(
		 'required' => 		self::REQUIRED_NO,
		 'type' => 			self::TYPE_STRING,
		 'length' => 		14,
		 'length_type' => 	self::TYPE_LENGTH_EQUAL,
		 )
		 );

		 $this->conf_param['item_sno'] = Array(
		 'goods_search' => Array(
		 'required' => 		self::REQUIRED_NO,
		 'type' => 			self::TYPE_STRING,
		 'length' => 		1,
		 'length_type' => 	self::TYPE_LENGTH_EQUAL_LARGE,
		 )
		 );

		 $this->conf_param['st_date'] = Array(
		 'goods_search' => Array(
		 'required' => 		self::REQUIRED_YES,
		 'type' =>		 	self::TYPE_INTEGER,
		 'length' => 		8,
		 'length_type' => 	self::TYPE_LENGTH_EQUAL,
		 )
		 );

		 $this->conf_param['ed_date'] = Array(
		 'goods_search' => Array(
		 'required' => 		self::REQUIRED_YES,
		 'type' => 			self::TYPE_INTEGER,
		 'length' => 		8,
		 'length_type' => 	self::TYPE_LENGTH_EQUAL,
		 )
		 );

		 $this->conf_param['page'] = Array(
		 'goods_search' => Array(
		 'required' => 		self::REQUIRED_YES,
		 'type' => 			self::TYPE_INTEGER,
		 )
		 );
		 */
	}

	/**
	 * form ��ü�� �ݵ�� �־�� ��.
	 * @param object $form, array $param
	 * @return bool $return_chk;
	 */
	public function paramCheckForm($form)
	{
		$this->_conf_param = $form->getElementsValidation();

		$result_validate = $this->paramCheck($this->_conf_param, $form->getValue());
		$tmp_arr = Array();

		if (is_array($result_validate) && ! empty($result_validate)) {
			foreach ($result_validate as $key_res => $val_res) {
				switch ($key_res) {
					case 'required' :
						$chk_str = '�ʼ�';
						break;
					case 'type' :
						$chk_str = '����';
						break;
					case 'length' :
						$chk_str = '����';
						break;
				}

				foreach ($val_res as $key => $val) {
					if ( ! $val) {
						$tmp_arr[$key][] = $chk_str;
					}
				}
			}
		}

		if (count($tmp_arr) > 0) {
			$tmp_arr2 = array();
			foreach ($tmp_arr as $key => $val) {
				$tmp_arr2[] = $key . '-' . implode(' ', $val);
			}

			$return_chk = false;

			if ($form instanceof Clib_Form_Abstract) {
				$form->setValidateMsg(sprintf('��û���� %s Ȯ�� �ʿ�', implode(',', $tmp_arr2)));
			}
		}
		else {
			$return_chk = true;
			if ($form instanceof Clib_Form_Abstract) {
				$form->setValidateMsg('����');
			}
		}

		return $return_chk;
	}

	/**
	 *
	 * @param array $param
	 * @return array $result
	 */
	public function paramCheck($conf_param, $param = array())
	{
		$this->_conf_param = $conf_param;

		if ( ! empty($param)) {
			$tmp_param = array_merge($param, $this->_conf_param);
		}
		else {
			$tmp_param = $this->_conf_param;
		}

		$chk_result = Array();

		foreach ($tmp_param as $param_key => $param_val) {

			if ($this->_conf_param[$param_key]) {

				// Ȯ���� ������ chk_param �� �����Ѵ�.
				$chk_param = $param[$param_key];

				// Ȯ���� ������ �����´�
				$chk_conf = $this->_conf_param[$param_key];

				$chk_result['required'][$param_key] = true;
				// �⺻�� true
				$chk_result['type'][$param_key] = true;
				// �⺻�� true
				$chk_result['length'][$param_key] = true;
				// �⺻�� true

				// �ʼ����� üũ
				if ($chk_conf['required'] === true) {
					if ( ! $chk_param && $chk_param !== '0' && $chk_param !== 0) {
						$chk_result['required'][$param_key] = false;
					}
				}

				if ($chk_param || $chk_param === 0 || $chk_param === '0') {
					// ������ üũ
					if ($chk_conf['type']) {
						$chk_result['type'][$param_key] = $this->checkParamType($chk_param, $chk_conf['type']);
					}
					else {
						$chk_result['type'][$param_key] = true;
					}

					// ���� �ڸ��� üũ
					if ($chk_conf['length']) {
						$chk_result['length'][$param_key] = $this->checkParamLength($chk_param, $chk_conf['length'], $chk_conf['length_type']);
					}
					else {
						$chk_result['length'][$param_key] = true;
					}
				}
				else {

					if ($chk_conf['type'] && $chk_conf['required'] === true) {
						$chk_result['type'][$param_key] = false;
					}

					if ($chk_conf['length'] && $chk_conf['required'] === true) {
						$chk_result['length'][$param_key] = false;
					}
				}
			}

		}

		return $chk_result;
	}

	/**
	 *
	 * @param $param, $length, $type
	 * @return bool
	 */
	public function checkParamLength($param, $length, $type)
	{
		$ret_check_param_length = true;
		$chk_length = $this->getLength($param);

		$tmp_length = explode(',', $length);

		if (count($tmp_length) < 2) {
			$tmp_length[1] = $tmp_length[0];
		}

		$tmp_length[0] = trim($tmp_length[0]);
		$tmp_length[1] = trim($tmp_length[1]);

		if ($tmp_length[0] <= $chk_length && $tmp_length[1] >= $chk_length) {

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 *
	 * @param $param, $type
	 * @return bool
	 */
	public function checkParamType($param, $type)
	{
		$ret_param_type = true;

		switch ($type) {
			case self::TYPE_INTEGER :
				// intger ��
				if ( ! $this->checkInteger($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_FLOAT :
				// float ��
				if ( ! $this->checkFloat($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_NUMBER :
				// integer �̵� float ���̵� ������ ��츸 Ȯ��
				if ( ! $this->checkNumber($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_STRING :
				// string �� ����ǥ�� �ѷ����� ���ڳ� true, false�� string ������ �з�
				if ( ! $this->checkString($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_BOOL :
				// bool �� Ȯ��
				if ( ! $this->checkBool($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_ARRAY :
				// Array Ȯ��
				if ( ! $this->checkArray($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_OBJECT :
				// object Ȯ��
				if ( ! $this->checkObject($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_URL :
				// url Ȯ��
				if ( ! $this->checkUrl($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_EMAIL :
				// email Ȯ��
				if ( ! $this->checkEmail($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_ENGLISH_NUMBER :
				// english_number Ȯ��
				if ( ! $this->checkEnglishNumber($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_ENGLISH :
				// english Ȯ��

				if ( ! $this->checkEnglish($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_DATE :
				// date Ȯ��
				if ( ! $this->checkDate($param)) {
					$ret_param_type = false;
				}
				break;

			case self::TYPE_DATETIME :
				// datetime Ȯ��
				if ( ! $this->checkDatetime($param)) {
					$ret_param_type = false;
				}
				break;
		}

		return $ret_param_type;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkInteger($param)
	{
		$ret_check = true;

		if ( ! is_numeric($param)) {
			$ret_check = false;
		}
		else {
			if ( - 2147483647 > $param || 2147483647 < $param) {
				$ret_check = false;
			}
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkFloat($param)
	{
		$ret_check = true;

		if ( ! is_numeric($param)) {
			$ret_check = false;
		}
		else {
			if ( - 9223372036854775808 > $param || 9223372036854775808 < $param) {
				$ret_check = false;
			}
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkNumber($param)
	{
		$ret_check = true;

		if ( ! is_numeric($param) || ! preg_match('/[0-9]/', $param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkString($param)
	{
		$ret_check = true;

		if ( ! is_string($param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkBool($param)
	{
		$ret_check = true;

		if ( ! is_bool($param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkArray($param)
	{
		$ret_check = true;

		if ( ! is_array($param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkObject($param)
	{
		$ret_check = true;

		if ( ! is_object($param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkEmail($param)
	{
		$ret_check = true;

		$email_exp = '[-_\\w]+(\\.\\w+)*@[-_\\w]+\\.\\w+(\\.\\w+)*';

		if ( ! preg_match('/' . $email_exp . '/', $param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkUrl($param)
	{
		$ret_check = true;

		$url_exp = '[-a-z0-9]+(\\.[-a-z0-9]+)+(:[0-9]{1,4})?(\\/\\~?[-_a-z0-9A-Z]+\\~?(\\.[a-z0-9A-Z]+)?)*(\\?[a-z0-9A-Z]+\\=[^\\=^\\<^\\>]*(\\&[a-z0-9A-Z]+\\=[^\\=^\\<^\\>]*)*)?';

		if ( ! preg_match('/' . $url_exp . '/', $param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkEnglishNumber($param)
	{
		$ret_check = true;

		if ( ! ctype_alnum($param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkEnglish($param)
	{
		$ret_check = true;

		if ( ! is_string($param) || ! preg_match('/[a-zA-z]/', $param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkDate($param)
	{
		$ret_check = true;

		if ( ! preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return bool
	 */
	public function checkDatetime($param)
	{
		$ret_check = true;

		if ( ! preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-4]):[0-5][0-9]:[0-5][0-9]$/', $param)) {
			$ret_check = false;
		}

		return $ret_check;
	}

	/**
	 *
	 * @param $param
	 * @return integer
	 */
	public function getBytes($param)
	{
		$ret_length = strlen($param);
		return $ret_length;
	}

	/**
	 *
	 * @param $param
	 * @return integer
	 */
	public function getLength($param)
	{
		$ret_length = 0;

		$han_length = 0;
		$eng_length = 0;

		if ( ! is_array($param) && ! is_object($param)) {
			# ���ڿ��� ���̸� ��´�
			$param_len = strlen($param);

			for ($i = 0; $i < $param_len; $i++) {
				$chk_ord = ord($param[$i]);

				if ($chk_ord > 128) {
					$han_length++;
				}
				else if ($chk_ord < 128) {
					$eng_length++;
				}
			}
		}

		$division_var = 0;

		if ($this->getEncoding($param, $this->conf_charset) == 'UTF-8') {
			$division_var = 2;
		}
		else {
			$division_var = 2;
		}

		if ($han_length > 0) {
			$han_length = $han_length / $division_var;
		}

		$ret_length = $eng_length + $han_length;

		return $ret_length;
	}

	/**
	 *
	 * @param $param
	 * @return integer
	 */
	public function getEncoding($param, $arr_chargset)
	{
		return mb_detect_encoding($param, $arr_chargset);
		/*
		 $ret_encoding = '';
		 foreach ($arr_chargset as $out_charset) {
		 $in_charset = $out_charset;
		 $chk_param = iconv($in_charset, $out_charset, $param);
		 if (md5($chk_param) == md5($param)) {
		 $ret_encoding = $out_charset;
		 break;
		 }
		 }
		 return $ret_encoding;
		 */
	}

}
