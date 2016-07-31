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
 * GODO_DB_interface_driver
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
interface GODO_DB_interface_driver {

	/**
	 * ����̹� �̸��� ����
	 * @return
	 */
	public function getName();

	/**
	 * DB ����
	 * @param array $server
	 * @return void
	 */
	public function connect( $server );

	/**
	 * DB ������ ����
	 * @param object $dbconn
	 * @return void
	 */
	public function disconnect( $dbconn );

	/**
	 * ���� �ڵ带 ����
	 * @return integer
	 */
	public function errorCode();

	/**
	 * ���� �޽����� ����
	 * @return string
	 */
	public function errorInfo();

	/**
	 * Ʈ����� ����
	 */
	public function begin();

	/**
	 * Ʈ����� �ѹ�
	 */
	public function rollback();

	/**
	 * Ʈ����� Ŀ��
	 */
	public function commit();

	/**
	 * ���ڿ��� �̽�������
	 * @param string $string
	 * @return string
	 */
	public function escape( $string );

	/**
	 * ������ ���Ե� pk �� ����
	 * @return integer
	 */
	public function lastID();

	/**
	 * ���̺� ������ ����
	 * @param string $table_name
	 * @return array
	 */
	public function desc($table_name);

}

?>
