<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/xmlWriter.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";
if(class_exists("Services_JSON")===false) require dirname(__FILE__) . "/../lib/json.class.php";

class e2i_goods_openstyle_api extends putLog
{
	/*��ǰ�� �ϳ��� xml ���Ϸ� ���� �ؾ��Ѵ�.*/
	var $interfaceurl;	# �������̽� URL
	//var $connPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	//var $testPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	var $connPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	var $testPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	var $errMsg;	# �����޽���
	var $citeKey;	# ��ǰ���/���� ����Ű
	var $secretKey;	# ��ǰ���/���� ���Ű
	var $interfaceId;	#���:InsertProductAPIData; ���� : UpdateProductAPIData


	function e2i_goods_openstyle_api($glist, $part='', $isTest=false)
	{
		$this->putLog('prdInfo');//�ӽ��ּ�
		$this->log("START");//�ӽ��ּ�

		if ($isTest) $this->interfaceurl = $this->testPath;
		else $this->interfaceurl = $this->connPath;

		## ȣ��Ʈ ����
		$tmp = parse_url($_SERVER['HTTP_HOST']);
		$this->host = ($tmp['host'] ? $tmp['host'] : $_SERVER['HTTP_HOST']);
		## API START
		$this->chkInpkOSCfg();//����Ű,���Ű Ȯ��



		//--- ��ǰ ������ŭ �ݺ� ---//
		$goodsnoArray = array();
		$p=0;
		foreach ($glist as $goodsno){
			$goodsnoArray[0] = $goodsno;
			switch ($part)
			{
				case "stock": # ������������ ����
					$gXml = $this->formatStock($goodsnoArray);
					break;

				default: # ��ǰ��ü���� ����
					$gXml = $this->formatGoods($goodsnoArray);
					break;
			}
			$xmlFile = $this->createXml($gXml);


			$this->strXml .= $this->socketInterpark($xmlFile);//�ӽ��ּ�
			## redefine strXml
			$pattern = array('<?xml version="1.0" encoding="euc-kr"?>', '<result>', '</result>');
			$this->strXml = str_replace($pattern, "", $this->strXml);
			$this->strXml = '<?xml version="1.0" encoding="euc-kr"?><result>' . "\n" . $this->strXml . '</result>';

			if (empty($this->errMsg) === false)
			{
				if ($part == 'stock') $this->log($this->errMsg["log"]);
				else $this->endHeader($this->errMsg);
			}
			$p++;

		}

		$this->log("END");//�ӽ��ּ�
	}

