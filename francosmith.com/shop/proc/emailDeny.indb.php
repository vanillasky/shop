<?php
### �̸��� ���Űź� ó��
include '../lib/library.php';

$emailDeny = Core::loader('LibEmailDeny');
$res = $emailDeny->setDeny($_POST['k'], $_POST['id']);

if ($res['result'] == true) {
	echo '<script type="text/javascript">alert("�̸��� ���Űźΰ� ���� ó���Ǿ����ϴ�.\n������� ���Ͻô� ��� ���������� > ȸ�������������� ����,�̺�Ʈ���� ���ſ� ��üũ���� �ֽñ� �ٶ��ϴ�."); parent.window.close();</script>';
}
else {
	echo '<script type="text/javascript">alert("�̸��� ���Űźο� �����Ͽ����ϴ�.\n��� �� �ٽ� �õ��ϰų� ���������� > ȸ�������������� ����,�̺�Ʈ���ϼ��ſ� ��üũ���� ������ �ֽñ� �ٶ��ϴ�."); parent.window.close();</script>';
}
?>