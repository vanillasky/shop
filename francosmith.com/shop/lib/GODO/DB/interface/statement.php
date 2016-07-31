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
 * GODO_DB_interface_statement
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
interface GODO_DB_interface_statement {

	/**
	 * ���� ����� ���ҽ��� ����
 	 * @return mixed resource or false
	 */
	public function getResultResource();

	/**
	 * ����
 	 * @param string $sql
	 * @return mixed ���� ���
	 */
	public function query( $sql );

	/**
	 * ���� ����� ���� �����Ͱ� ��ġ�� ���� fetch
	 * @param Integer $fetchstyle [optional]
	 * @return array
	 */
	public function fetch( $fetchstyle = 0 );

	/**
	 * ���� ����� ��� ���� fetch
	 * @param Integer $fetchstyle [optional]
	 * @return array
	 */
	public function fetchAll( $fetchstyle = 0 );

	/**
	 * ���ڿ��� escape
	 * @param string $string
	 * @return string
	 */
	public function escape( $string );

	/**
	 * ���� ����� ���ڵ� ���� ����
	 * @return integer
	 */
	public function rowCount();

	/**
	 * ���� ����� ���� �����͸� �̵�
	 * @param integer $field_offset �̵��� ��ġ
	 * @return boolean
	 */
	public function dataSeek( $field_offset );

	/**
	 * ���� ����� �迭�� ����(ȥ��)
 	 * @return array
	 */
	public function fetchArray();

	/**
	 * ���� ����� �迭�� ���� (����Ű)
 	 * @return array
	 */
	public function fetchAssoc();

	/**
	 * ���� ����� ��ü�� ����
 	 * @return object
	 */
	public function fetchObject();

	/**
	 * ������ ���Ե� pk �� ����
	 * @return integer
	 */
	public function lastID();

}

?>
