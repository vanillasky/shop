<?php
class Clib_Model_Goods_Brand extends Clib_Model_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected $idColumnName = 'sno';

	public function getConfig()
	{

		global $cfg;

		@include  sprintf('%s/conf/brand/%s.php', SHOPROOT, $this->getId());

		if ( ! $lstcfg['cols'])
			$lstcfg['cols'] = 4;
		if ( ! $lstcfg['size'])
			$lstcfg['size'] = $cfg['img_s'];
		if ( ! $lstcfg['tpl'])
			$lstcfg['tpl'] = "tpl_01";
		if ( ! count($lstcfg['page_num']))
			$lstcfg['page_num'] = array(
				12,
				20,
				32,
				48
			);

		if ( ! $lstcfg['rcols'])
			$lstcfg['rcols'] = 4;
		if ( ! $lstcfg['rsize'])
			$lstcfg['rsize'] = $cfg['img_s'];
		if ( ! $lstcfg['rtpl'])
			$lstcfg['rtpl'] = "tpl_01";
		if ( ! $lstcfg['rpage_num'] || $lstcfg['rpage_num'] == 0)
			$lstcfg['rpage_num'] = 4;

		// ���÷��� ���� ������
		// ���ø����� �۷ι� ������ �ҷ����� �ʴ� ��� scope �� �������� �ʾ� tpl, rtplŰ�� ���� ���� ������ϴ�
		$_dpCfg_keys = array(
			'alphaRate',
			'dOpt1',
			'dOpt2',
			'dOpt3',
			'dOpt4',
			'dOpt5',
			'dOpt6',
			'dOpt7',
			'dOpt8',
			'dOpt9',
			'dOpt10',
			'dOpt11'
		);
		foreach (array('rtpl','tpl') as $k => $v) {
			foreach ($_dpCfg_keys as $_k => $_v) {
				$GLOBALS['dpCfg'][$v][$_v] = $lstcfg[$_v][$v];
			}
		}

		return $lstcfg;
	}

	public function getBrandName()
	{
		return $this['brandnm'];
	}

}
