<?php
/**
 * Clib_Storage
 * GLOBAL ó���ؾ��ϴ� ���̳�, Ÿ �𵨿� �����ϱ� ���� �����͸� �ڵ鸵
 * @author extacy @ godosoft development team.
 */
class Clib_Storage
{
	/**
	 * ����� ������
	 * @var array
	 */
	private static $_storage = array();

	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Ư�� Ű�� ����� ���� ����
	 * @param string $key ������ ���� Ű
	 * @return mixed Ű�� ����� ��
	 */
	public function get($key = null)
	{
		if (is_null($key)) {
			return $this->_storage;
		}
		else {
			return $this->_storage[$key];
		}

	}

	/**
	 * Ư�� Ű�� ���� ����
	 * @param string $key ������ ���� Ű
	 * @param mixed $value Ű�� ������ ��
	 * @return boolean ���� ������ true, �̹� ������ ��� false
	 */
	public function set($key, $value)
	{
		if ($this->has($key)) {
			return false;
		}
		else {
			$this->_storage[$key] = $value;
			return true;
		}
	}

	/**
	 * Ư�� Ű�� �����Ǿ� �ִ��� ���θ� ����
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		return isset($this->_storage[$key]);
	}

	/**
	 * Ư�� Ű�� ����
	 * @param string $key
	 * @return void
	 */
	public function del($key)
	{
		unset($this->_storage[$key]);
	}

	/**
	 * ������ ���� ��� GLOBAL ó����
	 * @return void
	 */
	public function toGlobal()
	{
		if (is_array($this->_storage) && ! empty($this->_storage)) {
			foreach ($this->_storage as $key => $value) {
				$GLOBALS[$key] = $value;
			}
		}
	}

}
