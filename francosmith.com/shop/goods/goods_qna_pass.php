<?

include "../_header.php";

### �����Ҵ�
$mode		= $_GET[mode];
$sno		= $_GET[sno];
// 2013-01-16 dn ��ǰ QA �Խ��� ��ȸ�� �� ��й�ȣ �Է��� ���� �� �������� ���� ���� $mode������ default �� �Է�
if(!$mode) $mode = 'auth_qna';

### ��ǰ �������亯
$query = "select m_no, subject,secret from ".GD_GOODS_QNA." where sno='$sno'";
$data = $db->fetch($query,1);

### ��б� �������� üũ
$qna_auth = unserialize($_SESSION['qna_auth']);
if(!$qna_auth) $qna_auth = array();

### �α���üũ
if(!isset($sess) && $data['m_no']){
	msg('�α������ּ���!');
	echo("<script>opener.parent.location.href='../member/login.php';self.close();</script>");
}

### ���ø� ���
$tpl->print_('tpl');

?>