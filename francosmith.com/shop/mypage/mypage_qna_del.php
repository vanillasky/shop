<?

include "../_header.php"; chkMember();

### �����Ҵ�
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### 1:1 ����
$query = "select a.subject from ".GD_MEMBER_QNA." a where a.sno='$sno'";
$data = $db->fetch($query,1);

### ���ø� ���
$tpl->print_('tpl');

?>