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
 * GODO_DB_function Abstraction
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_function {

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
	 * ���ڿ��� quote ó�� ��
	 * @param string $value
	 * @return string
	 */
	protected function quote($value) {
		return $this->db->quote($value);
	}

	/**
     * ȸ�ǹ���(_) �� ���� ���� ���ڿ��� quote ó���Ͽ� ���� (ex: test123 -> 'test123')
     * ȸ�ǹ���(_) �� ���� ���, �̸� �����ϰ� ���� (ex: _ordno-> ordno)
	 * @param string $value
	 * @return string
	 */
	protected function getValue($value) {

		if (preg_match('/^_(.+)$/',$value,$matches)) $value = $matches[1];
		else $value = $this->quote($value);

		return $value;

	}

	/**
	 * description.
	 * @param string $val1
	 * @param string $val2
	 * @return string
	 */
	abstract public function left($val1, $val2);


	/**
	 * description.
	 * @param string $value
	 * @return
	 */
	abstract public function sum($value);

	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function count($value);

	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function min($value);

	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function max($value);


	/**
	 * description.
	 * @param string $val1
	 * @param string $val2
	 * @return string
	 */
	abstract public function concat($val1, $val2);


	/**
	 * description.
	 * @param string $value
	 * @return string
	 */
	abstract public function password($value);

	/**
	 * description.
	 * @param string $date
	 * @param string $format
	 * @return string
	 */
	abstract public function date_format($date, $format);

}

?>
