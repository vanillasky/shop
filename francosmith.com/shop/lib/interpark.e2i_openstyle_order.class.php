<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";

class e2i_order_api extends putLog
{
	var $callCnt = 0;	# API ȣ�� Ƚ��
	var $recallMax = 2;	# API ��ȣ�� �ִ�Ƚ��
	var $interfaceid;	# �������̽� ID
	var $interfaceurl = 'https://godointerpark.godo.co.kr/e2i.OpenStyle_orderApi.php?method=%s';	# �������̽� URL

	function e2i_order_api($interfaceid, $data)
	{

		if (is_array($data)) $data = array_map("trim",$data);
		$this->putLog('e2iOrderApi');
		$this->log("START");

		## �������̽� ID ����
		//$this->interfaceid = $interfaceid;
		$this->interfaceid = str_replace("openstyle_","",$interfaceid);

		## �������̽� URL ����
		$this->interfaceurl = sprintf($this->interfaceurl, $interfaceid);

		## API START
		switch ($this->interfaceid)
		{
			case "delvCompForComm": # ��۽���
				$this->delvCompForComm($data);
				break;

			case "cnclOutOfStockReqForComm": # ǰ���ֹ���ҿ�û
				$this->cnclOutOfStockReqForComm($data);
				break;

			case "cnclReqAcceptForComm": # �ֹ���ҿ�û����
				$this->cnclReqAcceptForComm($data);
				break;

			case "rtnReqAcceptForComm": # ��ǰ��û����
				$this->rtnReqAcceptForComm($data);
				break;

			case "exchReqAcceptForComm": # ��ȯ��û����
				$this->exchReqAcceptForComm($data);
				break;

			case "rtnReqRefuseForComm": # ��ǰ��û�ź�
				$this->rtnReqRefuseForComm($data);
				break;

			case "exchReqRefuseForComm": # ��ȯ��û�ź�
				$this->exchReqRefuseForComm($data);
				break;

			case "clmEnterWhCompForComm": # ��ǰ/��ȯ�԰�Ȯ��
				$this->clmEnterWhCompForComm($data);
				break;

			case "exchOutWhCompForComm": # ��ȯȮ��
				$this->exchOutWhCompForComm($data);
				break;
		}

		$this->log("END");
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

		if ($arrXml['ORDER_LIST']['RESULT']){
			$this->log(sprintf("INTERPARK Result : [CODE] %s", $arrXml['ORDER_LIST']['RESULT']['CODE']));
			$this->log(sprintf("INTERPARK Result : [MESSAGE] %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']));
			$this->log(sprintf("INTERPARK Result : [LOG_SEQ] %s", $arrXml['ORDER_LIST']['RESULT']['LOG_SEQ']));
		}

		return $arrXml;
    }

	### ��۽���
	function delvCompForComm($data)
	{
		$data['ordno'] = trim($data['ordno']);
		$this->log("openstyle delvCompForComm START");

		$gOrder = $GLOBALS['db']->fetch("select ordno, inflow, step, inpk_ordno, ddt, deliveryno, deliverycode, settlelog from ".GD_ORDER." where ordno='{$data['ordno']}'");
		if ($gOrder['ordno'] == ''){
			$this->log(sprintf("Fail : Non-existent order [%s]", $data['ordno']));
		}
		else if ($gOrder['inflow'] != 'openstyle'){
			$this->log("Fail : Not order of openstye");
		}
		else if ($gOrder['step'] != '3'){
			$this->log("Fail : �ֹ����� != �����(3)");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$gOrder['ddt'] = substr(str_replace("-", "", $gOrder['ddt']), 0, 8);

			$res = $GLOBALS['db']->query("select sno, goodsno, inpk_ordseq, inpk_prdno, dvno, dvcode from ".GD_ORDER_ITEM." where ordno='{$data['ordno']}' and istep='{$gOrder['step']}'");
			$resChild = array('TOT' => $GLOBALS['db']->count_($res), 'T' => 0);
			while ($gItem = $GLOBALS['db']->fetch($res))
			{
				// orderclm_no=�ֹ���ȣ&ord_seq=�ֹ�����&delv_dt=���Ϸ�����&delv_comp=�ù���ڵ�&delv_no=������ȣ
				// sc.ordclmNo=�ֹ���ȣ&sc.ordSeq=�ֹ�����&&sc.delvDt=yyyymmdd���Ϸ�����&sc.delvEntrNo=�ù���ڵ�&sc.invoNo=������ȣ&optPrdTp=�ɼǻ�ǰ����&sc.optOrdSeqList=�ֹ���������Ʈ

				$gItem['dvno'] = ($gItem['dvno'] ? $gItem['dvno'] : $gOrder['deliveryno']);
				$gItem['dvcode'] = trim($gItem['dvcode'] ? $gItem['dvcode'] : $gOrder['deliverycode']);

				//�Ϲ� ��ǰ��ǰ�� ���
				$optPrdTp = "01";
				$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&delv_dt=%s&delv_comp=%s&delv_no=%s&optPrdTp=%s&optOrdSeqList=%s", $this->interfaceurl, $gOrder['inpk_ordno'], $gItem['inpk_ordseq'], $gOrder['ddt'], $gItem['dvno'], $gItem['dvcode'],'01',$gItem['inpk_ordseq']);
				$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

				ob_start();
				$httpSock = new httpSock($url);
				$httpSock->send(true);
				$this->err( ob_get_clean() );

				$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '015')) === true){
					$resChild['T']++;
					$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $gItem['goodsno'], $gItem['inpk_prdno']);
					unset($msg);
					$msg[] = "Result [";
					$msg[] = sprintf("\t��� = %s", 'True');
					$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gItem['inpk_ordno']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gItem['inpk_ordseq']);
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gItem['inpk_prdno']);
					$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $data['ordno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gItem['sno']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gItem['goodsno']);
					$msg[] = "]";
					$this->log($msg);

					## Ŭ���ӿ�û����Ʈ üũ(�ź�ó��)
					list($itmsno) = $GLOBALS['db']->fetch("select itmsno from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where c.step='r' and c.clm_tpnm='������ֹ����' and i.clm_statnm='��û' and i.item_sno='{$gItem['sno']}'");
					if ($itmsno){
						# Ŭ���ӿ�û���� ������Ʈ
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='�ź�', latedt=now() where itmsno='{$itmsno}'";
						$GLOBALS['db']->query($query);
						# �α��߰�
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$itmsno}', '��û > �ź�', now())";
						$GLOBALS['db']->query($query);
					}
				}
				else {
					$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gItem['goodsno'], $gItem['inpk_prdno']);
					unset($msg);
					$msg[] = "Result [";
					$msg[] = sprintf("\t��� = %s", 'False');
					$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gItem['inpk_ordno']);
					$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gItem['inpk_ordseq']);
					$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gItem['inpk_prdno']);
					$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $data['ordno']);
					$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gItem['sno']);
					$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gItem['goodsno']);
					$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
					$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
					$msg[] = "]";
					$this->log($msg);
				}
			}
			if ($resChild['T'] == 0) ctlStep($data['ordno'],$data['preStep'],$data['stock']);

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("%s\n[%s] ������ũ ��۽���ó��\n%s\n", $gOrder['settlelog'], date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog='{$settlelog}' where ordno='{$data['ordno']}'");
			}
		}

		$this->log("delvCompForComm END");
	}

	### ǰ���ֹ���ҿ�û
	function cnclOutOfStockReqForComm($data)
	{
		$this->callCnt++;
		$data['ordno'] = trim($data['ordno']);
		$this->log("cnclOutOfStockReqForComm START");

		$gOrder = $GLOBALS['db']->fetch("select o.ordno, o.inpk_ordno, o.inflow, o.step, i.sno, i.inpk_ordseq, i.goodsno, i.inpk_prdno, i.ea from ".GD_ORDER." o left join ".GD_ORDER_ITEM." i on o.ordno=i.ordno where i.ordno='{$data['ordno']}' and i.sno='{$data['sno']}'");
		if ($gOrder['inpk_ordno'] == ''){
			$this->log(sprintf("Fail : Non-existent order [ordno:%s, sno:%s]", $data['ordno'], $data['sno']));
		}
		else if ($gOrder['inflow'] != 'openstyle'){
			$this->log("Fail : Not order of openstyle");
		}
		else if ($gOrder['step'] != '2'){
			$this->log("Fail : �ֹ����� != ����غ���(2)");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=�ֹ���ȣ&ord_seq=�ֹ�����
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s", $this->interfaceurl, $gOrder['inpk_ordno'], $gOrder['inpk_ordseq']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '005')) === true){
				## Ŭ���ӿ�û���� �߰�
				$query = "
				insert into ".INPK_CLAIM." set
					step				= 'r',
					ordno				= '{$gOrder['ordno']}',
					inpk_ordno			= '{$gOrder['inpk_ordno']}',
					clm_tpnm			= '�����ǰ���ֹ����',
					regdt 				= now()
				";
				$res = $GLOBALS['db']->query($query);
				$clmsno = $GLOBALS['db']->lastID();
				$query = "
				insert into ".INPK_CLAIM_ITEM." set
					clmsno			= '{$clmsno}',
					item_sno		= '{$gOrder['sno']}',
					goodsno			= '{$gOrder['goodsno']}',
					inpk_prdno		= '{$gOrder['inpk_prdno']}',
					clm_qty			= '{$gOrder['ea']}',
					inpk_ordseq		= '{$gOrder['inpk_ordseq']}',
					clm_statnm		= '��û�Ϸ�',
					clm_dt			= now(),
					clm_rsn_tpnm	= '�Ǹ���ǰ��',
					clm_rsn_dtl		= '�Ǹ���(����)������ ǰ���ֹ���Ҹ� ��û�Ͽ����ϴ�.',
					latedt			= now()
				";
				$res = $GLOBALS['db']->query($query);

				$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $gOrder['goodsno'], $gOrder['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gOrder['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gOrder['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gOrder['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gOrder['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gOrder['sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gOrder['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gOrder['goodsno'], $gOrder['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gOrder['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gOrder['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gOrder['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gOrder['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gOrder['sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gOrder['goodsno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ ǰ���ֹ���ҿ�û\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gOrder['ordno']}'");
			}
		}

		$this->log("cnclOutOfStockReqForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->cnclOutOfStockReqForComm($data);
		}
	}

	### �ֹ���ҿ�û����
	function cnclReqAcceptForComm($data)
	{
		$this->callCnt++;
		$this->log("cnclReqAcceptForComm START");

		$gClaim = $GLOBALS['db']->fetch("select c.clm_seq, c.inpk_ordno, c.ordno, i.* from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where i.clmsno='{$data['clmsno']}' and i.itmsno='{$data['itmsno']}'");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s, itmsno:%s]", $data['clmsno'], $data['itmsno']));
		}
		else if ($gClaim['clm_statnm'] != '��û'){
			$this->log("Fail : Ŭ���ӿ�û���� != ��û");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=�ֹ���ȣ&ord_seq=�ֹ�����&clmreq_seq=�ֹ���ҿ�û����
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&clmreq_seq=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['inpk_ordseq'], $gClaim['clm_seq']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '006')) === true){
				# Ŭ���ӿ�û���� ������Ʈ
				$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='����', latedt=now() where clmsno='{$data['clmsno']}' and itmsno='{$data['itmsno']}'";
				$GLOBALS['db']->query($query);
				# �α��߰�
				$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$data['itmsno']}', '��û > ����', now())";
				$GLOBALS['db']->query($query);

				$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaim['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaim['goodsno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# ����ڵ尡 004 �̸� ��ûöȸ�� ó��
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					# Ŭ���ӿ�û���� ������Ʈ
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='��ûöȸ', latedt=now() where clmsno='{$data['clmsno']}' and itmsno='{$data['itmsno']}'";
					$GLOBALS['db']->query($query);
					# �α��߰�
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$data['itmsno']}', '��û > ��ûöȸ', now())";
					$GLOBALS['db']->query($query);
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ �ֹ���ҿ�û����\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("cnclReqAcceptForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->cnclReqAcceptForComm($data);
		}
	}

	### ��ǰ��û����
	function rtnReqAcceptForComm($data)
	{
		$this->callCnt++;
		$this->log("rtnReqAcceptForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '��û'){
			$this->log("Fail : Ŭ���ӿ�û���� != ��û");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=�ֹ���ȣ&clmreq_seq=�ֹ���ҿ�û����&return_mthd_tp=���Ź���ڵ�
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&return_mthd_tp=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], $data['return_mthd_tp']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '008')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set accept_return_mthd_tp='{$data['return_mthd_tp']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# Ŭ���ӿ�û���� ������Ʈ
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='����', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# �α��߰�
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > ����', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# ����ڵ尡 004 �̸� ��ûöȸ�� ó��
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# Ŭ���ӿ�û���� ������Ʈ
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='��ûöȸ', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# �α��߰�
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > ��ûöȸ', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ ��ǰ��û����\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("rtnReqAcceptForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->rtnReqAcceptForComm($data);
		}
	}

	### ��ȯ��û����
	function exchReqAcceptForComm($data)
	{
		$this->callCnt++;
		$this->log("exchReqAcceptForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '��û'){
			$this->log("Fail : Ŭ���ӿ�û���� != ��û");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=�ֹ���ȣ&clmreq_seq=�ֹ���ҿ�û����&return_mthd_tp=���Ź���ڵ�
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&return_mthd_tp=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], $data['return_mthd_tp']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '010')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set accept_return_mthd_tp='{$data['return_mthd_tp']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# Ŭ���ӿ�û���� ������Ʈ
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='����', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# �α��߰�
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > ����', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# ����ڵ尡 004 �̸� ��ûöȸ�� ó��
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# Ŭ���ӿ�û���� ������Ʈ
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='��ûöȸ', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# �α��߰�
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > ��ûöȸ', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ ��ȯ��û����\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("exchReqAcceptForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->exchReqAcceptForComm($data);
		}
	}

	### ��ǰ��û�ź�
	function rtnReqRefuseForComm($data)
	{
		$this->callCnt++;
		$this->log("rtnReqRefuseForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '��û'){
			$this->log("Fail : Ŭ���ӿ�û���� != ��û");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=�ֹ���ȣ&clmreq_seq=�ֹ���ҿ�û����&refuse_rsn=�źλ���
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&refuse_rsn=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], urlencode($data['refuse_rsn']));
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '009')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set refuse_rsn='{$data['refuse_rsn']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# Ŭ���ӿ�û���� ������Ʈ
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='�ź�', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# �α��߰�
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > �ź�', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# ����ڵ尡 004 �̸� ��ûöȸ�� ó��
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# Ŭ���ӿ�û���� ������Ʈ
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='��ûöȸ', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# �α��߰�
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > ��ûöȸ', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ ��ǰ��û�ź�\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("rtnReqRefuseForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->rtnReqRefuseForComm($data);
		}
	}

	### ��ȯ��û�ź�
	function exchReqRefuseForComm($data)
	{
		$this->callCnt++;
		$this->log("exchReqRefuseForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '��û'){
			$this->log("Fail : Ŭ���ӿ�û���� != ��û");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=�ֹ���ȣ&clmreq_seq=�ֹ���ҿ�û����&refuse_rsn=�źλ���
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&refuse_rsn=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], urlencode($data['refuse_rsn']));
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '011')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set refuse_rsn='{$data['refuse_rsn']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# Ŭ���ӿ�û���� ������Ʈ
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='�ź�', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# �α��߰�
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > �ź�', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# ����ڵ尡 004 �̸� ��ûöȸ�� ó��
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# Ŭ���ӿ�û���� ������Ʈ
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='��ûöȸ', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# �α��߰�
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '��û > ��ûöȸ', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ ��ȯ��û�ź�\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("exchReqRefuseForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->exchReqRefuseForComm($data);
		}
	}

	### ��ǰ/��ȯ�԰�Ȯ��
	function clmEnterWhCompForComm($data)
	{
		$this->callCnt++;
		$this->log("clmEnterWhCompForComm START");

		$gClaim = $GLOBALS['db']->fetch("select c.clm_no, c.clm_seq, c.inpk_ordno, c.ordno, c.clm_tpnm, i.* from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where i.clmsno='{$data['clmsno']}' and i.itmsno='{$data['itmsno']}'");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s, itmsno:%s]", $data['clmsno'], $data['itmsno']));
		}
		else if ($gClaim['clm_statnm'] != '��ǰ/��ȯ��������'){
			$this->log("Fail : Ŭ���ӻ��� != ��ǰ/��ȯ��������");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// ��ǰ�� ���: 	orderclm_no=�ֹ���ȣ&ord_seq=�ֹ�����&clm_no=Ŭ���ӹ�ȣ&clm_seq=Ŭ���Ӽ���&delv_comp=�ù���ڵ�&delv_no=������ȣ&clm_crt_tpnm=��ǰ&supply_entr_no=��ü��ȣ&supply_ctrt_seq=���ް���Ϸù�ȣ
			// ����ȯ�� ���:	orderclm_no=�ֹ���ȣ&ord_seq=�ֹ�����&clm_no=Ŭ���ӹ�ȣ&clm_seq=Ŭ���Ӽ���&delv_comp=�ù���ڵ�&delv_no=������ȣ&clm_crt_tpnm=����ȯ
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&clm_no=%s&clm_seq=%s&delv_comp=%s&delv_no=%s&clm_crt_tpnm=%s",
					$this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['inpk_ordseq'], $gClaim['clm_no'], $gClaim['clm_seq'], $data['deliveryno'], $data['deliverycode'], $gClaim['clm_tpnm']);
			if ($gClaim['clm_tpnm'] == '��ǰ')
			{
				## load openstye conf
				$this->log("Load openstye_Conf START");
				if (class_exists('interpark'))
				{
					$interpark = new interpark();
					$interpark->getInpkOS();
					$this->inpkOSCfg = $interpark->inpkOSCfg;
				}
				else $this->log("Non-existent class interpark");
				$this->log("Load Interpark_Conf END");

				// ��ǰ�� ���: &supply_entr_no=��ü��ȣ&supply_ctrt_seq=���ް���Ϸù�ȣ
				$url .= sprintf("&supply_entr_no=%s&supply_ctrt_seq=%s", $this->inpkOSCfg['entrNo'], $this->inpkOSCfg['ctrtSeq']);
			}
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '012', '013')) === true){
				# Ŭ���ӻ��� ������Ʈ
				$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='��ǰ/��ȯ�԰�Ȯ������', latedt=now(), storeDvno='{$data['deliveryno']}', storeDvcode='{$data['deliverycode']}' where clmsno='{$data['clmsno']}'";
				$GLOBALS['db']->query($query);
				# �α��߰�
				$res = $GLOBALS['db']->query("select itmsno from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($row = $GLOBALS['db']->fetch($res))
				{
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$row['itmsno']}', '��ǰ/��ȯ�������� > ��ǰ/��ȯ�԰�Ȯ������', now())";
					$GLOBALS['db']->query($query);
				}

				$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaim['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaim['goodsno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ ��ǰ/��ȯ�԰�Ȯ��\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("clmEnterWhCompForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->clmEnterWhCompForComm($data);
		}
	}

	### ��ȯȮ��
	function exchOutWhCompForComm($data)
	{
		$this->callCnt++;
		$this->log("exchOutWhCompForComm START");

		$gClaim = $GLOBALS['db']->fetch("select c.clm_no, c.clm_seq, c.inpk_ordno, c.ordno, i.* from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where i.clmsno='{$data['clmsno']}' and i.itmsno='{$data['itmsno']}'");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s, itmsno:%s]", $data['clmsno'], $data['itmsno']));
		}
		else if ($gClaim['clm_statnm'] != '��ȯ/�����������'){
			$this->log("Fail : Ŭ���ӻ��� != ��ȯ/�����������");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## ����
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=�ֹ���ȣ&ord_seq=�ֹ�����&clm_no=Ŭ���ӹ�ȣ&clm_seq=Ŭ���Ӽ���&delv_comp=�ù���ڵ�&delv_no=������ȣ
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&clm_no=%s&clm_seq=%s&delv_comp=%s&delv_no=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['inpk_ordseq'], $gClaim['clm_no'], $gClaim['clm_seq'], $data['deliveryno'], $data['deliverycode']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '014')) === true){
				# Ŭ���ӻ��� ������Ʈ
				$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='��ȯȮ������', latedt=now(), exchangeDvno='{$data['deliveryno']}', exchangeDvcode='{$data['deliverycode']}' where clmsno='{$data['clmsno']}'";
				$GLOBALS['db']->query($query);
				# �α��߰�
				$res = $GLOBALS['db']->query("select itmsno from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($row = $GLOBALS['db']->fetch($res))
				{
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$row['itmsno']}', '��ȯ/����������� > ��ȯȮ������', now())";
					$GLOBALS['db']->query($query);
				}

				$settlelog[] = sprintf("- True : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'True');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaim['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : ��ǰ [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t��� = %s", 'False');
				$msg[] = sprintf("\t�ֹ���ҿ�û���� = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t������ũ �ֹ���ȣ = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t������ũ �ֹ����� = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t������ũ ��ǰ�ڵ� = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t�̳��� �ֹ���ȣ = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t�̳��� �ֹ����� = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t�̳��� ������ȣ = %s", $gClaim['goodsno']);
				$msg[] = sprintf("\t����ڵ� = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t����޼��� = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API ��ȣ�⿩��
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}
			}

			## ó��������
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## �����α�����
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] ������ũ ��ȯȮ��\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("exchOutWhCompForComm END");

		## API ��ȣ��
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->exchOutWhCompForComm($data);
		}
	}
}

?>
