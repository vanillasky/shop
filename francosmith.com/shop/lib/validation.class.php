<?php
/**
 * �Ķ���Ͱ��� Class
 *
 * @author gise, lee <birdmarine@godo.co.kr>
 * @version 1.0 2009/08/04 10:00:00
 * @copyright Copyright (c), Godosoft
 */

class Validation
{
	private static $_noAllowScript = 's\s*c\s*r\s*i\s*p\s*t|v\s*b\s*s\s*c\s*r\s*i\s*pt';	//���Ұ� ��ũ��Ʈ
	private static $_noAllowAttributeName = 'seekSegmentTime|FSCommand|on[a-zA-Z]{4,}|action';	//���Ұ� �Ӽ�
	private static $_noAllowAttributeValue = 'j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*pt:|v\s*b\s*s\s*c\s*r\s*i\s*p\s*t:|r\s*e\s*d\s*i\s*r\s*e\s*c\s*t|e\s*x\s*p\s*r\s*e\s*s\s*s\s*i\s*o\s*n|
	d\s*o\s*c\s*u\s*m\s*e\s*n\s*t\.c\s*o\s*o\s*k\s*i\s*e';	//���Ұ� �Ӽ���
	private static $_noAllowTagName = 'meta';	//���Ұ� �±�
	public static $_pluginLoadTag = array(	//�÷����� ����
		'iframe' => 'src',
		'embed' => 'src',
	);

	public static $_allowPluginTag = 'iframe|embed';	//��밡�� ����Ʈ �÷����� �±�
	public static $_allowPluginDomain = array( //��밡�� ����Ʈ �÷����� ������
		'youtube.com',
		'facebook.com',
		'instagram.com',
		'naver.com',
		'daum.net',
		'tistory.com',
		'flicker.com',
	);

	private static $_addSingleSlashes;	//single quotStyle �� ���� �߰�slashes
	private static $_addDoubleSlashes;	//double quotStyle �� ���� �߰�slashes

	const FILTER_TAG_COMMENT = '<!--Not Allowed Tag Filtered-->';	//���Ұ� ��ũ��Ʈ �ּ�
	const FILTER_DOMAIN_COMMENT = '[�ȳ�]�Խ����� ������ �̿��� ���Ͽ� �������� �±��� ����� �����Ͽ����ϴ�.<!--Not Allowed domain Filtered-->';	//���Ұ� ������ �ּ�

	const DEFAULT_KEY = '__DEFAULT__';
	/**
	 * ��ũ��Ʈ ����
	 * @param String $str
	 * @return $string
	*/
	private static function _convertScript($str)
	{
		$doublePluginPattern = "/(<((".self::$_noAllowScript.").*?)\>)(.*?)(<\/((".self::$_noAllowScript.").*?)>)/ixs"; //�ݱ��±װ� ������ �� ����üũ
		$str = preg_replace_callback($doublePluginPattern , array(self,'__callbackConvertScript') , $str);

		$singlePluginPattern = "/(<(".self::$_noAllowScript.")+.*?>)/ixs"; //�ݱ��±װ� ���� �� ����üũ
		$str = preg_replace_callback($singlePluginPattern , array(self,'__callbackConvertScript') , $str);

		return $str;
	}

	/**
	 * ��ũ��Ʈ ó��
	 * @param Array $str
	 * @return $string
	*/
	private static function __callbackConvertScript($matches)
	{
		$str = $matches[0];

		if($matches[5]) {	//�ݱ��±� �����Ҷ�
			$str = str_replace($matches[1],self::FILTER_TAG_COMMENT.'<noscript>',$str);
			$str = str_replace($matches[4],'[remove]',$str);
			$str = str_replace($matches[5],'</noscript>',$str);
		}
		else{
			$str = str_replace($matches[1],self::FILTER_TAG_COMMENT.'<noscript />',$str);
		}

		return $str;
	}

	/**
	 * �ܺ� �÷����� �±� ����
	 * @param String $str
	 * @return $string
	*/
	private static function _convertPlugin($str)
	{
		foreach(self::$_pluginLoadTag as $key=>$val) {
			$pluginTag[] = $key;
		}

		$doublePluginPattern = "/(<((".implode('|',$pluginTag).").*?)\>)(.*?)(<\/((".implode('|',$pluginTag).").*?)>)/ixs"; //�ݱ��±װ� ������ �� ����üũ
		$str = preg_replace_callback($doublePluginPattern , array(self,'__callbackConvertPlugin') , $str);

		$singlePluginPattern = "/(<(".implode('|',$pluginTag).").*?>)/ixs"; //�ݱ��±װ� ������ �� ����üũ
		$str = preg_replace_callback($singlePluginPattern , array(self,'__callbackConvertPlugin') , $str);

		return $str;
	}

