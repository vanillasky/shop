<?
class cardCancel {
var $cfg;
var $cpg;
var $tpl;
var $shopdir;
var $cfgMobileShop;
var $cancel_code = 9;		// �ֹ� ��ҽ� ��� ���� �ڵ� (�⺻�� 9)
var $pg_dir = 'order/card';
var $tax = 0;
var $vat = 0;
var $taxfree = 0;

function cardCancel(){
	$this->cfg = $GLOBALS['cfg'];
	$this->shopdir = substr(dirname(__FILE__),0,-4);
	if($GLOBALS['pg']['id']){
		$this->cpg = $GLOBALS['pg'];
	}else{
		include_once $this->shopdir.'/conf/pg.'.$this->cfg['settlePg'].'.php';
		$this->cpg = $pg;
	}
}

function cancel_pg($ordno){
		$row = $this->get_order_data($ordno);

		if($row['mobilepay']=='y' && in_array($this->cfg['settlePg'],array('allat','allatbasic','lgdacom'))){
			include_once $this->shopdir.'/conf/pg_mobile.'.$this->cfg['settlePg'].'.php';
			$this->cpg = $pg_mobile;
		}

		switch($this->cfg['settlePg']){
			case "allat" :
				if($row['mobilepay']=='y') return $this->cancel_allat_mobile_request($ordno);
				else {
					$this->cancel_allat_request($ordno,1);
					exit;
				}
			break;
			case "allatbasic" :
				if($row['mobilepay']=='y') return $this->cancel_allatbasic_mobile_request($ordno);
				else {
					$this->cancel_allatbasic_request($ordno,1);
					exit;
				}
			break;
			case "dacom" :
				$this->cancel_dacom_request($ordno);
				exit;
			break;
			case "lgdacom" :
				if($row['mobilepay']=='y') return $this->cancel_lgdacom_mobile($ordno);
				else return $this->cancel_lgdacom($ordno);
			break;
			case "inicis" :
				return $this->cancel_inicis($ordno,'������ �������');
			break;
			case "inipay" :
				return $this->cancel_inipay($ordno,'������ �������');
			break;
			case "kcp" :
				return $this->cancel_kcp($ordno,'������ �������');
			break;
			case "agspay" :
				return $this->cancel_agspay($ordno);
			break;
			case "easypay" :
				return $this->cancel_easypay($ordno);
			break;
			case "settlebank" :
				return $this->cancel_SettleBank($ordno);
			break;
		}
}

### �κ����
function partCancel_pg($ordno,$sno){
		$query = "select pgcancel from ".GD_ORDER_CANCEL." where ordno='".$ordno."' and sno='".$sno."'";
		$data = $GLOBALS['db']->fetch($query);
		
		// ��� ó�� �Ǿ����� üũ
		if ($data['pgcancel'] !== 'n') {
			return false;
		}
		else {}

		$row = $this->get_order_data($ordno);

		// ���հ��� ���� - 15.04.28 - su
		$order = new order();
		$order->load($ordno);
		$multitax = $order->getCancelItemTaxWithSno($sno);
		$this->tax = $multitax[kTAX];
		$this->vat = $multitax[kVAT];
		$this->taxfree = $multitax[kTAXFREE];

		switch($this->cfg['settlePg']){
			case "inicis" :
				return $this->partcancel_inicis($ordno,'ī����� �κ����',$sno,$this->repay);
			break;
			case "inipay" :
				return $this->partcancel_inipay($ordno,'ī����� �κ����',$sno,$this->repay);
			break;
			case "agspay" :
				return $this->partcancel_agspay($ordno,$sno,$this->repay);
			break;
			case "lgdacom" :
				return $this->partcancel_lgdacom($ordno,'ī����� �κ����',$sno,$this->repay);
			break;
			case "allat" :
				return $this->partcancel_allat($ordno,1,$sno,$this->repay);
			break;
			case "allatbasic" :
				return $this->partcancel_allatbasic($ordno,1,$sno,$this->repay);
			break;
			case "kcp" :
				return $this->partcancel_kcp($ordno,'ī����� �κ����',$sno,$this->repay);
			break;
			case "easypay" :
				return $this->partcancel_easypay($ordno,'ī����� �κ����',$sno,$this->repay);
			break;
		}
}

### �ֹ����� �ҷ�����
function get_order_data($ordno){
	if($ordno) return $GLOBALS['db'] -> fetch("select * from ".GD_ORDER." where ordno='".$ordno."'");
}

### ������� ��� dbó��
function cancel_db_proc($ordno,$tno=''){
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . "������� : PG�� ī����� ��� ���" . "\n-----------------------------------\n";
	if($tno)$field = ",cardtno='$tno'";
	$GLOBALS['db']->query("update ".GD_ORDER." set step='0',step2='51',settlelog=concat(ifnull(settlelog,''),'$settlelog')".$field." where ordno='$ordno'");
	$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set istep='51' where ordno='".$ordno."'");
}

### �ֹ����ó��
function cancel_proc($ordno,$msg){
	$res = $GLOBALS['db'] -> query("select * from ".GD_ORDER_ITEM." where ordno='".$ordno."'");
	while($item = $GLOBALS['db'] -> fetch($res)){
		$data['sno'][] = $item['sno'];
		$data['ea'][] = $item['ea'];
	}
	$data['memo'] = $msg;
	$data['name'] = $_COOKIE['member']['name'];
	$data['code'] = $this->cancel_code; // ��Ÿ

	### ȯ������ sno
	if($this->no_cancel != '' && !is_null($this->no_cancel)){
		$data['no_cancel'] = $this->no_cancel;
	}

	### �ֹ� ��� ó��
	chkCancel($ordno,$data);

	### ��ü��� �϶� ��� ����
	$GLOBALS['db'] -> query("update ".GD_ORDER." set pgcancel='y' where ordno='".$ordno."'");
	$GLOBALS['db'] -> query("update ".GD_ORDER_CANCEL." set pgcancel='y' where ordno='".$ordno."'");

	## ���� �α�
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $msg . "\n-----------------------------------\n";
	$GLOBALS['db'] -> query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
}

### pg �纰 ����� �ε�
function cancel_set_req($data){
	$cfg = $this -> cfg;
	$pg = $this -> cpg;
	$cfgMobileShop = $this -> cfgMobileShop;

	if(!$this->tpl){
		include_once $this->shopdir."/Template_/Template_.class.php";
		$this->tpl = new Template_;
	}
	$this->tpl->template_dir = $this->shopdir."/".$this->pg_dir."/".$cfg['settlePg'];
	$this->tpl->compile_dir = $this->shopdir."/Template_/_compiles/".$cfg['tplSkin']."/".$this->pg_dir."/".$cfg['settlePg'];
	$this->tpl->define('cancel',"cancel.htm");

	$this->tpl->assign($data);
	return $this->tpl->fetch('cancel');
}

### ��� üũ
function chkstock($goodsno,$opt1,$opt2,$ea){
	list($usestock) =  $GLOBALS['db'] -> fetch("select usestock from ".GD_GOODS." where goodsno='$goodsno' limit 1");
	if($usestock == "o"){
		list($goodsea) =  $GLOBALS['db'] -> fetch("select stock from ".GD_GOODS_OPTION." where goodsno='$goodsno' and opt1='".mysql_real_escape_string($opt1)."' and opt2='".mysql_real_escape_string($opt2)."' and go_is_deleted <> '1' limit 1");
		if($goodsea >= $ea) return true;
		else return false;
	}else{
		return true;
	}
}

### order item check stock
function chk_item_stock($ordno,$settlekind=''){
	$query = "select sum(ea) as ea,goodsno,opt1,opt2 from ".GD_ORDER_ITEM." where ordno='$ordno' group by goodsno,opt1,opt2";
	$res = $GLOBALS['db'] -> query($query);
	while($data = $GLOBALS['db'] -> fetch($res)){
		if(!$this->chkstock($data['goodsno'],$data['opt1'],$data['opt2'],$data['ea']))return false;
	}
	return true;
}

### �ÿ�ī�� ������ҽ�û
function cancel_allat_request($ordno,$actmode=0){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);
	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

### �ÿ�ī�� �κ� ������ҽ�û
function partcancel_allat($ordno,$actmode=0,$sno,$repay){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'sno' => $sno,
		'price' => $this->price,
		'repay' => $repay,
		'allat_amt' => $this->price,
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);

	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

### �ÿ�ī�� �κ� ������ҽ�û ����
function partcancel_allat_return($ordno,$a_sno,$settlelog,$a_price, $a_repay){
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $a_price;
	}else{
		$confirm_price = $row[settleprice] - $a_price;
	}
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$confirm_price."' where ordno='$ordno'");

	$rfee = (int)$a_repay-(int)$a_price;

	$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
		rprice = '".$a_price."',
		rfee = '".$rfee."',
		ccdt = '".date('Y:m:d H:i:s')."',
		pgcancel = 'r'
		where sno = '".$a_sno."'
	");

	return true;
}

