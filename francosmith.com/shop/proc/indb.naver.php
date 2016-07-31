<?
include "../lib/library.php";
include_once dirname(__FILE__)."/../conf/config.php";
include_once dirname(__FILE__)."/../conf/naverCheckout.cfg.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
include_once dirname(__FILE__)."/../lib/httpSock.class.php";

session_start();

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

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

if(!$mode) {
	msg("ó���� �۾��� Ÿ���� �������� �ʾҽ��ϴ�.", -1);
}

switch($mode) {
	case "checkAccount" :
		// �޾ƿ� ���� ����ϱ� ���ϰ� ����
			$shopId			= ($_POST['shopId'])		? $_POST['shopId']			: $_GET['shopId'];			// ���θ� ���̵�
			$shopPassword	= ($_POST['shopPassword'])	? $_POST['shopPassword']	: $_GET['shopPassword'];	// ���θ� ��й�ȣ
			$MallUserSSN1	= ($_POST['MallUserSSN1'])	? $_POST['MallUserSSN1']	: "";						// ���θ� ������ �ֹι�ȣ ���ڸ�
			$MallUserSSN2	= ($_POST['MallUserSSN2'])	? $_POST['MallUserSSN2']	: "";						// ���θ� ������ �ֹι�ȣ ���ڸ�

		// �ʼ� �Է� �� �˻�
			if(!$shopId) msg("���θ� ���̵� ���۵��� �ʾҽ��ϴ�.", -1);
			if(!$shopPassword) msg("���θ� ��й�ȣ�� ���۵��� �ʾҽ��ϴ�.", -1);
			if(!$MallUserSSN1) msg("�ֹι�ȣ ���ڸ��� ���۵��� �ʾҽ��ϴ�.", -1);
			if(!$MallUserSSN2) msg("�ֹι�ȣ ���ڸ��� ���۵��� �ʾҽ��ϴ�.", -1);

		// ȸ���� �����ϴ��� Ȯ��
			list($cnt) = $db->fetch("SELECT COUNT(m_id) FROM ".GD_MEMBER." WHERE m_id = '$shopId' AND password = password('$shopPassword')");
			if(!$cnt) go("./naver_storemember_fail1.php");

		// ȸ�� �̸� �� ȸ���� �ֹι�ȣ �˻� �� ���� �ι����� üũ
			list($m_no, $MallUserName, $resno1, $resno2, $rncheck) = $db->fetch("SELECT m_no, name, resno1, resno2, rncheck FROM ".GD_MEMBER." WHERE m_id = '".$shopId."'");
			if(!$MallUserName) msg("ȸ������ �� �̸��� �������� �ʽ��ϴ�.\\n�� ������ �����ϱ� ���ؼ� ȸ������ �ʼ������Դϴ�.\\n\\n���θ��� ȸ�������� Ȯ�� �� �ٽ� �õ��� �ּ���.", -1);
			if(!$resno1 || !$resno2) {
				// ���θ� ������ ��� ������ ���� �����޼��� ���� ǥ��
				include_once dirname(__FILE__)."/../conf/fieldset.php";

				if($rncheck == "ipin" && $ipin['useyn'] == "y") go("./naver_storemember_fail3.php");
				else go("./naver_storemember_fail6.php");
			}
			if(md5($MallUserSSN1) != $resno1 || md5($MallUserSSN2) != $resno2) go("./naver_storemember_fail6.php");

		// ���ǿ� ����
			$_SESSION['NCINFO']['MallUserID'] = $shopId;
			$_SESSION['NCINFO']['MallUserNo'] = $m_no;
			$_SESSION['NCINFO']['MallUserName'] = $MallUserName;
			$_SESSION['NCINFO']['MallUserSSN'] = $MallUserSSN1.$MallUserSSN2;

		go("./naver_storemember_confirm.php");

		break;

	case "infoSupplyAgreement" :
		$MallUserID		= ($_SESSION['NCINFO']['MallUserID'])	? $_SESSION['NCINFO']['MallUserID']		: "";
		$NCUserNo		= ($_SESSION['NCINFO']['NCUserNo'])		? $_SESSION['NCINFO']['NCUserNo']		: "";
		$MallUserName	= ($_SESSION['NCINFO']['MallUserName'])	? $_SESSION['NCINFO']['MallUserName']	: "";
		$MallUserSSN	= ($_SESSION['NCINFO']['MallUserSSN'])	? $_SESSION['NCINFO']['MallUserSSN']	: "";
		$Timestamp		= ($_SESSION['NCINFO']['Timestamp'])	? $_SESSION['NCINFO']['Timestamp']		: "";

		// �ʼ� �Է� �� �˻�
			if(!$MallUserID) msg("ȸ���� ���θ� ���̵� ������ �����ϴ�.\\n\\nó������ �ٽ� ������ �ּ���.", -1);
			if(!$MallUserSSN) msg("ȸ���� �ֹε�Ϲ�ȣ ������ �����ϴ�.\\n\\nó������ �ٽ� ������ �ּ���.", -1);

		// ������ȣ ��ȣȭ
			$temp_ar_NCUserNo = explode('|||', $naverCheckoutAPI->ncCrypt('decrypt', $NCUserNo, $Timestamp));
			if($temp_ar_NCUserNo[0] == "ERRO") msg("������ �ֽ��ϴ�.\\n\\n".$temp_ar_NCUserNo[1], -1);
			else $temp_NCUserNo = $temp_ar_NCUserNo[1];

		// ���̹� üũ�ƿ� ȸ������ Ȯ��
			$apiResult = $naverCheckoutAPI->CompareMember($MallUserSSN,$MallUserName,$temp_NCUserNo);

			if($apiResult === false) {
				if($naverCheckoutAPI->error) msg("������ �ֽ��ϴ�.\\n\\n".$naverCheckoutAPI->error);
			}
			else {
				if($apiResult == "Y") ;
				elseif($apiResult == "N") go("./naver_storemember_fail2.php"); // Error : ���θ� ȸ���� ���̹� ȸ���� �ֹε�� or �̸��� �ٸ� ���
				else go("./naver_storemember_fail5.php"); // Error : ���ӿ���
			}

			// ������ ȸ�� ���� �۽�
				if($checkoutCfg['testYn'] == "y") {
					$url4Send = "https://test-checkout.naver.com/customer/api/CP949/memberInfoRecieve.nhn"; // EUC-KR Test
					//$url4Send = "https://test-checkout.naver.com/customer/api/memberInfoRecieve.nhn"; // UTF-8 Test
				}
				else {
					$url4Send = "https://checkout.naver.com/customer/api/CP949/memberInfoRecieve.nhn"; // EUC-KR Service
					//$url4Send = "https://checkout.naver.com/customer/api/memberInfoRecieve.nhn"; // UTF-8 Service
				}

				// ������ ȸ�� ���� �Ķ����
					$NCMallID = $checkoutCfg['naverId']; // ������ ���̵�
					$MallUserID = $naverCheckoutAPI->ncCrypt('encrypt', $_SESSION['NCINFO']['MallUserID'], $Timestamp); // ���θ� ȸ�� ���̵�
					$MallUserNo = $naverCheckoutAPI->ncCrypt('encrypt', $_SESSION['NCINFO']['MallUserNo'], $Timestamp); // ���θ� ȸ�� ���� ��ȣ

					$param4Send = "NCMallID=".urlencode($NCMallID)."&MallUserID=".urlencode($MallUserID)."&MallUserNo=".urlencode($MallUserNo)."&NCUserNo=".urlencode($NCUserNo)."&Timestamp=".urlencode($Timestamp);

				// ȸ�������� outLink ���� ���
					list($outlink) = $db->fetch("SELECT outlink FROM ".GD_MEMBER." WHERE m_no = '".$_SESSION['NCINFO']['MallUserNo']."'");
					$db->query("UPDATE ".GD_MEMBER." SET outlink = '".modOutlink($outlink, "naverCheckout")."' WHERE m_no = '".$_SESSION['NCINFO']['MallUserNo']."'");

				// �����ĺ� ���� ���� & �������� ���� ���� ����
					$agree_CI = ($_POST['agree'] == "y") ? $_POST['agree'] : "n";
					$agree_info = ($_POST['agree2'] == "y") ? $_POST['agree2'] : "n";
					$query = "INSERT INTO ".GD_NAVERCHECKOUT_AGREEMENT." SET m_no = '".$_SESSION['NCINFO']['MallUserNo']."', agree_CI = '$agree_CI', agree_info = '$agree_info', regdt = NOW(), ip = '".$_SERVER['REMOTE_ADDR']."'";
					$db->query($query);

				unset($_SESSION['NCINFO']);
				go($url4Send."?".$param4Send);

		break;
}
?>
