<?
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	/********************************************************************************************************************************************
		NICE�ſ������� Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED

		���񽺸� : �����ֹι�ȣ���� (IPIN) ����
		�������� : �����ֹι�ȣ���� (IPIN) ����� ���� ���� ��� ������

				   ���Ź��� ������(�������)�� ��ȣȭ�Ͽ� ����� ������ Ȯ���մϴ�.
	*********************************************************************************************************************************************/

	$sSiteCode					= $ipin['code'];			// IPIN ���� ����Ʈ �ڵ�		(NICE�ſ����������� �߱��� ����Ʈ�ڵ�)
	$sSitePw					= $ipin['password'];			// IPIN ���� ����Ʈ �н�����	(NICE�ſ����������� �߱��� ����Ʈ�н�����)

	$sEncData					= "";			// ��ȣȭ �� ����� ���� ����
	$sDecData					= "";			// ��ȣȭ �� ����� ���� ����

	$sRtnMsg					= "";			// ó����� �޼���



	/*
	�� sType ������ ���� ����  ����������������������������������������������������������������������������������������������������������
		����Ÿ�� �����ϱ� ���� ���а�.

		SEQ : ��û��ȣ ����
		REQ : ��û ����Ÿ ��ȣȭ
		RES : ��û ����Ÿ ��ȣȭ
	������������������������������������������������������������������������������������������������������������������������������������������
	*/
	$sType						= "RES";


	/*
	�� sModulePath ������ ���� ����  ����������������������������������������������������������������������������������������������������������
		��� ��μ�����, '/������/����' ���� ������ �ּž� �մϴ�.

		+ FTP �� ��� ���ε�� �������¸� 'binary' �� ������ �ֽð�, ������ 755 �� ������ �ּ���.

		+ ������ Ȯ�ι��
		  1. Telnet �Ǵ� SSH ���� ��, cd ��ɾ �̿��Ͽ� ����� �����ϴ� ������ �̵��մϴ�.
		  2. pwd ��ɾ��� �̿��ϸ� �����θ� Ȯ���Ͻ� �� �ֽ��ϴ�.
		  3. Ȯ�ε� �����ο� '/����'�� �߰��� ������ �ּ���.
	������������������������������������������������������������������������������������������������������������������������������������������
	*/
	$self_filename = basename($_SERVER['PHP_SELF']);
	$loc = strpos($_SERVER['PHP_SELF'], $self_filename);
	$loc = substr($_SERVER['PHP_SELF'], 0, $loc);
	$sModulePath = $_SERVER['DOCUMENT_ROOT'].$loc."IPINClient";
	// $sModulePath = $_SERVER['DOCUMENT_ROOT']."/shop/member/ipin/IPINClient";

	// ipin_main.php ���� ������ ���� ������ �����մϴ�.
	// ����Ÿ ������ ������ ���� Ȯ���ϱ� �����̹Ƿ�, �ʼ������� �ƴϸ� ������ ���� �ǰ�����Դϴ�.
	$sCPRequest = $_SESSION['CPREQUEST'];

	// ȸ�����Խ� ���԰�ΰ� ��������� üũ, ������� ������üũ���� ���ǿ� ������ ���� �ҷ���
	$joinGubun = $_SESSION['joinGubun'];


	// ipin_process.php ���� ���Ϲ��� ��ȣȭ �� ����� ���� ����
	$sEncData = $_POST['enc_data'];

	//////////////////////////////////////////////// ���ڿ� ����///////////////////////////////////////////////
    if(preg_match('~[^0-9a-zA-Z+/=]~', $sEncData, $match)) {echo "�Է� �� Ȯ���� �ʿ��մϴ�"; exit;}
    if(base64_encode(base64_decode($sEncData))!= $sEncData) {echo " �Է� �� Ȯ���� �ʿ��մϴ�"; exit;}
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

	if ($sEncData != "") {

		// ����� ������ ��ȣȭ �մϴ�.
		// �������� �̱�����(`) �ܿ���, 'exec(), system(), shell_exec()' ��� �ͻ� ��å�� �°� ó���Ͻñ� �ٶ��ϴ�.
		$sDecData = exec("$sModulePath $sType $sSiteCode $sSitePw $sEncData");

		if ($sDecData == -9) {
			$sRtnMsg = "�Է°� ���� : ��ȣȭ ó����, �ʿ��� �Ķ���Ͱ��� ������ ��Ȯ�ϰ� �Է��� �ֽñ� �ٶ��ϴ�.";
		} else if ($sDecData == -12) {
			$sRtnMsg = "NICE�ſ����������� �߱��� ���������� ��Ȯ���� Ȯ���� ������.";
		} else {

			// ��ȣȭ�� ����Ÿ �����ڴ� ^ �̸�, �����ڷ� ����Ÿ�� �Ľ��մϴ�.
			/*
				- ��ȣȭ�� ����Ÿ ����
				�����ֹι�ȣȮ��ó������ڵ�^�����ֹι�ȣ^����^�ߺ�Ȯ�ΰ�(DupInfo)^��������^��������^�������(YYYYMMDD)^���ܱ�������^���� ��û Sequence
			*/
			$arrData = split("\^", $sDecData);
			$iCount = count($arrData);

			if ($iCount >= 5) {

				/*
					������ ���� ����� ������ ������ �� �ֽ��ϴ�.
					����ڿ��� �����ִ� ������, '�̸�' ����Ÿ�� ���� �����մϴ�.

					����� ������ �ٸ� ���������� �̿��Ͻ� ��쿡��
					������ ���Ͽ� ��ȣȭ ����Ÿ($sEncData)�� ����Ͽ� ��ȣȭ �� �̿��Ͻǰ��� �����մϴ�. (���� �������� ���� ó�����)

					����, ��ȣȭ�� ������ ����ؾ� �ϴ� ��쿣 ����Ÿ�� ������� �ʵ��� ������ �ּ���. (����ó�� ����)
					form �±��� hidden ó���� ����Ÿ ���� ������ �����Ƿ� �������� �ʽ��ϴ�.
				*/

				$strResultCode	= $arrData[0];			// ����ڵ�
				if ($strResultCode == 1) {
					$strCPRequest	= $arrData[8];			// CP ��û��ȣ

					if ($sCPRequest == $strCPRequest) {

						$sRtnMsg = "����� ���� ����";

						$strVno      		= $arrData[1];	// �����ֹι�ȣ (13�ڸ��̸�, ���� �Ǵ� ���� ����)
						$strUserName		= $arrData[2];	// �̸�
						$strDupInfo			= $arrData[3];	// �ߺ����� Ȯ�ΰ� (64Byte ������)
						$strAgeInfo			= $arrData[4];	// ���ɴ� �ڵ� (���� ���̵� ����)
						$strGender			= $arrData[5];	// ���� �ڵ� ()
						$strBirthDate		= $arrData[6];	// ������� (YYYYMMDD)
						$strNationalInfo	= $arrData[7];	// ��/�ܱ��� ���� (0:������; 1:�ܱ���)

					} else {
						$sRtnMsg = "CP ��û��ȣ ����ġ : ���ǿ� ���� $sCPRequest ����Ÿ�� Ȯ���� �ֽñ� �ٶ��ϴ�.";
					}
				} else {
					$sRtnMsg = "���ϰ� Ȯ�� ��, NICE�ſ������� ���� ����ڿ��� ������ �ּ���. [$strResultCode]";
				}

			} else {
				$sRtnMsg = "���ϰ� Ȯ�� ��, NICE�ſ������� ���� ����ڿ��� ������ �ּ���.";
			}

		}
	} else {
		$sRtnMsg = "ó���� ��ȣȭ ����Ÿ�� �����ϴ�.";
	}

	list($chkCount) = $db->fetch("SELECT count(m_id) FROM ".GD_MEMBER." WHERE dupeinfo = '$strDupInfo'");
	if($chkCount < 1){
		$dormant = Core::loader('dormant');
		$chkCount = $dormant->getCountDupeinfoFromDormant($strDupInfo);
	}

	$sess_OrderNo = "";
	session_register("sess_OrderNo");

	$ssCallType = $_SESSION["sess_callType"];

	if($strAgeInfo >= 6 ) {
		$_SESSION['adult'] = 1;
	}
	else {
		unset($_SESSION['adult']);
	}

	// ������������ ��14�� �̸� ȸ������ ������
	$mUnder14 = Core::loader('memberUnder14Join');
	$under14Code = $mUnder14->joinSelfCert($strBirthDate);

	if($ssCallType == "adultcheck" || $ssCallType == "adultcheckmobile") {
		if($_SESSION['adult']) {
			msg("���� �����Ǿ����ϴ�.");
			if($sess){
				setAdultAuthDate($session->m_id);
			}
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "http://".$_SERVER['HTTP_HOST']."/m/";
			} else {
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "../../index.php";
			}
			go($returnUrl, "parent");
		}
		else {
			unset($_SESSION['adult']);
			msg("���� ������ �����߽��ϴ�.");
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "http://".$_SERVER['HTTP_HOST']."/m/";
			} else {
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "../../index.php";
			}
			go($returnUrl, "parent");
		}
		exit();
	}
