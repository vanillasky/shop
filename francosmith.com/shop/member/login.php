<?

include "../_header.php";

### ȸ����������
if( $sess ){
	msg("������ �α��� ���Դϴ�.",$code=-1 );
}

if (!$_GET['returnUrl']) $returnUrl = $_SERVER['HTTP_REFERER'];
else $returnUrl = $_GET['returnUrl'];

### �����̼� ���뼥 ��ȸ�� ó�� ����
require "../lib/load.class.php";
// TodayShop class
$todayShop = Core::loader('todayshop');
if ($todayShop->cfg['shopMode'] == 'todayshop') {
	$tpl->assign('guest_disabled', 'y');
}
unset($todayShop);
### �����̼� ���뼥 ��ȸ�� ó�� ��

$tpl->print_('tpl');

?>
