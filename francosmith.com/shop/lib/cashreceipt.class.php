<?
class cashreceipt
{
	var $cfg, $pg;
	var $r_status = array('RDY' => '�߱޿�û', 'ACK' => '�߱޿Ϸ�', 'CCR' => '�߱����', 'RFS' => '�߱ް���');
	var $r_useopt = array('0' => '�ҵ������', '1' => '����������');
	var $autoCrno = array();

	function cashreceipt()
	{
		include dirname(__FILE__).'/../conf/config.php';
		include dirname(__FILE__).'/../conf/config.pay.php';
		if ($cfg['settlePg'] !== '' && file_exists(dirname(__FILE__).'/../conf/pg.'. $cfg['settlePg'] .'.php')){
			include dirname(__FILE__).'/../conf/pg.'. $cfg['settlePg'] .'.php';
		}

		$this->cfg = $cfg;
		$this->set = $set;
		$this->pg = $pg;
	}

	### �߱޽�û������
	function putReceipt($indata)
	{
		//��ǰ�� Ư������ �� �±� ����
		$indata['goodsnm'] = pg_text_replace(strip_tags($indata['goodsnm']));
		$indata['goodsnm'] = strcut($indata['goodsnm'],30);
		if ($indata['buyerphone'] == '--') $indata['buyerphone'] = '';

		if (strlen($indata['certno']) == 13)
		{
			$certno_encode = encode(substr($indata['certno'],6,7),1);
			$indata['certno'] = substr($indata['certno'],0,6);
		}

		$regdt = ($indata['regdt'] ? "'{$indata['regdt']}'" : "now()");

		$query = "
		insert into ".GD_CASHRECEIPT." set
			`ordno` = '{$indata['ordno']}',
			`goodsnm` = '{$indata['goodsnm']}',
			`buyername` = '{$indata['buyername']}',
			`buyeremail` = '{$indata['buyeremail']}',
			`buyerphone` = '{$indata['buyerphone']}',
			`useopt` = '{$indata['useopt']}',
			`certno` = '{$indata['certno']}',
			`certno_encode` = '{$certno_encode}',
			`amount` = '{$indata['amount']}',
			`supply` = '{$indata['supply']}',
			`surtax` = '{$indata['surtax']}',
			`ip` = '{$_SERVER['REMOTE_ADDR']}',
			`regdt` = {$regdt},
			`moddt` = {$regdt},
			`status` = 'RDY',
			`singly` = '{$indata['singly']}'
		";
		// ���հ��� ����! LG+
		if($this->cfg['settlePg'] == 'lgdacom' || $this->cfg['settlePg'] =='inicis' || $this->cfg['settlePg'] =='inipay'){
			$query .= ", `taxfree` = '{$indata['taxfree']}'";
		}
		$GLOBALS['db']->query($query);

		return $GLOBALS['db']->lastID();
	}

	### �߱޽�û������(�ֹ���/����������)
	function putUserReceipt($rdata)
	{
		$indata = $this->getOrder($rdata['ordno']);
		$indata['useopt'] = $rdata['useopt'];
		$indata['certno'] =$rdata['certno'];
		return $resid = $this->putReceipt($indata);
	}

	### �߱��ʿ� �ֹ�������
	function getOrder($ordno)
	{
		global $db;

		$query = "select * from ".GD_ORDER." where ordno='{$ordno}'";
		$data = $db->fetch($query);

		## ��ǰ��
		list($icnt) = $db->fetch("select count(*) from ".GD_ORDER_ITEM." where istep < 40 and ordno='{$data['ordno']}'");
		list($goodsnm) = $db->fetch("select goodsnm from ".GD_ORDER_ITEM." where istep < 40 and ordno='{$data['ordno']}' order by sno");

		$cutLen = 30;
		if ($icnt > 1){
			$cntStr = ' �� '.($icnt-1).'��';
			$cutLen -= strlen($cntStr) + 2;
		}
		$goodsnm = strcut($goodsnm,$cutLen) . $cntStr;

		$caseReceipt = array();
		$caseReceipt = $this->getCashReceiptCalCulate($ordno);

		$indata = array();
		$indata['ordno'] = $data['ordno'];
		$indata['goodsnm'] = $goodsnm;
		$indata['buyername'] = $data['nameOrder'];
		$indata['buyeremail'] = $data['email'];
		$indata['buyerphone'] = str_replace('-','',$data['mobileOrder']);
		$indata['amount'] = $caseReceipt['caseReceiptAmount'];
		$indata['supply'] = $caseReceipt['supply'];
		$indata['tax'] = $caseReceipt['tax'];
		$indata['surtax'] = $caseReceipt['vat'];
		$indata['taxfree'] = $caseReceipt['taxfree'];
		$indata['mobileOrder'] = $data['mobileOrder'];

		return $indata;
	}