?>
<html>
<? if($strResultCode == 1 && $sCPRequest == $strCPRequest) { ?>
<script language="JavaScript">
	var _joinGubun = "<? echo $joinGubun?>";
	function loadAction() {
		var strRetCd = "<?=$strResultCode?>";		// '1' �̾�� �Ѵ�.
		var strMsg = "<?=$sRtnMsg?>";

		var strName = "<?=$strUserName?>";					// �̸�
		var birthday = "<?=$strBirthDate?>";				// �������
		var sex = "<?=($strGender) ? 'M' : 'W'?>";			// ����
		var dupeInfo = "<?=$strDupInfo?>";					// ������ �ߺ� ��ȸ �ڵ�
		var ageInfo = "<?=$strAgeInfo?>";					// ������ �ߺ� ��ȸ �ڵ�
		var foreigner = "<?=$strNationalInfo?>";			// ������/�ܱ��� ����
		var dupeCount = "<?=$chkCount?>";					// ���Ե� Ƚ��
		var nice_minoryn = "<?=$ipin['nice_minoryn']?>";	// ��������
		var year = "<?=substr($strBirthDate, 0, 4)?>";
		var under14Code = "<?=$under14Code?>";				// ��14�� �̸� ȸ������ �������ڵ�

		// ȣ�������� ã�´�.
		var callType = "<?=$ssCallType?>";

		// ���̵� ã�⿡�� ȣ���� ���, parent �� act ������Ʈ�� �ִ�.
		if (callType == "findid" || callType == "findpwd") {
			parent.document.fm.action = '';
			parent.document.fm.target = '';
			parent.document.fm.rncheck.value = 'ipin';
			parent.document.fm.dupeinfo.value = dupeInfo;
			parent.document.fm.submit();
		}
		else {
			// default ȸ������
			if (dupeCount > 0) {
			alert( "�̹� ������ �Ǿ� �ֽ��ϴ�.");
			}
			else {
				if ( nice_minoryn == 'y' && ageInfo < "6" ) { // �Ǹ��������� & ������������
					parent.document.frmAgree['name']. value = '';
					alert( '�������� ����' ); //��� �޽��� ���
				}
				else if ( under14Code == 'rejectJoin' ) { // ��14�� �̸� ȸ������ �ź�
					parent.document.frmAgree['name']. value = '';
					alert( '�� 14�� �̸��� ��� ȸ�������� ������� �ʽ��ϴ�.' ); //��� �޽��� ���
				}
				else if ( under14Code == 'adminStatus' && confirm('��14�� �̸� ȸ���� ��� ������ ���� �� ������ �Ϸ�˴ϴ�.\n��� �����Ͻðڽ��ϱ�?') === false ) {
					// ��14�� �̸� ȸ������ ������ ���� �� ����
					parent.document.frmAgree['name']. value = '';
				}
				else if ( strRetCd == "1" ) // ��������������
				{
					alert( "������������ ����ó�� �Ǿ����ϴ�." ); //��� �޽��� ���
					parent.document.frmAgree.action = '';
					parent.document.frmAgree.target = '';
					parent.document.frmAgree.rncheck.value = 'ipin';
					parent.document.frmAgree.nice_nm.value = strName;
					parent.document.frmAgree.birthday.value = birthday;
					parent.document.frmAgree.sex.value = sex;
					parent.document.frmAgree.dupeinfo.value = dupeInfo;
					parent.document.frmAgree.foreigner.value = foreigner;
					parent.document.frmAgree.submit();
				}
				else // �Ǹ���������
				{
				//	����� ���� �Ǹ�Ƚ����ܰ� ���ǵ��� ������ ó���Ѵ�.
					parent.document.frmAgree['name']. value = '';
					alert( '������������ �����߽��ϴ�.\n\n' + strMsg); //��� �޽��� ���
				}
			}
		}
		if (_joinGubun == 'mobile'){
			if (typeof(parent.frmMaskRemove) != 'undefined') parent.frmMaskRemove('popupCertKey');
			else self.close();
		}
		else self.close();
	}
</script>
<? } ?>
	<body onload="javascript:loadAction();"></body>
</html>
