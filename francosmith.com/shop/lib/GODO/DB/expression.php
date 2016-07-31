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
 * GODO_DB_expression
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_expression {

	/**
	 * quote ó�� ���� ���� ���ڿ�
	 * @var string
	 */
	private $expression = '';

	/**
     * string ĳ���ý� ������ ���ڿ��� ����
	 * @return string
	 */
	public function __toString() {
		return $this->getExpression();
	}

	/**
	 * ���ڿ��� ����
     * @param string $str
	 * @return void
	 */
	public function setExpression($str) {
		$this->expression = $str;
	}

	/**
	 * ������ ���ڿ��� ����
	 * return string
	 */
	public function getExpression() {
		return (string) $this->expression;
	}

}
?>