<?php
require_once (dirname(__FILE__).'/naverCheckoutAPI.class.php');

class naverCheckoutAPI_4 extends naverCheckoutAPI {

	var $relayURL = 'http://navercheck.godo.co.kr/listen.shop.4.0.php';	// 4.0 api

	function naverCheckoutAPI_4() {
		parent::naverCheckoutAPI();
	}

	function _httpRequest($request) {
		$this->error='';
		$requestPost = array(
			'shopNo'=> $this->shopNo,
			'enc'=> $this->_encrypt(&$request),
		);

		$httpSock = new httpSock($this->relayURL,'POST',$requestPost);
		$httpSock->send();

		if(strncmp($httpSock->resContent,'APIRESULT',9)==0) {
			$this->requestResult = iconv_recursive('utf-8','euc-kr',$this->_decrypt(substr($httpSock->resContent,9)));

			if ($this->requestResult['result'] === false) {
				$this->error = $this->requestResult['error'];
				return false;
			}

			return true;
		}
		else {
			$this->error = 'API ��ſ� �����߽��ϴ�';
			return false;
		}
	}

	function sync($OrderID) {
		$request = array(
			'mode'=>'sync',
			'OrderID'=>$OrderID,
		);
		$this->_httpRequest($request);
	}

	/**
	 * �߰� ����(relayUrl)�� ȣ��, �� ����� ������ �ݿ�
	 * @param object $method
	 * @param object $params
	 * @return
	 */
	function request($method, $params) {

		$params = $this->_getParams($method, $params);

		if(!$this->_httpRequest($params)) {
			return false;
		}

		return true;

	}

	/**
	 * �� operation �� �Ķ���͸� ����
	 * @param object $method
	 * @param object $params
	 * @return
	 */
	function _getParams($method, $params) {

		$_set = array(
			'PlaceProductOrder' => array(
							'ProductOrderID' => true,
							),
			'DelayProductOrder' => array(
							'ProductOrderID' => true,
							'DispatchDueDate' => true,
							'DispatchDelayReasonCode' => true,
							'DispatchDelayDetailReason' => false,
							),
			'ShipProductOrder' => array(
							'ProductOrderID' => true,
							'DeliveryMethodCode' => true,
							'DeliveryCompanyCode' => false,
							'TrackingNumber' => false,
							'DispatchDate' => true,
							),
			'CancelSale' => array(
							'ProductOrderID' => true,
							'CancelReasonCode' => true,
							),
			'ApproveCancelApplication' => array(
							'ProductOrderID' => true,
							'EtcFeeDemandAmount' => true,
							'Memo' => false,
							),
			'RequestReturn' => array(
							'ProductOrderID' => true,
							'ReturnReasonCode' => true,
							'CollectDeliveryMethodCode' => true,
							'CollectDeliveryCompanyCode' => false,
							'CollectTrackingNumber' => false,
							),
			'ApproveReturnApplication' => array(
							'ProductOrderID' => true,
							'EtcFeeDemandAmount' => true,
							'Memo' => false,
							),
			'ApproveExchangeApplication' => array(
							'ProductOrderID' => true,
							'EtcFeeDemandAmount' => true,
							'Memo' => false,
							),
			'ApproveCollectedExchange' => array(
							'ProductOrderID' => true,
							'CollectDeliveryMethodCode' => true,
							),
			'ReDeliveryExchange' => array(
							'ProductOrderID' => true,
							'ReDeliveryMethodCode' => true,
							'ReDeliveryCompanyCode' => false,
							'ReDeliveryTrackingNumber' => false,
							),
			'GetMigratedProductOrderList' => array(
							'OldOrderID' => true,
							),
		);

		// ���ʿ��� ���� ���� �� �����Ƿ�...
		$_params = array();

		foreach ($_set[$method] as $k => $req) {
			if ($req && !isset($params[$k])) {
				// �ʼ��� ����.

			}

			$_params[$k] = iconv('euc-kr','utf-8',$params[$k]);

		}

		$_params['mode'] = $method;

		return $_params;

	}