	### �߱�
	function pgApproval()
	{
		global $db;

		$crdata = $db->fetch("select * from ".GD_CASHRECEIPT." where crno='{$_GET['crno']}'");

		if ($this->cfg['settlePg'] == '')
		{
			$this->errMsg = '���θ��⺻����" ���� ��������(PG)�� ���� ��û/�����ϼ���.';
			return false;
		}
		else if ($this->pg['receipt'] != 'Y')
		{
			$this->errMsg = '"���ݿ����� �߱޼���" ���� ���ݿ����� ��뿩�θ� ���� �����ϼ���.';
			return false;
		}
		else if ($crdata['status'] != 'RDY')
		{
			$this->errMsg = '�߱޽�û������ ó�����°� [�߱޿�û] ��쿡�� �߱��� �� �ֽ��ϴ�.';
			return false;
		}

		### ��������
		if ($crdata['certno_encode']){
			$crdata['certno'].= decode($crdata['certno_encode'],1);
		}

		### �߱�����
		if ($this->cfg['settlePg'] == 'allat')
		{
			$type = 'NBANK';

			$query = "select settlekind, settlelog from ".GD_ORDER." where ordno='{$crdata['ordno']}'";
			$odata = $db->fetch($query);
			if($odata['settlekind'] == 'o' && preg_match('/�ŷ���ȣ : (.*)/', $odata['settlelog'], $matched)){
				$tno = $matched[1];
				$type = 'ABANK';
			}
			include dirname(__FILE__).'/../order/card/allat/allat_cashapproval.php';
		}
		else if ($this->cfg['settlePg'] == 'allatbasic')
		{
			$type = 'NBANK';

			$query = "select settlekind, settlelog from ".GD_ORDER." where ordno='{$crdata['ordno']}'";
			$odata = $db->fetch($query);
			if($odata['settlekind'] == 'o' && preg_match('/�ŷ���ȣ : (.*)/', $odata['settlelog'], $matched)){
				$tno = $matched[1];
				$type = 'ABANK';
			}
			include dirname(__FILE__).'/../order/card/allatbasic/allat_cashapproval.php';
		}
		else if ($this->cfg['settlePg'] == 'inicis')
		{
			include dirname(__FILE__).'/../order/card/inicis/sample/INIreceipt.php';
		}
		else if ($this->cfg['settlePg'] == 'inipay')
		{
			include dirname(__FILE__).'/../order/card/inipay/INIreceipt.php';
		}
		else if ($this->cfg['settlePg'] == 'dacom')
		{
			$crdata['method'] = 'auth';
			include dirname(__FILE__).'/../order/card/dacom/cashreceipt.php';
		}
		else if ($this->cfg['settlePg'] == 'lgdacom')
		{
			$crdata['method'] = 'auth';
			include dirname(__FILE__).'/../order/card/lgdacom/CashReceipt.php';
		}
		else if ($this->cfg['settlePg'] == 'kcp')
		{
			$crdata['req_tx'] = 'pay';
			include dirname(__FILE__).'/../order/card/kcp/receipt/request/cash/pp_cli_hub.php';
		}
		else if ($this->cfg['settlePg'] == 'agspay')
		{
			$crdata['Pay_kind'] = 'cash-appr';
			include dirname(__FILE__).'/../order/card/agspay/AGSCash_ing.php';
		}
		else if ($this->cfg['settlePg'] == 'settlebank')
		{
			$crdata['Pay_kind'] = 'cash-appr';
			include dirname(__FILE__).'/../order/card/settlebank/settleCash_ing.php';
		}
		else if ($this->cfg['settlePg'] == 'easypay')
		{
			//--------���ο��� ���ۿ����� �߱�-----------------
			$crdata['EP_tr_cd'] = '00201050';	//���� ���ݿ����� �߱�����
			$crdata['pay_type'] = 'cash';
			$crdata['EP_req_type'] = 'issue';
			include dirname(__FILE__).'/../order/card/easypay/cash_receipt.php';
		}

	}

