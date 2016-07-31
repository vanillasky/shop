<?php
/**
 * Clib_Exception
 * @author extacy @ godosoft development team.
 */
class Clib_Exception extends Exception
{
	/**
	 * ���� ���
	 */
	const ERROR_COMMON = 0;
	const ERROR_CLASS_UNDEFINED = 1;
	const ERROR_DATA_UNLOADED = 2;
	const ERROR_PERMISSION_DENIED = 3;

	/**
	 * ���� ó��
	 * @param Clib_Exception $e
	 * @return void
	 */
	public static function displayMessage(Clib_Exception $e)
	{
		$code = $e->getCode();
		$msg = $e->getMessage();
		$trace = $e->getTraceAsString();

		// @todo : exception �߻��� ó�� ���� �߰�
		debug($code);
		debug($msg);
		debug($trace);

	}

}
