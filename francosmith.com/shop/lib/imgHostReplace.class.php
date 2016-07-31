<?

class imgHost
{
	var $ftp;				// FTP Resource
	var $ftpConf;			// FTP ����
	var $putCnt = 0;
	var $putMax = 5;		// FTP 1ȸ ���Ӵ� Img ���۰��ɼ�
	var $errMsg;			// ���� �޽���

	function imgHost($conf)
	{
		ob_start();
		if (is_string($conf) === true)
		{
			$conf = unserialize($conf);
			$conf['pass'] = decode($conf['pass'],1);
		}

		$this->ftpConf['domain'] = $conf['domain'];
		$this->ftpConf['user'][0] = $conf['userid'];
		$this->ftpConf['user'][1] = $conf['pass'];
		ob_end_clean();
	}

	### String replace(): �̹�����Ρ�ȣ���ð�� ġȯ
	function replace($source)
	{
		$split = $this->_split($source);
		for ($i=1,$s=count($split); $i < $s; $i += 2)
		{
			$prev = &$split[($i-1)];
			$self = &$split[$i];
			$next = &$split[($i+1)];

			if (preg_match('@^http:\/\/@ix', $self));
			else {
				# godoOld �Ӽ� ����
				$prev = $this->clearGodoOld('@(<(?:[^<])+$)@ix', $prev);
				$next = $this->clearGodoOld('@(^(?:[^>])+>)@ix', $next);

				# �̹��� ����
				$src = $this->_filePut($self);

				# godoOld �Ӽ� �߰�
				$quot = substr($prev,-1,1);
				if (in_array($quot, array('"', "'")) === false) $quot = '';
				$self = $src . $quot . ' godoOld=' . $quot . $self;
			}
		}
		$source = implode('', $split);
		return $source;
	}

	### String clearGodoOld(): godoOld �Ӽ� ���� �� ����
	function clearGodoOld($pattern, $str)
	{
		$res = preg_split($pattern, $str, 3, PREG_SPLIT_DELIM_CAPTURE);
		$res[1] = preg_replace('@ ?godoOld\=(?:"|\')(?:[^"|\'])*[^"|\']+(?:"|\')@i', '', $res[1]);
		$str = implode('', $res);
		return $str;
	}

	### Array imgStatus(): �̹������ ��Ȳ
	function imgStatus($source)
	{
		$cnt = array();
		if (is_string($source) === true) $split = $this->_split($source);
		else $split = $source;
		for ($i=1,$s=count($split); $i < $s; $i += 2)
		{
			$cnt['tot']++;
			if (preg_match('@^http:\/\/@ix', $split[$i]));
			else {
				if (substr($split[$i],0,1) == '/') $imgPath = $_SERVER['DOCUMENT_ROOT'] . $split[$i];
				if (file_exists($imgPath) === true){
					$chkimg = getimagesize($imgPath);
					if ($chkimg[2] != 0){
						$cnt['in']++;
					}
				}
			}
		}
		return $cnt;
	}

	### Array _split(): �̹������ �������� ����
	function _split($source)
	{
		$cnt = array();
		$Ext = '(?<=src\=")(?:[^"])*[^"](?=")'.
			"|(?<=src\=')(?:[^'])*[^'](?=')".
			'|(?<=src\=\\\\")(?:[^"])*[^"](?=\\\\")'.
			"|(?<=src\=\\\\')(?:[^'])*[^'](?=\\\\')";
		$pattern = '@('. $Ext .')@ix';
		$split = preg_split($pattern, $source, -1, PREG_SPLIT_DELIM_CAPTURE);
		return $split;
	}

	### String _filePut(): �̹��������� ȣ���������� �� ȣ���ð�� ����
	function _filePut($path)
	{
		$imgPath = $path;
		if (substr($imgPath,0,1) == '/') $imgPath = $_SERVER['DOCUMENT_ROOT'] . $imgPath;
		if (file_exists($imgPath) === false){
			return $path;
		} else {
			$chkimg = getimagesize($imgPath);
			if ($chkimg[2] == 0) return $path;
		}
		if (is_resource($this->ftp) === false) $this->_connector();

		$this->_setDir();
		$remotePath = ($this->ftpConf['dirPath'] == '/' ? '' : $this->ftpConf['dirPath']) . '/' . basename($imgPath);
		$res = $this->_call("put", array($remotePath, $imgPath, FTP_BINARY));
		if ($res){
			$this->_call("site", array(sprintf('CHMOD %u %s', 755, $remotePath)));
			$path = 'http://' . $this->ftpConf['domain'] . $remotePath;
		}
		else if (preg_match('@Warning.*ftp_put\(\).*Ok\ to\ send\ data@ix', $this->errMsg))
		{
			header("Status: �뷮 �ʰ��� ������ ������ �� �����ϴ�.", true, 400);
			echo "";
			exit;
		}

		$this->putCnt++;
		if ($this->putCnt >= $this->putMax){
			$this->putCnt = 0;
			$this->_destruct();
		}
		return $path;
	}

