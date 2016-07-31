<?

require_once dirname(__FILE__)."/sAPI.class.php";

class integrate_order_processor_selly extends integrate_order_processor {

	function extractData($var = null) {
		
		// �ֹ�������
		$query = "
			INSERT
			INTO ".$this->temp_table_order."
			(
				channel,ordno,old_ordno,m_no,m_id_out,ord_name,ord_email,ord_phone,ord_mobile,rcv_name,rcv_phone,rcv_mobile,rcv_zipcode,rcv_address,pay_amount,ord_amount,dis_amount,res_amount,dlv_amount,dlv_type,dlv_company,dlv_no,dlv_message,ord_date,dlv_date,pay_date,fin_date,pay_bank_name,pay_bank_account,pay_method,dlv_method,flg_escrow,flg_egg,flg_cashbag,flg_inflow,ori_status,sub_channel,
				reg_date,mod_date
			)

			SELECT
				'selly',B.order_no,'','',B.order_id,B.order_nm,'',B.order_tel,B.order_cel,B.receive_nm,B.receive_tel,B.receive_cel,B.receive_zip,B.receive_addr,B.settle_price,B.order_price,'',B.settle_price,B.settle_delivery_price,B.delivery_type_order,B.delivery_cd,B.delivery_no,B.delivery_msg,IF(B.order_date,B.order_date,B.reg_date),B.delivery_date,B.reg_date,B.delivery_end_date,'','',B.settle_type,B.delivery_st,'','','','',B.status,B.mall_cd,
				NOW(), null

			FROM ".GD_MARKET_ORDER." AS B

			WHERE
				B.sync_ = 0 AND B.order_no IS NOT NULL
		";

		$this->db->query($query);

		if ($this->db->affected() < 1)
			return false;

		// �ֹ� ��ǰ ����
		$query = "
			INSERT
			INTO ".$this->temp_table_item."
			(
				channel,ordno,goodsnm,goodsno,`option`,ea,price,
				cs
			)
			SELECT
				'selly', B.ordno,C.goodsnm,C.goodsno,CONCAT(C.opt1,' / ', C.opt2, ' / ', C.addopt),C.ea,C.price,
				IF (A.status = 0021, 'y' ,
				IF (A.status = 0031, 'y' ,
				IF (A.status = 0022, 'f' ,
				IF (A.status = 0032, 'f' , 'n'
				))))
				
			FROM ".$this->temp_table_order." AS B

			INNER JOIN ".GD_MARKET_ORDER." AS A
				ON B.ordno = A.order_no

			INNER JOIN ".GD_MARKET_ORDER_ITEM." AS C
				ON A.order_no = C.order_no
		";

		$this->db->query($query);

		// ���ڵ� �� ����
		$this->adjustData();

		return true;
	}

	function adjustData() {

		$_tmp = array(
						0	=> array(),				// �ֹ�����
						1	=> array('0010'),	// �Ա�Ȯ�� (�ݵ�� �������� �Է�)
						2	=> array('0020'),	// ����غ���
						3	=> array('0030'),		// �����
						4	=> array('0040','0050','0060'),	// ��ۿϷ�(�ǸſϷ�, �۱ݿϷ� ����)

						// ���
						10	=> array('0021'),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						11	=> array('0022'),	// �Ϸ�

						// ȯ��
						20	=> array(),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						21	=> array(),	// �Ϸ�

						// ��ǰ
						30	=> array('0031'),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						31	=> array('0032'),		// �Ϸ�

						// ��ȯ
						40	=> array('0041', '0042', '0043'),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						41	=> array('0044'),	// �Ϸ�

						// ��������
						50	=> array(),
						51	=> array(),
						54	=> array()
		);

		// �ֹ����� ����
		foreach ($_tmp as $_status => $_cond) {

			if (empty($_cond)) continue;

			$_cond = array_map(create_function('$var','return "\'".$var."\'";'), $_cond);

			$query = "
			UPDATE ".$this->temp_table_order." SET
				ord_status = $_status
			WHERE ori_status IN (".implode(',',$_cond).")
			";

			$this->db->query($query);
		}

		// ��Ÿ ��� ����
		$query = "
			UPDATE ".$this->temp_table_order." SET
				old_ordno = '',
				pay_method = IF(pay_method = '1','a',
							 IF(pay_method = '2','c',
							 IF(pay_method = '3','h',
							 IF(pay_method = '4','o',
							 IF(pay_method = '5','v',
							 IF(pay_method = '6','p',
							 IF(pay_method = '7','a','')))))))
		";
		$this->db->query($query);

	}

	function setSyncComplete() {

		if ($this->update('selly')) {

			$this->db->query("
				UPDATE ".GD_MARKET_ORDER." AS A
				INNER JOIN ".$this->temp_table_order." AS B
				ON A.order_no = B.ordno

				SET A.sync_ = 1,A.uptdt_ = '".date('Y-m-d H:i:s',$this->now)."'
			");

			$this->db->query("TRUNCATE TABLE ".$this->temp_table_order);
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_item);

		}
	}

	function setOrderDeliveryReady($ordno) {
		## ����Ȯ��
	}

	function setOrderDelivery($ordno, $extra) {
		## �����ó��
	}

    function setOrderCancelFin($ordno,$extra) {
		## ��ҿϷ� ó��
    }

    function setOrderReturnFin($ordno,$extra) {
		## ��ǰ�Ϸ� ó��
    }

	function setOrderExchangeFin($ordno, $extra) {
		## ��ȯ�Ϸ� ó��
	}

	

}
?>