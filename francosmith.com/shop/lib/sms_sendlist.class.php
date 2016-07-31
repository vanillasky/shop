<?php
/*
 * sms sendlist ó�� Ŭ����
 *
 * @author sms_sendlist.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2014-11-20
 */
class sms_sendlist
{
	var $sms_logNo, $sms_mode;

	/*
	 * SMS SENDLIST array insert
	 * @param array
	 * @return void
	 * @date 2014-11-20
	 */
	function setListInsert($data)
	{
		global $db;

		$chunkNum	= 50;
		$idx		= 0;
		$total		= count($data);
		$dataArray  = array_chunk($data, $chunkNum);

		foreach($dataArray as $list){
			$query = "INSERT INTO " . GD_SMS_SENDLIST . " (sms_mode, sms_memNo, sms_logNo, sms_name, sms_phoneNumber, sms_phoneNumberApi, sms_status, sms_send_status, sms_regdt) VALUES ";
			foreach($list as $key => $value){
				$idx++;
				if(($chunkNum-1) == $key || $idx == $total){
					$queryEnd = ";";
				}
				else {
					$queryEnd = ",";
				}
				$query .= " ('". $this->sms_mode . "', '". $value['sms_memNo'] . "', '". $this->sms_logNo . "', '". $value['sms_name'] . "', '". $value['phone'] . "', '". $this->setPhoneNumberApi($value['phone']) . "' , 'r', 'n', now())" . $queryEnd;
			}
			$db->query($query);
		}
	}

