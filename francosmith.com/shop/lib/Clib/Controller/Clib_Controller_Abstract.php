<?php
/**
 * Clib_Controller_Abstract
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Controller_Abstract
{
	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Controller ó�� ����� ȭ�鿡 ����Ѵ�.
	 * @return void
	 */
	public function display()
	{
		Clib_Application::template_()->make();
		Clib_Application::template_()->display();
	}

	/**
	 * Clib_Application Ŭ���� ���� Controller ������,
	 * ������ ������ ������ �ڵ����� ����ȴ�.
	 * @return mixed
	 */
	abstract public function main();

	private function _back($msg = null)
	{
		// back ó��?

	}

	protected function redirect()
	{
		// redirect

	}

}
