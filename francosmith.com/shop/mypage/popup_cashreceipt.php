<?

include "../_header.php";

if (!$sess && !$_COOKIE[guest_ordno]) go("../member/login.php?returnUrl=$_SERVER[PHP_SELF]");

include '../lib/cashreceipt.class.php';
$cashreceipt = new cashreceipt();
if ($_GET['ordno']){
	$url = $cashreceipt->getReceipturl($_GET['ordno'], 'ordno');
}
else {
	$url = $cashreceipt->getReceipturl($_GET['crno']);
}

if ($url){
	header('Location: '.$url);
	exit;
}
?>
<html>
<head>
<title>�� �� �� �� ��</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
</head>
<body oncontextmenu="return false">
<div style="margin:0 40 20 40; font-size:9pt;">
�� ����Ʈ������ ���ݿ����� ����� �������� �ʽ��ϴ�.<br>
����û ���ݿ�����(taxsave.go.kr)���� Ȯ���ϼ���.
</div>
</body>
</html>