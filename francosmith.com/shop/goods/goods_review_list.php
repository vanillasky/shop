<?
$noDemoMsg = $indexLog = 1;
include "../_header.php";
include "../lib/page.class.php";

### �����Ҵ�
$goodsno = $_GET[goodsno];
if ( file_exists( dirname(__FILE__) . '/../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';
if(!$cfg['reviewWriteAuth']) $cfg['reviewWriteAuth'] = (isset($cfg['reviewAuth_W']) && !$cfg['reviewAuth_W']) ? "free" : ""; // �۾��� ����

### �ı� ���ε� �̹��� ���� ����
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}
### ������ ����
if(!$cfg['reviewListCnt']) $cfg['reviewListCnt'] = 5;

### ��ǰ ����
$pg = new Page($_GET[page],$cfg['reviewListCnt']);
$pg->field = "b.m_no, b.m_id, b.level, a.sno, a.goodsno, a.subject, a.contents, a.point, a.regdt, a.emoney, a.name, b.name as m_name,a.parent,a.attach";
$pg->setQuery($db_table="".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no",$where=array("goodsno='$goodsno'"),$sort="parent desc, ( case when parent=a.sno then 0 else 1 end ) asc,regdt desc");
$pg->exec();
$totcnt = $pg -> recode[total]; //��ü �ۼ�

//DB Cache ��� 141030
$usedCache= true;
if ($_GET[page] != null && $_GET[page] >= 2) { //1page�� ĳ�� ���
	$usedCache= false;
} else {
	$dbCache = Core::loader('dbcache')->setLocation('goodsview_review');
	$loop = $dbCache->getCache($pg->query);
	if (!$loop) $usedCache= false;
}

if ($usedCache === false) {
	$res = $db->query($pg->query);
	while ($data=$db->fetch($res,1)) {

		if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
			$data = validation::xssCleanArray($data, array(
				validation::DEFAULT_KEY => 'text',
				'contents' => array('html', 'ent_noquotes'),
				'subject' => array('html', 'ent_noquotes'),
			));
		}

		$data['idx'] = $pg->idx--;

		$data[type] = ( $data[sno] == $data[parent] ? 'Q' : 'A' );

		$data[authmodify] = $data[authdelete] = $data[authreply] = 'Y'; # �����ʱⰪ

		if ( empty($cfg['reviewWriteAuth']) || isset($sess) || !empty($data[m_no]) ){ // ȸ������ or ȸ�� or �ۼ���==ȸ��
			$data[authmodify] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
			$data[authdelete] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
		}

		list( $data[replecnt] ) = $db->fetch("select count(*) from ".GD_GOODS_REVIEW." where sno != parent and parent='$data[sno]'");
		$data[authdelete] = ( $data[replecnt] > 0 ? 'N' : $data[authdelete] ); # ��� �ִ� ��� ���� �Ұ�

		if ( $data[sno] == $data[parent] ){

			if ( empty($cfg['reviewWriteAuth']) ){ // ȸ������
				$data[authreply] = ( isset($sess) ? 'Y' : 'N' );
			}
		}else $data[authreply] = 'N';

		// ���� : list( $level ) = $db->fetch("select level from ".GD_MEMBER." where m_no!='' and m_no='{$data[m_no]}'");
		if ( $data[level] == '100' && $adminicon ) $data[m_id] = $data[name] = "<img src='../data/skin/{$cfg['tplSkin'] }/{$adminicon}' border=0>";
		if ( empty($data[m_no]) ) $data[m_id] = $data[name]; // ��ȸ����

		$data[contents] = nl2br(htmlchars_ech($data[contents]));

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


		$data[point] = sprintf( "%0d", $data[point]);

		$loop[] = $data;
	}
	//1page�� ĳ��
	if ((!$_GET[page] || $_GET[page] == "1") && $dbCache) {
		$dbCache->setCache($pg->query, $loop);
	}
}

$tpl->assign( 'pg', $pg );
$tpl->assign( 'review_count', $totcnt );
### ���ø� ���
$tpl->print_('tpl');
?>
