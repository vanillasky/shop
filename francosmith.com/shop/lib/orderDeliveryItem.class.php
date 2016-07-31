<?php
class orderDeliveryItem {
	var $total_delivery_price;
	var $ordno;

	function orderDeliveryItem($ordno = null)
	{
		$this->ordno = $ordno;
		$this->db = $GLOBALS['db'];
	}

	function extra_delivery($ordno, $extra_price, $prn_extra_price)
	{
		$areaDeliveryData						= array();
		$areaDeliveryData['ordno']				= $ordno;
		$areaDeliveryData['delivery_price']		= $extra_price;
		$areaDeliveryData['prn_delivery_price']	= $prn_extra_price;
		$areaDeliveryData['delivery_type']		= 100;

		$area = &load_class('areaDelivery','areaDelivery');
		$extra_fee = $area->getPay();

		if($extra_fee > 0) $areaDeliveryData['conditional_price']		= $extra_fee;

		$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $areaDeliveryData);
		$res = $this->db->_query($query);

		$db_idx = $this->db->_last_insert_id();
		$this->delivery_data_log('a', $areaDeliveryData['prn_delivery_price'], $db_idx);
		return $db_idx;
	}

	/*
	*	�ֹ���ǰ�� ��ۺ� ���� �з�
		$data = shop/lib/lib.func.php	getDeliveryMode()
		set_delivery_data($data)
			Array
			(
				[price] => �� ��ۺ�
				[extra_price] => 0
				[free] => �⺻��� �������ǰ�
				[default_price] => �⺻��� ��ۺ�ݾ�
				[order_delivery_item] => Array
					(
						[���Ÿ���ڵ�] => Array
							(
								[��ǰ��ȣ] => ��ۺ�ݾ�
							)
						[���Ÿ���ڵ�] => Array //�������� ���
							(
								[��ǰ��ȣ][�ɼ��ڵ�] => ��ۺ�ݾ�
							)
					)
			)
	*/
	function set_delivery_data($data)
	{
		$set_data = $data['order_delivery_item'];
		$this->total_delivery_price = $data['price'];
		if(is_array($set_data) === false || empty($set_data)) return false;

		//������ �߰� ��ۺ�
		if($data['extra_price'] > 0){
			$area_idx = $this->extra_delivery($this->ordno, $data['extra_price'], $data['extra_price']);
			$rtn['area_idx'] = $area_idx;
		}

		foreach($set_data as $delivery_type => $delivery_data) {
			if($delivery_type == 0) {
				foreach($delivery_data as $goods_no => $basic_price) {
					if(!isset($db_idx)) {
					$ins_data['ordno'] = $this->ordno;
					$ins_data['delivery_price'] = $basic_price;
					$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
					$ins_data['delivery_type'] = $delivery_type;
					$ins_data['conditional_price'] = $data['free'];

					$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);
					$res = $this->db->_query($query);
					$db_idx = $this->db->_last_insert_id();

					$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);
					}

					$rtn[$goods_no] = $db_idx;
				}
			}
			else {
				foreach($delivery_data as $goods_no => $delivery_price) {
					if($delivery_type == 1) {
						if(is_array($delivery_price)) {
							foreach($delivery_price as $optno => $free_price) {
								$ins_data['ordno'] = $this->ordno;
								$ins_data['delivery_price'] = $free_price;
								$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
								$ins_data['delivery_type'] = $delivery_type;

								$goods_query = $this->db->_query_print('SELECT goods_delivery FROM '.GD_GOODS.' WHERE goodsno=[s]', $goods_no);
								$goods_res = $this->db->fetch($goods_query);

								$ins_data['conditional_price'] = $goods_res['goods_delivery'];

								$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);

								$res = $this->db->_query($query);
								$db_idx = $this->db->_last_insert_id();

								$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);

								$rtn[$goods_no][$optno] = $db_idx;
								unset($ins_data, $db_idx);
							}
						}
						else {
							$ins_data['ordno'] = $this->ordno;
							$ins_data['delivery_price'] = $delivery_price;
							$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
							$ins_data['delivery_type'] = $delivery_type;

							$goods_query = $this->db->_query_print('SELECT goods_delivery FROM '.GD_GOODS.' WHERE goodsno=[s]', $goods_no);
							$goods_res = $this->db->fetch($goods_query);

							$ins_data['conditional_price'] = $goods_res['goods_delivery'];

							$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);

							$res = $this->db->_query($query);
							$db_idx = $this->db->_last_insert_id();

							$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);

							$rtn[$goods_no] = $db_idx;
							unset($ins_data, $db_idx);
						}
					}
					else {
						$ins_data['ordno'] = $this->ordno;
						$ins_data['delivery_price'] = $delivery_price;
						$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
						$ins_data['delivery_type'] = $delivery_type;

						$goods_query = $this->db->_query_print('SELECT goods_delivery FROM '.GD_GOODS.' WHERE goodsno=[s]', $goods_no);
						$goods_res = $this->db->fetch($goods_query);

						$ins_data['conditional_price'] = $goods_res['goods_delivery'];

						$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);

						$res = $this->db->_query($query);
						$db_idx = $this->db->_last_insert_id();

						$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);

						$rtn[$goods_no] = $db_idx;
					}
				}
			}
			unset($ins_data, $db_idx);
		}

		return $rtn;
	}

	function delivery_data_log($log_type, $delivery_price, $db_idx)
	{
		$log_data['oi_delivery_idx'] = $db_idx;
		$log_data['delivery_price'] = $delivery_price;
		$log_data['log_type'] = $log_type;
		$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY_LOG.' SET [cv], change_date=now()', $log_data);
		$this->db->_query($query);
	}

	/* ��ҷ� ��ۺ� ȯ�ҳ��� ���� */
	function update_delivery_data($arr_data)
	{
		if(!empty($arr_data)) {
			foreach($arr_data as $sno => $price) {
				$check = $this->db->fetch('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE oi_delivery_idx='.$sno, true);
				if($check['prn_delivery_price'] >= $price) {
					$query = $this->db->_query_print('UPDATE '.GD_ORDER_ITEM_DELIVERY.' SET prn_delivery_price = (prn_delivery_price - [i]) WHERE oi_delivery_idx=[i]', $price, $sno);
					$this->db->_query($query);

					$this->delivery_data_log('m', $price, $sno);
				}
				else {
					$this->delivery_data_log('e', $price, $sno);
				}
			}
		}
	}

	function getDeliveryConf()
	{
		if(empty($this->delivery_set)) {
			$query = $this->db->_query_print('SELECT add_extra_fee_duplicate_free, add_extra_fee_duplicate_fixEach, add_extra_fee_duplicate_each, freeDelivery, goodsDelivery from '.GD_ORDER.' WHERE ordno=[i]', $this->ordno);
			$this->delivery_set = $this->db->fetch($query);
		}
	}

	/*
	 * �⺻��ۺ� ��ü ��ҵǴ��� üũ
	 * ��ҿ� �ش�Ǵ� ������ۺ� ��ǰ�� ��� ��ҵǴ��� üũ
	*/
	function checkAllCancel($sno, $item, $delivery_type)
	{
		//���Ÿ�� ��ü ��ۺ� ��ȸ
		if(($delivery_type == '0') || ($delivery_type == '4' && $this->delivery_set['add_extra_fee_duplicate_fixEach'] == '0') || ($delivery_type == '1')) {
			$delivery_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno=[i] AND delivery_type=[i]', $item['ordno'], $delivery_type);//�⺻��ۺ�
		}
		else $delivery_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno=[i] AND delivery_type=[i] AND oi_delivery_idx=[i]', $item['ordno'], $delivery_type, $item['oi_delivery_idx']);//������ۺ�
		$delivery_result = $this->db->_select($delivery_query, true);

		$rtn_bool = false;// true=��ۺ���, false=��ۺ��� ����

		foreach($delivery_result as $dres) {
			// ���idx �� ���ԵǴ� item��ȸ
			$item_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND oi_delivery_idx=[i] AND (cancel=0 OR NOT cancel=[i])', $item['ordno'], $dres['oi_delivery_idx'], $sno);
			$item_result = $this->db->_select($item_query, true);

			if(!empty($item_result)) {
				foreach($item_result as $ires) {
					// ��һ��°� �ƴ� �ֹ��� �ִ� ��� false
					if($ires['cancel'] == 0) {
						return false;
						break;
					}
					else {
						$cancel_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_CANCEL.' WHERE sno=[i] AND pgcancel=[s]', $ires['cancel'], 'n');
						$cancel_result = $this->db->fetch($cancel_query, true);
						if(!empty($cancel_result)) return false;//��һ��� ������ ���� pg��Ұ� �ƴ� ��� false
						else {
							$rtn_bool = true;
						}
					}
				}
			}
			else $rtn_bool = true;
		}

		if($rtn_bool === true) {
			$rtn_bool = $this->DeliveryAllCancelCheck($item['sno'], $item['ordno'], $item['oi_delivery_idx']);
		}

		return $rtn_bool;
	}

	/*
	 * �⺻���/��������� �κ���ҷ� ���� ��ҿϷ�� ���¿���
	 * ������� ��ȸ(�ֹ���������)�� �ߺ������ ������
	*/
	function DeliveryAllCancelCheck($sno, $ordno, $oi_delivery_idx)
	{
		$rtn_bool = true;

		$item_query = $this->db->_query_print('SELECT i.sno, i.cancel, i.istep FROM '.GD_ORDER_ITEM.' i LEFT JOIN '.GD_ORDER_CANCEL.' c ON i.cancel=c.sno WHERE i.ordno=[i] AND i.oi_delivery_idx=[i] ORDER BY ccdt DESC', $ordno, $oi_delivery_idx);
		$item_result = $this->db->_select($item_query, true);
		$item_sno = 0;

		foreach($item_result as $item_data) {

			if($item_data['istep'] === '44') {
				if($item_sno < 1) {
					$item_sno = $item_data['sno'];
					$cancel_complet = true;
				}
			}
			else {
				$cancel_complet = false;
				break;
			}
		}

		// ������ item ��ȣ�� �ٸ� ��� false
		if($sno != $item_sno && $cancel_complet === true) $rtn_bool = false;
		return $rtn_bool;
	}

	function allItemCancel($sno, $item)
	{
		$item_bool = false;

		$item_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND goodsno=[i] AND opt1=[s] AND opt2=[s] AND NOT cancel=[i]', $item['ordno'], $item['goodsno'], $item['opt1'], $item['opt2'], $sno);
		$item_result = $this->db->_select($item_query, true);

		if(empty($item_result)) return true;

		foreach($item_result as $ires) {
			if($ires['cancel'] == 0) {
				return false;
			}
			else {
				$cancel_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_CANCEL.' WHERE sno=[i] AND pgcancel=[s]', $ires['cancel'], 'n');
				$cancel_result = $this->db->fetch($cancel_query, true);
				if(!empty($cancel_result)) return false;//��һ��� ������ ���� pg��Ұ� �ƴ� ��� false
				else $item_bool = true;
			}
		}

		return $item_bool;
	}

	/* ������ۺ� - ��ǰ�� ��ü ��ҵǾ����� üũ */
	function allFixDeliveryCancel($cancel_sno, $item, $goodsno)
	{
		$fix_bool = false;

		$fix_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND goodsno=[i] AND NOT cancel=[i]', $item['ordno'], $goodsno, $cancel_sno);
		$fix_result = $this->db->_select($fix_query, true);
		if(empty($fix_result)) return true;

		foreach($fix_result as $fres) {
			if($fres['cancel'] == 0) {
				return false;
			}
			else {
				$cancel_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_CANCEL.' WHERE sno=[i] AND pgcancel=[s]', $fres['cancel'], 'n');
				$cancel_result = $this->db->fetch($cancel_query, true);
				if(!empty($cancel_result)) return false;//��һ��� ������ ���� pg��Ұ� �ƴ� ��� false
				else {
					$fix_bool = true;
				}
			}
		}

		/*
		 * ��ҿϷ�� �ֹ� ���� ��ȸ�� ������ۺ� �ߺ����Ǵ� ��찡 �־� ����ó����.
		*/
		if($fix_bool === true) {
			$fix_bool = $this->DeliveryAllCancelCheck($item['sno'], $item['ordno'], $item['oi_delivery_idx']);
		}
		return $fix_bool;
	}

	/* ������ ��ۺ� ��ȸ */
	function area_delivery_search($area_idx)
	{
		$area_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE oi_delivery_idx=[i] AND delivery_type=100', $area_idx);
		return $this->db->fetch($area_query, true);
	}

	function cancel_delivery($sno)
	{
		// ���ó���� �ֹ���ȸ
		$order_query = "select * from ".GD_ORDER_ITEM." where cancel='".$sno."' AND (istep=41 OR istep=42)";
		$order_res = $this->db->_select($order_query);

		return $this->get_cancel_delivery_data($sno, $order_res);
	}

	/* �ֹ��� ��ҽ�(�������) ��ұݾ� �� ���� ��ۺ�(������ ��ۺ� ����) ������ ����
	 * return Array
	 * total_cancel_price			int		�� ��ұݾ�
	 * cancel_delivery_price		int		��� ��ۺ�
	 * total_cancel_delivery_price	int		�� ��� ��ۺ�(��� ��ۺ� + ������ ��ۺ�)
	 * item							Array
	 *		sno						int		item��ȣ
	 *		oi_delivery_idx			int		���item ��ȣ(������ ��ۺ��� ��� dv_�ֹ���ȣ)
	 *		productAmt				int		��ұݾ�
	 *		productUnitPrice		int		���ܰ��ݾ�
	 *		ea						int		����
	 *		delivery				bool	������ ����Ǿ� �ְ� 1�� ��� ��ۺ�item
	 * delivery						Array
	 *		[��ۺ� item ��ȣ]	=>	��ұݾ�
	 * msg							Array
	*/
	function get_cancel_delivery($ordno)
	{
		$return_data['total_cancel_goods_price'] = 0;
		$return_data['total_cancel_delivery_price'] = 0;

		//������ ���ó���� ��ҹ�ȣ ��ȸ
		$order_cancel_query = $this->db->_query_print("SELECT sno FROM ".GD_ORDER_CANCEL." WHERE ordno=[i] AND pgcancel IN ([s],[s])", $ordno, 'y', 'r');
		$cancel_res = $this->db->query($order_cancel_query, true);

		//��ۺ� ���� �⺻��ۺ� ��ǰ�� �κ�����ϴ� ��� ��ۺ� �ߺ����Ǵ� ��찡 �־� ����ó����
		$basic_query = $this->db->_query_print("SELECT oi_delivery_idx FROM ".GD_ORDER_ITEM_DELIVERY." WHERE ordno=[i] and delivery_type=[i]", $ordno, '0');
		list($basic_sno) = $this->db->fetch($basic_query);
		$basic_delivery_price = 0;//��ҵ� ��ۺ� �ݾ׿� �⺻��ۺ� ���Կ���
		$basic_delivery = false;//üũ�ϴ� ��Ұǿ� �⺻��ۺ� ���Կ���

		while($data = $this->db->fetch($cancel_res)) {
			$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i]", $data['sno']);
			$arr_item_data = $this->db->_select($item_query);
			$arr_cancel_delivery = $this->get_cancel_delivery_data($data['sno'], $arr_item_data);

			foreach($arr_cancel_delivery['delivery'] as $delivery_idx => $delivery_price) {
				if($basic_sno == $delivery_idx) {
					if($basic_delivery !== true) {
						$basic_delivery_price += $delivery_price;
						$basic_delivery = true;
					}
				}
			}
			$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//��ҵ� ��ǰ �ݾ�
			$return_data['total_cancel_delivery_price'] += $arr_cancel_delivery['total_cancel_delivery_price'];//��ҵ� ��ۺ� �ݾ�
		}

		$return_data['total_cancel_delivery_price'] -= $basic_delivery_price;

		return $return_data;
	}

	function get_cancel_delivery_data($sno, $order_res)
	{
		// ��ۺ� ���� ������ �ε�
		$this->getDeliveryConf();

		$total_cancel_price = 0;
		$total_cancel_delivery_price = 0;
		$cancel_goods_price = 0;
		$total_cancel_area_delivery = 0;
		$sum_area_idxs = array();
		$view_data = Array();

		$fix_area_bool = Array();//������ۺ��� ������ ��ۺ� �ߺ����� ������
		$basic_area_bool = false;//�⺻��ۺ��� ������ ��ۺ� �ߺ����� ������
		$basic_bool = false;

		foreach($order_res as $item) {
			$cancel_delivery_price = 0;

			// �Ϻ� ������ ����ϴ� ��� �ֹ���ǰ��ȣ�� �޶��� �� �ֹ���ǰ��ȣ�� ��ȸ��.
			$sno_query = "select sno from ".GD_ORDER_ITEM." WHERE addopt='".$item['addopt']."' AND opt1='".$item['opt1']."' AND opt2='".$item['opt2']."' AND goodsno='".$item['goodsno']."' AND oi_delivery_idx='".$item['oi_delivery_idx']."' AND cancel < 1 AND ordno=".$item['ordno']." order by sno desc";
			$sno_res = $this->db->fetch($sno_query);


			if(!empty($sno_res['sno']) && ($item['sno']) != $sno_res['sno']) {
				$tmp_item['sno'] = $sno_res['sno'];//�ֹ���ǰ��ȣ
			}
			else $tmp_item['sno'] = $item['sno'];//�ֹ���ǰ��ȣ

			$tmp_item['productAmt'] = ($item['price'] - $item['memberdc'] - $item['coupon'] - $item['oi_special_discount_amount']) * $item['ea'];//�� �ݾ�
			$tmp_item['productUnitPrice'] = $item['price'];//��ǰ�ܰ�
			$tmp_item['ea'] = $item['ea'];//����

			$items[] = $tmp_item;
			unset($tmp_item);

			$total_cancel_price += ($item['price'] - $item['memberdc'] - $item['coupon'] - $item['oi_special_discount_amount']) * $item['ea'];//�� ��ұݾ�

			$cancel_goods_price += $item['price'] * $item['ea'];//��һ�ǰ�ݾ�

			$area_delivery = 0;//������ ��ۺ� �ݾ�

			$delivery_query = "select delivery_price, prn_delivery_price, delivery_type, conditional_price from ".GD_ORDER_ITEM_DELIVERY." WHERE oi_delivery_idx='".$item['oi_delivery_idx']."'";
			$delivery_res = $this->db->fetch($delivery_query);


			// ��� item ��ۺ� �� ������ ��ۺ� ���
/*
				 * 0 : �⺻ ���
				 * 1 : ������
				 * 2 : ��ǰ�� ��ۺ� (���̻� ������� ����)
				 * 3 : ���ҹ�ۺ�
				 * 4 : ���� ��ۺ�
				 * 5 : ������ ��ۺ�
*/

			switch($delivery_res['delivery_type']) {
				case '0' : //�⺻��ۺ�
					$view_data[0]['cnt'] += 1;

					if($this->checkAllCancel($sno, $item, $delivery_res['delivery_type']) === true) {
						//������ ��ۺ� ��ȸ
						$area_result = $this->area_delivery_search($item['oi_area_idx']);

						if(!empty($area_result) && $basic_area_bool === false) {

							$area_delivery = $area_result['conditional_price'];
							$total_cancel_area_delivery += $area_delivery;
							$deliverys[$item['oi_area_idx']] += $area_delivery;//��ۺ� ���̺� ������

							$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
							$tmp_area_item['productAmt'] = $area_delivery;
							$tmp_area_item['productUnitPrice'] = $area_delivery;
							$tmp_area_item['delivery'] = true;

							if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

							$arr_area_item[] = $tmp_area_item;
							unset($tmp_area_item);
							$basic_area_bool = true;

							$view_data[0]['area_delivery_price'] = $area_delivery;
						}

						if($basic_bool !== true) {
							$cancel_delivery_price = $delivery_res['delivery_price'];
							$total_cancel_delivery_price += $cancel_delivery_price;

							$tmp_delivery_item['oi_delivery_idx'] = $item['oi_delivery_idx'];//delivery_idx
							$tmp_delivery_item['productAmt'] = $cancel_delivery_price;//��ۺ�
							$tmp_delivery_item['productUnitPrice'] = $cancel_delivery_price;//��ۺ�
							$tmp_delivery_item['delivery'] = true;

							if($delivery_res['prn_delivery_price'] == $tmp_delivery_item['productAmt']) $tmp_delivery_item['ea'] = '1';

							$deliverys[$tmp_delivery_item['oi_delivery_idx']] += $cancel_delivery_price;//��ۺ� ���̺� ������

							$items[] = $tmp_delivery_item;
							unset($tmp_delivery_item);

							$basic_bool = true;
							$view_data[0]['delivery_price'] = $cancel_delivery_price;
						}
					}
					else if(count($view_data[0]) < 2) $view_data[0]['delivery_price'] = 0;

					//��ۺ���ұݾ�
					break;

				case '1' : //�����ۺ�
					//������ ��ۺ�
					if(!isset($this->delivery_free_type)) $this->delivery_free_type = false;
					$area_result = $this->area_delivery_search($item['oi_area_idx']);
					$view_data[1]['cnt'] += 1;

					if($this->delivery_set['add_extra_fee_duplicate_free'] == '1' && $this->allItemCancel($sno, $item) === true) {
						if(!empty($area_result)) {
							$area_delivery = $area_result['conditional_price'];
							$total_cancel_area_delivery += $area_delivery;
							$deliverys[$item['oi_area_idx']] += $area_delivery;//��ۺ� ���̺� ������

							$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
							$tmp_area_item['productAmt'] = $area_delivery;
							$tmp_area_item['productUnitPrice'] = $area_delivery;
							$tmp_area_item['delivery'] = true;

							if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

							$arr_area_item[] = $tmp_area_item;
							unset($tmp_area_item);

							$view_data[1]['area_delivery_price'] += $area_delivery;
						}
					}
					else if($this->delivery_set['add_extra_fee_duplicate_free'] != '1' && $this->delivery_free_type === false && $this->checkAllCancel($sno, $item, $delivery_res['delivery_type']) === true) {
						$this->delivery_free_type = true;
						if(!empty($area_result)) {
							$area_delivery = $area_result['conditional_price'];
							$total_cancel_area_delivery += $area_delivery;
							$deliverys[$item['oi_area_idx']] += $area_delivery;//��ۺ� ���̺� ������

							$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
							$tmp_area_item['productAmt'] = $area_delivery;
							$tmp_area_item['productUnitPrice'] = $area_delivery;
							$tmp_area_item['delivery'] = true;

							if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

							$arr_area_item[] = $tmp_area_item;
							unset($tmp_area_item);

							$view_data[1]['area_delivery_price'] = $area_delivery;
						}
					}
					break;

				case '3' : //���ҹ�ۺ�
					//������ ��ۺ�
					//��ۺ���ұݾ�
					$view_data[3][$item['goodsno']]['cnt'] += 1;
					$view_data[3][$item['goodsno']]['area_delivery_price'] = 0;
					$view_data[3][$item['goodsno']]['delivery_price'] = 0;
					break;

				case '4' : //������ۺ�
					$view_data[4][$item['goodsno']]['cnt'] += 1;

					//������ ��ۺ�
					//��� ������ۺ� ���/�κ���� �Ǵ� ��� ������ ��ۺ� ȯ��
					if($this->checkAllCancel($sno, $item, $delivery_res['delivery_type']) === true) {
						$area_result = $this->area_delivery_search($item['oi_area_idx']);

						if($this->delivery_set['add_extra_fee_duplicate_fixEach'] == '1') {
							// ������ۺ� ��ǰ�� ������ ��ۺ� ���� �ΰ�
							if(!empty($area_result) && $fix_area_bool[$item['goodsno']] !== true) {

								$area_delivery = $area_result['conditional_price'];
								$total_cancel_area_delivery += $area_delivery;
								$deliverys[$item['oi_area_idx']] += $area_delivery;//��ۺ� ���̺� ������

								$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
								$tmp_area_item['productAmt'] = $area_delivery;
								$tmp_area_item['productUnitPrice'] = $area_delivery;
								$tmp_area_item['delivery'] = true;

								if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

								$arr_area_item[] = $tmp_area_item;
								unset($tmp_area_item);
								$fix_area_bool[$item['goodsno']] = true;

								$view_data[4][$item['goodsno']]['area_delivery_price'] += $area_delivery;
							}
						}
						else {
							// ������ۺ� ��ǰ�� ������ ��ۺ� 1ȸ �ΰ�
							if(!empty($area_result) && $fix_area_bool[0] !== true) {

								$area_delivery = $area_result['conditional_price'];
								$total_cancel_area_delivery += $area_delivery;
								$deliverys[$item['oi_area_idx']] += $area_delivery;//��ۺ� ���̺� ������

								$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
								$tmp_area_item['productAmt'] = $area_delivery;
								$tmp_area_item['productUnitPrice'] = $area_delivery;
								$tmp_area_item['delivery'] = true;

								if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

								$arr_area_item[] = $tmp_area_item;
								unset($tmp_area_item);
								$fix_area_bool[0] = true;

								$view_data[4][$item['goodsno']]['area_delivery_price'] = $area_delivery;
							}
						}
					}

					//������ۺ� ���
					if($this->allFixDeliveryCancel($sno, $item, $item['goodsno']) === true) {

						if($deliverys[$item['oi_delivery_idx']] < 1) {
							if($delivery_res['delivery_price'] < 1) {//���� ������ۺ� 0���� ��� 0������ ��ȯ
								$view_data[4][$item['goodsno']]['delivery_price'] = '0';
								break;
							}

							$cancel_delivery_price = $delivery_res['conditional_price'];
							$total_cancel_delivery_price += $cancel_delivery_price;

							$tmp_delivery_item['oi_delivery_idx'] = $item['oi_delivery_idx'];//delivery_idx
							$tmp_delivery_item['productAmt'] = $cancel_delivery_price;//��ۺ�
							$tmp_delivery_item['productUnitPrice'] = $cancel_delivery_price;//��ۺ�
							$tmp_delivery_item['delivery'] = true;

							if($delivery_res['prn_delivery_price'] == $tmp_delivery_item['productAmt']) $tmp_delivery_item['ea'] = '1';

							$deliverys[$tmp_delivery_item['oi_delivery_idx']] += $cancel_delivery_price;//��ۺ� ���̺� ������

							$items[] = $tmp_delivery_item;
							unset($tmp_delivery_item);

							$view_data[4][$item['goodsno']]['delivery_price'] = $cancel_delivery_price;
						}
					}

					if(count($view_data[4][$item['goodsno']]) < 2) $view_data[4][$item['goodsno']]['area_delivery_price'] = 0;

					break;

				case '5' : //��������ۺ�
					//������ ��ۺ�
					$area_result = $this->area_delivery_search($item['oi_area_idx']);

					if(!empty($area_result)) {
						$area_delivery = $item['ea'] * $area_result['conditional_price'];
						$total_cancel_area_delivery += $area_delivery;
						$deliverys[$item['oi_area_idx']] += $area_delivery;//��ۺ� ���̺� ������

						$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
						$tmp_area_item['productAmt'] = $area_delivery;
						$tmp_area_item['productUnitPrice'] = $area_delivery;
						$tmp_area_item['delivery'] = true;

						if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

						$arr_area_item[] = $tmp_area_item;
						unset($tmp_area_item);

						$view_data[5][$item['optno']]['area_delivery_price'] = $area_delivery;
					}

					//������ ��ۺ� ���
					if($delivery_res['delivery_price'] < 1) {
						$view_data[5][$item['optno']]['delivery_price'] = '0';
						break;
					}

					$cancel_delivery_price = $delivery_res['conditional_price'] * $item['ea'];
					$total_cancel_delivery_price += $cancel_delivery_price;

					$tmp_delivery_item['oi_delivery_idx'] = $item['oi_delivery_idx'];//delivery_idx
					$tmp_delivery_item['productAmt'] = $cancel_delivery_price;//��ۺ�
					$tmp_delivery_item['productUnitPrice'] = $cancel_delivery_price;//��ۺ�
					$tmp_delivery_item['delivery'] = true;

					if($delivery_res['prn_delivery_price'] == $tmp_delivery_item['productAmt']) $tmp_delivery_item['ea'] = '1';

					$deliverys[$tmp_delivery_item['oi_delivery_idx']] += $cancel_delivery_price;//��ۺ� ���̺� ������

					$items[] = $tmp_delivery_item;
					unset($tmp_delivery_item);

					$view_data[5][$item['optno']]['delivery_price'] = $cancel_delivery_price;

					break;
			}
		}

		if(!empty($arr_area_item)) {
			$tmp_area = Array();
			foreach($arr_area_item as $area_item) {

				$tmp_area['oi_delivery_idx'] = $area_item['oi_delivery_idx'];//dv_1426211557647
				$tmp_area['productAmt'] += $area_item['productAmt'];//��ۺ�
				$tmp_area['productUnitPrice'] += $area_item['productUnitPrice'];//��ۺ�
				$tmp_area['delivery'] = true;

				if(isset($area_item['ea'])) $tmp_area['ea'] = '1';
			}
			if(!empty($tmp_area)) $items[] = $tmp_area;
		}

		/*
		 * �ֹ��� �⺻��ۺ� �ִ� ��� ��ҽ� �������ǿ��� ��ۺ� �ΰ��� ����Ǵ��� Ȯ��
		*/
		$basic_check_query = $this->db->_query_print('SELECT d.delivery_price, d.prn_delivery_price, d.conditional_price, count(*) as cnt FROM '.GD_ORDER_ITEM.' i LEFT JOIN '.GD_ORDER_ITEM_DELIVERY.' d ON i.oi_delivery_idx=d.oi_delivery_idx WHERE i.cancel=0 AND d.ordno=[i] AND d.delivery_type=0 ', $order_res[0]['ordno']);
		$basic_res = $this->db->fetch($basic_check_query, true);

		if(($basic_res['cnt'] > 0) && ($basic_res['prn_delivery_price'] == 0) && ($basic_res['conditional_price'] > $basic_res['price'])) {
			$ord_price_query = $this->db->_query_print('SELECT sum(price * ea) as price FROM '.GD_ORDER_ITEM.' WHERE cancel=0 AND ordno=[i]', $order_res[0]['ordno']);
			$ord_price = $this->db->fetch($ord_price_query);

			$rtn['msg'][] = '- �ֹ���ҷ� ��ǰ �ֹ��ݾ�('.number_format($ord_price['price']).'��)�� �⺻��� ��������('.number_format($basic_res['conditional_price']).'��)�� �̴޵˴ϴ�.<br>Ȯ���ϼż� ȯ�� �����Ḧ ������ �ֽñ� �ٶ��ϴ�.';
		}

		$rtn['total_cancel_price'] = $total_cancel_price;
		$rtn['cancel_delivery_price'] = $total_cancel_delivery_price;
		$rtn['total_cancel_delivery_price'] = $total_cancel_delivery_price + $total_cancel_area_delivery;
		$rtn['item'] = $items;
		$rtn['delivery'] = $deliverys;//��ۺ� ���̺� ������

		if($total_cancel_area_delivery > 0) $rtn['msg'][] = '��ۺ� ������ ��ۺ� ['.number_format($total_cancel_area_delivery).'��] �� ���ԵǾ� �ֽ��ϴ�.';

		$rtn['coupon'] = $this->getCoupon();//�ֹ��� ������볻�� ��ȸ
		$rtn['emoney'] = $this->getEmoney();//�ֹ��� ������ ��ȸ
		$rtn['view'] = $view_data;

		return $rtn;
	}

	function getEmoney()
	{
		$amount = 0;
		$order_query = $this->db->_query_print("SELECT emoney from ".GD_ORDER." WHERE ordno=[i]", $this->ordno);
		list($amount) = $this->db->fetch($order_query);

		return $amount;
	}

	function getCoupon()
	{
		$amount['f'] = 0;
		$amount['m'] = 0;

		$order_query = $this->db->_query_print("SELECT coupon from ".GD_ORDER." WHERE ordno=[i]", $this->ordno);
		list($amount['m']) = $this->db->fetch($order_query);

		$item_query = $this->db->_query_print("SELECT coupon * ea as coupon from ".GD_ORDER_ITEM." WHERE ordno=[i]", $this->ordno);
		$item_data = $this->db->_select($item_query);
		
		foreach($item_data as $data) {
			$amount['f'] += $data['coupon'];
		}
		return $amount;
	}

	// ������ ��Ұ����� Ȯ��
	function checkLastCancel($sno)
	{
		$query = $this->db->_query_print("select istep, cancel from ".GD_ORDER_ITEM." WHERE ordno=[s]", $this->ordno);
		$order_data = $this->db->_select($query);

		foreach($order_data as $item) {
			if($item['istep'] == '44') $bool = true;
			else if($item['istep'] >= '40' && $item['istep'] <= '43') {
				if($sno != $item['cancel']) {
					$cancel_query = $this->db->_query_print("select pgcancel from ".GD_ORDER_CANCEL." WHERE sno=[s]", $item['cancel']);
					$cancel_data = $this->db->fetch($cancel_query);
					if($cancel_data['pgcancel'] == 'n') {
						return false;
					}
					else $bool = true;
				}
				else {
					$bool = true;
				}
			}
			else {
				return false;
			}
		}

		return $bool;
	}

	function getDeliveryType($oi_delivery_idx)
	{
		$query = $this->db->_query_print("SELECT delivery_type FROM ".GD_ORDER_ITEM_DELIVERY." WHERE oi_delivery_idx=[i]", $oi_delivery_idx);
		list($delivery_type) = $this->db->fetch($query);
		if($delivery_type === '0') return 'basic';
		else if($delivery_type === '4') return 'fix';
		else return 'etc';
	}

	function getCancelCompletDelivery($oi_delivery_idx)
	{
		switch($this->getDeliveryType($oi_delivery_idx)) {
			case 'basic' ://�⺻��ۺ�
			case 'fix' ://������ۺ�
				$query = $this->db->_query_print("SELECT istep FROM ".GD_ORDER_ITEM." WHERE oi_delivery_idx=[i]", $oi_delivery_idx);
				$res = $this->db->query($query);
				while($data = $this->db->fetch($res)) {
					if($data['istep'] !== '44') return false;
				}
			break;
		}
		return true;
	}

	/**
	 * ������������� ��ۺ�
	 *
	 * @return int ��ۺ�
	 */
	public function getCancelingDeliveryFee()
	{
		$return_data['total_cancel_goods_price'] = 0;
		$return_data['total_cancel_delivery_price'] = 0;

		//������ ���ó���� ��ҹ�ȣ ��ȸ
		$order_cancel_query = $this->db->_query_print("SELECT sno FROM ".GD_ORDER_CANCEL." WHERE ordno=[i]", $this->ordno);
		$cancel_res = $this->db->query($order_cancel_query, true);
		$arr_delivery_price = Array();

		while($data = $this->db->fetch($cancel_res)) {

			$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i] AND istep>40 AND istep<44 ", $data['sno']);
			$arr_item_data = $this->db->_select($item_query);

			if(!empty($arr_item_data)) {
				$arr_cancel_delivery = $this->get_cancel_delivery_data($data['sno'], $arr_item_data);

				if(!empty($arr_cancel_delivery['delivery'])) {

					//���Ÿ�Ժ� ���
					foreach($arr_cancel_delivery['delivery'] as $oi_delivery_idx => $delivery_price) {
						switch($this->getDeliveryType($oi_delivery_idx)) {
							case 'basic' ://�⺻��ۺ�
							case 'fix' ://������ۺ�
								$arr_delivery_price[$oi_delivery_idx] = $delivery_price;
								break;
							default :
								$arr_delivery_price[$oi_delivery_idx] += $delivery_price;
								break;
						}
					}
				}

				$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//��ҵ� ��ǰ �ݾ�
				$return_data['total_cancel_delivery_price'] += $arr_cancel_delivery['total_cancel_delivery_price'];//��ҵ� ��ۺ� �ݾ�
			}
		}

		if(isset($arr_delivery_price)) $return_data['total_cancel_delivery_price'] = array_sum($arr_delivery_price);

		return $return_data;
	}

	/**
	 * ��ҿϷ������ ��ۺ�
	 *
	 * @return int ��ۺ�
	 */
	public function getCancelCompletedDeliveryFee()
	{
		$return_data['total_cancel_goods_price'] = 0;
		$return_data['total_cancel_delivery_price'] = 0;

		//������ ���ó���� ��ҹ�ȣ ��ȸ
		$order_cancel_query = $this->db->_query_print("SELECT sno FROM ".GD_ORDER_CANCEL." WHERE ordno=[i]", $this->ordno);
		$cancel_res = $this->db->query($order_cancel_query, true);

		while($data = $this->db->fetch($cancel_res)) {
			$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i] AND istep=44 ", $data['sno']);
			$arr_item_data = $this->db->_select($item_query);

			if(!empty($arr_item_data)) {
				$arr_cancel_delivery = $this->get_cancel_delivery_data($data['sno'], $arr_item_data);

				if(!empty($arr_cancel_delivery['delivery'])) {
					$arr_delivery_price = Array();


					$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//��ҵ� ��ǰ �ݾ�

					//��ҿϷ� ������ ��ۺ� ���
					foreach($arr_cancel_delivery['delivery'] as $oi_delivery_idx => $delivery_price) {
						if($this->getCancelCompletDelivery($oi_delivery_idx) === true) {
							$return_data['total_cancel_delivery_price'] += $delivery_price;//��ҵ� ��ۺ� �ݾ�
						}
					}
				}
			}
		}

		return $return_data;
	}

	/**
	  * ���SNO ������ ȯ�� ��ۺ�
	  *
	  * @return int ��ۺ�
	  */
	public function getCancelCompletedDeliverFeeWithSno($sno, $view = false) {
		$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i] AND istep=44 ", $sno);
		$arr_item_data = $this->db->_select($item_query);
		if(!empty($arr_item_data)) {
			$arr_cancel_delivery = $this->get_cancel_delivery_data($sno, $arr_item_data);
			if(!empty($arr_cancel_delivery['delivery'])) {
				$arr_delivery_price = Array();

				$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//��ҵ� ��ǰ �ݾ�
				//��ҿϷ� ������ ��ۺ� ���
				foreach($arr_cancel_delivery['delivery'] as $oi_delivery_idx => $delivery_price) {
					if($this->getCancelCompletDelivery($oi_delivery_idx) === true) {
						$return_data['total_cancel_delivery_price'] += $delivery_price;//��ҵ� ��ۺ� �ݾ�
					}
				}
			}
			if($view === true) {
				$return_data['view'] = $arr_cancel_delivery['view'];
			}
		}
		return $return_data;
	}
}