<?
function pgChk()
{
	global $godo;

	require_once(dirname(__FILE__)."/../lib/qfile.class.php");
	$qfile = new qfile();

	if(!$godo){
		$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
		if (!is_file($file)) return false;
		$file = file($file);
		$godo = decode($file[1],1);
	}

	@include dirname(__FILE__)."/../conf/config.php";
	if($cfg[settlePg]) @include dirname(__FILE__)."/../conf/pg.".$cfg[settlePg].".php";
	if($pg[id] && $pg[id] != $cfg[gpg_id])$arr = array('gpg_id' => $pg[id]);

	if($arr){
		## ȯ�漳�� ���Ͽ� pg_id ���� ����
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);
		$cfg = array_merge($cfg,$arr);
		$qfile->open( dirname(__FILE__)."/../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		## ������ pg_id�� ���� Ÿ���� �����Ѵ�
		$url = 'http://godo.co.kr/userinterface/pgUpdate.php?id='.$pg[id].'&sno='.$godo[sno].'&pg='.$cfg[settlePg];
		@file($url);
	}
}
?>