	/**
	 * �ܺ� �÷����� �±� ó��
	 * @param Array $matches
	 * @return $string
	*/
	private static function __callbackConvertPlugin($matches)
	{
		$str = $matches[0];

		if($matches[5]) {	//�ݱ��±� �����Ҷ�
			$tagName = $matches[3];
		}
		else{
			$tagName = $matches[2];
		}

		if(!in_array(strtolower($tagName), explode('|', self::$_allowPluginTag))) {	//���Ұ� �±׸�  ������ ġȯ
			if($matches[5]){	//�����±������Ҷ�
				$str = str_replace('</','</no',$str);
			}
			$str = str_replace('<'.$tagName,self::FILTER_DOMAIN_COMMENT.'<no'.$tagName,$str);
			return $str;
		}

		$loadTag = self::$_pluginLoadTag[strtolower($tagName)];
		$attributeUnitPattern = '/('.$loadTag.')=
			(
				 '.self::$_addSingleSlashes.'\'((.*?)[^\\\])*?'.self::$_addSingleSlashes.'\'|
				'.self::$_addDoubleSlashes.'"((.*?)[^\\\])*?'.self::$_addDoubleSlashes.'"|
				 `((.*?)[^\\\])*?`|
				 ([^\s<>])+
			)\s*
		/ixs';

		preg_match($attributeUnitPattern,$str ,$matches2 );
		$dataUrl = stripslashes(str_replace(array('"','\'') , array('',''),stripslashes($matches2[2])));
		$dataUrl = str_replace(' ','',$dataUrl);

		$isSearch = false;	//��밡�� ������ �˻�����
		array_push(self::$_allowPluginDomain, 'godo.co.kr');
		foreach(self::$_allowPluginDomain as $key=>$url) {
			$domainPattern = "/(\/\/)?(http(s)?(:)?\/\/)?([^\/]*)/i";
			preg_match($domainPattern,$dataUrl ,$matches3 );
			$domain = $matches3[0];

			if(!empty($url) && !empty($domain)){
				if(strpos($domain, $url)!==false){
					$isSearch = true;
				}
			}
		}

		if($isSearch===false){
			if($matches[5]){	//�����±������Ҷ�
				$str = str_replace('</','</no',$str);
			}
			$str = str_replace('<'.$tagName,self::FILTER_DOMAIN_COMMENT.'<no'.$tagName,$str);
		}

		return $str;
	}

	/**
	 * �±� ġȯ
	 * @param String $str
	 * @return $string
	*/
	private static function _convertTag($str)
	{
		$doublePluginPattern = "/(<((".self::$_noAllowTagName.").*?)\>)(.*?)(<\/((".self::$_noAllowTagName.").*?)>)/ixs"; //�ݱ��±װ� ������ �� ����üũ
		$str = preg_replace_callback($doublePluginPattern , array(self,'__callbackConvertTag') , $str);

		$singlePluginPattern = "/(<(".self::$_noAllowTagName.").*?>)/ixs"; //�ݱ��±װ� ������ �� ����üũ
		$str = preg_replace_callback($singlePluginPattern , array(self,'__callbackConvertTag') , $str);

		// @qnibus 2015-06 ���ּ� �±��� ������ ���ּ� �±��� ������ ���� ��ġ���� ������ �տ� �ּ� ����
		preg_match('/<!-{2,}/is', $str, $matches1);
		preg_match('/-{2,}>/is', $str, $matches2);
		if (sizeof($matches1[0]) != sizeof($matches2[0])) {
			$str = preg_replace('/<!-{2,}/is', '', $str);
		}

		return $str;
	}

	/**
	 * �±� ó��
	 * @param String $str
	 * @return $string
	*/
	private static function __callbackConvertTag($matches)
	{
		$str = $matches[0];

		if($matches[5]) {	//�ݱ��±� �����Ҷ�
			$tagName = $matches[3];
			$str = str_replace('</','</no',$str);
		}
		else{
			$tagName = $matches[2];
		}

		$str = str_replace('<'.$tagName,self::FILTER_TAG_COMMENT.'<no'.$tagName,$str);

		return $str;
	}

