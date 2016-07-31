<?php
	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );

	//#######################################################################################
	//#####
	//#####	���̽�������(��ü����Ű) ���� ���� ������ �ҽ�				�ѱ��ſ�����(��)
	//#####
	//#####	================================================================================
	//#####
	//#####	* �� �������� �ͻ��� ȭ�鿡 �°� �����Ͻʽÿ�.
	//#####	  ��, Head ������ ������ Javascript�� �����ϰų� �����Ͻø� ����� �� �����ϴ�.
	//#####
	//#######################################################################################


	//========================================================================================
	//=====	�� ȸ���� ID, ����Ʈ�ĺ����� ���� : ���ÿ� �߱޵� ȸ���� ID�� �����Ͻʽÿ�. ��
	//========================================================================================

	$NiceId = $ipin[id];
	$SIKey = $ipin[SIKey];

	//========================================================================================
	//=====	�� ��ȯ ����� ������ URL�� �����Ͻʽÿ�. (��, �������� �״�� ����Ͻʽÿ�)
	//=====	   �ѽ��� ���񽺿� ���޵Ǿ� ���ǹǷ� �ݵ�� ���� URL ��θ� �����ϼž� �մϴ�.
	//========================================================================================

	//EX) http://�ͻ��ǵ�����/NiceCheckPopup.php
	$self_filename = basename($_SERVER[PHP_SELF]);
	$loc = strpos($_SERVER[PHP_SELF], $self_filename);
	$sub_path = substr($_SERVER[PHP_SELF], 0, $loc);

	if($_SERVER['SERVER_PORT'] == 80) {
		$Port = "";
	} elseif($_SERVER['SERVER_PORT'] == 443) {
		$Port = "";
	} else {
		$Port = $_SERVER['SERVER_PORT'];
	}

	if (strlen($Port)>0) $Port = ":".$Port;
	$Protocol = $_SERVER['HTTPS']=='on'?'https://':'http://';

	$host = parse_url($_SERVER['HTTP_HOST']);
	if ($host['path']) {
		$Host = $host['path'];
	} else {
		$Host = $host['host'];
	}

	$ReturnURL = $Protocol.$Host.$Port.$sub_path."NiceCheckCallback.php";

	if($_GET['callType'] == 'applyipin') $ReturnURL = $Protocol.$_SERVER['HTTP_HOST'].$Port.$sub_path."IPINApply.php";
	$strOrderNo = date("Ymd") . rand(100000000000,999999999999); //�ֹ���ȣ 20�ڸ� .. �� ��û���� �ߺ����� �ʵ��� ����

	// ��ŷ������ ���� ��û���� ���ǿ� ����
	//session_start();		//library���� ��.
	$sess_OrderNo = $strOrderNo;
	session_register("sess_OrderNo");
	$_SESSION['sess_OrderNo'] = $strOrderNo;
	session_register("sess_callType");
	$_SESSION['sess_callType'] = $_GET['callType'];
	
	// ȸ�����Խ� ���԰�ΰ� ��������� üũ, ������� ������üũ���� GET���� ������
	session_register("joinGubun");
	$_SESSION['joinGubun'] = $_GET['joinGubun'];

	// �� �����ɿ����� �߰� ������ ���� �� �� �����Ƿ�, ������ �̿��Ѵ�.
	if ($_GET['returnUrl'] != '') {
		$_SESSION['ipin_requrlUrl'] = $_GET['returnUrl'];
	}
	else {
		parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $output);
		$_SESSION['ipin_requrlUrl'] = $output['returnUrl'];
	}
