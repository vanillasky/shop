<?
$noDemoMsg = $indexLog = 1;
include "../_header.php";
include "../lib/page.class.php";
include "../lib/goods_qna.lib.php";

### �����Ҵ�
$goodsno = $_GET[goodsno];
if ( file_exists( dirname(__FILE__) . '/../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';
if(!$cfg['qnaWriteAuth']) $cfg['qnaWriteAuth'] = (isset($cfg['qnaAuth_W']) && !$cfg['qnaAuth_W']) ? "free" : ""; // �۾��� ����

### ������ ����
if(!$cfg['qnaListCnt']) $cfg['qnaListCnt'] = 5;

### ��ǰ �������亯
$pg = new Page($_GET[page],$cfg['qnaListCnt']);
$pg -> field = "b.m_no, b.m_id,b.name as m_name,a.*";
$where[]="goodsno='$goodsno'";
$where[]="notice!='1'";
$pg->setQuery($db_table=GD_GOODS_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no",$where,$sort="parent desc, ( case when parent=a.sno then 0 else 1 end ) asc, regdt desc");
$pg->exec();
$totcnt = $pg -> recode[total]; //��ü �ۼ�

//DB Cache ��� 141030
$usedCache= true;
if ($_GET[page] != null && $_GET[page] >= 2) { //1page�� ĳ�� ���
	$usedCache= false;
} else {
	$dbCache = Core::loader('dbcache')->setLocation('goodsview_qna');
	$loop = $dbCache->getCache($pg->query);
	if (!$loop) $usedCache= false;
}

if ($usedCache === false) {
	$res = $db->query($pg->query);
	while ($data=$db->fetch($res)) {
		if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
			$data = validation::xssCleanArray($data, array(
				validation::DEFAULT_KEY => 'text',
				'contents' => array('html', 'ent_noquotes'),
				'subject'=>'html',
			));
		}

		### ���� üũ
		list($data['parent_m_no'],$data['secret'],$data['type']) = goods_qna_answer($data['sno'],$data['parent'],$data['secret']);

		### ����üũ
		if(!$cfg['qnaSecret']) $data['secret'] = 0;
		list($data['authmodify'],$data['authdelete'],$data['authview']) = goods_qna_chkAuth($data);

		### ��б� ������
		$data['secretIcon'] = 0;
		if($data['secret'] == '1') $data['secretIcon'] = 1;

		### ����ó��
		$data['idx'] = $pg->idx--;

		### ������ ������ ���
		list( $level ) = $db->fetch("select level from ".GD_MEMBER." where m_no!='' and m_no='".$data['m_no']."'");
		if ( $level == '100' && $adminicon ) $data['m_id'] = $data['name'] = "<img src='../data/skin/".$cfg['tplSkin']."/".$adminicon."' border=0>";

		$loop[] = $data;
	}
	//1page�� ĳ��
	if ((!$_GET[page] || $_GET[page] == "1") && $dbCache) {
		$dbCache->setCache($pg->query, $loop);
	}
}
$tpl->assign( 'pg', $pg );
$tpl->assign( 'qna_count', $totcnt );
### ���ø� ���
$tpl->print_('tpl');
?>