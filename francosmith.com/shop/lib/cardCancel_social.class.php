<?
/**
	2011-02-08 by x-ta-c
	�����̼� �ֹ���� Ŭ����
	- ��κ� ó�� ����� ������ �Ϻ� �ٸ� ���� �ʿ�� �ϹǷ� ��� Ŭ������ ����.
 */
class cardCancel_social extends cardCancel {

	function cardCancel_social() {

		// pg ����
		$todayShop = Core::loader('todayshop');
		$tsCfg = $todayShop->cfg;

		$tsPG = ($tsCfg['pg'] != '') ? unserialize($tsCfg['pg']) : array();

		if (!empty($tsPG)) {
			foreach($tsPG as $k => $v) {
				if ($k == 'mode') continue;
				$this->$k = $GLOBALS[$k];
				$this->$k = array_merge((array)$this->$k,$v);
			}

			$this->cpg = $this->pg;

		}
		$this->cancel_code = 10;	// �ŷ� �̼���

		$this->shopdir = substr(dirname(__FILE__),0,-4);
		$this->pg_dir = 'todayshop/card';

	}


	// �׽�Ʈ �Լ�.
	function _cancel_pg($ordno){

		return rand(0,1);

	}

	// cardCancel �� ��� �� �������̵��� �ʿ��Ѱ� ���⼭ ����..




	//
}
?>