	### ���
	function pgCancel()
	{
		global $db;

		$crdata = $db->fetch("select * from ".GD_CASHRECEIPT." where crno='{$_GET['crno']}'");

		if ($this->cfg['settlePg'] == '')
		{
			$this->errMsg = '���θ��⺻����" ���� ��������(PG)�� ���� ��û/�����ϼ���.';
			return false;
		}
		else if ($this->pg['receipt'] != 'Y')
		{
			$this->errMsg = '"���ݿ����� �߱޼���" ���� ���ݿ����� ��뿩�θ� ���� �����ϼ���.';
			return false;
		}
		else if ($crdata['status'] != 'ACK')
		{
			$this->errMsg = '�߱޽�û������ ó�����°� [�߱޿Ϸ�] ��쿡�� ����� �� �ֽ��ϴ�.';
			return false;
		}

		### ��������
		if ($crdata['certno_encode']){
			$crdata['certno'].= decode($crdata['certno_encode'],1);
		}

		### �������
		if ($this->cfg['settlePg'] == 'allat')
		{
			include dirname(__FILE__).'/../order/card/allat/allat_cashcancel.php';
		}
		else if ($this->cfg['settlePg'] == 'allatbasic')
		{
			include dirname(__FILE__).'/../order/card/allatbasic/allat_cashcancel.php';
		}
		else if ($this->cfg['settlePg'] == 'inicis')
		{
			include dirname(__FILE__).'/../order/card/inicis/sample/INIcancel.php';
		}
		else if ($this->cfg['settlePg'] == 'inipay')
		{
			include dirname(__FILE__).'/../order/card/inipay/INIreceiptCancel.php';
		}
		else if ($this->cfg['settlePg'] == 'dacom')
		{
			$crdata['method'] = 'cancel';
			include dirname(__FILE__).'/../order/card/dacom/cashreceipt.php';
		}
		else if ($this->cfg['settlePg'] == 'lgdacom')
		{
			$crdata['method'] = 'cancel';
			include dirname(__FILE__).'/../order/card/lgdacom/CashReceipt.php';
		}
		else if ($this->cfg['settlePg'] == 'kcp')
		{
			$crdata['req_tx'] = 'mod';
			include dirname(__FILE__).'/../order/card/kcp/receipt/request/cash/pp_cli_hub.php';
		}
		else if ($this->cfg['settlePg'] == 'agspay')
		{
			$crdata['Pay_kind'] = 'cash-cncl';
			include dirname(__FILE__).'/../order/card/agspay/AGSCash_ing.php';
		}
		else if ($this->cfg['settlePg'] == 'settlebank')
		{
			$crdata['Pay_kind'] = 'cash-cncl';
			include dirname(__FILE__).'/../order/card/settlebank/settleCash_ing.php';
		}
		else if ($this->cfg['settlePg'] == 'easypay')
		{
			$crdata['Pay_kind'] = 'cash-cncl';
			include dirname(__FILE__).'/../order/card/easypay/cash_cancel.php';
		}
	}

	### �ڵ��߱�
	function autoApproval($ordno)
	{
		global $db;

		if ($this->set['receipt']['publisher'] != 'seller') return;
		if ($this->set['receipt']['auto'] != 'Y') return;

		list($crno) = $db->fetch("select crno from ".GD_CASHRECEIPT." where ordno='{$ordno}' and status='RDY' order by crno desc limit 1");
		if ($crno != '') $this->autoCrno[] = $crno;
	}