// �ÿ�ī�� ������ҽ�û
function cancel_allatbasic_request($ordno,$actmode=0){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);
	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

// �ÿ�ī�� �κ� ������ҽ�û
function partcancel_allatbasic($ordno,$actmode=0,$sno,$repay){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'sno' => $sno,
		'price' => $this->price,
		'repay' => $repay,
		'allat_amt' => $this->price,
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno'],
		'rootDir' => $this->cfg[rootDir],
		'actmode' => $actmode
	);

	$out = $this->cancel_set_req($data);
	if($out) echo $out;
}

// �ÿ�ī�� �κ� ������ҽ�û ����
function partcancel_allatbasic_return($ordno,$a_sno,$settlelog,$a_price, $a_repay){
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $a_price;
	}else{
		$confirm_price = $row[settleprice] - $a_price;
	}
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$confirm_price."' where ordno='$ordno'");

	$rfee = (int)$a_repay-(int)$a_price;

	$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
		rprice = '".$a_price."',
		rfee = '".$rfee."',
		ccdt = '".date('Y:m:d H:i:s')."',
		pgcancel = 'r'
		where sno = '".$a_sno."'
	");

	return true;
}

### ������ ������ҽ�û
function cancel_dacom_request($ordno){
	$row = $this->get_order_data($ordno);
	$data = array(
		'mid'=>$this->cpg['id'],
		'oid'=>$ordno,
		'tid'=>$row['cardtno'],
		'ret_url'=>'http://'.$_SERVER['SERVER_NAME'].$this->cfg['rootDir'].'/'.$this->pg_dir.'/dacom/cancel_return.php',
		'hashdata'=> md5($this->cpg['id'].$ordno.$this->cpg['mertkey'])
	);

	$out = $this->cancel_set_req($data);

	if($out){
		echo $out;
		echo "<script>document.forms[0].submit();</script>";
	}
}

