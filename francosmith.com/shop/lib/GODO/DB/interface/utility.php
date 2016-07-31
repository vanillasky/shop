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
 * GODO_DB_interface_utility
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
interface GODO_DB_interface_utility {

	/**
	 * ����¡ ó���� GODO_DB_statement �� ����
 	 * @param GODO_DB_builder $builder
	 * @param integer $page_size [optional]
	 * @param integer $page [optional]
	 * @return GODO_DB_statement
	 */
	public function getPaging(GODO_DB_builder $builder,  $page_size=20,  $page=1);

	/**
	 * ����¡ ó�� ���� ���� GODO_DB_statement �� ����
	 * @param GODO_DB_builder $builder
	 * @return GODO_DB_statement
	 */
	public function getAll(GODO_DB_builder $builder);

	/**
	 * �� row �� ����
	 * @param GODO_DB_builder $builder
	 * @return array
	 */
	public function getOne(GODO_DB_builder $builder);

}
?>
