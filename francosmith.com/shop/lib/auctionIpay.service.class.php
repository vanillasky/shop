<?php

/*
https://api.auction.co.kr/ArcheSystem/IpayService.asmx
http://ipay.auction.co.kr/
*/

class auctionIpayService {

	var $serverUrl;
	var $action;
	var $data;

	var $cfg;

	var $_prevent = false;

	function auctionIpayService() {

		if(isset($auctionIpayCfg)===false) @include dirname(__FILE__)."/../conf/auctionIpay.cfg.php";

		if (empty($auctionIpayCfg)) {
			$this->_prevent = true;
		}
		else {

			$this->cfg = $auctionIpayCfg;

			$this->serverUrl = array();
			$this->serverUrl['real'] = 'https://api.auction.co.kr/ArcheSystem/IpayService.asmx';
			$this->serverUrl['test'] = 'https://apitest.auction.co.kr/ArcheSystem/IpayService.asmx';
			$this->action = '';

			$this->setServiceList();

		}
	}

	function error() {
		echo '�����߻�;';
		exit;
	}

	function setServiceList() {

		$_api = array();

		$_api['GetIpayPaidOrderList'];	// �ֹ�����Ʈ
		$_api['IpayConfirmReceivingOrder'];	// ����Ȯ��
		$_api['DoIpayShippingGeneral'];	// �߼�ó��
		$_api['IpayDenySell'];			// �Ǹ����

		$_api['GetIpayReturnList'];				// ��ǰ��û����Ʈ
		$_api['DoIpayReturnApproval'];			// ��ǰ��û����
		$_api['IpayConfirmCancelApprovalList'];	// ��ҽ���

		return;

	}


