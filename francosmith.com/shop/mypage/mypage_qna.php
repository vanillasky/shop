<?

include "../_header.php"; chkMember();
include "../lib/page.class.php";

$itemcds = codeitem( 'question' ); # ��������

### 1:1 ����.........2007-07-19 �ʵ��߰��κҷ���=>b.name
$pg = new Page($_GET[page],10);
$pg->field = "distinct a.sno, a.parent, a.itemcd, a.subject, a.contents, a.ordno, a.regdt as regdt, b.m_no, b.m_id, b.name, a.notice";
$db_table = "".GD_MEMBER_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no";

$where[] = "
a.sno in (select sno from ".GD_MEMBER_QNA." where m_no='$sess[m_no]' or notice='1')
OR a.sno in (select parent from ".GD_MEMBER_QNA." where m_no='$sess[m_no]')
OR a.parent in (select sno from ".GD_MEMBER_QNA." where m_no='$sess[m_no]')
OR a.parent in (select parent from ".GD_MEMBER_QNA." where m_no='$sess[m_no]')
";

$pg->setQuery($db_table,$where,$sort="notice desc, parent desc, ( case when parent=a.sno then 0 else 1 end ) asc, regdt desc");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){
	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$data = validation::xssCleanArray($data, array(
			validation::DEFAULT_KEY => 'text',
			'contents' => 'disable',
			'subject'=>'html',
		));
	}
	$data['idx'] = $pg->idx--;

	$data[authmodify] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
	$data[authdelete] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );

	if ( $data[sno] == $data[parent] ){
		$data[authreply] = ( isset($sess) ? 'Y' : 'N' );
	}
	else $data[authreply] = 'N';

	if ( $data[sno] == $data[parent] ){ // ����

		$data[itemcd] = $itemcds[ $data[itemcd] ];
		if ($data['notice'] == 1) { 
			$data['itemcd'] = "��������";
			$data['authreply'] = 'N';
		}
		if ( isset($sess) && $sess[m_no] == $data[m_no] ){
			list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_MEMBER_QNA." where sno != parent and parent='$data[sno]'");
		}
		else {
			list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_MEMBER_QNA." where sno != parent and parent='$data[sno]' and m_no='$sess[m_no]'");
		}
	}

	$data[authdelete] = ( $data[replecnt] > 0 ? 'N' : $data[authdelete] ); # ��� �ִ� ��� ���� �Ұ�

	$data[contents] = nl2br(htmlchars_ech($data[contents]));
	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );

### ���ø� ���
$tpl->print_('tpl');

?>
