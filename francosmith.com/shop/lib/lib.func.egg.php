<?
/**
 * ���ں������� ���̺귯��
 */

/**
 * ���ں������� ȯ�浥����
 *
 * @author pr
 * @return array
 *
 */
function getEggConf()
{
	@include dirname(__FILE__) . '/../conf/egg.usafe.php';
	$egg = (array)$egg;

	// �Һ��ںδ� �������� Default
	if ($egg['feerate'] == '') {
		$egg['feerate'] = '0.535';
	}

	return $egg;
}

/**
 * ���ں������� ���� ����
 *
 * @author pr
 * @param array $data �����׸�
 * @return bool
 *
 */
function saveEgg($data)
{
	include_once dirname(__FILE__) . '/../lib/qfile.class.php';
	$qfile = new qfile();

	$egg = getEggConf();
	if ($egg['use'] != 'Y') $data['cfg']['displayEgg'] = '';
	if ($egg['use'] != 'Y' || $egg['scope'] != 'P') $data['min'] = '';
	if ($egg['use'] == 'Y' && $egg['feerate'] == '') {
		$egg['feerate'] = '0.535';
	}

	// ���ں��� �ݾ� ����
	if (isset($egg) === true) {
		$egg = (array)$egg;
		$egg = array_map('stripslashes',$egg);
		$egg = array_map('addslashes',$egg);
		$egg['min'] = $data['min'];

		$qfile->open(dirname(__FILE__) . '/../conf/egg.usafe.php');
		$qfile->write("<? \n");
		$qfile->write("\$egg = array( \n");
		foreach ($egg as $k => $v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod(dirname(__FILE__) . '/../conf/egg.usafe.php',0707);
	}

	// ���� ���� ǥ�� ����
	include dirname(__FILE__) . '/../conf/config.php';
	$cfg = (array)$cfg;
	$cfg = array_map('stripslashes',$cfg);
	$cfg = array_map('addslashes',$cfg);
	$cfg['displayEgg'] = $data['cfg']['displayEgg'];

	$qfile->open(dirname(__FILE__) . '/../conf/config.php');
	$qfile->write("<? \n");
	$qfile->write("\$cfg = array( \n");
	foreach ($cfg as $k => $v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();

	return true;
}

/**
 * ���ں������� ������ ����
 *
 * @author pr
 * @param int $afterEggFee
 * @param int $price
 * @param float $feerate
 * @param string $eggFeeRateYn

 * @return int
 *
 */
function reCalcuEggFee($afterEggFee, $price, $feerate, $eggFeeRateYn)
{
	$beforeEggFee = 0;
	if (abs($afterEggFee) > 0 && $price > 0) {
		if ($eggFeeRateYn == 'Y' && $feerate > 0) {
			$beforeEggFee = floor($price * ($feerate / 100));
		}
		else {
			$beforeEggFee = floor($price * 0.00493);
		}
	}
	return $beforeEggFee;
}
?>