	### �ڵ����
	function autoCancel($ordno)
	{
		global $db;

		if ($this->set['receipt']['publisher'] != 'seller') return;
		if ($this->set['receipt']['auto'] != 'Y') return;

		list($cashreceipt) = $db->fetch("select cashreceipt from ".GD_ORDER." where ordno='{$ordno}'");
		list($crno) = $db->fetch("select crno from ".GD_CASHRECEIPT." where ordno='{$ordno}' and cashreceipt='{$cashreceipt}' and status='ACK' order by crno desc limit 1");
		if ($crno != ''){
			$this->autoCrno[] = $crno;
		}
		else {
			$db->query("update ".GD_CASHRECEIPT." set moddt=now(),status='RFS' where ordno='{$ordno}'");
		}
	}

	### �ڵ�����
	function autoAction($mode)
	{
		if (count($this->autoCrno) == 0) return;
		foreach ($this->autoCrno as $crno)
		{
			if (empty($crno) === false) {
				$_GET['crno']	= $crno;	// ������ GET ������ ó���� �Ǿ����Ƿ� GET ������ ��Ƽ� ó��
				if ($mode == 'approval') {
					$this->pgApproval();
				}
				else if ($mode == 'cancel') {
					$this->pgCancel();
				}
				unset($_GET['crno']);
			}
		}
	}

	### �������ּ�
	function getReceipturl($crno, $mode='')
	{
		global $db;

		if ($mode == 'ordno')
		{
			$ordno = $crno;
			list($cashreceipt, $mobileOrder, $settlekind, $cdt) = $db->fetch("select cashreceipt, mobileOrder, settlekind from ".GD_ORDER." where ordno='{$ordno}'");
			if ($cashreceipt != '') $where = "or (cashreceipt='{$cashreceipt}' and status='ACK')";
			$data = $db->fetch("select crno, pg, tid, certno, ordno, receiptnumber, moddt from ".GD_CASHRECEIPT." where ordno='{$ordno}' {$where} order by crno desc limit 1");
			if ($data['crno'] == ''){
				$data['pg'] = $this->cfg['settlePg'];
				$data['tid'] = $cashreceipt;
				$data['certno'] = str_replace('-','',$mobileOrder);
				$data['ordno'] = $ordno;
				$data['receiptnumber'] = $cashreceipt;
				$data['moddt'] = $cdt;
			}
		}
		else {
			$data = $db->fetch("select crno, pg, tid, certno, ordno, receiptnumber, moddt from ".GD_CASHRECEIPT." where crno='{$crno}'");
		}

		if ($data['pg'] == 'allat' && $data['tid'] && $data['certno'])
		{
			if ($data['crno'] == '' && ($settlekind == 'o' || $settlekind == 'v'));
			else {
				$url = 'https://www.allatpay.com/servlet/AllatBizPop/member/pop_cash_receipt.jsp?receipt_seq_no='.$data['tid'].'&cert_no='.$data['certno'];
			}
		}
		else if ($data['pg'] == 'allatbasic' && $data['tid'] && $data['certno'])
		{
			if ($data['crno'] == '' && ($settlekind == 'o' || $settlekind == 'v'));
			else {
				$url = 'https://www.allatpay.com/servlet/AllatBizPop/member/pop_cash_receipt.jsp?receipt_seq_no='.$data['tid'].'&cert_no='.$data['certno'];
			}
		}
		else if ($data['pg'] == 'inicis' && $data['tid'])
		{
			$url = 'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid='.$data['tid'].'&clpaymethod=22';
		}
		else if ($data['pg'] == 'inipay' && $data['tid'])
		{
			$url = 'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid='.$data['tid'].'&clpaymethod=22';
		}
		else if ($data['pg'] == 'dacom'||$data['pg'] == 'lgdacom')
		{
			// ��û URL
			// ���񽺿� : http://pg.dacom.net/transfer/cashreceipt.jsp
			// �׽�Ʈ�� : http://pg.dacom.net:7080/transfer/cashreceipt.jsp

			if (file_exists(dirname(__FILE__).'/../conf/pg.dacom.php')) include dirname(__FILE__).'/../conf/pg.dacom.php';
			$paramStr = 'orderid='.$data['ordno'].'&mid='.$this->pg['id'];

			if ($data['crno'] == '' && $settlekind == 'o') $paramStr .= '&servicetype=SC0030'; // ������ü
			else if ($data['crno'] == '' && $settlekind == 'v') $paramStr .= '&servicetype=SC0040&seqno=001'; // �������Ա�(�������)
			else $paramStr .= '&servicetype=SC0100'; // ��ü �������Ա�

			$url = 'http://pg.dacom.net/transfer/cashreceipt.jsp?'.$paramStr;
		}
		else if ($data['pg'] == 'kcp' && $data['tid'])
		{
			if ($data['crno'] == '' && ($settlekind == 'o' || $settlekind == 'v'));
			else {
				$url = 'http://admin.kcp.co.kr/Modules/Service/Cash/Cash_Bill_Common_View.jsp?cash_no='.$data['tid'];
			}
		}
		else if ($data['pg'] == 'agspay' && $data['receiptnumber'])
		{
			if ($data['crno'] == '' && ($settlekind == 'o' || $settlekind == 'v'));
			else {
				$send_dt = str_replace('-','',substr($data['moddt'],0,10));
				$url = 'http://www.allthegate.com/receipt/receipt_cash.jsp?service_id='.$this->pg['id'].'&send_dt='.$send_dt.'&adm_no='.$data['receiptnumber'];
			}
		}
		else if ($data['pg'] == 'easypay' && $data['receiptnumber'])
		{
			if ($data['crno'] == '' && ($settlekind == 'o' || $settlekind == 'v'));
			else {
				$send_dt = str_replace('-','',substr($data['moddt'],0,10));
				$url = "http://office.easypay.co.kr/receipt/ReceiptBranch.jsp?controlNo=".$data[tid]."&payment=���ݿ�����";
			}
		}
		else if ($data['pg'] == 'settlebank' && $data['ordno'])
		{
			if ($data['crno'] == '' && ($settlekind == 'o' || $settlekind == 'v')) {
			} else {
				$url = "http://pg.settlebank.co.kr/common/CommonMultiAction.do?_method=RcptView&mid=".$this->pg['id']."&ordNo=".$data['ordno']."&svcCd=CSH";
			}
		}

		return $url;
	}

