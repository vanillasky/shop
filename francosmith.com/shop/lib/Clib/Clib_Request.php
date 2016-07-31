<?php
/**
 * Clib_Request
 * Application ���ο��� GET, POST �� ����
 * @author extacy @ godosoft development team.
 */
class Clib_Request
{
	/**
	 * GET
	 */
	private $_GET;

	/**
	 * POST
	 */
	private $_POST;

	/**
	 * @var
	 */
	private $_store;

	/**
	 * Construct
	 * @return
	 */
	public function __construct()
	{
		$this->_init();
	}

	/**
	 * initialize
	 * @return
	 */
	private function _init()
	{
		$magicQuote = get_magic_quotes_gpc();

		if (isset($_GET)) {
			$this->_GET = $magicQuote ? Core::helper('string')->stripslashes($_GET) : $_GET;
		}

		if (isset($_POST)) {
			$this->_POST = $magicQuote ? Core::helper('string')->stripslashes($_POST) : $_POST;
		}

		// @todo : model ���ο��� super global ������ ����� ���� ����
		// ���ο� ����� �ʿ���
		// unset($_GET, $_POST);

	}

	/**
	 * GET, POST �� ������ ���� ����
	 * @param string $name ���� ������ Ű
	 * @param mixed $default [optional] �������� ���� ��� ������ �⺻��
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		$var = $default;

		// G -> P
		switch (true) {
			case isset($this->_store[$name]) :
				$var = $this->_store[$name];
				break;
			case isset($this->_GET[$name]) :
				$var = $this->_GET[$name];
				break;
			case isset($this->_POST[$name]) :
				$var = $this->_POST[$name];
				break;
			default :
				$this->_store[$name] = $var;
				break;
		}

		return $var;

	}

	public function set($name, $value)
	{
		$this->_store[$name] = $value;

		return $this;
	}

	/**
	 *
	 * @param string $gp [optional]
	 * @return
	 */
	public function gets($gp = 'post')
	{
		$gp = strtoupper($gp);

		switch (strtoupper($gp)) {
			case 'POST' :
				return $this->_POST;
				break;
			case 'GET' :
				return $this->_GET;
				break;
			default :
				return array();
				break;
		}

	}

	/**
	 *
	 * @param object $name
	 * @return
	 */
	public function file($name)
	{
		return isset($_FILES[$name]) ? $_FILES[$name] : null;
	}

	/**
	 * ���� ��ûŸ���� ����
	 * @return string POST, GET, DELETE, PUT
	 */
	public function getMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

}
