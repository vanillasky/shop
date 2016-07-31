<?php
/*
 * IP ���� ���� ���� (������ IP ���� ����, ���θ� IP ���� ���� ����)
 * @author artherot @ godosoft development team.
 */
class IPAccessRestriction
{
	public		$_thisIP;								// ���� ������ ������
	private		$_adminConfigFile;						// ������ IP ���� ���� ����
	private		$_userConfigFile;						// ���θ� IP �������� ���� ����
	private		$_exceptionConfigFile;					// ���� ó��
	private		$_accessKey;							// üũ�� ���� Ű ����
	private		$_cookieExpire			= 60;			// ���� �ð� (��)
	private		$_qfile;
	public		$_cookieDebug			= array();		// �����

	/**
	 * ������
	 */
	public function __construct()
	{
		$this->_thisIP					= $_SERVER['REMOTE_ADDR'];
		$this->_adminConfigFile			= SHOPROOT.'/conf/config.admin_access_ip.php';
		$this->_userConfigFile			= SHOPROOT.'/conf/config.user_ip_access_restriction.php';
		$this->_exceptionConfigFile		= SHOPROOT.'/lib/login_ok_manager.php';
		$this->_accessKey				= encode($this->_thisIP.'^'.time(), 1);
	}

	/**
	 * ������ IP �������� ���� ����
	 * @return  array $getData ���� ����
	 */
	public function getAdminAccessIP()
	{
		// �⺻ ������
		if (is_file($this->_adminConfigFile)) {
			include $this->_adminConfigFile;
			$getData['set_ip_permit']		= $set_ip_permit;
			$getData['set_regist_ip']		= $set_regist_ip;
			$getData['get_file_time']		= filemtime($this->_adminConfigFile);
		} else {
			$getData['set_ip_permit']		= '0';
			$getData['set_regist_ip']		= array();
			$getData['get_file_time']		= 0;
		}

		return $getData;
	}

	/**
	 * ���θ� IP �������� ���� ����
	 * @return  array $getData ���� ����
	 */
	public function getUserAccessIP()
	{
		// �⺻ ������
		if (is_file($this->_userConfigFile)) {
			include $this->_userConfigFile;
			$getData['user_ip_access_restriction']	= $user_ip_access_restriction;
			$getData['user_ip_address']				= $user_ip_address;
			$getData['restriction_page']			= $restriction_page;
			$getData['restriction_page_url']		= $restriction_page_url;
			$getData['restriction_page_skin']		= $restriction_page_skin;
			$getData['get_file_time']				= filemtime($this->_userConfigFile);
		} else {
			$getData['user_ip_access_restriction']	= 'N';
			$getData['user_ip_address']				= array();
			$getData['restriction_page']			= '404';
			$getData['restriction_page_url']		= '';
			$getData['restriction_page_skin']		= '';
			$getData['get_file_time']				= 0;
		}

		return $getData;
	}

	/**
	 * IP �������� ���� ���� ����
	 * @return  string $result (NO_DATA , NO_IP , true)
	 */
	public function saveAccessIP()
	{
		// ���� ������ ���� Class
		$this->_qfile	= Core::loader('qfile');

		$adminResult	= $this->saveAdminAccessIP();
		if($adminResult !== true ) {
			return $adminResult;
		}

		$userResult		= $this->saveUserAccessIP();
		if($userResult !== true ) {
			return $userResult;
		}

		return true;
	}

