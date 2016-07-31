<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */

/**
 * GODO_DB_procedure
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_procedure {

	/**
	 * GODO_DB
	 * @var object
	 */
	protected $db;

	/**
	 * Construct
	 * @param GODO_DB
 	 * @return void
	 */
	public function __construct(&$db) {
		$this->db = $db;
	}

	/**
	 * ������ procedure �� �����ϰ� ����� ����
	 * @return mixed
	 */
	public function procedure() {

		$args = func_get_args();
		return call_user_func_array(array($this, 'execute'), $args);

	}

	/**
	 * ������ procedure �� �����ϰ� ����� ����
	 * @return mixed
	 */
	abstract protected function execute();

}
?>