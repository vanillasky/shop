<?php

function reverse_file_array($arr)
{
	if(!$arr)return false;
	foreach($arr as $k => $v)
		foreach($v as $k1 => $v1)$tmp[$k1][$k] = $v1;
	return 	$tmp;
}

class upload_file
{
	var $file;
	var $target;
	var $extType;
	var $chkType;

	function upload_file($file='',$target='',$chkType='')
	{
		if($file){
			$this->upload_set(&$file,$target,$chkType);
		}
	}

	/**
	 * ���� �Ҵ�
	 * chkType �� ���� ȭ��Ʈ ���� or �� ���� �� �� �ϳ� ���� ����
	 * @void
	*/
	function upload_set(&$file,$target,$chkType='')
	{
		$this->file = &$file;
		$this->target = $target;
		switch($this->chkType){
			case "design":
				$this->extAllowType = array('htm','jpg','jpeg','gif','png','bmp','ico','swf'); //ȭ��Ʈ ����Ʈ
				$this->extLimitType = ""; //�� ����Ʈ
				$this->chkType = "text";
			break;
			case "image":
				$this->extAllowType = array('jpg','jpeg','gif','png','bmp','ico','swf'); //ȭ��Ʈ ����Ʈ
				$this->extLimitType = ""; //�� ����Ʈ
				$this->chkType = "image";
			break;
			default:
				$this->extAllowType = ""; //ȭ��Ʈ ����Ʈ
				$this->extLimitType = array('php','php3','php4','php5','phtml','shtml','html','htm','inc','asp','aspx','jsp','jar','exe','cgi','bat','pl','pm','py','htaccess'); // �� ����Ʈ
				$this->chkType = $chkType;
			break;
		}
	}

	/**
	 * �Ϲ� ���ε� ���� Ȯ���� ����
	 * @return bool
	*/
	function file_extension_check()
	{
		if($this->file['name']){
			$tmp = explode('.',$this->file['name']);
			$extension = strtolower($tmp[count($tmp)-1]);
			if($this->extAllowType){ //ȭ��Ʈ ����Ʈ üũ
				if(!in_array($extension,$this->extAllowType))return false;
			}
			if($this->extLimitType){ //�� ����Ʈ üũ
				if(in_array($extension,$this->extLimitType))return false;
			}
		}
		return true;
	}

	/**
	 * �Ϲ� ���ε� ���� ����
	 * @return bool
	*/
	function file_type_check()
	{
		if($this->file['tmp_name']){
			if($this->chkType == "image"){
				$imgarr = getimagesize($this->file['tmp_name']);
				if($imgarr[2] == 0){
					return false;
				}
			}
		}
		if($this->file['tmp_name'] && ($mime = Core::helper('File')->mime($this->file['tmp_name'])) != 'unknown' ) {
			if($this->chkType&&!preg_match('/'.$this->chkType.'/',$mime))return false;
		}
		return true;
	}

	/**
	 * ���Ͼ��ε�
	 * @return bool
	*/
	function upload()
	{
		if($this->file['tmp_name']){
			if(!$this->file_extension_check()){
				return false;
			}
			if(!$this->file_type_check()){
				return false;
			}
			@move_uploaded_file($this->file['tmp_name'],$this->target);
			@chmod($this->target,0707);
		}
		return true;
	}
}
?>