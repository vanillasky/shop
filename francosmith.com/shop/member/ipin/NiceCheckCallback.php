<?php
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );
	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );

	//session_start();
	//========================================================================================
	//=====	�� Ű��Ʈ�� 80�ڸ� ���� ��
	//========================================================================================
	$athKeyStr = $ipin[athKeyStr];

	$oivsObject = new OivsObject();
	$oivsObject->athKeyStr = $athKeyStr;

	$strRecvData 	= $_POST[ "SendInfo" ];
	$blRcv 		= $oivsObject->resolveClientData( $strRecvData );
	// ��ŷ������ ���� ���ǿ� ����� ���� �� ..

	$ssOrderNo = $_SESSION["sess_OrderNo"];

	if( $ssOrderNo != $oivsObject->ordNo){
		echo ("���������� �������� �ʽ��ϴ�.");
		exit;
	}
	$sess_OrderNo = "";
	session_register("sess_OrderNo");

	$ssCallType = $_SESSION["sess_callType"];

	// ȸ�����Խ� ���԰�ΰ� ��������� üũ, ������� ������üũ���� ���ǿ� ������ ���� �ҷ���
	$joinGubun = $_SESSION["joinGubun"];

	$year = date('Y');

	//debug($oivsObject);
	//debug($ssCallType);

	list($chkCount) = $db->fetch("select count(*) from ".GD_MEMBER." where dupeinfo='".$oivsObject->dupeInfo."'");
	if($chkCount < 1){
		$dormant = Core::loader('dormant');
		$chkCount = $dormant->getCountDupeinfoFromDormant($oivsObject->dupeInfo);
	}

	$birth_year = substr($oivsObject->birthday, 0, 4);

	$age = $year-$birth_year;

	if($age >= 19) {
		$_SESSION['adult'] = 1;
	}
	else {
		unset($_SESSION['adult']);
	}

	// ������������ ��14�� �̸� ȸ������ ������
	$mUnder14 = Core::loader('memberUnder14Join');
	$under14Code = $mUnder14->joinSelfCert($oivsObject->birthday);

	if($ssCallType == "adultcheck" || $ssCallType == "adultcheckmobile") {
		if($_SESSION['adult']) {
			$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "../../index.php";
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "http://".$_SERVER['HTTP_HOST']."/m/";
			}
			unset($_SESSION['ipin_requrlUrl']); // ����ϼ� returnUrl ������ ���ķ� ��ġ ����
			msg("���� �����Ǿ����ϴ�.");
			if($sess){
				setAdultAuthDate($session->m_id);
			}
			if($joinGubun == 'mobile'){
				echo "<script>
					 parent.location='$returnUrl';
					 </script>";
			}else{
				echo "<script>
					 opener.parent.location='$returnUrl';
					 window.close();
					 </script>";
			}
		}
		else {
			$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "../../index.php";
			unset($_SESSION['ipin_requrlUrl']);
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "http://".$_SERVER['HTTP_HOST']."/m/";
			}
			unset($_SESSION['adult']);
			msg("���� ������ �����߽��ϴ�.");
			if($joinGubun == 'mobile'){
				echo "<script>
					 parent.location='$returnUrl';
					 </script>";
			}else{
				echo "<script>
					 opener.parent.location='$returnUrl';
					 window.close();
					 </script>";
			}
		}
		exit();
	}

//!!!!!!!!!!!!!!!!!����!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//��翡�� �����Ͽ� ��ȣȭ�� ������
//SSL�̳� ������ ���ȸ���� ����Ǿ� ���� ���� ȯ�濡��
//�������� �����ϴ� ���� ���ȿ� �ɰ��� ������ �߱��� �� �ֽ��ϴ�.

//���� ȯ���� ���߾����� ���� ��ü������, �ʿ��� ����
//���� ������� �ٸ� ������ �̿��Ͽ� ����� �ֽñ� �ٶ��ϴ�.
//�� ������ �ؼ����� �ʾƼ� �߻��ϴ� ���Ȼ�� ���ؼ���
//��翡�� å���� ���� �ʻ����, ���Ǹ� ��￩ �ֽñ� �ٶ��ϴ�.

?>

