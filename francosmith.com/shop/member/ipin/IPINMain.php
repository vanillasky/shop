<?php
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	$sSiteCode	= $ipin['code'];			// IPIN ���� ����Ʈ �ڵ�		(NICE�ſ����������� �߱��� ����Ʈ�ڵ�)
	$sSitePw	= $ipin['password'];			// IPIN ���� ����Ʈ �н�����	(NICE�ſ����������� �߱��� ����Ʈ�н�����)
	$sEncData	= "";			// ��ȣȭ �� ����Ÿ
	$sRtnMsg	= "";			// ó����� �޼���
	
	
	
	/*
	�� sType ������ ���� ����  ����������������������������������������������������������������������������������������������������������
		����Ÿ�� �����ϱ� ���� ���а�.
		
		SEQ : ��û��ȣ ����
		REQ : ��û ����Ÿ ��ȣȭ
		RES : ��û ����Ÿ ��ȣȭ
	������������������������������������������������������������������������������������������������������������������������������������������
	*/
	$sType						= "";
	
	
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
	
	/*
	�� sReturnURL ������ ���� ����  ����������������������������������������������������������������������������������������������������������
		NICE�ſ������� �˾����� �������� ����� ������ ��ȣȭ�Ͽ� �ͻ�� �����մϴ�.
		���� ��ȣȭ�� ��� ����Ÿ�� ���Ϲ����� URL ������ �ּ���.
		
		* URL �� http ���� �Է��� �ּž��ϸ�, �ܺο����� ������ ��ȿ�� �������� �մϴ�.
		* ��翡�� �����ص帰 ���������� ��, ipin_process.jsp �������� ����� ������ ���Ϲ޴� ���� �������Դϴ�.
		
		�Ʒ��� URL �����̸�, �ͻ��� ���� �����ΰ� ������ ���ε� �� ���������� ��ġ�� ���� ��θ� �����Ͻñ� �ٶ��ϴ�.
		�� - http://www.test.co.kr/ipin_process.jsp, https://www.test.co.kr/ipin_process.jsp, https://test.co.kr/ipin_process.jsp
	������������������������������������������������������������������������������������������������������������������������������������������
	*/
	if($_SERVER['SERVER_PORT'] == 80) {
		$Port = "";
	} elseif($_SERVER['SERVER_PORT'] == 443) {
		$Port = "";
	} else {
		$Port = $_SERVER['SERVER_PORT'];
	}
	if (strlen($Port) > 0) $Port = ":".$Port;
	$Protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

	$host = parse_url($_SERVER['HTTP_HOST']);
	if ($host['path']) {
		$Host = $host['path'];
	} else {
		$Host = $host['host'];
	}

	$sReturnURL = $Protocol.$Host.$Port.$loc."IPINProcess.php";
	if($_GET['callType'] == 'applyipin') $sReturnURL = $Protocol.$Host.$Port.$loc."IPINApply.php";
	
	
	/*
	�� sCPRequest ������ ���� ����  ����������������������������������������������������������������������������������������������������������
		[CP ��û��ȣ]�� �ͻ翡�� ����Ÿ�� ���Ƿ� �����ϰų�, ��翡�� ������ ���� ����Ÿ�� ������ �� �ֽ��ϴ�. (�ִ� 30byte ������ ����)
		
		CP ��û��ȣ�� ���� �Ϸ� ��, ��ȣȭ�� ��� ����Ÿ�� �Բ� �����Ǹ�
		����Ÿ ������ ���� �� Ư�� ����ڰ� ��û�� ������ Ȯ���ϱ� ���� �������� �̿��Ͻ� �� �ֽ��ϴ�.
		
		���� �ͻ��� ���μ����� �����Ͽ� �̿��� �� �ִ� ����Ÿ�̱⿡, �ʼ����� �ƴմϴ�.
	������������������������������������������������������������������������������������������������������������������������������������������
	*/
	$sCPRequest					= "";
	
	
	
	
	
	$sType		= "SEQ";			// CP ��û��ȣ ���а�
	
	// �ռ� ����帰 �ٿͰ���, CP ��û��ȣ�� ������ ����� ���� �Ʒ��� ���� ������ �� �ֽ��ϴ�.
	// �������� �̱�����(`) �ܿ���, 'exec(), system(), shell_exec()' ��� �ͻ� ��å�� �°� ó���Ͻñ� �ٶ��ϴ�.
	$sCPRequest = exec("$sModulePath $sType $sSiteCode");
	
	
	// CP ��û��ȣ�� ���ǿ� �����մϴ�.
	// ���� ������ ������ ������ ipin_result.php ���������� ����Ÿ ������ ������ ���� Ȯ���ϱ� �����Դϴ�.
	// �ʼ������� �ƴϸ�, ������ ���� �ǰ�����Դϴ�.
    $_SESSION['CPREQUEST'] = $sCPRequest;
    
    
    
    $sType		= "REQ";			// ����Ÿ ��ȣȭ ���а�
    
    // ���� ������� ����, ���μ��� ���࿩�θ� �ľ��մϴ�.
    // �������� �̱�����(`) �ܿ���, 'exec(), system(), shell_exec()' ��� �ͻ� ��å�� �°� ó���Ͻñ� �ٶ��ϴ�.
    $sEncData	= exec("$sModulePath $sType $sSiteCode $sSitePw $sCPRequest $sReturnURL");
    
    // ���� ������� ���� ó������
    if ($sEncData == -9)
    {
    	$sRtnMsg = "�Է°� ���� : ��ȣȭ ó����, �ʿ��� �Ķ���Ͱ��� ������ ��Ȯ�ϰ� �Է��� �ֽñ� �ٶ��ϴ�.";
    } else {
    	$sRtnMsg = "$sEncData ������ ��ȣȭ ����Ÿ�� Ȯ�εǸ� ����, ������ �ƴ� ��� �����ڵ� Ȯ�� �� NICE�ſ������� ���� ����ڿ��� ������ �ּ���.";
    }

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

	// returnUrl ����
	if ($_REQUEST['returnUrl'] != '') {
		$returnUrl = $_REQUEST['returnUrl'];
	}
	else {
		parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $output);
		$returnUrl = $output['returnUrl'];
	}