	### load and check to interpark conf
	function chkInpkOSCfg()
	{
		## load interpark conf
		$this->log("Load Interpark_OpenStyle_Conf START");
		if (class_exists('interpark'))
		{
			$interpark = new interpark();//������ũ ����
			$interpark->getInpkOS();//���½�Ÿ�� ���� �ҷ�����
			$this->rootDir = $interpark->rootDir;
			$this->inpkOSCfg = $interpark->inpkOSCfg;
		}
		else $this->log("Non-existent class interpark");
		$this->log("Load Interpark_OpenStyle_Conf END");

		## check interpark conf
		$this->log("Check Interpark_OpenStyle_Conf START");

		if($this->inpkOSCfg['regiAuthKey']=="" || $this->inpkOSCfg['regiSecKey']==""){

			$this->errMsg = array(
				"log" => array('Empty regiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "������ũ ����Ű�� �������� �ʽ��ϴ�. ������ũ�� �����ϼ���."
			);
			return;

		}else if($this->inpkOSCfg['modiAuthKey']=="" || $this->inpkOSCfg['modiSecKey']==""){

			$this->errMsg = array(
				"log" => array('Empty regiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "������ũ ����Ű�� �������� �ʽ��ϴ�. ������ũ�� �����ϼ���."
			);
			return;

		}

		$this->log("Check Interpark_OpenStyle_Conf END");//�ӽ��ּ�
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
					$this->interfaceId="InsertProductAPIData";
				}
				else {
					$mode = 'modify';
					$item = &$xmlMod['item'][];
					$item['sidx'] = count($xmlMod['item']);	# �Ϸù�ȣ
					$this->interfaceId="UpdateProductAPIData";
					$item['prdNo'] = $data['inpk_prdno'];	# ������ũ ��ǰ��ȣ]
				}

				### e������ǰ�� �ʼ��ɼ�
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock'], $data['use_option']);//(��ǰ��ȣ, �ɼ�����|�ɼ�����, ���)

				### e������ǰ�� �߰��ɼ�
				//$prdAddOption = $this->getGoodsAddOption($data['goodsno'], $data['addoptnm']);//(��ǰ��ȣ,�ɼ�����)

				/*--------------- �ʼ� ��û ������ ���� : Start ---------------*/

				$item['prdStat']="01";																	//��ǰ����
				$item['shopNo']="0000100000";															//������ũ ������ȣ (default - 0000100000)
				$item['omDispNo']=$data['inpk_dispno'];													//������ũ �����ڵ�
				$item['prdNm']=strcut($data['goodsnm'],80);
				if(!$data['maker']) $data['maker']='����';//��ǰ�� - �ִ� 80byte
				$item['hdelvMafcEntrNm']=$data['maker'];												//������ü��
				if(!$data['origin']) $data['origin']='����';
				$item['prdOriginTp']=$data['origin'];													//������
				$item['taxTp']=($data['tax'] == '1' ? '01' : '02');										//�ΰ��鼼��ǰ - ������ǰ:01, �鼼��ǰ:02, ������ǰ:03
				$item['ordAgeRstrYn']="N";																//�� ������..���ο�ǰ���� - ���ο�ǰ:Y, �Ϲݿ�ǰ:N

				//$item['saleStatTp']="";																//�ǸŻ��� - �Ǹ���:01, ǰ��:02, �Ǹ�����:03, �Ͻ�ǰ��:05
				{ // �ǸŻ���
					if ($data['usestock'] && $stock==0) $data['runout'] = 1;							//����� ���� �ڵ� ǰ�� ó��

					if (!$data['open']) $item['saleStatTp'] = '03';										//�Ǹ�����
					else if ($data['runout'] == 1) $item['saleStatTp'] = '02';							//ǰ��
					else $item['saleStatTp'] = '01';													//�Ǹ���
					if ($mode == 'register' && $item['saleStatTp'] == '02') $item['saleStatTp'] = '03';
				}

				$item['saleUnitcost']=$price;															//�ǸŰ�
				$item['saleLmtQty']=($data['usestock'] ? $stock : 99999);								//�Ǹż��� - 99999 �� ���Ϸ� �Է�

				if ($this->interfaceId == 'InsertProductAPIData'){
					$item['saleStrDts'] = date('Ymd');													//�ǸŽ����� yyyyMMdd => ȣ���� ��¥
					$item['saleEndDts'] = '99991231';													//�Ǹ������� (������)
				}

				$item['proddelvCostUseYn']="N";															//�� ������ ��ǰ��ۺ��뿩�� - ��ǰ��ۺ���:Y, ��ü��ۺ���å���:N
				$item['prdrtnCostUseYn']="N";															//��ǰ ��ǰ�ù�� ��뿩�� - ��ǰ��ǰ�ù����:Y, ��ü��ǰ�ù����:N
				//$item['rtndelvCost']="";																//��ǰ ��ǰ�ù��. prdrtnCostUseYn �� 'Y' �� ��� �ʼ���

				//��ǰ����
				$item['prdBasisExplanEd']=sprintf("<style type=\"text/css\"><!-- #godoContents {font:12px dotum; color:#000000;} --></style>\n<div id=\"godoContents\">\n%s\n</div>", $data['longdesc']);

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

				//$item['prdPostfix']="";																//�޹��� - �ִ� 80byte [�ӽ�����]
				{//Ű����
					$keywords = array();
					$tmp = explode(",", $data['keyword']);
					for ($k = 0; $k < count($tmp); $k++){
						if ($k < 4) $keywords[] = $tmp[$k];
					}
					$item['prdKeywd']=implode(",", $keywords);											//�����±� - �ִ� 4������, �޸��� ����
				}

				//$item['brandNm']="";//�귣���
				if ($data['brandno']){
					list($item['brandNm']) = $GLOBALS['db']->fetch("select brandnm from ".GD_GOODS_BRAND." where Sno='{$data[brandno]}'");
				}
				else $item['brandNm'] = '';

				//$item['entrPoint']="";																//��üPOINT - ����Ʈ �ݾ� �Է�,�ǸŰ��� �ִ� 10%���� ����
				//$item['minOrdQty']="";																//�ּұ��ż��� - 1�� �̻� �Է� [�ӽ�����]
				//$item['perordRstrQty']="";															//1ȸ�� �ֹ� ���� ���� [�ӽ�����]

				//������ �ɼ� �̸��� �����Ҷ� ���. ����1�� Ÿ��, ����2�� �������� �ϴ� ��� Ÿ��,������ �� �±׼���.���� ���� ���� ��� ����1,����2�� �⺻ ������[�ӽ�����]
				$optnmArray = implode(",",explode("|",$data['optnm']));//AŸ��,BŸ��
				$item['selOptName']='';//$optnmArray;														//������ �ɼ� �̸��� ����

				//������ �ɼǳ��� ���� ����. �ɼ��� ���� ��� �ʼ�.01-��ϼ�, 02-�����ټ�. ������ �ɼǸ� �����.[�ӽ�����]
				$item['optPrirTp']="02";																//������ �ɼǳ��� ���� ����

				$item['prdOption']=$prdOption;															//�ֹ��� ���� ����
				$item['addOption']=$prdAddOption;														//�ֹ��� �߰� �ɼ�

				//$item['addQtyUseYn']="";																//��ǰ������ �߰��� �ɼ� ���� �Է� �ʵ� ��뿩�� [�ӽ�����]
				//$item['inOpt']="";																	//�Է��� �ɼ�. ex) ����ǰ�� �Է��ϼ���. [�ӽ�����]

				//$item['delvCost']="";																	//��ۺ� -��ǰ ��ۺ� �����϶� �ʼ�, 0�̸� ������ [�ӽ�����]
				//$item['delvAmtPayTpCom']="";															//��ۺ� ���� ��� -
				//$item['delvCostApplyTp']="";															//��ۺ� ���� ��� - ����:01, ������:02 [�ӽ�����]
				//$item['freedelvStdCnt']="";															//�����۱��� ���� - ���ؼ��� �Է� ������� ���� ��� 0 [�ӽ�����]
				$item['spcaseEd']=$spcaseEd;															//Ư�̻���
				//$item['intfreeInstmStrDts']="";														//�������Һν����� - yyyyMMdd [�ӽ�����]
				//$item['intfreeInstmEndDts']="";														//�������Һ������� - yyyyMMdd [�ӽ�����]
				//$item['listInstmMonths']="";															//�������Һΰ����� - 3,6,10,12 ���� ���� �Է� [�ӽ�����]
				$item['pointmUseYn']="N";																//����Ʈ����Ͽ��� - ����Ʈ����ǰ:Y,�Ϲݻ�ǰ:N 500�� �̸� ��ǰ�� ��� �Ұ�
				//$item['ippSubmitYn']="";																//���ݺ� ��� ���� -�����:Y, ��Ͼ���:N
				if ($this->interfaceId == 'InsertProductAPIData'){
					$item['ippSubmitYn'] = ($this->inpkOSCfg['ippSubmitYn'] == 'Y' ? 'Y' : 'N');		// ���ݺ� ��� ����
				}

				$item['originPrdNo']=$data['goodsno'];													//����ǰ��ȣ(���޾�ü��ǰ�ڵ�)
				//$item['shopDispInfo']="����Ÿ��<2>������ȣ<0000100000>���ù�ȣ<001410059>";				//���ñ������� [�ӽ�����]
				$item['detailImg']="";																	//���̹��� - ���̹��� URL
				if ($mode == 'modify') $item['imgUpdateYn']="Y"; else $item['imgUpdateYn']="N";   //�̹��� update ����
				/*--------------- �ʼ� ��û ������ ���� : End ---------------*/

				$item = array_map("trim", $item);														// ������ �յ� space �� ���� �ʵ��� ���� �ʿ�

				$json = new Services_JSON();
				$data['extra_info'] = $json->decode($data['extra_info']);
				$extra_info_set = array();
				foreach($data['extra_info'] as $extra_info)
				{
					if(isset($extra_info->inpk_code) && strlen($extra_info->inpk_type))
					{
						$extra_info_set[$extra_info->inpk_code] = array(
							'infoCd' => $extra_info->inpk_type,
							'infoTx' => $extra_info->desc
						);
					}
				}
				ksort($extra_info_set);
				$item['asInfo'] = $extra_info_set['INFOAS']['infoTx'];
				unset($extra_info_set['INFOAS']);
				$item['prdinfoNoti'] = array();
				$item['prdinfoNoti']['info'] = array();
				foreach($extra_info_set as $infoSubNo => $extra_info)
				{
					$item['prdinfoNoti']['info'][] = array(
						'infoSubNo' => $infoSubNo,
						'infoCd' => $extra_info['infoCd'],
						'infoTx' => $extra_info['infoTx']
					);
				}
			}//if ($data['goodsno'])
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
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock'], $data['use_option']);

				### e������ǰ�� �߰��ɼ�
				//$prdAddOption = $this->getGoodsAddOption($data['goodsno'], $data['addoptnm']);//(��ǰ��ȣ,�ɼ�����)

				/*--------------- �ʼ� ��û ������ : Start ---------------*/
				$item['prdNo'] = $data['inpk_prdno'];	# ������ũ ��ǰ��ȣ]
				$item['supplyEntrNo'] = $this->inpkOSCfg['entrNo'];	# ��ü��ȣ
				$item['supplyCtrtSeq'] = $this->inpkOSCfg['ctrtSeq'];	# ���ް���Ϸù�ȣ
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

				$optnmArray = implode(",",explode("|",$data['optnm']));//AŸ��,BŸ��
				$item['selOptName']="";//$optnmArray;														//������ �ɼ� �̸��� ����

				$item['saleUnitcost'] = $price;	# �ǸŰ�
				$item['saleLmtQty'] = ($data['usestock'] ? $stock : 99999);	# �Ǹż���
				$item['prdOption'] = $prdOption; # �ֹ����û���
				$item['addOption']=$prdAddOption; # �ֹ��߰��ɼ�
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
	function getGoodsOption($goodsno, $optnm, $usestock, $use_option)
	{

		$rData = array('price'=>'', 'stock'=>'', 'prdOption'=>'');//���ϵ� ������ �ʱ�ȭ
		$optnm = explode("|",$optnm);//AŸ��,BŸ��

		$query = "select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' and go_is_deleted <> '1' and go_is_display = '1'";
		if (!$use_option) {
			$query .= " and link = '1' ";
		}
		$res = $GLOBALS['db']->query($query);
		while ($tmp=$GLOBALS['db']->fetch($res,1)){

			if (!$use_option) {
				$tmp['opt1'] = $tmp['opt2'] = '';
			}

			$opt1[] = $tmp['opt1'];//����1,����2
			$opt2[] = $tmp['opt2'];//��,��,��

			if ($tmp['optno'] == ''){ // �ɼ��ڵ�
				$tmp['optno'] = $tmp['sno'];
				$GLOBALS['db']->query("update ".GD_GOODS_OPTION." set optno=sno where sno='{$tmp[sno]}'");
			}
			$opt[$tmp['opt1']][$tmp['opt2']] = array('price'=>$tmp['price'],'stock'=>$tmp['stock'],'optno'=>$tmp['optno']);
			## ����� ���
			$rData['stock'] += $tmp['stock'];
		}
		if ($opt1) $opt1 = array_unique($opt1);//AŸ��,BŸ��
		if ($opt2) $opt2 = array_unique($opt2);//��,��
		if (!$opt) $opt1 = $opt2 = array('');

		## �⺻ ���� �Ҵ�
		$rData['price']	  = $opt[$opt1[0]][$opt2[0]]['price'];

		## �ֹ����û���
		if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null)
		{
			$op2=$opt2[0];

			foreach ($opt1 as $op1){

				$tmp = &$rData['prdOption'];
				$tmp .= '{'.$op1.'<';
				$opArray=array();
				$oStock=array();
				$oPrice=array();
				$oOptno=array();
				$useYn = array();

				foreach ($opt2 as $op2){
					if(!$usestock) $oStock[] = '999'; else $oStock[] = $opt[$op1][$op2]['stock'];
					$total_price = $opt[$op1][$op2]['price'] - $rData['price'];
					if($total_price==0) $oPrice[] = '0'; else $oPrice[] = $opt[$op1][$op2]['price'] - $rData['price'];
					$oOptno[] = $opt[$op1][$op2]['optno'];
					//$opArray[] = $op2;
					if($op2) $opArray[] = $op2; else $opArray[]="����";
					if(!$usestock){
						$useYn[] = "Y";
					}else if(!$opt[$op1][$op2]['stock'] || $total_price<0 || !$opt[$op1][$op2]['optno']){
						$useYn[] = "N";
					}else{
						$useYn[] = "Y";
					}

				}

				$tmp .=implode(',',$opArray).'>';

				if ($usestock && $oStock) $tmp .= '����<'.implode(',',$oStock).'>';
				else $tmp .= '����<'.implode(',',$oStock).'>';
				//if (array_sum($oPrice)) $tmp .= '�߰��ݾ�<'.implode(',',$oPrice).'>';
				$tmp .= '�߰��ݾ�<'.implode(',',$oPrice).'>';
				$tmp .= '�ɼ��ڵ�<'.implode(',',$oOptno).'>��뿩��<'.implode(',',$useYn).'>}';

			}
		}
		return array_merge(array_values($rData), $rData);
	}