	function makebody($data=null,$method=null) {

		if (empty($data) || empty($method)) return false;

		$data['SellerID'] = $this->cfg['sellerid'];

		// �ֹ�����
		$xml_body['GetIpayPaidOrderList'] = '
			<req DurationType="{:DurationType:}" SearchType="{:SearchType:}" SearchValue="{:SearchValue:}">
			</req>
		';

		// ����Ȯ��
		$xml_body['IpayConfirmReceivingOrder'] = '
			<req OrderNo="{:OrderNo:}" />
		';

		// �߼�ó��
		$xml_body['DoIpayShippingGeneral'] = '
			<req SellerID="{:SellerID:}" OrderNo="{:OrderNo:}">
			<RemittanceMethod RemittanceMethodType="{:RemittanceMethodType:}" RemittanceAccountName="{:RemittanceAccountName:}" RemittanceAccountNumber="{:RemittanceAccountNumber:}" RemittanceBankCode="{:RemittanceBankCode:}" xmlns="http://schema.auction.co.kr/Arche.API.xsd" />
			<ShippingMethod SendDate="{:SendDate:}" InvoiceNo="{:InvoiceNo:}" MessageForBuyer="{:MessageForBuyer:}" ShippingMethodClassficationType="{:ShippingMethodClassficationType:}" DeliveryAgency="{:DeliveryAgency:}" DeliveryAgencyName="{:DeliveryAgencyName:}" ShippingEtcMethod="{:ShippingEtcMethod:}" ShippingEtcAgencyName="{:ShippingEtcAgencyName:}" xmlns="http://schema.auction.co.kr/Arche.API.xsd" />
			</req>
		';


		// �Ǹ����
		/*
		DenySellReason
		LowerThanWishPrice or RunOutOfStock or ManufacturingDefect or SoldToOtherBuyer or SellToOtherDitstributionChannel or UnreliableBuyer or OtherReason
		*/
		$xml_body['IpayDenySell'] = '
			<req SellerID="{:SellerID:}" ItemID="{:ItemID:}" OrderNo="{:OrderNo:}" DenySellReason="{:DenySellReason:}" />
		';

		// ��ǰ��û����Ʈ
		/*
		SearchFlags
		All or Requested or Hold or Reject or Finished

		*/
		$xml_body['GetIpayReturnList'] = '
			<req SearchFlags="{:SearchFlags:}" SearchType="{:SearchType:}" SearchKeyword="{:SearchKeyword:}" SearchDateType="{:SearchDateType:}" PageSize="{:PageSize:}">
				<SearchDuration StartDate="{:StartDate:}" EndDate="{:EndDate:}" xmlns="http://schema.auction.co.kr/Arche.API.xsd" />
			</req>
		';

		// ��ǰ��û����
		$xml_body['DoIpayReturnApproval'] = '
			<req SellerID="{:SellerID:}" OrderNo="{:OrderNo:}" />
		';



		// ��ҽ���
		$xml_body['IpayConfirmCancelApprovalList'] = '
			<req SellerID="{:SellerID:}" BuyerID="{:BuyerID:}" OrderNo="{:OrderNo:}" />
		';

		// ȯ��ó��(���Ű��� ����)
		$xml_body['DoIpayOrderDecisionCancel'] = '
			<req OrderNo="{:OrderNo:}"/>
		';

		// ������ ���� ��ǰ����ó��
		$xml_body['DoIpayReturnRequestBySeller'] = '
			<req SellerID="{:SellerID:}" OrderNo="{:OrderNo:}" ReturnBySellerDeliveryMethod="{:ReturnBySellerDeliveryMethod:}" ReturnBySellerReasonCode="{:ReturnBySellerReasonCode:}" ReturnReasonDetail="{:ReturnReasonDetail:}">
			</req>
		';

		// ����Ȯ��ó��
		$xml_body['DoIpayOrderDecisionRequest'] = '
			<req SellerID="{:SellerID:}" OrderNo="{:OrderNo:}" SellerManagementNumber="{:SellerManagementNumber:}" RequestReason="{:RequestReason:}" />
		';

		// �Ա�Ȯ��
		$xml_body['GetIpayAccountNumb'] = '
			<payNo>{:payNo:}</payNo>
		';

		// ���λ�����ȸ
		$xml_body['GetIpayAgreementStatus'] = '
			<ipayCartNo>{:ipayCartNo:}</ipayCartNo>
			<ipayItemNo>{:ipayItemNo:}</ipayItemNo>
		';

		// �Աݻ�����ȸ
		$xml_body['GetIpayReceiptStatus'] = '
			<ipayCartNo>{:ipayCartNo:}</ipayCartNo>
			<ipayItemNo>{:ipayItemNo:}</ipayItemNo>
		';

		// ��۹������
		$xml_body['IpayChangeShippingType'] = '
			<req OrderNo="{:OrderNo:}" ShippingMethodClassfication="{:ShippingMethodClassfication:}" DeliveryAgency="{:DeliveryAgency:}" DeliveryAgencyName="{:DeliveryAgencyName:}" ShippingEtcMethod="{:ShippingEtcMethod:}" ShippingEtcAgencyName="{:ShippingEtcAgencyName:}" InvoiceNo="{:InvoiceNo:}" MessageForBuyer="{:MessageForBuyer:}" />
		';

		$xml  =  '<?xml version="1.0" encoding="utf-8"?>	';
		$xml .=  '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"> ';
		$xml .=  '<soap:Header>		';
		$xml .=  '	<EncryptedTicket xmlns="http://www.auction.co.kr/Security">		';
		$xml .=	'		<Value>' . $this->getTicket() .  '</Value> ';
		$xml .=  '	</EncryptedTicket> ';
		$xml .=  '</soap:Header> ';
		$xml .=  '<soap:Body>		';

		// �� request �� ���� ó��
		$_body = $xml_body[$method];
		foreach($data as $attribute => $value) {
			$_body = str_replace('{:'.$attribute.':}', $value, $_body);
		}
		// �� Ű���� ����
		if (preg_match_all('/{:[a-zA-Z]+:}/',$_body,$matches)) {
			$_body = str_replace($matches[0],'',$_body);
		}

		$xml .=  '<'.$method.' xmlns="http://www.auction.co.kr/IpayService/Ipay">';
		$xml .=  $_body;
		$xml .=  '</'.$method.'> ';

		$xml .=  '</soap:Body> ';
		$xml .=  '</soap:Envelope> ';

		return iconv('EUC-KR', 'UTF-8', str_replace("&", "&amp;", $xml));
	}


	function getTicket() {

		static $ticket = null;

		if ($ticket === null)
			$ticket = $this->cfg['ticket'];

		return $ticket;
	}

	function request($method='', $data='',$test=false) {

		if ($this->_prevent === true) return false;

		if (($xml = $this->makebody($data,$method)) === false) {
			return false;
		}

		$header = array (
			"Content-Type: text/xml; charset=utf-8",
			"Content-Length: ".strlen($xml),
			"SOAPAction: http://www.auction.co.kr/IpayService/Ipay/".$method
		);

		$url = $test ? $this->serverUrl['test'] : $this->serverUrl['real'];

		$ch = curl_init();
			  curl_setopt($ch, CURLOPT_URL, $url);
			  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			  curl_setopt($ch, CURLOPT_POST, 1);
			  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$rs = curl_exec($ch);
		curl_close($ch);

		return $this->parse_result($rs,$method);
	}

