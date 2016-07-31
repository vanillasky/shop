<?php
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	/********************************************************************************************************************************************
		NICE�ſ������� Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
		
		���񽺸� : �����ֹι�ȣ���� (IPIN) ����
		�������� : �����ֹι�ȣ���� (IPIN) ����� ���� ���� ó�� ������
		
				   ���Ź��� ������(�������)�� ����ȭ������ �ǵ����ְ�, close�� �ϴ� ��Ȱ�� �մϴ�.
	*********************************************************************************************************************************************/
	
	// ����� ���� �� CP ��û��ȣ�� ��ȣȭ�� ����Ÿ�Դϴ�. (ipin_main.php ���������� ��ȣȭ�� ����Ÿ�ʹ� �ٸ��ϴ�.)
	$sResponseData = $_POST['enc_data'];
	
	// ipin_main.php ���������� ������ ����Ÿ�� �ִٸ�, �Ʒ��� ���� Ȯ�ΰ����մϴ�.
	$sReservedParam1  = $_POST['param_r1'];
	$sReservedParam2  = $_POST['param_r2'];
	$sReservedParam3  = $_POST['param_r3'];
	
	// ȸ�����Խ� ���԰�ΰ� ��������� üũ, ������� ������üũ���� ���ǿ� ������ ���� �ҷ���
	$joinGubun = $_SESSION['joinGubun'];
	
	//////////////////////////////////////////////// ���ڿ� ����///////////////////////////////////////////////
    if(preg_match('~[^0-9a-zA-Z+/=]~', $sResponseData, $match)) {echo "�Է� �� Ȯ���� �ʿ��մϴ�"; exit;}
	if(base64_encode(base64_decode($sResponseData))!= $sResponseData) {echo " �Է� �� Ȯ���� �ʿ��մϴ�"; exit;}	
    if(preg_match("/[#\&\\+\-@=\/\\\:;,\\'\"\^`~\|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam1, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam2, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam3, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// ��ȣȭ�� ����� ������ �����ϴ� ���
	if ($sResponseData != "") {

?>

<html>
<head>
	<title>NICE�ſ������� �����ֹι�ȣ ����</title>
	<script language='javascript'>
		var _joinGubun="<? echo $joinGubun?>";
		function fnLoad()
		{
			var _joinGubunHeader = (_joinGubun == "mobile") ? parent : opener;
			// ��翡���� �ֻ����� �����ϱ� ���� 'parent.opener.parent.document.'�� �����Ͽ����ϴ�.
			// ���� �ͻ翡 ���μ����� �°� �����Ͻñ� �ٶ��ϴ�.
			_joinGubunHeader.document.getElementById('ifrmRnCheck').contentWindow.document.vnoform.enc_data.value = "<?= $sResponseData ?>";
			
			_joinGubunHeader.document.getElementById('ifrmRnCheck').contentWindow.document.vnoform.param_r1.value = "<?= $sReservedParam1 ?>";
			_joinGubunHeader.document.getElementById('ifrmRnCheck').contentWindow.document.vnoform.param_r2.value = "<?= $sReservedParam2 ?>";
			_joinGubunHeader.document.getElementById('ifrmRnCheck').contentWindow.document.vnoform.param_r3.value = "<?= $sReservedParam3 ?>";
			
			_joinGubunHeader.document.getElementById('ifrmRnCheck').contentWindow.document.vnoform.target = "Parent_window";
			
			// ���� �Ϸ�ÿ� ��������� �����ϰ� �Ǵ� �ͻ� Ŭ���̾�Ʈ ��� ������ URL
			_joinGubunHeader.document.getElementById('ifrmRnCheck').contentWindow.document.vnoform.action = "IPINResult.php";
			_joinGubunHeader.document.getElementById('ifrmRnCheck').contentWindow.document.vnoform.submit();
			
			if (_joinGubun == "mobile"){
				if (typeof(parent.frmMaskRemove) != 'undefined') parent.frmMaskRemove('popupCertKey');
				else self.close();
			}
			else self.close();
		}
	</script>
</head>
<body onLoad="fnLoad()">

<?
	} else {
?>

<html>
<head>
	<title>NICE�ſ������� �����ֹι�ȣ ����</title>
	<body onLoad="self.close()">

<?
	}
?>

</body>
</html>