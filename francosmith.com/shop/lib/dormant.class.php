<?php
/**
 * Copyright (c) 2015 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 * workingby         2015.10.29        First Draft.
 */
set_time_limit(0);
ini_set("memory_limit", -1);
/**
 * �޸� ���� ���� Ŭ����
 *
 * @author dormant.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2015-10-29
 */
class dormant
{
	//�޸� ȸ�� ���̺��
	private $tableName = 'gd_dormant';

	//config����,  ��ȯ����, �۵�Ȯ�ο� temp ���ϰ��
	private $configFile, $executeTime, $temp_dormantCheckDir;

	//��ȣȭ ��� field
	private $secretFieldArray =array('phone', 'mobile', 'email', 'fax', 'birth_year', 'birth', 'zipcode', 'zonecode', 'address', 'address_sub', 'road_address', 'name');

	private $secretKey = 'alskadbsqjaduf*@#';

	//������� ȸ��ID
	private $dormantMemberIDArray = array();

	//�ӽ����̺��
	private $backupMemberTable = "gd_member_backup";

	//��������
	private $retailFieldArray = array("m_no", "m_id", "password");

	private $sendMail_successID;
	
	//SMS�߼� ȸ��
	private $sendSms_successID_30 = array(), $sendSms_successID_7 = array();

	function __construct()
	{
		//��ȯ����
		$this->executeTime = date("Y-m-d H:i:s");
		$this->configFile = self::getDormantConfigPath();
		$this->temp_dormantCheckDir = dirname(__FILE__) . '/../log/dormant_temp/';
	}

