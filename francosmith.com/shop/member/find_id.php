<?
include "../_header.php";
include "../conf/fieldset.php";

$config = Core::loader('config');
$hpauth = Core::loader('Hpauth');
$dormant = Core::loader('dormant');
$shopConfig = $config->load('config');
$hpauthRequestData = $hpauth->getAuthRequestData();

### ȸ����������
if( $sess ){
	msg("������ �α��� ���Դϴ�.",$code=-1 );
}

unset($fld['per']['resno']);

if( $_POST['act'] == 'Y' && $_POST['rncheck'] != 'ipin' && $_POST['rncheck'] != 'hpauthDream' ){

	$where = array();
	$where[] = "name='" . $_POST['srch_name'] . "'";
	if ( $checked['useField']['email'] ) $where[] = "email='" . $_POST['srch_mail'] . "'";

	list( $m_id, $name ) = $db->fetch("select m_id, name from ".GD_MEMBER." where " . implode( " and ", $where ));

	//�޸�ȸ�� ��ȸ
	if(!$m_id){
		list($m_id, $name) = $dormant->findIdUser('name', $_POST);
	}
} else if ( $_POST['act'] == 'Y' && ($_POST['rncheck'] == 'ipin' || $_POST['rncheck'] == 'hpauthDream')) {

	list( $m_id, $name ) = $db->fetch("select m_id, name from ".GD_MEMBER." where dupeinfo = '$_POST[dupeinfo]'");

	//�޸�ȸ�� ��ȸ
	if(!$m_id){
		list($m_id, $name) = $dormant->findIdUser('dupeinfo', $_POST);
	}
}

$tpl->assign('ipinyn', (empty($ipin[id]) ? 'n' : empty($ipin[useyn])? 'n': $ipin[useyn]));
$tpl->assign('niceipinyn', empty($ipin[nice_useyn])? 'n': $ipin[nice_useyn]);
$tpl->assign('hpauthDreamyn', $hpauthRequestData['useyn']);
$tpl->assign('hpauthDreamcpid', $hpauthRequestData['cpid']);
$tpl->print_('tpl');
?>