<?php
/*

*/
class qfile {

	var $filePath;
	var $fileData;

	var $tmpPath;
	var $tmpPathLock;

	var $fpLock;
	var $fpTmp;


	function qfile() {
		$this->tmpPath = dirname(__FILE__) . "/../data/chkQuota";
		$this->tmpPathLock = dirname(__FILE__) . "/../data/chkQuotaLock";
		//$this->tmpPath = dirname(__FILE__) . "/test/chkQuota";
		//$this->tmpPathLock = dirname(__FILE__) . "/test/chkQuotaLock";
	}


	function open($filepath) {

		if(!is_file($this->tmpPathLock)) die("�����ۼ��� ������ �߻��߽��ϴ�. tmp�ۼ��� �����߽��ϴ�");

		$this->filePath = $filepath;
		$this->fileData='';

		$this->fpLock = fopen($this->tmpPathLock,'w');

		if(!flock($this->fpLock, LOCK_EX))
		{
			return false;
		}

		$this->fpTmp = fopen($this->tmpPath, "w");


	}

	function write($string) {
		if($this->fpTmp==false) return false;
		if(fwrite($this->fpTmp,$string)===false) die("�����ۼ��� ������ �߻��߽��ϴ�. �����뷮�̳� ���ϱ����� Ȯ���ؾ��մϴ�.");
		$this->fileData.=$string;
	}

	function close() {
		if($this->fpTmp==false) return false;
		fclose($this->fpTmp);
		$this->fpTmp = fopen($this->tmpPath, "w");
		fclose($this->fpTmp);

		$fpOri = fopen($this->filePath, "w");
		fwrite($fpOri,$this->fileData);
		fclose($fpOri);


		flock($this->fpLock, LOCK_UN);
		fclose($this->fpLock);

		$this->fpLock=false;
		$this->fpTmp=false;

	}
}

?>