<?
header("content-type:text/xml");
include "../lib/library.php";
include_once dirname(__FILE__)."/../conf/config.php";
include_once dirname(__FILE__)."/../conf/naverCheckout.cfg.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
include_once dirname(__FILE__)."/../lib/httpSock.class.php";

$naverCheckoutAPI->ncLog('onclick_status', "START");

// ���� ������
$arrData = array(
	'SelectType'	=> trim($_GET['SelectType']),	// ������ȸ : 1; �ߺ���ȸ : 2
	'NCMallID'		=> trim($_GET['NCMallID']),		// ���̹� ������ ID
	'MallUserID'	=> trim($_GET['MallUserID']),	// ������ ȸ�� ���̵�
	'MallUserNo'	=> trim($_GET['MallUserNo']),	// ������ ȸ�� ������ȣ
	'NCUserSSN'		=> trim($_GET['NCUserSSN']),	// ���̹� ȸ�� �ֹε�Ϲ�ȣ(13�ڸ�)
	'NCUserName'	=> trim($_GET['NCUserName']),	// ���̹� ȸ�� �̸�
	'Timestamp'		=> trim($_GET['Timestamp']),	// ���� ��û �ð�
);

// ���� ��ȣȭ
$tmpData = array('SelectType' => $arrData['SelectType'], 'MallUserID' => $arrData['MallUserID'], 'MallUserNo' => $arrData['MallUserNo'], 'NCUserSSN' => $arrData['NCUserSSN'], 'NCUserName' => $arrData['NCUserName']);
foreach ($tmpData as $k => $v) {
	if ($v != '') {
		$temp_ar = explode('|||', $naverCheckoutAPI->ncCrypt('decrypt',$v,$arrData['Timestamp']));
		if($temp_ar[0] == "ERRO") $naverCheckoutAPI->memberStatusXML('ERROR', $temp_ar[1]);
		else $arrData[$k] = $temp_ar[1];
	}
}

// ���ǵ����� EUC-KR�� ��ȯ
foreach ($arrData as $k => $v) {
	$arrData[$k] = iconv('UTF-8', 'EUC-KR', $v);
}

$naverCheckoutAPI->ncLog('onclick_status',
	'SelectType => '.$arrData['SelectType']
	.', NCMallID => '.$arrData['NCMallID']
	.', MallUserID => '.$arrData['MallUserID']
	.', MallUserNo => '.$arrData['MallUserNo']
	.', NCUserSSN => '.substr($arrData['NCUserSSN'],0,6).preg_replace('/\d/', '*', substr($arrData['NCUserSSN'],6))
	.', NCUserName => '.$arrData['NCUserName']
	.', Timestamp => '.$arrData['Timestamp']
);

// ���� ����
if (in_array($arrData['SelectType'], array('1','2')) === false) {
	$naverCheckoutAPI->memberStatusXML('ERROR', '���� ��ȸ(1)�� �ߺ� ��ȸ(2)�� �ƴմϴ�.');
}
if ($arrData['NCMallID'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '���̹� ������ ���̵� ���޵��� �ʾҽ��ϴ�.');
}
if ($arrData['NCMallID'] <> $checkoutCfg['naverId']) {
	$naverCheckoutAPI->memberStatusXML('ERROR', '������ ������ ������ ���̵�� ���̹� ������ ���̵� �ٸ��ϴ�.');
}
if ($arrData['SelectType'] == '1' && $arrData['MallUserID'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '������ ȸ�� ���̵� ���޵��� �ʾҽ��ϴ�.');
}
if ($arrData['SelectType'] == '1' && $arrData['MallUserNo'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '������ ȸ�� ������ȣ�� ���޵��� �ʾҽ��ϴ�.');
}
if ($arrData['SelectType'] == '2' && $arrData['NCUserSSN'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '���̹� ȸ�� �ֹε�Ϲ�ȣ�� ���޵��� �ʾҽ��ϴ�.');
}
if ($arrData['SelectType'] == '2' && $arrData['NCUserName'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '���̹� ȸ�� �̸��� ���޵��� �ʾҽ��ϴ�.');
}

// ȸ�� ���� ��ȸ(1)
if ($arrData['SelectType'] == '1') {
	$query = $db->_query_print('select m_no from gd_member where m_id = [s] and m_no = [s]',$arrData['MallUserID'],$arrData['MallUserNo']);
	$result = $db->_select($query);
	$result = $result[0];
	$naverCheckoutAPI->memberStatusXML('SUCCESS', '', ($result['m_no'] != '' ? 'VALID' : 'INVALID'));
}

// ȸ�� �ߺ� ��ȸ(2)
if ($arrData['SelectType'] == '2') {
	$resno1 = substr($arrData['NCUserSSN'], 0, 6);
	$resno2 = substr($arrData['NCUserSSN'], 6, 7);
	$query = $db->_query_print('select m_no from gd_member where resno1 = md5([s]) and resno2 = md5([s]) and name = [s]',$resno1,$resno2,$arrData['NCUserName']);
	$result = $db->_select($query);
	$result = $result[0];
	$naverCheckoutAPI->memberStatusXML('SUCCESS', '', ($result['m_no'] != '' ? 'VALID' : 'INVALID'));
}

$naverCheckoutAPI->ncLog('onclick_status', "END");
?>
