<?

include "../_header.php";
chkMemberPopupConfirm();

$goodsno = $_GET['goodsno'];

##��ǰ�� �ɼ� ��� �ҷ�����
##��ǰ ���԰� �˸��� ����ϴ��� Ȯ��
$query = "SELECT count(goodsno) FROM ".GD_GOODS." WHERE goodsno='".$goodsno."' AND usestock='o' AND use_stocked_noti='1'";
list($useStock) = $db->fetch($query);
if($useStock < 1){
	msg("��ǰ ���԰� �˸��� ����� �� ���� ��ǰ�Դϴ�!");
	?><script type="text/javaascript">
		self.close();
	</script><?
}

##�ɼ� ���� ��������
$query = "SELECT count(goodsno) FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and go_is_deleted <> '1' and go_is_display = '1' ";
list($optCnt) = $db->fetch($query);

##��� ���� �ɼ��� ��������
$query = "SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' AND stock='0'  and go_is_deleted <> '1' and go_is_display = '1' ";
$optData = $db->query($query);
while($data = $db->fetch($optData)){
	$optList[] = $data;
}

##����wkdqj�����;
$query = "SELECT * FROM ".GD_GOODS." WHERE goodsno='".$goodsno."'";
$optData = $db->query($query);
$data = $db->fetch($optData);
$goodsnm = strcut($data['goodsnm'], 30);

##ȸ������
$query = "SELECT name, mobile FROM ".GD_MEMBER." WHERE m_no = '".$sess['m_no']."'";
$memberData = $db->fetch($query);
$name = $memberData['name'];
$mobile = str_replace("-", "", $memberData['mobile']);

// ������������ �� �̿뿡 ���� �ȳ�
$termsPolicyCollection4 = getTermsGuideContents('terms', 'termsPolicyCollection4');
$tpl->assign('termsPolicyCollection4', $termsPolicyCollection4);

### ���ø� ���
$tpl->assign('goodsno',$goodsno);				//��ǰ��ȣ
$tpl->assign('goodsnm',$goodsnm);				//��ǰ��ȣ
$tpl->assign('optCnt',$optCnt);					//�ɼ� ��
$tpl->assign('optList',$optList);				//�ɼ� ���
$tpl->assign('memberName',$name);	//ȸ���̸�
$tpl->assign('mobile',$mobile);					//ȸ�� ��ȭ��ȣ
$tpl->print_('tpl');
?>