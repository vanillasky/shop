<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/xmlWriter.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";

class e2i_goods_api extends putLog
{
	var $interfaceurl;	# �������̽� URL

	//http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=InsertProductAPIData&citeKey=[����Ű]&secretKey=[���Ű]&dataUrl=[XML������URL]
	var $connPath = "http://ipss1.interpark.com/openapi/product/PrdService.do?_method=%s&dataUrl=%s";
	var $testPath = "http://sptest.interpark.com/openapi/product/PrdService.do?_method=%s&dataUrl=%s"; //sptest.interpark.com
	var $errMsg;	# �����޽���

	function e2i_goods_api($glist, $part='', $isTest=false)
	{
		$this->putLog('prdInfo');
		$this->log("START");

		## �������̽� URL ����
		if ($isTest) $this->interfaceurl = $this->testPath;
		else $this->interfaceurl = $this->connPath;

		## ȣ��Ʈ ����
		$tmp = parse_url($_SERVER['HTTP_HOST']);
		$this->host = ($tmp['host'] ? $tmp['host'] : $_SERVER['HTTP_HOST']);

		## API START
		//$this->chkInpkCfg();
		$this->chkInpkOSCfg();
		switch ($part)
		{
			case "stock": # ������������ ����
				$gXml = $this->formatStock($glist);
				break;

			default: # ��ǰ��ü���� ����
				$gXml = $this->formatGoods($glist);
				break;
		}
		$xmlFile = $this->createXml($gXml);
		$this->strXml = $this->socketInterpark($xmlFile);

		## redefine strXml
		$pattern = array('<?xml version="1.0" encoding="euc-kr"?>', '<result>', '</result>');
		$this->strXml = str_replace($pattern, "", $this->strXml);
		$this->strXml = '<?xml version="1.0" encoding="euc-kr"?><result>' . "\n" . $this->strXml . '</result>';

		if (empty($this->errMsg) === false)
		{
			if ($part == 'stock') $this->log($this->errMsg["log"]);
			else $this->endHeader($this->errMsg);
		}

		$this->log("END");
	}

	### load and check to interpark conf
	function chkInpkCfg()
	{
		## load interpark conf
		$this->log("Load Interpark_Conf START");
		if (class_exists('interpark'))
		{
			$interpark = new interpark();
			$interpark->getInpk();
			$this->rootDir = $interpark->rootDir;
			$this->inpkCfg = $interpark->inpkCfg;
		}
		else $this->log("Non-existent class interpark");
		$this->log("Load Interpark_Conf END");

		## check interpark conf
		$this->log("Check Interpark_Conf START");
		if ($this->inpkCfg['entrNo'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty entrNo', 'Check Interpark_Conf END'),
				"header" => "������ũ ��ü��ȣ�� �������� �ʽ��ϴ�. ���� �����ϼ���."
				);
			return;
		}
		if ($this->inpkCfg['ctrtSeq'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty ctrtSeq', 'Check Interpark_Conf END'),
				"header" => "������ũ ���ް���Ϸù�ȣ�� �������� �ʽ��ϴ�. ���� �����ϼ���."
				);
			return;
		}
		$this->log("Check Interpark_Conf END");
	}

	### load and check to interpark conf
	function chkInpkOSCfg()
	{
		## load interpark conf
		$this->log("Load Interpark_OpenStyle_Conf START");
		if (class_exists('interpark'))
		{
			$interparkOS = new interpark();
			$interparkOS->getInpkOS();
			$this->rootDir = $interparkOS->rootDir;
			$this->inpkOSCfg = $interparkOS->inpkOSCfg;
		}
		else $this->log("Non-existent class interpark");
		$this->log("Load Interpark_OpenStyle_Conf END");

		## check interpark conf
		$this->log("Check Interpark_OpenStyle_Conf START");
		## ��ǰ�������Ű
		if ($this->inpkOSCfg['regiAuthKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty regiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "������ũ ��ǰ��� ����Ű�� �������� �ʽ��ϴ�. ������ũ�� �����ϼ���."
				);
			return;
		}
		## ��ǰ��Ϻ��Ű
		if ($this->inpkOSCfg['regiSecKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty regiSecKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "������ũ ��ǰ��� ���Ű�� �������� �ʽ��ϴ�. ������ũ�� �����ϼ���."
				);
			return;
		}
		## ��ǰ��������Ű
		if ($this->inpkOSCfg['modiAuthKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty modiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "������ũ ��ǰ���� ����Ű�� �������� �ʽ��ϴ�. ������ũ�� �����ϼ���."
				);
			return;
		}
		## ��ǰ�������Ű
		if ($this->inpkOSCfg['modiSecKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty modiSecKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "������ũ ��ǰ���� ���Ű�� �������� �ʽ��ϴ�. ������ũ�� �����ϼ���."
				);
			return;
		}

		$this->log("Check Interpark_OpenStyle_Conf END");
	}



