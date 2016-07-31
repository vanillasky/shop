<?php

class NaverCommonInflowScript
{

	var $shopConfig, $config, $accountId, $whiteList, $isEnabled, $useNaverService, $cpaAgreement, $saAgreement, $naverCommonInflowScriptCfgPath;

	function NaverCommonInflowScript()
	{
		$this->naverCommonInflowScriptCfgPath = dirname(__FILE__).'/../conf/naverCommonInflowScript.cfg.php';

		@include $this->naverCommonInflowScriptCfgPath;

		$this->shopConfig = $GLOBALS['config']->load('config');
		$this->config = $naverCommonInflowScriptConfig;

		if(strlen($this->config['accountId'])>0) $this->isEnabled = true;
		else $this->isEnabled = false;
		$this->useNaverService = $this->isEnabled;

		if(isset($partner)===false) @include dirname(__FILE__).'/../conf/partner.php';
		if($partner['cpaAgreement']==='true') $this->cpaAgreement = true;
		else $this->cpaAgreement = false;
		if ($this->useNaverService !== true && $partner['useYn'] === 'y') $this->useNaverService = true;

		if ($this->useNaverService !== true) {
			if (isset($checkoutCfg) === false) @include dirname(__FILE__).'/../conf/naverCheckout.cfg.php';
			if ($checkoutCfg['useYn'] === 'y') $this->useNaverService = true;
		}

		if ($this->useNaverService !== true) {
			$naverMileageConfig = $GLOBALS['config']->load('ncash');
			if ($naverMileageConfig['useyn'] === 'Y') $this->useNaverService = true;
		}

		$this->saAgreement = $this->cpaAgreement;

		$this->accountId = $this->config['accountId'];
		$this->whiteList = explode('|', $this->config['whiteList']);
	}

	function writeConfigFile($config)
	{
		$resource = fopen($this->naverCommonInflowScriptCfgPath, 'w');
		if($resource)
		{
			fwrite($resource, '<?php'.PHP_EOL.'$naverCommonInflowScriptConfig = array('.PHP_EOL);
			foreach ($config as $name => $value) fwrite($resource, '"'.$name.'" => "'.$value.'",'.PHP_EOL);
			fwrite($resource, ');'.PHP_EOL.'?>');
			fclose($resource);
			@chmod($this->naverCommonInflowScriptCfgPath, 0707);
			return true;
		}
		else
		{
			return false;
		}
	}

	// �������� ����
	function doSave($accountId, $whiteList)
	{
		$accountId = trim($accountId);
		if($accountId && strlen($accountId)>0)
		{
			return $this->writeConfigFile(array(
			    'accountId' => $accountId,
			    'whiteList' => implode('|', (array)$whiteList)
			));
		}
		else
		{
			return false;
		}
	}

	// �������� ����
	function deleteAccountID($accountId)
	{
		if ($accountId === $this->accountId) {
			return $this->writeConfigFile(array(
			    'accountId' => '',
			    'whiteList' => implode('|', (array)$this->whiteList)
			));
		}
		else {
			return false;
		}
	}

	// �������Խ�ũ��Ʈ ��ȯ
	function getCommonInflowScript()
	{
		if ($this->useNaverService) {
			$param = array();
			@include dirname(__FILE__).'/../conf/config.mobileShop.php';
			$removeDir = array(str_replace('/', '\/', $this->shopConfig['rootDir']));
			if (isset($cfgMobileShop) && isset($cfgMobileShop['mobileShopRootDir'])) $removeDir[] = str_replace('/', '\/', $cfgMobileShop['mobileShopRootDir']);
			$patternRootDir = '/^('.implode('|', $removeDir).')\//';
			$param[] = 'Path='.preg_replace($patternRootDir, '', $_SERVER['SCRIPT_NAME']);
			$param[] = 'Referer='.urlencode($_SERVER['HTTP_REFERER']);
			$param[] = 'AccountID='.$this->config['accountId'];
			$param[] = 'Inflow='.preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
			foreach($this->whiteList as $whiteList)
			{
				if(strlen(trim($whiteList))>0) $param[] = 'WhiteList[]='.$whiteList;
			}
			return '
			<script type="text/javascript" src="'.($_SERVER['HTTPS']?'https':'http').'://wcs.naver.net/wcslog.js"></script>
			<script type="text/javascript" src="'.$this->shopConfig['rootDir'].'/lib/js/naverCommonInflowScript.js?'.implode('&amp;', $param).'" id="naver-common-inflow-script"></script>
			';
		}
		else {
			return '';
		}
	}

