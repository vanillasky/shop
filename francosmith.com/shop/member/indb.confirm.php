<?
include "../lib/library.php";
include "../conf/config.php";

switch ($_POST['mode']) {
	case 'confirmPassword':
		if ($_POST['confirm_password']) {
			$checkQuery = "SELECT COUNT(m_id) FROM ".GD_MEMBER." WHERE m_id = '".$sess['m_id']."' AND password in (PASSWORD('".$_POST['confirm_password']."'),OLD_PASSWORD('".$_POST['confirm_password']."'),MD5('".$_POST['confirm_password']."'))";
			list($_SESSION['sess']['confirm_password']) = $db->fetch($checkQuery);
		}

		if (!$_SESSION['sess']['confirm_password'])
			msg('��й�ȣ�� ��Ȯ�ϰ� �Է��� �ּ���.');
		break;
}

go($_SERVER['HTTP_REFERER']);
?>
