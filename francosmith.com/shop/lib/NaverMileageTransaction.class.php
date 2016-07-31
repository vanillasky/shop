<?php

/**
 * ���̹� ���ϸ��� ó�� Ŭ����
 *
 * @TODO : ���� naverNcash.class.php�� �ִ� ������ API���� ���븸 �߷��� ���� ���Ϸ� �Űܾ� ��
 *
 * @author NaverMileageTransaction.class.php workingparksee <parksee@godo.co.kr>
 * @version 1.0
 * @date 2013-02-19, 2013-02-19
 */
class NaverMileageTransaction
{

	private $config;

	private $ordno, $transactionId, $saveMode, $orderAmount, $calcAmount, $orderItem = array(), $serializedOrderItem, $mileageUseAmount, $cashUseAmount, $discountAmount;

	/**
	 * NaverMileageTransaction ��ü�� ������
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $ordno �̳��� �ֹ���ȣ
	 * @date 2013-02-19, 2013-02-19
	 */
	public function __construct($ordno)
	{
		$this->config = Core::loader('config')->load('ncash');

		$this->setOrdno($ordno);

		// ���޵� �ֹ���ȣ�� �� Ʈ�������� �ִ°�� ��ȸ
		$db = Core::loader('db');
		$nmtxResultSet = $db->query('SELECT * FROM gd_naver_mileage_transaction WHERE nm_ordno='.$ordno.' LIMIT 1');
		if ($naverMileageTransaction = $db->fetch($nmtxResultSet, true)) {
			$this->setTransactionId($naverMileageTransaction['nm_transaction_id']);
			$this->setSaveMode($naverMileageTransaction['nm_save_mode']);
			$this->setOrderAmount($naverMileageTransaction['nm_order_amount']);
			$this->setCalcAmount($naverMileageTransaction['nm_calc_amount']);
			foreach (unserialize($naverMileageTransaction['nm_serialized_order_item']) as $orderItem) {
				$this->addOrderItem($orderItem['goodsno'], $orderItem['goodsnm'], $orderItem['ea'], $orderItem['price']);
			}
			$this->serializeOrderItem();
			$this->setMileageUseAmount($naverMileageTransaction['nm_mileage_use_amount']);
			$this->setCashUseAmount($naverMileageTransaction['nm_cash_use_amount']);
			$this->setDiscountAmount($naverMileageTransaction['nm_discount_amount']);
		}
	}

	/**
	 * ���̹� ���ϸ����� ���/������ �ֹ����� �ֹ���ȣ ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $ordno �̳��� �ֹ���ȣ
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setOrdno($ordno)
	{
		$this->ordno = $ordno;
	}

	/**
	 * ���̹� ���ϸ��� Ʈ�������� �ֹ���ȣ ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int �̳��� �ֹ���ȣ
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getOrdno()
	{
		return $this->ordno;
	}

	/**
	 * ���̹� ���ϸ��� ���/���� Ʈ������ ���̵� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $transactionId ���̹� ���ϸ��� txId
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setTransactionId($transactionId)
	{
		$this->transactionId = $transactionId;
	}

	/**
	 * ���̹� ���ϸ��� Ʈ�������� ���̵� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string ���̹� ���ϸ��� txId
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getTransactionId()
	{
		return $this->transactionId;
	}

	/**
	 * ������ ������ġ ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $saveMode ������ġ naver_mileage_only : ���̹� ���ϸ�����, both : ���θ��� ���̹� ���ϸ��� �Ѵ� ����
	 * @date 2013-02-19, 2013-02-20
	 */
	public function setSaveMode($saveMode)
	{
		$this->saveMode = $saveMode;
	}

	/**
	 * ������ ������ġ ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string ������ġ naver_mileage_only : ���̹� ���ϸ�����, both : ���θ��� ���̹� ���ϸ��� �Ѵ� ����
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getSaveMode()
	{
		return $this->saveMode;
	}

	/**
	 * �� �ֹ��ݾ� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $orderAmount �� �ֹ��ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setOrderAmount($orderAmount)
	{
		$this->orderAmount = $orderAmount;
	}

	/**
	 * �� �ֹ��ݾ� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int �� �ֹ��ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getOrderAmount()
	{
		return $this->orderAmount;
	}

	/**
	 * ���̹� ���ϸ��� �������ݾ� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $calcAmount ���̹� ���ϸ��� �������ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setCalcAmount($calcAmount)
	{
		$this->calcAmount = $calcAmount;
	}

	/**
	 * ���̹� ���ϸ��� �������ݾ� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int ���̹� ���ϸ��� �������ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getCalcAmount()
	{
		return $this->calcAmount;
	}

	/**
	 * �ֹ���ǰ �߰�
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $goodsno ��ǰ��ȣ
	 * @param string $goodsnm ��ǰ��
	 * @param int $ea ���ż���
	 * @param int $price ��ǰ�ܰ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function addOrderItem($goodsno, $goodsnm, $ea, $price)
	{
		$this->orderItem[] = array(
		    'goodsno' => $goodsno,
		    'goodsnm' => $goodsnm,
		    'ea' => $ea,
		    'price' => $price,
		);
	}

	/**
	 * �߰��� �ֹ���ǰ�� ����ȭ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string
	 * @date 2013-02-19, 2013-02-19
	 */
	public function serializeOrderItem()
	{
		$this->serializedOrderItem = serialize($this->orderItem);
		return $this->serializedOrderItem;
	}

