<?php
require_once("plusCheeseEncrypter.class.php");

class plusCheese {
	var $godosno = 0;
	var $_url = "http://pluscheese.godo.co.kr/listen.shop.php";

	//����.
	//$sno : godosno
	function plusCheese($sno=-1)
	{
		if($sno == -1){
			global $godo;
			$this->sno = $godo['sno'];
		}else{
			$this->sno = $sno;
		}
		$this->fpath = dirname(__FILE__);

		//�������� �б�
		if(file_exists($this->fpath."/../conf/config.plusCheeseCfg.php"))
			require $this->fpath."/../conf/config.plusCheeseCfg.php";
		$this->plusCheeseCfg = $plusCheeseCfg;
/*
		//���������� ���� �б�
		if(!empty($plusCheeseCfg) &&  (strtoupper($plusCheeseCfg['use']) == "Y" || strtoupper($plusCheeseCfg['test']) == "Y") ){
			$this->plusCheeseCfg = $plusCheeseCfg;
			$data = readurl($this->_url."?mode=status&godoid=".$this->getRelayKey());
			if(substr($data, 0, 4) == "DONE"){
				$data = substr($data, 4);
				$data = unserialize($data);
				$this->data = $data;
			}else{
				msg($data, -1);
			}
		}
*/
	}

	//�߰�Ű ���ϱ�
	function getRelayKey(){
		return $this->plusCheeseCfg['key'];
	}
	
	//���� ���� �ҷ�����(��ü)
	function getStatus(){
		//return $this->data;
		return ""; 
	}
	
	//���� ���� �ҷ�����(�÷���ġ�� ��ü ID)
	function getStatusID(){
		//return $this->data['pc_entID'];
		return ""; 
	}
	
	//���� ���� �ҷ�����(������)
	function getStatusCom(){
		return $this->data['pc_commission'];
	}
	
	//���� ���� �ҷ�����(��ȣȭŰ)
	function getStatusEncKey(){
		return $this->data['pc_key'];
	}
	
	//���� ���� �ҷ�����(�� ��û ����)
	function getStatusCond(){
		//return $this->data['pc_condCode'];
		return ""; 
	}

	//���� ���� �ҷ�����(�ѱ� �޽���)
	function getStatusCondMsg($msg){
		if($msg == "Y"){
			return "���� �Ϸ�(��밡��)";
		}else if($msg == "A"){
			return "���� ���";
		}else if($msg == "N"){
			return "���� ����";
		}else if($msg == "C"){
			return "���� ���� (�簡�� ���� : 070-7123-6015)";
		}else{
			return("�� �� ����(".$msg.")");
		}
	}
	
	//���� ���� �ҷ�����(��û��¥)
	function getStatusADate(){
		return $this->data['keyAssignDate'];
	}
	
	//���� ���� �ҷ�����(���γ�¥)
	function getStatusRDate(){
		return $this->data['approveDate'];
	}
	
	//���� ���� �ҷ�����(�ݷ���¥)
	function getStatusDDate(){
		return $this->data['denialDate'];
	}

	//��ȣȭ
	function encrypt($str){
		$encrypter = new plusCheeseEncrypter($this->getStatusEncKey());
		$encrypted = $encrypter->encrypt($str);
		return $encrypted;
	}

	//��ȣȭ
	function decrypt($str){
		$decrypter = new plusCheeseEncrypter($this->getStatusEncKey());
		$decrypter = $decrypter->decrypt($str);
		return $decrypter;
	}

	//��¥���� ����(YYYY-MM-DD HH:II:SS -> YYYYMMDD_HHIISS)
	function dateFormatToXML($date){
		return str_replace("-", "", substr($date, 0, 10))."_".str_replace(":", "", substr($date, 11, 8));
	}
	
	//��¥���� ����(YYYYMMDD -> YYYY-MM-DD)
	function dateFormatFromGET($date){
		return substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
	}
	
	//UTF-8�� ��ȯ
	function toUTF8($str){
		return iconv("CP949", "UTF-8", $str);
	}
	
