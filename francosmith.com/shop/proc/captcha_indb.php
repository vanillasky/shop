<?php
include '../lib/library.php';

$mode = $_POST['mode'];
if(!$mode) $mode = $_GET['mode'];

switch($mode){
	case "chkBoardCaptcha":
		header("Content-type: text/html; charset=euc-kr");
		if (file_exists("../conf/bd_$_POST[id].php")) {
			include "../conf/bd_$_POST[id].php";
		}

		# Anti-Spam ����
		$switch = ($bdSpamBoard&1 ? '103' : '000') . ($bdSpamBoard&2 ? '4' : '0');
		$rst = antiSpam($switch, "", "post");
		if (substr($rst[code],0,1) == '4') exit ("�ڵ���Ϲ������ڰ� ��ġ���� �ʽ��ϴ�. �ٽ� �Է��Ͽ� �ֽʽÿ�.");
		if ($rst[code] <> '0000') exit("���� ��ũ�� �����մϴ�.");
		echo 'true';
		break;
}
?>