	/**
	 * �ֹ����� ��ǰ�� ������ ���
	 *
	 * @param string $ProductOrderID ���̹� ��ǰ �ֹ���ȣ
	 */
	function setOrderEmoney($ProductOrderID) {

		$db = Core::loader('db');
		@include_once dirname(__FILE__)."/../conf/config.pay.php";

		// ��ǰ�ֹ� ����
		$query = "
		SELECT

			O.PaymentMeans,

			PO.MallMemberID,
			PO.ProductName,
			PO.ProductOption,
			PO.Quantity,
			PO.UnitPrice,
			PO.ProductDiscountAmount,

			G.goodsno,
			G.use_emoney

		FROM ".GD_NAVERCHECKOUT_ORDERINFO." AS O

		INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		ON O.OrderID = PO.OrderID

		INNER JOIN ".GD_MEMBER." AS MB
		ON PO.MallMemberID = MB.m_id

		INNER JOIN ".GD_GOODS." AS G
		ON PO.ProductID = G.goodsno

		WHERE PO.ProductOrderID = '$ProductOrderID' AND PO.ProductOrderStatus NOT IN ('CANCELED','RETURNED','CANCELED_BY_NOPAYMENT')
		";

		if ($data = $db->fetch($query,1)) {

			// �������� �����ϱ� ���� ���� Ȯ��
			if($set['emoney']['useyn'] == 'n') return; // ������ ��뿩��
			elseif($set['emoney']['limit'] == 1 && $data['PaymentMeans'] == '����Ʈ����') return; //������ ������ ��ǰ ������ ������
			else {

				// �ɼ� ����
				$tmpOption = explode('/', $data['ProductOption']);
				$tmp = explode(':', $tmpOption[0]); $opt1 = $tmp[1];
				$tmp = explode(':', $tmpOption[1]); $opt2 = $tmp[1];

				if($data['use_emoney'] == "1") {
					list($reserve) = $db->fetch("SELECT reserve FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$data['goodsno']."' AND opt1 = '$opt1' AND opt2 = '$opt2' and go_is_deleted <> '1' and go_is_display = '1' ");
					$productReserve = $reserve * $data['Quantity']; // ���� ������ŭ
				}
				else {
					if($set['emoney']['goods_emoney']) {
						if($set['emoney']['chk_goods_emoney'] == "0") {
							$productReserve = (($data['UnitPrice'] / 100 ) * $set['emoney']['goods_emoney']) * $data['Quantity'];
						}
						else {
							$productReserve = $set['emoney']['goods_emoney'] * $data['Quantity'];
						}
					}
					else $productReserve = 0;
				}
			}

			settype($productReserve, "integer");

			// ������ DB�� ����
			$db->query("UPDATE ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." SET eNamooEmoney = '$productReserve' WHERE ProductOrderID = '$ProductOrderID'");

		}

	}


	/**
	 * ��� �谨
	 * @param object $ProductOrderID
	 * @return
	 */
	function cutStock($ProductOrderID) {

		// ����� ����� ����� üũ�մϴ�.
		$config = Core::loader('config');
		$config_checkoutapi = $config->load('checkoutapi');
		if($config_checkoutapi['linkStock']!='y') {
			return;
		}

		$db = Core::loader('db');

		// ��� �谨�� ������ ��ǰ �ֹ������� ������.
		$query = "
		SELECT

			PO.ProductOption, PO.Quantity, PO.eNamooStockProcess, PO.OptionCode,
			G.goodsno

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_GOODS." AS G
		ON G.goodsno = PO.ProductID
		WHERE
			PO.ProductOrderID = '$ProductOrderID'
		AND PO.eNamooStockProcess = 'none'
		AND G.usestock = 'o'
		";

		if (!($productorder = $db->fetch($query,1))) return;

		// �ɼǺи�
		$opt = unserialize($productorder['OptionCode']);

		// �ɼ����谨 -> ��ǰ��ü������� -> ���谨�Ϸ�ó��
		$query = $db->_query_print(
				'update '.GD_GOODS_OPTION.' set stock = stock - [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
				(int)$productorder['Quantity'],
				(string)$productorder['goodsno'],
				(string)$opt[0],
				(string)$opt[1]);

		if ($db->query($query)) {

			// ��ü ��� ����
			$query = $db->_query_print(
					'UPDATE '.GD_GOODS.' SET totstock = (SELECT SUM(stock) FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] and go_is_deleted <> \'1\') WHERE goodsno=[s]',
					$productorder['goodsno'],
					$productorder['goodsno']);

			if ($db->query($query)) {
				// ��� �谨 �Ϸ�
				$query = $db->_query_print('UPDATE '.GD_NAVERCHECKOUT_PRODUCTORDERINFO.' SET eNamooStockProcess = "done" WHERE ProductOrderID=[s]',$ProductOrderID);
				$db->query($query);

			}
			else {
				// �ɼ� ��� rollback.
				$query = $db->_query_print(
						'update '.GD_GOODS_OPTION.' set stock = stock + [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
						(int)$productorder['Quantity'],
						(string)$productorder['goodsno'],
						(string)$opt[0],
						(string)$opt[1]);
				$db->query($query);
			}

		}

	}


	/**
	 * ��� ����
	 * @param object $ProductOrderID
	 * @return
	 */
	function backStock($ProductOrderID) {

		// ����� ����� ����� üũ�մϴ�.
		$config = Core::loader('config');
		$config_checkoutapi = $config->load('checkoutapi');
		if($config_checkoutapi['linkStock']!='y') {
			return;
		}

		$db = Core::loader('db');

		// ��� ������ ������(��� �谨��) ��ǰ �ֹ������� ������.
		$query = "
		SELECT

			PO.ProductOption, PO.Quantity, PO.eNamooStockProcess, PO.OptionCode,
			G.goodsno

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_GOODS." AS G
		ON G.goodsno = PO.ProductID
		WHERE
			PO.ProductOrderID = '$ProductOrderID'
		AND PO.eNamooStockProcess = 'done'
		AND G.usestock = 'o'
		";

		if (!($productorder = $db->fetch($query,1))) return;

		// �ɼǺи�
		$opt = unserialize($productorder['OptionCode']);

		// �ɼ������� -> ��ǰ��ü������� -> �������Ϸ�ó��
		$query = $db->_query_print(
				'update '.GD_GOODS_OPTION.' set stock = stock + [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
				(int)$productorder['Quantity'],
				(string)$productorder['goodsno'],
				(string)$opt[0],
				(string)$opt[1]);

		if ($db->query($query)) {

			// ��ü ��� ����
			$query = $db->_query_print(
					'UPDATE '.GD_GOODS.' SET totstock = (SELECT SUM(stock) FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] and go_is_deleted <> \'1\') WHERE goodsno=[s]',
					$productorder['goodsno'],
					$productorder['goodsno']);

			if ($db->query($query)) {
				// ��� ���� �Ϸ�
				$query = $db->_query_print('UPDATE '.GD_NAVERCHECKOUT_PRODUCTORDERINFO.' SET eNamooStockProcess = "back" WHERE ProductOrderID=[s]',$ProductOrderID);
				$db->query($query);

			}
			else {
				// �ɼ� ��� rollback.
				$query = $db->_query_print(
						'update '.GD_GOODS_OPTION.' set stock = stock - [i] where goodsno=[s] and opt1=[s] and opt2=[s]',
						(int)$productorder['Quantity'],
						(string)$productorder['goodsno'],
						(string)$opt[0],
						(string)$opt[1]);
				$db->query($query);
			}

		}

	}


	/**
	 * ������ ����
	 *
	 * @param integer $orderNo ���̹� �ֹ��� DB �ֹ���ȣ
	 * @param integer $mode ����:1, ȸ��:-1
	 */
	function setEmoney($ProductOrderID, $mode=1) {

		$db = Core::loader('db');

		// ���� ����, ��� ��ȸ
		$query = "
		SELECT
			PO.ProductOrderID, PO.eNamooEmoney,
			MB.m_id, MB.m_no,

			LE.sno, LE.emoney

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_MEMBER." AS MB
		ON PO.MallMemberID = MB.m_id
		INNER JOIN ".GD_LOG_EMONEY." AS LE
		ON LE.ordno = PO.ProductOrderID
		WHERE PO.ProductOrderID = '$ProductOrderID' AND PO.eNamooEmoney > 0
		";

		if (!($data = $db->fetch($query,1))) return;

		$msg = ($mode > 0) ? "���ſϷ�� ���� ���������� ���� - ���̹� üũ�ƿ�" : "������ҷ� ���� ���������� ȯ�� - ���̹� üũ�ƿ�";

		$dormantMember = false;
		$dormant = Core::loader('dormant');
		$dormantMember = $dormant->checkDormantMember(array('m_no'=>$data[m_no]), 'm_no');

		if ($mode > 0) {
			// ���� (���� ����� ������)
			if (is_null($data['sno'])) {
				if($dormantMember === true){
					$dormantEmoneyQuery = $dormant->getEmoneyUpdateQuery($data[m_no], $data[eNamooEmoney]);
					$db->query($dormantEmoneyQuery);
				}
				else {
					$db->query("UPDATE ".GD_MEMBER." SET emoney = emoney + $data[eNamooEmoney] WHERE m_no = '$data[m_no]'");
				}
				$db->query("INSERT INTO ".GD_LOG_EMONEY." SET
					m_no	= '$data[m_no]',
					ordno	= '$ProductOrderID',
					emoney	= '$data[eNamooEmoney]',
					memo	= '$msg',
					regdt	= NOW()
				");
			}
		}
		else {
			// ���� (���� ����� ������)
			if (!is_null($data['sno'])) {
				if($dormantMember === true){
					$dormantEmoneyQuery = $dormant->getEmoneyUpdateQuery($data[m_no], $data[emoney]);
					$db->query($dormantEmoneyQuery);
				}
				else {
					$db->query("UPDATE ".GD_MEMBER." SET emoney = emoney - $data[emoney] WHERE m_no = '$data[m_no]'");
				}
				$db->query("INSERT INTO ".GD_LOG_EMONEY." SET
					m_no	= '$data[m_no]',
					ordno	= '$ProductOrderID',
					emoney	= '-$data[emoney]',
					memo	= '$msg',
					regdt	= NOW()
				");

				$db->query("UPDATE ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." SET eNamooEmoney = '0' WHERE ProductOrderID = '$ProductOrderID'");
			}

		}

	}

	/**
	 * ���ſϷ� ���� �߱�
	 *
	 * @param integer $orderNo ���̹� �ֹ��� DB �ֹ���ȣ
	 */
	function setCoupon($ProductOrderID) {
		$db = Core::loader('db');

		$query = "
		SELECT
			PO.ProductOrderID, PO.ProductID,
			MB.m_id, MB.m_no

		FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
		INNER JOIN ".GD_MEMBER." AS MB
		ON PO.MallMemberID = MB.m_id
		INNER JOIN ".GD_GOODS." AS G
		ON PO.ProductID = G.goodsno

		WHERE PO.ProductOrderID = '$ProductOrderID'
		";

		if (!($data = $db->fetch($query,1))) return;

		$query = "SELECT category, CHAR_LENGTH(category) clen FROM ".GD_GOODS_LINK." WHERE hidden = 0 AND goodsno = '$data[ProductID]'";
		$res = $db->query($query);
		while($tmp = $db->fetch($res)) for($i = 3; $i <= $tmp['clen']; $i += 3) $arrCategory[] = "'".substr($tmp['category'], 0, $i)."'";
		if(count($arrCategory) > 0)$arrCategory = array_unique($arrCategory);
		else $arrCategory = array();

		$query	=	"SELECT a.*
					FROM
						".GD_COUPON." a
						LEFT JOIN ".GD_COUPON_CATEGORY." b ON a.couponcd = b.couponcd
						LEFT JOIN ".GD_COUPON_GOODSNO." c ON a.couponcd = c.couponcd
					WHERE a.coupontype = 3
						AND ((a.sdate <= '".date("Y-m-d H:i:s")."' AND a.edate >= '".date("Y-m-d H:i:s")."' AND a.priodtype='0') OR a.priodtype='1')
						AND (((b.category in(".implode(',',$arrCategory).") OR c.goodsno = '$data[ProductID]') AND a.goodstype='1') OR a.goodstype='0')";

		$res = $db->query($query);
		$i=0;

		while($data = $db->fetch($res)){

			$query = "select a.sno from ".GD_COUPON_APPLY." a left join ".GD_COUPON_APPLYMEMBER." b on a.sno=b.applysno where a.couponcd='$data[couponcd]' and b.m_no = '$m_no' order by a.regdt desc limit 1";
			list($applysno) = $db->fetch($query);
			$query = "select count(*) from ".GD_COUPON_ORDER." where applysno='$applysno' and m_no = '$m_no'";
			list($cnt) = $db->fetch($query);

			if(!$applysno){
				$newapplysno = new_uniq_id('sno',GD_COUPON_APPLY);
				$query = "INSERT INTO ".GD_COUPON_APPLY." SET
							sno				= '$newapplysno',
							couponcd		= '$data[couponcd]',
							membertype		= '2',
							member_grp_sno  = '',
							regdt			= now()";
				$db->query($query);
				$query = "insert into ".GD_COUPON_APPLYMEMBER." set m_no='$m_no', applysno ='$newapplysno'";
				$db->query($query);
			}else if($cnt == 0){
				$query = "update ".GD_COUPON_APPLY." set regdt=now() where sno='$applysno'";
				$db->query($query);
			}
		}

	}

}
?>
