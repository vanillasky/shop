<?

include "../_header.php";

if (!$sess && !$_COOKIE[guest_ordno]) go("../member/login.php?returnUrl=$_SERVER[PHP_SELF]");
?>
<html>
<head>
<title>���ݰ�꼭 �μ�</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
</head>
<div id=dynamic></div>
<body oncontextmenu="return false">
<style type="text/css"><!--
@media print { .notprint {display: none;} } /* �μ�� ���ʿ��� �κ� ��Ȱ��ȭ */
.small {
	font:8pt Dotum;
	letter-spacing:0px;
	padding-top:3px;
}
--></style>

<script language="javascript"><!--
window.onbeforeprint = function () // ���ݰ�꼭 ��½� ����
{
	var ele = eval("document.getElementsByName('taxtable')");
	for ( i=0; i<ele.length; i++ )
	{
		var jscript = document.createElement("script");
		jscript.src="../mypage/indb.php?mode=taxprint&sno=" + ele[i].taxsno;
		document.getElementById('dynamic').appendChild(jscript);
	}
}
--></script>

<DIV class="notprint" style="margin:0 40 20 40;">
<a href="javascript:window.print();"><img src="../admin/img/btn_print.gif" border="0" align="absmiddle"></a><br>
<font color=#5B5B5B>�� <span class=small>���ݰ�꼭 �μ�� ���ε� �μ�Ƿ��� ������ ���� �����Ǿ� �־�� �����մϴ�.</span></font>
<dl class=small style="color:#5B5B5B; margin:0 0 0 20px;">
<dt>1) ���ͳ� �ͽ��÷η� ��� ��
<dd>: ������ ����� ���� �޴� Ŭ�� ��, [���ͳݿɼ�]-[���]-[�μ�] ���� [���� �� �̹��� �μ�] üũ
<dt>2) ���̾����� ��� ��
<dd>: ������ ����� ���� �޴� Ŭ�� ��, [�μ�ȭ�鼳��]-[���� �� ����]-[�ɼ�]���� [��� �μ�(���� �� �׸�)] üũ
</dl>
</div>
<?

$_GET[taxarea] = 'blue';
$ordno = $_GET[ordno];
include "../admin/order/_paper.tax.php";

?>
</body>
</html>