	### ������� �ֹ��󼼳��� ���ݿ����� ���
	function prnAdminReceipt($ordno)
	{
		global $db;

		$prn = array();
		list($cashreceipt, $cashreceipt_ectway) = $db->fetch("select cashreceipt, cashreceipt_ectway from ".GD_ORDER." where ordno='{$ordno}'");

		if ($cashreceipt != '') $where = "or (cashreceipt='{$cashreceipt}' and status='ACK')";
		$crdata = $db->fetch("select * from ".GD_CASHRECEIPT." where ordno='{$ordno}' {$where} order by crno desc limit 1");

		if ($crdata['crno']) // ��û�����ִ�
		{
			$prn[] = '<font class="small1">'.$this->r_status[ $crdata['status'] ].'</font>';

			if (in_array($crdata['status'], array('ACK','CCR')))
			{
				if ($cashreceipt != '') $prn[] = $cashreceipt;
				else if ($crdata['tid'] != '') $prn[] = $crdata['tid'];
				else if ($crdata['receiptnumber'] != '') $prn[] = $crdata['receiptnumber'];

				$receipturl =  $this->getReceipturl($crdata['crno']);
				if ($receipturl == '')
					$prn[] = '<span class="hand" onclick="alert(\'������ ����� �������� �ʽ��ϴ�.\');"><img src="../img/i_receipt_off.gif"></span>';
				else
					$prn[] = '<span class="hand" onclick="window.open(\''.$receipturl.'\',\'\',\'width=400,height=600,scrollbars=0\');"><img src="../img/i_receipt_on.gif"></span>';
			}
		}
		else if ($cashreceipt != '') // ��û�������� ��������ȣ�ִ�
		{
			$prn[] = $cashreceipt;

			$receipturl =  $this->getReceipturl($ordno, 'ordno');
			if ($receipturl != '')
				$prn[] = '<span class="hand" onclick="window.open(\''.$receipturl.'\',\'\',\'width=400,height=600,scrollbars=0\');"><img src="../img/i_receipt_on.gif"></span>';
		}

		if ($crdata['crno'] == '' && $cashreceipt == '' && $this->pg['receipt'] == 'Y' && $cashreceipt_ectway != 'Y'){
			$prn[] = '<a href="javascript:popupLayer(\'./popup.cashreceiptOrder.php?ordno='.$ordno.'\', 650, 500)"><img src="../img/btn_cashreceipt_app.gif"></a>';
		}
		else if (in_array($crdata['status'], array('CCR','RFS')) && $this->pg['receipt'] == 'Y' && $cashreceipt_ectway != 'Y')
		{
			$prn[] = '<a href="javascript:popupLayer(\'./popup.cashreceiptOrder.php?ordno='.$ordno.'\', 650, 500)"><img src="../img/btn_cashreceipt_reapp.gif"></a>';
		}

		if ($crdata['crno'])
		{
			$prn[] = '<a href="../order/cashreceipt.list.php?skey=ordno&sword='.$ordno.'"><img src="../img/btn_more.gif"></a>';
		}

		return implode(' ', $prn);
	}

