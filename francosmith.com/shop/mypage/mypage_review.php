<?

include "../_header.php"; chkMember();
include "../lib/page.class.php";

### �ı� ���ε� �̹��� ���� ����
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}
### ��ǰ ����
$pg = new Page($_GET[page],10);
$pg->field = "distinct a.sno, a.goodsno, a.subject, a.contents, a.point, a.regdt, b.m_no, b.m_id, a.attach";
$db_table = "".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no";

$where[] = "a.m_no = '$sess[m_no]'";

$pg->setQuery($db_table,$where,$sort="regdt desc");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	// @qnibus 2015-06 XSS ������� ���� ���͸� ���� �߰�
	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$data = validation::xssCleanArray($data, array(
			validation::DEFAULT_KEY => 'text',
			'contents' => array('html', 'ent_noquotes'),
			'subject' => array('html', 'ent_noquotes'),
		));
	}
	$data['idx'] = $pg->idx--;
	$data[contents] = nl2br(htmlspecialchars($data[contents]));
	$data[point] = sprintf( "%0d", $data[point]);

	$query = "select b.goodsnm,b.img_s,c.price
	from
		".GD_GOODS." b
		left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
	where
		b.goodsno = '" . $data[goodsno] . "'";
	list( $data[goodsnm], $data[img_s], $data[price] ) = $db->fetch($query);

	if ($data[attach] == 1) {
		$data_path = "../data/review";
		for($ii=0;$ii<10;$ii++){
			if($ii == 0){
				$name = 'RV'.sprintf("%010s", $data[sno]);
			} else {
				$name = 'RV'.sprintf("%010s", $data[sno]).'_'.$ii;
			}
			if(file_exists($data_path.'/'.$name)){
				$data[image] .= "<img src='".$data_path."/".$name."'><br>";
			}
		}
	}
	else $data[image] = '';

	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );

### ���ø� ���
$tpl->print_('tpl');

?>