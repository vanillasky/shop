<?

class eTax
{
	var $godo, $gPath, $tPath;

	function eTax()
	{
		$this->gPath = dirname(__FILE__)."/../conf/godomall.cfg.php";
		$this->tPath = dirname(__FILE__)."/../conf/tax.cfg.php";
		$this->getGodo();
	}

	function getGodo()
	{
		global $godo;

		if (isset($godo) === true) $this->godo = $godo;
		else {
			if (!is_file($this->gPath)) return false;
			$file = file($this->gPath);
			$this->godo = decode($file[1],1);
		}

		if (file_exists($this->tPath))
		{
			$file = file($this->tPath);
			$this->godo[tax] = decode($file[1],1);
			if (isset($godo) === true){
				$godo['tax'] = $this->godo['tax'];
			}
		}
		else {
			$this->updateTaxPoint();

			$tmp = $this->godo;
			unset($tmp[tax]);
			$ret = encode($tmp,1);
			if(is_file($this->gPath)) unlink($this->gPath);
			$fp = fopen($this->gPath,"w");
			fwrite($fp,"<?/* \n");
			fwrite($fp,$ret."\n");
			fwrite($fp,"*/?>");
			fclose($fp);
		}
	}

	function hashdata(&$data)
	{
		/***************************************************************************************************
		*  hashdata ����
		*    - ������ ���Ἲ�� �����ϴ� �����ͷ� ��û�� �ʼ� �׸�.
		*    - godosno �� �������� md5 ������� ������ �ؽ���.
		***************************************************************************************************/

		$data[godosno]	= $this->godo[sno];					# ������ȣ
		$data[hashdata]	= md5($data[godosno]);				# hashdata ����
	}

	function isExists($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^������ �����ϼ���.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - �������̵� ȯ�������� �������̵�� ��ġ���� �ʽ��ϴ�.^������ �����ϼ���.");

		$data=array();
		$this->hashdata($data);
		return array('400', readurl("http://godotax.godo.co.kr/sock_isExists.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}"));
	}

	function putMerchant($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^������ �����ϼ���.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - �������̵� ȯ�������� �������̵�� ��ġ���� �ʽ��ϴ�.^������ �����ϼ���.");

		$data=array();
		$this->hashdata($data);
		$data = array_merge($data, $args);
		if ($data[userid] != '') $data[userid] = 'CGO_' . $data[userid];	# ��Ź�� ���̵�
		unset($data[mode]);
		unset($data[dummy]);
		return array('400', readpost("http://godotax.godo.co.kr/sock_putMerchant.php", $data));
	}

	function putTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^ ������ �����ϼ���.");
		if ($this->godo[tax] == '' || $this->godo[tax] == '0') return array('600', "false - ���ڼ��ݰ�꼭 �ܿ�����Ʈ�� �����մϴ�.^�߰� �����ϼż� ����ϼ���.");

		$data=array();
		$this->hashdata($data);

		ob_start();
		$taxData = $GLOBALS[db]->fetch("select * from ".GD_TAX." where `sno`='{$args[chk]}'", "assoc");
		$ordData = $GLOBALS[db]->fetch("select email, mobileOrder, cashreceipt from ".GD_ORDER." where ordno='{$taxData[ordno]}'");
		$data[method]			= "ORD";					# ������
		$data[doc_number]		= "ORD{$taxData[ordno]}_{$taxData[sno]}";	# ����������ȣ

