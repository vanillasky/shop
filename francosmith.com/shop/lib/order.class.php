<?
if(!defined('kTAXFREE')) define('kTAXFREE', 'taxfree');
if(!defined('kTAXALL')) define('kTAXALL', 'taxall');
if(!defined('kTAX')) define('kTAX', 'tax');
if(!defined('kVAT')) define('kVAT', 'vat');

class order implements ArrayAccess {
	private $_db;
	private $_data;
	private $_order_item;
	private $_order_cancel;
	private $_is_all_canceled; // @qni 2015-04 ��ü�������/�Ϸ� ����
	private $_is_all_canceling; // @qni 2015-04 ��ü������� ����
	private $_is_all_cancelCompleted; // @qni 2015-04 ��ü��ҿϷ� ����

	public function __construct() {
		$this->_db = Core::loader('db');
	}

	/**
	 * Whether a offset exists
	 * @param string $offset An offset to check for
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Offset to retrieve
	 * @param string $offset The offset to retrieve.
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->_data[$offset];
	}

	/**
	 * Offset to set
	 * @param string $offset The offset to assign the value to
	 * @param mixed $value The value to set
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		$this->_data[$offset] = $value;
	}

	/**
	 * Offset to unset
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}

	public function load($ordno) {
		$query = "select b.m_id,b.dormant_regDate,a.* from " . GD_ORDER . " a left join " . GD_MEMBER . " b on a.m_no=b.m_no where ordno='$ordno'";
		$this->_data = $this->_db->fetch($query, 1);
		$this->_order_item = null;
		$this->_order_cancel = null;

		// �ֹ���� ���� �Ҵ�
		$this->_is_all_canceled = false;
		$this->_is_all_canceling = false;
		$this->_is_all_cancelCompleted = false;
		$this->_is_all_pgcancel = false;
		$this->isAllCanceled();
	}

	public function getOrderItems() {
		if (is_null($this->_order_item)) {
			$this->_loadOrderItems();
		}

		return (array) $this->_order_item;
	}

	private function _loadOrderItems() {
		$query = "select sno from " . GD_ORDER_ITEM . " where ordno='{$this['ordno']}' order by sno";
		$rs = $this->_db->query($query);

		$item = new order_item();

		$this->_order_item = array();

		while ($row = $this->_db->fetch($rs, 1)) {
			$_item = clone $item;
			$_item->load($row['sno']);
			$this->_order_item[] = $_item;
		}
	}

	// @qni 2015-05 �ֹ���� ROW
	public function getOrderCancel($sno) {
		$data = array();
		if(is_numeric($sno)) {
			foreach ($this->getOrderCancels() as $cancel) {
				if ($sno == $cancel['sno']) {
					$data = $cancel;
					break;
				}
			}
		}

		return $data;
	}

	// @qni 2015-05 �ֹ���� ��ü
	public function getOrderCancels() {
		if (is_null($this->_order_cancel)) {
			$this->_loadOrderCancels();
		}

		return (array) $this->_order_cancel;
	}

	private function _loadOrderCancels() {
		// ��ҵ����͸� �������� ���� item ���̺��� cancel�ʵ� ��������
		$cancels = array();
		foreach ($this->getOrderItems() as $item) {
			$cancels[] += $item['cancel'];
		}
		$cancels = array_unique($cancels);// �ߺ��Ǵ� ���SNO ����

		if ($cancels) {
			$query = "select sno from " . GD_ORDER_CANCEL . " where sno in (" . implode(",",$cancels) .") order by sno";
			$rs = $this->_db->query($query);

			$cancel = new order_cancel();

			// �ν��Ͻ� ������ ���ݰ���� ���� �� �ʱ�ȭ
			$cancel->taxfree_ratio = $this->getTaxFreeRatio();// �鼼���� �Ҵ�

			$this->_order_cancel = array();

			while ($row = $this->_db->fetch($rs, 1)) {
				$_cancel = clone $cancel;
				$_cancel->load($row['sno']);
				$this->_order_cancel[] = $_cancel;
			}
		}
	}

	public function getDiscount() {
		return $this->getMemberDiscount() + $this['emoney'] + $this['coupon'] + $this['enuri'] + $this['ncash_emoney'] + $this['ncash_cash'] + $this['o_special_discount_amount'];
	}

	public function getDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this->getMemberDiscount()) {
			$discount_detail['ȸ������'] = $this->getMemberDiscount();
		}

		if ($this['coupon']) {
			$discount_detail['��������'] = array('%����' => $this->getPercentCouponDiscount(), '�ݾ�����' => $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'],);
		}

		if ($this['o_special_discount_amount']) {
			$discount_detail['��ǰ����'] = $this['o_special_discount_amount'];
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['��������']['��ٿ�'] = $this['about_dc_sum'];
		}

		if ($this['emoney']) {
			$discount_detail['�����ݻ��'] = $this['emoney'];
		}

		if ($this['ncash_emoney']) {
			$discount_detail['���̹����ϸ������'] = $this['ncash_emoney'];
		}

		if ($this['ncash_cash']) {
			$discount_detail['���̹�ĳ�����'] = $this['ncash_cash'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {

				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {

					$_tmp = array();

					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}

					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	public function getPercentCouponDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += $item->getPercentCouponDiscount();
		}

		return $amount;
	}

	public function getMemberDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += $item->getMemberDiscount();
		}
		return $amount;
	}

	public function getAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += $item->getAmount();
		}
		return $amount;
	}

	public function getCanceledAmount() {
		$amount = 0;

		$cnt = 0; // all count;
		$cnt2 = 0; // canceled count;

		foreach ($this->getOrderItems() as $item) {
			$cnt++;
			if ($item->hasCanceled()) {
				$cnt2++;
			}
			$amount += $item->getCanceledAmount();
		}

		// ��ü �ֹ� ǰ�� ����� ���
		// ��ۺ� - ������ - �����ݻ��� - ���̹����ϸ��� - ���̹�ĳ�� - �ݾ����� + ������������� �� �ջ��Ѵ�.
		if ($cnt == $cnt2) {
			$amount += $this->getDeliveryFee() - $this[enuri] - $this[emoney] - $this['ncash_emoney'] - $this['ncash_cash'] - ($this[coupon] - $this->getPercentCouponDiscount()) + $this[eggFee];
		}

		return $amount;
	}

	public function getCanceledDetailArray($format = false) {
		$amount = array();

		$cnt = 0; // all count;
		$cnt2 = 0; // canceled count;

		foreach ($this->getOrderItems() as $item) {
			$cnt++;
			if ($item->hasCanceled()) {
				$cnt2++;

				$amount['��һ�ǰ �Ǹűݾ�'] += $item->getAmount();
				$amount['��ǰ������ ����ݾ�'] += $item->getDiscount();
			}
		}

		$amount['��ǰ������ ����ݾ�'] = $amount['��ǰ������ ����ݾ�'] * -1;

		// ��ü �ֹ� ǰ�� ����� ���
		// + ��ۺ� - ������ - �����ݻ��� - ���̹����ϸ��� - ���̹�ĳ�� - �ݾ����� + ������������� �� �ջ��Ѵ�.
		if ($cnt == $cnt2) {
			if ($this[coupon] - $this->getPercentCouponDiscount()) {
				$amount['�ݾ�����'] = -($this[coupon] - $this->getPercentCouponDiscount());
			}

			if ($this->getDeliveryFee()) {
				$amount['��ۺ�'] = $this->getDeliveryFee();
			}

			if ($this[enuri]) {
				$amount['������'] = -$this[enuri];
			}

			if ($this[emoney]) {
				$amount['������'] = -$this[emoney];
			}

			if ($this[ncash_emoney]) {
				$amount['���̹� ���ϸ���'] = -$this[ncash_emoney];
			}

			if ($this[ncash_cash]) {
				$amount['���̹� ĳ��'] = -$this[ncash_cash];
			}

			if ($this[eggFee]) {
				$amount['�������������'] = $this[eggFee];
			}
		}


		if ($format) {
			$tmp = array();
			foreach ($amount as $k => $v) {
				$operator = $v > 0 ? '+' : '-';
				if (sizeof($tmp) == 0)
					$operator = '';
				$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format(abs($v)));
			}
			return $tmp;
		}

		return $amount;
	}

	public function getCanceledCount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceled())
				$amount++;
		}
		return $amount;
	}

	public function getRefundedAmount() {
		$amount = array();

		foreach ($this->getOrderItems() as $item) {
			$amount = $amount + $item->getRefundedAmount();
		}

		return array_sum($amount);
	}

	public function getSettleAmount() {
		return $this->getAmount() + $this->getDeliveryFee() - $this->getDiscount() + $this[eggFee];
	}

	public function getRealSettleAmount() {
		// ���ʰ����ݾ� - ��ұݾ�(ȯ�ҿ��� �ݾ� ����)
		return $this->getSettleAmount() - $this->getCanceledAmount();
	}

	public function getDeliveryFeeDetailArray($format = false) {
		$delivery_fee_detail = array();

		$delivery_type_label = array(0 => '�⺻ ��ۺ�', 1 => '������', 3 => '���� ��ۺ�', 2 => '��ǰ�� ��ۺ�', 4 => '���� ��ۺ�', 5 => '������ ��ۺ�',);

		foreach ($this->getOrderItems() as $item) {

			/*
			  [0] : �⺻��ۺ�
			  [1] : ������
			  [2] : ��ǰ�� ��ۺ�
			  [4] : ���� ��ۺ�
			  [5] : ������ ��ۺ�
			  [3] : ���� ��ۺ�
			 */
			if (in_array($item['oi_delivery_type'], array(2, 4, 5))) {
				// ������ ��ۺ�� ���� ������ ���Ѵ�.
				$item_delivery_fee = $item['oi_delivery_type'] == 5 ? $item['oi_goods_delivery'] * $item['ea'] : $item['oi_goods_delivery'];
				$delivery_fee_detail[$delivery_type_label[$item['oi_delivery_type']]] += $item_delivery_fee;
			}
		}


		$total_item_delivery_fee = array_sum($delivery_fee_detail);
		if (($_fee = $this['delivery'] - $total_item_delivery_fee) > 0) {
			$delivery_fee_detail['�⺻��ۺ�'] = $_fee;
		}

		if ($format) {
			$tmp = array();
			foreach ($delivery_fee_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';
				$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
			}

			return $tmp;
		}

		return $delivery_fee_detail;
	}

	public function getDeliveryFee() {
		return $this['delivery'];
	}

	public function getStepMsg() {
		return getStepMsg($this[step], $this[step2], $this[ordno]);
	}

	public function hasExchanged() {
		list($cnt) = $this->_db->fetch("select count(*) from " . GD_ORDER . " where oldordno='{$this[ordno]}'");

		return $cnt > 0 ? true : false;
	}


	/**
	 * ������ > �ֹ��� > �����ݾ�����
	 * ��� �� �κ���ҿ� ���õ� �ݾ��� ����ȭ�ϴ� �۾�
	 *
	 * @since 2015-04
	 * @author qnibus <qnibus@godo.co.kr>
	 * @link http://[userid].godo.co.kr/shop/admin/order/view.php?ordno=[ordno]
	 */

	 // @qni 2015-04 ������ ��ġ ��ġ ���� Ȯ��
	private function isInstallPayco() {
		global $config;
		$payco = $config->load('payco');
		return !empty($payco);
	}

	// @qni 2015-04 ������ ��ġ ��ġ ���� Ȯ��
	private function isPaycoPay() {
		return ($this['pg'] == 'payco'); // ������ ����� �ּ�����
	}

	// @qni 2015-04 �ֹ���ǰ ��ҿ��� �������� (load �Լ����� �ʱ�ȭ)
	private function isAllCanceled() {
		$cnt = $cnt2 = $cnt3 = $cnt4 = 0;
		foreach ($this->getOrderItems() as $item) {
			$cnt++;
			if ($item->hasCanceling()) {
				$cnt2++;
			}
			if ($item->hasCancelCompleted()) {
				$cnt3++;
			}
			if ($item->hasPgCanceled()) {
				$cnt4++;
			}
		}

		$this->_is_all_canceled = ($cnt == $cnt2 + $cnt3);
		$this->_is_all_canceling = ($cnt == $cnt2);
		$this->_is_all_cancelCompleted = ($cnt == $cnt3);
		$this->_is_all_pgcancel = ($cnt - $cnt4 == 1);
	}

	// @qni 2015-04 �����ݾ� > ������ ���� ����
	function getPaycoOrderDetailArray($format = false) {
		$payco_detail = $this->getPaycoOrderTypeArray();

		if ($this->isPaycoPay()) {
			$tmp = array();
			foreach ($payco_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				$k = str_replace('Payco', '<img src="../img/icon_payco.gif">', $k);

				if (is_array($v)) {
					$_tmp = array();



					foreach ($v as $k2 => $v2) {
						if ($v2) {
							if ($format) $_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
							else $_tmp[] = sprintf('%s', $k2);
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					if ($k) {
						if ($format) $tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
						else $tmp[] = sprintf('%s %s', $operator, $k);
					}
				}
			}
			return $tmp;
		}

		return $payco_detail;
	}
	function getPaycoOrderTypeArray() {
		$payco_detail = array();

		if ($this->isPaycoPay()) {
			$settlekind_label = array(
				"a" => "������",
				"c" => "�ſ�ī��",
				"o" => "������ü",
				"v" => "�������",
				"d" => "��������",
				"h" => "�ڵ���",
				"p" => "����Ʈ",
				"u" => "�ſ�ī�� (�߱�)",
				"y" => "��������",
				"e" => "������ ����Ʈ",
			);

			if ($this['payco_use_point']) {
				$payco_detail['Payco����Ʈ'] = $this['payco_use_point'];
			}

			if ($this['payco_coupon_price']) {
				$payco_detail['Payco����'] = $this['payco_coupon_price'];
			}

			$payco_detail['Payco'.$settlekind_label[$this['settlekind']]] = $this['settleprice'] - $this['payco_use_point'] - $this['payco_coupon_price'];
			arsort($payco_detail);
		}

		return $payco_detail;
	}

	// @qni 2015-04 �����ݾ� > ��ǰ����
	public function getGoodsDiscount() {
		return $this['o_special_discount_amount'];
	}

	// @qni 2015-04 �����ݾ� > ��ǰ���� ����
	public function getGoodsDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this['o_special_discount_amount']) {
			$discount_detail['��ǰ����'] = $this['o_special_discount_amount'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 �����ݾ� > ��������
	public function getCouponDiscount() {
		return $this['coupon'];
	}

	// @qni 2015-04 �����ݾ� > �������� ����(���� + ��ٿ�����)
	public function getCouponDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this['coupon']) {
			$discount_detail['��������'] = array('%����' => $this->getPercentCouponDiscount(), '�ݾ�����' => $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'],);
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['��������']['��ٿ�'] = $this['about_dc_sum'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s', $operator, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s��', $operator, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 �����ݾ� > ������(������ + ���̹�ĳ�� + ���̹����ϸ���)
	public function getEmoneyDiscount() {
		return $this['emoney'] + $this->getNcashEmoneyDiscount() + $this->getNcashCashDiscount();
	}

	// @qni 2015-04 �����ݾ� > �����ݳ� ���̹����ϸ��� (����� ����)
	public function getNcashEmoneyDiscount() {
		$amount = 0;
		foreach ($this->getOrderCancels() as $cancel) {
			$amount += $cancel['rncash_emoney'];
		}

		return $this['ncash_emoney'] + $amount;
	}

	// @qni 2015-04 �����ݾ� > �����ݳ� ���̹�ĳ�� (����� ����)
	public function getNcashCashDiscount() {
		$amount = 0;
		foreach ($this->getOrderCancels() as $cancel) {
			$amount += $cancel['rncash_cash'];
		}

		return $this['ncash_cash'] + $amount;
	}

	// @qni 2015-04 �����ݾ� > ������(������ + ���̹�ĳ�� + ���̹����ϸ���) ����
	public function getEmoneyDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this['emoney']) {
			$discount_detail['�����ݻ��'] = $this['emoney'];
		}

		if ($this->getNcashEmoneyDiscount()) {
			$discount_detail['���̹����ϸ������'] = $this->getNcashEmoneyDiscount();
		}

		if ($this->getNcashCashDiscount()) {
			$discount_detail['���̹�ĳ�����'] = $this->getNcashCashDiscount();
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 �����ݾ� > �����ݾ�
	public function getSettledAmount() {
		return $this->getSettleAmount() + $this->getEnuriAmount();
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > ���(ȯ��)�����ݾ�
	public function getCancelingAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getSettleAmount();
			}
		}

		// ��ۺ� (������ �� ��ü�ֹ������ ��� �ڵ����)
		$amount += $this->getCancelingDeliveryFee();

		// �������� ��üǰ�� ����� ��� ���̹� ���ϸ����� ��ҽ� ��� �����ǵ��� ó��
		$amount -= $this->getCancelingEmoney();

		// ��ü �ֹ� ǰ�� ����� ���
		// ��ۺ� - ������ - �����ݻ��� - ���̹����ϸ��� - ���̹�ĳ�� - �ݾ����� + ������������� �� �ջ��Ѵ�.
		if ($this->_is_all_canceled) {
			$amount += $this['eggFee'] - ($this['coupon'] - $this->getPercentCouponDiscount());
		}

		return $amount;
	}

	public function isCancelingEgg() {
		return $this->_is_all_canceled && $this['eggFee'];
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > ��ǰ�Ǹűݾ�
	public function getCancelingGoodsAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getAmount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > ȸ������
	public function getCancelingMemberDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getMemberDiscount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > ��ǰ����
	public function getCancelingGoodsDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getSpecialDiscount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > �������� (�κ���ҽ� �������� ���� ��� ����ؾ� ��)
	public function getCancelingCouponDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$amount += $item->getPercentCouponDiscount();
			}
		}

		// (�����ݾ� - �ֹ��������αݾ� = �ݾ�����) - ��ٿ����αݾ� + ������� ��ǰ�������αݾ�
		if ($this->_is_all_canceled) {
			$amount += $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'];
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > �������� ����
	function getCancelingCouponDiscountDetailArray($format = false) {
		$discount_detail = array();
		$coupon_percent_amount = 0;
		$coupon_price_amount = 0;
		$coupon_about_amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling()) {
				$coupon_percent_amount += $item->getPercentCouponDiscount();
			}
		}

		if ($this->_is_all_canceled) {
			$coupon_price_amount = $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'];
		}

		if ($this['coupon']) {
			$discount_detail['��������'] = array('%����' => $coupon_percent_amount, '�ݾ�����' => $coupon_price_amount, $this['about_dc_sum']);
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['��������']['��ٿ�'] = $this['about_dc_sum'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s', $operator, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s��', $operator, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > ������
	public function getCancelingEmoney() {
		$amount = 0;

		// ���̹� ���ϸ���/ĳ�� ���
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCanceling()) {
				$amount += $cancel['rncash_emoney'] + $cancel['rncash_cash'];
			}
		}

		// �ֹ���ü�� ��ҷ� �� ��� ������� ������ ��ȯ
		if ($this->_is_all_canceled) {
			$amount += $this['emoney'];
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ������(������ + ���̹�ĳ�� + ���̹����ϸ���) ����
	public function getCancelingEmoneyDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this->_is_all_canceled && $this['emoney']) {
			$discount_detail['�����ݻ��'] = $this['emoney'];
		}

		// ���̹� ���ϸ���/ĳ�� ���
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCanceling()) {
				if ($cancel['rncash_emoney']) {
					$discount_detail['���̹����ϸ������'] += $cancel['rncash_emoney'];
				}

				if ($cancel['rncash_cash']) {
					$discount_detail['���̹�ĳ�����'] += $cancel['rncash_cash'];
				}

			}
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ��� �ֹ���ǰ ����
	public function getCancelingCount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCanceling())
				$amount++;
		}
		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ���(ȯ��)�Ϸ�ݾ�
	public function getCancelCompletedAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getSettleAmount();
			}
		}

		// ��ۺ� (������ �� ��ü�ֹ������ ��� ���)
		$amount += $this->getCancelCompletedDeliveryFee();

		// �������� ��üǰ�� ����� ��� ���̹� ���ϸ����� ��ҽ� ��� �����ǵ��� ó��
		$amount -= $this->getCancelCompletedEmoney();

		// ��ü �ֹ� ǰ�� ����� ���
		// ��ۺ� - ������ - �����ݻ��� - ���̹����ϸ��� - ���̹�ĳ�� - �ݾ����� + ������������� �� �ջ��Ѵ�.
		if ($this->_is_all_cancelCompleted) {
			$amount += $this[eggFee] - ($this[coupon] - $this->getPercentCouponDiscount());
		}

		return $amount;
	}

	public function isCancelCompletedEgg() {
		return $this->_is_all_cancelCompleted && $this['eggFee'];
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ��ǰ�Ǹűݾ�
	public function getCancelCompletedGoodsAmount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getAmount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ȸ������
	public function getCancelCompletedMemberDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getMemberDiscount();
			}
		}
		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ��ǰ����
	public function getCancelCompletedGoodsDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getSpecialDiscount();
			}
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ��������
	public function getCancelCompletedCouponDiscount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount += $item->getPercentCouponDiscount();
			}
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > �������� ����
	public function getCancelCompletedCouponDiscountDetailArray($format = false) {
		$discount_price = $coupon_percent_amount = 0;
		$discount_detail = array();

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$coupon_percent_amount += $item->getPercentCouponDiscount(); // ��ǰ���� ���αݾ�
			}
		}

		if ($this['coupon']) {
			if ($this->_is_all_cancelCompleted) {
				$discount_price = $this['coupon'] - $this->getPercentCouponDiscount() - $this['about_dc_sum'];
			}
			$discount_detail['��������'] = array('%����' => $coupon_percent_amount, '�ݾ�����' => $discount_price,);
		}

		if ($this['about_coupon_flag']) {
			$discount_detail['��������']['��ٿ�'] = $this['about_dc_sum'];
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s', $operator, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s��', $operator, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ���(ȯ��)�Ϸ�ݾ� ���� (�� ���(ȯ��)�ݾ� - ȯ�Ҽ�����)
	public function getCancelCompletedDetailArray($format = false) {
		$amount = array();

		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				$amount['ȯ�Ҽ�����'] += $cancel['rfee'];
			}
		}

		$amount['ȯ�ұݾ�'] += $this->getCancelCompletedAmount() - $amount['ȯ�Ҽ�����'];
		ksort($amount);

		if ($format) {
			$tmp = array();
			foreach ($amount as $k => $v) {
				$operator = $v >= 0 ? '+' : '-';
				if (sizeof($tmp) == 0)
					$operator = '';
				$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format(abs($v)));
			}
			return $tmp;
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ������
	public function getCancelCompletedEmoney() {
		$amount = 0;

		// ���̹� ���ϸ���/ĳ�� ���
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				$amount += $cancel['rncash_emoney'] + $cancel['rncash_cash'];
			}
		}

		// �ֹ���ü�� ��ҷ� �� ��� ��ҿϷ� ������ ��ȯ
		if ($this->_is_all_cancelCompleted) {
			$amount += $this['emoney'];
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ������(������ + ���̹�ĳ�� + ���̹����ϸ���) ����
	public function getCancelCompletedEmoneyDiscountDetailArray($format = false) {
		$discount_detail = array();

		if ($this->_is_all_cancelCompleted && $this['emoney']) {
			$discount_detail['�����ݻ��'] = $this['emoney'];
		}

		// ���̹� ���ϸ���/ĳ�� ���
		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				if ($cancel['rncash_emoney']) {
					$discount_detail['���̹����ϸ������'] += $cancel['rncash_emoney'];
				}

				if ($cancel['rncash_cash']) {
					$discount_detail['���̹�ĳ�����'] += $cancel['rncash_cash'];
				}

			}
		}

		if ($format) {
			$tmp = array();
			foreach ($discount_detail as $k => $v) {
				$operator = '+';
				if (sizeof($tmp) == 0)
					$operator = '';

				if (is_array($v)) {
					$_tmp = array();
					foreach ($v as $k2 => $v2) {
						if ($v2) {
							$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
						}
					}
					$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
				} else {
					$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
				}
			}
			return $tmp;
		}

		return $discount_detail;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� �ֹ���ǰ ����
	public function getCancelCompletedCount() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) {
				$amount++;
			}
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ������ (������� ������ ������)
	public function getRefundedEmoney() {
		$amount = 0;

		foreach ($this->getOrderItems() as $item) {
			$amount += array_sum($item->getRefundedEmoney());
		}

		return $amount;
	}

	// @qni 2015-04 ���������ݾ� > �����ڰ�������
	function getPaycoSettleDetailArray($format = false) {
		$payco_detail = array();

		if ($this->isPaycoPay()) {
			$settlekind_label = array(
				"a" => "������",
				"c" => "�ſ�ī��",
				"o" => "������ü",
				"v" => "�������",
				"d" => "��������",
				"h" => "�ڵ���",
				"p" => "����Ʈ",
				"u" => "�ſ�ī�� (�߱�)",
				"y" => "��������",
				"e" => "������ ����Ʈ",
			);

			$payco_detail['Payco'.$settlekind_label[$this['settlekind']]] = $this->getCancelCompletedRealSettleAmount();
			arsort($payco_detail);

			if ($format) {
				$tmp = array();
				foreach ($payco_detail as $k => $v) {
					$operator = '+';
					if (sizeof($tmp) == 0)
						$operator = '';

					$k = str_replace('Payco', '<img src="../img/icon_payco.gif">', $k);

					if (is_array($v)) {
						$_tmp = array();
						foreach ($v as $k2 => $v2) {
							if ($v2) {
								$_tmp[] = sprintf('%s : %s��', $k2, number_format($v2));
							}
						}
						$tmp[] = sprintf('%s %s(%s)', $operator, $k, implode(' / ', $_tmp));
					} else {
						$tmp[] = sprintf('%s %s(%s��)', $operator, $k, number_format($v));
					}
				}
				return $tmp;
			}
		}

		return $payco_detail;
	}

	// @qni 2015-04 ���������ݾ� > ȯ�Ҽ�����
	public function getRefundedFeeAmount() {
		$amount = 0;

		foreach ($this->getOrderCancels() as $cancel) {
			if ($cancel->hasCancelCompleted()) {
				$amount += $cancel['rfee'];
			}
		}

		return $amount;
	}

	// @qni 2015-04 ���������ݾ� > ȯ�Ҽ����� ����
	public function getRefundedFeeCount() {
		if ($this->getRefundedFeeAmount() > 0) {
			return true;
		}
		return false;
	}

	// @qni 2015-04 ���������ݾ� > �����ǰ����ݾ� = �����ֹ��ݾ� + ��ۺ� - ���αݾ� + ������������� - ��ҿϷ�ݾ�
	public function getCancelCompletedRealSettleAmount() {
		// ���ʰ����ݾ� - ���(ȯ��)�Ϸ�ݾ� (���/ȯ�������ݾ� ������) - ������
		return $this->getSettledAmount() - $this->getCancelCompletedAmount() - $this->getEnuriAmount();
	}

	// @qni 2015-04 ���������ݾ� > ���������ݾ� (���������ݾ� + ȯ�Ҽ�����)
	public function getRealPrnSettleAmount() {
		// �ڿ� ���̹�ĳ�ø� ������ ���� ������ �̹� getCancelCompletedAmount�� ��ҵ� ���̹�ĳ�ð� �����ǰ� order ���̺��� ���̹�ĳ�ð� 0�� �Ǿ��� ����
		return $this->getCancelCompletedRealSettleAmount() + $this->getRefundedFeeAmount();
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > ȯ�� �� ��������ݾ� (���ʰ����ݾ� - ��ҿϷ�ݾ� - ��������ݾ�)
	public function getCancelingRealPrnSettleAmount() {
		return $this->getRealPrnSettleAmount() - $this->getCancelingAmount();
	}

	// @qni 2015-04 ���������ݾ� > ������ ��ȣǥ��
	public function getEnuriSign() {
		if ($this['enuri'] > 0) {
			return '+ ';
		} else {
			return ' ';
		}
	}

	// @qni 2015-04 ���������ݾ� > ������
	public function getEnuriAmount() {
		return $this['enuri'];
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > PAYCO ��ۺ�
	public function getPaycoCancelingDeliveryFee() {
		$amount = 0;

		if ($this->isPaycoPay()) {
			$order_delivery_item = new orderDeliveryItem($this['ordno']);
			$delivery_price = $order_delivery_item->getCancelingDeliveryFee($this['ordno']);
			$amount = $delivery_price['total_cancel_delivery_price'];
			unset($order_delivery_item);
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�����ݾ� > ��ۺ�
	public function getCancelingDeliveryFee() {
		$amount = 0;
		$amount += $this->getPaycoCancelingDeliveryFee();

		// ��ü �ֹ� ǰ�� �������/��ҿϷ��̸鼭 �����ڰ����� �ƴ� ���
		if ($this->_is_all_canceled && !$this->isPaycoPay()) {
			$amount += $this->getDeliveryFee();
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > PAYCO ��ۺ�
	public function getPaycoCancelCompletedDeliveryFee() {
		$amount = 0;

		if ($this->isPaycoPay()) {
			$order_delivery_item = new orderDeliveryItem($this['ordno']);
			$delivery_price = $order_delivery_item->getCancelCompletedDeliveryFee($this['ordno']);
			$amount = $delivery_price['total_cancel_delivery_price'];
			unset($order_delivery_item);
		}

		return $amount;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ�ݾ� > ��ۺ�
	public function getCancelCompletedDeliveryFee() {
		$amount = 0;
		$amount += $this->getPaycoCancelCompletedDeliveryFee();

		// ��ü �ֹ� ǰ�� ����� ���
		if ($this->_is_all_cancelCompleted && !$this->isPaycoPay()) {
			$amount += $this->getDeliveryFee();
		}

		return $amount;
	}

	// @deprecated ������ �����ϸ� ����� �����̸� ������ ��Ҵܰ躰 ����Ʈ �����ϴ� �κ���
	public function getPaycoCancelCompletedPoint($sno = 0) {
		$amount = 0;
		$settlekind_label = array(
			"a" => "������",
			"c" => "�ſ�ī��",
			"o" => "������ü",
			"v" => "�������",
			"d" => "��������",
			"h" => "�ڵ���",
			"p" => "����Ʈ",
			"u" => "�ſ�ī�� (�߱�)",
			"y" => "��������",
			"e" => "������ ����Ʈ",
		);

		$payco_price = $this['settleprice'] - $this['payco_use_point'];//1000
		$payco_point = $this['payco_use_point'];//12000

		if ($this->isPaycoPay()) {
			foreach($this->getOrderCancels() as $cancel) {
				if ($cancel->hasCancelCompleted()) {
					if ($payco_price <= $cancel['rprice']) {
						$payco_price = 0;
						$payco_point -= ($cancel['rprice'] - $payco_price);
					} else {
						$payco_price -= $cancel['rprice'];
					}
				}

				if ($cancel['sno'] == $sno) break;
			}
		}

		return $this['payco_use_point'] - $payco_point;
	}

	// @qni 2015-05 ���ʺ��հ��� > ����/�鼼 ��ǰ�����ݾ�
	// �Ʒ� �Լ��� Canceled�� ������ ������ pgcancel�ʵ尡 r,y�� ���
	// @return array [0]�鼼/[1]���� �ֹ���ǰ �����ݾ� �ջ�
	public function getSettleAmountsForTax() {
		$amounts = array();
		foreach($this->getOrderItems() as $item) {
			$key = $item['tax'] == 0 ? kTAXFREE : kTAXALL;
			$amounts[$key] += $item->getSettleAmountWithTax($item['tax']);
		}
		return $amounts;
	}

	// @qni 2015-05 ���ʺ��հ��� > ����/�鼼 �ֹ���ü ���αݾ�
	// ������ ��ǰ����(getPercentCouponDiscount)�� ���� ������ �ֹ����̺��� coupon�� ���������̺��� coupon�� �ߺ��ΰ� �Ǳ⶧���� �ش� �κ��� ����� ��Ȯ�� ���αݾ��� ����
	public function getDiscountAmountForTax() {
		return $this->getCouponDiscount() + $this->getNaverDiscountAmountForTax() + $this[emoney] + $this['enuri'] - $this->getPercentCouponDiscount();
	}

	public function getNaverDiscountAmountForTax() {
		return $this->getNcashEmoneyDiscount() + $this->getNcashCashDiscount();
	}

	// @qni 2015-05 ���ʺ��հ��� > �鼼����
	public function getTaxFreeRatio() {
		$amounts = $this->getSettleAmountsForTax();
		return $amounts[kTAXFREE]/array_sum($amounts);
	}


	// @qni 2015-05 ���ʺ��հ��� > �鼼���αݾ�
	public function getTaxFreeDiscountAmount() {
		return ceil($this->getDiscountAmountForTax() * $this->getTaxFreeRatio());
	}

	// @qni 2015-05 ���ʺ��հ��� > �鼼�����ݾ�
	public function getTaxFreeTmpSettleAmount() {
		$amounts = $this->getSettleAmountsForTax();
		return $amounts[kTAXFREE] - $this->getTaxFreeDiscountAmount();
	}

	// @qni 2015-05 ���ʺ��հ��� > �鼼�����ݾ� (ȭ����¿�)
	public function getTaxFreeSettleAmount() {
		// ���� ���� �鼼�ݾ��� ��� �鼼�ݾ׺��� ���� ��� �������� ����

		$amount = $this->getTaxFreeTmpSettleAmount();
		// ������ ������ ��� �鼼���� ����
		if ($this->getTaxTmpSettleAmount() < 0) {
			$amount += $this->getTaxTmpSettleAmount();
		}
		return $amount > 0 ? $amount : 0;
	}


	// @qni 2015-05 ���ʺ��հ��� > �������αݾ�
	public function getTaxDiscountAmount() {
		return $this->getDiscountAmountForTax() - $this->getTaxFreeDiscountAmount();
	}


	// @qni 2015-05 ���ʺ��հ��� > �����߰��ݾ�
	public function getTaxAddAmount() {
		return $this->getDeliveryFee() + $this[eggFee];
	}


	// @qni 2015-05 ���ʺ��հ��� > ���������ݾ�
	public function getTaxTmpSettleAmount() {
		$amounts = $this->getSettleAmountsForTax();
		return $amounts[kTAXALL] - $this->getTaxDiscountAmount() + $this->getTaxAddAmount();
	}
	public function getTaxSettleAmount() {
		$amount = $this->getTaxTmpSettleAmount();
		// �鼼�� ������ ��� �������� ����
		if ($this->getTaxFreeTmpSettleAmount() < 0) {
			$amount += $this->getTaxFreeTmpSettleAmount();
		}
		return $amount;
	}


	// @qni 2015-05 ���ʺ��հ��� > �鼼�ݾ�
	public function getTaxFreeAmount() {
		return $this->getTaxFreeSettleAmount();
	}

	// @qni 2015-05 ���ʺ��հ��� > �����ݾ�
	public function getTaxAmount() {
		return ceil($this->getTaxSettleAmount() / 1.1);
	}

	// @qni 2015-05 ���ʺ��հ��� > �ΰ���
	public function getVatAmount() {
		return $this->getTaxSettleAmount() - $this->getTaxAmount();
	}

	// @qni 2015-05 ������ ����ֹ� ����Ʈ�� SNO
	private function _getCancelLastSno() {
		$sno = 0;
		foreach($this->getOrderCancels() as $cancel) {
			$sno = $cancel['sno'];
		}
		return $sno;
	}


	/**
	 * getFirstTaxAmounts()
	 * ���հ��� > ���� �ֹ����� ���հ����ݾ� ��ȯ
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access public
	 * @return array ���� ���հ����ݾ�
	 *		taxall ��ü�����ݾ�
	 *		tax �����ݾ�
	 *		vat �ΰ���
	 *		taxfree �鼼�ݾ�
	 */
	public function getFirstTaxAmounts() {
		return array(
			kTAXALL => $this->getTaxSettleAmount(),
			kTAXFREE => $this->getTaxFreeAmount(),
			kTAX => $this->getTaxAmount(),
			kVAT => $this->getVatAmount(),
		);
	}

	/**
	 * _setCaculateVat()
	 * ���հ��� > ���� �����鼼 �迭�� VAT �߰� �� �迭 ��ȯ
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access private
	 * @param array $data ����� ����/�鼼 �迭
	 * @return array ���� ���հ����ݾ�
	 *		taxall ��ü�����ݾ�
	 *		tax �����ݾ�
	 *		vat �ΰ���
	 *		taxfree �鼼�ݾ�
	 * @error array
	 *		code �����ڵ�
	 *		msg �����޽���
	 */
	private function _setCalculateVat($data) {
		if (array_key_exists(kTAXALL, $data)) {
			// �鼼�ݾ��� ������ ������ ��� ������ �ΰ�
			if ($data[kTAXFREE] < 0) {
				if ($data[kTAXALL] >= abs($data[kTAXFREE])) {
					$data[kTAXALL] += $data[kTAXFREE];
					$data[kTAXFREE] = 0;
				} else {
					// ����
					$data = array(
						'code' => '902',
						'msg' => '��� �� �ݾ��� �����ϴ�.',
					);
				}
			}

			// �����ݾ��� ������ ������ ��� �鼼�� �ΰ�
			if ($data[kTAXALL] < 0) {
				if ($data[kTAXFREE] >= abs($data[kTAXALL])) {
					$data[kTAXFREE] += $data[kTAXALL];
					$data[kTAXALL] = 0;
				} else {
					// ����
					$data = array(
						'code' => '902',
						'msg' => '��� �� �ݾ��� �����ϴ�.',
					);
				}
			}

			if (!array_key_exists('code', $data)) {
				// �������
				$data[kTAX] = ceil($data[kTAXALL]/1.1);
				$data[kVAT] = $data[kTAXALL] - $data[kTAX];
			}
		}
		return $data;
	}


	/**
	 * _setCalculateCancelingTaxAmounts()
	 * ���հ��� > SNO���� ������ ����������� ���� ��һ�ǰ ���հ����ݾ� ����
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access private
	 * @param int $sno ��ҽ����� SNO
	 * @param array $realdata SNO ������ ��������
	 * @return array ���� ���հ����ݾ�
	 *		taxall ��ü�����ݾ�
	 *		tax �����ݾ�
	 *		vat �ΰ���
	 *		taxfree �鼼�ݾ�
	 * @error array
	 *		code �����ڵ�
	 *		msg �����޽���
	 */
	private function _setCalculateCancelingTaxAmounts($sno, $realdata = null) {
		$cancel = $this->getOrderCancel($sno);
		if (!$cancel instanceof order_cancel) {
			$data = array(
				'code' => '901',
				'msg' => '�ش� �ֹ����� ���SNO�� �������� �ʽ��ϴ�.',
			);
		} else {
			// realdata�� ���� ��� ���� ����ݾ� ȣ��
			if (is_null($realdata)) {
				$realdata = $this->getRealTaxAmounts($sno);
			}

			// ������ �ֹ���� ��ǰ�� ��� ���� �� ��ۺ� ����� �� �ֵ��� ������ cancel.class�� ������
			$discount[kTAXFREE] = $discount[kTAXALL] = 0;
			if ($this->_is_all_canceled && $sno == $this->_getCancelLastSno()) {
				$discount[kTAXALL] += $this->getDeliveryFee() + $this['eggFee']; // �������ݾ� = �������ݾ� + ��ۺ� + �������������
				$discount[kTAXALL] -= $this->getTaxDiscountAmount(); // �������αݾ� = �������ݾ� - �������αݾ�
				$discount[kTAXFREE] -= $this->getTaxFreeDiscountAmount(); // �鼼���αݾ� = ���鼼�ݾ� - �鼼���αݾ�

				// ���̹��� ù ��ҽ� �����⶧���� ���������� ������� �Ѵ�.
				$discount[kTAXALL] += ($this->getNcashCashDiscount() + $this->getNcashEmoneyDiscount());
			}
			$data = $cancel->getCancelTaxAmounts($discount);

			// ���� ���� �鼼�ݾ��� ��� �鼼�ݾ׺��� ���� ��� �������� ����
			if ($realdata[kTAXFREE] < $data[kTAXFREE]) {
				$difference = $data[kTAXFREE] - $realdata[kTAXFREE];
				$data[kTAXALL] += $difference;
				$data[kTAXFREE] = $realdata[kTAXFREE];
			}

			// ���� ���� �����ݾ��� ��� �����ݾ׺��� ���� ��� �鼼���� ����
			if ($realdata[kTAXALL] < $data[kTAXALL]) {
				$difference = $data[kTAXALL] - $realdata[kTAXALL];
				$data[kTAXFREE] += $difference;
				$data[kTAXALL] = $realdata[kTAXALL];
			}
		}

		return $this->_setCalculateVat($data);
	}


	/**
	 * getRealTaxAmounts()
	 * ���հ��� > ���� �����ִ� ���հ����ݾ� ��ȯ (��ҿϷ�� �͸� ���)
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access public
	 * @param int $sno �ִ°��(�ش� SNO ���������� ����), ���°��(��ҿϷ�� �ֹ��� ����)
	 * @param boolean $is_enamoo �̳��� ��� �������� ó�� (�ֹ������� ��ҿϷ�/���� ����)
	 * @return array ���� ���հ����ݾ�
	 *		taxall ��ü�����ݾ�
	 *		tax �����ݾ�
	 *		vat �ΰ���
	 *		taxfree �鼼�ݾ�
	 * @error array
	 *		code �����ڵ�
	 *		msg �����޽���
	 * @use
	 *		getRealTaxAmounts(SNO)
	 *			1) �ش� SNO ���ڰ��� 0�� ��� : PG������ ��ҿϷ� ���� ���� �����ִ� ���հ����ݾ� ��ȯ
	 *			2) �ش� SNO�� �ֹ���Ұ� PG�Ϸ��� ��� : SNO ���������� ����� �����ִ� ���հ����ݾ� ��ȯ
	 *			3) �ش� SNO�� �ֹ���Ұ� PG�Ϸᰡ �ƴ� ��� : 1)���� ����
	 */
	public function getRealTaxAmounts($sno = 0, $is_enamoo = false) {
		// ���� ���հ����ݾ� �Ҵ� (�ʱ�ȭ)
		$realdata = $this->getFirstTaxAmounts();

		foreach($this->getOrderCancels() as $cancel) {
			// SNO�� �ִ� ��� ���� ����ݾ� ��ȯó���� ���� ����
			if (is_numeric($sno) && $sno > 0 && $cancel['sno'] == $sno) break;

			// ��ҽ��� (�̳����� ��Ұ� �ƴϸ� ������ PG��ҿϷ� �������� ����
			$is_cancel = $cancel->hasPgCanceled();
			if ($is_enamoo) {
				$is_cancel = $cancel->hasCancelCompleted();
			}

			// ��ҿϷ�� ����ݾ� = �����ݾ� - ��ұݾ�
			if ($is_cancel) {
				$data = $this->_setCalculateCancelingTaxAmounts($cancel['sno'], $realdata);

				// ��Һκ� �����ǰ� �����ݾ�
				$realdata[kTAXFREE] -= $data[kTAXFREE];
				$realdata[kTAXALL] -= $data[kTAXALL];
			}
		}

		return $this->_setCalculateVat($realdata);
	}


	/**
	 * getCancelItemTaxWithSno()
	 * ���հ��� > SNO�� ��� �� �ֹ���ǰ�� ���հ����ݾ� ��ȯ (�ش� SNO�� �ֹ���Ұ� PG�Ϸ��� ���)
	 * ���հ��� > SNO�� ��� �� �ֹ���ǰ�� ���հ����ݾ� ��ȯ (�ش� SNO�� �ֹ���Ұ� PG�Ϸᰡ �ƴ� ���)
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @access public
	 * @param int $sno ��ҽ����� SNO
	 * @return array ���� ���հ����ݾ�
	 *		taxall ��ü�����ݾ�
	 *		tax �����ݾ�
	 *		vat �ΰ���
	 *		taxfree �鼼�ݾ�
	 * @error array
	 *		code �����ڵ�
	 *		msg �����޽���
	 */
	public function getCancelItemTaxWithSno($sno) {
		return $this->_setCalculateCancelingTaxAmounts($sno);
	}

	public function getRealTaxAmountsPaycoAdd($sno = 0, $is_enamoo = false) {
		// ���� ���հ����ݾ� �Ҵ� (�ʱ�ȭ)
		$realdata = $this->getFirstTaxAmounts();

		foreach($this->getOrderCancels() as $cancel) {
			// SNO�� �ִ� ��� ���� ����ݾ� ��ȯó���� ���� ����
			if (is_numeric($sno) && $sno > 0 && $cancel['sno'] == $sno) break;

			// ��ҽ��� (�̳����� ��Ұ� �ƴϸ� ������ PG��ҿϷ� �������� ����
			$is_cancel = $cancel->hasPgCanceled();
			if ($is_enamoo) {
				$is_cancel = $cancel->hasCancelCompleted();
			}

			// ��ҿϷ�� ����ݾ� = �����ݾ� - ��ұݾ�
			if ($is_cancel) {
				$data = $this->_setCalculateCancelingTaxAmounts($cancel['sno'], $realdata);

				// ��Һκ� �����ǰ� �����ݾ�
				$realdata[kTAXFREE] -= $data[kTAXFREE];
				$realdata[kTAXALL] -= $data[kTAXALL];
			}
		}

		if($this->isPaycoPay()){
			$realdata[kTAXALL] -= $this->getPaycoCancelCompletedDeliveryFee();
		}

		return $this->_setCalculateVat($realdata);
	}

	public function getRedifineDeliveryExclude($taxPrice)
	{
		$deliveryFee = $deliverySurtax = 0;
		$deliveryFee = $this->getDeliveryFee() - $this->getCancelCompletedDeliveryFee();
		$taxPrice['taxall'] -= (int)$deliveryFee;
		$taxPrice['tax'] = ceil($taxPrice['taxall']/1.1);
		$taxPrice['vat'] = $taxPrice['taxall'] - $taxPrice['tax'];

		return array($taxPrice['taxall'], $taxPrice['tax'], $taxPrice['vat']);
	}

	public function hasPaycoExchange()
	{
		list($settle_inflow) = $this->_db->fetch("select settleInflow from " . GD_ORDER . " where ordno='{$this['oldordno']}'");
		if($settle_inflow == 'payco') return true;
		else return false;
	}
}
