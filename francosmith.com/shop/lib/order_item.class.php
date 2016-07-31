<?
class order_item implements ArrayAccess
{

	private $_db;

	private $_data;

	public function __construct()
	{
		$this->_db = Core::loader('db');
	}

	/**
	 * Whether a offset exists
	 * @param string $offset An offset to check for
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Offset to retrieve
	 * @param string $offset The offset to retrieve.
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}

	/**
	 * Offset to set
	 * @param string $offset The offset to assign the value to
	 * @param mixed $value The value to set
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}

	/**
	 * Offset to unset
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}


	public function load($sno)
	{
		if (is_null($this->_data)) {
			$query = "
			select b.*,a.*, tg.tgsno from
				" . GD_ORDER_ITEM . " a
				left join " . GD_GOODS . " b on a.goodsno=b.goodsno
				left join " . GD_TODAYSHOP_GOODS . " tg on a.goodsno=tg.goodsno
			where
				a.sno='$sno'
			";
			$this->_data = $this->_db->fetch($query, 1);
		}

	}

	public function getSettleAmount()
	{
		return $this->getAmount() - $this->getDiscount();
	}

	public function getDiscount()
	{
		return $this->getPercentCouponDiscount() + $this->getMemberDiscount() + $this->getSpecialDiscount();
	}

	public function getMemberDiscount()
	{
		return $this['memberdc'] * $this['ea'];
	}

	// % ���� ���ξ� (���� �Ǵ� ���� % ���� ���� �����)
	public function getPercentCouponDiscount()
	{
		return $this['coupon'] * $this['ea'];
	}

	// ���� ������
	public function getCouponReserve()
	{
		return $this['coupon_emoney'] * $this['ea'];
	}

	// �ֹ���ǰ�� �ֹ� �ݾ�
	public function getAmount()
	{
		return $this['price'] * $this['ea'];
	}

	public function getCanceledAmount()
	{
		// ��һ����� ���, �ֹ��ݾ� - ���ξ�
		return $this->hasCanceled() ? $this->getSettleAmount() : 0;
	}

	public function hasCanceled()
	{
		return $this['istep'] > 40 ? true : false;
	}

	public function hasRefunded()
	{
		return $this['istep'] == 44 && in_array($this['cyn'], array('r', 'y')) ? true : false;
	}

	public function getRefundedAmount()
	{
		if ($this->hasRefunded()) {
			$cancel = $this->_db->fetch("select * from gd_order_cancel where sno = '$this[cancel]'", 1);
			return array($cancel['sno']=>$cancel['rprice']);
		}
		else {
			return array();
		}
	}

	//tax amount
	public function getSupplyPrice()
	{
		if ($this['tax'] != '1') { // �鼼
			return $this->getAmount();
		}
		else { // ���� (vat 10%)
			return round($this->getAmount() / 1.1);
		}
	}

	public function getTax()
	{
		if ($this['tax'] != '1') { // �鼼
			return 0;
		}
		else { // ���� (vat 10%)
			return $this->getAmount() - $this->getSupplyPrice();
		}
	}

	public function getSpecialDiscount()
	{
		return $this['oi_special_discount_amount'] * $this['ea'];
	}

	/**
	 * �ֹ��� UI����
	 */

	// @qni 2015-04 order_item ���̺� cancel �ʵ�� order_cancel ���̺� ����
	private function _getOrderCancelWithSno($field = 'rprice') {
		return $this->_db->fetch("select sno, $field from gd_order_cancel where sno = '$this[cancel]'", 1);
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ� ��ǰ�ݾ�
	public function getCancelCompletedAmount()
	{
		// ��һ����� ���, �ֹ��ݾ� - ���ξ�
		return $this->hasCancelCompleted() ? $this->getSettleAmount() : 0;
	}

	// @qni 2015-04 ���(ȯ��)���� ����
	public function hasCanceling()
	{
		return $this['istep'] > 40 && $this['istep'] < 44 ? true : false;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ� ����
	public function hasCancelCompleted()
	{
		return $this['istep'] == 44 ? true : false;
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ� > ���(ȯ��)�Ϸ�ݾ� �� ���� ȯ���ֹ��ݾ� (ȯ�Ҽ������ ������ �������� ����)
	public function getRealCancelingAmount()
	{
		if ($this->hasCanceling()) {
			$cancel = $this->_getOrderCancelWithSno();
			return array($cancel['sno']=>$cancel['rprice']);
		}
		else {
			return array();
		}
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ� > ���(ȯ��)�Ϸ�ݾ� �� ���� ȯ���ֹ��ݾ� (ȯ�Ҽ������ ������ �������� ����)
	public function getRealRefundedAmount()
	{
		if ($this->hasCancelCompleted()) {
			$cancel = $this->_getOrderCancelWithSno();
			return array($cancel['sno']=>$cancel['rprice']);
		}
		else {
			return array();
		}
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ� > ȯ�Ҽ�����
	public function getRefundedFeeAmount()
	{
		if ($this->hasRefunded()) {
			$cancel = $this->_getOrderCancelWithSno('rfee');
			return array($cancel['sno']=>$cancel['rfee']);
		}
		else {
			return array();
		}
	}

	// @qni 2015-04 ���(ȯ��)�Ϸ� > ���(ȯ��)������
	public function getRefundedEmoney()
	{
		if ($this->hasCancelCompleted()) {
			$cancel = $this->_getOrderCancelWithSno('remoney');
			return array($cancel['sno']=>$cancel['remoney']);
		}
		else {
			return array();
		}
	}

	// @qni 2015-04 ���(ȯ��)���� > ���(ȯ��)������
	public function getCancelingEmoney()
	{
		if ($this->hasCanceling()) {
			$cancel = $this->_getOrderCancelWithSno('remoney');
			return array($cancel['sno']=>$cancel['remoney']);
		}
		else {
			return array();
		}
	}

	// @qni 2015-04 PG ��ҿϷ� Ȥ�� �κ���� ���� (�������� ��쿡 ���� ���� �߰�)
	public function hasPgCanceled() {
		$cancel = $this->_getOrderCancelWithSno('pgcancel');
		if ($this['istep'] > 40 && $this['istep'] < 50) {
			if (in_array($cancel['pgcancel'], array('r', 'y'))) {
				return true;
			} else {
				if ($this['cyn'] == 'r') {
					return true;
				}
			}
		}

		return false;
	}

	// @qni 2015-04 �������ο� ���� ��ǰ�ݾ�
	public function getSettleAmountWithTax($tax = 0) {
		$price = 0;
		if ($this['tax'] == $tax) {
			$price = $this->getSettleAmount();
		}
		return $price;
	}
}