<html>
	<script type="text/javascript">
		var _joinGubun = "<? echo $joinGubun?>";
		function loadAction()
		{
			var strRetCd = "<? echo $oivsObject->retCd; ?>";		// '1' �̾�� �Ѵ�.
			var strRetDtlCd = "<? echo $oivsObject->retDtlCd; ?>";	// 'A" �̾�� �Ѵ�.
			var strMsg = "<? echo $oivsObject->message; ?>";

			var strName = "<? echo $oivsObject->niceNm; ?>";
			var birthday = "<? echo $oivsObject->birthday; ?>";
			var sex = "<? if ($oivsObject->sex == '1') echo 'M'; else 'W'; ?>";
			var dupeInfo = "<? echo $oivsObject->dupeInfo; ?>";
			var foreigner = "<? echo $oivsObject->foreigner; ?>";
			var paKey = "<? echo $oivsObject->paKey; ?>";

			var dupeCount = "<? echo $chkCount ?>";

			var minoryn = "<? echo $ipin[minoryn]; ?>";

			var year = "<? echo $year; ?>";

			var age = year-birthday.substring( 0, 4);
			// ȣ�������� ã�´�.
			var callType = "<? echo $ssCallType; ?>";
			var under14Code = "<?=$under14Code?>";	// ��14�� �̸� ȸ������ �������ڵ�

			//	�ѱ��ſ������� ���� ����ڵ忡 �ش��ϴ� �޽����� �޾ƿ´�.
			//	(�ٸ� �޽����� ������ �޴��� ������ �����Ͽ�  strRetCd, strRetDtlCd �� �޽����� ������ �ش�.
			//strProcessMessage = getMessage( strRetCd, strRetDtlCd );

			// ���̵� ã�⿡�� ȣ���� ���, opener.parent �� act ������Ʈ�� �ִ�.
			if (callType == "findid" || callType == "findpwd") {
				opener.parent.document.fm.action = '';
				opener.parent.document.fm.target = '';
				opener.parent.document.fm.rncheck.value = 'ipin';
				opener.parent.document.fm.dupeinfo.value = dupeInfo;
				opener.parent.document.fm.submit();
			}
			else {
				var _joinGubunHeader = (_joinGubun == "mobile") ? parent : opener.parent;
				// default ȸ������
				if (dupeCount > 0) {
				alert( "�̹� ������ �Ǿ� �ֽ��ϴ�.");
				}
				else {
					if ( minoryn == 'y' && strRetCd == "1" && age < 20 ){ // �Ǹ��������� & ������������
						_joinGubunHeader.document.frmAgree['name']. value = '';
						alert( '�������� ����' ); //��� �޽��� ���
					}
					else if ( under14Code == 'rejectJoin' ) { // ��14�� �̸� ȸ������ �ź�
						_joinGubunHeader.document.frmAgree['name']. value = '';
						alert( '�� 14�� �̸��� ��� ȸ�������� ������� �ʽ��ϴ�.' ); //��� �޽��� ���
					}
					else if ( under14Code == 'adminStatus' && confirm('��14�� �̸� ȸ���� ��� ������ ���� �� ������ �Ϸ�˴ϴ�.\n��� �����Ͻðڽ��ϱ�?') === false ) {
						// ��14�� �̸� ȸ������ ������ ���� �� ����
						_joinGubunHeader.document.frmAgree['name']. value = '';
					}
					else if ( strRetCd == "1" && strRetDtlCd == "A") // ��������������
					{
						alert( "������������ ����ó�� �Ǿ����ϴ�." ); //��� �޽��� ���
						_joinGubunHeader.document.frmAgree.action = '';
						_joinGubunHeader.document.frmAgree.target = '';
						_joinGubunHeader.document.frmAgree.rncheck.value = 'ipin';
						_joinGubunHeader.document.frmAgree.nice_nm.value = strName;
						_joinGubunHeader.document.frmAgree.pakey.value = paKey;
						_joinGubunHeader.document.frmAgree.birthday.value = birthday;
						_joinGubunHeader.document.frmAgree.sex.value = sex;
						_joinGubunHeader.document.frmAgree.dupeinfo.value = dupeInfo;
						_joinGubunHeader.document.frmAgree.foreigner.value = foreigner;
						_joinGubunHeader.document.frmAgree.submit();
					}
					else // �Ǹ���������
					{
					//	����� ���� �Ǹ�Ƚ����ܰ� ���ǵ��� ������ ó���Ѵ�.
						_joinGubunHeader.document.frmAgree['name']. value = '';
						alert( '������������ �����߽��ϴ�. ' + strMsg); //��� �޽��� ���
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

	<body onload="javascript:loadAction();"></body>
</html>
