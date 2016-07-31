<?
require_once dirname(__FILE__)."/auctionIpay.service.class.php";

class integrate_order_processor_ipay extends integrate_order_processor {



	function extractData($var = null) {
		/*
		 * �ֹ����� �������� �ֹ� ��ȣ�� �������� �ʾ����Ƿ�, old_ordno �ʵ带 �̿��Ͽ� ipayno �� �ֹ���ȣ�� ������Ʈ �Ѵ�.
		 * ���� : �ֹ���ȣ�� ������ȣ�� ������ �̹Ƿ�, ������ȣ�� �ֹ���ȣ ������� ����մϴ�.
		 */
		// �ֹ�������
		$query = "
			INSERT
			INTO ".$this->temp_table_order."
			(
				channel,ordno,old_ordno,m_no,m_id_out,ord_name,ord_email,ord_phone,ord_mobile,rcv_name,rcv_phone,rcv_mobile,rcv_zipcode,rcv_address,pay_amount,ord_amount,dis_amount,res_amount,dlv_amount,dlv_type,dlv_company,dlv_no,dlv_message,ord_date,dlv_date,pay_date,fin_date,pay_bank_name,pay_bank_account,pay_method,dlv_method,flg_escrow,flg_egg,flg_cashbag,flg_inflow,ori_status,
				reg_date,mod_date
			)

			SELECT
				'ipay',B.auctionpayno,'','','','','','','','','','','','',B.payprice,'','','',B.shippingprice,'','','','',IF(B.orderdate,B.orderdate,B.regdt ),'','','','','',B.paymenttype,'','','','','','',
				NOW(), null

			FROM ".GD_AUCTIONIPAY." AS B

			WHERE
				B.sync_ = 0 AND B.auctionpayno IS NOT NULL
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
				'ipay', B.ordno,C.goodsnm,C.goodsno,C.option,C.ea,C.price,
				IF (C.responsetype = 1, 'f','n')
			FROM ".$this->temp_table_order." AS B

			INNER JOIN ".GD_AUCTIONIPAY." AS A
				ON B.ordno = A.auctionpayno

			INNER JOIN ".GD_AUCTIONIPAY_ITEM." AS C
				ON A.ipaysno = C.ipaysno
		";

		$this->db->query($query);

		// ���ڵ� �� ����
		$this->adjustData();

		return true;
	}