	### Option of goods

	function getGoodsAddOption($goodsno,$goodsAddNm)
	{
		global $db;
		### �߰��ɼ�
		$r_addoptnm = explode("|",$goodsAddNm);
		for ($i=0;$i<count($r_addoptnm);$i++){
			list ($addoptnm[],$addoptreq) = explode("^",$r_addoptnm[$i]);
		}

		$query = "select * from ".GD_GOODS_ADD." where goodsno='$goodsno' order by sno";
		$res = $db->query($query);

		while ($tmp=$db->fetch($res)){
			$addopt[$tmp[step]][] = $tmp;
		}

		$returnValue="";
		foreach ($addopt as $k=>$v){

			$returnValue .="{".$addoptnm[$k];
			$opt=array();
			$addprice=array();
			$optcode=array();

			//�����׸�
			foreach ($v as $v2){
				$opt[] = $v2['opt'];
				$addprice[] = $v2['addprice'];
				$optcode[]  = $v2['sno'];
			}

			if($opt) $returnValue .= "<".implode(",",$opt).">";
			if($addprice) $returnValue .= "�ݾ�<".implode(",",$addprice).">";
			if($optcode) $returnValue .= "�ɼ��ڵ�<".implode(",",$optcode).">";
			$returnValue .= "}";

		}

		return $returnValue;

	}


