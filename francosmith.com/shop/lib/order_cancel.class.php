<?
class order_cancel implements ArrayAccess
{
	public $taxfree_ratio;

	private $_db;
	private $_data;
	private $_order_item;

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
			select * from
				" . GD_ORDER_CANCEL . "
			where
				sno='$sno'
			";
			$this->_data = $this->_db->fetch($query, 1);
		}
	}

	public function getOrderItems() {
		if (is_null($this->_order_item)) {
			$this->_loadOrderItems();
		}

		return (array) $this->_order_item;
	}

	private function _loadOrderItems() {
		$query = "select sno from " . GD_ORDER_ITEM . " where cancel='{$this['sno']}' order by sno";
		$rs = $this->_db->query($query);

		$item = new order_item();

		$this->_order_item = array();

		while ($row = $this->_db->fetch($rs, 1)) {
			$_item = clone $item;
			$_item->load($row['sno']);
			$this->_order_item[] = $_item;
		}
	}

	public function hasCanceling() {
		foreach ($this->getOrderItems() as $item) {
			$rst = $item->hasCanceling();
		}
		return $rst;
	}

	public function hasCancelCompleted() {
		foreach ($this->getOrderItems() as $item) {
			$rst = $item->hasCancelCompleted();
		}
		return $rst;
	}

	// @qni 2015-05 PG������ ��ҿϷ� ���� (item.class �Լ��� üũ)
	public function hasPgCanceled() {
		foreach ($this->getOrderItems() as $item) {
			$rst = $item->hasPgCanceled();
		}
		return $rst;
	}

	// @qni 2015-05 PAYCO �ֹ��� ��� �ش� SNO ����� PAYCO ȯ�� ��ۺ� ��ȯ
    public function getPaycoCancelCompletedDeliveryFee($pg) {
        $amount = 0;

        if ($pg == 'payco') {
            $order_delivery_item = &load_class('orderDeliveryItem', 'orderDeliveryItem', $this['ordno']);
            $delivery_price = $order_delivery_item->getCancelCompletedDeliverFeeWithSno($this['sno']);
            $amount = $delivery_price['total_cancel_delivery_price'];
            unset($order_delivery_item);
        }

        return $amount;
    }

	/**
	 * getCancelTaxAmounts()
	 * ���հ��� > ����Ҵܰ��� ���հ����ݾ� (���� �ֹ���һ�ǰ�� ���)
	 *
	 * @release 2015-05
	 * @author qnibus
	 * @param array $discount �ֹ����αݾ� (������ ����� ��� discount�� ���� �Ҵ�Ǿ� �Ѿ��)
	 * @return array ����Ҵܰ��� ���հ����ݾ�
	 *		taxfree �鼼�ݾ�
	 *		taxall �����ݾ�
	 */
	public function getCancelTaxAmounts($discount) {
		// ��ȯ�� �ʱ�ȭ
		$data[kTAXFREE] = $data[kTAXALL] = 0;

		foreach ($this->getOrderItems() as $item) {
			$key = $item['tax'] == 0 ? kTAXFREE : kTAXALL;
			$data[$key] += $item->getSettleAmount();
		}

		// ���̹�ĳ��/���ϸ��� ���αݾ�
		$rncash = $this['rncash_cash'] + $this['rncash_emoney'];
		if($rncash > 0) {
			if ($data[kTAXFREE] > 0 && $data[kTAXALL] == 0) {// �鼼�� ���
				$data[kTAXFREE] -= $rncash;
			} else if ($data[kTAXALL] > 0 && $data[kTAXFREE] == 0) {// ������ ���
				$data[kTAXALL] -= $rncash;
			} else if ($data[kTAXFREE] > 0 && $data[kTAXALL] > 0) {// ����,�鼼�� ���
				$data[kTAXFREE] -= ceil($rncash * $this->taxfree_ratio);
				$data[kTAXALL] -= ($rncash + $data[kTAXFREE]);
			}
		}

		// ȯ�Ҽ����� ������ �߰�
		$data[kTAXALL] -= $this['rfee'];

		// ���� ��ҽ� �ֹ��� �ݾ� �߰�
		$data[kTAXALL] += $discount[kTAXALL];
		$data[kTAXFREE] += $discount[kTAXFREE];

		return $data;
	}
}