	/**
	 * ������ IP �������� ���� ���� ����
	 * @return  string $result (NO_DATA , NO_IP , true)
	 */
	private function saveAdminAccessIP()
	{
		//�������� �� �� Ȥ�� 0,1�� �ƴѰ�� ���â ���
		if ($_POST['set_ip_permit'] == '' || ($_POST['set_ip_permit'] != 0 && $_POST['set_ip_permit'] != 1) ) {
			return 'NO_DATA';
		}

		// ������IP���������� ��������� �����ϰ� IP�� ������� �ʾ����� ���â ���
		if($_POST['set_ip_permit'] == '1' && count($_POST['admin_ip_address']) < 1){
			return 'NO_IP';
		}

		// ���Ͽ� ����
		$this->_qfile->open($this->_adminConfigFile);
		$this->_qfile->write('<?php'.PHP_EOL);
		$this->_qfile->write('$set_ip_permit	= \''.$_POST['set_ip_permit'].'\';'.PHP_EOL);

		if ($_POST['set_ip_permit'] == '1') {
			$set_ip = array();

			if (is_array($_POST['admin_ip_address'])){
				$_POST['admin_ip_address']	= array_filter($_POST['admin_ip_address']);		//�迭�� �� ����.
				$_POST['admin_ip_address']	= array_unique($_POST['admin_ip_address']);		//�ߺ�IP����

				foreach($_POST['admin_ip_address'] as $ip_address)
				{
					if (!$ip_address == trim($ip_address)){
						continue;
					}
					$set_ip[]	= $ip_address;
				}
			}

			$this->_qfile->write('$set_regist_ip	= array('.PHP_EOL);

			foreach ($set_ip as $key => $val )
			{
				$this->_qfile->write($key.'	=> \''.$val.'\','.PHP_EOL);
			}

			$this->_qfile->write(');'.PHP_EOL);
		}

		$this->_qfile->write('?>');
		$this->_qfile->close();
		chmod($this->_adminConfigFile, 0707);

		return true;
	}

	/**
	 * ���θ� IP �������� ���� ���� ����
	 * @return  string $result (NO_DATA , NO_IP , true)
	 */
	private function saveUserAccessIP()
	{
		//�������� �� �� Ȥ�� 0,1�� �ƴѰ�� ���â ���
		if (empty($_POST['user_ip_access_restriction']) === true || in_array($_POST['user_ip_access_restriction'], array('Y','N')) === false ) {
			return 'NO_DATA';
		}

		// ������IP���������� ��������� �����ϰ� IP�� ������� �ʾ����� ���â ���
		if($_POST['user_ip_access_restriction'] == 'Y' && count($_POST['user_ip_address']) < 1){
			return 'NO_IP';
		}

		// ���Ͽ� ����
		$this->_qfile->open($this->_userConfigFile);
		$this->_qfile->write('<?php'.PHP_EOL);
		$this->_qfile->write('$user_ip_access_restriction	= \''.$_POST['user_ip_access_restriction'].'\';'.PHP_EOL);
		$this->_qfile->write('$restriction_page	= \''.$_POST['restriction_page'].'\';'.PHP_EOL);
		$this->_qfile->write('$restriction_page_url	= \''.$_POST['restriction_page_url'].'\';'.PHP_EOL);
		$this->_qfile->write('$restriction_page_skin	= \''.$_POST['restriction_page_skin'].'\';'.PHP_EOL);

		if ($_POST['user_ip_access_restriction'] == 'Y') {
			$set_ip	= array();

			if (is_array($_POST['user_ip_address'])){
				$_POST['user_ip_address']	= array_filter($_POST['user_ip_address']);		//�迭�� �� ����.
				$_POST['user_ip_address']	= array_unique($_POST['user_ip_address']);		//�ߺ�IP����

				foreach($_POST['user_ip_address'] as $ip_address)
				{
					if (!$ip_address == trim($ip_address) || $ip_address == $this->_thisIP) {
						continue;
					}
					$set_ip[]	= $ip_address;
				}
			}

			$this->_qfile->write('$user_ip_address	= array('.PHP_EOL);

			foreach ($set_ip as $key => $val )
			{
				$this->_qfile->write($key.'	=> \''.$val.'\','.PHP_EOL);
			}

			$this->_qfile->write(');'.PHP_EOL);
		}

		$this->_qfile->write('?>');
		$this->_qfile->close();
		chmod($this->_userConfigFile, 0707);

		return true;
	}

	/**
	 * ������ IP �������� ó��
	 * @return  boolean $result (true , false)
	 */
	public function setAdminAccessIP()
	{
		$checkResult	= $this->setAccessIP('admin');	// $checkResult �� true �� ��쿡�� ���� ���

		if ($checkResult === false) {
			msg('���� �����Ͻ� IP�� ���θ� ������ �Ұ��մϴ�.\r\n���θ� �����ڿ��� ���� �Ͻñ� �ٶ��ϴ�.','../../member/logout.php');
			exit();
		}

		return true;
	}