	function getmicrotime()
	{
		$microtimestmp = split(" ",microtime());
        return str_replace(".","",$microtimestmp[0]+$microtimestmp[1]);

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
					$getmicrotime = explode(" ", microtime());
					$xmlFile[$k] = sprintf("interpark_%s_%s_.xml", $k, date('ymdHis')."_".$this->getmicrotime());
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
			$this->log("Empty-xmlFile (Connection_INTERPARK_OpenStyle Before)");
			return;
		}
		$this->log("Connection_INTERPARK_OpenStyle START");

		$strXml = '';
		foreach ($xmlFile as $k => $filenm)
		{
			if ($filenm != '')
			{
				if (file_exists($this->logPath . $filenm) === false){
					$this->errMsg = array(
						"log" => array('Non-existent file : ' . $filenm, 'Connection_INTERPARK_OpenStyle_xml_fail END'),
						"header" => "XML ������ �������� �ʾҽ��ϴ�. log ���� �۹̼��� Ȯ���Ͻ� �� �ٽ� �õ��ϼ���."
						);
					return;
				}

				## Socket
				if($k == 'xmlReg'){
					$method ='InsertProductAPIData';
					$this->citeKey = $this->inpkOSCfg['regiAuthKey'];
					$this->secretKey = $this->inpkOSCfg['regiSecKey'];

				}else{

					$method ='UpdateProductAPIData';
					$this->citeKey = $this->inpkOSCfg['modiAuthKey'];
					$this->secretKey = $this->inpkOSCfg['modiSecKey'];
				}

				//�ʿ��� ���� : ?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
				$interfaceurl = sprintf($this->interfaceurl, $this->interfaceId,$this->citeKey,$this->secretKey, ("http://{$this->host}{$this->rootDir}/log/" . $filenm));
				$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $interfaceurl)));

				ob_start();
				if (class_exists('httpSock'))
				{
					$this->log("interpark_url = ".$interfaceurl." - interpark_id = ".$this->interfaceId);
					$httpSock = new httpSock($interfaceurl);//[�ӽ��ּ�]
					$httpSock->send(true);//[�ӽ��ּ�]
				}
				else $this->log("Non-existent class httpSock");
				$this->err( ob_get_clean() );

				## Delete XML-File
				$this->delFile($this->logPath . $filenm);//[�ӽ��ּ�]

				$strXml .= $httpSock->resContent;
				if (strpos($httpSock->resContent, "Interpark Partner Support System - �ý��� ����") !== false){
					$this->errMsg = array(
						"log" => array('Interpark Partner Support System - �ý��� ����', 'Connection_INTERPARK_connect_OpenStyle END'),
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
				if ($result[SUCCESS])
				{
					$items = &$result[SUCCESS];
					foreach ($items as $item){
						$field = $item[child];
						if ($field[PRDNO][0][data]){

							$query = "update ".GD_GOODS." set inpk_prdno='" . $field[PRDNO][0][data] . "', inpk_regdt=if(inpk_regdt='0000-00-00 00:00:00', now(), inpk_regdt), inpk_moddt=now() where goodsno='" . $field[ORIGINPRDNO][0][data] . "'";
							$res = $GLOBALS[db]->query($query);
							$this->log(sprintf("Update_Result : goodsno[%s], inpk_prdno[%s] %s", $field[ORIGINPRDNO][0][data], $field[PRDNO][0][data], ($res ? 'True' : 'False')));
						}
					}

					$this->log("StrucXMLParser success");

				}else{
					$items = &$result[ERROR];
					foreach ($items as $item){
						$field = $item[child];
						$this->log("StrucXMLParser fail code = ".$field[CODE][0][data]);
						$this->log("StrucXMLParser fail message = ".$field[MESSAGE][0][data]);
						$this->log("StrucXMLParser fail message = ".$field[EXPLANATION][0][data]);
					}
					$this->log("StrucXMLParser fail");
				}

				$this->err( $err=ob_get_clean() );
				$this->log("StrucXMLParser end");

				$this->log("Update_Result : " . ($err == '' ? 'True' : 'False'));
			}
		}

		$this->log("Connection_INTERPARK_OpenStyle END");
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
