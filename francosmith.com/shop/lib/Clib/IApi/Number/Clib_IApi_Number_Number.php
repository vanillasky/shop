<?php
/**
 * Clib_IApi_Number_Number
 * @author extacy @ godosoft development team.
 */
class Clib_IApi_Number_Number
{

	public function getCuttingConfigString($use, $unit, $method)
	{
		return sprintf('%d:%s:%s', $use, strlen($unit), $method);
	}

	public function getCuttedNumberFromConfigString($number, $config)
	{
		list($use, $unit, $method) = explode(':', $config);

		if ($use) {

			$multi = $unit ? pow(10, $unit - 1) : 1;

			$_number = $number / $multi;

			switch($method) {
				case 'c' : // �ø�
					$_number = ceil($_number);
					break;
				case 'r' : // �ݿø�
					$_number = round($_number);
					break;
				case 'f' : // ����
				default :
					$_number = floor($_number);
					break;
			}

			$number = $_number * $multi;

		}
		else {
			// ���� �����϶�, �Ҽ��� ���ϴ� ����
			$number = floor($number);
		}

		return $number;

	}

}
