<?php
/**
 * @todo
 *
 */
Class Log {
	var $logDir = '';		// �α� ����Ǵ� �⺻ ���丮
	var $logFile = '';		// �α� ����Ǵ� ���ϸ�
	var $logMsg = '';		// �α� ����Ǵ� ����
	var $logProtectDir = '';	//�α� ������ ��ȣ ���丮 - �ش� ��� ���� ������ ������ �ȵ�

	/**
	 * Constructor
	 */
	function Log() {
		$this->logProtectDir = dirname(__FILE__).'/../log';
	}

	function setLogDir($dir) {
		$this->logDir = $dir;
	}
	function setLogProtectDir($protectDir) {
		$this->logProtectDir = $protectDir;
	}
	function setLogFile($fileName) {
		$this->logFile = $fileName;
	}
	function setLogMsg($msg) {
		$this->logMsg = $msg;
	}
	function getLogDir() {
		return $this->logDir;
	}
	function getLogProtectDir() {
		return $this->logProtectDir;
	}
	function getLogFile() {
		return $this->logFile;
	}
	function getLogMsg() {
		return $this->logMsg;
	}

	/**
	 * ���� ����
	 * @param array $delArr ������ ������ ���ϸ� �Ǵ� ���丮
	 */
	function deletePathArray($delArr) {
		$return = array();
		foreach($delArr as $value) {
			if(@is_dir($value)) {
				$this->deleteDirectory($value);
			} else {
				$pos = strpos($dir, $this->getLogProtectDir());
				if($pos !== false){
					@unlink($value);
				} else {
					$return['code'] = "003";
					$return['msg'] = $value." ���� ���еǾ����ϴ�.";
				}
			}
		}
	}

	/**
	 * ���丮 ����
	 * ���� ���丮 �� ���� ���� �� ��û ���丮 ����
	 * @param string $dir ������ ���丮 ���
	 * @example $delReturn = $this->deleteDirectory("/www/s4self/shop/log/admin");
	 * @return array ó����� (�����ڵ� �����޼���)
	 */
	function deleteDirectory($dir) {
		$return = array();
		$pos = strpos($dir, $this->getLogProtectDir());
		if($pos === false){
			$return['dir']['code'] = "003";
			$return['dir']['msg'] = $dir." ���� ���еǾ����ϴ�.";
		} else {
			if(!@is_dir($dir)) {
				$return['dir']['code'] = "001";
				$return['dir']['msg'] = $dir." ���丮�� Ȯ���Ͻʽÿ�.";
			} else {
				if (substr($dir, strlen($dir) - 1, 1) != DIRECTORY_SEPARATOR) {
					$dir .= DIRECTORY_SEPARATOR;
				}
				$dirPath = glob($dir."*",GLOB_MARK);
				foreach($dirPath as $dirNow) {
					if(@is_dir($dirNow)) {
						$this->deleteDirectory($dirNow);
					} else {
						@unlink($dirNow);
					}
				}
				if(@rmdir($dir)) {
					$return['dir']['code'] = "000";
					$return['dir']['msg'] = "�����Ǿ����ϴ�.";
				} else {
					$return['dir']['code'] = "002";
					$return['dir']['msg'] = $dir." ���� ���еǾ����ϴ�.";
				}
			}
		}
		return $return;
	}

	/**
	 * ���丮 ����
	 * ��û ���丮���� ������ ����
	 * @param string $dir ������ ���丮 ���
	 * @example $makeReturn = $this->makeDirectory("/www/s4self/shop/log/admin");
	 * @return array ó����� (�����ڵ� �����޼���)
	 */
	function makeDirectory($dir) {
		$return = array();
		$pos = strpos($dir, $this->getLogProtectDir());
		if($pos === false){
			$return['code'] = "003";
			$return['msg'] = $dir." ���� ���еǾ����ϴ�.";
		} else {
			$dirFull = "";
			$dirPath = explode(DIRECTORY_SEPARATOR, $dir);
			foreach($dirPath as $dirNow) {
				if($dirNow) {
					$dirNow = DIRECTORY_SEPARATOR.$dirNow;
					$dirFull .= $dirNow;
					if (!@is_dir($dirFull)) {
						@umask(0);
						if(@mkdir($dirFull, 0707)) {
							$return['code'] = "000";
							$return['msg'] = "�����Ǿ����ϴ�.";
						} else {
							$return['code'] = "001";
							$return['msg'] = $dirFull." ���� ���еǾ����ϴ�.";
						}
					} else {
						$return['code'] = "002";
						$return['msg'] = "�̹� �����ϴ� ���丮�Դϴ�.";
					}
				} else {
					$return['code'] = "004";
					$return['msg'] = "���� ���еǾ����ϴ�.";
				}
			}
		}
		return $return;
	}

	/**
	 * path ���� (argument list�� �����η� ����)
	 * @param argument list
	 * @example $logDir = $this->buildPath('shop', 'log', date('Ym'));
	 * @return string ó����� (/shop/log/201506)
	 */
	function buildPath() {
		$segments = func_get_args();
		$path = join(DIRECTORY_SEPARATOR, $segments);
		return $this->getAbsolutePath($path);
	}

	/**
	 * ������ �� (../, ./)�� ���Ե� ��θ� �����Ͽ� ��� ��ȯ
	 * @param string $path ������
	 * @return string $path ������
	 */
	function getAbsolutePath($path) {
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.' == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		return DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $absolutes);
	}

	/**
	 * ����
	 * @param string $zipTargetDirectory ������ ����
	 * @param string $zipTargetFile ������ ����
	 * @return ó�����(�����ڵ� or array ������)
	 */
	function makeZipLogFile($zipTargetDir, $zipTargetFile) {
		include_once dirname(__FILE__).'/pclzip/pclzip.lib.php';

		$zipTargetDir = $this->getAbsolutePath($zipTargetDir);

		$zipFile = $zipTargetDir.DIRECTORY_SEPARATOR.$zipTargetFile.'.zip';
		$targetFile = $zipTargetDir.DIRECTORY_SEPARATOR.$zipTargetFile;

		$zipLog = new PclZip($zipFile);
		$returnZip = $zipLog->create($targetFile);
		for($i=0; $i<sizeof($returnZip); $i++) {
			if($returnZip[$i]['status'] == 'ok') {
				@chmod($zipFile, 0707);
				@unlink($targetFile);
			}
		}

		return $returnZip;
	}

	/**
	 * �α� �ۼ�
	 * @param string $meg �α� ����
	 * @param string $logDir �α� ������
	 * @param string $logFile �α� ���ϸ�
	 */
	function writeLog() {
		if(!$this->getLogDir()) {
			$error['code'] = '001';
			$error['msg'] = '���� ��ΰ� �����ϴ�.';
			return $error;
		}
		if(!$this->getLogFile()) {
			$error['code'] = '002';
			$error['msg'] = '���� ���ϸ��� �����ϴ�.';
			return $error;
		}
		if(!$this->getLogMsg()) {
			$error['code'] = '003';
			$error['msg'] = '���� �޼����� �����ϴ�.';
			return $error;
		}
		if($this->makeDirectory($this->getLogDir())) {
			$logFile = $this->buildPath($this->getLogDir(), $this->getLogFile());
			@error_log($this->getLogMsg(), 3, $logFile);
			@chmod($logFile, 0707);
		}
	}
}