	// CPA�ֹ����� ������ ��ȯ
	function getOrderCompleteData($ordno)
	{
		$orderCompleteData = array();

		// �������Խ�ũ��Ʈ�� �����Ǿ������� �ֹ���ǰ������ �±׷� �����Ͽ� ��ȯ
		if($this->isEnabled && $this->cpaAgreement)
		{
			$db = $GLOBALS['db'];

			$orderItemSet = array();	// �ֹ���ǰ����

			// ordno�� ���ԵǴ� �ֹ���ǰ���� ��ȸ
			$res = $db->query("
			SELECT `oi`.`sno`, `oi`.`ordno`, `oi`.`goodsno`, `oi`.`goodsnm`, `oi`.`ea`, `oi`.`addopt`, (`oi`.`price`*`oi`.`ea`)-`oi`.`coupon` AS `payment_price`, `go`.`optno`, `go`.`opt1`, `go`.`opt2`, `g`.`optnm`, `g`.`addoptnm`
			FROM `gd_order_item` AS `oi`
			INNER JOIN `gd_goods_option` AS `go`
			ON `oi`.`goodsno`=`go`.`goodsno` AND `oi`.`opt1`=`go`.`opt1` AND `oi`.`opt2`=`go`.`opt2` and go_is_deleted <> '1' and go_is_display = '1'
			INNER JOIN `gd_goods` AS `g`
			ON `oi`.`goodsno`=`g`.`goodsno`
			WHERE `ordno`=".$ordno);
			while($orderRepItem = $db->fetch($res, 1))
			{
				// �߰���ǰ �������� ����
				$addoptStep = array();
				$addoptValue = array();
				$addoptCondition = array();
				$addoptStepSet = array();
				foreach(explode('|', $orderRepItem['addoptnm']) as $index => $addoptnm) $addoptStepSet[array_shift(explode('^', $addoptnm))] = $index;
				foreach(explode('^', $orderRepItem['addopt']) as $orderAddopt)
				{
					$addopt = explode(':', $orderAddopt);
					$addoptCondition[] = "(`step`=".$addoptStepSet[$addopt[0]]." AND `opt`='".$addopt[1]."')";
				}

				// ������ ������ ����Ͽ� �ֹ��� �߰��ɼ������� ��ȸ
				$orderAddItemSet = array();	// �߰��ɼ�����
				$addoptRes = $db->query("
				SELECT `sno`, `opt`, `addprice`
				FROM `gd_goods_add`
				WHERE `goodsno`=".$orderRepItem['goodsno']."
				AND (".implode(' OR ', $addoptCondition).")
				");
				while($orderAddItem = $db->fetch($addoptRes, 1))
				{
					// ��ȸ�� �ֹ���ǰ �߰��ɼ����� �߰�
					$orderAddItemSet[] = array(
						'sno'       => $orderRepItem['sno'],
						'ordno'     => $orderRepItem['ordno'],
						'goodsno'   => $orderRepItem['goodsno'],
						'optno'     => $orderAddItem['sno'],
						'goodsnm'   => $orderAddItem['opt'],
						'ea'        => $orderRepItem['ea'],
						'price'     => $orderAddItem['addprice']*$orderRepItem['ea'],
						'is_parent' => 'false'
					);
					// �ֹ���ǰ���ݿ��� �߰��ɼǰ��� ����
					$orderRepItem['payment_price'] -= $orderAddItem['addprice']*$orderRepItem['ea'];
				}

				// ��ȸ�� �ֹ���ǰ �߰�
				$optionInfoSet = array();	// �ֹ���ǰ �ɼ�����
				$optnm = explode('|', $orderRepItem['optnm']);
				if(strlen(trim($orderRepItem['opt1']))>0) $optionInfoSet[] = (strlen(trim($optnm[0]))>0?(trim($optnm[0]).':'):'').trim($orderRepItem['opt1']);
				if(strlen(trim($orderRepItem['opt2']))>0) $optionInfoSet[] = (strlen(trim($optnm[1]))>0?(trim($optnm[1]).':'):'').trim($orderRepItem['opt2']);
				$orderItemSet[] = array(
					'sno'       => $orderRepItem['sno'],
					'ordno'     => $orderRepItem['ordno'],
					'goodsno'   => $orderRepItem['goodsno'],
					'optno'     => $orderRepItem['optno'],
					'goodsnm'   => $orderRepItem['goodsnm'].(count($optionInfoSet)>0?'['.implode(',', $optionInfoSet).']':''),
					'ea'        => $orderRepItem['ea'],
					'price'     => $orderRepItem['payment_price'],
					'is_parent' => 'true'
				);

				// �ֹ���ǰ�� ���� �߰����� �ֹ���ǰ �߰��ɼ� �߰�
				foreach($orderAddItemSet as $addItem) $orderItemSet[] = $addItem;
			}

			// �ϼ��� �ֹ���ǰ������ �±׷� ��ȯ
			foreach($orderItemSet as $orderItem)
			{
				$orderItemField = array();
				foreach($orderItem as $field => $value)
				{
					switch($field)
					{
						case 'ea': case 'payment_price': case 'is_parent':
							$orderItemField[] = $field.":".$value;
							break;
						case 'goodsnm':
							$orderItemField[] = $field.":'".addslashes($value)."'";
							break;
						default:
							$orderItemField[] = $field.":'".$value."'";
							break;
					}
				}
				$orderCompleteData[] = '<input type="hidden" name="naver-common-inflow-script-order-item" value="{'.implode(',', $orderItemField).'}"/>';
			}
		}

		if ($this->isEnabled && $this->saAgreement) {
			$orderInfo = $db->fetch('SELECT goodsprice FROM '.GD_ORDER.' WHERE ordno='.$ordno, 1);
			$orderField = array();
			foreach ($orderInfo as $field => $name) {
				$orderField[] = $field.":'".$name."'";
			}
			if (count($orderField) > 0) $orderCompleteData[] = '<input type="hidden" id="naver-common-inflow-script-order" value="{'.implode(',', $orderField).'}"/>';
		}

		if (count($orderCompleteData) > 0) return implode($orderCompleteData);
		else return '';
	}

}

?>