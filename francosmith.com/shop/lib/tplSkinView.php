<?

/* ���� �������̰� REFERER�� ������ ����� ��� ó�� */
if( $sess['level'] >= 80 && ($_SERVER['HTTP_REFERER'] || $_GET['gd_preview'] == 1) ){
	if( eregi( "/admin/" , $_SERVER['HTTP_REFERER'] ) || $_GET['gd_preview'] == 1 ){
		if( $_GET['tplSkin'] ){
			$_SESSION['tplSkin']	= $_GET['tplSkin'];
		}
		elseif($_GET['tplSkinToday']) {
			$_SESSION['tplSkinToday']	= $_GET['tplSkinToday'];
		}
		else{
			unset($_SESSION['tplSkin']);
			unset($_SESSION['tplSkinToday']);
		}
	}
}

/* �̸����� ������ ���°�� ���� ���� */
if( !$_SESSION['tplSkin'] && !$_SESSION['tplSkinToday']){
	unset($_SESSION['tplSkin']);
	unset($_SESSION['tplSkinToday']);
}

/* PC ����� �Ѱ�� ���� ���� */
if( isset($_GET['pc']) ){
	unset($_SESSION['tplSkin']);
	unset($_SESSION['tplSkinToday']);
}

/* ������ ��Ų�� �̸����� ��Ų���� ���� */
if( $_SESSION['tplSkin'] ){
	// ��Ʈ�δ� �⺻ ���������� ��ȯ
	if( ( !$cfg['tplSkinWork'] || $cfg['tplSkinWork'] != $cfg['tplSkin'] ) && $cfg['tplSkin'] != $_SESSION['tplSkin'] ){
		$cfg['introUseYN'] = "N";
	}
	$cfg['tplSkin'] = $_SESSION['tplSkin'];
}

/* ������ �����̼� ��Ų�� �̸����� ��Ų���� ���� */
if( $_SESSION['tplSkinToday'] ){
	// ��Ʈ�δ� �⺻ ���������� ��ȯ
	if( ( !$cfg['tplSkinTodayWork'] || $cfg['tplSkinTodayWork'] != $cfg['tplSkinToday'] ) && $cfg['tplSkinToday'] != $_SESSION['tplSkinToday'] ){
		$cfg['introUseYN'] = "N";
	}
	$cfg['tplSkinToday'] = $_SESSION['tplSkinToday'];
}

$_tmp['folder']	= explode("/",str_replace($_SERVER['DOCUMENT_ROOT'],"", $_SERVER['SCRIPT_FILENAME']));
$_tmp['defaultFolder'] = $_SERVER['DOCUMENT_ROOT']."/".$_tmp['folder'][1];
if(is_file( $_tmp['defaultFolder']."/conf/design_basic_".$cfg['tplSkin'].".php")){
	include $_tmp['defaultFolder']."/conf/design_basic_".$cfg['tplSkin'].".php";
}
?>