<?

include "../_header.php";

### ȸ����������
chkMember();

$tpl->assign('ori_returnUrl', $_GET['ori_returnUrl']);
$tpl->print_('tpl');

?>