	//EUC-KR�� ��ȯ
	function toEUCKR($str){
		return iconv("UTF-8", "CP949", $str);
	}

	//XML�Ľ� �����Ϳ��� ���� ���Ѵ�.
	//$xml : XML��
	//$key : ���� ���� Ű
	//$c : ������ ���ص� ��� ������ ����(true�̸� ���)
	function findKey($xml, $key, $c = false){
		for($i=0;$i<count($xml);$i++){
			if(strtoupper($xml[$i]['tag']) == strtoupper($key)){
				$return[] = $xml[$i]['val'];
				if(!$c) return $return;
			}
		}
		return $return;
	}
	
	//������ ��ǰ�� �÷���ġ�� ���� ��ǰ���� üũ
	function except_goods($goodsno){
		return true; 
		/*
		//��ǰ�˻�
		if($this->plusCheeseCfg['e_refer']){
			foreach($this->plusCheeseCfg['e_refer'] as $v){
				if($v == $goodsno){
					return true;
				}
			}
		}

		//��ǰ�� ��ϵ� ��� ī�װ� �˻�
		global $db;
		$query = "SELECT category FROM ".GD_GOODS_LINK." WHERE goodsno=".$goodsno;
		$res = $db->query($query);
		while($data=$db->fetch($res)){
			$category[] = $data['category'];
		}
		if($this->plusCheeseCfg['category']){
			foreach($this->plusCheeseCfg['category'] as $v){
				foreach($category as $v2){
					$cate_len = strlen($v);
					if($v == substr($v2, 0, $cate_len)){
						return true;
					}
				}
			}
		}
		return false;
		*/
	}

	//�ֹ����� �÷���ġ��� ����
	function sendStatus($ordno, $step, $cancel=0){
		return; 
		/*
		global $db;
		$url = "http://admin.pluscheese.com/api/receiveOrderCond.do";
		$query = "SELECT o.pCheeseOrdNo ordno, oi.pCheeseOrdNo iordno FROM ".GD_ORDER." o, ".GD_ORDER_ITEM." oi WHERE o.ordno= oi.ordno AND o.ordno='".$ordno."'";
		$res = $db->query($query);
		//
		//$step
		//0:�ֹ����� => 001
		//1:�Ա�Ȯ�� => 002
		//2:����غ� => 003
		//3:�����   => 004
		//4:��ۿϷ� => 006
		
		
		switch($step){
			case 0:
				$stepMsg = "001";
				break;
			case 1:
				$stepMsg = "002";
				break;
			case 2:
				$stepMsg = "003";
				break;
			case 3:
				$stepMsg = "004";
				break;
			case 4:
				$stepMsg = "006";
				break;
		}
		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
		while($data=$db->fetch($res)){
			$xml .= "<orderCondInfo>\n";
			$xml .= "	<tempOrderNo>".$data['ordno']."</tempOrderNo>\n";
			$xml .= "	<tempOrderSeq>".($data['iordno'])."</tempOrderSeq>\n";
			$xml .= "	<orderCancelCount>".$cancel."</orderCancelCount>\n";
			$xml .= "	<orderCondition>".$stepMsg."</orderCondition>\n";
			$xml .= "	<changeDate>".$this->dateFormatToXML(date("Y-m-d H:i:s"))."</changeDate>\n";
			$xml .= "</orderCondInfo>";
		}
		$xml = $this->encrypt($xml);
		$param['XMLData'] = $xml;
		$param['entID'] = $this->getStatusID();
		$result = $this->postRequest($url, $param);
		return $result;
		*/
	}
	
