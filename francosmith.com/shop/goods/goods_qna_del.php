<?

include "../_header.php";

### �����Ҵ�
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### ��ǰ �������亯
$query = "select a.m_no, a.subject from ".GD_GOODS_QNA." a where a.sno='$sno'";
$data = $db->fetch($query,1);

### ���ø� ���
$tpl->print_('tpl');

if(isset($sess) && $data[m_no] == $sess[m_no]){
	echo("<script>document.forms[0].password.value='1';document.forms[0].submit();</script>");
}
?>