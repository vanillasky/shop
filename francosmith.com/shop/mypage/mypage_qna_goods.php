<?

include "../_header.php"; chkMember();
include "../lib/page.class.php";

### ȸ�������� ���ô�� & ȸ����۰� �������� �� �⺻Ű
$qna_sno = array();
$res = $db->query( "select sno, parent from ".GD_GOODS_QNA." where m_no='$sess[m_no]'" );
while ( $row = $db->fetch( $res ) ){
	if ( $row['sno'] == $row['parent'] ){
		$res_s = $db->query( "select sno from ".GD_GOODS_QNA." where parent='$row[sno]'" );
		while ( $row_s = $db->fetch( $res_s ) ) $qna_sno[] = $row_s['sno'];
	}
	else if ( $row['sno'] != $row['parent'] ){
		$qna_sno[] = $row['sno'];
		$qna_sno[] = $row['parent'];
	}
}

### ��ǰ ����
$pg = new Page($_GET[page],10);
$pg->field = "distinct a.sno, a.parent, a.goodsno, a.subject, a.contents, a.regdt as regdt, a.name, b.m_no, b.m_id";
$db_table = "".GD_GOODS_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no";

if ( count( $qna_sno ) ) $where[] = "a.sno in ('" . implode( "','", $qna_sno ) . "')";
else $where[] = "0";

$pg->setQuery($db_table,$where,$sort="parent desc, ( case when parent=a.sno then 0 else 1 end ) asc, regdt desc");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){
	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$data = validation::xssCleanArray($data, array(
			validation::DEFAULT_KEY => 'text',
			'contents' => array('html', 'ent_noquotes'),
			'subject' => array('html', 'ent_noquotes'),
		));
	}

	$data['idx'] = $pg->idx--;

	$data[authmodify] = $data[authdelete] = $data[authreply] = 'Y'; # �����ʱⰪ

	if ( empty($cfg['qnaWriteAuth']) || isset($sess) || !empty($data[m_no]) ){ // ȸ������ or ȸ�� or �ۼ���==ȸ��
		$data[authmodify] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
		$data[authdelete] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
	}

	if ( $data[sno] == $data[parent] ){
		if ( empty($cfg['qnaWriteAuth']) ){ // ȸ������
			$data[authreply] = ( isset($sess) ? 'Y' : 'N' );
		}
	}
	else $data[authreply] = 'N';

	if ( $data[sno] == $data[parent] ){ // ����

		$query = "select b.goodsnm,b.img_s,c.price
		from
			".GD_GOODS." b
			left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
		where
			b.goodsno = '" . $data[goodsno] . "'";
		list( $data[goodsnm], $data[img_s], $data[price] ) = $db->fetch($query);

		if ( isset($sess) && $sess[m_no] == $data[m_no] ){
			list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_GOODS_QNA." where sno != parent and parent='$data[sno]'");
		}
		else {
			list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_GOODS_QNA." where sno != parent and parent='$data[sno]' and m_no='$sess[m_no]'");
		}
	}

	$data[authdelete] = ( $data[replecnt] > 0 ? 'N' : $data[authdelete] ); # ��� �ִ� ��� ���� �Ұ�

	if ( empty($data[m_no]) ) $data[m_id] = $data[name]; // ��ȸ����

	$data[contents] = nl2br(htmlspecialchars($data[contents]));
	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );
$tpl->assign( 'lstcfg', $lstcfg );

### ���ø� ���
$tpl->print_('tpl');

?>