	//CPS�ֹ� �÷���ġ��� ����
	function sendCPSOrder($ordno, $orderItem, $delivery, $settleprice, $settlekind, $sellerID){
		return; 
		/*
		if($settlekind == "a"){ //�������Ա�
			$settlekind = "C_TRANSFER";
		}else if($settlekind == "c"){ //�ſ�ī��
			$settlekind = "C_CARD";
		}else if($settlekind == "o"){ //�ǽð� ������ü
			$settlekind = "R_TRANSFER";
		}else if($settlekind == "v"){ //������� ����
			$settlekind = "C_TRANSFER";
		}else if($settlekind == "d"){ //��������
			$settlekind = "C_TRANSFER";
		}else if($settlekind == "a"){ //�޴�������
			$settlekind = "PHONE";
		}else if($settlekind == "p"){ //����Ʈ ����
			$settlekind = "POINT";
		}else if($settlekind == "u"){ //�߱� �ſ�ī�� ����
			$settlekind = "C_CARD";
		}
		
		$url = "http://admin.pluscheese.com/api/receiveCPSOrder.do";

		$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
		$xml .= "<orderRequest>\n";
		$xml .= "	<contentsClass>COCL000001</contentsClass>\n";
		$xml .= "	<orderNo>".$ordno."</orderNo>\n";
		$xml .= "	<sellerID>".$sellerID."</sellerID>\n";
		$xml .= "	<entID>".$this->getStatusID()."</entID>\n";
		for($i=0;$i<count($orderItem['orderSeq']);$i++){
			$xml .= "	<orderInfo>\n";
			$xml .= "		<orderSeq>".$orderItem['orderSeq'][$i]."</orderSeq>\n";
			$xml .= "		<prdCode>".$orderItem['prdCode'][$i]."</prdCode>\n";
			$xml .= "		<prdName><![CDATA[".$this->toUTF8($orderItem['prdName'][$i])."]]></prdName>\n";
			$xml .= "		<optionTitle>".$this->toUTF8($orderItem['optionTitle'][$i])."</optionTitle>\n";
			$xml .= "		<prdPrice>".$orderItem['prdPrice'][$i]."</prdPrice>\n";
			$xml .= "		<orderCount>".$orderItem['orderCount'][$i]."</orderCount>\n";
			$xml .= "		<orderSum>".$orderItem['orderSum'][$i]."</orderSum>\n";
			$xml .= "	</orderInfo>\n";
		}
		$xml .= "	<deliveryCharge>".$delivery."</deliveryCharge>\n";
		$xml .= "	<paymentAmount>".$settleprice."</paymentAmount>\n";
		$xml .= "	<paymentMethod>".$settlekind."</paymentMethod>\n";
		$xml .= "	<partnerCommission>".$this->getStatusCom()."</partnerCommission>\n";
		$xml .= "	<orderDate>".date("Ymd_His")."</orderDate>\n";
		$xml .= "</orderRequest>";
		$xml = $this->encrypt($xml);
		$param['XMLData'] = $xml;
		$param['entID'] = $this->getStatusID();
		$result = $this->postRequest($url, $param);
		$result = $this->decrypt($result['content']);
		return $result;
		*/
	}

	function postRequest($url, $data, $referer='') {
		return; 
		/*
		// Convert the data array into URL Parameters like a=b&foo=bar etc.
		$data = http_build_query($data);

		// parse the given URL
		$url = parse_url($url);

		if ($url['scheme'] != 'http') { 
			die('Error: Only HTTP request are supported !');
		}

		// extract host and path:
		$host = $url['host'];
		$path = $url['path'];

		// open a socket connection on port 80 - timeout: 30 sec
		$fp = fsockopen($host, 80, $errno, $errstr, 30);

		if ($fp){
			// send the request headers:
			fputs($fp, "POST $path HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");

			if ($referer != '') fputs($fp, "Referer: $referer\r\n");

			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data);

			$result = ''; 
			while(!feof($fp)) {
				// receive the results of the request
				$result .= fgets($fp, 128);
			}
		}else { 
			return array(
				'status' => 'err', 
				'error' => "$errstr ($errno)"
			);
		}

		// close the socket connection:
		fclose($fp);

		// split the result header from the content
		$result = explode("\r\n\r\n", $result, 2);

		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';

		// return as structured array:
		return array(
			'status' => 'ok',
			'header' => $header,
			'content' => $content
		);
		*/
	}
}
?>