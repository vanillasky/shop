<?
include "../_header.php";

if ($sess) {
	msg("������ �α��� ���Դϴ�.", '../', 'parent');
}


error_reporting(0);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

switch ((int) $_GET['type']) {
	case 1: // �̸��� ����
		$_tpl = 'auth_by_mb_email.htm';
		// 13�� ���ø� authNum
		break;
	case 2: // �޴��� ����
		$_tpl = 'auth_by_mb_mobile.htm';
		break;

	case 3: // ������ �޴��� ����
		$_tpl = 'auth_by_mobile.htm';
		break;
	case 4: // ������ ���� ���������� ����
		$_tpl = 'auth_by_certificate.htm';
		break;
	default:
		echo '<script>top.location.reload();</script>';
		exit;
		break;
}

$tpl->define(array('tpl'=>'member/'.$_tpl));

$tpl->assign('token', $_POST['token']);
$tpl->assign('m_id', $_POST['m_id']);

### ���ø� ���
$tpl->print_('tpl');

?>
