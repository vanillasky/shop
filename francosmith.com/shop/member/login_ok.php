<?php
include("../lib/library.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

// ������ �α��ο��� �����ں��� �������� üũ
$_ref = parse_url($_SERVER['HTTP_REFERER']);
if (preg_match('/admin\/login\/login\.php$/',$_ref['path']) || $_POST['adm_login']) {
	$alCert = Core::loader('adminLoginCert');
	$alStat = $alCert->loginStatus();
	if ($alStat == 'failure') {
		msg('������ �޴��� ���� ������ ���� �ϼ���.', '../admin/login/adm_login_cert.php');
	}
}

if ($_POST['mode']=="guest"){ // ��ȸ�� �ֹ���Ϻ���
	$ordno = (string)$_POST['ordno'];
	$nameOrder = (string)$_POST['nameOrder'];

	// ���� ��ȿ�� ����
	$validation_check = array(
		'ordno'=>array('require'=>true,'pattern'=>'/^[0-9]+$/'),
		'nameOrder'=>array('require'=>true),
	);
	$chk_result = array_value_cheking($validation_check,array('ordno'=>$ordno,'nameOrder'=>$nameOrder));

	if(count($chk_result)) {
		msg("�ֹ��ڸ�� �ֹ���ȣ�� ��ġ�ϴ� �ֹ��� �������� �ʽ��ϴ�",-1);
	}

	// �ֹ���ȣ�� �ֹ��ڸ����� ��ȸ
	$query = $db->_query_print("select ordno from gd_order where ordno=[s] and nameOrder=[s]",$ordno,$nameOrder);
	$result = $db->_select($query);
	if($result[0]['ordno']) {
		setcookie("guest_ordno",$ordno,0,'/');
		setcookie("guest_nameOrder",$nameOrder,0,'/');
		go('../mypage/mypage_orderlist.php');
	}
	else {
		msg("�ֹ��ڸ�� �ֹ���ȣ�� ��ġ�ϴ� �ֹ��� �������� �ʽ��ϴ�",-1);
	}
	exit;
}
else if ($_POST['mode']=="adult_guest") {

	include "../conf/fieldset.php";

	if ( $realname[useyn] == 'y' && !empty($realname[id]) ){

		// ���� ó�� �� ������ �̵��� �Ʒ� ���Ͽ��� ó�� ��.
		require_once( "./realname/RNCheckRequest.php" );
		exit;
	}
	else {
		msg("�������� ���񽺸� ����ϰ� ���� �ʽ��ϴ�.",-1);
	}
}
else { // ȸ�� �α��� �κ�
	if ($_GET['SOCIAL_CODE']) {
		include dirname(__FILE__).'/../lib/SocialMember/SocialMemberServiceLoader.php';
		$socialMemberService = SocialMemberService::getMember($_GET['SOCIAL_CODE']);
		$result = $socialMemberService->login();
		$_POST['returnUrl'] = $_GET['return_url'];
	}
	else {
		$m_id = (string)$_POST['m_id'];
		$password = (string)$_POST['password'];
		$result = $session->login($m_id, $password);
	}

	if($result!==true) {
		if($result==='NOT_FOUND') {
			msg('���̵� �Ǵ� ��й�ȣ �����Դϴ�',-1);
		}
		elseif($result==='NOT_ACCESS') {
			msg('������ �� ����Ʈ���� ���ε��� �ʾ� �α����� ���ѵ˴ϴ�.',-1);
		}
		if($result==='NOT_VALID') {
			msg('���̵� �Ǵ� ��й�ȣ �Է� ���� �����Դϴ�',-1);
		}
		exit;
	}

	// �����ں��� ���� �α��� ó��
	if ($alStat == 'success') {
		$_SESSION['alcAccess'] = md5(crypt(''));
	}

	//�⼮üũ���� ó��
	if(!preg_match('/admin/',$_POST['returnUrl'])) {
		$attd = Core::loader('attendance');
		$result = $attd->login_check($session->m_no);
		if($result) {

			msg($attd->get_check_message($result));
		}
	}


	### aceī���� ó�� �κ�
	$Acecounter = Core::loader('Acecounter');
	$Acecounter->get_common_script();
	$Acecounter->member_login($session->m_id);
	if($Acecounter->scripts){
		echo $Acecounter->scripts;
	}

	## �α��� ���� ���
	member_log( $session->m_id );

	## � üũ
	if ($session->level > 80) {
		include(SHOPROOT.'/proc/shop_warning_msg.php');
	}

	// �����̼� �з� ����
	$todayshop = Core::loader('todayshop');
	if ($todayshop->auth() && $todayshop->cfg['useTodayShop'] == 'y') {
		$ts_interest = unserialize(stripslashes($todayshop->cfg['interest']));
		if ($ts_interest['use'] == 'y') {
			// ���� �з��� ��ϵǾ� �ִ°�
			list($sc) = $db->fetch("SELECT category FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE m_id = '".$session->m_id."' AND category <> '' ");

			if (!$sc) $ext_param = '&interest=1';
			else	 {
				$ext_param = '&category='.$sc;
				$_POST['returnUrl'] = isset($_POST['returnUrl']) ? str_replace('today_goods.php','today_list.php',$_POST['returnUrl']) : str_replace('today_goods.php','today_list.php',$_SERVER['HTTP_REFERER']);
			}

		}
	}

	// �н����� ���� ķ����
	$info_cfg = $config->load('member_info');
	$_ref = parse_url($_SERVER['HTTP_REFERER']);
	if (( preg_match('/admin\/login\/login\.php$/',$_ref['path']) || preg_match('/member\/login\.php$/',$_ref['path']) || preg_match('/main\/intro_adult\.php$/',$_ref['path']) || preg_match('/main\/intro_member\.php$/',$_ref['path']) )
		&&
		( $info_cfg['campaign_use'] && is_file('../data/skin/'.$cfg['tplSkin'].'/member/password_campaign.htm') )) {

		$show = true;

		// ������ �����ϱ�
		if ($show && $_COOKIE['campaign_disregarded_date']) {

			$operate = sprintf('%+d day', $info_cfg['campaign_next_term']);
			$tmp = strtotime($_COOKIE['campaign_disregarded_date']);
			$tmp = strtotime($operate ,$tmp);

			if ($tmp > time()) {
				$show = false;
			}

		}

		if ($show && ($tmp = (int)strtotime($_SESSION['member']['password_moddt'])) > 0) {

			$operate = sprintf('%+d %s', $info_cfg['campaign_period_value'], ($info_cfg['campaign_period_type'] == 'd' ? 'day' : 'month'));
			$tmp = strtotime($operate ,$tmp);

			if ($tmp > time()) {
				$show = false;
			}

		}

		if ($show) {
			$ext_param .= '&ori_returnUrl='.($_POST['returnUrl'] ? urlencode($_POST['returnUrl']) : urlencode($_SERVER['HTTP_REFERER']));
			$_POST['returnUrl'] = str_replace('member/login_ok.php','member/password_campaign.php', $_SERVER['REQUEST_URI']);
		}

	}
}

$auth_date = getAdultAuthDate($session->m_id);
$auth_date = $auth_date['auth_date'];
$current_date = date("Y-m-d");
$auth_period = strtotime("+1 years", strtotime($auth_date)); 
$auth_period = date("Y-m-d", $auth_period);

if(!$_POST['returnUrl']) { $_POST['returnUrl'] = $_SERVER['HTTP_REFERER']; }

if(strpos($_SERVER['HTTP_REFERER'], 'main/intro_adult.php') && ($auth_date == '0000-00-00' || $current_date > $auth_period) && ((int)($session->level) < 80) ){
	go($cfg[rootDir].'/main/intro_adult_login.php?returnUrl=' . urlencode($_POST['returnUrl']) . ($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : ''));
}
else{
	if(($auth_date != '0000-00-00' && $current_date < $auth_period) || ((int)($session->level) > 80)){
		$_SESSION['adult'] = 1;
	}
	$div = explode("/",$_POST['returnUrl']);
	if (preg_match("/http/",$div[0]) && in_array($div[count($div)-2],array("member","mypage"))) $_POST['returnUrl'] = "../main/index.php";
	$_POST['returnUrl'] = preg_match('/\?/',$_POST['returnUrl']) ? $_POST['returnUrl'].$ext_param : $_POST['returnUrl'].'?'.$ext_param;

	if (preg_match('/(^\.\.\/)|(^\/shop\/)/',$_POST['returnUrl'])) {
		$_POST['returnUrl'] = preg_replace('/(^\.\.\/)|(^\/shop\/)/', '', $_POST['returnUrl']);
		$_POST['returnUrl'] = $sitelink->link($_POST['returnUrl'],'regular');
	}
	go($_POST['returnUrl']);
}

?>