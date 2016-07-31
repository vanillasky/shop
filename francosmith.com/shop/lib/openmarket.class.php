<?

class openmarket
{
	### ��������
	function getGodo()
	{
		ob_start();
		$file = dirname(__FILE__) . "/../conf/godomall.cfg.php";
		if (!is_file($file)) return false;
		$file = file($file);
		$this->godo = decode($file[1],1);
		ob_end_clean();
	}

	### hashdata ���� (������ ���Ἲ�� �����ϴ� �����ͷ� ��û�� �ʼ� �׸�)
	function hashdata(&$data)
	{
		$data[godosno]	= $this->godo[sno];					# ������ȣ
		$data[hashdata]	= md5($data[godosno]);				# hashdata ����
	}

	### ��������
	function isExists()
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^���� �����ϼ���.");

		$data=array();
		$this->hashdata($data);
		$out = readurl("http://godosiom.godo.co.kr/sock_isExists.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}");

		### ����������� �޽�������
		if ($out == 'false-need:join') $this->isExistsMsg = "�Ŀ����¸����� ��û�ϼž� ����� �����մϴ�.";
		else if ($out == 'false-need:extension') $this->isExistsMsg = "�Ŀ����¸����� �����ϼž� ����� �����մϴ�.";

		return array('400', $out);
	}

	### �ʼ������� ����
	function verifyData($data)
	{
		$needs = array();
		if ($data['category'] == '') $needs[] = '���¸��� ǥ�غз��� �Է����ּ���.';
		if ($data['goodsnm'] == '') $needs[] = '��ǰ���� �Է����ּ���.';
		if ($data['goodscd'] == '') $needs[] = '�𵨸��� �Է����ּ���.';
		if ($data['maker'] == '') $needs[] = '��������� �Է����ּ���.';
		if ($data['origin_kind'] == '') $needs[] = '�������� �Է����ּ���.';
		if ($data['brandnm'] == '') $needs[] = '�귣�带 �Է����ּ���.';
		if ($data['price'] == '') $needs[] = '�ǸŰ��� �Է����ּ���.';
		if ($data['consumer'] == '') $needs[] = '������ �Է����ּ���.';
		if ($data['tax'] == '') $needs[] = '����/������� �Է����ּ���.';

		### ��ǰ����
		if ($data['longdesc'] == '') $needs[] = '��ǰ�������� �Է����ּ���.';
		if ($this->imgStatus($data['longdesc'])) $needs[] = '��ǰ�������� �̹���ȣ�������� ��ȯ�� �ʿ��� �̹����� �ֽ��ϴ�.';

		### ��ǰ�̹���
		if ($data['img_m'] == '') $needs[] = '��ǰ�̹���(���̹���)�� �Է����ּ���.';
		$imgs = explode("|",$data['img_m']);
		if (!preg_match('/\.(jpg|png)$/i', $imgs[0])) $needs[] = '��ǰ�̹���(���̹���) �߿��� ù��° �̹����� JPG�� PNG ���ϸ� ����� �� �ֽ��ϴ�.('. $imgs[0] . ')';

		### ��ۤ�A/S
		if ($data['noSameShipAS'] != 'o')
		{
			@include dirname(__FILE__) . "/../conf/openmarket.php";
			if (isset($omCfg) === true) $data = array_merge($data, $omCfg);
		}
		if ($data['ship_type'] == '3'); // ������
		else if ($data['ship_type'] == '' || $data['ship_price'] == '') $needs[] = '��ۺ� �������ּ���.';
		else if ($data['ship_type'] == '5' && $data['ship_base'] == '' ) $needs[] = '�������Ǻ� ���ῡ�� ���ݱ����� �������ּ���.';
		else if ($data['ship_type'] == '4' && $data['ship_base'] == '' ) $needs[] = '�������Ǻ� ���ῡ�� ���������� �������ּ���.';
		if ($data['ship_pay'] == '') $needs[] = '��ۺ� ���������θ� �������ּ���.';

		return $needs;
	}

	### Array imgStatus(): �̹������ ��Ȳ
	function imgStatus($source)
	{
		$inCnt = 0;
		if (is_string($source) === true) $split = $this->_split($source);
		else $split = $source;
		for ($i=1,$s=count($split); $i < $s; $i += 2)
		{
			if (preg_match('@^http:\/\/@ix', $split[$i]));
			else $inCnt++;
		}
		return $inCnt;
	}

	### Array _split(): �̹������ �������� ����
	function _split($source)
	{
		$cnt = array();
		$Ext = 'gif|jpg|jpeg|png';
		$Ext = '(?<=src\=")(?:[^"])*[^"]+\.(?:'. $Ext .')(?=")'.
			"|(?<=src\=')(?:[^'])*[^']+\.(?:". $Ext .")(?=')".
			'|(?<=src\=\\\\")(?:[^"])*[^"]+\.(?:'. $Ext .')(?=\\\\")'.
			"|(?<=src\=\\\\')(?:[^'])*[^']+\.(?:". $Ext .")(?=\\\\')";
		$pattern = '@('. $Ext .')@ix';
		$split = preg_split($pattern, $source, -1, PREG_SPLIT_DELIM_CAPTURE);
		return $split;
	}
}



