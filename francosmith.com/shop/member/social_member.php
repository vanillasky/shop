<?php

include dirname(__FILE__).'/../lib/library.php';
include dirname(__FILE__).'/../lib/SocialMember/SocialMemberServiceLoader.php';

$_MODE = $_REQUEST['MODE'];
$_SOCIAL_CODE = $_REQUEST['SOCIAL_CODE'];

if (isset($_REQUEST['user_identifier'])) {
	SocialMemberService::setPersistentData('user_identifier', $_REQUEST['user_identifier']);
}

$socialMember = SocialMemberService::getMember($_SOCIAL_CODE);

switch ($_MODE) {
	case 'login':
		if (!$socialMember || $socialMember->hasError()) {
			msg('�ý��� ��ְ� �߻��Ͽ����ϴ�.\r\n�����ͷ� �����Ͽ��ֽñ� �ٶ��ϴ�.', 'close');
		}
		if (isset($_REQUEST['error'])) {
			msg('���̽��� �ۿ��� ����� ���������� ��������\r\nȹ������ ���Ͽ� �α��� �� �� �����ϴ�.', 'close');
		}
		if (isset($_REQUEST['error_code'])) {
			msg('���̽��� �ۿ��� ����� ���������� ��������\r\nȹ������ ���Ͽ� �α��� �� �� �����ϴ�.', 'close');
		}

		SocialMemberService::updateIdentifierIfChanged($socialMember);
		if (SocialMemberService::existsMember($socialMember)) {
			echo '<script type="text/javascript">window.opener.location.href = "./login_ok.php?SOCIAL_CODE='.$_SOCIAL_CODE.'&return_url='.$_REQUEST['return_url'].'";self.close();</script>';
		}
		else {
			echo '<script type="text/javascript">window.opener.location.href = "./join.php?MODE=social_member_join&SOCIAL_CODE='.$_SOCIAL_CODE.'";self.close();</script>';
		}
		break;

	case 'connect':
		if (!$socialMember || $socialMember->hasError()) {
			msg('�ý��� ��ְ� �߻��Ͽ����ϴ�.\r\n�����ͷ� �����Ͽ��ֽñ� �ٶ��ϴ�.', 'close');
		}
		if (isset($_REQUEST['error'])) {
			msg('���̽��� �ۿ��� ����� ���������� ��������\r\nȹ������ ���Ͽ� ���� �� �� �����ϴ�.', 'close');
		}
		if (isset($_REQUEST['error_code'])) {
			msg('���̽��� �ۿ��� ����� ���������� ��������\r\nȹ������ ���Ͽ� �α��� �� �� �����ϴ�.', 'close');
		}

		SocialMemberService::updateIdentifierIfChanged($socialMember);
		if ($socialMember->isConnected()) {
			echo '<script type="text/javascript">window.opener.socialMemberConnectCallback("FACEBOOK", "ERR_ALREADY_EXISTS");self.close();</script>';
		}
		else {
			$socialMember->connect($sess['m_no']);
			echo '<script type="text/javascript">window.opener.socialMemberConnectCallback("'.$_SOCIAL_CODE.'");self.close();</script>';
		}
		break;

	case 'disconnect':
		if (!$socialMember || $socialMember->hasError()) {
			msg('�ý��� ��ְ� �߻��Ͽ����ϴ�.\r\n�����ͷ� �����Ͽ��ֽñ� �ٶ��ϴ�.', 'close');
		}
		$result = false;
		list($password, $connected_sns) = $db->fetch('SELECT password, connected_sns FROM '.GD_MEMBER.' WHERE m_no='.$sess['m_no']);
		if (strlen($password) < 1 && count(explode(',', $connected_sns)) === 1) {
			$result = 'ERR_PASSWORD_NOT_EXISTS';
		}
		else if ($socialMember) {
			$disconnectResult = $socialMember->disconnect($sess['m_no']);
			if ($disconnectResult) {
				if (SocialMemberService::getPersistentData('social_code') === $_SOCIAL_CODE) SocialMemberService::expirePersistentData('social_code');
				$result = 'SUCCESS';
			}
			else {
				$result = 'ERR_SYSTEM_ERROR';
			}
		}
		else {
			$result = 'ERR_SYSTEM_ERROR';
		}
		echo '<script type="text/javascript">window.parent.socialMemberDisconnectCallback("'.$result.'");</script>';
		break;

	case 'join':

		include dirname(__FILE__).'/../conf/fieldset.php';
		$dormant = Core::loader('dormant');

		// ������� ����
		if (strlen($_POST['birthday']) === 8) { // ������ & �޴�������
			$_POST['birthday'] = $_POST['birthday'];
		}
		else if ($_POST['birth']) { // ������� �����Է�
			$_POST['birthday'] = trim(sprintf("%04d%02d%02d",$_POST['birth_year'],$_POST['birth'][0],$_POST['birth'][1]));
		}

		// ȸ�����忡�� ��14�� �̸� ȸ������ ������
		if (file_exists(dirname(__FILE__).'/../lib/memberUnder14Join.class.php') === true) {
			$mUnder14 = Core::loader('memberUnder14Join');
			$under14Code = $mUnder14->joinIndb($_POST['birthday']);
			$under14 = 0;
			if ( $under14Code == 'rejectJoin' ) { // ��14�� �̸� ȸ������ �ź�
				msg('�� 14�� �̸��� ��� ȸ�������� ������� �ʽ��ϴ�.');
				exit;
			}
			else if ( $under14Code == 'undecidableRejectJoin' ) { // ��14�� �̸� �ǴܺҰ��� ȸ������ �ź�
				msg('��14�� �̸��� Ȯ���� �� �����Ƿ� ȸ�������� ������� �ʽ��ϴ�. �����ڿ��� ������ �ּ���.');
				exit;
			}
			else if ( $under14Code == 'adminStatus' ) { // ��14�� �̸� ȸ������ ������ ���� �� ����
				$joinset['status'] = 0;
				$under14 = 1;
			}
			else if ( $under14Code == 'undecidableAdminStatus' ) { // ��14�� �̸� �ǴܺҰ��� ȸ������ ������ ���� �� ����
				$joinset['status'] = 0;
			}
			else if ( $under14Code == 'over14' ) { // ��14�� �̻�
				$under14 = 2;
			}

			// 'under14' �ʵ� ���翩��
			$fRes = $db->_select("SHOW COLUMNS FROM ".GD_MEMBER." WHERE field='under14'");
			if ($fRes[0]['Field'] != '') {
				$under14FieldYn = 'Y';
			}
		}

		if (!$socialMember || $socialMember->hasError()) {
			msg('�ý��� ��ְ� �߻��Ͽ����ϴ�.\r\n�����ͷ� �����Ͽ��ֽñ� �ٶ��ϴ�.');
			exit;
		}
		if (SocialMemberService::existsMember($socialMember)) {
			msg('�̹� ��ϵǾ��ִ� ȸ���Դϴ�.\r\n�ڵ��α��� ó�� �˴ϴ�.', './login_ok.php?SOCIAL_CODE='.$_SOCIAL_CODE.'&return_url=../main/index.php', 'parent');
		}

		if ($socialMember->getLoginStatus() !== true) {
			msg(SocialMemberService::getServiceName($socialMember->getCode()).'���� ������ ����Ǿ����ϴ�.\r\n�ٽ� �����Ͽ��ֽñ� �ٶ��ϴ�.', './join.php', 'parent');
		}
		// �⺻ ȸ���׷� ����
		if (!$joinset['grp']) {
			$joinset['grp'] = 1;
		}

		// ���̵� �Է����� üũ
		if (preg_match('/^[\xa1-\xfea-zA-Z0-9_-]{4,20}$/',$_POST['m_id']) == 0) {
			msg('���̵� �Է� ���� �����Դϴ�');
			exit;
		}

		// ���Ұ� ���̵� üũ
		if (find_in_set(strtoupper($_POST['m_id']),strtoupper($joinset['unableid']))) {
			msg('��� �Ұ����� ���̵��Դϴ�');
			exit;
		}

		// ���̵� �ߺ� üũ
		list($chk) = $db->fetch('SELECT m_id FROM '.GD_MEMBER.' WHERE m_id="'.$_POST['m_id'].'"');
		if ($chk) {
			msg('�̹� ��ϵ� ���̵��Դϴ�');
			exit;
		}

		// ȸ�� �⺻���� ����
		$column = array();
		$column['m_id'] = $_POST['m_id'];
		$column['name'] = $_POST['name'];
		$column['email'] = $_POST['email'];
		$column['dupeinfo'] = $_POST['dupeinfo'];
		$column['rncheck'] = $_POST['rncheck'];
		$column['pakey'] = $_POST['pakey'];
		$column['sex'] = $_POST['sex'];
		$column['foreigner'] = $_POST['foreigner'];
		$column['mailling'] = $_POST['mailling'] ? 'y' : 'n';
		$column['sms'] = $_POST['sms'] ? $_POST['sms'] : 'n';
		$column['private1'] = $_POST['private1'] ? $_POST['private1'] : 'n';
		$column['private2'] = $_POST['private2'] ? $_POST['private2'] : 'n';
		$column['private3'] = $_POST['private3'] ? $_POST['private3'] : 'n';
		$column['status'] = $joinset['status'];
		if ($under14FieldYn == 'Y') $column['under14'] = $under14;
		$column['emoney'] = $joinset['emoney'];
		$column['level'] = $joinset['grp'];
		$column['LPINFO'] = $_COOKIE['LPINFO'];
		$column['regdt'] = date('Y-m-d H:i:s');
		$column['calendar'] = ($_POST['calendar'] == 'l' ? 'l' : 's');
		if (strlen($_POST['birthday']) === 8) {
			$column['birth_year'] = substr($_POST['birthday'], 0, 4);
			$column['birth'] = substr($_POST['birthday'], 4, 4);
		}
		if (strlen($_POST['mobile']) === 10) {
			$column['mobile'] = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '${1}-${2}-${3}', $_POST['mobile']);
		}
		else if (strlen($_POST['mobile']) === 11) {
			$column['mobile'] = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/', '${1}-${2}-${3}', $_POST['mobile']);
		}

		// dupeinfo �ߺ�üũ
		if ($_POST['dupeinfo']) {
			list($chk) = $db->fetch('SELECT COUNT(*) AS cnt FROM '.GD_MEMBER.' WHERE dupeinfo = "'.$_POST['dupeinfo'].'"');
			if ($chk < 1) {
				$chk = $dormant->getCountDupeinfoFromDormant($_POST['dupeinfo']);
			}
			if ($chk > 0) {
				msg('�̹� ȸ������� ���Դϴ�');
				exit;
			}
		}

		// ȸ���簡�ԱⰣ üũ
		if ($joinset['rejoin'] > 0) {
			$rejoindt = date('Ymd', time() - (($joinset['rejoin'] - 1) * 86400));
			if ($_POST['dupeinfo']) {
				list($lastRegdt) = $db->fetch('SELECT regdt FROM '.GD_LOG_HACK.' WHERE dupeinfo="'.$_POST['dupeinfo'].' AND DATE_FORMAT(regdt, "%Y%m%d") >= '.$rejoindt.' ORDER BY regdt DESC LIMIT 1');
				if ($_POST['dupeinfo'] && $chk) {
					msg('ȸ��Ż�� �� '.$joinset['rejoin'].'�� ���� �簡���� �� �����ϴ�.\\nȸ������ '.$lastRegdt.'�� Ż���ϼ̽��ϴ�.');
					exit;
				}
			}
		}

		// ȸ�����
		$query = $db->_query_print('INSERT INTO '.GD_MEMBER.' SET [cv]', $column);
		$db->query($query);
		$m_no = $db->lastID();

		//�߰������׸� ���ǿ���
		if (is_array($_POST['consent']) && count($_POST['consent'])>0){
			foreach ($_POST['consent'] as $key => $value){
				$query = "INSERT INTO ".GD_MEMBER_CONSENT." SET m_no = '".$m_no."', consent_sno = '".$key."', consentyn = '".$value."', regdt=now()";
				$db->query($query);
			}
		}

		// �ҼȰ��� ����
		$socialMember->connect($m_no);

		// ������ ���� �Է�
		$code = codeitem('point');
		$query = 'INSERT INTO '.GD_LOG_EMONEY.' SET m_no = '.$m_no.', emoney = '.$joinset['emoney'].', memo = "'.$code['01'].'", regdt = now()';
		$db->query($query);

		// ȸ�����Ը���
		if ($_POST['email'] && $cfg['mailyn_10'] == 'y') {
			$modeMail = 10;
			include '../lib/automail.class.php';
			$automail = new automail();
			$automail->_set($modeMail, $_POST['email'], $cfg);
			$automail->_assign($_POST);
			$automail->_send();
		}

		// ȸ������SMS
		if ($column['mobile']) {
			sendSmsCase('join', $column['mobile']);
		}

		// ȸ���������� �߱�
		$date = date('Y-m-d H:i:s');
		$query = 'SELECT * FROM '.GD_COUPON.' WHERE coupontype = 2 AND (( priodtype = 1 ) OR ( priodtype = 0 AND sdate <= "'.$date.'" AND edate >= "'.$date.'" ))';
		$res = $db->query($query);
		$couponCnt = 0;
		while ($data = $db->fetch($res)) {
			$query = 'SELECT COUNT(a.sno) FROM '.GD_COUPON_APPLY.' AS a LEFT JOIN '.GD_COUPON_APPLY.'member b ON a.sno=b.applysno WHERE a.couponcd="'.$data['couponcd'].'" AND b.m_no = '.$m_no;
			list($cnt) = $db->fetch($query);
			if (!$cnt) {
				$newapplysno = new_uniq_id('sno',GD_COUPON_APPLY);
				$query = 'INSERT INTO '.GD_COUPON_APPLY.' SET
							sno = "'.$newapplysno.'",
							couponcd = "'.$data['couponcd'].'",
							membertype = "2",
							member_grp_sno = "",
							regdt = now()';
				$db->query($query);

				$query = 'INSERT INTO '.GD_COUPON_APPLY.'member SET m_no="'.$m_no.'", applysno ="'.$newapplysno.'"';
				$db->query($query);
				$couponCnt++;
			}
		}

		// ���� �� ���� ó��
		if ($joinset['status']=='0') {
			msg("������ ������ ����ó���˴ϴ�");
			go($sitelink->link('index.php'), 'parent');
		}

		// �Ҽȷα��� ó��
		$result = $socialMember->login();

		// ���� �߱� �޽���
		if ($couponCnt) {
			msg('ȸ�� ���� ������ �߱� �Ǿ����ϴ�!');
		}

		go($sitelink->link('member/join_ok.php'), 'parent');
		break;

	case 'confirm_social_member':
		if (!$socialMember || $socialMember->hasError()) {
			msg('�ý��� ��ְ� �߻��Ͽ����ϴ�.\r\n�����ͷ� �����Ͽ��ֽñ� �ٶ��ϴ�.', 'close');
		}
		if (isset($_REQUEST['error'])) {
			msg('���̽��� �ۿ��� ����� ���������� ��������\r\nȹ������ ���Ͽ� ���� �� �� �����ϴ�.', 'close');
		}
		if (isset($_REQUEST['error_code'])) {
			msg('���̽��� �ۿ��� ����� ���������� ��������\r\nȹ������ ���Ͽ� �α��� �� �� �����ϴ�.', 'close');
		}

		SocialMemberService::updateIdentifierIfChanged($socialMember);
		if ($socialMember->getLoginStatus() === true && $socialMember->isSameMember()) {
			$_SESSION['sess'][$_GET['session_status_key']] = 1;
			echo '<script type="text/javascript">opener.location.reload();self.close();</script>';
		}
		else {
			unset($_SESSION['sess'][$_GET['session_status_key']]);
			msg('������ �����Ͽ����ϴ�.');
			echo '<script type="text/javascript">opener.location.reload();self.close();</script>';
		}
		break;
}