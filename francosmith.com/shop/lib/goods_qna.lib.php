<?php
### qna ����üũ
function goods_qna_chkAuth($data){
	global $cfg,$sess;

	### �������� ���
	if($data['notice'])return array('N','N','Y');

	### �Խù� ���� ����
	$qna_auth = unserialize($_SESSION['qna_auth']);
	if(!$qna_auth) $qna_auth = array();

	### ��ȸ��ó��
	if ( empty($data['m_no']) ) $data['m_id'] = $data['name']; // ��ȸ����

	### ���Ѽ���
	$data['authmodify'] = $data['authdelete'] = $data['authview'] = 'Y'; # �����ʱⰪ
	if(!$cfg['qnaSecret']){
		$data['secret'] = 0;
	}
	if($data['secret'] == '1'){
		$data['authmodify'] = $data['authdelete'] = $data['authview'] = 'N'; # �����ʱⰪ
		$data['authview'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
	}

	### �Խù� ���� üũ
	if ( empty($cfg['qnaWriteAuth']) || isset($sess) || !empty($data['m_no']) ){ // ȸ������ or ȸ�� or �ۼ���==ȸ��
		$data['authmodify'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
		$data['authdelete'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
	}

	### ���� �Խù� ���� üũ
	if ( empty($cfg['qnaWriteAuth']) || isset($sess) || !empty($data['parent_m_no']) ){ // ȸ������ or ȸ�� or �ۼ���==ȸ��
		if( isset($sess) && $sess['m_no'] == $data['parent_m_no']) $data['authview'] = 'Y';
	}

	### ���� �Խù� ���� üũ
	if(in_array($data['sno'],$qna_auth)) $data['authmodify'] = $data['authdelete'] = $data['authview'] = 'Y';
	if(in_array($data['parent'],$qna_auth)) $data['authview'] = 'Y';

	### �������ϰ��
	if(isset($sess) && $sess['level'] > 79){
		$data['authmodify'] = $data['authdelete'] = $data['authview'] =  'Y';
	}
	return array($data['authmodify'],$data['authdelete'],$data['authview']);
}

### �亯
function goods_qna_answer($sno,$parent,$secret,$notice=''){
	global $db;
	$type = 'Q';
	if($sno != $parent){
		$query = "select m_no,secret from ".GD_GOODS_QNA." where sno='".$parent."' limit 1";
		list($parent_m_no,$parent_secret) = $db->fetch($query);
		if($secret == 0 && $parent_secret == 1) $secret = 1;
		$type = 'A';
	}
	if($notice) $type = 'N';
	return array($parent_m_no,$secret,$type);
}

### �亯 ����
function goods_qna_answer_yn($sno){
	global $db;

	$query = "SELECT COUNT(sno) as cnt_answer FROM ".GD_GOODS_QNA." WHERE parent='".$sno."' AND sno != '".$sno."'";
	list($cnt_answer) = $db->fetch($query);

	return $cnt_answer;
}
?>