		### ���޾�ü����
		include dirname(__FILE__)."/../conf/config.php";
		$regnum = str_replace("-", "", trim($cfg[compSerial]));
		$data[sup_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# ���޾�ü����ڹ�ȣ(XXX-XX-XXXXX)
		$data[sup_company]		= $cfg[compName];			# ���޾�üȸ���
		$data[sup_employer]		= $cfg[ceoName];			# ���޾�ü��ǥ�ڸ�
		$data[sup_address]		= $cfg[address];			# ���޾�ü�ּ�
		$data[sup_bizcond]		= $cfg[service];			# ���޾�ü����
		$data[sup_bizitem]		= $cfg[item];				# ���޾�ü����
		$data[sup_empemail]		= $cfg[adminEmail];			# ���޾�ü������̸���
		$data[sup_empmobile]	= $cfg[smsAdmin];			# ���޾�ü������ڵ�����ȣ

		### �����ü����
		$regnum = str_replace("-", "", trim($taxData[busino]));
		$data[buy_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# �����ü����ڹ�ȣ(XXX-XX-XXXXX)
		$data[buy_company]		= $taxData[company];		# �����üȸ���
		$data[buy_employer]		= $taxData[name];			# �����ü��ǥ�ڸ�
		$data[buy_address]		= $taxData[address];		# �����ü�ּ�
		$data[buy_bizcond]		= $taxData[service];		# �����ü����
		$data[buy_bizitem]		= $taxData[item];			# �����ü����
		$data[buy_empemail]		= $ordData[email];			# �����ü������̸���
		$data[buy_empmobile]	= $ordData[mobileOrder];	# �����ü������ڵ�����ȣ

		### �ݾ�
		$data[tax_supprice]		= $taxData[supply];			# ���ް����Ѿ�
		$data[tax_taxprice]		= $taxData[surtax];			# �����Ѿ�
		$data[pay_totalprice]	= $taxData[price];			# �հ�ݾ�
		$data[gen_tm]			= str_replace("-", "", $taxData[issuedate]) . '000000';	# �ۼ�����

		### ����
		$data[tax_gendates][0]	= str_replace("-", "", $taxData[issuedate]);		# ǰ���������
		$data[item_names][0]	= $taxData[goodsnm];		# ǰ���
		$data[tax_supprices][0]	= $taxData[supply];			# ���ް���
		$data[tax_taxprices][0]	= $taxData[surtax];			# ����
		ob_end_clean();

		if ($ordData[cashreceipt] != '') return array('400', "false - ���ݿ������� ����� �����Դϴ�.^���ݿ������� ���ݰ�꼭�� ���ÿ� �߱޵Ǿ� �� �� �����ϴ�.");

		$out = readpost("http://godotax.godo.co.kr/sock_putTaxbill.php", $data);

		if ($out == 'true'){
			$GLOBALS[db]->query("update ".GD_TAX." set step=3, agreedt = now(), doc_number = '{$data[doc_number]}' where `sno`='{$args[chk]}'");
			$this->godo[tax]--;
			$this->updateTaxPoint();
		}

		return array('400', $out);
	}

	function putSugiTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^ ������ �����ϼ���.");
		if ($this->godo[tax] == '' || $this->godo[tax] == '0') return array('600', "false - ���ڼ��ݰ�꼭 �ܿ�����Ʈ�� �����մϴ�.^�߰� �����ϼż� ����ϼ���.");

		$headers = apache_request_headers();
		foreach ( $args as $k => $v ){
			if (is_array($v) === true)
				foreach ( $v as $sk => $sv ) $args[$k][$sk] = iconv("UTF-8","EUC-KR",$sv);
			else $args[$k] = iconv("UTF-8","EUC-KR",$v);
		}
		

		/** ������ & ���޹޴��� üũ **/
		if (strlen($args[SupNo]) == 0) return array('600', "false - ������ ��Ϲ�ȣ�� �ʼ��Դϴ�.");
		if (strlen($args[SupComp]) == 0) return array('600', "false - ������ ��ȣ�� �ʼ��Դϴ�.");
		if (strlen($args[SupEmployer]) == 0) return array('600', "false - ������ ������ �ʼ��Դϴ�.");
		if (strlen($args[SupAddr]) == 0) return array('600', "false - ������ �ּҴ� �ʼ��Դϴ�.");
		if (strlen($args[SupCond]) == 0) return array('600', "false - ������ ���´� �ʼ��Դϴ�.");
		if (strlen($args[SupItem]) == 0) return array('600', "false - ������ ����� �ʼ��Դϴ�.");
		if (strlen($args[BuyNo]) == 0) return array('600', "false - ���޹޴��� ��Ϲ�ȣ�� �ʼ��Դϴ�.");
		if (strlen($args[BuyComp]) == 0) return array('600', "false - ���޹޴��� ��ȣ�� �ʼ��Դϴ�.");
		if (strlen($args[BuyEmployer]) == 0) return array('600', "false - ���޹޴��� ������ �ʼ��Դϴ�.");
		if (strlen($args[BuyAddr]) == 0) return array('600', "false - ���޹޴��� �ּҴ� �ʼ��Դϴ�.");

		$data=array();
		$this->hashdata($data);

		$data[method]			= "WRI";					# ������
		$data[doc_number]		= "WRI" . date('YmdHis');	# ����������ȣ
		$data[tax_type]			= $args[TaxType];			# �������� (VAT:����(���ݰ�꼭), FRE:�鼼(��꼭), RCP:������)
		$data[bill_type]		= $args[Indicator];			# û�������� (T01:������, T02:û����)
		$data[ref_volume]		= $args[Volume];			# å��ȣ ��
		$data[ref_number]		= $args[Number];			# å��ȣ ȣ
		$data[ref_serial]		= $args[SerialNo];			# �Ϸù�ȣ

		### ���޾�ü����
		$regnum = str_replace("-", "", trim($args[SupNo]));
		$data[sup_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# ���޾�ü����ڹ�ȣ(XXX-XX-XXXXX)
		$data[sup_company]		= $args[SupComp];			# ���޾�üȸ���
		$data[sup_employer]		= $args[SupEmployer];		# ���޾�ü��ǥ�ڸ�
		$data[sup_address]		= $args[SupAddr];			# ���޾�ü�ּ�
		$data[sup_bizcond]		= $args[SupCond];			# ���޾�ü����
		$data[sup_bizitem]		= $args[SupItem];			# ���޾�ü����
		$data[sup_empsector]	= $args[SupSector];			# ���޾�ü���μ�
		$data[sup_employee]		= $args[SupEmployee];		# ���޾�ü����ڸ�
		$data[sup_empemail]		= $args[SupEmail];			# ���޾�ü������̸���
		$data[sup_empmobile]	= $args[SupPhone];			# ���޾�ü������ڵ�����ȣ

		### �����ü����
		$regnum = str_replace("-", "", trim($args[BuyNo]));
		$data[buy_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# �����ü����ڹ�ȣ(XXX-XX-XXXXX)
		$data[buy_company]		= $args[BuyComp];			# �����üȸ���
		$data[buy_employer]		= $args[BuyEmployer];		# �����ü��ǥ�ڸ�
		$data[buy_address]		= $args[BuyAddr];			# �����ü�ּ�
		$data[buy_bizcond]		= $args[BuyCond];			# �����ü����
		$data[buy_bizitem]		= $args[BuyItem];			# �����ü����
		$data[buy_empsector]	= $args[BuySector];			# �����ü���μ�
		$data[buy_employee]		= $args[BuyEmployee];		# �����ü����ڸ�
		$data[buy_empemail]		= $args[BuyEmail];			# �����ü������̸���
		$data[buy_empmobile]	= $args[BuyPhone];			# �����ü������ڵ�����ȣ

		### �ݾ�
		$data[tax_supprice]		= str_replace(",", "", $args[TotalMoa]);		# ���ް����Ѿ�(���ޱݾ�)
		$data[tax_taxprice]		= str_replace(",", "", $args[TotalTax]);		# �����Ѿ�
		$data[pay_totalprice]	= str_replace(",", "", $args[MoaTax]);			# �հ�ݾ�
		$data[gen_tm]			= sprintf("%04d%02d%02d000000", $args[TaxYear], $args[TaxMonth], $args[TaxDay]);	# �ۼ�����

		### ����
		for ($i = 1; $i < 5; $i++){
			$month		= str_replace(",", "", $args["LinMonth{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinMonth{$i}"]);
			$day		= str_replace(",", "", $args["LinDay{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinDay{$i}"]);
			$itemnm		= $args["LinItem{$i}"];
			$unit		= $args["LinUnit{$i}"];
			$qty		= str_replace(",", "", $args["LinQty{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinQty{$i}"]);
			$pri		= str_replace(",", "", $args["LinPri{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinPri{$i}"]);
			$moa		= str_replace(",", "", $args["LinMoa{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinMoa{$i}"]);
			$tax		= str_replace(",", "", $args["LinTax{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinTax{$i}"]);
			$remark		= $args["LinRemark{$i}"];

			if (strlen($month . $day . $itemnm . $unit . $qty . $pri . $moa . $tax . $remark) == 0) continue;
			if (strlen($month) == 0) return array('600', "false - ������ ���� �ʼ��Դϴ�");
			else if (($month*1) != ($args[TaxMonth]*1)) return array('600', "false - �ۼ����� ������ ���� ���ƾ��մϴ�");
			else if (strlen($day) == 0) return array('600', "false - ������ ���� �ʼ��Դϴ�");
			else if (strlen($itemnm) == 0) return array('600', "false - ������ ǰ����� �ʼ��Դϴ�");
			else if (strlen($moa) == 0) return array('600', "false - ������ ���ް����� �ʼ��Դϴ�");

			$data[tax_gendates][]	= sprintf("%04d%02d%02d", $args[TaxYear], $month, $day);		# ǰ���������
			$data[item_names][]		= $itemnm;		# ǰ���
			$data[item_units][]		= $unit;		# ǰ��԰�
			$data[item_nums][]		= $qty;			# ǰ�����
			$data[item_dangas][]	= $pri;			# ǰ��ܰ�
			$data[tax_supprices][]	= $moa;			# ���ް���
			$data[tax_taxprices][]	= $tax;			# ����
			$data[item_bigos][]		= $remark;		# ǰ����
		}
		if (count($data[tax_gendates]) == 0) return array('600', "false - ǰ�������� �ϳ��̻� ������� �����ϼž� �մϴ�.");

		$out = readpost("http://godotax.godo.co.kr/sock_putTaxbill.php", $data);

		if ($out == 'true'){
			$this->godo[tax]--;
			$this->updateTaxPoint();
		}

		return array('400', $out);
	}

	function getTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^������ �����ϼ���.");

		$data=array();
		$this->hashdata($data);
		return array('400', readurl("http://godotax.godo.co.kr/sock_getTaxbill.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}&doc_number={$args[doc_number]}"));
	}

	function getTaxsugiList($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^������ �����ϼ���.");

		$data=array();
		$this->hashdata($data);
		$data = array_merge($data, $args);
		return array('400', readpost("http://godotax.godo.co.kr/sock_getTaxsugiList.php", $data));
	}

	function ccrTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^������ �����ϼ���.");

		$data=array();
		$this->hashdata($data);
		$out = readurl("http://godotax.godo.co.kr/sock_ccrTaxbill.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}&doc_number={$args[doc_number]}");
		if (preg_match('/DEL:/', $out)){
			$this->godo[tax]++;
			$this->updateTaxPoint();
		}
		return array('400', $out);
	}

	function updateTaxPoint()
	{
		$ret = encode($this->godo[tax],1);
		if(is_file($this->tPath)) unlink($this->tPath);
		$fp = fopen($this->tPath,"w");
		fwrite($fp,"<?/* \n");
		fwrite($fp,$ret."\n");
		fwrite($fp,"*/?>");
		fclose($fp);
	}

}

?>