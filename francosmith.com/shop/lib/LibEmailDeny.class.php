<?php
/**
 * LibEmailDeny
 * �̸��� ���Űź� Ŭ����
 * @author pr @ godosoft development team.
 */
class LibEmailDeny
{
	protected $cryptKey = 'dusqhdtkdtmd';

	/**
	 * ���Űź� ��ũ ����
	 * @param string $id ȸ�����̵�
	 * @return string
	 */
	public function getDenyLink($id)
	{
		global $cfg;
		if (!$cfg) include dirname(__FILE__).'/../conf/config.php';

		$r_url = explode(':',$_SERVER['HTTP_HOST']);
		$r_dns = str_replace(array('https://','http://','www.'),'',$r_url[0]).$cfg['rootDir'];
		$enTric = urlencode($this->_encrypt(date('Ymi')));
		$enId = urlencode($this->_encrypt(trim($id)));
		$denyLink = 'http://'.$r_dns. '/proc/emailDeny.php?k='.$enTric.'&id=' . $enId;
		return $denyLink;
	}

	/**
	 * ���Űź� ó��
	 * @param string $id ȸ�����̵�
	 * @return string
	 */
	public function setDeny($k, $id)
	{
		global $db, $cfg;
		if (!$cfg) include dirname(__FILE__).'/../conf/config.php';

		try {
			$dormant = Core::loader('dormant');

			// ���� ��ȿ�� üũ
			$k = trim($k);
			$id = trim($id);
			if ($k == '') throw new Exception('k ���� ��� ����.');
			if ($id == '') throw new Exception('id ���� ��� ����.');

			// id ���ڵ�
			$m_id = $this->_decrypt($id);
			if ($m_id == '') throw new Exception('id ���� ���ڵ� ���� ����.');

			$dormantMember = false;
			$dormantMember = $dormant->checkDormantMember(array('m_id'=>$m_id), 'm_id');

			// ȸ�� ���翩�� üũ
			if($dormantMember === true){
				list($data['m_no'], $data['email']) = $dormant->getDormantInfo('emailDeny', array('m_id'=>$m_id));
			}
			else {
				$data = $db->fetch("select m_no, email from ".GD_MEMBER." where m_id='$m_id'");
			}
			if ($data['m_no'] == '') throw new Exception($m_id.'�� �������� ����.');

			// ���Űź� ó��
			if($dormantMember === true){
				$res = $dormant->updateMailling($m_id);
			}
			else {
				$res = $db->query("update ".GD_MEMBER." set mailling='n' where m_id='$m_id'");
			}
			if ($res == false) throw new Exception('���Űź� ó�� ����');

			// ���ŰźοϷ� �ȳ�����
			if ($data['email'] && $cfg['mailyn_30'] == 'y') {
				$modeMail = 30;
				$automail = Core::loader('automail');
				$automail->_set($modeMail,$data['email'],$cfg);
				$automail->_assign('mail',$data['email']);
				$automail->_assign('agreeDate_year',date('Y'));
				$automail->_assign('agreeDate_month',date('m'));
				$automail->_assign('agreeDate_day',date('d'));
				$automail->_send();
			}

			return array('result' => true, 'msg' => '���������� ���Űźΰ� �Ǿ����ϴ�.('.$m_id.')');
		} catch(Exception $e) {
			return array('result' => false, 'msg' => $e->getMessage());
		}
	}

	/**
	 * ���ڵ� ����
	 * @param string $data ���ڵ� ������
	 * @return string
	 */
	protected function _decrypt($data)
	{
		$xxtea = Core::loader('xxtea');
		$xxtea->setKey($this->cryptKey);
		return @unserialize($xxtea->decrypt(base64_decode($data)));
	}

	/**
	 * ���ڵ� ����
	 * @param string $data ���ڵ� ������
	 * @return string
	 */
	protected function _encrypt($data)
	{
		$xxtea = Core::loader('xxtea');
		$xxtea->setKey($this->cryptKey);
		return base64_encode($xxtea->encrypt(serialize($data)));
	}
}
?>