	function parse_result($xml,$method) {

		$xml = XML::parse($xml);
		$xml = iconv_recursive('UTF-8', 'EUC-KR', $xml);

		if(isset($xml['Envelope']['Body']['Fault']))
		{
			$xml['Envelope']['Body']['Fault']['faultstring'] = $xml['Envelope']['Body']['Fault']['faultstring'];
			$res = $xml['Envelope']['Body']['Fault'];
		}
		else
		{
			$res = $xml['Envelope']['Body'][$method.'Response'][$method.'Result'];
		}



		return $res;
	}



	/* �������̽� */
	function getlist() {
	}


	function ctlStep($ordno,$step,$stock='') {
		/*
			�ֹ�����, �Ա�Ȯ��, ��ۿϷ� ó���� �� ����
		*/

		switch ($step){

			case "0":				// �ֹ�����
			case "1":				// �Ա�Ȯ��
			case "4":				// ��ۿϷ�
				break;

			case "2":		// �Ա�Ȯ��, ����غ���

				if ($pre[cyn]=="n") $exec_cyn_y = true;
				if ($pre[dyn]=="y") $exec_dyn_n = true;
				if ($pre[confirm])  $exec_confirm_n = true;
				break;

			case "3":				// �����

				if ($pre[cyn]=="n") $exec_cyn_y = true;
				if ($pre[dyn]=="n") $exec_dyn_y = true;
				if ($pre[confirm])  $exec_confirm_n = true;
				break;

		}

	}

}




class XML {

    var $path;
    var $result = null;
	var $index = 0;


	function __construct() {	}
	function __destruct() {}

	function &getInstance() {
		static $instance = null;
		if ($instance === null) $instance = & new XML();
		return $instance;
	}

	function create(&$data) {
		$class = XML::getInstance();
		if(is_object($class))
			return $class->_create($data);
	}

	function parse($xml) {
		$class = XML::getInstance();
		if(is_object($class))
			return $class->_parse($xml);
	}


    function _parse($data) {

		$this->result = null;
        $this->path = "\$this->result";
        $this->index = 0;

		$xml_parser = xml_parser_create_ns('UTF-8',':');
		xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
		xml_set_object($xml_parser, $this);
		xml_set_element_handler($xml_parser, '_parse_open', '_parse_close');
		xml_set_character_data_handler($xml_parser, '_parse_data');

		xml_parse($xml_parser, $data, true);
		xml_parser_free($xml_parser);

		$result = $this->result;

		return $this->toArray($result);

    }

	function toArray($object) {

		if(is_array($object) || is_object($object)) {

			$array = array();

			foreach($object as $key => $value) {
				$array[$key] = $this->toArray($value);
			}

			return $array;
		}

		return $object;
	}

    function toString($string='') {
		return ($string == '') ? '' : trim(addslashes($string));
    }

    function _parse_open($parser, $tag, $attribute) {

		$tag = (strpos($tag,':') === false ) ? $tag : array_pop(explode(':',$tag));

        $this->path .= "->".$tag;
		eval("\$data = ".$this->path.";");

        if (is_array($data)) {
			$index = sizeof($data);
            $this->path .= "[".$index."]";

        } else if (is_object($data)) {
            eval($this->path." = array(".$this->path.");");
            $this->path .= "[1]";
        }

        foreach($attribute as $name => $value) {
            eval($this->path."->".$name. " = '".$this->toString($value)."';");
		}
    }

    function _parse_close($parser, $tag) {
        $this->path = substr($this->path, 0, strrpos($this->path, "->"));

    }

    function _parse_data($parser, $data) {


		if (($data = $this->toString($data)) != '') {
			eval('
				if (is_string('.$this->path.'))
					'.$this->path.' .= " ".$data;
				else
					'.$this->path.' = $data;
			');
		}

    }



	function _create(&$data) {

		$XML = '<?xml version="1.0" encoding="euc-kr" ?>';
		$XML .= $this->_xml_create_array_to_struct($data);

		return $XML;

	}

	function _xml_create_array_to_struct(& $data) {

		$XML = '';

		foreach($data as $k => $val) {

			$XML .= '<'.$k.'>';

			if (is_array($val)) {
				$XML .= $this->_xml_create_array_to_struct($val);
			}
			else {
				if (preg_match('/["&\'<>]/',$val)) {
					$XML .= '<![CDATA['.$val.']]>';
				}
				else {
					$XML .= $val;
				}

			}
			$XML .= '</'.$k.'>'."\n";
		}

		return $XML;

	}

}
?>