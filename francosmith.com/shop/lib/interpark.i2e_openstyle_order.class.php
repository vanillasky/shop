<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/xmlWriter.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";

class i2e_order_api extends putLog
{
	var $interfaceid;	# �������̽� ID

	function i2e_order_api($isTest=false)
	{
		$this->putLog($_GET['mode']);
		$this->log("START");

		## �������̽� ID ����
		$this->interfaceid = $_GET['mode'];

		## API START
		$this->chkGetParameter();
		$strXml = $this->socketGodo($_GET['dataUrl']);
		$arrXml = $this->parseXml($strXml);

		switch ($this->interfaceid)
		{
			case "orderListForMulti": # �ֹ�������ȸ
			case "orderListDelvForMulti": # �ֹ�������ȸ2
				$this->orderListForMulti($arrXml);
				break;

			case "cnclNClmReqListForMulti": # �ֹ����/��ǰ/��ȯ��û����Ʈ
				$this->cnclNClmReqListForMulti($arrXml);
				break;

			case "clmListForMulti": # Ŭ���Ӹ���Ʈ
				$this->clmListForMulti($arrXml);
				break;

			case "orderCompListForMulti": # ����Ȯ����ȸ
				$this->orderCompListForMulti($arrXml);
				break;
		}

		$this->log("END");
	}

	### Check Get Parameter
	function chkGetParameter()
	{
		$this->log("Check_Get_Parameter START");
		if ($_GET['dataUrl'] == ''){
			$msg = array(
				"log" => array('Empty GET[dataUrl]', 'Check_Get_Parameter END'),
				"header" => "�Ķ������ �����Ͱ�ΰ� �������� �ʽ��ϴ�.."
				);
			$this->endHeader($msg, 400);
		}
		$this->log("Check_Get_Parameter END");
	}