	function adjustData() {

		$api = $this->getApiInstance();

		$query = "SELECT O.ordno, SUM(OI.price * OI.ea) AS ord_amount FROM ".$this->temp_table_order." AS O INNER JOIN ".$this->temp_table_item." AS OI ON O.ordno = OI.ordno GROUP BY O.ordno";

		$rs = $this->db->query($query);

		while ($row = $this->db->fetch($rs,1)) {

			$data = array(
				'DurationType'=>'Nothing',
				'SearchType'=>'PayNo',
				'SearchValue'=>$row['ordno']
			);

			$ip = $api->request('GetIpayPaidOrderList',$data);
			$order = $ip['GetOrderListResponseT'];
			if (empty($order)) continue;
			if (isset($order[0]) && isset($order[1])) $order = $order[0];

			$query = "
			UPDATE ".$this->temp_table_order."
			SET
				ordno			= '$order[PayNo]',
				m_id_out		= '$order[BuyerId]',
				ord_name		= '".$this->db->_escape($order['BuyerName'])."',
				rcv_name		= '".$this->db->_escape($order['BuyerName'])."',
				rcv_phone		= '".$this->db->_escape($order['DistTel'])."',
				rcv_mobile		= '".$this->db->_escape($order['DistMobileTel'])."',
				rcv_zipcode		= '".$this->db->_escape($order['DistPostNo'])."',
				rcv_address		= '".$this->db->_escape($order['DistAddressPost'].' '.$order['DistAddressDetail'])."',
				ori_status		= '".$this->db->_escape($order['OrderStatus'])."',
				ord_amount		= '$row[ord_amount]'

			WHERE ordno = $order[PayNo]
			";
			$this->db->query($query);

		}


		$_tmp = array(
						0	=> array('�Ա�Ȯ����'),				// �ֹ�����
						1	=> array('�����Ϸ�'),	// �Ա�Ȯ�� (�ݵ�� �������� �Է�)
						2	=> array('����غ���'),	// ����غ���
						3	=> array('�����'),		// �����
						4	=> array('��ۿϷ�','���Ű������','�۱ݿϷ�','�ŷ��Ϸ�'),	// ��ۿϷ�(�ǸſϷ�, �۱ݿϷ� ����)

						// ���
						10	=> array(),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						11	=> array('��ҿϷ�','���Űź�','���Աݱ������'),	// �Ϸ�

						// ȯ��
						20	=> array(),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						21	=> array('ȯ�ҿϷ�'),	// �Ϸ�

						// ��ǰ
						30	=> array(),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						31	=> array(),		// �Ϸ�

						// ��ȯ
						40	=> array(),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
						41	=> array(),	// �Ϸ�

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
				pay_method = IF(pay_method = 'A','a',
							 IF(pay_method = 'C','c',
							 IF(pay_method = 'M','h',
							 IF(pay_method = 'D','o',''))))
		";
		$this->db->query($query);

	}

	function setSyncComplete() {

		if ($this->update('ipay')) {

			$this->db->query("
				UPDATE ".GD_AUCTIONIPAY." AS A
				INNER JOIN ".$this->temp_table_order." AS B
				ON A.auctionpayno = B.ordno

				SET A.sync_ = 1,A.uptdt_ = '".date('Y-m-d H:i:s',$this->now)."'
			");

			$this->db->query("TRUNCATE TABLE ".$this->temp_table_order);
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_item);

		}
	}

	// ipay api ��ü ����.
	function &getApiInstance() {
        static $ins = null;

        if ($ins === null)
            $ins = & new auctionIpayService();

        return $ins;

	}

	function _execApi($method, $data) {
		$api = $this->getApiInstance();
		return $api->request($method, $data);
	}

	// �ֹ� ó�� ���� �޼��� (������� �ʰų� �������� �ʴ� �޼���� �����ص� ������)
	function setOrderDeliveryReady($ordno) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'IpayConfirmReceivingOrder';

		foreach ($orders as $order) {

			$data = array(
				'OrderNo'=>$order['OrderNo']
			);

			$this->_execApi($method, $data);
		}

		return true;

	}

	function setOrderDelivery($ordno, $extra) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'DoIpayShippingGeneral';

		foreach ($orders as $order) {

			$data = array(
				'OrderNo'=>$order['OrderNo'],
				'SendDate'=>date('Y-m-d'),
				'InvoiceNo'=>$extra['dlv_no'],
				'MessageForBuyer'=>'',
				'ShippingMethodClassficationType'=>'Door2Door',
				'DeliveryAgency'=>$extra['dlv_company'],
				'ShippingEtcMethod'=>'Nothing',
				'ShippingEtcAgencyName'=>'',
			);

			$this->_execApi($method, $data);
		}

