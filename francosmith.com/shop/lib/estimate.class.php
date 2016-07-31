<?php
/**
 * ������ : ��ٱ��Ͽ� ���� ��ǰ���� �������� ����
 */

class estimate
{
	private $_db = null;

	/**
	 * ������
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = Core::loader('db');
	}

	/**
	 * ���ڸ� �ѱ۷� ��ȯ�ϴ� �Լ�
	 * @return string
	 */
	public function transNum($totalPrice)
	{
		$number_arr = array('','��','��','��','��','��','��','ĥ','��','��');

		// õ�ڸ� ���� �ڸ� ���� �ѱ� �迭
		$unit_arr1 = array('','��','��','õ');

		// ���ڸ� �̻� �ڸ� ���� �ѱ� �迭
		$unit_arr2 = array('','��','��','��','��','��');

		// ���ڰ��� �������� �迭�� ��, 4�ڸ� �������� ����
		$reverse_arr = str_split(strrev($totalPrice), 4);

		$result = array();	// ����� �迭 ����
		$result_idx = 0;	// ���ڸ� ���� ���� ����

		foreach ($reverse_arr as $reverse_idx=>$reverse_number) {
			// 1�ڸ��� ����
			$convert_arr = str_split($reverse_number);
			$convert_idx = 0;

			foreach ($convert_arr as $split_idx=>$split_number) {
				// �ش� ���ڰ� 0�� ��� ó������ ����
				if (!empty($number_arr[$split_number])) {
					// 0���� 9���� ���� �ѱ۷� ���� �׸��� õ�ڸ� ���� ���� ����
					$result[$result_idx] = $number_arr[$split_number].$unit_arr1[$split_idx];

					// ���ڸ� �̻� ���� ����
					if (empty($convert_idx)) $result[$result_idx] .= $unit_arr2[$reverse_idx];
					$convert_idx++;
				}
				$result_idx++;
			}
		}

		// �迭 �������� ������ �� ��ħ
		$result = implode('', array_reverse($result));
		return $result;
	}
	
	/**
	 * ����� Name �������� �Լ�
	 * @return string
	 */
	public function getName($m_no)
	{
		$query = " select * from ".GD_MEMBER." a left join ".GD_MEMBER_GRP." b on a.level=b.level where m_no='$m_no'";
		$member = $this->_db->fetch($query,1);
		return $member['name'];
	}

	/**
	 * üũ �� ��ǰ ������
	 * @return array
	 */
	public function getGoods($item,$idxs)
	{
		$count = 1;
		$goods = array();	// ��ǰ�� �����ĵ� �迭
		foreach ($idxs as $value) {
			$item[$value]['idxs'] = $count;
			$goods[$count] = $item[$value];
			$count++;
		}

		return $goods;
	}

	/**
	 * ���� ���� �������� �Լ�
	 * @return array
	 */
	public function getTax($item)
	{
		$tax = '';
		$goodsno = '';

		for ($i=1; $i<=count($item); $i++) {
			$goodsno = $item[$i]['goodsno'];
			$query = "select tax from ".GD_GOODS." where goodsno='$goodsno'";
			list($item[$i]['tax']) = $this->_db->fetch($query);	// �������θ� item �迭�� �߰�
		}

		return $item;
	}

	/**
	 * �հ� �ݾ� ���
	 * @return int
	 */
	public function totalPrice($item)
	{
		$totalPrice = 0;	// �հ�ݾ�
		foreach ($item as $goods) {
			$totalPrice += $goods['price']*$goods['ea'];	// ��ǰ ���� �ջ�
			if ($goods['addprice']) $totalPrice += $goods['addprice']*$goods['ea'];	// �ɼ� �߰� ���� �ջ�
		}

		return $totalPrice;
	}

	/**
	 * ���ް��� ���
	 * @return array
	 */
	public function supplyPrice($item)
	{
		for ($i=1; $i<=count($item); $i++) {
			$supplyPrice = 0;	// ���ް���
			$supplyPrice += $item[$i]['price']*$item[$i]['ea'];	// ��ǰ ���� �ջ�
			if ($item[$i]['addprice']) $supplyPrice += $item[$i]['addprice']*$item[$i]['ea'];	// �ɼ� �߰� ���� �ջ�
			if ($item[$i]['tax'] === '1') $supplyPrice = ($supplyPrice/1.1);	// ���� ��ǰ �ΰ��� ����
			$item[$i]['supply'] = ceil($supplyPrice);
		}

		return $item;
	}

	/**
	 * ���ް��� �ջ�
	 * @return string
	 */
	public function totalSupplyPrice($item)
	{
		$totalSupplyPrice = 0;
		foreach ($item as $goods) {
			$totalSupplyPrice += $goods['supply'];
		}

		return $totalSupplyPrice;
	}

	/**
	 * HTML �±�����
	 * @return array
	 */
	public function tagStrip($item)
	{
		for ($i=1; $i<=count($item); $i++) {
			$item[$i]['goodsnm'] = strip_tags($item[$i]['goodsnm']);
		}

		return $item;
	}


}
?>
