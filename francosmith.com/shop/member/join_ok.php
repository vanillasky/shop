<?

include "../_header.php";

if(!$session->m_no) {
	msg('ȸ�����Կ����Դϴ�',-1);
	exit;
}

$query = $db->_query_print('select * from gd_member where m_id=[s]',$session->m_id);
$result = $db->_select($query);
$data = $result[0];
unset($_SESSION['adult']);

$Acecounter->member_join($session->m_id);
if($Acecounter->scripts){
	$systemHeadTagStart .= $Acecounter->scripts;
	$tpl->assign('systemHeadTagStart',$systemHeadTagStart);
}

### ���̹� üũ�ƿ�(ȸ������)
@include "../conf/naverCheckout.cfg.php";
if($checkoutCfg['useYn']=='y' && $checkoutCfg['ncMemberYn']=='y'):
	require "../lib/naverCheckout.class.php";
	$NaverCheckout = Core::loader('NaverCheckout');
	$ncResData = $NaverCheckout->get_oneclickJoinOk($_GET['mode']);

	if ($ncResData['mode'] == 'ok') {
		$tpl->assign('naverCheckout_oneclickStep',$ncResData['stepHtml']);
	}
endif;

$tpl->assign($data);
$tpl->print_('tpl');

?>
