<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>�̸��� ���Űź�</title>
<script type="text/javascript">
window.onload = function() {
	if (confirm('���Űźη� �����Ͻø� e-mail ���� ���������� Ư����ǰ, �̺�Ʈ ������ �޾ƺ��� �� �����ϴ�. ���Űź� �Ͻðڽ��ϱ�?')) {
		document.sForm.submit();
	}
	else {
		window.close();
	}
}
</script>
</head>
<body>
<form name="sForm" method="post" action="./emailDeny.indb.php" target=ifrmHidden>
<input type="hidden" name="k" value="<?=$_GET['k']?>"/>
<input type="hidden" name="id" value="<?=$_GET['id']?>"/>
</form>
<iframe name="ifrmHidden" src='../blank.php' style="display:none;width:100%;height:600"></iframe>
</body>
</html>