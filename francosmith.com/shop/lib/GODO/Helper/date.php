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
 * GODO_helper_date
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_date extends GODO_helper {

	/**
	 * ��¥���� ��� �� �ִ�/�ּ� ��
	 * @var array
	 */
	private $date_range = array(
			'y' => array(1000,9999),	// �ڵ� ���� ������ ġȯ�ǹǷ�, �� ���� �ƹ� �ǹ� ����.
			'm' => array(1,12),
			'd' => array(1,31),
			'h' => array(0,23),
			'i' => array(0,59),
			's' => array(0,59),
		);

	/**
	 * ��¥ ����
	 * @var string
	 */
	private $date_format = 'Y-m-d H:i:s';

	/**
	 * ���ڿ��� y,m,d,h,i,s Ű�� ���� �迭�� ��ȯ�Ͽ� ����
	 * $mktime �� true �϶� timestamp �� ������
	 * @param string $date
	 * @param boolean $mktime
	 * @return mixed
	 */
	private function getTime($date, $mktime = false) {

		if (preg_match('/^(\\d{4})[^0-9]?(\\d{1,2})?[^0-9]?(\\d{1,2})?[^0-9]?(\\d{1,2})?[^0-9]?(\\d{1,2})?[^0-9]?(\\d{1,2})?$/',$date,$matches)) {

			array_shift($matches);	// �� ���� ���� ����.

			$ret = array();
			$keys = array_keys($this->date_range);

			foreach($keys as $k => $v) {
				$ret[$v] = isset($matches[$k]) ? $matches[$k] : null;
			}

			return $mktime ? mktime($ret['h'], $ret['i'], $ret['s'], $ret['m'], $ret['d'], $ret['y']) : $ret;

		}
		else {
			return false;
		}

	}

	/**
	 * ����ȭ�� ���� ��¥�� ����
	 * @return string
	 */
	public function now() {
		return date($this->date_format, G_CONST_NOW);
	}

	/**
	 * ���� ��¥�� ����
	 * @return integer
	 */
	public function time() {
		return G_CONST_NOW;
	}

	/**
	 * �Է� ��¥�� �ּҰ��� ����
	 * <code>
	 * echo $date->min('2012-05-18');
	 * // 2012-05-18 00:00:00
	 * </code>
	 * @param string $date
	 * @param boolean $format
	 * @return string
	 */
	public function min($date, $format = true) {

		if (($time = $this->getTime($date)) !== false) {

			$keys = array_keys($this->date_range);

			foreach($keys as $v) {
				${$v} = $time[$v] ? $time[$v] : $this->date_range[$v][0];
			}

			$time = mktime($h, $i, $s, $m, $d, $y);

			return $format ? date($this->date_format, $time) : $time;

		}
		else {
			return '';
		}

	}

	/**
	 * �Է� ��¥�� �ִ밪�� ����
	 * <code>
	 * echo $date->min('2012-05-18');
	 * // 2012-05-18 23:59:59
	 * </code>
	 * @param string $date
	 * @param boolean $format
	 * @return string
	 */
	public function max($date, $format = true) {

		if (($time = $this->getTime($date)) !== false) {

			$keys = array_keys($this->date_range);

			foreach($keys as $v) {

				if ($v == 'd') {
					$_tmp = mktime(0, 0, 0, $m, 1, $y);
					$this->date_range[$v][1] = date('t',$_tmp);
					unset($_tmp);
				}

				${$v} = $time[$v] ? $time[$v] : $this->date_range[$v][1];
			}

			$time = mktime($h, $i, $s, $m, $d, $y);

			return $format ? date($this->date_format, $time) : $time;

		}
		else {
			return '';
		}

	}

	/**
	 * ��¥�� ������ �Ͽ� ����
	 * @param integer $timestamp
	 * @param string $format [optional]
	 * @return string
	 */
	public function format($timestamp, $format='') {
		if ($format == '') {
			$format = $this->date_format;
		}

		if ((string)(int)$timestamp !== (string)$timestamp) {
			$timestamp = $this->getTime($timestamp, true);
		}

		return date($format, $timestamp);
	}
   /* return ((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);*/

	/**
	 * �Է� ��¥�� �ִ�, �ּ� ���� ����
	 * <code>
	 * print_r( $date->rance('2012-05') );
	 * 0 => 2012-05-01 00:00:00
	 * 1 => 2012-05-31 23:59:59
	 * </code>
	 * @return array
	 */
	public function range(/* y m d h i s */) {

		$argv = func_get_args();

		$y = isset($argv[0]) ? $argv[0] : date('Y', G_CONST_NOW);	// �ּ� �⵵ ������ �Է� �Ǿ� ��
		$m = isset($argv[1]) ? $argv[1] : $this->date_range['m'][0];
		$d = isset($argv[2]) ? $argv[2] : $this->date_range['d'][0];
		$h = isset($argv[3]) ? $argv[3] : $this->date_range['h'][0];
		$i = isset($argv[4]) ? $argv[4] : $this->date_range['i'][0];
		$s = isset($argv[5]) ? $argv[5] : $this->date_range['s'][0];

		$from = mktime($h, $i, $s, $m, $d, $y);

		$y = isset($argv[0]) ? $argv[0] : date('Y', G_CONST_NOW);	// �ּ� �⵵ ������ �Է� �Ǿ� ��
		$m = isset($argv[1]) ? $argv[1] : $this->date_range['m'][1];
		$d = isset($argv[2]) ? $argv[2] : date('t', $from);
		$h = isset($argv[3]) ? $argv[3] : $this->date_range['h'][1];
		$i = isset($argv[4]) ? $argv[4] : $this->date_range['i'][1];
		$s = isset($argv[5]) ? $argv[5] : $this->date_range['s'][1];

		$to = mktime($h, $i, $s, $m, $d, $y);

		return array(
			date($this->date_format, $from),
			date($this->date_format, $to)
		);

	}

	/**
	 * �� ��¥ ������ ��, ��, �� ���� ����
	 * @param integer $from
	 * @param integer $to
	 * @return string
	 */
	public function diff($from, $to) {

		$from = strtotime(array_shift(explode(" ",$from)));	// ��¥�θ�

		$time_gap = $from - $to;

		if ($time_gap > 0)
			$mod = '��';
		else
			$mod = '����';

		$time_gap = abs($time_gap);

		$Y=date('Y',$time_gap)-1970;
		$m=date('n',$time_gap)-1;
		$d=date('j',$time_gap)-1;

		if($Y)
			return sprintf('%d�� %s',$Y , $mod);
		elseif($m)
			return sprintf('%d�� %s',$m , $mod);
		elseif($d)
			return sprintf('%d�� %s',$d , $mod);
		else
			return '';

	}	// function

	/**
	 * �� ��¥ ������ ��, ��, �� ���� ����
	 * @param integer $from
	 * @param integer $to
	 * @return string
	 */
	public function diff2($from, $to) {

		//$from = strtotime(array_shift(explode(" ",$from)));	// ��¥�θ�

		$time_gap = $from - $to;

		if ($time_gap > 0)
			$suffix = '��';
		else
			$suffix = '����';

		$time_gap = abs($time_gap);

		$Y=date('Y',$time_gap)-1970;
		$m=date('n',$time_gap)-1;
		$d=date('j',$time_gap)-1;

		if($Y)
			return $Y.'�� '.$suffix;
		elseif($m)
			return $m.'�� '.$suffix;
		elseif($d)
			return $d.'�� '.$suffix;
		else
			return '-';

	}	// function

}
?>
