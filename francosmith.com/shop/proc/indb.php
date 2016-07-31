<?

include "../lib/library.php";
include_once dirname(__FILE__)."/../conf/config.php";

function chk_referer($url=""){
	if( preg_match('/'.addslashes($_SERVER['HTTP_HOST']).'/',$_SERVER['HTTP_REFERER']) ) $ret = true;
	else $ret = false;

	if( $ret && $url){
		$url = str_replace('/','\/',$url);
		if( preg_match('/'.$url.'/',$_SERVER['HTTP_REFERER']) )	$ret = true;
		else $ret = false;
	}
	return $ret;
}

$mode  = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

switch ($mode){

	case "sendmail":
		
		if(!chk_referer($cfg[rootDir].'/proc/popup_email.php')){			
			exit;
		}
		
		include "../lib/mail.class.php";

		$_POST[Subject] = stripSlashes($_POST[Subject]);

		$mail = new Mail($params);

		if(!$cfg['adminEmail']){
			msg("������ ���� �ּҰ� �����ϴ�.","close");
			exit;
		}

		$headers	= array(
					Name	=> "$_POST[Name]",
					From	=> "$_POST[From]",
					To		=> $cfg['adminEmail'],
					Subject	=> "$_POST[Subject]",
					);
		if ($mail->send($headers, $_POST[Body])) msg("���������� ������ �߼۵Ǿ����ϴ�","close");
		else msg("���� ������ ������ �߻��߽��ϴ�. �ٽ� �����ֽñ� �ٶ��ϴ�",-1);
		exit;
		break;

	case "send_sms":

		if(!chk_referer()){			
			exit;
		}
		
		include_once dirname(__FILE__)."/../lib/sms.class.php";
		$sms = new Sms();
		$sms_sendlist = $sms->loadSendlist();

		if( !trim($cfg['smsAdmin']) ){
			header("Status: ������ �޴��� ��ȣ�� �����ϴ�.", true, '400');
			echo ""; # ��������
			exit;
		}
		

		if ($sms->smsPt<=0){
			header("Status: �ܿ��ݼ� �������� ���ڸ� �߼��� �� �����ϴ�.", true, '400');
			echo ""; # ��������
			exit;
		}

		$sms->log($_GET[msg],$cfg[smsAdmin],'',1);
		$sms_sendlist->setSimpleInsert($cfg[smsAdmin], $sms->smsLogInsertId, '');
		if ($sms->send($_GET[msg],$cfg[smsAdmin],$_GET[callback])){
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
		else {
			header("Status: ���� �߼��� ���еǾ����ϴ�.", true, '400');
			echo ""; # ��������
		}

		exit;
		break;

}

?>