<?

include "../_header.php";

### �����Ҵ�
if ( file_exists( dirname(__FILE__) . '/../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';

### ����Ÿ
$data=$db->fetch("select b.m_no, b.m_id, b.name as m_name, a.* from ".GD_GOODS_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.sno='" . $_GET['sno'] . "'");
$data['authmodify'] = $data['authdelete'] = $data['authreply'] = 'Y'; # �����ʱⰪ

if ( empty($cfg['qnaWriteAuth']) || isset($sess) || !empty($data['m_no']) ){ // ȸ������ or ȸ�� or �ۼ���==ȸ��
	$data['authmodify'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
	$data['authdelete'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
}

list( $answer_cnt,$tsecret ) = $db->fetch("select count(*),sum(secret) from ".GD_GOODS_QNA." where sno != parent and parent='{$data['sno']}'");
$data['authdelete'] = ( $answer_cnt > 0 ? 'N' : $data['authdelete'] ); # ��� �ִ� ��� ���� �Ұ�

if ( empty($cfg['qnaWriteAuth']) ){ // ȸ������
	$data['authreply'] = ( isset($sess) ? 'Y' : 'N' );
}

list( $level ) = $db->fetch("select level from ".GD_MEMBER." where m_no!='' and m_no='{$data['m_no']}'");
if ( $level == '100' && $adminicon ) $data['m_id'] = $data['name'] = "<img src='../data/skin/{$cfg['tplSkin'] }/{$adminicon}' border='0'>";
if ( empty($data['m_no']) ) $data['m_id'] = $data['name']; // ��ȸ����

### �α���üũ
if(!isset($sess) && $data['m_no']){
	msg('�α������ּ���!');
	echo("<script>opener.parent.location.href='../member/login.php';self.close();</script>");
}

### �Խñ� ����
if(($data['secret'] == '1' || $tsecret > 0) && $cfg['qnaSecret']){
	$qna_auth = unserialize($_SESSION['qna_auth']);
	if(!$qna_auth) $qna_auth = array();
	$chk_res = false;

	### ��й�ȣ ����
	if(in_array($data['sno'],$qna_auth)) $chk_res = true;

	### ȸ������
	if($data['sno'] && $data['sno']==$sess['m_no']) $chk_res = true;

	### ����������
	if($sess && $sess > 79) $chk_res = true;

	if(!$chk_res){
		msg('���ٱ����� �����ϴ�.');
		echo("<script>self.close();</script>");
		exit;
	}
}

### ��ǰ
$query = "select b.goodsnm,b.img_s,c.price
from
	".GD_GOODS." b
	left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
where
	b.goodsno = '" . $data[goodsno] . "'";
list( $data['goodsnm'], $data['img_s'], $data['price'] ) = $db->fetch($query);


### ���
$res = $db->query("select distinct a.sno, a.goodsno, a.subject, a.contents, a.regdt, a.name, b.m_no, b.m_id, b.name as m_name from ".GD_GOODS_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.sno != a.parent and parent='{$data['sno']}' order by regdt desc");
while ($r_data=$db->fetch($res)){

	$r_data['authmodify'] = $r_data['authdelete'] = 'Y'; # �����ʱⰪ

	if ( empty($cfg['qnaWriteAuth']) || isset($sess) || !empty($r_data['m_no']) ){ // ȸ������ or ȸ�� or �ۼ���==ȸ��
		$r_data['authmodify'] = ( isset($sess) && $sess['m_no'] == $r_data['m_no'] ? 'Y' : 'N' );
		$r_data['authdelete'] = ( isset($sess) && $sess['m_no'] == $r_data['m_no'] ? 'Y' : 'N' );
	}

	list( $level ) = $db->fetch("select level from ".GD_MEMBER." where m_no!='' and m_no='{$r_data['m_no']}'");
	if ( $level == '100' && $adminicon ) $r_data['m_id'] = $r_data['name'] = "<img src='../data/skin/{$cfg['tplSkin'] }/{$adminicon}' border=0>";
	if ( empty($r_data['m_no']) ) $r_data['m_id'] = $r_data['name']; // ��ȸ����

	$r_data['contents'] = $r_data['contents'];
	$loop[] = $r_data;
}


### ���ø� ���
$tpl->print_('tpl');

?>