	### Socket to Godo
	function socketGodo($dataUrl)
	{
		$this->log("Connection_Godo START");
		$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $dataUrl)));

		ob_start();
		if (class_exists('httpSock'))
		{
			$httpSock = new httpSock($dataUrl);
			$httpSock->send(true);
		}
		else $this->log("Non-existent class httpSock");
		$this->err( ob_get_clean() );

		$this->log("Connection_Godo END");
		return $httpSock->resContent;
    }

	### Parse XML
    function parseXml($strXml)
    {
		if (trim($strXml) == ''){
			$this->log("Empty-strXml (Parsing Before)");
			return;
		}
		$this->log("Parse_XML START");

		if (class_exists('StrucXMLParser'))
		{
			$xml = new StrucXMLParser();
			$xml->detailStruc = false;
			$xml->parse($strXml);
			$arrXml = $xml->parseOut();
		}
		else $this->log("Non-existent class StrucXMLParser");

		$this->log("Parse_XML END");
		if (is_array($arrXml) === false || count($arrXml) == 0){
			$this->log("Empty-arrXml (Parsing After)");
			return;
		}

		return $arrXml;
    }

    ### �ֹ�������ȸ(Insert Order)
    function orderListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Insert_Order Before)");
			return;
		}
		$this->log("Insert_Order START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## Ư������ ��Ʈ��
			foreach ($order as $k => $v){
				if (is_array($v) === false) $order[$k] = addslashes( stripslashes($v) );
			}

			## �����ֹ� �����ϴ��� üũ, ������ Insert
			$gOrder = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
			if ($gOrder['ordno'] != ''){
				unset($msg);
				$msg[] = "Fail : Existent order of openstyle [";
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gOrder['ordno']);
				$msg[] = "]";
				$this->log($msg);
				continue;
			}
			else {
				ob_start();

				## �ֹ���ȣ ����
				$ordno = getordno();

				## ��ۺ� �� �ֹ��ݾ� ���
				$delivery = $order['DELIVERY']['DELV'][0]['DEL_AMT'];
				$settleprice = $delivery;
				foreach($order['PRODUCT']['PRD'] as $k => $item){
					$settleprice += $item['ORD_AMT'];
				}
				if ($delivery) $deli_type = '����';

				## �����ȣ ���� ����
				$order['DEL_ZIP'] = substr($order['DEL_ZIP'],0,3) . '-' . substr($order['DEL_ZIP'],3,3);

				## �����α�
				$settlelog =  sprintf("[%s] ������ũ���½�Ÿ�� �Ա�Ȯ�ε� �ֹ�\n", date('y-m-d H:i:s'));

				## �ֹ��������Ŀ� ���� ����
				if($order['ORDER_DT']) $order['ORDER_DT'] = date("Y-m-d H:i:s",$order['ORDER_DT']);

				## �ֹ����� ����
				$query = "
				insert into ".GD_ORDER." set
					ordno			= '{$ordno}',
					nameOrder		= '{$order['ORD_NM']}',
					email			= '{$order['EMAIL']}',
					phoneOrder		= '{$order['TEL']}',
					mobileOrder		= '{$order['MOBILE_TEL']}',
					nameReceiver	= '{$order['RCVR_NM']}',
					phoneReceiver	= '{$order['DELI_TEL']}',
					mobileReceiver	= '{$order['DELI_MOBILE']}',
					zipcode			= '{$order['DEL_ZIP']}',
					address			= '{$order['DELI_ADDR1']} {$order['DELI_ADDR2']}',
					settlekind		= '',
					settleprice		= '{$settleprice}',
					prn_settleprice	= '{$settleprice}',
					deli_title		= '',
					deli_type		= '{$deli_type}',
					delivery		= '{$delivery}',
					ip				= '{$_SERVER['REMOTE_ADDR']}',
					memo			= '{$order['DELI_COMMENT']}',
					inflow			= 'openstyle',
					orddt			= '{$order['ORDER_DTS']}',
					settlelog		= '{$settlelog}',
					inpk_ordno		= '{$order['ORD_NO']}',
					inpk_regdt 		= now()
				";
				$res = $GLOBALS['db']->query($query);

				$this->err($err=ob_get_clean());
				if ($res && $err == '') $resParent['T']++;
				unset($msg);
				$msg[] = "Insert_Result (".GD_ORDER.") [";
				$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $ordno);
				$msg[] = "]";
				$this->log($msg);
			}

			## �ֹ���ǰ
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## ���ϻ�ǰ �����ϴ��� üũ, ������ Insert
				$gOrderItem = $GLOBALS['db']->fetch("select sno, goodsno from ".GD_ORDER_ITEM." where ordno='{$ordno}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gOrderItem['sno'] != ''){
					unset($msg);
					$msg[] = "Fail : Existent orderItem of openstyle [";
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gOrderItem['goodsno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gOrderItem['sno']);
					$msg[] = "]";
					$this->log($msg);
					continue;
				}
				else {
					ob_start();

					## ��ǰ���� ��������
					$data = $GLOBALS['db']->fetch("select goodsno, goodsnm, maker, tax, brandno from ".GD_GOODS." where inpk_prdno='{$item['PRD_NO']}'");
					$data['goodsnm'] = addslashes(strip_tags($data['goodsnm']));
					$data['maker'] = addslashes($data['maker']);
					list($brandnm) = $GLOBALS['db'] -> fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$data['brandno']}'");
					$brandnm = addslashes($brandnm);

					## ��ǰ ���̺��� ���ް� ��������
					list ($item['supply'], $opt[0], $opt[1]) = $GLOBALS['db']->fetch("select supply, opt1, opt2 from ".GD_GOODS_OPTION." where goodsno='{$data['goodsno']}' and optno='{$item['OPT_NO']}' and go_is_deleted <> '1' and go_is_display = '1'");

					## �߰��ɼ� (����/��|������/100)
					if ($opt[0] == '' && $opt[1] == ''){
						$opt = array();
						$tmp = explode("|", $item['OPT_NM']);
						foreach($tmp as $v){
							$opt[] = trim(array_pop(explode("/", $v)));
						}
					}

					$price = $item['ORD_AMT'] / $item['ORD_QTY'];
					$deli_msg = ($item['IS_COLLECTED'] == 'Y' ? '���ҹ��' : '');
					$query = "
					insert into ".GD_ORDER_ITEM." set
						ordno			= '{$ordno}',
						goodsno			= '{$data['goodsno']}',
						goodsnm			= '{$data['goodsnm']}',
						opt1			= '{$opt[0]}',
						opt2			= '{$opt[1]}',
						price			= '{$price}',
						supply			= '{$item['supply']}',
						ea				= '{$item['ORD_QTY']}',
						maker			= '{$data['maker']}',
						brandnm			= '{$brandnm}',
						tax				= '{$data['tax']}',
						optno			= '{$item['OPT_NO']}',
						inpk_prdno		= '{$item['PRD_NO']}',
						inpk_ordseq		= '{$item['ORD_SEQ']}',
						deli_msg		= '{$deli_msg}'
					";
					$res = $GLOBALS['db']->query($query);
					$item_sno = $GLOBALS['db']->lastID();

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Insert_Result (".GD_ORDER_ITEM.") [";
					$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $data['goodsno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $item_sno);
					$msg[] = "]";
					$this->log($msg);
				}
			}

			## �����Ȳ�� ó��
			ctlStep($ordno,2,'stock'); // ����غ��� �� �������
		}

		## ó��������
		unset($msg);
		$msg[] = sprintf("Insert_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], $resParent['T'], ($resParent['TOT'] - $resParent['T']));
		$msg[] = sprintf("Insert_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Insert_Order END");
    }

    ### �ֹ����/��ǰ/��ȯ��û����Ʈ
    function cnclNClmReqListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Insert_Cancel&Claim&ExchangeRequest Before)");
			return;
		}
		$this->log("Insert_Cancel&Claim&ExchangeRequest START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## ���Ͽ�û �����ϴ��� üũ, ������ Insert
			$gClaim = $GLOBALS['db']->fetch("select clmsno, ordno, clm_tpnm from ".INPK_CLAIM." where inpk_ordno='{$order['ORD_NO']}' and clm_no='' and clm_seq='{$order['CLMREQ_SEQ']}'");
			if ($gClaim['clmsno'] == '')
			{
				ob_start();

				list($gClaim['ordno']) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
				$query = "
				insert into ".INPK_CLAIM." set
					step				= 'r',
					ordno				= '{$gClaim['ordno']}',
					inpk_ordno			= '{$order['ORD_NO']}',
					clm_seq				= '{$order['CLMREQ_SEQ']}',
					clm_tpnm			= '{$order['CLMREQ_TPNM']}',
					return_mthd_tpnm	= '{$order['RETURN_MTHD_TPNM']}',
					regdt 				= now()
				";
				$res = $GLOBALS['db']->query($query);
				$gClaim['clmsno'] = $GLOBALS['db']->lastID();

				$this->err($err=ob_get_clean());
				if ($res && $err == '') $resParent['T']++;
				unset($msg);
				$msg[] = "Insert_Result (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t������ũ �ֹ���ҿ�û���� = %s", $order['CLMREQ_SEQ']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� Ŭ���ӹ�ȣ = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\tŬ���ӿ�û���� = %s", $order['CLMREQ_TPNM']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				unset($msg);
				$msg[] = "Existent (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t������ũ �ֹ���ҿ�û���� = %s", $order['CLMREQ_SEQ']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� Ŭ���ӹ�ȣ = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\tŬ���ӿ�û���� = %s", $gClaim['clm_tpnm']);
				$msg[] = "]";
				$this->log($msg);
			}

			## �ֹ���ǰ
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## ���ϻ�ǰ �����ϴ��� üũ, ������ Insert
				$gClaimItem = $GLOBALS['db']->fetch("select itmsno, item_sno, goodsno, clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$gClaim['clmsno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gClaimItem['itmsno'] == '')
				{
					ob_start();

					list($gClaimItem['item_sno'], $gClaimItem['goodsno'], $istep) = $GLOBALS['db']->fetch("select sno, goodsno, istep from ".GD_ORDER_ITEM." where ordno='{$gClaim['ordno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
					$query = "
					insert into ".INPK_CLAIM_ITEM." set
						clmsno			= '{$gClaim['clmsno']}',
						item_sno		= '{$gClaimItem['item_sno']}',
						goodsno			= '{$gClaimItem['goodsno']}',
						inpk_prdno		= '{$item['PRD_NO']}',
						clm_qty			= '{$item['CLMREQ_QTY']}',
						inpk_ordseq		= '{$item['ORD_SEQ']}',
						clm_statnm		= '{$item['CLMREQ_STATNM']}',
						clm_dt			= '{$order['CLMREQ_DT']}',
						clm_rsn_tpnm	= '{$item['CLMREQ_RSN_TPNM']}',
						clm_rsn_dtl		= '{$item['CLMREQ_RSN_DTL']}',
						latedt			= '{$order['CLMREQ_DT']}'
					";
					$res = $GLOBALS['db']->query($query);
					$gClaimItem['itmsno'] = $GLOBALS['db']->lastID();

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Insert_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t�̳��� Ŭ���Ӽ��� = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\tŬ���ӿ�û���� = %s", $item['CLMREQ_STATNM']);
					$msg[] = "]";
					$this->log($msg);

					## �ֹ����� üũ(�ź�ó��)
					if ($order['CLMREQ_TPNM'] == "������ֹ����" && $item['CLMREQ_STATNM'] == "��û" && in_array($istep, array('3', '4'))){
						$item['CLMREQ_STATNM'] = '�ź�';
					}
				}
				else {
					unset($msg);
					$msg[] = "Existent (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t�̳��� Ŭ���Ӽ��� = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\tŬ���ӿ�û���� = %s", $gClaimItem['clm_statnm']);
					$msg[] = "]";
					$this->log($msg);
				}

				## Ŭ���ӿ�û���� ������Ʈ
				if ($gClaimItem['clm_statnm'] != $item['CLMREQ_STATNM'])
				{
					if ($gClaimItem['clm_statnm'] == ''){
						$memo = $item['CLMREQ_STATNM'];
						$regdt = $order['CLMREQ_DT'];
					}
					else {
						$memo = "{$gClaimItem['clm_statnm']} > {$item['CLMREQ_STATNM']}";
						$regdt = $item['CLMREQ_CNCL_DT'];
					}

					## ���¼���
					ob_start();
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='{$item['CLMREQ_STATNM']}', latedt='{$regdt}' where itmsno='{$gClaimItem['itmsno']}'";
					$res = $GLOBALS['db']->query($query);

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['stat_T']++;
					unset($msg);
					$msg[] = "Update_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\tŬ���ӿ�û���� = %s", $item['CLMREQ_STATNM']);
					$msg[] = "]";
					$this->log($msg);

					## �α��߰�
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$gClaimItem['itmsno']}', '{$memo}', '{$regdt}')";
					$GLOBALS['db']->query($query);
				}
			}
		}

		## ó��������
		unset($msg);
		$msg[] = sprintf("Insert_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], $resParent['T'], ($resParent['TOT'] - $resParent['T']));
		$msg[] = sprintf("Insert_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		$msg[] = sprintf("Update_Stat_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['stat_T'], ($resChild['TOT'] - $resChild['stat_T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Insert_Cancel&Claim&ExchangeRequest END");
    }

    ### Ŭ���Ӹ���Ʈ
    function clmListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Insert_ClaimList Before)");
			return;
		}
		$this->log("Insert_ClaimList START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## ���Ͽ�û �����ϴ��� üũ, ������ Insert
			$gClaim = $GLOBALS['db']->fetch("select clmsno, ordno, clm_tpnm from ".INPK_CLAIM." where inpk_ordno='{$order['ORD_NO']}' and clm_no='{$order['CLM_NO']}' and clm_seq='{$order['CLM_SEQ']}'");
			if ($gClaim['clmsno'] == '')
			{
				ob_start();

				list($gClaim['ordno']) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
				$query = "
				insert into ".INPK_CLAIM." set
					step				= 'c',
					ordno				= '{$gClaim['ordno']}',
					inpk_ordno			= '{$order['ORD_NO']}',
					clm_no				= '{$order['CLM_NO']}',
					clm_seq				= '{$order['CLM_SEQ']}',
					clm_tpnm			= '{$order['CLM_CRT_TPNM']}',
					regdt 				= now()
				";
				$res = $GLOBALS['db']->query($query);
				$gClaim['clmsno'] = $GLOBALS['db']->lastID();

				$this->err($err=ob_get_clean());
				if ($res && $err == '') $resParent['T']++;
				unset($msg);
				$msg[] = "Insert_Result (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t������ũ Ŭ���ӹ�ȣ = %s", $order['CLM_NO']);
				$msg[] = sprintf("\t������ũ Ŭ���Ӽ��� = %s", $order['CLM_SEQ']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� Ŭ���ӹ�ȣ = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\tŬ���ӻ������� = %s", $order['CLM_CRT_TPNM']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				unset($msg);
				$msg[] = "Existent (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t������ũ Ŭ���ӹ�ȣ = %s", $order['CLM_NO']);
				$msg[] = sprintf("\t������ũ Ŭ���Ӽ��� = %s", $order['CLM_SEQ']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� Ŭ���ӹ�ȣ = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\tŬ���ӻ������� = %s", $gClaim['clm_tpnm']);
				$msg[] = "]";
				$this->log($msg);
			}

			## �ֹ���ǰ
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## ���ϻ�ǰ �����ϴ��� üũ, ������ Insert
				$gClaimItem = $GLOBALS['db']->fetch("select itmsno, item_sno, goodsno, clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$gClaim['clmsno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gClaimItem['itmsno'] == '')
				{
					ob_start();

					list($gClaimItem['item_sno'], $gClaimItem['goodsno']) = $GLOBALS['db']->fetch("select sno, goodsno from ".GD_ORDER_ITEM." where ordno='{$gClaim['ordno']}' and optno='{$item['OPT_NO']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
					$query = "
					insert into ".INPK_CLAIM_ITEM." set
						clmsno			= '{$gClaim['clmsno']}',
						item_sno		= '{$gClaimItem['item_sno']}',
						goodsno			= '{$gClaimItem['goodsno']}',
						inpk_prdno		= '{$item['PRD_NO']}',
						clm_qty			= '{$item['CLM_QTY']}',
						inpk_ordseq		= '{$item['ORD_SEQ']}',
						clm_statnm		= '{$item['CURRENT_CLMPRD_STATNM']}',
						clm_dt			= '{$item['CLM_DT']}',
						clm_rsn_tpnm	= '{$item['CLM_RSN_TPNM']}',
						clm_rsn_dtl		= '{$item['CLM_RSN_DTL']}',
						latedt			= '{$item['CLM_DT']}'
					";
					$res = $GLOBALS['db']->query($query);
					$gClaimItem['itmsno'] = $GLOBALS['db']->lastID();

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Insert_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t�̳��� Ŭ���Ӽ��� = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\tŬ���ӻ��� = %s", $item['CURRENT_CLMPRD_STATNM']);
					$msg[] = "]";
					$this->log($msg);
				}
				else {
					unset($msg);
					$msg[] = "Existent (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t�̳��� Ŭ���Ӽ��� = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\tŬ���ӻ��� = %s", $gClaimItem['clm_statnm']);
					$msg[] = "]";
					$this->log($msg);
				}

				## Ŭ���ӻ��� ������Ʈ
				if ($gClaimItem['clm_statnm'] != $item['CURRENT_CLMPRD_STATNM'])
				{
					## �̳����ֹ�ó��
					if ($gClaimItem['clm_statnm'] == '')
					{
						ob_start();
						$arr = array('sno' => $gClaimItem['item_sno'], 'ea' => $item['CLM_QTY']);
						$this->eNamooClmReceipt($gClaim['ordno'],$arr); // Ŭ����-Ŭ��������
						$this->err($err=ob_get_clean());
						$this->log(sprintf("Ŭ�������� ó������ = %s", ($err == '' ? 'True' : 'False')));
					}

					switch ($item['CURRENT_CLMPRD_STATNM'])
					{
						case "Ŭ����Ȯ��_ȯ�ҿϷ�":
							if ($order['CLM_CRT_TPNM'] != '����ȯ'){
								ob_start();
								$this->eNamooClmFinal($gClaimItem['item_sno']); // Ŭ����-Ŭ����Ȯ��_ȯ�ҿϷ�
								$this->err($err=ob_get_clean());
								$this->log(sprintf("Ŭ����Ȯ��_ȯ�ҿϷ� ó������ = %s", ($err == '' ? 'True' : 'False')));
							}
							break;

						case "Ŭ�������":
							ob_start();
    						$this->eNamooClmCancel($gClaimItem['item_sno']); // Ŭ����-Ŭ�������
							$this->err($err=ob_get_clean());
							$this->log(sprintf("Ŭ������� ó������ = %s", ($err == '' ? 'True' : 'False')));
							break;
					}

					switch ($gClaimItem['clm_statnm'])
					{
						case "��ǰ/��ȯ��������":
						case "��ǰ/��ȯ�԰�Ȯ������":
							if ($item['CURRENT_CLMPRD_STATNM'] == '��ǰ/��ȯ�԰�Ϸ�' || $item['CURRENT_CLMPRD_STATNM'] == 'Ŭ����Ȯ��_ȯ�ҿϷ�'){
								ob_start();
	    						$this->eNamooClmStoreComp($ordno,$gClaimItem['item_sno']); // Ŭ����-��ǰ/��ȯ�԰�Ϸ�
								$this->err($err=ob_get_clean());
								$this->log(sprintf("��ǰ/��ȯ�԰�Ϸ� ó������ = %s", ($err == '' ? 'True' : 'False')));
							}
							break;

						case "��ȯ/�����������":
						case "��ȯȮ������":
							if ($item['CURRENT_CLMPRD_STATNM'] == '��ȯ/�������Ϸ�' || $item['CURRENT_CLMPRD_STATNM'] == 'Ŭ����Ȯ��_ȯ�ҿϷ�'){
								ob_start();
	    						$this->eNamooExchangeComp($ordno,$gClaimItem['item_sno'],$gClaimItem['itmsno']); // Ŭ����-��ȯ/�������Ϸ�
								$this->err($err=ob_get_clean());
								$this->log(sprintf("��ȯ/�������Ϸ� ó������ = %s", ($err == '' ? 'True' : 'False')));
							}
							break;
					}

					## ���¼���
					ob_start();
					if ($order['CLM_CRT_TPNM'] == '����ȯ' && $item['CURRENT_CLMPRD_STATNM'] == 'Ŭ����Ȯ��_ȯ�ҿϷ�') $item['CURRENT_CLMPRD_STATNM'] = 'Ŭ����Ȯ��';
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='{$item['CURRENT_CLMPRD_STATNM']}', latedt='{$item['CLM_DT']}' where itmsno='{$gClaimItem['itmsno']}'";
					$res = $GLOBALS['db']->query($query);

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['stat_T']++;
					unset($msg);
					$msg[] = "Update_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t��� = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\tŬ���ӻ��� = %s", $item['CURRENT_CLMPRD_STATNM']);
					$msg[] = "]";
					$this->log($msg);

					## �α��߰�
					if ($gClaimItem['clm_statnm'] == ''){
						$memo = $item['CURRENT_CLMPRD_STATNM'];
					}
					else {
						$memo = "{$gClaimItem['clm_statnm']} > {$item['CURRENT_CLMPRD_STATNM']}";
					}
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$gClaimItem['itmsno']}', '{$memo}', '{$item['CLM_DT']}')";
					$GLOBALS['db']->query($query);
				}
			}
		}

		## ó��������
		unset($msg);
		$msg[] = sprintf("Insert_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], $resParent['T'], ($resParent['TOT'] - $resParent['T']));
		$msg[] = sprintf("Insert_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		$msg[] = sprintf("Update_Stat_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['stat_T'], ($resChild['TOT'] - $resChild['stat_T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Insert_ClaimList END");
    }

    ### Ŭ����-Ŭ��������
    function eNamooClmReceipt($ordno,$arr)
    {
		$data = $GLOBALS['db']->fetch("select * from ".GD_ORDER_ITEM." where sno='{$arr['sno']}'", 1);
		$istep = ($data[cyn]=="n" && $data[dyn]=="n") ? 44 : 41;

		### �ֹ������� ��Ҽ����� ����ġ�� ��� �ֹ��� �и�
		$gap = $data['ea'] - $arr['ea'];
		if ($gap){
			unset($tmp);
			foreach ($data as $k=>$v)
			{
				if ($k == 'sno');
				else if ($k == 'ea') $tmp[] = "`$k`='{$gap}'";
				else $tmp[] = "`$k`='".addslashes($v)."'";
			}
			$tmp = implode(",",$tmp);
			$query = "insert into ".GD_ORDER_ITEM." set $tmp";
			$GLOBALS['db']->query($query);
			$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set ea='{$arr['ea']}', istep='$istep' where sno='{$arr['sno']}'");
		} else $GLOBALS['db']->query("update ".GD_ORDER_ITEM." set istep='$istep' where sno='{$arr['sno']}'");

		$pre = $GLOBALS['db']->fetch("select * from ".GD_ORDER." where ordno='$ordno'");

		### �ֹ���� ���� üũ
		$cnt = 0;
		$query = "select * from ".GD_ORDER_ITEM." where ordno='$ordno'";
		$res = $GLOBALS['db']->query($query);
		while ($data=$GLOBALS['db']->fetch($res))	if ($data[istep]>40) $cnt++;

		if ($GLOBALS['db']->count_($res)==$cnt){
			list($step2) = $GLOBALS['db']->fetch("select min(istep) from ".GD_ORDER_ITEM." where ordno='$ordno'");
			$GLOBALS['db']->query("update ".GD_ORDER." set step2=$step2 where ordno='$ordno'");
		}

		### �������
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    ### Ŭ����-��ǰ/��ȯ�԰�Ϸ�
    function eNamooClmStoreComp($ordno,$item_sno)
    {
		### �ֹ������� ó��
		$query = "update ".GD_ORDER_ITEM." set istep=42,dyn='r' where sno='{$item_sno}'";
		$GLOBALS['db']->query($query);

		### �ֹ� �ϰ� ó��
		list($ordno) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER_ITEM." where sno='{$item_sno}'");
		$query = "update ".GD_ORDER." set step2=42,dyn='r' where ordno='$ordno' and step2=41";
		$GLOBALS['db']->query($query);

		### �������
		setStock($ordno);
    }

    ### Ŭ����-��ȯ/�������Ϸ�
    function eNamooExchangeComp($ordno,$item_sno,$itmsno)
    {
		### �ֹ������� ó��
		$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where sno='{$item_sno}'";
		$GLOBALS['db']->query($query);

		### �ֹ� �ϰ� ó��
		list($ordno) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER_ITEM." where sno='{$item_sno}'");
		$query = "update ".GD_ORDER." set step2=44,dyn='e',cyn='e' where ordno='$ordno' and step2=41";
		$GLOBALS['db']->query($query);

		### ���ֹ�
		$cancel = time();
		$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set cancel='{$cancel}' where sno='{$item_sno}'");
		$newOrdno = reorder($ordno,$cancel);
		$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set cancel='' where sno='{$item_sno}'");

		### ��ۿϷ�ó��
		$gClaimItem = $GLOBALS['db']->fetch("select exchangeDvno, exchangeDvcode from ".INPK_CLAIM_ITEM." where itmsno='{$itmsno}'");
		$query = "update ".GD_ORDER." set
				step='4',
				cyn='y',
				cdt=now(),
				dyn='y',
				ddt=now(),
				confirm='admin',
				confirmdt=now(),
				deliveryno='{$gClaimItem['exchangeDvno']}',
				deliverycode='{$gClaimItem['exchangeDvcode']}'
				where ordno='{$newOrdno}'";
		$GLOBALS['db']->query($query);
		$query = "update ".GD_ORDER_ITEM." set
				istep='4',
				cyn='y',
				dyn='y',
				dvno='{$gClaimItem['exchangeDvno']}',
				dvcode='{$gClaimItem['exchangeDvcode']}'
				where ordno='{$newOrdno}'";
		$GLOBALS['db']->query($query);
    }

    ### Ŭ����-Ŭ����Ȯ��_ȯ�ҿϷ�
    function eNamooClmFinal($item_sno)
    {
		### �ֹ������� ó��
		$query = "update ".GD_ORDER_ITEM." set istep=44,cyn='r' where sno='{$item_sno}'";
		$GLOBALS['db']->query($query);
		### �ֹ� �ϰ� ó��
		list($ordno) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER_ITEM." where sno='{$item_sno}'");
		$query = "update ".GD_ORDER." set step2=44,cyn='r' where ordno='$ordno' and step2 in (41,42)";
		$GLOBALS['db']->query($query);

		### �������
		setStock($ordno);
    }

    ### Ŭ����-Ŭ�������
    function eNamooClmCancel($item_sno)
    {
		$query = "select * from ".GD_ORDER." a,".GD_ORDER_ITEM." b where a.ordno=b.ordno and b.sno='{$item_sno}'";
		$data = $GLOBALS['db']->fetch($query,1);

		$data['opt1'] = addslashes($data['opt1']);
		$data['opt2'] = addslashes($data['opt2']);
		$data['addopt'] = addslashes($data['addopt']);

		### ������ ���ϴܰ��� �������� �����ϴ��� üũ
		$query = "
		select sno from
			".GD_ORDER_ITEM."
		where
			ordno = '$data[ordno]'
			and istep = '$data[step]'
			and goodsno = '$data[goodsno]'
			and opt1 = '$data[opt1]'
			and opt2 = '$data[opt2]'
			and addopt = '$data[addopt]'
			and price = '$data[price]'
		";
		list ($sno) = $GLOBALS['db']->fetch($query);

		if ($sno){
			$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set ea=ea+$data[ea] where sno='$sno'");
			$GLOBALS['db']->query("delete from ".GD_ORDER_ITEM." where sno='$data[sno]'");
		} else {
			$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set istep=$data[step],cancel=0 where sno='$data[sno]'");
		}

		### ��ü �ֹ��ܰ谡 ��Ҵܰ�� �Ϲ� �ֹ��ܰ�� �ܰ躹��
		if ($data[step2]){
			$query = "update ".GD_ORDER." set step2='' where ordno='$data[ordno]'";
			$GLOBALS['db']->query($query);
		}

		### �������
		setStock($data[ordno]);
		set_prn_settleprice($data[ordno]);
    }

    ### ����Ȯ����ȸ
    function orderCompListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Update_OrderItem_Compdt Before)");
			return;
		}
		$this->log("Update_OrderItem_Compdt START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## �����ֹ� �����ϴ��� üũ, ������ Update
			$gOrder = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
			if ($gOrder['ordno'] == ''){
				unset($msg);
				$msg[] = "Fail : Non-existent order of openstyle [";
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = "]";
				$this->log($msg);
				continue;
			}
			else {
				unset($msg);
				$msg[] = "Existent (".GD_ORDER.") [";
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gOrder['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}

			## �ֹ���ǰ
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## ���ϻ�ǰ �����ϴ��� üũ, ������ Update
				$gOrderItem = $GLOBALS['db']->fetch("select sno, goodsno from ".GD_ORDER_ITEM." where ordno='{$gOrder['ordno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gOrderItem['sno'] == ''){
					unset($msg);
					$msg[] = "Fail : Non-existent orderItem of openstyle [";
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = "]";
					$this->log($msg);
					continue;
				}
				else {
					ob_start();

					$query = "update ".GD_ORDER_ITEM." set inpk_compdt='{$item['ORD_COMP_DT']}' where ordno='{$gOrder['ordno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'";
					$res = $GLOBALS['db']->query($query);

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Update_Result (".GD_ORDER_ITEM.") [";
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gOrderItem['goodsno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gOrderItem['sno']);
					$msg[] = "]";
					$this->log($msg);
				}
			}
		}

		## ó��������
		unset($msg);
		$msg[] = sprintf("Update_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], '-', '-');
		$msg[] = sprintf("Update_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Update_OrderItem_Compdt END");
    }
}

?>
