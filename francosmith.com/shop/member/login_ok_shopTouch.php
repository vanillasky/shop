<?php
include("../lib/library.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
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
		go('/shopTouch/shopTouch_myp/orderlist.php');
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
		msg("�������� ���񽺸� ����ϰ� ���� �ʽ��ϴ�.");
	}
}
else { // ȸ�� �α��� �κ�
	$m_id = (string)$_POST['m_id'];
	$password = (string)$_POST['password'];

	$result = $session->login($m_id,$password);

	if($result !== true) {
		if($result==='NOT_FOUND') {
			msg('���̵� �Ǵ� ��й�ȣ �����Դϴ�', -1);
		}
		elseif($result==='NOT_ACCESS') {
			msg('������ �� ����Ʈ���� ���ε��� �ʾ� �α����� ���ѵ˴ϴ�.', -1);
		}
		elseif($result==='NOT_VALID') {
			msg('���̵� �Ǵ� ��й�ȣ �Է� ���� �����Դϴ�', -1);
		}
		exit;
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
}


$mem_query = $db->_query_print('SELECT name FROM '.GD_MEMBER.' WHERE m_id=[s]', $session->m_id);
$mem_res = $db->_select($mem_query);
$mem_name = $mem_res[0]['name'];

if($_POST['save_id']=='y') {
    setcookie('save_id',$_POST['m_id'],time()+3600*24*5,'/');
}
else {
	setcookie('save_id','',time(),'/');
}

if($_POST['save_pw']=='y') {
    setcookie('save_pw',base64_encode($_POST['password']),time()+3600*24*5,'/');
}
else {
	setcookie('save_pw','',time(),'/');
}

if($_POST['returnUrl']) {
	msg('�α��� ����', 'vumall://vercoop.com/login_success?close=false&usr_nm='.urlencode(iconv('euc-kr', 'utf-8', $mem_name)).'&redirect='.urlencode('http://'.$_SERVER['HTTP_HOST'].$_POST['returnUrl']));
}
else {
	msg('�α��� ����', 'vumall://vercoop.com/login_success?close=true&usr_nm='.urlencode(iconv('euc-kr', 'utf-8', $mem_name)));
}


?>
