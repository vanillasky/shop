<?php /**
 * Clib_Session
 * Application ���ο��� SESSION �� ����
 * @author extacy @ godosoft development team.
 */
class Clib_Session
{
	/**
	 * SESSION ������ ���� ����
	 * @param string $name ���� ������ Ű
	 * @param mixed $default [optional] �������� ���� ��� ������ �⺻��
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		$var = $default;

		switch (true) {
			case isset($_SESSION[$name]) :
				$var = $_SESSION[$name];
				break;
		}

		return $var;
	}

	/**
	 * ���ǿ� ����� ȸ�� ������ ����
	 * @return array
	 */
	private function _getMember()
	{
		return $this->get('sess');
	}

	/**
	 * �α��ε� ȸ�� ID ����
	 * @return string
	 */
	public function getMemberId()
	{
		$sess = $this->_getMember();
		return (string)$sess['m_id'];
	}

	/**
	 * �α��ε� ȸ�� ��ȣ ����
	 * @return integer
	 */
	public function getMemberNo()
	{
		$sess = $this->_getMember();
		return (int)$sess['m_no'];
	}

	/**
	 * �α��ε� ȸ�� ���� ����
	 * @return integer
	 */
	public function getMemberLevel()
	{
		$sess = $this->_getMember();
		return (int)$sess['level'];
	}

	public function isAdmin()
	{
		return $this->getMemberLevel() >= 80;
	}

	public function isAdult()
	{
		$sess = $this->_getMember();

		if ($sess['adult'] || $this->get('adult')) {
			return true;
		}

		return false;
	}

	public function canAccessAdult()
	{
		return $this->isAdmin() || $this->isAdult();
	}

	public function isLogged()
	{
		return $this->getMemberNo() > 0 ? true : false;
	}

}