	/**
	 * ���θ� IP �������� ó��
	 * @return  boolean $result (true , false)
	 */
	public function setUserAccessIP()
	{
		$checkResult	= $this->setAccessIP('user');	// $checkResult �� true �� ��쿡�� ���� ���

		if ($checkResult === false) {
			// ���θ� IP�������� ������
			$getData	= $this->getUserAccessIP();

			if ($getData['restriction_page'] === 'skin' && empty($getData['restriction_page_skin']) === false) {
				$redirectUrl	= $getData['restriction_page_skin'];
			}
			else if ($getData['restriction_page'] === 'url' && empty($getData['restriction_page_url']) === false) {
				$redirectUrl	= $getData['restriction_page_url'];
			}
			else {
				$redirectUrl	= '/404';
			}
			header('location:'.$redirectUrl);
			exit();
		}

		return true;
	}

	/**
	 * ���� ������ ���� ��Ű ����
	 * @param1  string $thisMode ��� (admin , user)
	 * @return  string $result (��Ű����)
	 */
	private function setAccessCookie($thisMode)
	{
		// ��Ű�� Ű�̸�
		$cookieKey		= 'gd_'.$thisMode.'_enamooPass';

		setcookie($cookieKey, $this->_accessKey, time() + ($this->_cookieExpire * 60), '/');
	}

	/**
	 * ���� ������ ���� ��Ű ����
	 * @param1  string $thisMode ��� (admin , user)
	 * @return  string $result (��Ű����)
	 */
	private function delAccessCookie($thisMode)
	{
		// ��Ű�� Ű�̸�
		$cookieKey		= 'gd_'.$thisMode.'_enamooPass';

		setcookie($cookieKey, '', time() - 3600, '/');
	}

	/**
	 * ���� ������ ���� ��Ű�� üũ
	 * @param1  string $thisMode ��� (admin , user)
	 * @return  boolean $result (true , false)
	 */
	private function checkAccessCookie($thisMode, $UnixTime)
	{
		// ��Ű�� Ű�̸�
		$cookieKey		= 'gd_'.$thisMode.'_enamooPass';

		// ���� ���� ��Ű�� ���� ��� false return
		if (empty($_COOKIE[$cookieKey]) === true) {
			// ����� ����
			$this->_cookieDebug[]	= $thisMode.' - no cookie';

			return false;
		}

		$tmp			= explode('^', decode($_COOKIE[$cookieKey],1));
		$accessIP		= $tmp[0];
		$accessTime		= $tmp[1];

		if ($accessIP === $this->_thisIP && $accessTime > $UnixTime) {
			// ����� ����
			$this->_cookieDebug[]	= $thisMode.' - ok pass';

			return true;
		} else {
			// ������ ���� ���� ��Ű ����
			$this->delAccessCookie();

			// ����� ����
			$this->_cookieDebug[]	= $thisMode.' - expire';

			return false;
		}
	}