/***************************************************************************************************
*  openmarketSend Ŭ����
*    - ���¸����ǸŰ��� Temp_DB�� Insert �ϴ� Ŭ����.
***************************************************************************************************/
class openmarketSend extends openmarket
{
	### ��ǰ���
	function putGoods($goodsno, $mode)
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - ���θ� ȯ�������� �������̵� ��� �ֽ��ϴ�.^���� �����ϼ���.");

		$data = $GLOBALS['db']->fetch("select * from ".GD_OPENMARKET_GOODS." where goodsno='{$goodsno}'", 1);
		if ($data['goodsno'] == '') return array('400', "false - ������ ��ǰ��ȣ�� �������� �ʾҽ��ϴ�.");
		$data['mode'] = $mode;

		# ��ǰ����
		$data['longdesc'] = sprintf("<style type=\"text/css\"><!-- #godoContents {font:12px dotum; color:#000000;} --></style>\n<div id=\"godoContents\">\n%s\n</div>", $data['longdesc']);

		# �ʼ��ɼ�
		$tmp = $this->mixOption($data);
		if ($tmp != '') $data['option'] = $tmp;

		# �̹���
		$cnt = 0;
		$imgs = explode("|",$data['img_m']);
		foreach ( $imgs as $filenm ){
			if ( $filenm != '' && file_exists( dirname(__FILE__) . "/../data/goods/{$filenm}") )
			{
				$cnt++;
				$tmp = "http://{$_SERVER[HTTP_HOST]}" . str_replace($_SERVER['DOCUMENT_ROOT'], "", realpath(dirname(__FILE__) . "/../data/goods/{$filenm}"));
				if ($cnt == 1) $data['fix_image'] = $tmp;
				$data['image' . $cnt] = $tmp;
			}
		}

		### ��ۤ�A/S
		if ($data['noSameShipAS'] != 'o')
		{
			@include dirname(__FILE__) . "/../conf/openmarket.php";
			if (isset($omCfg) === true) $data = array_merge($data, $omCfg);
		}
		$data['ship_price'] = sprintf("%0d", $data['ship_price']);
		$data['ship_base'] = sprintf("%0d", $data['ship_base']);
		if ($data['ship_type'] == '0'){
			$data['ship_type'] = ($data['ship_pay'] == 'Y' ? '2' : '1');
		}
		else if ($data['ship_type'] == '3') $data['ship_pay']='N';

		### ����������
		unset($data['img_m'], $data['optnm'], $data['noSameShipAS'], $data['regdt'], $data['moddt']);
		$data = array_map("trim", $data); // ������ �յ� space �� ���� �ʵ��� ���� �ʿ�
		$this->hashdata($data);

		$out = readpost("http://godosiom.godo.co.kr/sock_putGoods.php", $data);
		return array('400', $out);
	}

	function mixOption(&$data)
	{
		$optnm = explode("|",$data['optnm']);
		$query = "select * from ".GD_OPENMARKET_GOODS_OPTION." where goodsno='{$data['goodsno']}'";
		$res = $GLOBALS['db']->query($query);
		while ($tmp=$GLOBALS['db']->fetch($res, 1)){
			$opt[$tmp['opt1']][$tmp['opt2']] = $tmp['stock'];
			$opt1[] = $tmp['opt1'];
			$opt2[] = $tmp['opt2'];

			### ����� ���
			$stock += $tmp['stock'];
		}

		$data['stock'] = $this->reStock($stock, $data);
		if ($opt1) $opt1 = array_unique($opt1);
		if ($opt2) $opt2 = array_unique($opt2);
		if (!$opt){
			$opt1 = array('');
			$opt2 = array('');
		}

		if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null)
		{
			### �ɼǸ��� ������(������/����)
	        $ctrim = create_function('$n', 'return str_replace(" ", "", $n);');
			$optnm = array_map($ctrim, $optnm);
			$optnm = array_map("strtolower", $optnm);

			$synSize = array('������', 'size', 'ũ��');
			$synColor = array('����', 'color', '�÷�');

			if (in_array($optnm[0], $synSize) === true){
				$optnm[0] = '������';
				$optnm[1] = '����';
			}
			else if (in_array($optnm[0], $synColor) === true){
				$optnm[0] = '����';
				$optnm[1] = '������';
			}
			else {
				$optnm[0] = '������';
				$optnm[1] = '����';
			}

			### �ֹ����û��� �� Format
			$op2=$opt2[0];
			foreach ($opt2 as $op2){
				foreach ($opt1 as $op1){
					$oStock[] = $this->reStock($opt[$op1][$op2], $data);
				}
			}
			$tmp = '';
			$opt1v = implode(',',$opt1);
			$opt2v = implode(',',$opt2);
			$tmp .= $optnm[0].'<'.($opt1v ? $opt1v : ' ').'>';
			$tmp .= $optnm[1].'<'.($opt2v ? $opt2v : ' ').'>';
			$tmp .= '����<'.implode(',',$oStock).'>';
		}
		return $tmp;
	}

	function reStock($stock, &$data)
	{
		if ($data['runout'] == 1) return 0;
		else if ($data['usestock'] != 'o') return 9999;
		else return sprintf("%0d", $stock);
	}
}

?>