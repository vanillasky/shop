<?php
/**
 * Clib_Cookie
 * COOKIE �ڵ鷯
 * @author extacy @ godosoft development team.
 */
class Clib_Cookie
{
	/**
	 * ��Ű�� ���´�
	 * @param string $name
	 * @param mixed $value
	 * @param integer $expire [optional]
	 * @param string $path [optional]
	 * @param string $domain [optional]
	 * @param boolean $secure [optional]
	 * @param boolean $httponly [optional]
	 * @return
	 */
	public function set($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
	{
		if ( ! headers_sent()) {
			$expire = $expire > 0 ? $expire + time() : 0;
			setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
		}
		else {
			// ��Ű�� ���� �� ����.
		}
	}

	/**
	 * ��Ű�� ������ ���� ����
	 * @param string $name ������ ��Ű�� Ű
	 * @return mixed|null
	 */
	public function get($name)
	{
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}

	/**
	 * ��Ű�� �����Ѵ�(��Ű�� ����ó�� �Ѵ�)
	 * @param string $name ������ ��Ű�� Ű
	 * @return void
	 */
	public function del($name)
	{
		$this->set($name, false, - 86400);
	}

}