### �ÿ�ī��(�����) ������ҽ�û
function cancel_allat_mobile_request($ordno){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno']
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/allat/mobile/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

// �ÿ�ī��(�����) ������ҽ�û
function cancel_allatbasic_mobile_request($ordno){
	$row = $this->get_order_data($ordno);
	$r_sk = array('c'=>'CARD','o'=>'ABANK','v'=>'VBANK','h'=>'HP');
	$data = array(
		'allat_shop_id' => $this->cpg['id'],
		'allat_order_no' => $ordno,
		'allat_amt' => $row['settleprice'],
		'allat_pay_type' => $r_sk[$row['settlekind']],
		'allat_seq_no' => $row['cardtno']
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/allatbasic/mobile/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### LG������ ������ҽ�û
function cancel_lgdacom($ordno){
	$row = $this->get_order_data($ordno);

	if(!$this->cpg['serviceType']) $this->cpg['serviceType'] = "service";
	$data = array(
		'mid'		=> $this->cpg['id'],
		'service'	=> $this->cpg['serviceType'],
		'oid'		=> $ordno,
		'tid'		=> $row['cardtno'],
		'shopdir'	=> $this->shopdir
	);

	if ($row['settlekind'] == 'u') $data['mid'] = $this->cpg['cup_id'];	// �߱� ����ī�� ������ ���� ���̵� ��ü

	require_once($this->shopdir.'/'.$this->pg_dir.'/lgdacom/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### LG������ �κ����
function partcancel_lgdacom($ordno,$msg,$sno,$repay){
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $this->price;
	}else{
		$confirm_price = $row[settleprice] - $this->price;
	}

	if(!$this->cpg['serviceType']) $this->cpg['serviceType'] = "service";

	$data = array(
		'mid'		=> $this->cpg['id'],
		'service'	=> $this->cpg['serviceType'],
		'oid'		=> $ordno,
		'tid'		=> $row['cardtno'],
		'shopdir'	=> $this->shopdir,
		'taxfree'	=> $this->taxfree, // ����� �鼼�ݾ�
		'price'		=> $this->price // ����� �ݾ�
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/lgdacom/PartialCancel.php');

	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	if($cardCancelResult == true){
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$confirm_price."' where ordno='$ordno'");

		$rfee = (int)$repay-(int)$this->price;

		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$this->price."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}else{
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

### LG������ (�����) ������ҽ�û
function cancel_lgdacom_mobile($ordno){
	$row = $this->get_order_data($ordno);

	if(!$this->cpg['serviceType']) $this->cpg['serviceType'] = "service";
	$data = array(
		'mid'		=> $this->cpg['id'],
		'service'	=> $this->cpg['serviceType'],
		'oid'		=> $ordno,
		'tid'		=> $row['cardtno'],
		'shopdir'	=> $this->shopdir
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/lgdacom/mobile/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### �̴Ͻý� �������
function cancel_inicis($ordno,$msg){
	$row = $this->get_order_data($ordno);

	require_once($this->shopdir.'/'.$this->pg_dir.'/inicis/sample/INIpay41Lib.php');
	$inipay = new INIpay41;
	$inipay->m_inipayHome = $this->shopdir.'/'.$this->pg_dir.'/inicis/'; // �̴����� Ȩ���͸�
	$inipay->m_type = 'cancel'; // ����
	$inipay->m_pgId = 'INIpayRECP'; // ����
	$inipay->m_subPgIp = '203.238.3.10'; // ����
	$inipay->m_keyPw = '1111'; // Ű�н�����(�������̵� ���� ����)
	$inipay->m_debug = 'true'; // �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
	$inipay->m_mid = $this->cpg['id']; // �������̵�
	$inipay->m_tid = $row['cardtno']; // ����� �ŷ��� �ŷ����̵�
	$inipay->m_cancelMsg = $msg; // ��һ���
	$inipay->m_uip = getenv('REMOTE_ADDR'); // ����
	$inipay->startAction();

	##�����α� ����
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $inipay->m_resultMsg . "\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	if( !strcmp($inipay->m_resultCode,'00') ){
		$this -> cancel_proc($ordno,$msg);
		return true;
	}
	return false;
}

### �̴Ͻý� �κ����
function partcancel_inicis($ordno,$msg,$sno,$repay){ // �ֹ���ȣ, �޼���, ȯ���ֹ���sno, ȯ�������ȱݾ�
	$row = $this->get_order_data($ordno);

	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price = $row[repayprice] - $this->price;
	}else{
		$confirm_price = $row[settleprice] - $this->price;
	}

	require_once($this->shopdir.'/'.$this->pg_dir.'/inicis/sample/INIpay41Lib.php');
	$inipay = new INIpay41;
	$inipay->m_inipayHome = $this->shopdir.'/'.$this->pg_dir.'/inicis'; // �̴����� Ȩ���͸�
	$inipay->m_type = 'repay'; // ����
	$inipay->m_pgId = 'INIpayRPAY'; // ����
	$inipay->m_subPgIp = '203.238.3.10'; // ����
	$inipay->m_keyPw = '1111'; // Ű�н�����(�������̵� ���� ����)
	$inipay->m_debug = 'true'; // �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
	$inipay->m_mid = $this->cpg['id']; // �������̵�
	$inipay->m_oldTid = $row['cardtno']; // ���ŷ� TID
	$inipay->m_uip = getenv('REMOTE_ADDR'); // ����
	$inipay->m_currency = "WON"; // "WON" ���� ����
	$inipay->m_price = $this->price; // ����� �ݾ�
	$inipay->m_merchantReserved1 = "Tax=".$this->vat."&TaxFree=".$this->taxfree; //�ΰ��� & �鼼 ����
	$inipay->m_confirm_price = $confirm_price; // �κ���� �� ���ε� �ݾ�
	$inipay->m_buyerEmail = $row['email']; // ������ �̸��� �ּ�(����ÿ��� �Է�)
	$inipay->startAction();

	##�����α� ����
	$settlelog = "\n{$ordno} (" . date('Y:m:d H:i:s') . ") ".$msg."\n-----------------------------------\n" .
				"������� : ".$inipay->m_resultMsg."\n".
				"�Űŷ���ȣ : ".$inipay->m_tid."\n".
				"����αݾ� : ".$inipay->m_remains."\n".
				"��ұݾ� : ".$inipay->m_resultprice."\n".
				"�κ����(�����) ��û Ƚ�� : ".$inipay->m_cnt_partcancel."\n".
				"\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	if( !strcmp($inipay->m_resultCode,'00') ){

		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$inipay->m_remains."' where ordno='$ordno'");

		$rfee = (int)$repay-(int)$inipay->m_resultprice;

		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$inipay->m_resultprice."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");

		return true;
	} else {
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

/**
 * �̴Ͻý�(TX5) �������
 *
 * @param int $ordno �ֹ���ȣ
 * @param string $msg ��һ���
 * @return boolean true or false
 */
function cancel_inipay($ordno,$msg)
{
	//--- �ֹ� ����
	$row	= $this->get_order_data($ordno);

	//--- ���̺귯�� ��Ŭ���
	require_once($this->shopdir.'/'.$this->pg_dir.'/inipay/libs/INILib.php');

	//--- INIpay50 Ŭ������ �ν��Ͻ� ����
	$inipay	= new INIpay50;

	//--- ��� ���� ����
	$inipay->SetField('inipayhome',	$this->shopdir.'/'.$this->pg_dir.'/inipay');	// �̴����� Ȩ���͸�
	$inipay->SetField('type', 'cancel');											// ���� (���� ���� �Ұ�)
	$inipay->SetField('debug', 'true');												// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
	$inipay->SetField('mid', $this->cpg['id']);										// �������̵�
	$inipay->SetField('admin', '1111');												// ���Ī ���Ű Ű�н�����
	$inipay->SetField('tid', $row['cardtno']);										// ����� �ŷ��� �ŷ����̵�
	$inipay->SetField('cancelmsg', $msg);											// ��һ���

	//--- ��� ��û
	$inipay->startAction();
	/********************************************************************
	* ��� ���															*
	*																	*
	* ����ڵ� : $inipay->getResult('ResultCode') ("00"�̸� ��� ����)	*
	* ������� : $inipay->getResult('ResultMsg') (��Ұ���� ���� ����)	*
	* ��ҳ�¥ : $inipay->getResult('CancelDate') (YYYYMMDD)			*
	* ��ҽð� : $inipay->getResult('CancelTime') (HHMMSS)				*
	* ���ݿ����� ��� ���ι�ȣ : $inipay->getResult('CSHR_CancelNum')	*
	* (���ݿ����� �߱� ��ҽÿ��� ���ϵ�)								*
	********************************************************************/

	//--- �α� ����
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
	$settlelog	.= '�ŷ���ȣ : '.$row['cardtno'].chr(10);
	$settlelog	.= '����ڵ� : '.$inipay->GetResult('ResultCode').chr(10);
	$settlelog	.= '������� : '.$inipay->GetResult('ResultMsg').chr(10);
	$settlelog	.= '��ҳ�¥ : '.$inipay->GetResult('CancelDate').chr(10);
	$settlelog	.= '��ҽð� : '.$inipay->GetResult('CancelTime').chr(10);

	//--- ���ο��� / ���� ����� ���� ó�� ����
	if($inipay->GetResult('ResultCode') == "00"){
		// PG ���
		$getPgResult	= true;

		$settlelog	= '===================================================='.chr(10).'PG ��� ó�� : ��ҿϷ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	} else {
		// PG ���
		$getPgResult	= false;

		$settlelog	= '===================================================='.chr(10).'PG ��� ó�� : ��ҿ����ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	}

	//--- �����α� ����
	$GLOBALS['db']->query("UPDATE ".GD_ORDER." SET settlelog=concat(ifnull(settlelog,''),'$settlelog') WHERE ordno='$ordno'");

	//--- ��� ��� ó��
	if( $getPgResult === true ){
		$this -> cancel_proc($ordno,$msg);
		return true;
	}
	return false;
}

/**
 * �̴Ͻý�(TX5) �κ����
 *
 * @param int $ordno �ֹ���ȣ
 * @param string $msg ��һ���
 * @param int $sno ȯ���ֹ���sno
 * @param int $repay ȯ�������ȱݾ�
 * @return boolean true or false
 */
function partcancel_inipay($ordno,$msg,$sno,$repay)
{
	//--- �ֹ� ����
	$row	= $this->get_order_data($ordno);

	//--- �ֹ� ����� ���αݾ� ó��
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price	= $row['repayprice'] - $this->price;
	}else{
		$confirm_price	= $row['settleprice'] - $this->price;
	}

	//--- ���̺귯�� ��Ŭ���
	require_once($this->shopdir.'/'.$this->pg_dir.'/inipay/libs/INILib.php');

	//--- INIpay50 Ŭ������ �ν��Ͻ� ����
	$inipay	= new INIpay50;

	//--- ��� ���� ����
	$inipay->SetField('inipayhome',	$this->shopdir.'/'.$this->pg_dir.'/inipay');	// �̴����� Ȩ���͸�
	$inipay->SetField('type', 'repay');												// ���� (���� ���� �Ұ�)
	$inipay->SetField('pgid', 'INIphpRPAY');										// ���� (���� ���� �Ұ�)
	$inipay->SetField('subpgip','203.238.3.10');									// ����
	$inipay->SetField('debug', 'true');												// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
	$inipay->SetField('mid', $this->cpg['id']);										// �������̵�
	$inipay->SetField('admin', '1111');												// ���Ī ���Ű Ű�н�����
	$inipay->SetField('oldtid', $row['cardtno']);									// ����� �ŷ��� �ŷ����̵�
	$inipay->SetField('currency', 'WON');											// ȭ�����
	$inipay->SetField('price', $this->price);										// ��ұݾ�
	$inipay->SetField('tax', $this->vat);											// ��� �ΰ���
	$inipay->SetField('taxfree', $this->taxfree);									// ��� �鼼
	$inipay->SetField('confirm_price', $confirm_price);								// ���ο�û�ݾ�
	$inipay->SetField('buyeremail', $row['email']);									// ������ �̸��� �ּ�

	//--- ����� ��û
	$inipay->startAction();
	/*********************************************************************
	 * 5. ����� ���													 *
	 *                                                                   *
	 * �Űŷ���ȣ : $inipay->getResult('TID')                            *
	 * ����ڵ� : $inipay->getResult('ResultCode') ("00"�̸� ����� ����)*
	 * ������� : $inipay->getResult('ResultMsg') (����� ���� ����)     *
	 * ���ŷ� ��ȣ : $inipay->getResult('PRTC_TID')                      *
	 * �������� �ݾ� : $inipay->getResult('PRTC_Remains')                *
	 * �κ���� �ݾ� : $inipay->getResult('PRTC_Price')                  *
	 * �κ����,����� ���а� : $inipay->getResult('PRTC_Type')          *
	 *                          ("0" : �����, "1" : �κ����)           *
	 * �κ���� ��ûȽ�� : $inipay->getResult('PRTC_Cnt')                *
	*********************************************************************/

	//--- �α� ����
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
	$settlelog	.= '�ŷ���ȣ : '.$row['cardtno'].chr(10);
	$settlelog	.= '����ڵ� : '.$inipay->GetResult('ResultCode').chr(10);
	$settlelog	.= '������� : '.$inipay->GetResult('ResultMsg').chr(10);
	$settlelog	.= '�Űŷ���ȣ : '.$inipay->GetResult('TID').chr(10);
	$settlelog	.= '���ŷ� ��ȣ : '.$inipay->GetResult('PRTC_TID').chr(10);
	$settlelog	.= '�������� �ݾ� : '.$inipay->GetResult('PRTC_Remains').chr(10);
	$settlelog	.= '�κ���� �ݾ� : '.$inipay->GetResult('PRTC_Price').chr(10);
	$settlelog	.= '�κ���� ���� : '.$inipay->GetResult('PRTC_Type').'("0" : �����, "1" : �κ����)'.chr(10);
	$settlelog	.= '�κ���� ��ûȽ�� : '.$inipay->GetResult('PRTC_Cnt').chr(10);
	$settlelog	.= '��ҳ�¥ : '.date('Y-m-d H:i:s').chr(10);

	//--- ���ο��� / ���� ����� ���� ó�� ����
	if($inipay->GetResult('ResultCode') == "00"){
		// PG ���
		$getPgResult	= true;

		$settlelog	= '===================================================='.chr(10).'ī�� �κ� ��� ó�� : ��ҿϷ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	} else {
		// PG ���
		$getPgResult	= false;

		$settlelog	= '===================================================='.chr(10).'ī�� �κ� ��� ó�� : ��ҿ����ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
	}

	//--- �����α� ����
	$GLOBALS['db']->query("UPDATE ".GD_ORDER." SET settlelog=concat(ifnull(settlelog,''),'$settlelog') WHERE ordno='$ordno'");

	//--- ��� ��� ó��
	if( $getPgResult === true ){

		// �� ���� �ݾ�
		$GLOBALS['db']->query("UPDATE ".GD_ORDER." SET pgcancel = 'r' , repayprice = '".$inipay->GetResult('PRTC_Remains')."' WHERE ordno='$ordno'");

		// ���� ����� �ݾ�
		$rfee	= (int)$repay - (int)$inipay->GetResult('PRTC_Price');

		// ��� ����Ÿ ����
		$GLOBALS['db']->query("UPDATE ".GD_ORDER_CANCEL." SET
			rprice = '".$inipay->GetResult('PRTC_Price')."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			WHERE sno = '".$sno."'
		");

		return true;
	} else {
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

### KCP ������ҽ�û
function cancel_kcp($ordno,$msg){
	$row = $this->get_order_data($ordno);

	require_once $this->shopdir."/".$this->pg_dir."/kcp/pp_ax_hub_lib.php";                  // library [�����Ұ�]
	$c_PayPlus = new C_PP_CLI;
	$c_PayPlus->mf_clear();

	$c_PayPlus->mf_set_modx_data( "tno",      $row['cardtno']      ); // KCP ���ŷ� �ŷ���ȣ
	$c_PayPlus->mf_set_modx_data( "mod_type", "STSC" ); // ���ŷ� ���� ��û ����
	$c_PayPlus->mf_set_modx_data( "mod_ip",   $_SERVER['REMOTE_ADDR']  ); // ���� ��û�� IP
	$c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc ); // ���� ����

	// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	$c_PayPlus->mf_do_tx( $trace_no, $this->shopdir."/".$this->pg_dir."/kcp/payplus", $this->cpg['id'], $this->cpg['key'], '00200000', "",
												'paygw.kcp.co.kr', 8090, "payplus_cli_slib", $ordno,
												$_SERVER['REMOTE_ADDR'], 3, 0, 0 );

	$res_cd  = $c_PayPlus->m_res_cd;  // ��� �ڵ�
	$res_msg = $c_PayPlus->m_res_msg; // ��� �޽���

	if( !strcmp($res_cd,'0000') ){
		$this -> cancel_proc($ordno,$msg);
		return true;
	}
	return false;

}

### KCP �κ����
function partcancel_kcp($ordno,$msg,$sno,$repay){ // �ֹ���ȣ, �޼���, ȯ���ֹ���sno, ȯ�������ȱݾ�
	$row = $this->get_order_data($ordno);

	### ��Ұ��� �ܾ� ���
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$repayprice = $row[repayprice];
	}else{
		$repayprice = $row[settleprice];
	}

	$data = array(
		'mod_type'	=> 'RN07',					// �κ���ҿ�û ���� ���� ( �ſ�ī�� : RN07, ������ü : STPA )
		'tno'		=> $row['cardtno'],			// ���ŷ���ȣ
		'mod_desc'	=> $msg,					// ��һ���
		'mod_mny'	=> $this->price,			// ��ҿ�û�ݾ�
		'rem_mny'	=> $repayprice,				// ��� ���� �ܾ�
	);

	$crdata['req_tx'] = 'repay';
	require_once $this->shopdir."/".$this->pg_dir."/kcp/receipt/request/cash/pp_cli_hub.php";

	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	if($cardCancelResult == true){
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' , repayprice = '".$rem_mny."' where ordno='$ordno'");
		$rfee = (int)$repay-(int)$this->price;
		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$this->price."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}else{
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}

### �ô�����Ʈ ������ҽ�û
function cancel_agspay($ordno){
	$row = $this->get_order_data($ordno);

	if ($row['pgCardCd'] == '0100' || $row['pgCardCd'] == '0200') {
		$SubTy = 'isp'; // ����,BC (ISP��������)
		$rApprTm = substr($row['pgAppDt'], 0, 8); //8�ڸ�
	} else {
		$SubTy = 'visa3d'; // ����,�Ｚ,��ȯ,����,�Ե� �� ��Ÿī�� (�Ƚ�Ŭ��)
		$rApprTm = $row['pgAppDt']; //14�ڸ�
	}
	$data = array(
		'StoreId'	=> $this->cpg['id'],
		'SubTy'		=> $SubTy,
		'rApprNo'	=> $row['pgAppNo'],
		'rApprTm'	=> $rApprTm,
		'rDealNo'	=> $row['cardtno'],
		'rootDir'	=> $this->cfg[rootDir],
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/agspay/Cancel.php');

	if($cardCancelResult == true){
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}else{
		$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		return false;
	}
}

### �ô�����Ʈ �κ����
function partcancel_agspay($ordno,$sno,$repay){ // �ֹ���ȣ, ȯ���ֹ���sno, ȯ�������ȱݾ�
	$row = $this->get_order_data($ordno);

	if ($row['pgCardCd'] == '0100' || $row['pgCardCd'] == '0200') {
		$SubTy = 'isp'; // ����,BC (ISP��������)
		$rApprTm = substr($row['pgAppDt'], 0, 8); //8�ڸ�
	} else {
		$SubTy = 'visa3d'; // ����,�Ｚ,��ȯ,����,�Ե� �� ��Ÿī�� (�Ƚ�Ŭ��)
		$rApprTm = $row['pgAppDt']; //14�ڸ�
	}
	$data = array(
		'StoreId'		=> $this->cpg['id'],
		'SubTy'			=> $SubTy,
		'rApprNo'		=> $row['pgAppNo'],
		'rApprTm'		=> $rApprTm,
		'rDealNo'		=> $row['cardtno'],
		'cancelPrice'	=> $this->price, // ����� �ݾ�
		'rootDir'		=> $this->cfg[rootDir],
	);

	require_once($this->shopdir.'/'.$this->pg_dir.'/agspay/Cancel.php');

	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	if($cardCancelResult == true){
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r' where ordno='$ordno'");
		$rfee = (int)$repay-(int)$this->price;
		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$this->price."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}else{
		echo '<div style="font:9pt verdana;">'.nl2br($settlelog).'</div>';
		return false;
	}
}


### �������� ��ü���
function cancel_easypay($ordno){ // �ֹ���ȣ, �޼���, ȯ���ֹ���sno, ȯ�������ȱݾ�
	$row = $this->get_order_data($ordno);
	$tr_cd="00201000";	//�ŷ�����
	$mgr_txtype="40";		//�ŷ����� 40:��� 20:���Կ�û 30:������� 31:�κи������
	$org_cno=$row[cardtno];	//PG�ŷ���ȣ
	$client_ip= $_SERVER['REMOTE_ADDR'];

	include($this->shopdir.'/conf/config.php');
	include($this->shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/inc/easypay_config.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/easypay_client.php');
	$easyPay = new EasyPay_Client;
	$easyPay->clearup_msg();

	$easyPay->set_home_dir($g_home_dir);
	$easyPay->set_gw_url($g_gw_url);
	$easyPay->set_gw_port($g_gw_port);
	$easyPay->set_log_dir($g_log_dir);
	$easyPay->set_log_level($g_log_level);
	$easyPay->set_cert_file($g_cert_file);

	 /* ::: ������� ��û                                                      */
    /* ---------------------------------------------------------------------- */
    $mgr_data = $easyPay->set_easypay_item("mgr_data");
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , $mgr_txtype       );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , $mgr_subtype      );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $org_cno          );
    $easyPay->set_easypay_deli_us( $mgr_data, "pay_type"        , $pay_type         );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_amt"         , $mgr_amt          );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_bank_cd"     , $mgr_bank_cd      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_account"     , $mgr_account      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_depositor"   , $mgr_depositor    );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_socno"       , $mgr_socno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_telno"       , $mgr_telno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_corp_cd"    , $deli_corp_cd     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_invoice"    , $deli_invoice     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_nm"     , $deli_rcv_nm      );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_tel"    , $deli_rcv_tel     );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );

	/* -------------------------------------------------------------------------- */
	/* ::: ����                                                                   */
	/* -------------------------------------------------------------------------- */
	$opt = "option value";
	$easyPay->easypay_exec($g_mall_id, $tr_cd, $ordno, $client_ip, $opt);
	$res_cd  = $easyPay->_easypay_resdata["res_cd"];    // �����ڵ�
	$res_msg = $easyPay->_easypay_resdata["res_msg"];   // ����޽���
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $res_msg . "\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	if($res_cd=='0000') {						//��������
		##�����α� ����
		$this -> cancel_proc($ordno,$settlelog);
		return true;
	}
	return false;

}


### �������� �κн������
function partcancel_easypay($ordno,$msg,$sno,$repay){ // �ֹ���ȣ, �޼���, ȯ���ֹ���sno, ȯ�������ȱݾ�
	//--- �ֹ� ����
	$row = $this->get_order_data($ordno);

	//--- �ֹ� ����� ���αݾ� ó��
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$confirm_price	= $row['repayprice'] - $this->price;
	}else{
		$confirm_price	= $row['settleprice'] - $this->price;
	}


	$tr_cd="00201000";	//�ŷ�����
	$mgr_txtype="31";		//�ŷ����� 40:��� 20:���Կ�û 30:������� 31:�κи������
	$org_cno=$row[cardtno];	//PG�ŷ���ȣ
	$mgr_amt=$repay;					//�κ����/ȯ�� �ݾ�
	$mgr_rem_amt = $confirm_price		;		//�κ����	�ܾ�
	$client_ip= $_SERVER['REMOTE_ADDR'];

	include($this->shopdir.'/conf/config.php');
	include($this->shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/inc/easypay_config.php');
	require_once($this->shopdir.'/'.$this->pg_dir.'/easypay/easypay_client.php');
	$easyPay = new EasyPay_Client;
	$easyPay->clearup_msg();

	$easyPay->set_home_dir($g_home_dir);
	$easyPay->set_gw_url($g_gw_url);
	$easyPay->set_gw_port($g_gw_port);
	$easyPay->set_log_dir($g_log_dir);
	$easyPay->set_log_level($g_log_level);
	$easyPay->set_cert_file($g_cert_file);

	 /* ::: ������� ��û                                                      */
    /* ---------------------------------------------------------------------- */
    $mgr_data = $easyPay->set_easypay_item("mgr_data");
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , $mgr_txtype       );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , $mgr_subtype      );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $org_cno          );
    $easyPay->set_easypay_deli_us( $mgr_data, "pay_type"        , $pay_type         );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_amt"         , $mgr_amt          );
	$easyPay->set_easypay_deli_us( $mgr_data, "mgr_rem_amt"         , $mgr_rem_amt          );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_bank_cd"     , $mgr_bank_cd      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_account"     , $mgr_account      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_depositor"   , $mgr_depositor    );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_socno"       , $mgr_socno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_telno"       , $mgr_telno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_corp_cd"    , $deli_corp_cd     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_invoice"    , $deli_invoice     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_nm"     , $deli_rcv_nm      );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_tel"    , $deli_rcv_tel     );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );

	/* -------------------------------------------------------------------------- */
	/* ::: ����                                                                   */
	/* -------------------------------------------------------------------------- */
	$opt = "option value";
	$easyPay->easypay_exec($g_mall_id, $tr_cd, $ordno, $client_ip, $opt);
	$res_cd  = $easyPay->_easypay_resdata["res_cd"];    // �����ڵ�
	$res_msg = $easyPay->_easypay_resdata["res_msg"];   // ����޽���
	$amount=  $easyPay->_easypay_resdata["amount"];				// ��Ұ����ݾ�
	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . $msg . "\n-----------------------------------\n";
	$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

	### ��Ұ��� �ܾ� ���
	if (empty($row['repayprice']) === false && $row['pgcancel'] == 'r') {
		$repayprice = $row[repayprice];
	}else{
		$repayprice = $row[settleprice];
	}


	if($res_cd=='0000') {						//��������
		##�����α� ����
		$GLOBALS['db']->query("update ".GD_ORDER." set pgcancel = 'r', repayprice = '".$amount."' where ordno='$ordno'");
		$rfee = (int)$repay-(int)$this->price;
		$GLOBALS['db']->query("update ".GD_ORDER_CANCEL." set
			rprice = '".$amount."',
			rfee = '".$rfee."',
			ccdt = '".date('Y:m:d H:i:s')."',
			pgcancel = 'r'
			where sno = '".$sno."'
		");
		return true;
	}
	else{
		echo "�����ڵ� : $res_cd <br/>";
		echo "����޽��� : $res_msg <br/>";


	}
	return false;
}

### ��Ʋ��ũ Spay ������ҽ�û
function cancel_SettleBank($ordno){
	//�ֹ������� 
	$row = $this->get_order_data($ordno);
	
	$data = array(
		'mid'=>$this->cpg['id'],
		'p_tr_no'=>$row['cardtno'],
		'pg_auth_key'=>$this->cpg['key'],
		'passwd'=>md5($row['cardtno'].$this->cpg['key']),
		'refund_list'=>$row['cardtno'].":".$row['settleprice'],
		'rootDir'=>$this->cfg['rootDir']
	);

	$out = $this->cancel_set_req($data);

	if($out){
		echo $out;
		echo "<script>document.forms[0].submit();</script>";
	}
}

}
?>