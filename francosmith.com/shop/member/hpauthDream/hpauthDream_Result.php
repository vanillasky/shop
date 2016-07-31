<?php
include dirname(__FILE__)."/../../lib/library.php";

$hpauth = Core::loader('Hpauth');
$hpauthServiceConfig = $hpauth->loadCurrentServiceConfig();

/* �帲��ť��Ƽ ******************************************************************************************/

$sDevelopedData = $_REQUEST['sDevelopedData'];

$rtn = split("\\$", $sDevelopedData);

			$ci = $rtn[0];
			$di = $rtn[1];
			$phoneNo = $rtn[2];
			$phoneCorp = $rtn[3];
			$birthDay = $rtn[4];  //������� ��)860406
			$gender = $rtn[5];
			$nation = $rtn[6];
			$name = $rtn[7];
			$reqNum = $rtn[8];
			$reqdate = $rtn[9];


$ibirth		= $_REQUEST["ibirth"];		//������� ��)19860406
$clntReqNum	= $_REQUEST["clntReqNum"];	//��û��ȣ : $reqNum���� ���ƾ� �������� (����������)
$result		= $_REQUEST["result"];		//������, success
$resultCd	= $_REQUEST["resultCd"];	//������, ��������

$_SESSION["sess_callType"]	= $_REQUEST["ssCallType"];	//�з���

/* �帲��ť��Ƽ ******************************************************************************************/

if ($gender == 1 || $gender == 3) { //�����Ǻ�
	$gender = "M";
} else if ($gender == 2 || $gender == 4){
	$gender = "W";
} else {
	$gender = 0;
}

//��20�� ���� ����
$birthyear = substr($ibirth,0,4); //����
$thisyear = date("Y", time()); //����

$age = $thisyear - $birthyear; //����

$ssCallType = $_SESSION["sess_callType"];

if($age >= 19) {
	$_SESSION['adult'] = 1;
}
else {
	unset($_SESSION['adult']);
}

// ������������ ��14�� �̸� ȸ������ ������
$mUnder14 = Core::loader('memberUnder14Join');
$under14Code = $mUnder14->joinSelfCert($ibirth);

if($ssCallType == "adultcheck" || $ssCallType == "adultcheckmobile") {//��������
	if ($hpauthServiceConfig['serviceCode'] === 'mcerti' && $result === 'error' && $resultCd == '06') {
		msg("������ȣ �Է½ð��� ����Ǿ����ϴ�.");
		exit;
	}
	if($_SESSION['adult']) {
		msg("���� �����Ǿ����ϴ�.");
		if($sess){
			setAdultAuthDate($session->m_id);
		}
		if($ssCallType == "adultcheckmobile"){
			$returnUrl = $_REQUEST['returnUrl'] ? urldecode($_REQUEST['returnUrl']) : "http://".$_SERVER['HTTP_HOST']."/m/";
			echo "
				<script>
				var adultTargetWindow = (opener) ? opener : parent;
				adultTargetWindow.location.replace('$returnUrl');
				if(opener) window.close();
				</script>
			";
		} else {
			$returnUrl = $_REQUEST['returnUrl'] ? urldecode($_REQUEST['returnUrl']) : "../../index.php";
			go($returnUrl, "parent");
		}
	} else {
		unset($_SESSION['adult']);
		msg("���� ������ �����߽��ϴ�.");
		if($ssCallType == "adultcheckmobile"){
			$returnUrl = $_REQUEST['returnUrl'] ? urldecode($_REQUEST['returnUrl']) : "http://".$_SERVER['HTTP_HOST']."/m/";
			echo "
				<script>
				var adultTargetWindow = (opener) ? opener : parent;
				adultTargetWindow.location.replace('$returnUrl');
				if(opener) window.close();
				</script>
			";
		} else {
			$returnUrl = $_REQUEST['returnUrl'] ? urldecode($_REQUEST['returnUrl']) : "../../index.php";
			go($returnUrl, "parent");
		}
	}
	exit();
}

list($chkCount) = $db->fetch($db->query("SELECT count(m_id) FROM ".GD_MEMBER." WHERE dupeinfo != '' and dupeinfo is not null and dupeinfo = '$di'")); // ���Ե� Ƚ��
if($chkCount < 1){
	$dormant = Core::loader('dormant');
	$chkCount = $dormant->getCountDupeinfoFromDormant($di);
}

?>

