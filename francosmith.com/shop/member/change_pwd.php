<?
include "../_header.php";

if ($sess) {
	msg("������ �α��� ���Դϴ�.", '../', 'parent');
}


if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$now = time();

$query = sprintf("select mb.mobile, mb.email, mb.name, mb.m_id, otp.token, otp.otp, otp.expire, otp.auth from ".GD_OTP." as otp inner join ".GD_MEMBER." as mb on otp.m_id = mb.m_id AND otp.expire > '%s' where otp.m_id = '%s' AND otp.token > '' AND otp.token = '%s' AND otp.auth = 1", date('Y-m-d H:i:s', $now), $db->_escape($_POST['m_id']), $db->_escape($_POST['token']));
if (($_mb = $db->fetch($query, 1)) !== false) {

}
else {
	msg('����������� �������� �ʽ��ϴ�.', '../member/find_pwd.php', 'parent');
	exit;
}

$tpl->assign('token', $_POST['token']);
$tpl->assign('m_id', $_POST['m_id']);

### ���ø� ���
$tpl->print_('tpl');

?>
