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
 * GODO_helper_array
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_array extends GODO_helper {

	/**
	 * �������� ���� �迭�� ���� (����, ����Ű ȥ�� ����)
	 * @param array $array
	 * @return array
	 */
	public function shuffle($array) {

		$_keys = array_keys($array);
		shuffle($_keys);

		$ret = array();

		foreach ($_keys as $k) {
			$ret[$k] = $array[$k];
		}

		return $ret;

	}

}
?>