<?php

	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	//#######################################################################################
	//#####
	//#####	���νǸ�Ȯ�� ���� �ҽ� (�Ǹ�Ȯ�ο�û)						�ѱ��ſ�����(��)
	//#####	( PHPScript ó�� )
	//#####
	//#####	================================================================================
	//#####
	//#####	* �� �������� �ͻ��� ������ �����ؼ� �����Ͻʽÿ�.
	//#####	  �������� �����ϰų� �������� ���ʽÿ�. (���� ����� ������ �˴ϴ�)
	//#####
	//#######################################################################################

	/****************************************************************************************
	 *****	�� ȸ���� ID ���� : ���ÿ� �߱޵� ȸ���� ID�� �����Ͻʽÿ�. ��
	 ****************************************************************************************/

	$strNiceId = $realname[id];


	/****************************************************************************************
	 *****	��  NiceCheck.htm ���� �Ѱ� ���� SendInfo ���� ��ȣȭ �Ͽ�
	 *****		�ֹι�ȣ,���� �� ������ ���� �����Ѵ� ��
	 ****************************************************************************************/
	$oivsObject->clientData = $_POST['SendInfo'];
	$oivsObject->desClientData();

	// ��ȣȭ �� ���� �Ʒ� �ּ��� Ǯ�� Ȯ�� �����մϴ�.
	// (���� ȸ�� üũ�� �� �κп��� �Ͻø� �˴ϴ�.)
	/*
	echo "<BR>���� : " . $oivsObject->userNm ;
	echo "<BR>�ֹι�ȣ/�ܱ��ι�ȣ : " . $oivsObject->resIdNo ;
	echo "<BR>��ȸ�����ڵ� : " . $oivsObject->inqRsn ;
	echo "<BR>��/�ܱ��� �����ڵ� : " . $oivsObject->foreigner ;
	*/

	/****************************************************************************************
	 *****	�� �Ǹ�Ȯ�� ���񽺸� ȣ���Ѵ�. ��
	 ****************************************************************************************/

	$oivsObject->niceId = $strNiceId;
	$oivsObject->callService();

	/****************************************************************************************
	 *****	�� �Ǹ�Ȯ�� ���񽺸� ���䰪�� ó���Ѵ�. ��

	 *****	strRetCd �� strRetDtlCd�� �̿��Ͽ� �۾� �Ͻø� �˴ϴ�.
	 *****	��! strRetDtlCd �� Y,C�� ���� ������ ������ ���� �Ǹ�Ȯ���� ���Ƴ��� �����̹Ƿ�
	 *****	���ý�ũ��Ʈ�� �������� ���ñ� �ٶ��ϴ�.
	 ****************************************************************************************/

	//==================================================================================================================
	//				���信 ���� ��� �� �����鿡 ���� ����
	//------------------------------------------------------------------------------------------------------------------
	//
	//	< �ѱ��ſ����� �¶��� �ĺ� ���񽺿��� �����ϴ� ���� >
	//
	//	oivsObject->message			: ���� �Ǵ� ������ �޽���
	//	oivsObject->retCd			: ��� �ڵ�(�޴��� ����) // cf. �ѱ��ſ����� ���� ��� �� ���� ������ : https://www.nuguya.com
	//	oivsObject->retDtlCd			: ��� �� �ڵ�(�޴��� ����)
	//	oivsObject->minor 			: �������� ��� �ڵ�
	//									"1"	: ����
	//									"2"	: �̼���
	//									"9"	: Ȯ�� �Ұ�
	//
	//=================================================================================================================
$Protocol = $_SERVER['HTTPS']=='on'?'https://':'http://';

// �������� ó��
if ($_POST['mode']=="adult_guest") {

	if (($oivsObject->retCd == 1) && ($oivsObject->minor == 1)) {

		$_SESSION['adult'] = 1;

		msg("���� �����Ǿ����ϴ�.");

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];
		go($_POST['returnUrl']);

	}
	else {

		msg("���� ������ �����߽��ϴ�.");

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];
		go($_POST['returnUrl']);
	}
	exit;
}
?>

<html>
	<head>
		<title>�ѱ��ſ������ֽ�ȸ�� ���νǸ�Ȯ�� ���� ������</Title>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

		<!--	==========================================================	-->
		<!--	�ѱ��ſ������ֽ�ȸ�� ó�� ��� (���� �� �������� ���ʽÿ�)	-->
		<!--	==========================================================	-->
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.msg.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>
	</head>

	<script type="text/javascript">

		function loadAction()
		{
			var strRetCd = "<? echo $oivsObject->retCd; ?>";
			var strRetDtlCd = "<? echo $oivsObject->retDtlCd; ?>";
			var strMsg = "<? echo $oivsObject->message; ?>";
			var strMinor = "<? echo $oivsObject->minor; ?>";
			var minoryn = "<? echo $realname[minoryn]; ?>";
			var userdi = "<?=$oivsObject->dupeinfo?>";

			//	�ѱ��ſ������� ���� ����ڵ忡 �ش��ϴ� �޽����� �޾ƿ´�.
			//	(�ٸ� �޽����� ������ �޴��� ������ �����Ͽ�  strRetCd, strRetDtlCd �� �޽����� ������ �ش�.
			strProcessMessage = getMessage( strRetCd, strRetDtlCd );

			if ( minoryn == 'y' && strRetCd == "1" && strMinor != '1' ){ // �Ǹ��������� & ������������
				parent.document.frmAgree['name']. value = '';
				parent.document.frmAgree['resno[]'][0]. value = '';
				parent.document.frmAgree['resno[]'][1]. value = '';
				alert( '�������� ����' ); //��� �޽��� ���
			}
			else if ( strRetCd == "1" ) // �Ǹ���������
			{
				alert( strProcessMessage ); //��� �޽��� ���
				parent.document.frmAgree.action = '';
				parent.document.frmAgree.target = '';
				if(parent.document.frmAgree.rncheck)parent.document.frmAgree.rncheck.value = 'realname';
				if(parent.document.frmAgree.dupeinfo)parent.document.frmAgree.dupeinfo.value = userdi;
				parent.document.frmAgree.submit();
			}
			else // �Ǹ���������
			{
			//	����� ���� �Ǹ�Ƚ����ܰ� ���ǵ��� ������ ó���Ѵ�.
				parent.document.frmAgree['name']. value = '';
				parent.document.frmAgree['resno[]'][0]. value = '';
				parent.document.frmAgree['resno[]'][1]. value = '';
				if ( strRetDtlCd == "Y" )
				{
					//	ó�� ����� �Ǹ�Ƚ����� ���������� Ȯ���Ѵ�.
					var retVal = confirm( strProcessMessage + "\n\n" + getCheckMessage( "S31" ) );
					if ( retVal == true )
					{
						goSafeBlockExpt();
						return;
					}
					else
					{
						return;
					}
				}
				else if ( strRetDtlCd == "C" )
				{
					//	ó�� ����� �Ǹ���ǵ������� ���������� Ȯ���Ѵ�.
					alert( strProcessMessage + "\n\n" + getCheckMessage( "S32" ) );
				}
				else
				{
					alert( strProcessMessage ); //��� �޽��� ���
				}
			}
		}

	</script>

	<body onload="javascript:loadAction();"></body>
</html>