	### ������� �ֹ�������
	function prnUserReceipt($ordno)
	{
		global $db;

		list($cashreceipt) = $db->fetch("select cashreceipt from ".GD_ORDER." where ordno='{$ordno}'");
		$query = "select crno,useopt,moddt,receiptnumber,tid,status from ".GD_CASHRECEIPT." where ordno='{$ordno}' order by crno desc";
		$res = $db->query($query);
		$cnt = $db->count_($res);
		while ($sub=$db->fetch($res,1))
		{
			# �߱޿뵵
			$sub['useoptStr'] = $this->r_useopt[ $sub['useopt'] ];
			# ó������
			$sub['statusStr'] = $this->r_status[ $sub['status'] ];
			# ������
			if ($sub['receiptnumber'] != '' && $this->getReceipturl($sub['crno']) != '') $sub['printable'] = 'crno='.$sub['crno'];

			$this->list[] = $sub;
			if ($sub['status']=='RDY' || $sub['status']=='ACK') $doCnt++;
		}

		if (($cnt && $doCnt <= 0) || ($cnt == 0 && $cashreceipt == '')) $this->writeable = true;
		if ($this->getReceipturl($ordno, 'ordno')) $this->printable = 'ordno='.$ordno;
	}

	//get realsettleprice
	function getCashReceiptCalCulate($ordno)
	{
		global $db, $order;

		$multitax = $cashReceipt = array();
		$realPrn_settleprice = 0;
		if(!is_object($order)){
			$order = Core::loader('order');
			$order->load($ordno);
		}
		$multitax = $order->getRealTaxAmounts(0, true);

		$cashReceipt['caseReceiptAmount'] = $multitax['taxall'] + $multitax['taxfree'];
		if($this->cfg['settlePg'] == 'lgdacom' || $this->cfg['settlePg'] =='inicis' || $this->cfg['settlePg'] =='inipay'){
			$cashReceipt['supply'] = $multitax['tax']+$multitax['taxfree'];
			$cashReceipt['tax'] = $multitax['tax'];
			$cashReceipt['vat'] = $multitax['vat'];
			$cashReceipt['taxfree'] = $multitax['taxfree'];
		}
		else {
			if ($this->set['receipt']['compType'] == '1'){ // �鼼/���̻����
				$cashReceipt['supply'] = $cashReceipt['caseReceiptAmount'];
				$cashReceipt['vat'] = 0;
			}
			else { // ���������
				$cashReceipt['supply'] = round($cashReceipt['caseReceiptAmount'] / 1.1);
				$cashReceipt['vat'] = $cashReceipt['caseReceiptAmount'] - $cashReceipt['supply'];
			}
			$cashReceipt['cashreceipt'] = $order['cashreceipt'];
		}

		return $cashReceipt;
	}
}

?>