<?

include "../_header.php";

### �����Ҵ�
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### ��ǰ ����
$query = "select a.m_no, a.subject from ".GD_GOODS_REVIEW." a where a.sno='$sno'";
$data = $db->fetch($query,1);

### ���ø� ���
$tpl->print_('tpl');

?>