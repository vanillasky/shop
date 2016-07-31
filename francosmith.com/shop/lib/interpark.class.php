<?

class interparkConf
{
	### ��������
	function getGodo()
	{
		ob_start();
		$file = dirname(__FILE__) . "/../conf/godomall.cfg.php";
		if (!is_file($file)) return false;
		$file = file($file);
		$this->godo = decode($file[1],1);
		ob_end_clean();
	}

	### ������ũ����
	function getInpk()
	{
		// Root Directory
		ob_start();
		@include_once dirname(__FILE__) . "/../conf/config.php";
		$this->rootDir = $cfg[rootDir];
		if ($this->rootDir == ''){
			$this->rootDir = str_replace(array($_SERVER['DOCUMENT_ROOT'], "/lib"), "", dirname(__FILE__));
		}

		// ������ũ
		if (isset($GLOBALS['inpkCfg'])){
			$this->inpkCfg = $GLOBALS['inpkCfg'];
		}
		else {
			@include_once dirname(__FILE__) . "/../conf/interpark.php";
			$this->inpkCfg = $inpkCfg;
		}
		ob_end_clean();
	}

	### ������ũ ���½�Ÿ�� ����
	function getInpkOS()
	{
		// Root Directory
		ob_start();
		@include_once dirname(__FILE__) . "/../conf/config.php";
		$this->rootDir = $cfg[rootDir];
		if ($this->rootDir == ''){
			$this->rootDir = str_replace(array($_SERVER['DOCUMENT_ROOT'], "/lib"), "", dirname(__FILE__));
		}

		// ������ũ
		if (isset($GLOBALS['inpkOSCfg'])){
			$this->inpkOSCfg = $GLOBALS['inpkOSCfg'];
		}
		else {
			@include_once dirname(__FILE__) . "/../conf/interparkOpenStyle.php";
			$this->inpkOSCfg = $inpkOSCfg;
		}
		ob_end_clean();
	}

}


class interpark extends interparkConf
{
	function hashdata(&$data)
	{
		/***************************************************************************************************
		*  hashdata ����
		*    - ������ ���Ἲ�� �����ϴ� �����ͷ� ��û�� �ʼ� �׸�.
		*    - godosno �� �������� md5 ������� ������ �ؽ���.
		***************************************************************************************************/

		$data[godosno]	= $this->godo[sno];					# ������ȣ
		$data[hashdata]	= md5($data[godosno]);				# hashdata ����
	}

	function isExists($args)
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^���� �����ϼ���.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - �������̵� ȯ�������� �������̵�� ��ġ���� �ʽ��ϴ�.^���� �����ϼ���.");

		$this->hashdata($data=array());
		return array('400', readurl("http://godointerpark.godo.co.kr/sock_isExists.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}"));
	}

	function putMerchant($args)
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^���� �����ϼ���.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - �������̵� ȯ�������� �������̵�� ��ġ���� �ʽ��ϴ�.^���� �����ϼ���.");

		$this->hashdata($data=array());
		$data = array_merge($data, $args);
		unset($data[mode]);
		unset($data[dummy]);
		return array('400', readpost("http://godointerpark.godo.co.kr/sock_putMerchant.php", $data));
	}

	function getShopCategory($data)
	{
		return readurl("http://godointerpark.godo.co.kr/sock_getShopCategory.php?callCate=".urlencode($data[callCate]));
	}

	function getDispSrch($data)
	{
		return readurl("http://godointerpark.godo.co.kr/sock_getDispSrch.php?srchName=".urlencode($data[srchName]));
	}

	function getDispNm($data)
	{
		return readurl("http://godointerpark.godo.co.kr/sock_getDispNm.php?dispno=".$data[dispno]);
	}
}

?>