?>

<html>
<head>
	<title>NICE�ſ������� �����ֹι�ȣ ����</title>
	
	<script language='javascript'>
	window.name ="Parent_window";
	
	function fnPopup(){
		document.form_ipin.target = "popupCertKey";
		document.form_ipin.action = "https://cert.vno.co.kr/ipin.cb";
		document.form_ipin.submit();
	}
	</script>
</head>

<body onload="fnPopup();">

<!-- �����ֹι�ȣ ���� �˾��� ȣ���ϱ� ���ؼ��� ������ ���� form�� �ʿ��մϴ�. -->
<form name="form_ipin" method="post">
	<input type="hidden" name="m" value="pubmain">						<!-- �ʼ� ����Ÿ��, �����Ͻø� �ȵ˴ϴ�. -->
    <input type="hidden" name="enc_data" value="<?= $sEncData ?>">		<!-- ������ ��ü������ ��ȣȭ �� ����Ÿ�Դϴ�. -->
    
    <!-- ��ü���� ����ޱ� ���ϴ� ����Ÿ�� �����ϱ� ���� ����� �� ������, ������� ����� �ش� ���� �״�� �۽��մϴ�.
    	 �ش� �Ķ���ʹ� �߰��Ͻ� �� �����ϴ�. -->
    <input type="hidden" name="param_r1" value="<?=urlencode($returnUrl)?>">
    <input type="hidden" name="param_r2" value="">
    <input type="hidden" name="param_r3" value="">
</form>

<!-- �����ֹι�ȣ ���� �˾� ���������� ����ڰ� ������ ������ ��ȣȭ�� ����� ������ �ش� �˾�â���� �ްԵ˴ϴ�.
	 ���� �θ� �������� �̵��ϱ� ���ؼ��� ������ ���� form�� �ʿ��մϴ�. -->
<form name="vnoform" method="post">
	<input type="hidden" name="enc_data">								<!-- �������� ����� ���� ��ȣȭ ����Ÿ�Դϴ�. -->
	
	<!-- ��ü���� ����ޱ� ���ϴ� ����Ÿ�� �����ϱ� ���� ����� �� ������, ������� ����� �ش� ���� �״�� �۽��մϴ�.
    	 �ش� �Ķ���ʹ� �߰��Ͻ� �� �����ϴ�. -->
    <input type="hidden" name="param_r1" value="<?=urlencode($returnUrl)?>">
    <input type="hidden" name="param_r2" value="">
    <input type="hidden" name="param_r3" value="">
</form>

</body>
</html>
