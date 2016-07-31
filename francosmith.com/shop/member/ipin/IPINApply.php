<?
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );
	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );
	$dormant = Core::loader('dormant');

	if ($ipin['nice_useyn'] == 'y') {
		$sSiteCode	= $ipin['code'];		// IPIN ���� ����Ʈ �ڵ�		(NICE�ſ����������� �߱��� ����Ʈ�ڵ�)
		$sSitePw	= $ipin['password'];	// IPIN ���� ����Ʈ �н�����	(NICE�ſ����������� �߱��� ����Ʈ�н�����)
		$sEncData	= "";					// ��ȣȭ �� ����� ���� ����
		$sDecData	= "";					// ��ȣȭ �� ����� ���� ����
		$sRtnMsg	= "";					// ó����� �޼���
		$sType		= "RES";				// RES : ��ȣȭ

		$self_filename = basename($_SERVER['PHP_SELF']);
		$loc = strpos($_SERVER['PHP_SELF'], $self_filename);
		$loc = substr($_SERVER['PHP_SELF'], 0, $loc);
		$sModulePath = $_SERVER['DOCUMENT_ROOT'].$loc."IPINClient";

		$sCPRequest = $_SESSION['CPREQUEST'];
		$sEncData = $_POST['enc_data'];

		if ($sEncData != "") {
			$sDecData = exec("$sModulePath $sType $sSiteCode $sSitePw $sEncData");

			if ($sDecData == -9) $sRtnMsg = "�Է°� ���� : ��ȣȭ ó����, �ʿ��� �Ķ���Ͱ��� ������ ��Ȯ�ϰ� �Է��� �ֽñ� �ٶ��ϴ�.";
			else if ($sDecData == -12) $sRtnMsg = "NICE�ſ����������� �߱��� ���������� ��Ȯ���� Ȯ���� ������.";
			else {
				$arrData = split("\^", $sDecData);
				$iCount = count($arrData);

				if ($iCount >= 5) {
					$strResultCode = $arrData[0];		// ����ڵ�

					if ($strResultCode == 1) {
						$strCPRequest = $arrData[8];	// CP ��û��ȣ

						if ($sCPRequest == $strCPRequest) {
							list($chkCount) = $db->fetch("select count(*) from ".GD_MEMBER." where dupeinfo='".$arrData[3]."'");
							if(!$chkCount){
								$chkCount = $dormant->getCountDupeinfoFromDormant($arrData[3]);
							}
							if(!$chkCount) {
								$sRtnMsg = "����� ���� ����";
								$strDupInfo = $arrData[3];
							}
							else $sRtnMsg = "�̹� ���Եƽ��ϴ�.";
						}
						else $sRtnMsg = "CP ��û��ȣ ����ġ : ���ǿ� ���� $sCPRequest ����Ÿ�� Ȯ���� �ֽñ� �ٶ��ϴ�.";
					}
					else $sRtnMsg = "���ϰ� Ȯ�� ��, NICE�ſ������� ���� ����ڿ��� ������ �ּ���. [$strResultCode]";
				}
				else $sRtnMsg = "���ϰ� Ȯ�� ��, NICE�ſ������� ���� ����ڿ��� ������ �ּ���.";
			}
		}
		else $sRtnMsg = "ó���� ��ȣȭ ����Ÿ�� �����ϴ�.";
	}
	else if ($ipin['useyn'] == 'y') {
		$athKeyStr = $ipin['athKeyStr'];

		$oivsObject = new OivsObject();
		$oivsObject->athKeyStr = $athKeyStr;

		$strRecvData 	= $_POST[ "SendInfo" ];
		$blRcv 		= $oivsObject->resolveClientData( $strRecvData );
		// ��ŷ������ ���� ���ǿ� ����� ���� �� ..

		$ssOrderNo = $_SESSION["sess_OrderNo"];

		if( $ssOrderNo != $oivsObject->ordNo) $sRtnMsg = "���������� �������� �ʽ��ϴ�.";

		list($chkCount) = $db->fetch("select count(*) from ".GD_MEMBER." where dupeinfo='".$oivsObject->dupeInfo."'");
		if(!$chkCount){
			$chkCount = $dormant->getCountDupeinfoFromDormant($oivsObject->dupeInfo);
		}
		if(!$chkCount) {
			$sRtnMsg = "����� ���� ����";
			$strDupInfo = $oivsObject->dupeInfo;
		}
		else $sRtnMsg = "�̹� ���Եƽ��ϴ�.";
	}
?>
<script language="JavaScript">
	alert("<?=$sRtnMsg?>");
	<? if($strDupInfo) { ?>
	opener.parent.document.frmMember.dupeinfo.value = "<?=$strDupInfo?>";
	<? } ?>
	opener.parent.document.getElementById('ipinManual').style.display = 'none';
	self.close();
</script>