	/**
	 * ������Ʈ ó��
	 * @param String $str
	 * @return $string
	*/
	private static function _convertAttribute($str)
	{
		$tagPattern = '/
		<([a-zA-Z]+).*?
			(
				(\w*)=
					(
						 '.self::$_addSingleSlashes.'\'((.*?)[^\\\])*?'.self::$_addSingleSlashes.'\'|
						'.self::$_addDoubleSlashes.'"((.*?)[^\\\])*?'.self::$_addDoubleSlashes.'"|
						`((.*?)[^\\\])*?`|
						 [^\s<>]+
					)\s*
			)*?
			((\s\w*\s)?).*?
		>
		/ixs';

		$str = preg_replace_callback($tagPattern,array(self,'__callbackConvertDecimalCharacter'),$str);
		$str = preg_replace_callback($tagPattern,array(self,'__callbackConvertAttributeName'),$str);
		$str = preg_replace_callback($tagPattern,array(self,'__callbackConvertAttributeValue'),$str);

		return $str;
	}

	/**
	 * ������Ʈ ��Ʈ����Ʈ ���� ġȯ �ݹ��Լ�
	 * @param Array $matches
	 * @return $string
	*/
	private static function __callbackConvertAttributeName($matches)
	{
		$attrPattern = '/
		(
			('.self::$_noAllowAttributeName.')=
				 (
				    '.self::$_addSingleSlashes.'\'((.*?)[^\\\])*?'.self::$_addSingleSlashes.'\'|
				    '.self::$_addDoubleSlashes.'"((.*?)[^\\\])*?'.self::$_addDoubleSlashes.'"|
					`((.*?)[^\\\])*?`|
					 [^\s<>]+
			 )\s*
		)
		/ixs';

		$str = ($matches[0]);

		preg_match_all($attrPattern,$str,$matches2,PREG_SET_ORDER);

		$_newTag = $str;
		foreach($matches2 as $key=>$_attr) {
			$_newTag = str_replace($_attr[0],'',$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		return $str;
	}

	/**
	 * ������Ʈ ��Ʈ����Ʈ ��� ġȯ �ݹ��Լ�
	 * @param Array $matches
	 * @return $string
	*/
	private static function __callbackConvertAttributeValue($matches)
	{
		$str = $matches[0];

		$commentPattern = array(
			'#\/\*.*\*\/#Uis',
			'/\x00/',
			'/\x0a/',
			'/<!--.*?-->/',
		);

		$str = preg_replace($commentPattern, array('','',''), $str);//�ּ� �Ǵ� �����ڵ� ����
		$attrValuePattern = '/
		(
			(\w*)=
				 (
				    '.self::$_addSingleSlashes.'\'.*?('.self::$_noAllowAttributeValue.')+.*?[^\\\]'.self::$_addSingleSlashes.'\'|
				    '.self::$_addDoubleSlashes.'".*?('.self::$_noAllowAttributeValue.')+.*?[^\\\]'.self::$_addDoubleSlashes.'"|
					`.*?('.self::$_noAllowAttributeValue.')+.*?[^\\\]`|
					('.self::$_noAllowAttributeValue.')+[^\s<>]+
				 )\s*
		)
		/ixs';

		preg_match_all($attrValuePattern,$str,$matches2,PREG_SET_ORDER);

		$_newTag = $str;
		foreach($matches2 as $key=>$_attr) {
			$_newTag =str_replace($_attr[0],'',$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		return $str;
	}

 	/**
	 * �±� <,> ġȯ
	 * @param String $str
	 * @return $string
	*/
	private static function _cleanTag($str)
	{
		$str = str_replace(array('<','>') , array('&lt;','&gt;') , $str);
		return $str;
	}

 	/**
	 * ���� ��Ÿ�� ����
	 * @param String $quotStyle ent_compat : Ȭ����ǥ���� �������� ó���� ���  , ent_quotes : ������ǥ�� �������� ó���Ȱ��, ent_noquotes : �������� ó�� �ȵ��ִ� ���
	 * @return $string
	*/
	private static function _setQuotStyle($quotStyle) {
		if($quotStyle == 'ent_compat') {
			self::$_addSingleSlashes = '\\\\';
			self::$_addDoubleSlashes = '';
		}
		else if($quotStyle == 'ent_quotes'){
			self::$_addSingleSlashes = '\\\\';
			self::$_addDoubleSlashes = '\\\\';
		}
		else if($quotStyle == 'ent_noquotes'){
			self::$_addSingleSlashes = '';
			self::$_addDoubleSlashes = '';
		}
		else{
			self::$_addSingleSlashes = '';
		}
	}

	/**
	 * ���Ұ� �÷����� �±׼���
	 * @param String $noAllowPluginTag '|' �����ڷ� ���ڿ�����
	 * @return
	*/
	private static function _setNoAllowPluginTag($noAllowPluginTag) {
		if(isset($noAllowPluginTag)) {
			if(empty($noAllowPluginTag)) {
				self::$_allowPluginTag = 'null';
			}
			else{
				self::$_allowPluginTag = $noAllowPluginTag;
			}
		}
	}

	/**
	 * ���Ұ� �÷����� �����μ���
	 * @param String $allowPluginDomain '|' �����ڷ� ���ڿ�����
	 * @return
	*/
	private static function _setAllowPluginDomain($allowPluginDomain) {
		if(isset($allowPluginDomain)) {
			self::$_allowPluginDomain = explode('|',$allowPluginDomain);
		}
	}

	/**
	 * Decimal �Ǵ� Hexadecimal HTML character ġȯ
	 * @param Array $macthes
	 * @return String
	*/
	private function __callbackConvertDecimalCharacter($matches) {
		$haxPattern = "/&#x([0-9a-f]+);?/i";
		$charPattern = "/&#([0-9]+);?/i";
		$str = $matches[0];

		preg_match_all($haxPattern,$str,$matches2,PREG_SET_ORDER);
		$_newTag = $str;
		foreach($matches2 as $key=>$_attr) {
			$_newTag = str_replace($_attr[0],chr(hexdec($_attr[1])),$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		preg_match_all($charPattern,$str,$matches3,PREG_SET_ORDER);

		$_newTag = $str;
		foreach($matches3 as $key=>$_attr) {
			$_newTag = str_replace($_attr[0],chr($_attr[1]),$_newTag);
			$str = str_replace($str , $_newTag , $str);
		}

		return $str;
	}

 	/**
	 * xss �������� ����ó��
	 * @param String $str  : ó���� ���ڿ�
	 * @param String $mode : ��� [text : html�±׸� ������ ��� , html : html�±׸� ����� ���]
	 * @param String $quotStyle [ent_compat : Ȭ����ǥ���� �������� ó���� ���  , ent_quotes : ������ǥ�� �������� ó���Ȱ��, ent_noquotes : �������� ó�� �ȵ��ִ� ���]
	 * @param String $noAllowPluginTag : ���Ұ� �÷����� �±�  '|' �����ڷ� ���ڿ�����
	 * @param String $noAllowPluginTag : ���Ұ� ������ '|' �����ڷ� ���ڿ�����
	 * @return $string
	*/
	public static function xssClean($str, $mode = 'html' , $quotStyle , $noAllowPluginTag = null, $allowPluginDomain = null, $disable = '')
	{
		if($mode == 'disable' || $disable == 'disable'){
			return $str;
		}

		if($mode == 'text') {
			$str = self::_cleanTag($str);
		}
		else{	//html���
			self::_setQuotStyle($quotStyle);
			self::_setNoAllowPluginTag($noAllowPluginTag);
			self::_setAllowPluginDomain($allowPluginDomain);
			$str = self::_convertScript($str);
			$str = self::_convertAttribute($str);
			$str = self::_convertPlugin($str);
			$str = self::_convertTag($str);
		}

		return $str;
	}

	/**
	 * xss �������� ����ó��
	 * @param String $str  : ó���� ���ڿ�
	 * @param Array $extraData : ������ ó���� ���ڰ�
	 * @return Array
	*/
	public static function xssCleanArray($str, $extraData)
	{
		if(!is_array($str)) {
			return $str;
		}

		if(array_key_exists(self::DEFAULT_KEY, $extraData)) {	//DEFAULT ó��
			foreach($str as $key=>$val) {
				if(!array_key_exists($key, $extraData)) {
					$arg = array();
					if(!is_array($extraData[self::DEFAULT_KEY])) {
						$arg[0] = $extraData[self::DEFAULT_KEY];
						$arg[1] = null;
						$arg[2] = null;
						$arg[3] = null;
						$arg[4] = null;
					}
					else{
						$arg[0] = $extraData[self::DEFAULT_KEY][0];
						$arg[1] = $extraData[self::DEFAULT_KEY][1];
						$arg[2] = $extraData[self::DEFAULT_KEY][2];
						$arg[3] = $extraData[self::DEFAULT_KEY][3];
						$arg[4] = $extraData[self::DEFAULT_KEY][3];
					}

					$str[$key] = self::xssClean($str[$key],$arg[0], $arg[1],$arg[2], $arg[3], $arg[4] );
				}
			}
		}

		foreach($extraData as $key=>$val) {
			if($key == self::DEFAULT_KEY) {
				continue;
			}

			$arg = array();
			if(!is_array($val)) {
				$arg[0] = $val;
				$arg[1] = null;
				$arg[2] = null;
				$arg[3] = null;
				$arg[4] = null;
			}
			else{
				$arg[0] = $val[0];
				$arg[1] = $val[1];
				$arg[2] = $val[2];
				$arg[3] = $val[3];
				$arg[4] = $val[4];
			}

			$str[$key] = self::xssClean($str[$key],$arg[0], $arg[1], $arg[2], $arg[3], $arg[4] );
		}

		return $str;
	}

	/**
	 * ���ڿ� ���� �̸�������
	 * @param String $value
	 * @return bool
	*/
	function check_email($value)
	{
		$pattern = "(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*$)";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * ���ڿ� ���� ��ȭ��ȣ
	 * @param String $value
	 * @return bool
	*/
	function check_phone($value)
	{
		if(is_array($value))$value = implode('-',$value);
		$pattern = "^[0-9]{2,4}\-[0-9]{3,4}\-[0-9]{4}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * ���ڿ� ���� date����
	 * @param String $value
	 * @return bool
	*/
	function check_date($value)
	{
		$pattern = "^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * ���ڿ� ���� time����
	 * @param String $value
	 * @return bool
	*/
	function check_time($value)
	{
		$pattern = "^[0-9]{2}:[0-9]{2}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * ���ڿ� ���� datetime����
	 * @param String $value
	 * @return bool
	*/
	function check_datetime($value)
	{
		$pattern = "^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * ���ڿ� ���� ����������
	 * @param String $value
	 * @return bool
	*/
	function check_domain($value)
	{
		$pattern = "^[.a-zA-Z0-9-]+\.[a-zA-Z]+$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * ���ڿ� ���� url����
	 * @param String $value
	 * @return bool
	*/
	function check_url($value)
	{
		$pattern = "^[.a-zA-Z0-9-]+\.[a-zA-Z]+[^:space:]+$";
		return $this -> check_pattern($pattern,$value);
	}

	/**
	 * ���ڿ� ���� �ּұ���
	 * @param String $value
	 * @return bool
	*/
	function check_min($min,$value)
	{
		if($min > strlen($value))return false;
		return true;
	}

	/**
	 * ���ڿ� ���� �ִ����
	 * @param String $value
	 * @return bool
	*/
	function check_max($max,$value)
	{
		if($max < strlen($value))return false;
		return true;
	}

	/**
	 * ���ڿ� ���� ��������
	 * @param String $value
	 * @return bool
	*/
	function check_digit($value)
	{
		if(is_numeric($value))return true;
		return false;
	}


	/**
	 * ���ڿ� ���� ����
	 * @param String $pattern,$value
	 * @return bool
	*/
	function check_pattern($pattern,$value)
	{
		if(ereg($pattern,$value)){
			return true;
		}
		return false;
	}

	function check_require($value)
	{
		$value = trim($value);
		if( empty($value) )return false;
		return true;
	}

	/*
	* ���� ī�װ� üũ
	* working by
	* @param int, array [goodsno - ��ǰ��ȣ], [cfgArr - ����ī�װ��� ������ array�� ����]
	* @return boolean true - ����ī�װ�����, false - ����ī�װ�������
	* @date 2015-01-29
	*/
	function check_exception_category($goodsno, $exception_category)
	{
		global $db;

		if(!$goodsno || !is_array($exception_category)) return false;

		//��ǰī�װ� ���ϱ�
		$res = $db->query("SELECT category FROM " . GD_GOODS_LINK . " WHERE goodsno='".$goodsno."' and category");
		while($data = $db->fetch($res)){
			for($i=3; $i<=strlen($data['category']); $i=$i+3)
			{
				$category = substr($data['category'], 0, $i);
				if(in_array($category, $exception_category) ) return true;
			}
		}
		return false;
	}

	/*
	* ���� ��ǰ üũ
	* working by
	* @param int
	* @return boolean true - ���ܻ�ǰ����, false - ���ܻ�ǰ������
	* @date 2015-01-29
	*/
	function check_exception_goods($goodsno, $exception_goods)
	{
		if(!$goodsno || !is_array($exception_goods)) return false;

		if($exception_goods && in_array($goodsno, $exception_goods)){
			return true;
		}

		return false;
	}
}
?>