<?php
/*
 * Clib_Filter
 *
 * @desc ���� ���͸� ���� �Լ��� �����Ѵ�. �����Ǵ� �Լ��� ���� static ���� �����Ѵ�.
 */
final class Clib_Filter
{
	/*
	 * setFilter($variable)
	 * @desc �־��� �������� ���͸��Ѵ�.
	 * @desc filter_var() �Լ� ��� ���� �ʿ�
	 */
	public static function setFilter($val)
	{
		$filterResult = "";

		if (is_int($val)) {
			return intval($val);
		}
		else if (is_bool($val)) {
			return $val;
		}
		else if (is_numeric($val)) {
			return $val;
		}
		else if (is_string($val)) {
			return $val;
		}
		else if (is_object($val)) {
			return $val;
		}
		else if (is_array($val)) {
			return $val;
		}
		else {
			return "";
		}
	}

}