	### Void _setDir(): ���Ͼ��ε��� ���丮 ����
	function _setDir()
	{
		ob_start();
		if ($this->ftpConf['dirPath'] == "")
		{
			# goods_XXXX ���丮 �̵�
			list($docRoot) = explode(".", basename($_SERVER['DOCUMENT_ROOT']));
			$docRoot = "goods_{$docRoot}";
			if (!$this->_call("chdir", array($docRoot)))
			{
				if ($this->_call("mkdir", array($docRoot)))
				{
					$this->_call("site", array(sprintf('CHMOD %u %s', 755, $docRoot)));
					$this->_call("chdir", array($docRoot));
				}
			}

			# infra ���丮 �̵�
			$ls = $this->_call("rawlist", array("/{$docRoot}"));
			if (count($ls) > 0)
			{
				foreach ($ls as $v)
				{
					$vinfo = preg_split("/[\s]+/", $v, 9);
					if ($vinfo[0] !== "total" && $vinfo[0]{0} == "d") $dirlist[] = strip_tags($vinfo[8]);
				}
				sort($dirlist, SORT_NUMERIC);
				$lastDir = $dirlist[count($dirlist) - 1];
				$last_ls = $this->_call("rawlist", array("/{$docRoot}/{$lastDir}"));
				if (count($last_ls) > 100){
					$lastDir = sprintf("%d", $lastDir + 1);
				}
			}
			else {
				$lastDir = sprintf("%d", 1);
			}
			if (!$this->_call("chdir", array($lastDir)))
			{
				if ($this->_call("mkdir", array($lastDir)))
				{
					$this->_call("site", array(sprintf('CHMOD %u %s', 755, $lastDir)));
					$this->_call("chdir", array($lastDir));
				}
			}
			$this->ftpConf['dirPath'] = $this->_call("pwd");
		}
		ob_end_clean();
	}

	### Void _connector(): ����
	function _connector()
	{
		if ($this->ftpConf['domain'] == '')
		{
			header("Status: FTP �������� �Է��ϼž� �մϴ�.", true, 400);
			echo "";
			exit;
		}
		if ($this->ftpConf['user'][0] == '')
		{
			header("Status: FTP ID�� �Է��ϼž� �մϴ�.", true, 400);
			echo "";
			exit;
		}
		if ($this->ftpConf['user'][1] == '')
		{
			header("Status: FTP Password�� �Է��ϼž� �մϴ�.", true, 400);
			echo "";
			exit;
		}

		$ftpDomin = 'ftp.' . $this->ftpConf['domain'];
		$this->_construct($ftpDomin);
		if (is_resource($this->ftp) === false)
		{
			header("Status: FTP �������� ���ӵ��� �ʽ��ϴ�.", true, 400);
			echo "";
			exit;
		}

		$login_result = $this->_call("login", $this->ftpConf['user']);
		if ($login_result === false)
		{
			header("Status: FTP ���̵� �Ǵ� ��й�ȣ�� ��Ȯ���� �ʽ��ϴ�.", true, 400);
			echo "";
			exit;
		}
	}

	### Void _construct(): Constructor
	function _construct($host, $port = 21, $timeout = 90)
	{
		$this->ftp = @ftp_connect($host, $port, $timeout);
	}

	### Void _destruct(): Destructor
	function _destruct()
	{
		@ftp_close($this->ftp);
	}

	### Mixed _call(): Re-route all function calls to the PHP-functions
	function _call($function, $arguments=array())
	{
		array_unshift($arguments, $this->ftp); // Prepend the ftp resource to the arguments array
		ob_start();
		$result = call_user_func_array('ftp_' . $function, $arguments); // Call the PHP function
		$this->errMsg = ob_get_clean();
		return $result;
	}
}

?>