	/**
	 * IP �������� ó��
	 * @param1  string $thisMode ��� (admin , user)
	 * @return  boolean $result (true , false)
	 */
	private function setAccessIP($thisMode)
	{
		if ($thisMode == 'admin') {
			// ������ IP�������� ������
			$getData	= $this->getAdminAccessIP();

			if ($getData['set_ip_permit'] == 1) {
				$getData['ip_check']	= 'Y';
			}
			$getData['ip_address']		= $getData['set_regist_ip'];
		} else {
			// ���θ� IP�������� ������
			$getData	= $this->getUserAccessIP();

			$getData['ip_check']		= $getData['user_ip_access_restriction'];
			$getData['ip_address']		= $getData['user_ip_address'];
		}

		// ������ �ȵǾ� �ִٸ� �н�
		if ($getData['ip_check'] !== 'Y') {
			// ����� ����
			$this->_cookieDebug[]	= $thisMode.' - no config';

			return true;
		}

		// ���� ���� ��Ű üũ
		if ($this->checkAccessCookie($thisMode, $getData['get_file_time']) === true) {
			return true;
		}

		// ���� ó��
		if (is_file($this->_exceptionConfigFile)) {
			include $this->_exceptionConfigFile;
			foreach ($m_ip as $cVal) {
				$tmp	= explode('.', $cVal);									// IP�� .���� �����ؼ� �迭ó��
				$dTmp	= explode('~', $tmp[3]);								// D Class �� ~ �����ؼ� �뿪ó��
				$cTmp	= trim($tmp[0]).'.'.trim($tmp[1]).'.'.trim($tmp[2]);	// C Class ������ IP �ּ�

				if (count($dTmp) == 1) {
					$dTmp[0]	= $tmp[3];
					$dTmp[1]	= $tmp[3];
				}

				for ($i = $dTmp[0]; $i <= $dTmp[1]; $i++) {
					$exceptionData	= $cTmp.'.'.trim($i);

					// ���� �������� IP�� �ִٸ� ����ó��
					if ($this->_thisIP === $exceptionData) {

						// ���� ���� ��Ű ����
						$this->setAccessCookie($thisMode);

						// ����� ����
						$this->_cookieDebug[]	= $thisMode.' - exception';

						return true;											// ������ ���� ���
					}
				}
				unset($tmp, $cTmp, $dTmp, $exceptionData, $i);
			}
			unset($m_ip, $cVal);
		}

		// IP�� C Class ������ ó�� (C Class ���� �迭Ű�� ����ϰ� D Class �� ������ �����)
		foreach ($getData['ip_address'] as $cVal) {
			$tmp	= explode('.', $cVal);									// IP�� .���� �����ؼ� �迭ó��
			$dTmp	= explode('~', $tmp[3]);								// D Class �� ~ �����ؼ� �뿪ó��
			$cTmp	= trim($tmp[0]).'.'.trim($tmp[1]).'.'.trim($tmp[2]);	// C Class ������ IP �ּ�

			if (count($dTmp) == 1) {
				$dTmp[0]	= $tmp[3];
				$dTmp[1]	= $tmp[3];
			}

			for ($i = $dTmp[0]; $i <= $dTmp[1]; $i++) {
				$checkData	= $cTmp.'.'.trim($i);

				// ���� �������� IP�� �ִٸ� ����ó��
				if ($this->_thisIP === $checkData) {
					// ��忡 ���� ó��
					if ($thisMode == 'admin') {

						// ���� ���� ��Ű ����
						$this->setAccessCookie($thisMode);

						// ����� ����
						$this->_cookieDebug[]	= $thisMode.' - access';

						return true;										// ������ IP ���������� ��� ���� ���
					} else {
						return false;										// ���θ� IP ���������� ��� ���� �Ұ�
					}
				}
			}
			unset($tmp, $cTmp, $dTmp, $checkData, $i);
		}
		unset($cVal);

		// �ش� �Ǵ� IP�� ���� ��� ��忡 ���� ó��
		if ($thisMode == 'admin') {
			return false;										// ������ IP ���������� ��� ���� �Ұ�
		} else {
			// ���� ���� ��Ű ����
			$this->setAccessCookie($thisMode);

			// ����� ����
			$this->_cookieDebug[]	= $thisMode.' - access';

			return true;										// ���θ� IP ���������� ��� ���� ���
		}
	}


	/**
	 * ��Ų�� service ������ HTML�� �ҷ���
	 * @param1  string $skinName ��Ų��
	 * @param2  string $folderName ������
	 * @return  array $result ���� �迭
	 */
	public function getSkinFolderFile($skinName, $folderName)
	{
		// ���� ���
		$skinFolder	= dirname(__FILE__) . "/../data/skin/".$skinName."/".$folderName."/";

		// ������ ����Ÿ
		$getData	= array();

		// ���� �˻� �� ����Ÿ ����
		if (is_dir($skinFolder)) {
			if ($dh = opendir($skinFolder)) {
				while (($file = readdir($dh)) !== false)
				{
					if (filetype($skinFolder . $file) === 'file' && substr($file, -4) === '.htm') {
						$getData[]	= $file;
					}
				}
				closedir($dh);

				// ������ ���ϸ��� ����
				sort($getData);
			}
		}

		return $getData;
	}
}
?>