<?

/* ���� �������̰� REFERER�� ������ ����� ��� ó�� */
if( $sess['level'] >= 80 && ($_SERVER['HTTP_REFERER'] || $_GET['gd_preview'] == 1) ){
	if( eregi( "/admin/" , $_SERVER['HTTP_REFERER'] ) || $_GET['gd_preview'] == 1 ){
		if( $_GET['tplSkin'] ){
			$_SESSION['tplSkin']	= $_GET['tplSkin'];
		}else{
			unset($_SESSION['tplSkin']);
		}
	}
}

/* �̸����� ������ ���°�� ���� ���� */
if( !$_SESSION['tplSkin'] ){
	unset($_SESSION['tplSkin']);
}

/* ������ ��Ų�� �̸����� ��Ų���� ���� */
if( $_SESSION['tplSkin'] ){
	$cfgMobileShop['tplSkinMobile'] = $_SESSION['tplSkin'];
}

$_tmp['folder']	= explode("/",str_replace($_SERVER['DOCUMENT_ROOT'],"", $_SERVER['SCRIPT_FILENAME']));
$_tmp['defaultFolder'] = $_SERVER['DOCUMENT_ROOT']."/".$_tmp['folder'][1];
?>