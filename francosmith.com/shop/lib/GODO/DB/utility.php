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
 * GODO_DB_utility
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_utility {

	/**
	 * DB �ν��Ͻ�
 	 * @var GODO_DB
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
	 * �ν��Ͻ� ����
	 * @return GODO_DB_builder
	 */
	public function __clone() {

		return $this;

	}

	/**
	 * �� ���ڵ� ���� ����
	 * @param GODO_DB_builder $builder
	 * @return
	 */
	abstract protected function getTotalCount(GODO_DB_builder $builder);

}
?>