?>
<html>
	<head>
		<title>�ѱ��ſ������ֽ�ȸ�� ��������Ű(��ü����Ű) ���� ���� ������</Title>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

		<!--	==========================================================	-->
		<!--	�ѱ��ſ������ֽ�ȸ�� ó�� ��� (���� �� �������� ���ʽÿ�)	-->
		<!--	==========================================================	-->
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.msg.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>

		<LINK href="./nice.nuguya.oivs.css" type=text/css rel=stylesheet>
	</head>

	<script language="javascript">

		document.onkeydown = onKeyDown;

		function onKeyDown( event )
		{
			var e = event;
			if ( event == null ) e = window.event;

			if ( e.keyCode == 13 ) goIDCheck();
		}

		function loadAction()
		{
			if ( document.getElementById( "PingInfo" ).value == "" )
			{
				alert( "�ѱ��ſ�����(��)�� ��������Ű ���񽺰� �������Դϴ�.\n����� �ٽ� �õ��Ͻñ� �ٶ��ϴ�.\n\n���°� ��ӵǸ� ����Ʈ�����ڿ��� �����Ͻʽÿ�" );
				return;
			}
			goIDCheck();
		}

		function validate()
		{
			var NiceId		= document.getElementById( "NiceId" );
			var PingInfo	= document.getElementById( "PingInfo" );
			var ReturnURL	= document.getElementById( "ReturnURL" );

			if ( NiceId.value == "" )
			{
				alert( getCheckMessage( "S60" ) );
				NiceId.focus();
				return false;
			}

			if ( PingInfo.value == "" )
			{
				alert( getCheckMessage( "S61" ) );
				return false;
			}

			if ( ReturnURL.value == "" )
			{
				alert( getCheckMessage( "S64" ) );
				ReturnURL.focus();
				return false;
			}

			return true;
		}

		function goIDCheck()
		{
			if ( validate() == true )
			{
				var strNiceId 	= document.getElementById( "NiceId" ).value;
				var strPingInfo	= document.getElementById( "PingInfo" ).value;
				var strOrderNo	= document.getElementById( "OrderNo" ).value;
				var strInqRsn	= document.getElementById( "InqRsn" ).value;
				var strReturnUrl	= document.getElementById( "ReturnURL" ).value;
				var strSIKey 	= document.getElementById( "SIKey" ).value;

				document.reqForm.SendInfo.value = makeCertKeyInfoPA( strNiceId, strPingInfo, strOrderNo, strInqRsn, strReturnUrl, strSIKey );
//				document.reqForm.SendInfo.value = makeCertKeyInfo( strNiceId, strPingInfo, strOrderNo, strInqRsn, strReturnUrl );
				document.reqForm.ProcessType.value = strPersonalCertKey;

				//var popupWindow = window.open( "", "popupCertKey", "top=100, left=200, status=0, width=417, height=490" );
				document.reqForm.target = "popupCertKey";
				document.reqForm.action = strCertKeyServiceUrl;
				document.reqForm.submit();
				//popupWindow.focus();
			}

			return;
		}

	</script>

	<BODY onLoad="javascript:loadAction();" >
		<FORM id="reqForm" name="reqForm" method="POST" action="">
			<input class="small" type="hidden" id="SendInfo" name="SendInfo" >
			<input class="small" type="hidden" id="ProcessType" name="ProcessType" >
		</FORM>
		<FORM id="pageForm" name="pageForm" method="POST" action="">
	  		<INPUT type="hidden" id="NiceId" name="NiceId" value="<? echo $NiceId ; ?>">
	  		<INPUT type="hidden" id="SIKey" name="SIKey" value="<? echo $SIKey ; ?>">
			<INPUT type="hidden" id="PingInfo" name="PingInfo" value="<? echo getPingInfo(); ?>">
	  		<INPUT type="hidden" id="ReturnURL" name="ReturnURL" value="<? echo $ReturnURL ; ?>" >
			<!--	��ȸ������ �����Ͻʽÿ� ( '10'-ȸ������, '20'-����ȸ�� Ȯ��, '30'-��������, '40'-��ȸ�� Ȯ��, '50'-��Ÿ ���� )	-->
			<input type="hidden" id="InqRsn" name="InqRsn" value="10">
			<!--	�ֹ���ȣ�� �����Ͻʽÿ�. (�ּ� 14�ڸ�, 20�ڸ��̸�)	-->
			<input type="hidden" id="OrderNo" name="OrderNo" value="<? echo $strOrderNo; ?>">
		</form>
	</BODY>

</html>
