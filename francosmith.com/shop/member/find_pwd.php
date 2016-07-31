<?
include "../_header.php";
include "../conf/fieldset.php";

$config = Core::loader('config');
$hpauth = Core::loader('Hpauth');
$dormant = Core::loader('dormant');
$shopConfig = $config->load('config');
$hpauthRequestData = $hpauth->getAuthRequestData();

### ȸ����������
if ($sess) {
	msg("������ �α��� ���Դϴ�.", -1);
}

unset($fld['per']['resno']);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

if ($_POST['act'] == 'Y') {

	$now = time();

	// ���� ���� ���� ����
	if ($_POST['rncheck'] != "ipin" && $_POST['rncheck'] != "hpauthDream") {

		$query = sprintf(
		"select mb.m_id, mb.email, mb.mobile, otp.token, otp.expire from ".GD_MEMBER." AS mb left join ".GD_OTP." as otp ON mb.m_id = otp.m_id AND otp.expire > '%s' where mb.m_id = '%s' and mb.name='%s'"
		, date('Y-m-d H:i:s', $now)
		, $db->_escape($_POST['srch_id'])
		, $db->_escape($_POST['srch_name'])
		);

		if ( $checked['useField']['email'] ) {
			$query .=  " AND mb.email='".$db->_escape($_POST['srch_mail'])."'";
		}
		$_mb = $db->fetch($query, 1);

		//�޸�ȸ�� ��ȸ
		if(!$_mb){
			$_mb = $dormant->findPasswordUser('name', $_POST);
		}
	}
	else {

		$query = sprintf("select mb.m_id, mb.email, mb.mobile, otp.token, otp.expire from ".GD_MEMBER." AS mb left join ".GD_OTP." as otp ON mb.m_id = otp.m_id AND otp.expire > '%s' where mb.dupeinfo = '%s'", date('Y-m-d H:i:s', $now), $db->_escape($_POST['dupeinfo']));

		$_mb = $db->fetch($query, 1);

		//�޸�ȸ�� ��ȸ
		if(!$_mb){
			$_mb = $dormant->findPasswordUser('dupeinfo', $_POST);
		}
	}

	if ($_mb['m_id']) {

		// �ű� ���
		if (is_file($tpl->template_dir.'/member/find_pwd_choice.htm')) {

			$info_cfg = $config->load('member_info');

			$otp = Core::loader('gd_otp');

			$_token = $otp->getToken();

			if ( empty($_mb['token']) || $_token != $_mb['token']) {

				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));

				$_mb['token'] = $_token;

				$query = sprintf("
				INSERT INTO ".GD_OTP." SET
					m_id = '%s',
					token = '%s',
					expire = '%s'
				",
				$_mb['m_id'],
				$_mb['token'],
				date('Y-m-d H:i:s',
				strtotime('+1 hour', $now)) // �ѽð��� �����.
				);

				$db->query($query);
				$_mb['token'] = $_token;
			}
			elseif ($_mb['expire'] < date('Y-m-d H:i:s', $now)) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				msg('��ȿ�Ⱓ�� ����Ǿ����ϴ�. �ٽ� �õ��� �ּ���.', $_SERVER['PHP_SELF']);
				exit;
			}

			$sf = Core::loader('stringFormatter');

			$temp_email = $temp_mobile = '';
			if (($_email = $sf->get($_mb['email'], 'email')) !== false) {

				// �̸��� id �� ���ݸ�(�ִ� 4��)�����ָ� 8~12 �ڸ��� ���� ���̷� * padding �Ͽ� ������.
				$_tmp = explode('@', $_email);
				$_email_id = $_tmp[0];
				$_show = ceil(strlen($_email_id) / 2);
				$_show = $_show > 4 ? 4 : $_show;
				$_size = mt_rand(8, 12);
				$temp_email = str_pad(substr($_email_id, 0, $_show), $_size, '*', STR_PAD_RIGHT).'@'.$_tmp[1];
				unset($_show, $_size, $_tmp, $_email);
			}

			if ($info_cfg['finder_use_mobile']) {

				if(file_exists(dirname(__FILE__)."/../conf/sms.cfg.php")){
					$_file = file(dirname(__FILE__)."/../conf/sms.cfg.php");
					$_sms_point = trim($_file[1]);
				}else{
					$_sms = Core::loader('Sms');
					$_sms_point = $_sms->smsPt;
				}

				if ( (int)$_sms_point > 0 && ($_mobile = $sf->get($_mb['mobile'], 'dial', '-')) !== false) {
					// �޹�ȣ ����
					$_tmp = explode('-', $_mobile);
					$_tmp[2] = '****';
					$temp_mobile = implode('-', $_tmp);
				}

			}

			$tpl->assign('temp_email', $temp_email);
			$tpl->assign('temp_mobile', $temp_mobile);
			$tpl->assign('token', $_mb['token']);
			$tpl->assign('m_id', $_mb['m_id']);
			$tpl->define(array('tpl'=>'member/find_pwd_choice.htm'));
			unset($_mb, $_tmp);
		}
		else {

			{ // �ӽ� �н�����
				$memberpass = str_replace("l", "", md5(uniqid(mt_rand(), true)));
				$memberpass = substr($memberpass, 0, 10);
				$db->query("UPDATE ".GD_MEMBER." SET password=password('$memberpass') WHERE m_id='" . $_POST['srch_id'] . "'");
				$dormant->findPasswordUser('originalChange', $_POST);
			}

			list( $name , $mobile ) = $db->fetch("select name, mobile from ".GD_MEMBER." where m_id='" . $_POST['srch_id'] . "'");
			if(!$name && !$mobile){
				list( $name , $mobile ) = $dormant->findPasswordUser('originalChangeLoad', $_POST);
			}

			if ( $_POST[srch_mail] != '' ){ ### ��й�ȣã�����
				$modeMail = 11;
				include "../lib/automail.class.php";
				include "../conf/config.php";
				$automail = new automail();
				$automail->_set($modeMail,$_POST[srch_mail],$cfg);
				$automail->_assign('name',$name);
				$automail->_assign('id',$_POST['srch_id']);
				$automail->_assign('password',$memberpass);
				$automail->_send();
			}

			### ��й�ȣã�� SMS ����
			$dataSms['name']	= $name;
			$dataSms['password']= $memberpass;
			$GLOBALS['dataSms']	= $dataSms;
			sendSmsCase('id_pass',$mobile);

			msg( $msg='������ ���۵Ǿ����ϴ�.', '../member/login.php' );
		}
	}
	else {
		msg('����������� �������� �ʽ��ϴ�.', $_SERVER['PHP_SELF']);
		exit;
	}
}
$tpl->assign('ipinyn', ( empty($ipin[id]) ? 'n' : empty($ipin[useyn]) ? 'n' : $ipin[useyn]));
$tpl->assign('niceipinyn', empty($ipin[nice_useyn]) ? 'n' : $ipin[nice_useyn]);
$tpl->assign('hpauthDreamyn', $hpauthRequestData['useyn']);
$tpl->assign('hpauthDreamcpid', $hpauthRequestData['cpid']);
$tpl->print_('tpl');
?>