	### Format goods
	function formatGoods($glist)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($glist) === false || count($glist) == 0){
			$this->log("Empty-glist (Format_goods Before)");
			return;
		}
		$this->log("Format_goods START");

		## ���
		$xmlReg = array();
		$xmlReg['title'] = "��ǰ���� ������";
		$xmlReg['description'] = "��ǰ���� ����� ���� Open Api ������";
		$xmlReg['item'] = array();

		## ����
		$xmlMod = array();
		$xmlMod['title'] = "��ǰ���� ������";
		$xmlMod['description'] = "��ǰ���� ������ ���� Open Api ������";
		$xmlMod['item'] = array();

		## Ư�̻���
		ob_start();
		@include dirname(__FILE__) . "/../conf/interpark_spcaseEd.php";
		$spcaseEd = ob_get_contents();
		ob_end_clean();

		ob_start();
		foreach ($glist as $goodsno)
		{
			$data = $GLOBALS['db']->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");
			if ($data['goodsno'])
			{
				if ($data['inpk_prdno'] == ''){
					$mode = 'register';
					$item = &$xmlReg['item'][];
					$item['sidx'] = count($xmlReg['item']);	# �Ϸù�ȣ
				}
				else {
					$mode = 'modify';
					$item = &$xmlMod['item'][];
					$item['sidx'] = count($xmlMod['item']);	# �Ϸù�ȣ
				}

				### e������ǰ�� �ʼ��ɼ�
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock']);

				/*--------------- �ʼ� ��û ������ : Start ---------------*/
				$item['supplyEntrNo'] = $this->inpkCfg['entrNo'];	# ��ü��ȣ
				$item['supplyCtrtSeq'] = $this->inpkCfg['ctrtSeq'];	# ���ް���Ϸù�ȣ

				if ($mode == 'modify'){
					$item['prdNo'] = $data['inpk_prdno'];	# ������ũ ��ǰ��ȣ
				}
				else {
					$item['prdStat'] = '01';	# ��ǰ���� (��������)
					$item['shopNo'] = '0000100000';	# ������ũ ������ȣ (��������)
					$item['omDispNo'] = $data['inpk_dispno'];	# ������ũ �����ڵ�
				}

				$item['prdNm'] = strcut($data['goodsnm'],80);	# ��ǰ�� (80bybte)
				$item['hdelvMafcEntrNm'] = $data['maker'];	# ������
				$item['prdOriginTp'] = $data['origin'];	# ������
				$item['taxTp'] = ($data['tax'] == '1' ? '01' : '02');	# �ΰ��鼼��ǰ
				$item['ordAgeRstrYn'] = 'N';	# ���ο�ǰ���� (��������)

				{ // �ǸŻ���
					if ($data['usestock'] && $stock==0) $data['runout'] = 1; # ����� ���� �ڵ� ǰ�� ó��

					if (!$data['open']) $item['saleStatTp'] = '03'; # �Ǹ�����
					else if ($data['runout'] == 1) $item['saleStatTp'] = '02'; # ǰ��
					else $item['saleStatTp'] = '01'; # �Ǹ���
					if ($mode == 'register' && $item['saleStatTp'] == '02') $item['saleStatTp'] = '03';
				}

				$item['saleUnitcost'] = $price;	# �ǸŰ�
				$item['saleLmtQty'] = ($data['usestock'] ? $stock : 99999);	# �Ǹż���

				if ($mode == 'register'){
					$item['saleStrDts'] = date('Ymd');	# �ǸŽ�����
					$item['saleEndDts'] = '99991231';	# �Ǹ������� (������)
				}

				$item['proddelvCostUseYn'] = 'N';	# ��ǰ��ۺ��뿩�� (��������)
				$item['prdBasisExplanEd'] = sprintf("<style type=\"text/css\"><!-- #godoContents {font:12px dotum; color:#000000;} --></style>\n<div id=\"godoContents\">\n%s\n</div>", $data['longdesc']);	# ��ǰ����

				{ // ��ǥ�̹���
					$imgs = $image_url = array();
					$imgs = array_merge( $imgs, explode("|",$data['img_l']) );
					$imgs = array_merge( $imgs, explode("|",$data['img_m']) );
					foreach ( $imgs as $filenm ){
						if (!$filenm) continue;

						if (preg_match('/^http(s)?:\/\/.+(jpg|gif|jpeg|png)$/i',$filenm)) {
								$image_url[] = $filenm;
						}
						else {
							if (file_exists( dirname(__FILE__) . "/../data/goods/{$filenm}") )
								$image_url[] = "http://{$this->host}{$this->rootDir}/data/goods/{$filenm}";
						}
						if ( count($image_url) >=1 ) break;
					}
					$item['zoomImg'] = str_replace(' ', '%20', $image_url[0]);
				}
				/*--------------------------------------------------------*/
				if ($mode == 'modify') $item['imgUpdateYn']="Y"; else $item['imgUpdateYn']="N";

				/*--------------- ���� ��û ������ : Start ---------------*/
				//$item['prdPrefix'] = '';	# �չ���
				//$item['prdPostfix'] = '';	# �޹���

				$keywords = array();
				$tmp = explode(",", $data['keyword']);
				for ($k = 0; $k < count($tmp); $k++){
					if ($k < 4) $keywords[] = $tmp[$k];
				}
				$item['prdKeywd'] = implode(",", $keywords);	# �����±� �ִ� 4�� �޸�����

				// �귣��
				if ($data['brandno']){
					list($item['brandNm']) = $GLOBALS['db']->fetch("select brandnm from ".GD_GOODS_BRAND." where Sno='{$data[brandno]}'");
				}
				else $item['brandNm'] = '';

				//$item['entrPoint'] = '';	# ��üPOINT
				//$item['minOrdQty'] = '';	# �ּұ��ż���
				$item['prdOption'] = $prdOption; # �ֹ����û���
				//$item['delvCost'] = '';	# ��ۺ�
				//$item['delvAmtPayTpCom'] = '';	# ��ۺ� ���� ���
				//$item['delvCostApplyTp'] = '';	# ��ۺ� ���� ���
				//$item['freedelvStdCnt'] = '';	# �����ۺ���� ����
				$item['spcaseEd'] = $spcaseEd;	# Ư�̻���
				//$item['intfreeInstmStrDts'] = '';	# �������Һν�����
				//$item['intfreeInstmEndDts'] = '';	# �������Һ�������
				//$item['listInstmMonths'] = '';	# �������Һΰ�����
				$item['pointmUseYn'] = 'N';	# ����Ʈ����Ͽ���
				if ($mode == 'register'){
					$item['ippSubmitYn'] = ($this->inpkCfg['ippSubmitYn'] == 'Y' ? 'Y' : 'N');	# ���ݺ� ��� ����
				}
				$item['shopDispInfo'] = '����Ÿ��<2>������ȣ<0000100000>���ù�ȣ<001410059>';	# ���ñ�������
				$item['originPrdNo'] = $data['goodsno'];	# ����ǰ��ȣ
				/*--------------------------------------------------------*/

				$item = array_map("trim", $item); // ������ �յ� space �� ���� �ʵ��� ���� �ʿ�
			}
		}
		$this->err( ob_get_clean() );
		$this->log("Format_goods END");

		return array('xmlReg' => $xmlReg, 'xmlMod' => $xmlMod);
	}

	### Format stock of goods
	function formatStock($glist)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($glist) === false || count($glist) == 0){
			$this->log("Empty-glist (Format_stock Before)");
			return;
		}
		$this->log("Format_stock START");

		$xmlMod = array();
		$xmlMod['title'] = "��ǰ���� ������";
		$xmlMod['description'] = "��ǰ���� ������ ���� Open Api ������";
		$xmlMod['item'] = array();

		ob_start();
		foreach ($glist as $goodsno)
		{
			$data = $GLOBALS['db']->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");
			if ($data['goodsno'])
			{
				if ($data['inpk_prdno'] == '') continue;
				$item = &$xmlMod['item'][];
				$item['sidx'] = count($xmlMod['item']);	# �Ϸù�ȣ

				### e������ǰ�� �ʼ��ɼ�
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock']);

				/*--------------- �ʼ� ��û ������ : Start ---------------*/
				$item['supplyEntrNo'] = $this->inpkCfg['entrNo'];	# ��ü��ȣ
				$item['supplyCtrtSeq'] = $this->inpkCfg['ctrtSeq'];	# ���ް���Ϸù�ȣ
				$item['prdNo'] = $data['inpk_prdno'];	# ������ũ ��ǰ��ȣ
				/*--------------------------------------------------------*/

				/*--------------- ���� ��û ������ : Start ---------------*/
				$item['prdNm'] = strcut($data['goodsnm'],80);	# ��ǰ�� (80bybte)
				{ // �ǸŻ���
					if ($data['usestock'] && $stock==0) $data['runout'] = 1; # ����� ���� �ڵ� ǰ�� ó��

					if (!$data['open']) $item['saleStatTp'] = '03'; # �Ǹ�����
					else if ($data['runout'] == 1) $item['saleStatTp'] = '02'; # ǰ��
					else $item['saleStatTp'] = '01'; # �Ǹ���
				}

				$item['saleUnitcost'] = $price;	# �ǸŰ�
				$item['saleLmtQty'] = ($data['usestock'] ? $stock : 99999);	# �Ǹż���
				$item['prdOption'] = $prdOption; # �ֹ����û���
				$item['originPrdNo'] = $data['goodsno'];	# ����ǰ��ȣ
				/*--------------------------------------------------------*/

				$item = array_map("trim", $item); // ������ �յ� space �� ���� �ʵ��� ���� �ʿ�
			}
		}
		$this->err( ob_get_clean() );
		$this->log("Format_stock END");

		return array('xmlMod' => $xmlMod);
	}

	### Option of goods
	function getGoodsOption($goodsno, $optnm, $usestock)
	{
		$rData = array('price'=>'', 'stock'=>'', 'prdOption'=>'');
		$optnm = explode("|",$optnm);
		$res = $GLOBALS['db']->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' and go_is_deleted <> '1' and go_is_display = '1' ");
		while ($tmp=$GLOBALS['db']->fetch($res,1)){
			$opt1[] = $tmp['opt1'];
			$opt2[] = $tmp['opt2'];
			if ($tmp['optno'] == ''){ // �ɼ��ڵ�
				$tmp['optno'] = $tmp['sno'];
				$GLOBALS['db']->query("update ".GD_GOODS_OPTION." set optno=sno where sno='{$tmp[sno]}'");
			}
			$opt[$tmp['opt1']][$tmp['opt2']] = array('price'=>$tmp['price'],'stock'=>$tmp['stock'],'optno'=>$tmp['optno']);

			## ����� ���
			$rData['stock'] += $tmp['stock'];
		}
		if ($opt1) $opt1 = array_unique($opt1);
		if ($opt2) $opt2 = array_unique($opt2);
		if (!$opt) $opt1 = $opt2 = array('');

		## �⺻ ���� �Ҵ�
		$rData['price']	  = $opt[$opt1[0]][$opt2[0]]['price'];

		## �ֹ����û���
		if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null)
		{
			$op2=$opt2[0];
			foreach ($opt1 as $op1){
				foreach ($opt2 as $op2){
					$oStock[] = $opt[$op1][$op2]['stock'];
					$oPrice[] = $opt[$op1][$op2]['price'] - $rData['price'];
					$oOptno[] = $opt[$op1][$op2]['optno'];
				}
			}

			$tmp = &$rData['prdOption'];
			if ($optnm[0]) $tmp .= $optnm[0].'<'.implode(',',$opt1).'>';
			if ($optnm[1]) $tmp .= $optnm[1].'<'.implode(',',$opt2).'>';
			if ($usestock && $oStock) $tmp .= '����<'.implode(',',$oStock).'>';
			if (array_sum($oPrice)) $tmp .= '�߰��ݾ�<'.implode(',',$oPrice).'>';
			$tmp .= '�ɼ��ڵ�<'.implode(',',$oOptno).'>';
		}

		return array_merge(array_values($rData), $rData);
	}

	### Create XML
	function createXml($gXml)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($gXml) === false || count($gXml) == 0){
			$this->log("Empty-gXml (Create_XML Before)");
			return;
		}
		$this->log("Create_XML START");

		if (class_exists('XmlWriter_py'))
		{
			$xmlFile = array('xmlReg'=>'', 'xmlMod'=>'');
			foreach ($xmlFile as $k => $filenm)
			{
				if (count($gXml[$k]['item']))
				{
					## Format XML
					$xmlWrite = new XmlWriter_py();
					$xmlWrite->act('result', $gXml[$k]);
					$buffer = $xmlWrite->getXml();
					$this->log(sprintf("method : [%s] Format XML", $k));

					## Create XML-File
					ob_start();
					$xmlFile[$k] = sprintf("interpark_%s_%s.xml", $k, date('ymdHis'));
					$file = $this->logPath . $xmlFile[$k];
					$fp = fopen($file,"w");
					fwrite($fp,$buffer);
					fclose($fp);
					$this->err( ob_get_clean() );
					$this->log(sprintf("method : [%s] Create XML-File", $k));
				}
			}
		}
		else $this->log("Non-existent class XmlWriter_py");

		$this->log("Create_XML END");
		return $xmlFile;
	}

	### Socket to INTERPARK(put Goods)
	function socketInterpark($xmlFile)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($xmlFile) === false || count($xmlFile) == 0){
			$this->log("Empty-xmlFile (Connection_INTERPARK Before)");
			return;
		}
		$this->log("Connection_INTERPARK START");

		$strXml = '';
		foreach ($xmlFile as $k => $filenm)
		{
			if ($filenm != '')
			{
				if (file_exists($this->logPath . $filenm) === false){
					$this->errMsg = array(
						"log" => array('Non-existent file : ' . $filenm, 'Connection_INTERPARK END'),
						"header" => "XML ������ �������� �ʾҽ��ϴ�. log ���� �۹̼��� Ȯ���Ͻ� �� �ٽ� �õ��ϼ���."
						);
					return;
				}

				## Socket
				$method = ($k == 'xmlReg' ? 'registerPrdInfo' : 'updatePrdInfo');
				$interfaceurl = sprintf($this->interfaceurl, $method, ("http://{$this->host}{$this->rootDir}/log/" . $filenm));
				$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $interfaceurl)));

				ob_start();
				if (class_exists('httpSock'))
				{
					$httpSock = new httpSock($interfaceurl);
					$httpSock->send(true);
				}
				else $this->log("Non-existent class httpSock");
				$this->err( ob_get_clean() );

				## Delete XML-File
				$this->delFile($this->logPath . $filenm);

				$strXml .= $httpSock->resContent;
				if (strpos($httpSock->resContent, "Interpark Partner Support System - �ý��� ����") !== false){
					$this->errMsg = array(
						"log" => array('Interpark Partner Support System - �ý��� ����', 'Connection_INTERPARK END'),
						"header" => "Interpark Partner Support System - �ý��� ����"
						);
					return;
				}

				## Read XML
				unset($xml);
				if (class_exists('StrucXMLParser'))
				{
					$xml = new StrucXMLParser();
					$xml->parse($httpSock->resContent);
					$data = $xml->parseOut();
				}
				else $this->log("Non-existent class StrucXMLParser");

				## Update Result
				ob_start();
				$result = &$data[RESULT][0][child];
				if ($result[TOTALSUCCESSCNT][0][data] > 0)
				{
					$items = &$result[ITEM];
					foreach ($items as $item){
						$field = $item[child];
						if ($field[SUCCESS][0][data] == 'true'){
							$query = "update ".GD_GOODS." set inpk_prdno='" . $field[PRDNO][0][data] . "', inpk_regdt=if(inpk_regdt='0000-00-00 00:00:00', now(), inpk_regdt), inpk_moddt=now() where goodsno='" . $field[ORIGINPRDNO][0][data] . "'";
							$res = $GLOBALS[db]->query($query);
							$this->log(sprintf("Update_Result : goodsno[%s], inpk_prdno[%s] %s", $field[ORIGINPRDNO][0][data], $field[PRDNO][0][data], ($res ? 'True' : 'False')));
						}
					}
				}
				$this->err( $err=ob_get_clean() );
				$this->log("Update_Result : " . ($err == '' ? 'True' : 'False'));
			}
		}

		$this->log("Connection_INTERPARK END");
		return $strXml;
	}

	### Delete XML-File
	function delFile($filePath)
	{
		$res = @unlink($filePath);
		$this->log(sprintf("Delete file : [%s] %s", ($res ? 'True' : 'False'), basename($filePath)));
	}
}

?>