	/**
	 * �������� ��ȿ�Ⱓ�� ��� ��� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return blooean true-����, false-����
	 * @date 2015-10-29
	 */
	public function executeDormantAll()
	{
		try {
			$errorMessage = '';

			self::writeLog('start', '�������� ��ȿ�Ⱓ�� ���� ����');

			//���� ���������� Ȯ��
			$errorMessage = self::checkProcess('dormantAll');
			if($errorMessage != ''){
				throw new Exception($errorMessage, 1);
			}

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setStartTemp('dormantAll');

			//DB PATCH CHECK
			$errorMessage = self::checkDbPatch();
			if($errorMessage != ''){
				throw new Exception($errorMessage, 1);
			}
			self::writeLog('4step', 'STEP1. DB PATCH CHECK OK!');

			//�޸�ȸ�� ���̺� ����
			$errorMessage = self::setDormantTable();
			if($errorMessage != ''){
				throw new Exception($errorMessage, 2);
			}
			self::writeLog('4step', 'STEP2. CREATE DORMANT TABLE OK!');

			//�޸�ȸ�� ��ȯ��� üũ
			$dormantMemberChangeCount = self::getDormantMemberCount('dormantMemberAll');
			if($dormantMemberChangeCount > 0){
				self::writeLog('4step', 'STEP3. DORMANT MEMBER CHECK[EXISTS] OK!');

				//�޸�ȸ�� TABLE ROW �̵�
				$errorMessage = self::dormantMemberCopy('dormantMemberAll');
				if($errorMessage != ''){
					throw new Exception($errorMessage, 2);
				}
				self::writeLog('4step', 'STEP3-1. MOVE TO MEMBER TABLE OK!');

				//ȸ�� TABLE ����
				$errorMessage = self::updateMemberTable();
				if($errorMessage != ''){
					throw new Exception($errorMessage, 2);
				}
				self::writeLog('4step', 'STEP3-2. UPDATE VOID MEMBER TABLE OK!');
			}
			else {
				self::writeLog('4step', 'STEP3. DORMANT MEMBER CHECK[NOT EXISTS] OK!');
			}

			//config ���� ����
			$errorMessage = self::setDormantConfigFile('dormantAll');
			if($errorMessage != ''){
				throw new Exception($errorMessage, 3);
			}
			self::writeLog('4step', 'STEP4. CREATE CONFIG FILE OK!');
			self::writeLog('success', '');

			//updateMemberTable ���� ������ ������̺� ����
			self::removeBackupTable();
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAll');

			return true;
		}
		catch(Exception $e){
			if($e->getCode() > 1){
				self::resetDormant();
			}
			if($e->getCode() > 2){
				self::restoreMemberData();
			}

			//updateMemberTable ���� ������ ������̺� ����
			self::removeBackupTable();
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAll');

			self::writeLog('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * ���� ���μ����� ���������� üũ
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param vstring $type
	 * @return string
	 * @date 2015-10-29
	 */
	private function checkProcess($type)
	{
		try {
			$tmpName = self::getTempFileName($type);

			if (is_dir($this->temp_dormantCheckDir)) {
				if ($dir = opendir($this->temp_dormantCheckDir)) {
					while (($fileName = readdir($dir)) !== false) {
						if(preg_match('/'.$tmpName.'\_/', $fileName)){
							throw new Exception('���μ��� �ߺ� ����!');
						}
					}
					closedir($dir);
				}
			}

			return '';

		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * �ӽ����� ���� - �ߺ�����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @return void
	 * @date 2015-10-29
	 */
	private function setStartTemp($type)
	{
		if(!is_dir($this->temp_dormantCheckDir)) {
			@mkdir($this->temp_dormantCheckDir, 0707);
			@chmod($this->temp_dormantCheckDir, 0707);
		}

		$tmpName = self::getTempFileName($type);

		@tempnam($this->temp_dormantCheckDir, $tmpName . '_');
	}

	/**
	 * �ӽ����� ���� - �ߺ�����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @return void
	 * @date 2015-10-29
	 */
	private function setEndTemp($type)
	{
		$tmpName = self::getTempFileName($type);

		if (is_dir($this->temp_dormantCheckDir)) {
			if ($dir = opendir($this->temp_dormantCheckDir)) {
				while (($fileName = readdir($dir)) !== false) {
					if(preg_match('/'.$tmpName.'\_/', $fileName)){
						@unlink($this->temp_dormantCheckDir . '/' . $fileName);
					}
				}
				closedir($dir);
			}
		}
	}

	/**
	 * ���� - dormant ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	private function resetDormant()
	{
		global $db;

		if($db->tableCheck($this->tableName) == true){
			$backupTableName = "gd_dormant_backup_" . date("YmdHis");
			$res = $db->query("RENAME TABLE " . $this->tableName . " TO " . $backupTableName);
			if($res){
				self::writeLog('restore', 'RESTORE[RESET] DORMANT TABLE[EXISTS] - BACKUP_TABLE = '.$backupTableName.' OK!');
			}
		}
		else {
			self::writeLog('restore', 'RESTORE[RESET] DORMANT TABLE[NOT EXISTS] OK!');
		}
	}

	/**
	 * ���� - ȸ�� ������
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	private function restoreMemberData()
	{
		global $db;

		$updateQuery = array();
		$memberColumnsArray = self::getMemberColumns();
		foreach($memberColumnsArray as $key => $columns){
			if(!in_array($columns, $this->retailFieldArray)){
				$updateQuery[] = "a.".$columns."=b.".$columns;
			}
		}

		$query = "UPDATE ".GD_MEMBER." AS a, ".$this->backupMemberTable." AS b SET " . implode(",", $updateQuery) . " WHERE a.m_id=b.m_id";
		$res = $db->query($query);
		if($res){
			self::writeLog('restore', 'RESTORE MEMBER DATA OK!');
		}
	}

	/**
	 * �޸�ȸ�� TABLE ROW �̵�
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function dormantMemberCopy($type, $postDataArray=array())
	{
		global $db;

		try {
			$memberColumnsArray = $_insertQueryArray = $insertQueryArray = $_dormantMemberIDArray = $selectMemberIDArray = array();
			$lastInsertQuery = $lastInsertQueryFix = '';

			//get member table field
			$memberColumnsArray = self::getMemberColumns();


			switch($type){
				//��ɼ���, �ڵ���ȯ
				case 'dormantMemberAll' : case 'dormantMemberAuto' :
					$query = self::getDormantQuery($type);
					$result = $db->query($query);
				break;

				//������ȯ
				case 'dormantAdmin':
					$query = "SELECT * FROM " . GD_MEMBER . " WHERE m_no IN ('" . implode("','", $postDataArray) . "')";
					$result = $db->query($query);
				break;
			}

			//field hash
			while($member = $db->fetch($result, 1)){
				$_dormantMemberIDArray[] = $member['m_id']; //updateMemberTable function ���� ���

				foreach($memberColumnsArray as $columns){
					if(in_array($columns, $this->secretFieldArray)){
						$member[$columns] = "HEX(AES_ENCRYPT('".addslashes($member[$columns])."', '".$this->secretKey."'))";
					}
					else if($columns == 'dormant_regDate'){
						$member[$columns] = "'".$this->executeTime."'";
					}
					else {
						$member[$columns] = "'".addslashes($member[$columns])."'";
					}
				}

				$_insertQueryArray[$member['m_id']] = " (" . implode(",", $member) . ")";
			}

			$this->dormantMemberIDArray = @array_chunk($_dormantMemberIDArray, 100);//updateMemberTable function ���� ���

			//�ڵ�üũ�� �޸�ȸ�� ���̺� ���� üũ
			if($type == 'dormantMemberAuto' || $type == 'dormantAdmin'){
				foreach($this->dormantMemberIDArray as $m_id){
					$query = "SELECT * FROM " . $this->tableName . " WHERE m_id IN ('".implode("','", $m_id)."') ";
					$res = $db->query($query);
					if($res){
						while($dormantMember = $db->fetch($res)){
							unset($_insertQueryArray[$dormantMember['m_id']]);
						}
					}
				}
			}

			$insertQueryArray = @array_chunk($_insertQueryArray, 100);

			//insert
			if(count($insertQueryArray) > 0){
				$lastInsertQueryFix = "INSERT INTO " . $this->tableName . " (" . implode(", ", $memberColumnsArray) . ") VALUES ";
				foreach($insertQueryArray as $query){
					$lastInsertQuery = $lastInsertQueryFix . implode(", ", $query);
					$result = $db->query($lastInsertQuery);
					if(!$result){
						throw new Exception("Query Error - don't move member row.");
					}
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * get member table field
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return array $columnsArray
	 * @date 2015-10-29
	 */
	private function getMemberColumns()
	{
		global $db;

		$columnsArray = array();

		//get member table field query
		$query = self::getDormantQuery("memberColumns");
		$res = $db->query($query);
		while($columns = $db->fetch($res)){
			$columnsArray[] = $columns['Field'];
		}

		return $columnsArray;
	}

	/**
	 * ȸ�� TABLE UPDATE
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function updateMemberTable()
	{
		global $db;

		try{
			$deleteFieldArray = array();

			//���������� ������ ������ �ʵ� �� ����
			$query = self::getDormantQuery("memberColumns");
			$res = $db->query($query);
			while($member = $db->fetch($res, 1)){
				if(!in_array($member['Field'], $this->retailFieldArray)){
					$deleteFieldArray[] = $member['Field'] . " = '' ";
				}
			}

			//���
			self::backupMemberData();

			//UPDATE
			foreach($this->dormantMemberIDArray as $indexKey => $midArray){
				$query = "UPDATE " . GD_MEMBER . " SET " . implode(",", $deleteFieldArray) . ", dormant_regDate = '".$this->executeTime."' WHERE m_id IN ('" . implode("','", $midArray) . "')";
				$res = $db->query($query);
				if(!$res){
					throw new Exception("Query Error - fail member data void update.");
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * DB patch ���� üũ
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function checkDbPatch()
	{
		global $db;

		try {
			$query = self::getDormantQuery('checkDBpatch');
			$res = $db->query($query);
			if(!$res){
				throw new Exception("Query Error - unconfirmed gd_member table.");
			}

			$count = 0;
			while($row = $db->fetch($res)){
				if($row['Field']) $count++;
			}

			if($count < 2){
				throw new Exception("DB patch miss out.");
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * create doramant table
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function setDormantTable()
	{
		global $db;

		try {
			//TABLE ����
			$query = self::getDormantQuery('createDormantTable');
			$res = $db->query($query);
			if(!$res){
				throw new Exception("Query error - fail to create dormant table.");
			}

			//�ֹ��� ��ȣȭ�� ���� FILED �Ӽ�����
			foreach($this->secretFieldArray as $field){
				$res = $db->query("ALTER TABLE ".$this->tableName." CHANGE `".$field."` `".$field."` VARCHAR( 150 ) NOT NULL");
				if(!$res){
					throw new Exception("Query error - fail to dormant field change field.");
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * �������� ��ȿ�Ⱓ�� ��� ��� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $userCheck
	 * @return boolean
	 * @date 2015-10-29
	 */
	public function checkDormantAgree($userCheck='')
	{
		global $config;

		//config����[X] - ���data[O] - config���� ����
		if($userCheck != 'y'){
			if(!is_file($this->configFile)){
				if(!is_object($config)){
					$config = Core::loader('config');
				}
				$installSetting = false;
				$dormantConfig = array();
				$dormantConfig = $config->load('dormantConfig');

				//�ν��纻 - config ���� �ȵǾ� ���� ��� ���� ����
				if($dormantConfig['installSetting'] == 'y' && ($dormantConfig['use'] != 'y' && !$dormantConfig['agreeDate'])){
					//�޸�ȸ�� ���̺� ����
					self::setDormantTable();

					//config ���� ����
					self::setDormantConfigFile('dormantAll');

					$installSetting = true;
					$dormantConfig = array();
					$dormantConfig = $config->load('dormantConfig');
				}

				//gd_env ���� && �ν��� ������ �ȵǾ��� ��� = config ����
				if(count($dormantConfig) > 1 && $installSetting === false){
					self::writeConfigFile($dormantConfig);
				}
			}
		}

		if(is_file($this->configFile)){
			@include $this->configFile;
			if($configDormantAgree['use'] == 'y'){
				return true;
			}
		}

		return false;
	}

	/**
	 * �ڵ����� ���� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return blooean
	 * @date 2015-10-29
	 */
	public function checkAutoExecuteAble()
	{
		$dormantCheckDate = '';
		if(is_file($this->configFile)){
			@include $this->configFile;
			$dormantCheckDate = $configDormantAgree['checkDormantDate'];
		}

		$dormantCount = 0;
		$dormantCount = self::getDormantMemberCount('dormantMemberAuto');

		//��� ��뼳�� ���� - üũ�� üũ - ��ȯ��� Ȯ��
		if(self::checkDormantAgree() === true && ($dormantCheckDate && $dormantCheckDate != '0000-00-00 00:00:00' && $dormantCheckDate < date("Ymd") ) && $dormantCount > 0){
			return true;
		}

		return false;
	}

	/**
	 * �ڵ����� ���� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return blooean
	 * @date 2015-10-29
	 */
	public function checkAutoMailExecuteAble()
	{
		global $cfg;

		$dormantCheckMailDate = '';
		if(is_file($this->configFile)){
			@include $this->configFile;
			$dormantCheckMailDate = $configDormantAgree['checkDormantMailDate'];
		}

		$dormantMailCount = 0;
		$dormantMailCount = self::getDormantMemberCount('dormantMemberAutoMail');

		//��� ��뼳�� ���� - üũ�� üũ - ���� �߼۴�� Ȯ�� - ���Ϲ߼� ���� Ȯ��
		if(self::checkDormantAgree() === true && ($dormantCheckMailDate && $dormantCheckMailDate != '0000-00-00 00:00:00' && $dormantCheckMailDate < date("Ymd") ) && $dormantMailCount > 0 && $cfg['mailyn_40'] == 'y'){
			return true;
		}

		return false;
	}

	/**
	 * �������� ��ȿ�Ⱓ�� ��� ��� ���� ��
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	public function getDormantAgreeDate()
	{
		global $godo;

		$returnDate = '';
		if(is_file($this->configFile)){
			@include $this->configFile;
			if($configDormantAgree['use'] == 'y'){
				if($configDormantAgree['agreeDate']){
					$agreeDate = array();
					$agreeDate = explode("-", $configDormantAgree['agreeDate']);
					$returnDate = sprintf("%04d�� %02d�� %02d��",$agreeDate[0], $agreeDate[1], substr($agreeDate[2],0,2));
				}
				else {
					$returnDate = sprintf("%04d�� %02d�� %02d��", substr($godo['sdate'],0,4), substr($godo['sdate'],4,2), substr($godo['sdate'],6,2));
				}
			}
		}

		return $returnDate;
	}

	/**
	 * �޸�ȸ�� ��ȯ���, ���Ϲ߼۴�� �� ��
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $checkType
	 * @return int $count
	 * @date 2015-10-29
	 */
	public function getDormantMemberCount($checkType)
	{
		global $db;

		$count = 0;
		$query = self::getDormantQuery($checkType);
		$res = $db->query($query);
		$count = $db->count_($res);

		return $count;
	}

	/**
	 * �������� ���� �� �������� ���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @return string
	 * @date 2015-10-29
	 */
	private function setDormantConfigFile($type)
	{
		try {
			$errorMessage = '';

			if(is_file($this->configFile)){
				@include $this->configFile;
			}

			switch($type){
				case 'dormantAll':
					$configDormantAgree['use'] = 'y';
					$configDormantAgree['agreeDate'] = $this->executeTime;
					$configDormantAgree['checkDormantDate'] = date("Ymd");
					$configDormantAgree['checkDormantMailDate'] = date("Ymd");
					$configDormantAgree['checkDormantSmsDate'] = date("Ymd");
				break;

				case 'dormantAuto':
					$configDormantAgree['checkDormantDate'] = date("Ymd");
				break;

				case 'dormantAutoMail':
					$configDormantAgree['checkDormantMailDate'] = date("Ymd");
				break;
				
				
				case 'dormantAutoSms':
					$configDormantAgree['checkDormantSmsDate'] = date("Ymd");
				break;
				
			}

			$errorMessage = self::writeConfigFile($configDormantAgree);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//���� ���� ���
			self::saveConfigBackup($configDormantAgree);

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * config ���� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $configDormantAgree
	 * @return string
	 * @date 2015-10-29
	 */
	private function writeConfigFile($configDormantAgree)
	{
		global $qfile;

		try {
			if(!is_object($qfile)){
				include_once(dirname(__FILE__) . "/../lib/qfile.class.php");
				$qfile = new qfile();
				if(!is_object($qfile)) {
					throw new Exception("Class Error - not exists qfile class");
				}
			}

			$qfile->open($this->configFile);
			$qfile->write("<? \n");
			$qfile->write("\$configDormantAgree = array( \n");
			foreach($configDormantAgree as $key => $value){
				$qfile->write("'".$key."' => '".$value."', \n");
			}
			$qfile->write(");?>");
			$qfile->close();

			@chmod($this->configFile, 0707);
			if(!is_file($this->configFile)){
				throw new Exception("File Error - fail to create config file.");
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * ���� ���� ���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	private function saveConfigBackup($configDormantAgree)
	{
		global $config;

		if(!is_object($config)){
			$config = Core::loader('config');
		}

		$config->save('dormantConfig', $configDormantAgree);
	}

	/**
	 * �޸� ���� ���� ���� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $mode
	 * @return string $query
	 * @date 2015-10-29
	 */
	private function getDormantQuery($mode)
	{
		$query = '';
		switch($mode) {
			//�޸�ȸ�� ���̺� ����
			case 'createDormantTable':
				$query = "CREATE TABLE ".$this->tableName." LIKE " . GD_MEMBER;
			break;

			//DB��ġ ���� üũ
			case 'checkDBpatch' :
				$query = "SHOW COLUMNS FROM " . GD_MEMBER . " WHERE field = 'dormant_regDate' OR field = 'dormant_mailSendDate'";
			break;

			//�޸�ȸ�� ��ȯ����� üũ - ���ʻ�� ����
			case 'dormantMemberAll' :
				$query = "SELECT * FROM ".GD_MEMBER." WHERE last_login < '".date("Y-m-d H:i:s", strtotime($this->executeTime . " -1 year"))."' AND dormant_regDate = '0000-00-00 00:00:00' AND level!='100'";
			break;

			//�޸� ��ȯ ���� ȸ������Ʈ
			case 'dormantMemberToBeWhere' :
				$query = "(DATE_ADD(last_login, INTERVAL +335 DAY) <= '".$this->executeTime."') AND dormant_regDate = '0000-00-00 00:00:00' AND level!='100'";
			break;

			//�޸�ȸ�� ��ȯ����� üũ - auto üũ
			case 'dormantMemberAuto' :
				$query = "SELECT * FROM ".GD_MEMBER." WHERE last_login < '".date("Y-m-d H:i:s", strtotime($this->executeTime . " -1 year"))."' AND dormant_regDate = '0000-00-00 00:00:00' AND level!='100'";
			break;

			//�޸�ȸ�� ��ȯ����� üũ - auto ���� üũ
			case 'dormantMemberAutoMail' :
				$query = "SELECT * FROM ".GD_MEMBER." WHERE (DATE_ADD(last_login, INTERVAL +12 MONTH)  >= '".$this->executeTime."') AND (DATE_ADD(last_login, INTERVAL +11 MONTH) <= '".$this->executeTime."') AND dormant_mailSendDate = '0000-00-00 00:00:00' AND level!='100'";
			break;
			
			//�޸�ȸ�� ��ȯ����� üũ - auto SMS üũ_30����
			case 'dormantMemberAutoSms_30' :
				$query = "SELECT * FROM ".GD_MEMBER." WHERE level!='100' AND dormant_regDate = '0000-00-00 00:00:00' AND (DATE_ADD(last_login, INTERVAL +358 DAY)  > '".$this->executeTime."') AND (DATE_ADD(last_login, INTERVAL +335 DAY) <= '".$this->executeTime."') AND dormant_smsSendCheck = '0'";
			break;

			//�޸�ȸ�� ��ȯ����� üũ - auto SMS üũ_7����
			case 'dormantMemberAutoSms_7' :
				$query = "SELECT * FROM ".GD_MEMBER." WHERE level!='100' AND dormant_regDate = '0000-00-00 00:00:00' AND (DATE_ADD(last_login, INTERVAL +12 MONTH)  > '".$this->executeTime."') AND (DATE_ADD(last_login, INTERVAL +358 DAY) <= '".$this->executeTime."') AND (dormant_smsSendCheck = '0' OR dormant_smsSendCheck = '30')";
			break;
			
			//ȸ�� columns
			case 'memberColumns' :
				$query = "SHOW COLUMNS FROM " . GD_MEMBER;
			break;

			case 'memberTotal' :
				$query = "SELECT * FROM " . GD_MEMBER . " WHERE m_id != 'godomall' AND level!='100' AND dormant_regDate = '0000-00-00 00:00:00'";
			break;

			//�޸�ȸ�� columns
			case 'dormantMemberColumns' :
				$query = "SHOW COLUMNS FROM " . $this->tableName;
			break;

			//������̺� ����
			case 'removeBackupTable' :
				$query = "DROP TABLE IF EXISTS " . $this->backupMemberTable;
			break;

			//�޸�ȸ�� columns
			case 'dormantCount' :
				$query = "SELECT * FROM " . $this->tableName;
			break;
		}

		return $query;
	}

	/**
	 * �αױ��
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $mode $message
	 * @return void
	 * @date 2015-10-29
	 */
	private function writeLog($mode, $message)
	{
		global $_SERVER, $_SESSION;

		$logPath = dirname(__FILE__) . '/../log/dormant/';
		$logFileName = 'dormantLog_' . date('Ymd') . '.log';
		if(!is_dir($logPath)) {
			@mkdir($logPath, 0707);
			@chmod($logPath, 0707);
		}
		$saveLogFileName = $logPath . $logFileName;

		$logMessage = '';
		switch($mode){
			case 'start' :
				$logMessage .= PHP_EOL.PHP_EOL. '===== LOG START =====' . PHP_EOL;
				$logMessage .= '[EXE TYPE]' . $message . PHP_EOL;
				$logMessage .= '[START TIME]' . $this->executeTime . PHP_EOL;
				$logMessage .= '[IP]' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
				$logMessage .= '[ID]' . $_SESSION['sess']['m_id'] . PHP_EOL;
			break;

			case 'startLogin' :
				$logMessage .= PHP_EOL.PHP_EOL. '===== LOG START =====' . PHP_EOL;
				$logMessage .= '[EXE TYPE - ID]' . $message . PHP_EOL;
				$logMessage .= '[START TIME]' . $this->executeTime . PHP_EOL;
				$logMessage .= '[IP]' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
			break;

			case 'changePassword' :
				$logMessage .= PHP_EOL.PHP_EOL. '===== LOG START =====' . PHP_EOL;
				$logMessage .= '[EXE TYPE - ID]' . $message . PHP_EOL;
				$logMessage .= '[START TIME]' . $this->executeTime . PHP_EOL;
				$logMessage .= '[IP]' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
				$logMessage .= PHP_EOL. '===== LOG END =====' . PHP_EOL;
			break;

			case 'success' :
				$logMessage .= '[RESULT] OK' . PHP_EOL;
			break;

			case 'end' :
				$logMessage .= PHP_EOL. '===== LOG END =====' . PHP_EOL;
			break;

			case '1step' : case '2step' : case '3step' : case '4step' :
				$logMessage .= '['.strtoupper($mode).'] ' . $message . PHP_EOL;
			break;

			case 'restore' :
				$logMessage .= '[ERROR-RESTORE] ' . $message . PHP_EOL;
			break;

			case 'error' :
				$logMessage .= '[ERROR] ' . $message . PHP_EOL;
			break;

			case 'shutdown' :
				$logMessage .= '[SHUTDOWN] ' . $message . PHP_EOL;
				$logMessage .= PHP_EOL. '===== LOG END =====' . PHP_EOL;

				//3������ �α� ����
				self::deleteLog($logPath);
			break;
		}

		@error_log($logMessage, 3, $saveLogFileName);
	}

	/**
	 * shutdown log
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	public function shutdownLog($type)
	{
		$memoryError = false;
		switch($type){
			case 'dormantAll' :
				self::removeBackupTable(); //������̺� ����

				$message =  '���� ���� : ';
			break;

			case 'dormantAuto' :
				self::removeBackupTable(); //������̺� ����

				$message =  '�ڵ� ��ȯ : ';
			break;

			case 'dormantAdmin' :
				self::removeBackupTable(); //������̺� ����

				$message =  '���� ��ȯ : ';
			break;

			case 'dormantAutoMail' :
				$message =  '�ڵ� ���� �߼� : ';
			break;
			
			case 'dormantAutoSms' :
				$message =  '�ڵ� SMS �߼� : ';
			break;

			case 'dormantRestoreAdmin' :
				$message =  '�޸�ȸ�� ����[������] : ';
			break;

			case 'dormantRestoreUser' :
				$message =  '�޸�ȸ�� ����[�����α���] : ';
			break;

			case 'dormantMemberDelete' :
				$message =  'ȸ������[������] : ';
			break;

			case 'dormantMemberDeleteAll' :
				$message =  'ȸ����ü����[������] : ';
			break;
		}

		//�ӽ����� ���� - �ߺ� ���� ����
		self::setEndTemp($type);

		if(function_exists('error_get_last')){
			$error = array();
			$error = error_get_last();
			if(strpos($error['message'], 'Allowed memory size') === 0){
				$message .= $error['message'];
				$memoryError = true;
			}
		}

		if($memoryError != true){
			switch (connection_status()) {
				case CONNECTION_NORMAL: // Connection ������ ���
					$message .= 'CONNECTION_OK [Connection ����]';
				break;

				case CONNECTION_TIMEOUT: // Connection TIMEOUT ����
					$message .= 'CONNECTION_TIMEOUT [Connection TIMEOUT ����]';
				break;

				case CONNECTION_ABORTED: // Connection Client ����(Client ���� ������ ���)
					$message .= 'CONNECTION_ABORTED [Connection Client ����]';
				break;

				case (CONNECTION_ABORTED & CONNECTION_TIMEOUT): // Connection Client ���ܵǰ� TIMEOUT ����(Client ���� ������ ���)
					$message .= 'CONNECTION_TIMEOUT_ABORTED [Connection Client ���ܵǰ� TIMEOUT ����]';
				break;

				default: // Connection �˷����� ���� ����
					$message .= 'CONNECTION_UNKNOWN_ERROR [�˷����� ���� ����]';
				break;
			}
		}

		self::writeLog('shutdown', $message);
	}

	/**
	 * config ���� ���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function getDormantConfigPath()
	{
		return dirname(__FILE__).'/../conf/config.dormantAgree.php';
	}

	/**
	 * ���� - ȸ�������� ���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function backupMemberData()
	{
		global $db;

		foreach($this->dormantMemberIDArray as $indexKey => $midArray){
			if($indexKey == 0){
				$query = "CREATE TABLE ".$this->backupMemberTable." SELECT * FROM ".GD_MEMBER. " WHERE m_id IN ('" . implode("','", $midArray) . "')";
			}
			else {
				$query = "INSERT INTO ".$this->backupMemberTable." SELECT * FROM ".GD_MEMBER. " WHERE m_id IN ('" . implode("','", $midArray) . "')";
			}
			$db->query($query);
		}
	}

	/**
	 * ������̺� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	private function removeBackupTable()
	{
		global $db;

		$query = self::getDormantQuery('removeBackupTable');
		$db->query($query);
	}

	/**
	 * �޸�ȸ�� ��ȯ ����� 30���� ȸ�� ���� �߼�
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	public function executeDormantMail()
	{
		try {
			$errorMessage = '';

			self::writeLog('start', '�޸� ȸ�� ��ȯ 30���� ���� �߼�');

			//���� ���������� Ȯ��
			$errorMessage = self::checkProcess('dormantAutoMail');
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setStartTemp('dormantAutoMail');

			$errorMessage = self::sendDormantMail();
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP1. MAIL SEND OK!');

			$errorMessage = self::updateDormantMail();
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP2. UPDATE MEMBER SEND MAIL DATE OK!');

			$errorMessage = self::setDormantConfigFile('dormantAutoMail');
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP3. UPDATE CONFIG FILE OK!');

			self::writeLog('success', '');

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAutoMail');

			return true;
		}
		catch(Exception $e){
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAutoMail');

			self::writeLog('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * ���Ϲ߼��� ������Ʈ
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	private function updateDormantMail()
	{
		global $db;

		try {
			$sendMailSuccessList = array();
			$sendMailSuccessList = array_chunk($this->sendMail_successID, 100);
			foreach($sendMailSuccessList as $m_id){
				$query = "UPDATE " . GD_MEMBER . " SET dormant_mailSendDate = '".$this->executeTime."' WHERE m_id IN ('" . implode("','", $m_id) . "')";
				$res = $db->query($query);
				if(!$res){
					throw new Exception("Query Error - fail to update email date.");
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * ���Ϲ߼�
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-10-29
	 */
	private function sendDormantMail()
	{
		global $db, $cfg, $automail;

		try {
			if(!$automail){
				include_once dirname(__FILE__) . '/../lib/automail.class.php';
				$automail = new automail();
			}

			if($cfg['mailyn_40'] == 'y'){
				$query = self::getDormantQuery('dormantMemberAutoMail');
				$res = $db->query($query);
				if(!$res){
					throw new Exception("Query Error - fail to load auto mail Query.");
				}

				while($member = $db->fetch($res, 1)){
					if(trim($member['email']) != ''){
						$automail->_set('40', $member['email'], $cfg);
						$automail->_assign('id', $member['m_id']);
						$automail->_assign('name', $member['name']);
						$automail->_assign('toBeDate', date("Y-m-d", strtotime($member['last_login'] . " +1 year")));
						$result = $automail->_send();
						if($result == true){
							$this->sendMail_successID[] = $member['m_id'];
						}
					}
				}
			}
			else {
				throw new Exception("mail config - don't save send config");
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * �ڵ����Ϲ߼�, �ڵ���ȯ �����ڸ��������� iframe
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @return string $iframeHtml
	 * @date 2015-10-29
	 */
	public function loadIframe($type)
	{
		$iframeHtml = '';
		if($type == 'dormantMemberAutoMail'){
			$src = '../proc/_iframe.dormantMail.php';
			$name = 'iframe_dormantMail';
		}
		else if($type == 'dormantMemberAuto'){
			$src = '../proc/_iframe.dormantMember.php';
			$name = 'iframe_dormantMember';
		}
		else if($type == 'dormantMemberAutoSms'){
			$src = '../proc/_iframe.dormantSms.php';
			$name = 'iframe_dormantSms';
		}
		else {
			$src = '';
			$name = '';
		}

		if($name != '' && $src != ''){
			$iframeHtml = "<iframe name='".$name."' id='".$name."' src='".$src."' style='display:none;'></iframe>";
		}

		return $iframeHtml;
	}

	/**
	 * DB sync add dormant table
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $memberArray $memberFieldArray $dormantArray $dormantFieldArray
	 * @return string
	 * @date 2015-10-29
	 */
	private function syncAddDormantTable($memberArray, $memberFieldArray, $dormantArray, $dormantFieldArray)
	{
		global $db;

		try{
			$memberDiffArray = array();
			$memberDiffArray = array_diff($memberFieldArray, $dormantFieldArray);
			if(count($memberDiffArray) > 0){
				foreach($memberDiffArray as $fieldName){
					$default = ($memberArray[$fieldName]['Default'] != '') ? "DEFAULT '" . $memberArray[$fieldName]['Default'] ."'" : '';
					$null = ($memberArray[$fieldName]['Null'] == 'YES') ? 'NULL' : 'NOT NULL';
					$query = "ALTER TABLE ".$this->tableName." ADD ".$fieldName." ".$memberArray[$fieldName]['Type']."  ".$null." ".$default;
					$res = $db->query($query);
					if(!$res){
						throw new Exception("Query Error - fail to sync add field.");
					}
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * DB sync drop dormant table
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $memberFieldArray $dormantFieldArray
	 * @return string
	 * @date 2015-10-29
	 */
	private function syncDropDormantTable($memberFieldArray, $dormantFieldArray)
	{
		global $db;

		try{
			$dormantDiffArray = array();
			$dormantDiffArray = array_diff($dormantFieldArray, $memberFieldArray);
			if(count($dormantDiffArray) > 0){
				foreach($dormantDiffArray as $fieldName){
					$query = "ALTER TABLE ".$this->tableName." DROP COLUMN " . $fieldName;
					$res = $db->query($query);
					if(!$res){
						throw new Exception("Query Error - fail to sync drop field.");
					}
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * DB sync change field dormant table
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $memberArray $dormantArray
	 * @return string
	 * @date 2015-10-29
	 */
	private function syncChangeDormantTable($memberArray, $dormantArray)
	{
		global $db;

		try{
			$_diffArray = $diffArray = array();
			foreach($memberArray as $key => $valueArray){
				$_diffArray[$key] = array_diff($valueArray, $dormantArray[$key]);
			}

			$diffArray = array_filter($_diffArray);
			foreach($this->secretFieldArray as $field){
				unset($diffArray[$field]);
			}

			if(count($diffArray) > 0){
				foreach($diffArray as $fieldName => $value){
					$default = ($memberArray[$fieldName]['Default'] != '') ? "DEFAULT '" . $memberArray[$fieldName]['Default'] ."'" : '';
					$null = ($memberArray[$fieldName]['Null'] == 'YES') ? 'NULL' : 'NOT NULL';
					$query = "ALTER TABLE ".$this->tableName." CHANGE COLUMN `".$fieldName."` `".$fieldName."` ".$memberArray[$fieldName]['Type']."  ".$null." ".$default;
					$res = $db->query($query);
					if(!$res){
						throw new Exception("Query Error - fail to sync change field.");
					}
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * DB sync
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function syncDbField()
	{
		global $db;

		try{
			$memberArray = $memberFieldArray = $dormantFieldArray = $dormantArray = array();

			//ȸ�� ���̺� �÷�
			$query = self::getDormantQuery('memberColumns');
			$res = $db->query($query);
			while($row = $db->fetch($res, 1)){
				$memberArray[$row['Field']] = $row;
				$memberFieldArray[] = $row['Field'];
			}

			//�޸�ȸ�� ���̺� �÷�
			$query = self::getDormantQuery('dormantMemberColumns');
			$res = $db->query($query);
			while($row = $db->fetch($res, 1)){
				$dormantArray[$row['Field']] = $row;
				$dormantFieldArray[] = $row['Field'];
			}

			//�ʵ尡 ȸ�����̺� �ְ� �޸�ȸ�� ���̺� ���� ��� - �޸����̺� add
			$errorMessage = self::syncAddDormantTable($memberArray, $memberFieldArray, $dormantArray, $dormantFieldArray);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//�ʵ尡 �޸����̺� �ְ� ȸ�����̺� ���� ��� - �޸����̺� drop
			$errorMessage = self::syncDropDormantTable($memberFieldArray, $dormantFieldArray);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//�ʵ� �Ӽ��� �ٸ� ��� - �޸����̺� change
			$errorMessage = self::syncChangeDormantTable($memberArray, $dormantArray);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * �ڵ�ȸ�� ��ȯ [�����ڸ��������� - adm_basic_index.php]
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	public function executeDormant()
	{
		try {
			$errorMessage = '';

			//DB ��ġ üũ
			self::writeLog('start', '�޸� ȸ�� �ڵ���ȯ');

			//���� ���������� Ȯ��
			$errorMessage = self::checkProcess('dormantAuto');
			if($errorMessage != ''){
				throw new Exception($errorMessage, 1);
			}

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setStartTemp('dormantAuto');

			//DB PATCH CHECK
			$errorMessage = self::checkDbPatch();
			if($errorMessage != ''){
				throw new Exception($errorMessage, 1);
			}
			self::writeLog('4step', 'STEP1. DB PATCH CHECK OK!');

			//gd_member �ʵ� ��ũ
			$errorMessage = self::syncDbField();
			if($errorMessage != ''){
				throw new Exception($errorMessage, 1);
			}
			self::writeLog('4step', 'STEP2. DB FIELD SYNC OK!');

			//�޸�ȸ�� ��ȯ��� üũ
			$dormantMemberChangeCount = self::getDormantMemberCount('dormantMemberAuto');
			if($dormantMemberChangeCount > 0){
				self::writeLog('4step', 'STEP3. DORMANT MEMBER CHECK[EXISTS] OK!');

				//�޸�ȸ�� TABLE ROW �̵�
				$errorMessage = self::dormantMemberCopy('dormantMemberAuto');
				if($errorMessage != ''){
					throw new Exception($errorMessage, 2);
				}
				self::writeLog('4step', 'STEP3-1. MOVE TO MEMBER TABLE OK!');

				//ȸ�� TABLE ����
				$errorMessage = self::updateMemberTable();
				if($errorMessage != ''){
					throw new Exception($errorMessage, 2);
				}
				self::writeLog('4step', 'STEP3-2. UPDATE VOID MEMBER TABLE OK!');
			}
			else {
				self::writeLog('4step', 'STEP3. DORMANT MEMBER CHECK[NOT EXISTS] OK!');
			}

			//config ���� ����
			$errorMessage = self::setDormantConfigFile('dormantAuto');
			if($errorMessage != ''){
				throw new Exception($errorMessage, 3);
			}
			self::writeLog('4step', 'STEP4. CREATE CONFIG FILE OK!');
			self::writeLog('success', '');

			self::removeBackupTable();
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAuto');

			return true;
		}
		catch(Exception $e){
			if($e->getCode() > 2){
				self::restoreMemberData();
			}
			self::removeBackupTable();
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAuto');

			self::writeLog('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * 3������ �α� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string
	 * @return void
	 * @date 2015-10-29
	 */
	private function deleteLog($logPath)
	{
		$searchPath = $logPath . 'dormantLog_' . date("Ym", strtotime("-3 month")) . '*';
		foreach(glob($searchPath) as $logFile){
			if(strpos($logFile, 'dormantLog_') !== false){
				@unlink($logFile);
			}
		}
	}

	/**
	 * return dormant table name
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	public function getDormantTableName()
	{
		return $this->tableName;
	}

	/**
	 * return list where syntax
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $skey, $sword
	 * @return string
	 * @date 2015-10-29
	 */
	public function getListWhere($skey, $sword)
	{
		$returnWhere = '';
		if(in_array($skey , $this->secretFieldArray)){
			$returnWhere = "AES_DECRYPT(UNHEX(".$skey."), '".$this->secretKey."') = '".$sword."'";
		}
		else {
			$returnWhere = $skey ." = '".$sword."'";
		}

		return $returnWhere;
	}

	/**
	 * return list secret field
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	public function getSecretField()
	{
		$returnField = "*";
		foreach($this->secretFieldArray as $field){
			$returnField .= ", AES_DECRYPT(UNHEX(".$field."), '".$this->secretKey."') AS ".$field;
		}

		return $returnField;
	}

	/**
	 * ������ - �޸���� ȸ�� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $_postDataArray
	 * @return string
	 * @date 2015-10-29
	 */
	public function executeMemberRestoreAdmin($_postDataArray)
	{
		try {
			$postDataArray = array_filter($_postDataArray);

			//���� ���������� Ȯ��
			$errorMessage = self::checkProcess('dormantRestoreAdmin');
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setStartTemp('dormantRestoreAdmin');

			self::writeLog('start', '�޸���� ȸ�� ����[������ ����]');

			//ȸ�����̺�� ���� �̵�
			$errorMessage = self::memberCopy($postDataArray);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('2step', 'STEP1. RESTORE UPDATE MEMBER TABLE OK!');

			//�޸�ȸ�� remove
			$errorMessage = self::removeDormantMember($postDataArray);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('2step', 'STEP2. REMOVE DORMANT MEMBER ROW OK!');

			self::writeLog('success', '');

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantRestoreAdmin');

			return true;
		}
		catch(Exception $e){
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantRestoreAdmin');

			self::writeLog('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * ������ - ȸ�� ����, ȸ�� ��ü����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $_postDataArray
	 * @return string
	 * @date 2015-10-29
	 */
	public function executeMemberDelete($type, $_postDataArray=array())
	{
		global $db;

		try {
			$errorMessage = '';
			$postDataArray = array();

			switch($type){
				//�޸�ȸ�� ����Ʈ
				case 'dormantMemberDeleteAll':
					$logStartName = 'ȸ����ü����[������ ����]';
					$logStep = '2step';

					$query = self::getDormantQuery('dormantCount');
					$res = $db->query($query);
					while($data = $db->fetch($res)){
						$_postDataArray[] = $data['m_no'];
					}
				break;

				//�޸�ȸ�� ����Ʈ
				case 'dormantMemberDelete':
					$logStartName = 'ȸ������[������-�޸�ȸ������Ʈ]';
					$logStep = '2step';
				break;

				//�޸� ��ȯ ���� ȸ�� ����Ʈ
				case 'dormantMemberToBeDelete':
					$logStartName = 'ȸ������[������-�޸���ȯ����ȸ������Ʈ]';
					$logStep = '1step';
				break;
			}

			$postDataArray = array_filter($_postDataArray);

			//���� ���������� Ȯ��
			$errorMessage = self::checkProcess($type);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setStartTemp($type);

			self::writeLog('start', $logStartName);

			//ȸ������ ���μ���
			$errorMessage =  self::removeMemberProcess($type, $postDataArray);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog($logStep, 'STEP1. REMOVE MEMBER DATA OK!');

			//�޸� ��ȯ ���� ȸ������Ʈ �� �ƴҰ��
			if($type != 'dormantMemberToBeDelete'){
				//�޸�ȸ�� remove
				$errorMessage = self::removeDormantMember($postDataArray);
				if($errorMessage != ''){
					throw new Exception($errorMessage);
				}
				self::writeLog('2step', 'STEP2. REMOVE DORMANT MEMBER ROW OK!');
			}

			self::writeLog('success', '');

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp($type);

			return true;
		}
		catch(Exception $e){
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp($type);

			self::writeLog('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * ȸ�� ���� ���μ���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type, array $postDataArray
	 * @return string
	 * @date 2015-10-29
	 */
	private function removeMemberProcess($type, $postDataArray)
	{
		global $db;

		try {
			@include dirname(__FILE__) . '/../naverCheckout.cfg.php';

			foreach ($postDataArray as $m_no){
				switch($type){
					//�޸�ȸ������Ʈ
					case 'dormantMemberDeleteAll': case 'dormantMemberDelete':
						$query = "SELECT ". self::getSecretField() ." FROM " . $this->tableName . " WHERE m_no = '".$m_no."' LIMIT 1";
						$res = $db->query($query);
					break;

					//�޸� ��ȯ ���� ȸ�� ����Ʈ
					case 'dormantMemberToBeDelete':
						$query = "SELECT * FROM " . GD_MEMBER . " WHERE m_no = '".$m_no."' LIMIT 1";
						$res = $db->query($query);
					break;
				}
				if(!$res){
					throw new Exception("Query error - fail to load member info");
				}
				$data = $db->fetch($res);

				//���̹� üũ�ƿ�(ȸ������)
				if($checkoutCfg['useYn'] == 'y'){
					$res = naverCheckoutHack($m_no);
					if ($res['result'] === false) {
						throw new Exception("���̹�üũ�ƿ� ȸ�� öȸ�� ���еǾ� Ż���� �� ����.");
					}
				}

				// Ż��α� ����
				$res = $db->query("INSERT INTO " . GD_LOG_HACK . " ( m_id, name, actor, ip, regdt ) values ( '".$data['m_id']."', '".$data['name']."', '0', '" . $_SERVER['REMOTE_ADDR'] . "', now() )" );
				if(!$res) throw new Exception("Query error - fail to insert log_hack data");

				$res = $db->query("DELETE FROM " . GD_MEMBER . " WHERE m_no='$m_no'");
				if(!$res) throw new Exception("Query error - fail to delete member data");

				$res = $db->query("DELETE FROM " . GD_LOG_EMONEY . " WHERE m_no='$m_no'");
				if(!$res) throw new Exception("Query error - fail to delete log emoney");

				$res = $db->query("DELETE FROM gd_sns_member WHERE m_no='$m_no'");
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * �޸�ȸ�� remove
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $postDataArray
	 * @return string
	 * @date 2015-10-29
	 */
	private function removeDormantMember($postDataArray)
	{
		global $db;

		try {
			$query = "DELETE FROM " . $this->tableName . " WHERE m_no IN ('" . implode("','", $postDataArray) . "') ";
			$res = $db->query($query);
			if(!$res){
				throw new Exception("Query error - fail to delete dormant member");
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * �޸�ȸ�� TABLE ROW �̵�
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-10-29
	 */
	private function memberCopy($postDataArray)
	{
		global $db;

		try {
			$memberColumnsArray = array();
			$memberColumnsArray = self::getMemberColumns();

			$query = "SELECT ". self::getSecretField() ." FROM " . $this->tableName . " WHERE m_no IN ('" . implode("','", $postDataArray) . "') ";
			$res = $db->query($query);
			if(!$res){
				throw new Exception("Query error - fail to load dormant member");
			}

			while($dormantMember = $db->fetch($res)){
				$updateQuery = '';
				$_updateQuery = array();

				foreach($memberColumnsArray as $field){
					if(in_array($field, $this->retailFieldArray)){
						continue;
					}

					if($field == 'dormant_regDate' || $field == 'dormant_mailSendDate' || $field == 'dormant_smsSendCheck'){
						$_updateQuery[] = $field . " = '' ";
					}
					else if($field == 'last_login'){
						$_updateQuery[] = $field . " = now() ";
					}
					else {
						$_updateQuery[] = $field . "='" . addslashes($dormantMember[$field]) . "'";
					}
				}

				$updateQuery = "UPDATE " . GD_MEMBER . " SET " . implode(",", $_updateQuery) . " WHERE m_no = '" . $dormantMember['m_no'] . "'";
				$updateResult = $db->query($updateQuery);
				if(!$updateResult){
					throw new Exception("Query error - fail to update member");
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * temp ���ϸ� ��ȯ
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @return string $tmpName
	 * @date 2015-10-29
	 */
	private function getTempFileName($type)
	{
		$tmpName = '';
		switch($type){
			case 'dormantAll':
				$tmpName = 'dormantTempAll';
			break;

			case 'dormantAuto':
				$tmpName = 'dormantTemp';
			break;

			case 'dormantAutoMail':
				$tmpName = 'dormantMailTemp';
			break;
			
			case 'dormantAutoSms':
				$tmpName = 'dormantSmsTemp';
			break;
			
			case 'dormantAdmin' :
				$tmpName = 'dormantAdminTemp';
			break;

			case 'dormantRestoreAdmin':
				$tmpName = 'dormantTempRestoreAdmin';
			break;

			case 'dormantMemberDelete':
				$tmpName = 'dormantTempMemberDelete';
			break;

			case 'dormantMemberDeleteAll':
				$tmpName = 'dormantTempMemberDeleteAll';
			break;

			case 'dormantMemberToBeDelete' :
				$tmpName = 'dormantTempMemberToBeDelete';
			break;
		}

		return $tmpName;
	}

	/**
	 * where �� ��ȯ - adm_dormant_dormantToBeMemberList.php
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string $query
	 * @date 2015-10-29
	 */
	public function getToBeMemberWhere()
	{
		$query = '';
		$query = self::getDormantQuery('dormantMemberToBeWhere');

		return $query;
	}

	/**
	 * �޸�ȸ�� ��ȯ - ������ ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $_postDataArray
	 * @return string
	 * @date 2015-10-29
	 */
	public function executeDormantAdmin($_postDataArray)
	{
		try {
			$errorMessage = '';
			$postDataArray = array_filter($_postDataArray);

			//DB ��ġ üũ
			self::writeLog('start', '�޸� ȸ�� ����[������]��ȯ');

			//���� ���������� Ȯ��
			$errorMessage = self::checkProcess('dormantAdmin');
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setStartTemp('dormantAdmin');

			//DB PATCH CHECK
			$errorMessage = self::checkDbPatch();
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP1. DB PATCH CHECK OK!');

			//gd_member �ʵ� ��ũ
			$errorMessage = self::syncDbField();
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP2. DB FIELD SYNC OK!');

			//�޸�ȸ�� TABLE ROW �̵�
			$errorMessage = self::dormantMemberCopy('dormantAdmin', $postDataArray);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP3-1. MOVE TO MEMBER TABLE OK!');

			//ȸ�� TABLE ����
			$errorMessage = self::updateMemberTable();
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP3-2. UPDATE VOID MEMBER TABLE OK!');

			self::writeLog('success', '');

			self::removeBackupTable();
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAdmin');

			return true;
		}
		catch(Exception $e){
			self::removeBackupTable();

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAdmin');

			self::writeLog('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * �޸�ȸ�� ���� üũ - USER PAGE �α���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $id $password
	 * @return blooean true - �޸�ȸ��O �Ϲ�ȸ������ ���󺹿��Ǿ�����, false - �޸�ȸ��X , ���󺹿� ���� �ʾ�����
	 * @date 2015-10-29
	 */
	public function checkDormantLogin($id, $password)
	{
		global $db;

		$query = $db->_query_print('SELECT m_no, dormant_regDate FROM ' . GD_MEMBER . ' WHERE m_id = [s] AND password IN (password([s]),old_password([s]),[s]) LIMIT 1',$id,$password,$password,md5($password));
		$res = $db->query($query);
		list($m_no, $dormant_regDate) = $db->fetch($res);
		if($m_no){
			if($dormant_regDate != '0000-00-00 00:00:00' && !empty($dormant_regDate)){
				$restoreResult = self::executeMemberRestoreUser((array)$m_no, $id);
				if($restoreResult == true){
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * �޸�ȸ�� ���� üũ - USER PAGE SOCIAL �α���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $m_no
	 * @return blooean true - �޸�ȸ��O �Ϲ�ȸ������ ���󺹿��Ǿ�����, false - �޸�ȸ��X , ���󺹿� ���� �ʾ�����
	 * @date 2015-10-29
	 */
	public function checkDormantSocialLogin($m_no)
	{
		global $db;

		// ȸ������ ��ȸ
		$query = $db->_query_print('SELECT m_no, m_id, dormant_regDate FROM ' . GD_MEMBER . ' WHERE m_no = [i]', $m_no);
		$res = $db->query($query);
		list($m_no, $m_id, $dormant_regDate) = $db->fetch($res);
		if($m_no){
			if($dormant_regDate != '0000-00-00 00:00:00' && !empty($dormant_regDate)){
				$restoreResult = self::executeMemberRestoreUser((array)$m_no, $m_id);
				if($restoreResult == true){
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * �޸�ȸ�� ���� - USER PAGE �α���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $m_no
	 * @return string
	 * @date 2015-10-29
	 */
	private function executeMemberRestoreUser($m_no, $id)
	{
		try {
			self::writeLog('startLogin', '�޸���� ȸ�� ����[���� �α��� ID - '.$id.']');

			//ȸ�����̺�� ���� �̵�
			$errorMessage = self::memberCopy($m_no);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('2step', 'STEP1. RESTORE UPDATE MEMBER TABLE OK!');

			//�޸�ȸ�� remove
			$errorMessage = self::removeDormantMember($m_no);
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('2step', 'STEP2. REMOVE DORMANT MEMBER ROW OK!');
			self::writeLog('success', '');
			self::writeLog('end', '');

			return true;
		}
		catch(Exception $e){
			self::writeLog('error', $e->getMessage());
			self::writeLog('end', '');

			return false;
		}
	}

	/**
	 * ID ã�� - USER PAGE
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type, array $postData
	 * @return array
	 * @date 2015-10-29
	 */
	public function findIdUser($type, $postData)
	{
		global $checked, $db;

		$query = $where = $m_id = $name = '';

		if(self::checkDormantAgree('y') == true){
			if($type == 'name'){
				$where = "AES_DECRYPT(UNHEX(name), '".$this->secretKey."') = '".$postData['srch_name']."' ";
				if($checked['useField']['email']) $where .= " AND AES_DECRYPT(UNHEX(email), '".$this->secretKey."') = '".$postData['srch_mail']."' ";

				$query = "SELECT m_id, AES_DECRYPT(UNHEX(name), '".$this->secretKey."') FROM " . $this->tableName . " WHERE " . $where;
			}
			else if($type == 'dupeinfo'){
				$query = "SELECT m_id, AES_DECRYPT(UNHEX(name), '".$this->secretKey."') FROM " . $this->tableName . " WHERE dupeinfo = '".$postData[dupeinfo]."'";
			}
			else {
				$query = '';
			}

			if($query){
				$res = $db->query($query);
				list($m_id, $name) = $db->fetch($res);
			}
		}

		return array($m_id, $name);
	}

	/**
	 * ��й�ȣ ã�� - USER PAGE
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type, array $postData
	 * @return array
	 * @date 2015-10-29
	 */
	public function findPasswordUser($type, $postData)
	{
		global $checked, $db, $now, $memberpass;

		$query = '';
		$member = array();

		if(self::checkDormantAgree('y') == true){
			switch($type){
				case 'name':
					$query = sprintf("SELECT mb.m_id, AES_DECRYPT(UNHEX(mb.email), '".$this->secretKey."') AS email, AES_DECRYPT(UNHEX(mb.mobile), '".$this->secretKey."') AS mobile, otp.token, otp.expire FROM " . $this->tableName . " AS mb LEFT JOIN " . GD_OTP . " AS otp ON mb.m_id = otp.m_id AND otp.expire > '%s' WHERE mb.m_id = '%s' and AES_DECRYPT(UNHEX(mb.name), '".$this->secretKey."')='%s'"
						, date('Y-m-d H:i:s', $now)
						, $db->_escape($postData['srch_id'])
						, $db->_escape($postData['srch_name'])
					);

					if($checked['useField']['email']){
						$query .=  " AND AES_DECRYPT(UNHEX(mb.email), '".$this->secretKey."') = '".$db->_escape($postData['srch_mail'])."' ";
					}
				break;

				case 'dupeinfo':
					$query = sprintf("SELECT mb.m_id, AES_DECRYPT(UNHEX(mb.email), '".$this->secretKey."') AS email, AES_DECRYPT(UNHEX(mb.mobile), '".$this->secretKey."') AS mobile, otp.token, otp.expire FROM ".$this->tableName." AS mb LEFT JOIN ".GD_OTP." as otp ON mb.m_id = otp.m_id AND otp.expire > '%s' WHERE mb.dupeinfo = '%s'", date('Y-m-d H:i:s', $now), $db->_escape($postData['dupeinfo']));
				break;

				case 'send':
					$query = sprintf("SELECT AES_DECRYPT(UNHEX(mb.mobile), '".$this->secretKey."') AS mobile, AES_DECRYPT(UNHEX(mb.email), '".$this->secretKey."') AS email, AES_DECRYPT(UNHEX(mb.name), '".$this->secretKey."') AS name, mb.m_id, otp.token, otp.otp, otp.expire, otp.auth FROM ".GD_OTP." AS otp INNER JOIN ".$this->tableName." AS mb ON otp.m_id = mb.m_id WHERE otp.m_id = '%s' AND otp.token > '' AND otp.token = '%s'", $db->_escape($postData['m_id']), $db->_escape($postData['token']));
				break;

				case 'change':
					$query = sprintf("
						UPDATE " . $this->tableName . " AS mb INNER JOIN " . GD_OTP . " AS otp ON mb.m_id = otp.m_id AND otp.expire > '%s' SET
							mb.password = password('%s'),
							mb.password_moddt = NOW()
						WHERE
							otp.m_id = '%s' AND otp.token = '%s'", date('Y-m-d H:i:s'), $db->_escape($postData['pwd']), $db->_escape($postData['m_id']), $db->_escape($postData['token']));

					$result = $db->query($query);
					if($result){
						self::writeLog('changePassword', '�н����� ���� - �������[ID - '. $postData['m_id'].']');
					}

					return $result;
				break;

				case 'originalChange' :
					$result = $db->query("UPDATE ".$this->tableName." SET password=PASSWORD('".$memberpass."') WHERE m_id='" . $postData['srch_id'] . "'");
					if($result){
						self::writeLog('changePassword', '�н����� ���� - �������[ID - '. $postData['srch_id'].']');
					}
				break;

				case 'originalChangeLoad' :
					$name = $mobile = '';
					list($name, $mobile) = $db->fetch("SELECT AES_DECRYPT(UNHEX(name), '".$this->secretKey."') AS name, AES_DECRYPT(UNHEX(mobile), '".$this->secretKey."') AS mobile FROM ".$this->tableName." WHERE m_id='" . $postData['srch_id'] . "'");

					return array($name, $mobile);
				break;

				default :
					$query = '';
				break;
			}

			if($query){
				$member = $db->fetch($query, 1);
			}

			return $member;
		}
		else {
			return '';
		}
	}

	/**
	 * �޸�ȸ�� Ȯ��
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param array $data, string $checkType
	 * @return blooean
	 * @date 2015-10-29
	 */
	public function checkDormantMember($data, $checkType)
	{
		global $db;

		$dormant_regDate = $m_no = $where = '';
		$where = $checkType . " = '".$data[$checkType]."'";

		list($dormant_regDate) = $db->fetch("SELECT dormant_regDate FROM " . GD_MEMBER . " WHERE ".$where." LIMIT 1");
		list($m_no) = $db->fetch("SELECT m_no FROM " . $this->tableName . " WHERE ".$where." LIMIT 1");
		if($dormant_regDate && $dormant_regDate!='0000-00-00 00:00:00' && $m_no){
			return true;
		}

		return false;
	}

	/**
	 * �̸��� �ߺ�üũ - ȸ������
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $email, $returnType
	 * @return string $returnEmail, $returnM_id, array
	 * @date 2015-10-29
	 */
	public function checkDormantEmail($email, $returnType)
	{
		global $db;

		if(self::checkDormantAgree('y') == true){
			list($returnEmail, $returnM_id) = $db->fetch("SELECT AES_DECRYPT(UNHEX(email), '".$this->secretKey."') AS email, m_id FROM ".$this->tableName." WHERE AES_DECRYPT(UNHEX(email), '".$this->secretKey."') = '".$email."'");

			if($returnType == 'email'){
				return $returnEmail;
			}
			else if($returnType == 'm_id'){
				return $returnM_id;
			}
			else {
				return array($returnEmail, $returnM_id);
			}
		}
	}

	/**
	 *�޸�ȸ���� ��û������ ���� ȸ�� ���
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $dupeinfo
	 * @return int $count
	 * @date 2015-10-29
	 */
	public function getCountDupeinfoFromDormant($dupeinfo)
	{
		global $db;

		$count = 0;
		if(self::checkDormantAgree('y') == true){
			list($count) = $db->fetch("SELECT COUNT(*) FROM ".$this->tableName." WHERE dupeinfo = '".$dupeinfo."'");
		}

		return $count;
	}

	/**
	 * �޸�ȸ�� ������ ����, ȯ��
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param int $m_no, int $emoney
	 * @return void
	 * @date 2015-10-29
	 */
	public function getEmoneyUpdateQuery($m_no, $emoney, $type='')
	{
		$query = '';
		if($type == '-'){
			$query = "UPDATE ".$this->tableName." SET emoney = emoney - '".$emoney."' WHERE m_no = '".$m_no."'";
		}
		else {
			$query = "UPDATE ".$this->tableName." SET emoney = emoney + '".$emoney."' WHERE m_no = '".$m_no."'";
		}

		return $query;
	}

	/**
	 * ���ϸ� ���Űź� ������Ʈ
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $m_id
	 * @return blooean
	 * @date 2015-10-29
	 */
	public function updateMailling($m_id)
	{
		global $db;

		$res = false;
		$res = $db->query("UPDATE " . $this->tableName . " SET mailling = 'n' WHERE m_id='".$m_id."'");

		return $res;
	}

	/**
	 * �޸�ȸ�� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type
	 * @return array $data
	 * @date 2015-10-29
	 */
	public function getDormantInfo($type, $data)
	{
		global $db;

		if(self::checkDormantAgree('y') == true){
			switch($type){
				case 'emailDeny' :
					$m_no = $email = '';
					list($m_no, $email) = $db->fetch("select m_no, AES_DECRYPT(UNHEX(email), '".$this->secretKey."') AS email from " . $this->tableName . " WHERE m_id='".$data['m_id']."'");

					return array($m_no, $email);
				break;

				case 'checkNickname':
					$nickName = '';
					list ($nickName) = $db->fetch("SELECT nickname FROM ".$this->tableName." WHERE nickname='".$data['nickname']."'");

					return $nickName;
				break;

				case 'level' :
					$level = '';
					list ($level) = $db->fetch("SELECT level FROM ".$this->tableName." WHERE m_no='".$data['aoc_m_no']."'");

					return $level;
				break;
			}
		}
	}

	/**
	 * �޸�ȸ�� �������� update ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param int $m_no, $member_sum, $member_cnt
	 * @return string $query
	 * @date 2015-10-29
	 */
	public function getSumSaleUpdateQuery($m_no, $member_sum, $member_cnt)
	{
		$query = '';
		$query = "UPDATE ".$this->tableName." SET sum_sale='".$member_sum."', cnt_sale='".$member_cnt."',last_sale=now() WHERE m_no='".$m_no."'";

		return $query;
	}

	/**
	 * ������ ���� �޴������� ����
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param int $aocSno
	 * @return string $query
	 * @date 2015-10-29
	 */
	public function getOtpContactQuery($aocSno)
	{
		global $db;

		$query = '';
		$query = sprintf("select aoc.aoc_mobile, AES_DECRYPT(UNHEX(mb.name), '".$this->secretKey."') AS name, mb.m_id, mb.m_no from gd_admin_otp_contact as aoc inner join ".$this->tableName." as mb on aoc.aoc_m_no = mb.m_no where mb.level >= 80 and aoc.aoc_sno = '%s'", $db->_escape($aocSno));

		return $query;
	}

	/**
	 * �ڵ����� ���� ���� - SMS
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return boolean
	 * @date 2015-11-26
	 */
	public function checkAutoSmsExecuteAble()
	{
		$dormantSmsConfFilePath = dirname(__FILE__) . '/../conf/sms/dormant.php';
		if(is_file($dormantSmsConfFilePath)){
			include_once($dormantSmsConfFilePath);
		}
		$dormantCheckSmsDate = '';
		if(is_file($this->configFile)){
			@include $this->configFile;
			$dormantCheckSmsDate = $configDormantAgree['checkDormantSmsDate'];
		}

		$dormantSmsCount_30 = $dormantSmsCount_7 = 0;
		$dormantSmsCount_30 = self::getDormantMemberCount('dormantMemberAutoSms_30');
		$dormantSmsCount_7 = self::getDormantMemberCount('dormantMemberAutoSms_7');

		$dormantSmsConfigCheck = false;
		if((($sms_auto['sendBeforeDay_30'] == 'y' && $dormantSmsCount_30 > 0) || ($sms_auto['sendBeforeDay_7'] == 'y' && $dormantSmsCount_7 > 0)) && $sms_auto['send_c'] == 'on'){
			$dormantSmsConfigCheck = true;
		}

		//��� ��뼳�� ���� - SMS üũ�� üũ - SMS�߼� ���� Ȯ��
		if($this->checkDormantAgree() === true && (empty($dormantCheckSmsDate) || ($dormantCheckSmsDate && $dormantCheckSmsDate != '0000-00-00 00:00:00' && $dormantCheckSmsDate < date("Ymd") )) && $dormantSmsConfigCheck === true){
			return true;
		}

		return false;
	}

	/**
	 * �޸�ȸ�� ��ȯ ����� SMS �߼�
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return boolean
	 * @date 2015-11-26
	 */
	public function executeDormantSms()
	{
		try {
			$errorMessage = '';

			self::writeLog('start', '�޸� ȸ�� ��ȯ �� SMS �߼�');

			//���� ���������� Ȯ��
			$errorMessage = self::checkProcess('dormantAutoSms');
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setStartTemp('dormantAutoSms');

			//SMS �߼�
			$errorMessage = self::sendDormantSms();
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP1. SMS SEND OK!');

			$errorMessage = self::updateDormantSms();
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP2. UPDATE MEMBER SEND SMS DATE OK!');

			$errorMessage = self::setDormantConfigFile('dormantAutoSms');
			if($errorMessage != ''){
				throw new Exception($errorMessage);
			}
			self::writeLog('3step', 'STEP3. UPDATE CONFIG FILE OK[SMS]!');

			self::writeLog('success', '');

			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAutoSms');

			return true;
		}
		catch(Exception $e){
			//�ӽ����� ���� - �ߺ� ���� ����
			self::setEndTemp('dormantAutoSms');

			self::writeLog('error', $e->getMessage());

			return false;
		}
	}

	/**
	 * SMS�߼�
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string
	 * @date 2015-11-26
	 */
	private function sendDormantSms()
	{
		global $db;

		try {
			$dormantSmsCount_30 = $dormantSmsCount_7 = 0;
			$dormantSmsConfFilePath = dirname(__FILE__) . '/../conf/sms/dormant.php';
			if(is_file($dormantSmsConfFilePath)){
				include_once($dormantSmsConfFilePath);
			}

			$dormantSmsCount_30 = self::getDormantMemberCount('dormantMemberAutoSms_30');
			$dormantSmsCount_7 = self::getDormantMemberCount('dormantMemberAutoSms_7');

			if($dormantSmsCount_30 > 0 && $sms_auto['sendBeforeDay_30'] == 'y' && $sms_auto['send_c'] == 'on'){
				$query = self::getDormantQuery('dormantMemberAutoSms_30');
				$res = $db->query($query);
				if(!$res){
					throw new Exception("Query Error - fail to load auto sms 30 Query.");
				}

				while($member = $db->fetch($res, 1)){
					$GLOBALS['dataSms'] = $smsData = array();
					if(str_replace("-", "", $member['mobile'])){
						$smsData['id']	= $member['m_id'];
						$smsData['name']	= $member['name'];
						$smsData['mobile']	= $member['mobile'];
						$GLOBALS['dataSms'] = $smsData;
						sendSmsCase('dormant', $smsData['mobile']);
						$this->sendSms_successID_30[] = $member['m_id'];
					}
				}
			}

			if($dormantSmsCount_7 > 0 && $sms_auto['sendBeforeDay_7'] == 'y' && $sms_auto['send_c'] == 'on'){
				$query = self::getDormantQuery('dormantMemberAutoSms_7');
				$res = $db->query($query);
				if(!$res){
					throw new Exception("Query Error - fail to load auto sms 7 Query.");
				}
				while($member = $db->fetch($res, 1)){
					$GLOBALS['dataSms'] = $smsData = array();
					if(str_replace("-", "", $member['mobile'])){
						$smsData['id']	= $member['m_id'];
						$smsData['name']	= $member['name'];
						$smsData['mobile']	= $member['mobile'];
						$GLOBALS['dataSms'] = $smsData;
						sendSmsCase('dormant', $smsData['mobile']);
						$this->sendSms_successID_7[] = $member['m_id'];
					}
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * SMS �߼��� ������Ʈ
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-11-26
	 */
	private function updateDormantSms()
	{
		global $db;

		try {
			$sendSmsSuccessList_30 = $sendSmsSuccessList_7 =  array();
			$sendSmsSuccessList_30 = @array_chunk($this->sendSms_successID_30, 100);
			$sendSmsSuccessList_7 = @array_chunk($this->sendSms_successID_7, 100);

			foreach($sendSmsSuccessList_30 as $m_id){
				$query = "UPDATE " . GD_MEMBER . " SET dormant_smsSendCheck = '30' WHERE m_id IN ('" . implode("','", $m_id) . "')";
				$res = $db->query($query);
				if(!$res){
					throw new Exception("Query Error - fail to update sms 30 date.");
				}
			}
			foreach($sendSmsSuccessList_7 as $m_id){
				$query = "UPDATE " . GD_MEMBER . " SET dormant_smsSendCheck = '7' WHERE m_id IN ('" . implode("','", $m_id) . "')";
				$res = $db->query($query);
				if(!$res){
					throw new Exception("Query Error - fail to update sms 7 date.");
				}
			}

			return '';
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}
}
?>