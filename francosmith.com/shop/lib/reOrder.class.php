<?php

/**
 * ���ֹ� : �ֹ������� �ִ� ��ǰ���� ��ٱ��Ͽ� ��� ���
 * ���� ��� ��ǰ�� ������ ���� �Ǹ��ϰ� �ִ� ��ǰ�� ������ ���� ����� ����
 */
class reOrder
{
	private $_db = null;
	private $_cart = null;
	
	/**
	 * ������
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = Core::loader('db');
		$this->_cart = Core::loader('Cart');
	}
	
	/**
	 * �ֹ����� �ִ� ��ǰ üũ
	 * @param int $ordno �ֹ���ȣ
	 * @return array
	 */
	public function chk_order($ordno)
	{
		$added = 0;				// ��ٱ��Ͽ� �߰� �� ��ǰ ���� üũ
		$price_result = 0;		// ���ݺ��� �� ��ǰ ���� üũ
		$itemCount = 0;			// ��ǰ ����
		
		$orderItem = $this->_db->query("select goodsno, opt1, opt2, addopt, ea, price from ".GD_ORDER_ITEM." where ordno='".$ordno."'");
		$itemCount = mysql_affected_rows();

		while ($item = $this->_db->fetch($orderItem,1)) {
			
			// ��ǰ ���� ���� üũ
			$chkOpen = 0;
			$chkOpen = chkOpenYn($item['goodsno'],'D',0,'true');
			if ($chkOpen == 1) {
				continue;
			}

			$price_chk = 0;		// ���� ������ üũ�ϱ� ���� ����
			$price_opt = 0;		// ���ݿɼ� ����
			$opt = array();		// ���ݿɼ� �ʱ�ȭ

			// ���� �ɼ�
			$option = $this->_db->query("select price,sno from ".GD_GOODS_OPTION." where opt1='".mysql_real_escape_string($item['opt1'])."' and opt2='".mysql_real_escape_string($item['opt2'])."' and goodsno='".$item['goodsno']."' and go_is_display = '1' and go_is_deleted <> '1'");
			$optItem = $this->_db->fetch($option);

			// ���ݿɼ��� �������� ���
			if ($item['opt1'] && !$optItem['sno']) {
				continue;
			}
			$price_opt = $optItem['price'];
			$opt[] = $item['opt1'];
			$opt[] = $item['opt2'];
			
			// �߰��ɼ� üũ �Լ� ȣ��
			$addOptionRes = $this->chk_addOption($item['addopt'],$item['goodsno']);
		
			if ($addOptionRes['pass'] == '') {
				$added += $this->_cart->addCart($item['goodsno'], $opt, $addOptionRes['addopt'], $addOptionRes['addopt_inputable'], $item['ea'], 'true');
				$price_chk = $price_opt+$addOptionRes['price_addopt']+$addOptionRes['price_addopt_inputable'];

				// ������ �ٲ� ��ǰ�� �ִ��� üũ
				if($item['price'] != $price_chk){
					$price_result++;
				}
			}
		}
		$reOrderRes = array(
					'added'			=>	$added,
					'price_result'	=>	$price_result,
					'itemCount'		=>	$itemCount,
		);
		return $reOrderRes;
	}
	/**
	 * �߰��ɼ� üũ
	 * @param int $itemGoodsno ��ǰ ��ȣ
	 * @param string $itemOpt ��ǰ �߰��ɼ�
	 * @return array
	 */	
	private function chk_addOption($itemOpt,$itemGoodsno)
	{
		
		$count = 0;						// �߰��ɼǰ� �Է¿ɼ��� �������� ��� ���� �ʱ� ���� üũ�ϴ� ����
		$pass = '';						// �߰��ɼ��� ��ٱ��Ͽ� ���� �ʰ� �ѱ�� ���� ����
		$addopt = array();				// �߰��ɼ� �ʱ�ȭ
		$addopt_inputable = array();	// �Է¿ɼ� �ʱ�ȭ
		$price_addopt = 0;				// �߰��ɼ� ����
		$price_addopt_inputable = 0;	// �Է¿ɼ� ����
		
		// �ֹ������� �ִ� ��ǰ�� �߰��ɼǰ� �Է¿ɼ��� ������ �и�
		$addoption = explode('^',$itemOpt);

		$add = $this->_db->query("select a.type, a.sno, a.opt, a.addprice, a.step, g.addoptnm from ".GD_GOODS_ADD." a left join ".GD_GOODS." g on a.goodsno=g.goodsno where a.goodsno='".$itemGoodsno."'");
		while ($addItem = $this->_db->fetch($add,1)) {

			// ��ϵ� ��ǰ �������� �Է¿ɼǰ� �߰��ɼ��� �̸��� �и�
			$addoptnms = explode('|',$addItem['addoptnm']);

			$addoptNm = array();
			$addoptReq = array();
			$addoptType = array();
			$chk_name = '';
			
			// �ɼ� ������ �̸�,������,Ÿ�Ժ��� �з�
			for ($a=0,$b=count($addoptnms); $a<$b; $a++) {
				list($addoptNm[], $addoptReq[], $addoptType[]) = explode('^',$addoptnms[$a]);
			}

			// �߰��ɼ� �Ǵ� �Է¿ɼ��� ��ġ�ϴ��� ��
			for ($i=0; $i<count($addoption); $i++) {
				$addoptInCart[$i] = str_replace(':','^',$addoption[$i]);			// addCart�� ������ ����
				list($chkAddNm[$i], $chkAddVal[$i]) = explode(':',$addoption[$i]);	// ���ϱ� ���� ����

				// �߰��ɼ��� ���� ��ǰ�� ���������� �ʼ�üũ �� �߰��ɼ� �Ǵ� �Է¿ɼ��� ������ ���
				if (strpos($addItem['addoptnm'],'^o^') > -1 && $addoption[$i] == null) {
					$pass = 'pass';
				}

				// ��ϵ� ��ǰ�� �ֹ������� ��ǰ�� ��
				else if (in_array($chkAddNm[$i],$addoptNm) === true) {
					// �߰� �ɼ����� üũ
					if ($addItem['type'] === 'S' && $addItem['opt'] === $chkAddVal[$i]) {
						$addopt[] = $addItem['sno'].'^'.$addoptInCart[$i].'^'.$addItem['addprice'];
						$price_addopt += $addItem['addprice'];
					}
					// �Է� �ɼ�����, ���ڼ� ���ѿ� �ɸ����� üũ
					else if ($addItem['type'] === 'I' && $addItem['opt'] >= mb_strlen($chkAddVal[$i],'euc-kr')) {
						$name_offset = $addItem['step'] + ($addItem['type'] === 'I' ? (int) array_search('I', $addoptType) : 0);
						$chk_name = $addoptNm[$name_offset];

						if($chk_name === $chkAddNm[$i]){
							$addopt_inputable[] = $addItem['sno'].'^'.$addoptInCart[$i].'^'.$addItem['addprice'];
							$price_addopt_inputable += $addItem['addprice'];
						}
					}
					else {}
				}
				// ��ǰ�� �Է¿ɼ� �Ǵ� �߰��ɼ��� �̸��� ���� �Ȱ��� üũ 
				else if ($addItem['addoptnm'] != null && $chkAddNm[$i] != null) {
					$pass = 'pass';
				}
				else {}
			}
			// �������� �ʾҴ� �߰��ɼ��� �ʼ�üũ �Ǿ��� ���
			if(array_search('o',$addoptReq) != false && in_array($addoptNm[array_search('o',$addoptReq)],$chkAddNm) === false){
				$pass = 'pass';
			}
		}
		// �ֹ������� �ִ� �߰��ɼǵ��� ������ �ִ��� Ȯ��
		if ($addoption[0] == null) {
			$count = 0;
		}
		else {
			$count = count($addoption);
		}
		
		if ($count != count($addopt)+count($addopt_inputable)) {
			$pass = 'pass';
		}

		$addOptionRes = array (
						'pass'					=>	$pass,
						'addopt'				=>	$addopt,
						'addopt_inputable'		=>	$addopt_inputable,
						'price_addopt'			=>	$price_addopt,
						'price_addopt_inputable'=>	$price_addopt_inputable,
		);
		return $addOptionRes;
	}
}
?>