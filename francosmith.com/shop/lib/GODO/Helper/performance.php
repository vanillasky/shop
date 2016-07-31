<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */

/**
 * GODO_helper_performance
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_performance extends GODO_helper {

	/**
	 * ���� �α�
	 * @var array
	 */
	private $log = array();

	/**
	 * ���� ���� ���� �ð� (����ũ�� Ÿ��)
	 * @var float
	 */
	private static $start;

	/**
	 * �޸� ��뷮
	 * @var integer
	 */
	private static $memory;

	/**
	 * ���� ����
	 * @return void
	 */
	public function start() {
		$this->start = $this->_time();
		$this->memory = memory_get_usage();
	}

	/**
	 * ���� �α� �� ���� (html)
	 * @return string
	 */
	public function __toString() {

		$_total['lap'] = 0;
		$_total['mem'] = 0;

		foreach ($this->log as $log) {
			$_total['lap'] += $log['lap'];
			$_total['mem'] += $log['mem'];
		}

		ob_start();
		print_r($_total);
		print_r($this->log);

		return ob_get_clean();

	}

	/**
	 * ����ũ�� Ÿ���� ����
	 * @return float
	 */
	private function _time() {

		list($ms, $s) = explode(' ', microtime());
		return (float)$ms + (float)$s;

	}

	/**
	 * ���� �ð��� ����
	 * @return float ���� �ð�
	 */
	private function _lap() {

		static $from = null;

		if ($from === null) $from = $this->start;

		$to = $this->_time();

		$lap = $to - $from;

		$from = $to;

		return $lap;

	}

	/**
	 * �޸� ��뷮�� ����
	 * @return integer
	 */
	private function _mem() {

		static $from = null;

		if ($from === null) $from = $this->memory;

		$to = memory_get_usage();

		$mem = $to - $from;

		$from = $to;

		return $mem;

	}

	/**
	 * ȣ�� ������, ���� (�޸�, �ð�) �� ����
	 * @param string $scope [optional]
	 * @return array
	 */
	public function checkpoint($scope='') {

		$_log = array(
			'lap' => $this->_lap(),
			'mem' => $this->_mem()
		);

		if ($scope) {
			$this->log[$scope][] = $_log;
		}
		else {
			$this->log[] = $_log;
		}

	}

}
?>