		return true;

	}

    function setOrderCancelFin($ordno,$extra) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'DoIpayShippingGeneral';

		foreach ($orders as $order) {

			if ($extra['reject'] == 1) {
				// �ǸŰź�
				$method = 'IpayDenySell';
				$param = array(
					'ItemID' => $order['ItemNo'],
					'OrderNo' => $order['OrderNo'],
					'DenySellReason' => $extra['cs_reason_code']
				);
			}
			else {
				// ��� ����
				$method = 'IpayConfirmCancelApprovalList';
				$param = array(
					'BuyerID' => $order['BuyerId'],	// ��� Ŭ���ӹ�ȣ
					'OrderNo' => $order['OrderNo']	// �ֹ���ȣ
				);
			}

			$this->_execApi($method, $param);

		}

		return true;


    }

    function setOrderReturnFin($ordno,$extra) {

		$data = array(
			'DurationType'=>'Nothing',
			'SearchType'=>'PayNo',
			'SearchValue'=>$ordno
		);

		$ip = $this->_execApi('GetIpayPaidOrderList', $data);
		$orders = $ip['GetOrderListResponseT'];

		if (empty($orders)) return false;
		if (!isset($orders[0])) $orders = array($orders);

		$method = 'DoIpayReturnApproval';

		foreach ($orders as $order) {

			$param = array(
				'OrderNo' => $order['OrderNo']				// �ֹ���ȣ
			);

			$this->_execApi($method, $param);

		}

		return true;

    }

	function setOrderExchangeFin($ordno,$extra) {

		// ��߼� �ؾ� �ϹǷ� ���� �ǸŰ������� ó���ϵ��� �ȳ�.

	}

	/*
	 * �������ܺ� �������� �� ���������ȸ ������ �޾� �ɴϴ�.
	 */
	function GetIpayAccountNumb($ordno){

		$method = 'GetIpayAccountNumb';
		$data = array(
			'payNo'	=>	$ordno
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * ���Ű����Ǿ� �۱��� �Ϸ�� �ֹ��ǿ� ���� ȯ��ó���մϴ�.
	 */
	function DoIpayOrderDecisionCancel($ordno){

		$method = 'DoIpayOrderDecisionCancel';
		$data = array(
			'OrderNo'	=>	$ordno,
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * �ǸŰź�.
	 */
	function IpayDenySell($itemId, $orderNo, $enamooCancelReasonCode){
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$enamooCancelReason = array(
			'1' => 6,
			'2' => 1,
			'3' => 6,
			'4' => 6,
			'5' => 6,
			'6' => 6,
			'7' => 6,
			'8' => 2,
			'9' => 6,
			'10' => 6
		);
		$denySellReason = array(
			0 => 'LowerThanWishPrice',	// ���������� ��ǰ���ݺ��� ����
			1 => 'RunOutOfStock',	// ������
			2 => 'ManufacturingDefect',	// ��ǰ����
			3 => 'SoldToOtherBuyer',	// ������(�ٸ������ڰ� ����)
			4 => 'SellToOtherDitstributionChannel',	// ������(�ٸ� �Ǹ�ä�ο��� ����)
			5 => 'UnreliableBuyer',	// �����ڸ� �ŷ��Ҽ� ����
			6 => 'OtherReason'	// ��Ÿ����
		);
		$method = 'IpayDenySell';
		$data = array(
			'SellerID'			=>	$auctionIpayCfg['sellerid'],
			'ItemID'			=>	$itemId,
			'OrderNo'			=>	$orderNo,
			'DenySellReason'	=>	$denySellReason[$enamooCancelReason[$enamooCancelReasonCode]]
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * ����Ȯ��.
	 */
	function IpayConfirmReceivingOrder($OrderNo)
	{
		$method = 'IpayConfirmReceivingOrder';
		$data = array(
			'OrderNo'	=>	$OrderNo,
		);

		return $this->_execApi($method,$data);
	}

	/*
	 * �����߼�.
	 */
	function DoIpayShippingGeneral($OrderNo, $deliveryNo, $invoiceNo)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayShippingGeneral';
		$deliveryAgency = array(
			'37' => 'etc',
			'38' => 'etc',
			'15' => 'cjgls',
			'36' => 'etc',
			'35' => 'etc',
			'1' => 'kgbls',
			'2' => 'ktlogistics',
			'23' => 'etc',
			'24' => 'nothing',
			'39' => 'kyungdong',
			'3' => 'etc',
			'34' => 'etc',
			'27' => 'nedex',
			'26' => 'etc',
			'33' => 'daesin',
			'4' => 'korex',
			'29' => 'korex',
			'21' => 'dongbu',
			'25' => 'ajutb',
			'7' => 'ajutb',
			'5' => 'etc',
			'17' => 'sagawa',
			'6' => 'hth',
			'31' => 'etc',
			'16' => 'sedex',
			'8' => 'yellow',
			'30' => 'epost',
			'18' => 'epost',
			'9' => 'epost',
			'100' => 'epost',
			'32' => 'innogis',
			'10' => 'etc',
			'22' => 'ilyang',
			'28' => 'etc',
			'19' => 'chonil',
			'11' => 'etc',
			'20' => 'hanaro',
			'12' => 'hanjin',
			'13' => 'hyundai',
			'14' => 'etc'
		);

		$deliveryAgencyName = array(
			'37' => 'ACI Express',
			'38' => 'AirBoyExpress',
			'15' => 'CJ GLS(HTH����)',
			'36' => 'CVSnet',
			'35' => 'GSM �����ù�',
			'1' => 'KGB�ù�',
			'2' => 'KT������',
			'23' => 'WIZWA',
			'24' => '[��ü���]',
			'39' => '�浿�ù�',
			'3' => '����ù�',
			'34' => '����Ʈ��',
			'27' => '�׵���',
			'26' => '����',
			'33' => '����ù�',
			'4' => '�������',
			'29' => '�������(�̱����)',
			'21' => '�����ͽ�������',
			'25' => '�����ο���-�����ù�',
			'7' => '�����ο���-�ο����ù�(��:����)',
			'5' => '�����ù�',
			'17' => '�簡�� �ͽ�������',
			'6' => '�Ｚ�ù�HTH(��)',
			'31' => '���ǵ��ͽ�������',
			'16' => '�ż���(�굦��)',
			'8' => '�ٷο�ĸ',
			'30' => '��ü��EMS',
			'18' => '��ü�����',
			'9' => '��ü���ù�',
			'100' => '��ü���ù�(����)',
			'32' => '�̳������ù�',
			'10' => '�����ù�',
			'22' => '�Ͼ��ù�',
			'28' => '�����ͽ�������',
			'19' => 'õ���ù�',
			'11' => 'Ʈ���',
			'20' => '�ϳ����ù�',
			'12' => '�����ù�',
			'13' => '�����ù�',
			'14' => '�ѹ̸��ù�'
		);

		$data = array(
			'SellerID'							=> $auctionIpayCfg['sellerid'],
			'OrderNo'							=> $OrderNo,
			'RemittanceMethodType'				=> 'Emoney',
			'SendDate'							=> date('Y-m-d'),
			'InvoiceNo'							=> $invoiceNo,
			'MessageForBuyer'					=> '',
			'ShippingMethodClassficationType'	=> 'Door2Door',
			//'DeliveryAgency'					=> $deliveryAgency[$deliveryNo],
			'DeliveryAgency'					=> 'etc',
			'DeliveryAgencyName'				=> $deliveryAgencyName[$deliveryNo],
			'ShippingEtcMethod'					=> 'Nothing',
			'ShippingEtcAgencyName'				=> ''
		);
		return $this->_execApi($method,$data);
	}

	function DoIpayReturnRequestBySeller($orderNo, $enamooCancelReasonCode, $returnReasonDetail)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayReturnRequestBySeller';

		$returnBySellerReasonCode = array(
			'1' => 'ChangeOfMind',
			'2' => 'GoodsFault',
			'3' => 'GoodsFault',
			'4' => 'GoodsFault',
			'5' => 'GoodsFault',
			'6' => 'GoodsFault',
			'7' => 'GoodsFault',
			'8' => 'GoodsFault',
			'9' => 'GoodsFault',
			'10' => 'ChangeOfMind'
		);

		$data = array(
			'SellerID'							=> $auctionIpayCfg['sellerid'],
			'OrderNo'							=> $orderNo,
			'ReturnBySellerDeliveryMethod'		=> 'Buyer',
			'ReturnBySellerReasonCode'			=> $returnBySellerReasonCode[$enamooCancelReasonCode],
			'ReturnReasonDetail'				=> $returnReasonDetail
		);
		return $this->_execApi($method,$data);
	}

	function DoIpayReturnApproval($orderNo)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayReturnApproval';

		$data = array(
			'SellerID'							=> $auctionIpayCfg['sellerid'],
			'OrderNo'							=> $orderNo
		);
		return $this->_execApi($method,$data);
	}

	function GetIpayAgreementStatus($ipayCartNo, $ipayItemNo)
	{
		$method = 'GetIpayAgreementStatus';
		$data = array(
			'ipayCartNo'	=> $ipayCartNo,
			'ipayItemNo'	=> $ipayItemNo
		);
		return $this->_execApi($method,$data);
	}

	function GetIpayReceiptStatus($ipayCartNo, $ipayItemNo)
	{
		$method = 'GetIpayReceiptStatus';
		$data = array(
			'ipayCartNo'	=> $ipayCartNo,
			'ipayItemNo'	=> $ipayItemNo
		);

		return $this->_execApi($method,$data);
	}

	function IpayChangeShippingType($orderNo, $deliveryNo, $invoiceNo)
	{
		$method = 'IpayChangeShippingType';

		$deliveryAgencyName = array(
			'37' => 'ACI Express',
			'38' => 'AirBoyExpress',
			'15' => 'CJ GLS(HTH����)',
			'36' => 'CVSnet',
			'35' => 'GSM �����ù�',
			'1' => 'KGB�ù�',
			'2' => 'KT������',
			'23' => 'WIZWA',
			'24' => '[��ü���]',
			'39' => '�浿�ù�',
			'3' => '����ù�',
			'34' => '����Ʈ��',
			'27' => '�׵���',
			'26' => '����',
			'33' => '����ù�',
			'4' => '�������',
			'29' => '�������(�̱����)',
			'21' => '�����ͽ�������',
			'25' => '�����ο���-�����ù�',
			'7' => '�����ο���-�ο����ù�(��:����)',
			'5' => '�����ù�',
			'17' => '�簡�� �ͽ�������',
			'6' => '�Ｚ�ù�HTH(��)',
			'31' => '���ǵ��ͽ�������',
			'16' => '�ż���(�굦��)',
			'8' => '�ٷο�ĸ',
			'30' => '��ü��EMS',
			'18' => '��ü�����',
			'9' => '��ü���ù�',
			'100' => '��ü���ù�(����)',
			'32' => '�̳������ù�',
			'10' => '�����ù�',
			'22' => '�Ͼ��ù�',
			'28' => '�����ͽ�������',
			'19' => 'õ���ù�',
			'11' => 'Ʈ���',
			'20' => '�ϳ����ù�',
			'12' => '�����ù�',
			'13' => '�����ù�',
			'14' => '�ѹ̸��ù�'
		);

		$data = array(
			'OrderNo'						=> $orderNo,
			'ShippingMethodClassfication'	=> 'Door2Door',
			'DeliveryAgency'				=> '0',
			'DeliveryAgencyName'			=> $deliveryAgencyName[$deliveryNo],
			'ShippingEtcMethod'				=> 'Nothing',
			'ShippingEtcAgencyName'			=> '',
			'InvoiceNo'						=> $invoiceNo,
			'MessageForBuyer'				=> ''
		);
		return $this->_execApi($method,$data);
	}

	function DoIpayOrderDecisionRequest($orderNo)
	{
		include dirname(__FILE__).'/../conf/auctionIpay.cfg.php';
		$method = 'DoIpayOrderDecisionRequest';

		$data = array(
			'SellerID'               => $auctionIpayCfg['sellerid'],
			'OrderNo'                => $orderNo,
			'SellerManagementNumber' => '',
			'RequestReason'          => ''
		);
		return $this->_execApi($method,$data);
	}
}
?>