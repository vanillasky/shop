<?

include "../_header.php"; chkMember();

### �����Ҵ�
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### 1:1 ����
if ( $mode == 'mod_qna' ){
	$query = "select b.m_id, a.m_no, a.itemcd, a.subject, a.contents, a.email, a.mobile, a.mailling, a.sms, a.ordno, a.parent from ".GD_MEMBER_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.sno='$sno'";
	$data = $db->fetch($query,1);
	// @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ�� (�α����� ȸ���� ��밡��)
	if($sess['level'] < 80 && $sess['m_no'] != $data['m_no']) {
		msg('������ �ۼ��� 1:1���Ǹ� �����Ͻ� �� �ֽ��ϴ�.', 'close');
	}
	$data[mobile]	= explode("-",$data[mobile]);
}
else {
	$data['m_id'] = $sess['m_id'];
}

if( $mode == 'reply_qna' || ( $sno != '' && $data['parent'] != '' && $sno != $data['parent'] ) ) $formtype = 'reply'; // �Է��׸� ����


### ���Ẹ�ȼ��� ȸ��ó��url
$tpl->assign('myqnaActionUrl',$sitelink->link('mypage/indb.php','ssl'));

// ������������ �� �̿뿡 ���� �ȳ�
$termsPolicyCollection4 = getTermsGuideContents('terms', 'termsPolicyCollection4');
$tpl->assign('termsPolicyCollection4', $termsPolicyCollection4);

### ���ø� ���
$tpl->print_('tpl');

?>