	/*
	 * SMS SENDLIST simple insert
	 * @param string phoneNumber, sms log no, reserve
	 * @return void
	 * @date 2014-11-20
	 */
	function setSimpleInsert($sms_phoneNumber, $sms_logNo, $reserve){
		$list['sms_mode']			= $this->getSms_mode($reserve);
		$list['sms_phoneNumber']	= $sms_phoneNumber;
		list($list['sms_name'], $list['sms_memNo'])  = $this->getMember($sms_phoneNumber);
		$GLOBALS[db]->query("
			INSERT INTO " . GD_SMS_SENDLIST . " SET
				sms_mode			= '". $list['sms_mode'] . "',
				sms_memNo			= '". $list['sms_memNo'] . "',
				sms_logNo			= '". $sms_logNo . "',
				sms_name			= '". $list['sms_name'] . "',
				sms_phoneNumber		= '". trim($list['sms_phoneNumber']) . "',
				sms_phoneNumberApi	= '". trim($this->setPhoneNumberApi($list['sms_phoneNumber'])) . "',
				sms_status			= 'r',
				sms_send_status		= 'n',
				sms_regdt			= now()
		");
	}

	/*
	 * phonenumber setting
	 * @param string
	 * @return string
	 * @date 2014-11-20
	 */
	function setPhoneNumberApi($phoneNumber)
	{
		return str_replace("-", "", $phoneNumber);
	}

	/*
	 * sms send mode
	 * @param int
	 * @return string
	 * @date 2014-11-20
	 */
	function getSms_mode($reserve)
	{
		if($reserve == 1){
			//����߼�
			return 'r';
		}
		else{
			//��ù߼�
			return 'i';
		}
	}

	/*
	 * get member info
	 * @param string phonenumber
	 * @return array name, m_no
	 * @date 2014-11-20
	 */
	function getMember($phoneNumber)
	{
		global $db;

		list($m_no, $name) = $db->fetch("SELECT m_no, name FROM " . GD_MEMBER . " WHERE mobile = '" . $phoneNumber . "' LIMIT 1");
		if(!$m_no && !$name){
			list($name) = $db->fetch("SELECT sms_name FROM " . GD_SMS_ADDRESS . " WHERE sms_mobile = '" . $phoneNumber . "' ORDER BY sno desc LIMIT 1");
		}
		
		return array($name, $m_no);
	}


	/*
	 * sendlist �������� update
	 * @param string sms log ��ȣ, ����ȣ
	 * @return void
	 * @date 2014-11-20
	 */
	function updateSendStatus($sms_logNo, $phoneNumber)
	{
		$phoneNumberArr = explode(",", $phoneNumber);
		foreach($phoneNumberArr as $sms_phoneNumber){
			$GLOBALS[db]->query("
				UPDATE " . GD_SMS_SENDLIST . " SET 
					sms_send_status = 'y' 
				WHERE 
					sms_logNo = '".$sms_logNo."' and 
					sms_phoneNumber = '" . $sms_phoneNumber . "' and 
					sms_send_status = 'n' 
				LIMIT 1");
		}
	}

	/*
	 * sendlist sms_status
	 * @param string  ���ӻ���, �߼ۻ���, ��� or ����, ����ð�
	 * @return string  c - �������, r - ������Ŵ��, y - �߼ۼ���, n - �߼۽���
	 * ��ù߼� step r -> y or n
	 * ����߼� step r -> y or n or c
	 * @date 2014-11-20
	 */
	function getSendListStatus($acceptStatus, $sendStatus, $reserveType, $reserveDate)
	{
		if($acceptStatus == 'y'){
			switch($sendStatus){
				case 'c':
					return '�������';
				break;

				case 'r':
					if($reserveType == 'r'){
						if(strtotime($reserveDate) < strtotime('now')){
							return '������Ŵ��';
						}
						else {
							return '�߼۴��';	
						}
					}
					else {
						return '������Ŵ��';
					}
				break;

				case 'y':
					return '�߼ۼ���';
				break;

				case 'n':
					return '�߼۽���';
				break;
			}
		}
		else {
			return '�߼ۿ�û����';
		}
	}

	/*
	 * ���� �����ڵ� ����Ʈ
	 * @param
	 * @return array
	 * @date 2014-11-20
	 */
	function errorCodeList()
	{
		$smsErrorCode = array(
			'1'=>'���۽ð� �ʰ�',
			'2'=>'��������',
			'3'=>'���� ����',
			'4'=>'�߸��� ��ȭ��ȣ',
			'5'=>'�Ͻ� ���� ����',
			'6'=>'��Ÿ �ܸ��� ����',
			'7'=>'����� �� ��� ����',
			'8'=>'�ڵ��� ȣ ó�� ��',
			'9'=>'������ �Ͻ����� ����',
			'10'=>'�ߺ��� ���Ź�ȣ',
			'11'=>'�߼��������� ���� ����',
			'12'=>'��Ÿ'
		);
		return $smsErrorCode;
	}

	/*
	 * sms log �������п��� update
	 * @param void
	 * @return void
	 * @date 2014-11-20
	 */
	function updateSmsLogAcceptFail($smsLogInsertId)
	{
		global $db;

		list($cnt) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo = '" . $smsLogInsertId . "' and sms_send_status = 'n'");
		if($cnt > 0){
			$db->query("UPDATE " . GD_SMS_LOG . " SET accept_fail_check = 'y' WHERE sno = '" . $smsLogInsertId . "' and accept_fail_check = 'n'");
		}
		else {
			$db->query("UPDATE " . GD_SMS_LOG . " SET accept_fail_check = 'n' WHERE sno = '" . $smsLogInsertId . "' and accept_fail_check = 'y'");
		}
	}

	/*
	 * sms log �߼۽��п��� update
	 * @param void
	 * @return void
	 * @date 2014-11-20
	 */
	function updateSmsLogSendFail($smsLogInsertId)
	{
		global $db;

		list($cnt) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo = '" . $smsLogInsertId . "' and sms_status = 'n'");
		if($cnt > 0){
			$db->query("UPDATE " . GD_SMS_LOG . " SET send_fail_check = 'y' WHERE sno = '" . $smsLogInsertId . "' and send_fail_check = 'n'");
		}
	}

	/*
	 * get failcode from sms_faillist
	 * @param int  (sms faillist sno)
	 * @return string
	 * @date 2014-11-20
	 */
	function getFailList_FailCode($sno)
	{
		list($failCode) = $GLOBALS['db']->fetch(" SELECT failCode FROM " . GD_SMS_FAILLIST . " WHERE sno = '" . $sno . "' LIMIT 1");

		return $failCode;
	}

	/**
	 * update status (reserve type)
	 * @param  void
	 * @return void
	 * @date 2014-11-20
	 */
	function updateReserveSendingAll()
	{
		global $db;

		if(preg_match('/sms.sendList.php/', $GLOBALS['_SERVER']['SCRIPT_NAME'])){
			$where = "reservedt < now() and reservedt != '0000-00-00 00:00:00' and reservedt != '' and status = '1'";

			//�߼۴������ ����߼۰� Ȯ��
			list($updateCnt) = $db->fetch("SELECT COUNT(*) FROM ".GD_SMS_LOG." WHERE $where");
			if($updateCnt > 0){
				$GLOBALS['db']->query("UPDATE ".GD_SMS_LOG." SET status = '3' WHERE $where");
			}
		}
	}

	/**
	 * get faillist phonenumber
	 * @param string  (faillist sno)
	 * @return array
	 * @date 2014-11-20
	 */
	function getSmsFailListNumber($failListSno)
	{
		global $db;

		$_failListSnoArr = explode("|", $failListSno);
		$failListSnoArr = array_chunk($_failListSnoArr, 1000);
		foreach($failListSnoArr as $v){
			$where = implode("','", $v);
			$result = $db->query("SELECT phoneNumber FROM " . GD_SMS_FAILLIST . " WHERE sno IN( '" . $where . "') ");
			while($row = $db->fetch($result, 1)){
				$failListResult[] = $row['phoneNumber'];
			}
		}
		return $failListResult;
	}

	/*
	 * ����߼۰� �ߺ�Ȯ��
	 * @param string ��¥, ����, �޽���
	 * @return int
	 * @date 2014-11-20
	 */
	function checkOverlapReserve($type, $date, $_msg, $subject='')
	{
		if(is_array($_msg)){
			$msg = implode("", $_msg);
		}
		else {
			$msg = $_msg;
		}
		
		if($type == 'lms'){
			$where = " and subject = '".$subject."'";
		}
		list($overLapCnt) = $GLOBALS['db']->fetch("SELECT COUNT(*) FROM " . GD_SMS_LOG . " WHERE reservedt = '".date("Y-m-d H:i:s", $date)."' and msg='". $msg . "' " . $where);

		return $overLapCnt;
	}
}
?>