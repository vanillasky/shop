<?

include "../_header.php";

### ����üũ
if ($_GET['mode'] == "add_review" && $cfg['reviewAuth_W'] && $cfg['reviewAuth_W'] > $sess['level']) msg("�̿��ı� �ۼ� ������ �����ϴ�", "close");
if ($_GET['mode'] == "reply_review" && $cfg['reviewAuth_P'] && $cfg['reviewAuth_P'] > $sess['level']) msg("�̿��ı� �亯 ������ �����ϴ�", "close");

### �����Ҵ�
$mode		= $_GET[mode];
$goodsno	= $_GET[goodsno];
$sno		= $_GET[sno];
$referer	= $_GET[referer];

### �ı� ���ε� �̹��� ���� ����
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}
### ��ǰ ����Ÿ
$query = "
select
	goodsnm,img_s,price
from
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1'
where
	a.goodsno='$goodsno'
";
$goods = $db->fetch($query,1);

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$goods = validation::xssCleanArray($goods, array(
		validation::DEFAULT_KEY => 'html',
		'price'=>'text',
	));
}

### ȸ������
if($mode != 'mod_review' && $sess['m_no']){
	list($data['name'],$data['nickname']) = $db-> fetch("select name,nickname from ".GD_MEMBER." where m_no='".$sess['m_no']."' limit 1");
	if($data['nickname'])$data['name'] = $data['nickname'];
} //end if

### ��ǰ ����
if($mode == 'mod_review'){
	$query = "select a.sno, b.m_no, b.m_id, a.subject, a.contents, a.point, a.name, a.attach from ".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.sno='$sno'";
	$data = $db->fetch($query,1);
	
	// @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ��
	if($sess['m_no'] && $sess[level] < 80 && $sess['m_no'] != $data['m_no']) {
		msg('������ �ۼ��� ��ǰ�ı⸸ �����Ͻ� �� �ֽ��ϴ�.', 'close');
	}

	$data['point'] = array( $data['point'] => 'checked' );

	$file_arr = '';
	$data[image] = '';
	if ($data[attach] == 1) {
		if($cfg['reviewFileNum'] > 0){
			$upload_folder = "../data/review/";
			// ���� ���� ���� �ִ� �� ��ŭ(������ �ִ� 10������ ����)
			for ($i=0; $i<10; $i++){
				if($i == 0){
					$upload_file = 'RV'.sprintf("%010s", $data[sno]);
				} else {
					$upload_file = 'RV'.sprintf("%010s", $data[sno]).'_'.$i;
				}
				if(file_exists($upload_folder.$upload_file)){
					$file_arr[$i] = "<input type=\"hidden\" name=\"file_ori[]\" value=\"$i\" /><input type=\"checkbox\" name=\"del_file[$i]\" value=\"on\" class=linebg /> ����<img src='".$upload_folder.$upload_file."' width='40px' height='40px' align=absmiddle>";
				}
			}
			// �迭�� �߰� �� ä���
			$max_arr = end(array_keys($file_arr));
			for($mi=0;$mi<=$max_arr;$mi++){
				if(!$file_arr[$mi]){
					$file_arr[$mi] = "";
				}
			}
			// �迭 Ű ����
			ksort($file_arr);
		} else {
			$data[image] = '<img src="../data/review/'.'RV'.sprintf("%010s", $data[sno]).'" width="20" style="border:1 solid #cccccc" onclick=popupImg("../data/review/'.'RV'.sprintf("%010s", $data[sno]).'","../") class=hand>';
		}
	}
}
else {
	$data['m_id'] = $sess['m_id'];
}

// ���� ������ ó��
$data['subject'] = ($_POST['subject']) ? $_POST['subject'] : $data['subject'];
$data['contents'] = ($_POST['contents']) ? $_POST['contents'] : $data['contents'];
if($_POST['point']) $data['point'] = array( $_POST['point'] => 'checked' );

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$data = validation::xssCleanArray($data, array(
		validation::DEFAULT_KEY => 'text',
		'subject' => 'html',
		'contents' => 'html',
	));
}

### ���ø� ���
$tpl->print_('tpl');

?>