<html>
<? if($clntReqNum == $reqNum) { // ��û�� �ѱ� ��û��ȣ(clntReqNum)�� ����� ���Ե� ��û��ȣ(reqNum)�� ��ġ�ؾ� ��������  ?>
<script type="text/javascript">
	function loadAction() {
		var strResult   = "<?=$result?>";		// ����ڵ�
		var di			= "<?=$di?>";			// �ߺ�����Ȯ������(DI)
		var reqNum		= "<?=$reqNum?>";		// ��û��ȣ
		var reqdate		= "<?=$reqdate?>";		// ��û�Ͻ�

		var strName		= "<?=$name?>";			// �̸�
		var birthday	= "<?=$ibirth?>";		// �������
		var phoneNo		= "<?=$phoneNo?>";		// �޴�����ȣ
		var phoneCorp	= "<?=$phoneCorp?>";	// �̵���Ż�
		var foreigner	= "<?=$nation?>";		// ���ܱ�������
		var sex			= "<?=$gender?>";		// ����
		var ageInfo		= "<?=$age?>";			// ����

		var dupeCount	= "<?=$chkCount?>";		// ���Ե� Ƚ��

		var callType = "<?=$ssCallType?>";		// ȣ������

		var minoryn = "<?=$hpauthServiceConfig[minoryn]; ?>"; //�������� ��뿩��
		var under14Code = "<?=$under14Code?>";	// ��14�� �̸� ȸ������ �������ڵ�

		// ���̵�/��й�ȣ ã�⿡�� ȣ���� ���, parent �� callType ������Ʈ�� �ִ�.
		if (callType == "findid" || callType == "findpwd") {
			parent.document.fm.action = '';
			parent.document.fm.target = '';
			parent.document.fm.rncheck.value = 'hpauthDream';
			parent.document.fm.dupeinfo.value = di;
			parent.document.fm.submit();

		//ȸ������ ������ �޴������������� ������ �޴�����ȣ ������Ʈ
		} else if (callType == "modifymember" || callType == "modifymembermobile") {
			var mobile = new Array();

			mobile[0] = phoneNo.substr(0,3);
			if (phoneNo.length == 10) mobile[1] = phoneNo.substr(3,3);
			else if (phoneNo.length == 11) mobile[1] = phoneNo.substr(3,4);
			mobile[2] = phoneNo.substr(-4,4);

			for(var i=0;i<=2;i++){
				if (parent.document.getElementById("mobile"+i))
					parent.document.getElementById("mobile"+i).value = mobile[i];
				else
					parent.document.getElementsByName("mobile[]")[i].value = mobile[i];
			}
		} else { // default ȸ������

			if (dupeCount > 0) {
				alert( "�̹� ������ �Ǿ� �ֽ��ϴ�.");
			} else {
				var targetWindow = (opener) ? opener : parent;
				if ( minoryn == 'y' && ageInfo < 19 ) { // �Ǹ��������� & ������������
					targetWindow.document.frmAgree['name'].value = '';
					alert( '�������� ����' ); //��� �޽��� ���
				} else if ( under14Code == 'rejectJoin' ) { // ��14�� �̸� ȸ������ �ź�
					targetWindow.document.frmAgree['name'].value = '';
					alert( '�� 14�� �̸��� ��� ȸ�������� ������� �ʽ��ϴ�.' ); //��� �޽��� ���
				} else if ( under14Code == 'adminStatus' && confirm('��14�� �̸� ȸ���� ��� ������ ���� �� ������ �Ϸ�˴ϴ�.\n��� �����Ͻðڽ��ϱ�?') === false ) {
					// ��14�� �̸� ȸ������ ������ ���� �� ����
					targetWindow.document.frmAgree['name'].value = '';
				} else if ( strResult == "success" ) { // �޴������� ����
					// alert( "�޴��������� ����ó�� �Ǿ����ϴ�." ); //��� �޽��� ���
					targetWindow.document.frmAgree.action = '';
					targetWindow.document.frmAgree.target = '';
					targetWindow.document.frmAgree.rncheck.value = 'hpauthDream';
					targetWindow.document.frmAgree.nice_nm.value = strName;
					targetWindow.document.frmAgree.mobile.value = phoneNo;
					targetWindow.document.frmAgree.birthday.value = birthday;
					targetWindow.document.frmAgree.sex.value = sex;
					targetWindow.document.frmAgree.dupeinfo.value = di;
					targetWindow.document.frmAgree.foreigner.value = foreigner;
					targetWindow.document.frmAgree.submit();
				} else { // �޴�����������
					targetWindow.document.frmAgree['name'].value = '';
					alert( '�޴��������� �����߽��ϴ�.\n\n'); //��� �޽��� ���
				}
			}

		}
		if (callType == "joinmembermobile" || callType == "modifymembermobile") {
			if (typeof(parent.frmMaskRemove) != 'undefined')  parent.frmMaskRemove('hpauthFrame');
			else self.close();
		}else{
			self.close();
		}
	}
</script>
<? } else { ?>
<script type="text/javascript">
	alert("�߸��� �����Դϴ�.");
</script>
<? } ?>

<body onload="javascript:loadAction();"></body>
</html>
