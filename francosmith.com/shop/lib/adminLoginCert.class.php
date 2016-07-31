<?
/**
 * Admin Login Cert ���̺귯��
 * @author pr
 */
class adminLoginCert
{
	var $useLoginCert;

	function adminLoginCert()
	{
		// �����ں��� ��������
		$cfgfile = dirname(__FILE__).'/../conf/config.admin_login_cert.php';
		if(file_exists($cfgfile)) @include $cfgfile;
		if ($admLoginCertCfg['use'] == 'Y') {
			$this->useLoginCert = true;
		}
		else {
			$this->useLoginCert = false;
		}

		// ���� IP üũ
		if ($admLoginCertCfg['unCheckGdip'] != 'Y') {
			if ($this->checkGdip() === true) {
				$this->useLoginCert = false;
			}
		}
	}

	/**
	 * ���� IP üũ
	 * @return bool
	 */
	function checkGdip()
	{
		// 1. IP ����
		$myip = $_SERVER['REMOTE_ADDR'];
		$ex_myip = explode('.', $myip);
		$myip_ABC = $ex_myip[0] . '.' . $ex_myip[1] . '.' . $ex_myip[2] . '.'; // ����IP�� ABCŬ����
		$myip_Dclass = $ex_myip[3]; // ����IP�� DŬ����

		// 2. IP ����
		$result = '';
		$mipFile = dirname(__FILE__).'/../lib/login_ok_manager.php';
		if(file_exists($mipFile)) @include $mipFile;
		$m_ip = (array)$m_ip;

		if (in_array($myip, $m_ip) === true) { // ������ IP Ȯ��
			$result = 'ok';
		}
		else { // ���IP �뿪 Ȯ��
			$m_cnt = count($m_ip);
			for ($i = 0; $i < $m_cnt; $i++) {
				$tmp = explode('~', $m_ip[$i]);
				$regIP_Dclass_end = trim($tmp[1]); // �뿪�� ��ȣ

				if ($regIP_Dclass_end != '') { // �뿪���� ���
					$ex_regip = explode('.', trim($tmp[0]));
					$regip_ABC = $ex_regip[0] . '.' . $ex_regip[1] . '.' . $ex_regip[2] . '.'; // ���IP�� ABCŬ����
					$regip_Dclass = $ex_regip[3]; // ���IP�� DŬ����

					if ($result != 'ok' ){
						if ($myip_ABC == $regip_ABC) { // ���IP�� ����IP�� CŬ�������� ��
							if( ($regip_Dclass <= $myip_Dclass) && ($myip_Dclass <= $regIP_Dclass_end) ) { // ���IP �뿪 ��
								$result = 'ok';
								break;
							}
						}
					}
				}
			}
		}

		// 3. ��û����� ���� ����� ��ȯ
		if ($result === 'ok') {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * �α��ο��� �����ں��� ��������
	 *
	 * @return string (�����ڵ� : unused : �̻��, success : ������, failure : ���� �ȵ�)
	 *
	 */
	function loginStatus()
	{
		// 1. �����ں��� ���� ��뿩��
		if ($this->useLoginCert !== true) return 'unused';

		// 2. OTP ����ó ��� ������
		if (count($this->getOtpContants()) < 1) return 'unused';

		// 3. SMS ����Ʈ �ܿ�(�ּ� 1����Ʈ) üũ
		if (isset($_SESSION['alcCompare']) !== true) {
			$sms = Core::loader('Sms');
			if ((int)$sms->smsPt < 1) return 'unused';
		}

		// 4. ����Ȯ���� �ϰ� �����α��� ���� ���
		if (isset($_SESSION['alcCompare']) === true && isset($_SESSION['alcAccess']) !== true) {
			return 'success';
		}
		else {
			return 'failure';
		}
	}

	/**
	 * ������� ������ �����ں��� ���� �α��λ���
	 *
	 * @return string (�����ڵ� : unused : �̻��, success : ���� �α��ε�, failure : ���� �α��� �ȵ�)
	 *
	 */
	function inStatus()
	{
		// 1. �����ں��� ���� ��뿩��
		if ($this->useLoginCert !== true) return 'unused';

		// 2. OTP ����ó ��� ������
		if (count($this->getOtpContants()) < 1) return 'unused';

		// 3. SMS ����Ʈ �ܿ�(�ּ� 1����Ʈ) üũ
		if (isset($_SESSION['alcCompare']) !== true) {
			$sms = Core::loader('Sms');
			if ((int)$sms->smsPt < 1) return 'unused';
		}

		// 4. ����Ȯ�� �� �����α����� ���
		if (isset($_SESSION['alcCompare']) === true && isset($_SESSION['alcAccess']) === true) {
			return 'success';
		}
		else {
			unset($_SESSION['alcCompare'], $_SESSION['alcAccess']);
			return 'failure';
		}
	}

	/**
	 * OTP ����ó ���
	 *
	 * @param string $asterisk ��ǥ����
	 * @return array
	 *
	 */
	function getOtpContants($asterisk='N')
	{
		global $db;

		$dormant = Core::loader('dormant');

		$contacts = array();
		$res = $db->query("select aoc.aoc_sno, aoc.aoc_mobile, aoc.aoc_m_no, mb.level, mb.dormant_regDate from gd_admin_otp_contact as aoc inner join ".GD_MEMBER." as mb on aoc.aoc_m_no = mb.m_no order by aoc_regdt asc");

		while( $row = $db->fetch($res, 1) ) {
			if($row['dormant_regDate'] != '0000-00-00 00:00:00'){
				$row['level'] = $dormant->getDormantInfo('level', $row);
			}
			if($row['level'] < 80){
				continue;
			}
			if ($asterisk == 'Y') {
				$len = strlen(substr($row['aoc_mobile'], 3, -4));
				$row['aoc_mobile'] = substr_replace($row['aoc_mobile'], str_repeat('*', $len), 3, -4);
			}
			$contacts[] = $row;
		}
		return $contacts;
	}

	/**
	 * OTP ����ó ����
	 *
	 * @param string $aocSno ContactŰ
	 * @return array or bool
	 *
	 */
	function getOtpContact($aocSno)
	{
		global $db;

		$dormant = Core::loader('dormant');

		$query = sprintf("select aoc.aoc_mobile, mb.name, mb.m_id, mb.m_no from gd_admin_otp_contact as aoc inner join ".GD_MEMBER." as mb on aoc.aoc_m_no = mb.m_no where mb.level >= 80 and aoc.aoc_sno = '%s'", $db->_escape($aocSno));
		if (($_aoc = $db->fetch($query, 1)) === false) {
			$query = $dormant->getOtpContactQuery($aocSno);
			if (($_aoc = $db->fetch($query, 1)) === false) {
				return false;
			}
		}
		return $_aoc;
	}

	/**
	 * Login OTP ����
	 *
	 * @param string $aocSno ContactŰ
	 * @param string $token ��ū
	 * @return string
	 *
	 */
	function sendLoginOtp($aocSno, $token)
	{
		global $db, $cfg;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // ��ū

		// ��ū ����
		if ($token != $_token) {
			return '0002'; // �߸��� ����
		}

		// OTP ����ó ����
		if (($_aoc = $this->getOtpContact($aocSno)) === false) {
			return '0001'; // ����� ���� ������
		}

		// ��ū ����
		$now = time();
		$query = sprintf("select mb.m_id, otp.token, otp.expire from ".GD_MEMBER." AS mb left join ".GD_OTP." as otp ON mb.m_id = otp.m_id AND otp.expire > '%s' where mb.m_no = '%s'", date('Y-m-d H:i:s', $now), $db->_escape($_aoc['m_no']));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			if (empty($_mb['token']) || $_token != $_mb['token']) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));

				$_mb['token'] = $_token;

				$query = sprintf("INSERT INTO ".GD_OTP." SET m_id = '%s', token = '%s', expire = '%s'", $_mb['m_id'], $_mb['token'], date('Y-m-d H:i:s', strtotime('+3 minute', $now))); // ��ȿ�Ⱓ 3��
				$db->query($query);
			}
		}
		else {
			return '0001'; // ����� ���� ������
		}

		// OTP ����
		$authNum = $gdOtp->getOTP();
		$sf = Core::loader('stringFormatter');

		// �޴��� ����
		if ($_aoc['aoc_mobile']) {
			// �߼۰Ǽ�
			$sms = Core::loader('Sms');
			$sms_sendlist = $sms->loadSendlist();
			if ((int)$sms->smsPt < 1) {
				return '0008'; // SMS �ܿ� ����Ʈ ����
			}

			$GLOBALS['dataSms']['authNum'] = $authNum;

			$msg = parseCode('[{shopName}]'."\n".'������� ������ȣ�� {authNum} �Դϴ�. ��Ȯ�� �Է����ּ���.');
			
			$sms->log($msg,$_aoc['aoc_mobile'],'',1);
			$sms_sendlist->setSimpleInsert($_aoc['aoc_mobile'], $sms->smsLogInsertId, '');
			$sms->send($msg,$_aoc['aoc_mobile'],$cfg['smsRecall']);
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
		else {
			return '0005'; // �޴�����ȣ ���� ����ġ
		}

		// ������Ʈ
		$query = sprintf("update ".GD_OTP." set otp = '%s', auth = 0 where m_id = '%s' AND token = '%s'", $authNum, $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
		$db->query($query);

		return '0000';
	}

	/**
	 * Login OTP Ȯ��
	 *
	 * @param string $otp OTP
	 * @param string $aocSno ContactŰ
	 * @param string $token ��ū
	 * @return string
	 *
	 */
	function compareLoginOtp($otp, $aocSno, $token)
	{
		global $db;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // ��ū

		// ��ū ����
		if ($token != $_token) {
			return '0002'; // �߸��� ����
		}

		// OTP ����ó ����
		if (($_aoc = $this->getOtpContact($aocSno)) === false) {
			return '0001'; // ����� ���� ������
		}

		// üũ
		$query = sprintf("select mb.name, mb.m_id, otp.token, otp.otp, otp.expire, otp.auth from ".GD_OTP." as otp inner join ".GD_MEMBER." as mb on otp.m_id = mb.m_id where mb.m_no = '%s' AND otp.token > '' AND otp.token = '%s'", $db->_escape($_aoc['m_no']), $db->_escape($token));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			// ��ȿ�Ⱓ(3��) üũ
			if ($_mb['expire'] < date('Y-m-d H:i:s')) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0003';
			}

			// ������ȣ ���뿩�� üũ
			if ($_mb['auth'] == '1') {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0004';
			}

			// OTP üũ
			if ($_mb['otp'] == $otp) {
				$query = sprintf("update ".GD_OTP." set auth = 1 where m_id = '%s' AND token = '%s'", $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
				$db->query($query);
				$_SESSION['alcCompare'] = md5(crypt(''));
				return '0000';
			}
			else {
				return '0006'; // ������ȣ ����
			}
		}
		else {
			return '0001'; // ����� ���� ������
		}
	}

	/**
	 * Regit OTP ����
	 *
	 * @param string $mobile �޴�����ȣ
	 * @param string $token ��ū
	 * @return string
	 *
	 */
	function sendRegitOtp($mobile, $token)
	{
		global $db, $cfg, $sess;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // ��ū

		// ��ū ����
		if ($token != $_token) {
			return '0002'; // �߸��� ����
		}

		// ��ū ����
		$now = time();
		$query = sprintf("select mb.m_id, mb.mobile, otp.token, otp.expire from ".GD_MEMBER." AS mb left join ".GD_OTP." as otp ON mb.m_id = otp.m_id AND otp.expire > '%s' where mb.m_id = '%s'", date('Y-m-d H:i:s', $now), $db->_escape($sess['m_id']));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			if (empty($_mb['token']) || $_token != $_mb['token']) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));

				$_mb['token'] = $_token;

				$query = sprintf("INSERT INTO ".GD_OTP." SET m_id = '%s', token = '%s', expire = '%s'", $_mb['m_id'], $_mb['token'], date('Y-m-d H:i:s', strtotime('+3 minute', $now))); // ��ȿ�Ⱓ 3��
				$db->query($query);
			}
		}
		else {
			return '0001'; // ����� ���� ������
		}

		// OTP ����
		$authNum = $gdOtp->getOTP();
		$sf = Core::loader('stringFormatter');

		// �޴��� ����
		if ($mobile) {
			// �߼۰Ǽ�
			$sms = Core::loader('Sms');
			$sms_sendlist = $sms->loadSendlist();
			if ((int)$sms->smsPt < 1) {
				return '0008'; // SMS �ܿ� ����Ʈ ����
			}

			$GLOBALS['dataSms']['authNum'] = $authNum;

			$msg = parseCode('[{shopName}]'."\n".'������� ������ȣ�� {authNum} �Դϴ�. ��Ȯ�� �Է����ּ���.');

			$sms->log($msg,$mobile,'',1);
			$sms_sendlist->setSimpleInsert($mobile, $sms->smsLogInsertId, '');
			$sms->send($msg,$mobile,$cfg['smsRecall']);
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
		else {
			return '0005'; // �޴�����ȣ ���� ����ġ
		}

		// ������Ʈ
		$query = sprintf("update ".GD_OTP." set otp = '%s', auth = 0 where m_id = '%s' AND token = '%s'", $authNum, $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
		$db->query($query);

		return '0000';
	}

	/**
	 * Regit OTP Ȯ��
	 *
	 * @param string $otp OTP
	 * @param string $token ��ū
	 * @return string
	 *
	 */
	function compareRegitOtp($otp, $token)
	{
		global $db, $sess;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // ��ū

		// ��ū ����
		if ($token != $_token) {
			return '0002'; // �߸��� ����
		}

		// üũ
		$query = sprintf("select mb.name, mb.m_id, otp.token, otp.otp, otp.expire, otp.auth from ".GD_OTP." as otp inner join ".GD_MEMBER." as mb on otp.m_id = mb.m_id where otp.m_id = '%s' AND otp.token > '' AND otp.token = '%s'", $db->_escape($sess['m_id']), $db->_escape($token));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			// ��ȿ�Ⱓ(3��) üũ
			if ($_mb['expire'] < date('Y-m-d H:i:s')) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0003';
			}

			// ������ȣ ���뿩�� üũ
			if ($_mb['auth'] == '1') {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0004';
			}

			// OTP üũ
			if ($_mb['otp'] == $otp) {
				$query = sprintf("update ".GD_OTP." set auth = 1 where m_id = '%s' AND token = '%s'", $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
				$db->query($query);
				return '0000';
			}
			else {
				return '0006'; // ������ȣ ����
			}
		}
		else {
			return '0001'; // ����� ���� ������
		}
	}

	/**
	 * ���� ����
	 *
	 * @param array $data ��뿩�� ��
	 *
	 */
	function setAdminLoginCert($data)
	{
		$val = array(
			'use' =>($data['use']=='Y') ? 'Y' : 'N'
		);

		$qfile = Core::loader('qfile');
		$qfile->open(dirname(__FILE__).'/../conf/config.admin_login_cert.php');
		$qfile->write("<? \n");
		$qfile->write("\$admLoginCertCfg = array( \n");
		foreach ($val as $k=>$v){
			if($v!='')$qfile->write("'$k' => '$v', \n");
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod(dirname(__FILE__).'/../conf/config.admin_login_cert.php',0707);

		if ($data['use'] == 'Y') { // '�����'���� ������ �α׾ƿ� ���� �������� ����ó��
			$_SESSION['alcAccess'] = md5(crypt(''));
			$_SESSION['alcCompare'] = md5(crypt(''));
		}
	}

	/**
	 * OTP ����ó ����
	 *
	 * @param array $aocSnos ContactŰ
	 *
	 */
	function delContact($aocSnos)
	{
		global $db;
		foreach ($aocSnos as $aoc_sno) {
			$db->query("delete from gd_admin_otp_contact WHERE aoc_sno='$aoc_sno'");
		}
	}

	/**
	 * OTP ����ó ���
	 *
	 * @param array $data ������
	 *
	 */
	function regitContact($data)
	{
		global $db;
		$data['mobile'] = trim(str_replace('-', '', $data['mobile'])); // ������(-) ����
		$query = sprintf("INSERT INTO gd_admin_otp_contact SET aoc_m_no = '%s', aoc_mobile = '%s', aoc_regdt = now()", $data['mno'], $data['mobile']);
		$db->query($query);

		if ($this->useLoginCert === true) { // ��뿩�ΰ� '�����'�� ��� �α׾ƿ� ���� �������� ����ó��
			$_SESSION['alcAccess'] = md5(crypt(''));
			$_SESSION['alcCompare'] = md5(crypt(''));
		}
	}
}
?>