	/**
	 * ����ȭ�� �ֹ���ǰ�� ����ȭ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return array
	 * @date 2013-02-19, 2013-02-19
	 */
	public function unserializeOrderItem()
	{
		$this->orderItem = unserialize($this->serializedOrderItem);
		return $this->orderItem;
	}

	/**
	 * �־��� �ε����� �ش��ϴ� �ֹ���ǰ ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $index
	 * @return array 'goodsno' => int, 'goodsnm' => string, 'ea' => int, 'price' => int
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getOrderItem($index)
	{
		return $this->orderItem[$index];
	}

	/**
	 * ���̹� ���ϸ��� ���ݾ� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $mileageUseAmount ���̹� ���ϸ��� ���ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setMileageUseAmount($mileageUseAmount)
	{
		$this->mileageUseAmount = $mileageUseAmount;
	}

	/**
	 * ���̹� ���ϸ��� ���ݾ� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int ���̹� ���ϸ��� ���ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getMileageUseAmount()
	{
		return $this->mileageUseAmount;
	}

	/**
	 * ���̹� ĳ�� ���ݾ� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $cashUseAmount ���̹� ĳ�� ���ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setCashUseAmount($cashUseAmount)
	{
		$this->cashUseAmount = $cashUseAmount;
	}

	/**
	 * ���̹� ĳ�� ���ݾ� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int ���̹� ĳ�� ���ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getCashUseAmount()
	{
		return $this->cashUseAmount;
	}

	/**
	 * �̳������� ���ι��� �ݾ� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param int $discountAmount �̳������� ���ι��� �ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function setDiscountAmount($discountAmount)
	{
		$this->discountAmount = $discountAmount;
	}

	/**
	 * �̳������� ���ι��� �ݾ� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return int �̳������� ���ι��� �ݾ�
	 * @date 2013-02-19, 2013-02-19
	 */
	public function getDiscountAmount()
	{
		return $this->discountAmount;
	}

	/**
	 * ������ ������ ���Ͽ� Ʈ�������� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @date 2013-02-19, 2013-02-19
	 */
	public function createTransaction()
	{
		$db = Core::loader('db');
		$db->query('
			INSERT gd_naver_mileage_transaction SET
			nm_ordno = '.$this->getOrdno().',
			nm_transaction_id = "'.$this->getTransactionId().'",
			nm_save_mode = "'.$this->getSaveMode().'",
			nm_order_amount = '.$this->getOrderAmount().',
			nm_calc_amount = '.$this->getCalcAmount().',
			nm_serialized_order_item = "'.mysql_real_escape_string($this->serializeOrderItem()).'",
			nm_mileage_use_amount = '.$this->getMileageUseAmount().',
			nm_cash_use_amount = '.$this->getCashUseAmount().',
			nm_discount_amount = '.$this->getDiscountAmount().'
		');
	}

	/**
	 * ���� SESSION ����� ó������� ȣȯ�ϱ� ���� ������ ������ġ ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $saveMode ������ġ ncash : ���̹� ���ϸ�����, both : ���θ��� ���̹� ���ϸ��� �Ѵ� ����
	 * @date 2013-02-20, 2013-02-20
	 */
	public function setLegacySaveMode($saveMode)
	{
		switch ($saveMode) {
			case 'ncash':
				$this->saveMode = 'naver_mileage_only';
				break;
			case 'both':
				$this->saveMode = 'both';
				break;
			default :
				//throw new Exception('�߸��� ������ġ�Դϴ�.');
				return false;
				break;
		}
	}

	/**
	 * ���� SESSION ����� ó������� ȣȯ�ϱ� ���� ������ ������ġ ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return string ������ġ ncash : ���̹� ���ϸ�����, both : ���θ��� ���̹� ���ϸ��� �Ѵ� ����
	 * @date 2013-02-20, 2013-02-20
	 */
	public function getLegacySaveMode()
	{
		switch ($this->getSaveMode()) {
			case 'naver_mileage_only':
				$saveMode = 'ncash';
				break;
			case 'both':
				$saveMode = 'both';
				break;
			default :
				//throw new Exception('�߸��� ������ġ�Դϴ�.');
				return false;
				break;
		}
		return $saveMode;
	}

	/**
	 * ���� SESSION ����� ó������� ȣȯ�ϱ� ���� ������ Ʈ������ ��ü�� �迭�� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @return array
	 * @date 2013-02-20, 2013-02-20
	 */
	public function toLegacyArray()
	{
		$naverMileageTransaction = array();
		$naverMileageTransaction['ordno'] = $this->getOrdno();
		$naverMileageTransaction['save_mode'] = $this->getLegacySaveMode();
		$naverMileageTransaction['goodsPrice'] = $this->getCalcAmount();
		$naverMileageTransaction['orderAmount'] = $this->getOrderAmount();
		$naverMileageTransaction['discountAmount'] = $this->getDiscountAmount();
		$naverMileageTransaction['item'] = $this->unserializeOrderItem();
		$naverMileageTransaction['mileageUseAmount'.$this->config['api_id']] = $this->getMileageUseAmount();
		$naverMileageTransaction['cashUseAmount'.$this->config['api_id']] = $this->getCashUseAmount();
		$naverMileageTransaction['reqTxId'.$this->config['api_id']] = $this->getTransactionId();
		return $naverMileageTransaction;
	}

}

?>