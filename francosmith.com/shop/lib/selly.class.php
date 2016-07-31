<?
/**
 * @Path : ����Ʈ/lib/selly.class.php
 * @Description : ���� ���� Ŭ���� - ( db.class.php�� parsexml.class.php �ʿ� )
 * @Since : 2011.04.28 WED
 */

// ���� < �̳���
class selly {

	// �⺻ ���� ��
	var $reqPath = array();				// ��û URL / Path
	var $type = "1";					// �������� �۾��� ���� ( 1=����; 2=ī�װ�����; 3=��ǰ����; )
	var $encoding = "utf-8";			// ���ų� ���� xml�� ���ڵ� ��� ( ������ UTF-8�� ���ڵ��� ���� )
	var $mode = "INSERT";				// ��û ������ �������� �������� ���� ( INSERT=�߰�; UPDATE=���� )
	var $sXml = "";						// ������ xml �ӽ�����
	var $rXml = "";						// ���Ϲ��� xml �ӽ�����

	// �������� ���� ��
	var $shop_cd;						// ���θ� ������
	var $cust_cd;						// �����ڵ� - �ʱ� ������ �ο��޴� cust_cd �� [SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd']
	var $cust_seq;						// ����Ű - �ʱ� ������ �ο��޴� cust_seq �� [SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq']
	var $mhost = "stdev24.godo.co.kr";	// ���� ������ ���� ��û�� host
	var $domain;						// ��ǰ�̳� ī�װ� ���� ��û�� host �� [SELECT value FROM gd_env WHERE category = 'selly' AND name = 'host']

	// ��ǰ���� ���� ��
	var $origin = "001";				// �����ڵ� ( 001=���ѹα�; 002=�߱�; 003=�Ϻ�; 004=�̱�; 005=��Ż����; ...; 243=���嵵 �Ƶ��ε屺�� )
	var $delivery_type = "1";			// ���Ÿ�� ( 1=����; 2=��������������; 3=���Ҹ�����; 4=������������ )
	var $delivery_price = 0;		// ��ۺ�

	// ����� ����
	var $resCode;						// xml ���� �� ���Ϲ��� code��
	var $resMsg;						// xml ���� �� ���Ϲ��� msg��
	var $errMsg = array();				// class ��ü���� ó�� �� �Էµ� �޼���


	// main - ���� ���� �� �ʱ�ȭ
	function selly() {
		$this->encoding = "utf-8";
		$this->mode = "INSERT";
		$this->errMsg = array();
		$this->resCode = "";
		$this->resMsg = "";
		$this->reqPath = array("", "/enamooAPI/STCustomerSeq.gm", "/enamooAPI/STShopCategory.gm", "/enamooAPI/STShopGoods.gm");

		$db = &$GLOBALS['db'];
		list($this->cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");
		list($this->cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
		list($this->domain) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'domain'");
	}


	// ���� �޼��� ����
	function history() {
		echo "<br />\n";

		if(!count($this->errMsg)) echo "[function.history] ����� ���� �޼����� �����ϴ�.<br />\n";
		else for($i = 0, $imax = count($this->errMsg); $i < $imax; $i++) echo $this->errMsg[$i]."<br />\n";

		if($this->resCode || $this->resMsg) echo "(".$this->resCode.") ".$this->resMsg."<br />\n";
	}


	// xml ���� �� ��� �� ����
	function curlXML($xml="", $host="", $path="", $enc="") {
		if(!$xml) $xml = $this->sXml;
		if(!$host) $host = $this->domain;
		if(!$path) $path = $this->reqPath[$this->type];
		if(!$enc) $enc = $this->encoding;

		if(!$xml) { $this->errMsg[] = "[function.curlXML] ������ xml�� ���� �����ϴ�."; return false; }
		if(!$host) { $this->errMsg[] = "[function.curlXML] ��û�� HOST�� �������� �ʾҽ��ϴ�."; return false; }
		if(!$path) { $this->errMsg[] = "[function.curlXML] ��û�� �������� �������� �ʾҽ��ϴ�."; return false; }

		$uri = "http://$host$path";
		$params = array("xml_data" => iconv("euc-kr", $enc, $xml));
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $uri);
		curl_setopt($curl, CURLOPT_HEADER,  0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($curl);
		curl_close($curl);

		$result = iconv($enc, "euc-kr", $result);
		return $result;
	}


	// ���ڿ� ������ �˻�
	function chkChar($str) {
		if(!ctype_alnum($str)) return "<![CDATA[".$str."]]>";
		else return $str;
	}


	// ����� ����
	function makeHeader($cd="", $seq="", $enc="", $mode="") {
		if(!$cd) $cd = $this->cust_cd;
		if(!$seq) $seq = $this->cust_seq;
		if(!$enc) $enc = $this->encoding;
		if(!$mode) $mode = $this->mode;

		if(!$cd) { $this->errMsg[] = "[function.makeHeader] �����ڵ尡 �����ϴ�."; return false; }
		if(!$seq) { $this->errMsg[] = "[function.makeHeader] ����Ű�� �����ϴ�."; return false; }
		if(!$enc) { $this->errMsg[] = "[function.makeHeader] ���ڵ� ����� �������� �ʾҽ��ϴ�."; return false; }
		if(!$mode) { $this->errMsg[] = "[function.makeHeader] ��û�� �������� �������� �������� �ʾҽ��ϴ�."; return false; }

		// ���� ��û�� �ʿ��� ����κ� ����
		$addXml = "<?xml version=\"1.0\" encoding=\"$enc\"?>\n";
		$addXml .= "<data>\n";
		$addXml .= "	<header>\n";
		if($this->type == 3) $addXml .= "		<mode>".$this->chkChar($mode)."</mode>\n";
		$addXml .= "		<cust_cd>".$this->chkChar($cd)."</cust_cd>\n";
		$addXml .= "		<cust_seq>".$this->chkChar($seq)."</cust_seq>\n";
		$addXml .= "	</header>\n";

		return $addXml;
	}


	// ���� �ޱ�
	function idShop($shop_cd="") {
		if(!$shop_cd) { $this->errMsg[] = "[function.idShop] �����ڵ尡 �����ϴ�."; return false; }

		$this->type = "1";
		$this->sXml .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$this->sXml .= "	<data>\n";
		$this->sXml .= "		<login_data>\n";
		$this->sXml .= "			<shop_cd>$shop_cd</shop_cd>\n";
		$this->sXml .= "		</login_data>\n";
		$this->sXml .= "	</data>\n";

		$this->reqPath[1] = "/enamooAPI/STCustomerSeq.gm";

		$this->rXml = $this->curlXML($this->sXml, $this->mhost, $this->reqPath[1]);

		if(!$this->rXml) { $this->errMsg[] = "[function.idShop] ��� ���� �����ϴ�."; return false; }

		$xmlParser = &$GLOBALS['xmlParser']; // xml Parser Ŭ���� ( �̸� ��������� ��� ���� : ����Ʈ/lib/parsexml.class.php )
		$xmlParser->parse($this->rXml);
		$resArr = $xmlParser->parseOut();

		$db = &$GLOBALS['db']; // DB Ŭ���� ( �̸� ��������� ��� ���� )

		for($i = 0, $imax = count($resArr); $i < $imax; $i++) {
			if(is_array($resArr[$i])) {
				$tmpTag = strtolower($resArr[$i]['tag']);
				$tmpVal = $resArr[$i]['val'];

				// �����ڵ� �� ����Ű �ޱ�
				if($tmpTag == "cust_cd" || $tmpTag == "cust_seq" || $tmpTag == "domain") {
					list($tmpCnt) = $db->fetch("SELECT COUNT(*) AS cnt FROM gd_env WHERE category = 'selly' AND name = '".$tmpTag."'");
					if($tmpCnt) {
						$db->query("UPDATE gd_env SET value = '".$tmpVal."' WHERE CONVERT( gd_env.category USING utf8 ) = 'selly' AND CONVERT( gd_env.name USING utf8 ) = '".$tmpTag."'");
						$this->{$tmpTag} = $tmpVal;
					}
					else {
						$db->query("INSERT gd_env SET category = 'selly', name = '".$tmpTag."', value = '".$tmpVal."'");
						$this->{$tmpTag} = $tmpVal;
					}
				}

				// ��� �ڵ� �� ��� �޼��� ����
				if($tmpTag == "code") $this->resCode = $tmpVal;
				if($tmpTag == "msg") $this->resMsg = $tmpVal;
			}
		}

		if(!$this->cust_cd) { $this->errMsg[] = "[function.idShop] �����ڵ� ���� �����ϴ�."; return false; }
		if(!$this->cust_seq) { $this->errMsg[] = "[function.idShop] ����Ű ���� �����ϴ�."; return false; }
		if($this->resCode != "000") return false;
		else return true;
	}


	function sendCategory() {
		if(!$this->shop_cd) { $this->errMsg[] = "[function.sendCategory] ���� ������(shop_cd)�� �����ϴ�."; return false; }

		$this->type = 2;

		$this->sXml = $this->makeHeader();
		$this->sXml .= "	<category_data>\n";

		$db = &$GLOBALS['db'];
		$query = "SELECT * FROM gd_category ORDER BY LENGTH(category) ASC, category ASC";
		$rs = $db->query($query);

		for($i = 0; $row = $db->fetch($rs); $i++) {
			$this->sXml .= "		<item>\n";
			$this->sXml .= "			<seq>".($i + 1)."</seq>\n";
			$this->sXml .= "			<shop_cd>".$this->shop_cd."</shop_cd>\n";
			$this->sXml .= "			<category_cd1>".$this->chkChar(substr($row['category'], 0, 3))."</category_cd1>\n";
			if(strlen($row['category']) > 3) $this->sXml .= "			<category_cd2>".$this->chkChar(substr($row['category'], 0, 6))."</category_cd2>\n";
			if(strlen($row['category']) > 6) $this->sXml .= "			<category_cd3>".$this->chkChar(substr($row['category'], 0, 9))."</category_cd3>\n";
			if(strlen($row['category']) > 9) $this->sXml .= "			<category_cd4>".$this->chkChar(substr($row['category'], 0, 12))."</category_cd4>\n";
			$this->sXml .= "			<category_nm>".$this->chkChar($row['catnm'])."</category_nm>\n";
			$this->sXml .= "			<category_cd>".$this->chkChar($row['category'])."</category_cd>\n";
			$this->sXml .= "			<sort>".$this->chkChar($row['sort'])."</sort>\n";
			$this->sXml .= "		</item>\n";
		}

		$this->sXml .= "	</category_data>\n";
		$this->sXml .= "</data>\n";

		// XML ���� �� ����
		$this->rXml = $this->curlXML($this->sXml, $this->domain, $this->reqPath[$this->type]);

		if(!$this->rXml) { $this->errMsg[] = "[function.sendCategory] ��� ���� �����ϴ�."; return false; }

		// ���Ϲ��� XML -> Array
		$xmlParser = &$GLOBALS['xmlParser']; // xml Parser Ŭ���� ( �̸� ��������� ��� ���� : ����Ʈ/lib/parsexml.class.php )
		$xmlParser->parse($this->rXml);
		$resArr = $xmlParser->parseOut();

		// ��� ��� �� ���� �� ���� �߻� �� ó��
		for($i = 0; $i < 2; $i++) $resHeader[strtolower($resArr[$i]['tag'])] = $resArr[$i]['val'];
		if($resHeader['code'] != "000") { $this->errMsg[] = "[function.sendCategory] [".$resHeader['code']."] ".$resHeader['msg']; return false; }

		// ���� ���� ��� �˻�
		$tmpArr = array();			// �Ľ̵� �迭�� �ӽ÷� �������ؼ� ���� �迭
		$tmpStarter = false;		// ó�� ����� ��� ���� �����ϱ� ���� tmpArr ��� ���� ��
		$codeMsg = array();			// �ڵ庰 �޼��� ���� ( �� : $codePerMsg['000'] = "����" )
		$errCate = array();			// ���۽����� �ڵ庰 ī�װ� ( �� : $codePerCate['803'] = "023001" )
		$rtnMsg = "";				// ���� ������ ������ �� ����� ��

		for($i = 0, $imax = count($resArr); $i < $imax; $i++) {
			if(!$tmpStarter) if($resArr[$i]['tag'] == "SEQ") $tmpStarter = true;

			if($tmpStarter) {
				$tmpArr[$resArr[$i]['tag']] = $resArr[$i]['val'];
				if($resArr[$i]['tag'] == "MSG" && $tmpArr['CODE'] != "000") $codeMsg[$tmpArr['CODE']] = $tmpArr['MSG'];
				if($resArr[$i]['tag'] == "CODE" && $resArr[$i]['val'] != "000") list($errCate[$tmpArr['CODE']][]) = $db->fetch("SELECT catnm FROM gd_category WHERE category = '".$tmpArr['CATEGORY_CD']."'");
			}
		}

		// ���� �޼��� ����
		if(count($codeMsg)) {
			$rtnMsg .= "���� �� ���� ������ ī�װ��� �����մϴ�.\\n\\n";

			foreach($codeMsg as $k => $v) {
				$rtnMsg .= $v."\\n";

				for($i = 0, $imax = count($errCate[$k]); $i < $imax; $i++) $rtnMsg .= "  ".$errCate[$k][$i]."\\n";

				$rtnMsg .= "\\n";
			}

			$rtnMsg .= "���� ī�װ��� Ȯ�� �� �ٽ� ������ �ֽñ� �ٶ��ϴ�.";
		}
		else $rtnMsg = "ī�װ� ���� �� ���� ��û�� �Ϸ��߽��ϴ�.";

		return $rtnMsg;
	}


	// ��ǰ���� XML �����
	function makeGoods($row) {
		if(!$this->shop_cd) { $this->errMsg[] = "[function.makeGoods] ���� ������(shop_cd)�� �����ϴ�."; return false; }

		// ��ۺ� ����
		if($this->delivery_type == 1) $this->delivery_price = 0;

		$db = &$GLOBALS['db'];
		$this->sXml .= "		<item>\n";

		$this->sXml .= "			<seq>".($i + 1)."</seq>\n";
		$this->sXml .= "			<shop_cd>".$this->shop_cd."</shop_cd>\n";
		$this->sXml .= "			<goods>\n";
		$this->sXml .= "				<goods_cd_cust>".$this->chkChar($row['goodsno'])."</goods_cd_cust>\n";
		$this->sXml .= "				<goods_nm>".$this->chkChar($row['goodsnm'])."</goods_nm>\n";
		$this->sXml .= "				<category_cust>".$this->chkChar($row['category'])."</category_cust>\n";
		$this->sXml .= "				<brand_nm>".$this->chkChar($row['brandnm'])."</brand_nm>\n";
		$this->sXml .= "				<keyword>".$this->chkChar($row['keyword'])."</keyword>\n";
		$this->sXml .= "				<maker>".$this->chkChar($row['maker'])."</maker>\n";
		$this->sXml .= "				<model_nm>".$this->chkChar($row['model_name'])."</model_nm>\n";
		$this->sXml .= "				<make_date>".$this->chkChar($row['manufacture_date'])."</make_date>\n";
		$this->sXml .= "				<tax>".(($row['tax'] == "0") ? "2" : "1")."</tax>\n";
		$this->sXml .= "				<origin>".$this->chkChar($this->origin)."</origin>\n";
		$this->sXml .= "				<delivery_type>".$this->chkChar($this->delivery_type)."</delivery_type>\n";
		$this->sXml .= "				<delivery_price>".$this->chkChar($this->delivery_price)."</delivery_price>\n";
		list($row['market_price'], $row['sale_price'], $row['buy_price']) = $db->fetch("SELECT consumer, price, supply FROM gd_goods_option WHERE goodsno = '".$row['goodsno']."' AND price > 0 ORDER BY price ASC LIMIT 1");
		$this->sXml .= "				<market_price>".$this->chkChar($row['market_price'])."</market_price>\n";
		$this->sXml .= "				<sale_price>".$this->chkChar($row['sale_price'])."</sale_price>\n";
		$this->sXml .= "				<buy_price>".$this->chkChar($row['buy_price'])."</buy_price>\n";
		$this->sXml .= "				<str_price>".$this->chkChar($row['strprice'])."</str_price>\n";
		$tmpArr = explode("|", $row['optnm']); // �ɼǸ�
		$this->sXml .= "				<opt_nm1>".$this->chkChar($tmpArr[0])."</opt_nm1>\n";
		$this->sXml .= "				<opt_nm2>".$this->chkChar($tmpArr[1])."</opt_nm2>\n";
		$this->sXml .= "				<desc>".$this->chkChar(str_replace("
", "", $row['longdesc']))."</desc>\n";
		$this->sXml .= "				<desc_short>".$this->chkChar($row['shortdesc'])."</desc_short>\n";

		$tmpImg = explode("|", $row['img_l']);
		$tmpNum = 1;
		$GLOBALS['cfg']['shopUrl'] = $_SERVER['HTTP_HOST'];
		for($j = 0, $jmax = count($tmpImg); $j < $jmax; $j++) {
			if($tmpNum > 5) break;

			if(preg_match("/^http\:\/\//", $tmpImg[$j])) { // URL �����Է�
				$this->sXml .= "				<img{$tmpNum}>".$this->chkChar($tmpImg[$j])."</img{$tmpNum}>\n";
				$tmpNum++;
			}
			else { // ���� ���ε� �̹���
				$tmpImgPath = $GLOBALS['cfg']['rootDir']."/data/goods/".$tmpImg[$j];

				if(file_exists($_SERVER['DOCUMENT_ROOT'].$tmpImgPath) && is_file($_SERVER['DOCUMENT_ROOT'].$tmpImgPath)) {
					$this->sXml .= "				<img{$tmpNum}>".$this->chkChar("http://".$GLOBALS['cfg']['shopUrl'].$tmpImgPath)."</img{$tmpNum}>\n";
					$tmpNum++;
				}
			}
		}

		$this->sXml .= "			</goods>\n";

		// �ɼ�
		$query = "SELECT opt1, opt2, price, supply, stock FROM gd_goods_option WHERE goodsno = '".$row['goodsno']."' AND go_is_deleted <> '1' AND go_is_display = '1' ORDER BY opt1 ASC, opt2 ASC";
		if (!$row['use_option']) {
			$query = "SELECT '' AS opt1, '' AS opt2, price, supply, stock FROM gd_goods_option WHERE goodsno = '".$row['goodsno']."' AND go_is_deleted <> '1' AND go_is_display = '1' AND link = '1' ORDER BY opt1 ASC, opt2 ASC";
		}
		$rs_o = $db->query($query);
		if($db->count_($rs_o)) {
			$this->sXml .= "			<option>\n";

			for($j = 0; $row_o = $db->fetch($rs_o); $j++) {
				$this->sXml .= "				<opt>\n";
				$this->sXml .= "					<opt_value1>".$this->chkChar($row_o['opt1'])."</opt_value1>\n";
				$this->sXml .= "					<opt_value2>".$this->chkChar($row_o['opt2'])."</opt_value2>\n";
				$this->sXml .= "					<add_price>".$this->chkChar($row_o['price'] - $row['sale_price'])."</add_price>\n";
				$this->sXml .= "					<add_buy_price>".$this->chkChar($row_o['supply'] - $row['buy_price'])."</add_buy_price>\n";
				$this->sXml .= "					<stock>".$this->chkChar($row_o['stock'])."</stock>\n";
				$this->sXml .= "				</opt>\n";
			}

			$this->sXml .= "			</option>\n";
		}

		$this->sXml .= "		</item>\n";
	}


	// AJAX�� ��ǰ ���� - ����ڵ�( 0:����; 1:����; )||���ϸ޼���||�����(������ ��ǥ��)
	function ajaxGoods($goodsno="") {
		// ��ǰ ��ȣ ����
		if(!$goodsno) {
			$this->errMsg[] = "[function.ajaxGoods] ��ǰ��ȣ�� �Ѿ���� �ʾҽ��ϴ�.";
			echo "1||��ǰ��ȣ�� �Ѿ���� �ʾҽ��ϴ�.||";
			return false;
		}
		if(is_array($goodsno)) {
			$this->errMsg[] = "[function.ajaxGoods] ������ǰ�� �Է��� �����մϴ�.";
			echo "1||������ǰ�� �Է��� �����մϴ�.||";
			return false;
		}

		// ���� ����
		$this->type = 3;
		$this->sXml = $this->makeHeader();
		$this->sXml .= "	<goods_data>\n";

		// ���õ� ��ǰ �о����
		$db = &$GLOBALS['db'];
		$query = "SELECT G.*, ".getCategoryLinkQuery('L.category', null, 'max').", B.brandnm FROM gd_goods AS G LEFT JOIN gd_goods_link AS L ON L.goodsno = G.goodsno LEFT JOIN gd_goods_brand AS B ON B.sno = G.brandno WHERE G.goodsno = '$goodsno'";
		$rs = $db->query($query);
		$row = $db->fetch($rs);
		$this->makeGoods($row); // XML ��ǰ�κ� ����

		$this->sXml .= "	</goods_data>\n";
		$this->sXml .= "</data>\n";
		// ���� ��ħ

		// XML ���� �� ����
		$this->rXml = $this->curlXML($this->sXml, $this->domain, $this->reqPath[$this->type]);
		$this->rXml = str_replace(array("><![CDATA[", "]]><"), array(">", "<"), $this->rXml); // <![CDATA[]]> ������ �Ľ��� ������ ���� �κ��� �־ ����;

		// ���Ϲ��� XML -> Array
		$xmlParser = &$GLOBALS['xmlParser']; // xml Parser Ŭ���� ( �̸� ��������� ��� ���� : ����Ʈ/lib/parsexml.class.php )
		$xmlParser->parse($this->rXml);
		$resArr = $xmlParser->parseOut();

		if(!$this->rXml) {
			$this->errMsg[] = "[function.ajaxGoods] ��� ���� �����ϴ�.";
			echo "1||��� ���� �����ϴ�.||";
			return false;
		}

		// ��� ��� �� ���� �� ���� �߻� �� ó��
		for($i = 0; $i < 2; $i++) $resHeader[strtolower($resArr[$i]['tag'])] = $resArr[$i]['val'];

		if($resHeader['code'] != "000") {
			echo "1||������ �����߽��ϴ�.||";
			return false;
		}

		// ��ǰ �α�����
		$tmpArr = array();
		$tmpNow = date("Y-m-d H:i:s");
		for($i = 2, $imax = count($resArr); $i < $imax; $i++) {
			$tmpArr[$resArr[$i]['tag']] = $resArr[$i]['val'];

			// MSG�� ��� �� ��ǰ�� �������̹Ƿ� �� �������� �α� ó��
			if($resArr[$i]['tag'] == "MSG") {
				if(!$tmpArr['GOODS_CD_CUST']) $tmpArr['GOODS_CD_CUST'] = $goodsno;
				$sql_log = "INSERT INTO ".GD_GOODS_STLOG." SET goodsno = '".$tmpArr['GOODS_CD_CUST']."', code = '".$tmpArr['CODE']."', msg = '".$tmpArr['MSG']."', requrl = '".$this->domain."', regdt = '$tmpNow'";
				$db->query($sql_log);
				$this->errMsg[] = "[function.ajaxGoods] �α� ���� ( ���� �ƴ� ) : ".$sql_log;
			}
		}

		// �Է� ���� �α� �о���� & �޼���ó��
		$qrMsg = "SELECT L.code, L.msg, G.goodsnm FROM ".GD_GOODS_STLOG." AS L INNER JOIN gd_goods AS G ON L.goodsno = G.goodsno WHERE L.regdt = '$tmpNow' AND L.code != '000' AND L.goodsno = '$goodsno' ORDER BY L.code ASC";
		$rsMsg = $db->query($qrMsg);
		$trMsg = $db->count_($rsMsg);

		// �Է� �Ϸ� �α� �о����
		$qrSMsg = "SELECT L.code, L.msg, G.goodsnm FROM ".GD_GOODS_STLOG." AS L INNER JOIN gd_goods AS G ON L.goodsno = G.goodsno WHERE L.regdt = '$tmpNow' AND L.code = '000' AND L.goodsno = '$goodsno' ORDER BY L.code ASC";
		$rsSMsg = $db->query($qrSMsg);
		$trSMsg = $db->count_($rsSMsg);

		if($trMsg) {
			$tmpCode = "";			// �ӽ÷� ���� �ڵ带 ���� �� ����
			$tmpAlert = "";			// alert���� ��� �޼��� ����..
			$tmpLimitCount = 0;		// �� �������� �ִ� 5������ ���� �ֱ� ���� ī����
			for($i = 0; $row = $db->fetch($rsMsg); $i++) {
				if($tmpCode != $row['code']) {
					if($tmpCode) $tmpAlert .= "\\n";
					$tmpCode = $row['code'];
					$tmpAlert .= $row['msg'];
					$tmpLimitCount = 0;
				}

				$tmpLimitCount++;
			}
		}
		else {
			if($trSMsg) {
				echo "0||��û�� �Ϸ��߽��ϴ�.||$tmpNow";
				return true;
			}
			else {
				echo "1||���α׷� �󿡼� �ش� ��ǰ�Է��� �޾Ƶ����� ���߽��ϴ�.||$tmpNow";
				return true;
			}
		}

		echo "1||".$tmpAlert."||";
		return false;
	}
}



// �̳��� < ����
class sellyRec {

	var $reqData = array();			// ��û���� xml���ڿ��� ����
	var $rXml;						// ��������� �������� xml������ ����
	var $type = "3";				// �������� �۾��� ���� ( 1=����; 2=ī�װ�����; 3=��ǰ����; )

	var $errMsg = array();			// class ��ü���� ó�� �� �Էµ� �޼���
	var $requiredFields = array();	// �ʼ��Է� ��
	var $resCodeList = array();		// �����ڵ� �� �޼���


	function sellyRec() {
		$this->rXml = "";

		$this->required['g'] = array("goodsnm|��ǰ��|str|255", "tax|��������|enum|0;1", "delivery_type|�����å|enum|0;1;2;3", "longdesc|�󼼼���|str|", "img1|�̹���|str|");
		$this->required['o'] = array("price|�ǸŰ�|int|10", "stock|���|int|8");

		$this->resCodeList = array(
			"999" => "���� ������ ���̵�(cust_id) ���� ���۵��� �ʾҽ��ϴ�.",		// ��������
			"998" => "���� ������ ��й�ȣ(cust_pw) ���� ���۵��� �ʾҽ��ϴ�.",
			"997" => "���θ� ���Ⱓ�� ����ƽ��ϴ�.",
			"996" => "DB���ӿ� �����߽��ϴ�.",
			"995" => "���� ������ ���� ������ ���� �ʽ��ϴ�.",						// /��������
			"899" => "�ɼ��� �������� �ʽ��ϴ�.",									// ��ǰ����
			"898" => "�ʼ� �׸��� �����ϴ�.",
			"897" => "�ɼ��� �ʼ� �׸��� �����ϴ�.",
			"896" => "�׸� ���°� ���� �ʽ��ϴ�.",
			"895" => "�ɼ��� �׸� ���°� ���� �ʽ��ϴ�.",
			"894" => "������ �������� �ʴ� ī�װ��Դϴ�.",						// /��ǰ����
		);
	}


	// ���� �޼��� ����
	function history() {
		echo "<br />\n";

		if(!count($this->errMsg)) echo "[function.history] ����� ���� �޼����� �����ϴ�.<br />\n";
		else for($i = 0, $imax = count($this->errMsg); $i < $imax; $i++) echo $this->errMsg[$i]."<br />\n";

		if($this->resCode || $this->resMsg) echo "(".$this->resCode.") ".$this->resMsg."<br />\n";
	}


	// �����ڵ常 ���̱�
	function onlyErr($code="", $addmsg="") {
		if(!$code) { $this->errMsg[] = "[function.onlyErr] ����� �����ڵ尡 �����ϴ�.."; return false; }

		$this->rXml = "<?xml version=\"1.0\" encoding=\"euc-kr\"?>\n";
		$this->rXml .= "<data>\n";
		$this->rXml .= "	<header>\n";
		$this->rXml .= "		<code>$code</code>\n";
		$this->rXml .= "		<msg><![CDATA[".$this->resCodeList[$code].$addmsg."]]></msg>\n";
		$this->rXml .= "	</header>\n";
		$this->rXml .= "</data>\n";
		return $this->rXml;
	}


	// ��ǰ����ڵ� �߰�
	function addResMsg($seq, $code, $addmsg="") {
		$this->rXml .= "		<item>\n";
		$this->rXml .= "			<seq>".$seq."</seq>\n";
		$this->rXml .= "			<code>".$code."</code>\n";
		$this->rXml .= "			<msg><![CDATA[".$this->resCodeList[$code].$addmsg."]]></msg>\n";
		$this->rXml .= "		</item>\n";
	}


	function idCheck() {
		$db = &$GLOBALS['db']; // DB ���� Ŭ����
		$godo = &$GLOBALS['godo']; // ���� ����

		/*
		// db ���� link �� ���� ������ �� ����
		if(!$db->db_conn) {
			echo $this->onlyErr("996");
			return false;
		}*/

		if(!$this->reqData['h']['cust_id']) {
			$this->errMsg[] = "[function.idCheck] ���� ������ ���̵� ���� �����ϴ�..";
			echo $this->onlyErr("999");
			return false;
		}
		if(!$this->reqData['h']['cust_pw']) {
			$this->errMsg[] = "[function.idCheck] ���� ������ ��й�ȣ ���� �����ϴ�..";
			echo $this->onlyErr("998");
			return false;
		}

		if(betweenDate($godo['today'],$godo['edate']) < 0) {
			echo $this->onlyErr("997");
			return false;
		}

		$sql = "SELECT m_id FROM gd_member WHERE m_id = '".$this->reqData['h']['cust_id']."' AND password = password('".$this->reqData['h']['cust_pw']."')";
		$rs = $db->query($sql);
		if($db->count_($rs)) {
			return true;
		}
		else {
			echo $this->onlyErr("995");
			return false;
		}
	}


	// parsexmlstruc.class.php�� �Ľ��� �迭�� �ٽ� ����
	function convertArray($ar) {
		$this->checkType($ar); // ��ǰ���� ī�װ����� Ȯ��

		// ��ǰ��û�� ���
		switch($this->type) {
			case 3 :
				$reqGood = $ar['DATA'][0]['child']['GOODS_DATA'];

				for($i = 0, $imax = count($reqGood[0]['child']['ITEM']); $i < $imax; $i++) {
					$this->reqData['g'][$i]['stock'] = 0;

					$tmpGoods = $ar['DATA'][0]['child']['GOODS_DATA'][0]['child']['ITEM'][$i]['child']; // ��ǰ;
					$tmpOption = $tmpGoods['OPTION'][0]['child']['OPT']; // �ɼ�;
					$this->reqData['g'][$i]['seq'] = $tmpGoods['SEQ'][0]['data']; // ��û ������

					// ��ǰ �ʵ�κ� ����
					foreach($tmpGoods['GOODS'][0]['child'] as $k => $v) {
						$this->reqData['g'][$i][strtolower($k)] = $v[0]['data'];
					}

					// �ɼǺκ� - �ɼ��� ���� ��쵵 ����� ������ ���� ���� �ϳ��� �����ؾ���..
					for($j = 0, $jmax = count($tmpOption); $j < $jmax; $j++) {
						foreach($tmpOption[$j]['child'] as $k => $v) {
							$this->reqData['g'][$i]['option'][$j][strtolower($k)] = $v[0]['data'];

							if($k == "STOCK") $this->reqData['g'][$i]['stock'] += $v[0]['data']; // �� ���
						}
					}
				}
			break;
		}

		return $this->reqData;
	}


	// ���� ��û�� Ÿ���� �˻� ( 2=ī�װ�����; 3=��ǰ����; )
	function checkType($ar) {
		if(isset($ar['DATA'][0]['child']['GOODS_DATA'])) $this->type = 3;

		return $this->type;
	}


	// ��� ����
	function makeHeader() {
		$this->rXml = "<?xml version=\"1.0\" encoding=\"euc-kr\"?>\n";
		$this->rXml .= "<data>\n";
		$this->rXml .= "	<header>\n";
		$this->rXml .= "		<code>000</code>\n";
		$this->rXml .= "		<msg><![CDATA[����]]></msg>\n";
		$this->rXml .= "	</header>\n";
	}

	// ����� �Լ� ( ���� : �ֽŹ��� /ROOT_PATH/lib/lib.func.php )
	function thumbnail($src, $folder, $sizeX=100, $sizeY=100, $fix=0) {
		if ( !eregi('http://',$src) ){
			if(!is_file($src)) return;
		}else{
			$result = $this->imgage_check($src);
			if(!$result) return;
		}
		$size = getimagesize($src);

		switch ($size[2]){
			case 1:	$image = @ImageCreatefromGif($src); break;
			case 2:	$image = ImageCreatefromJpeg($src); break;
			case 3:	$image = ImageCreatefromPng($src);  break;
		}

		if ($fix){
			$gap = abs($size[0]-$size[1]);
			switch ($fix){
				case 1:		# ������ ũ�⿡ ���� ������ ����
					$reSize = ImgSizeSet($src,$sizeX,$sizeY,$size[0],$size[1]);
					$g_width = 0;
					$g_height = 0;
					$newSizeX = $reSize[0];
					$newSizeY = $reSize[1];
					break;
				case 2:		# ������ ����
					if ($size[0]>$size[1]) $g_width  = $gap / 2;
					else $g_height = $gap / 2;
					$newSizeX = $sizeX;
					$newSizeY = $sizeX;
					if ($size[0]>$size[1]) $size[0] = $size[1];
					else $size[1] = $size[0];
					break;
				case 3:		# ������ ����
					if ($size[0]>$size[1]) $g_width  = $gap;
					else $g_height = $gap;
					$newSizeX = $sizeX;
					$newSizeY = $sizeX;
					if ($size[0]>$size[1]) $size[0] = $size[1];
					else $size[1] = $size[0];
					break;
				case 4:
					$newSizeX = $sizeX;
					$newSizeY = $sizeY;
					break;
			}

			$dst = ImageCreateTruecolor($newSizeX,$newSizeY);
			Imagecopyresampled($dst,$image,0,0,$g_width,$g_height,$newSizeX,$newSizeY,$size[0],$size[1]);
		} else {
			$width = $sizeX;
			$height = $size[1] / $size[0] * $sizeX;
			$dst = ImageCreateTruecolor($width,$height);
			Imagecopyresampled($dst,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
		}
		ImageJpeg($dst,$folder,100);
		ImageDestroy($dst);
		@chmod($folder,0707); // ���ε�� ���� ���� ����
	}

	// �ܺ� ȣ���� �̹��� ��ȿ�� üũ ( ���� : �ֽŹ��� /ROOT_PATH/lib/lib.func.php )
	function imgage_check($src) {
		$url = parse_url($src);

		$fp = fsockopen($url[host],80,$errno,$errstr,10);

		if($fp){
			socket_set_timeout($fp, 3);
			if(fputs($fp,"POST ".$url[path]." HTTP/1.0\r\n"."Host: ".$url[host]."\r\n"."User-Agent: Web 0.1\r\n"."\r\n")){
				while(!feof($fp)){
					$data .= fread($fp,1024);
				}
				if(stristr($data,"Content-Type: image")){
					return true;
				}
			}
			fclose($fp);
		}
		return false;
	}

	// �̹��� ���� - 4���� �̹����� ����
	function imgSaver($imgUrl, $imgType) {
		$cfg = &$GLOBALS['cfg'];													// ���� ����
		$imgPath = $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/data/goods/";		// �̹���
		$thumbPath = $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/data/goods/t/";	// �����

		$imgInfo = getimagesize($imgUrl);
		$imgFormat = array("", ".gif", ".jpg", ".png");

		for($i = 0; $i < 100; $i++) {
			$fileName = time()."_".substr($imgType, -1, 1)."_".$i.$imgFormat[$imgInfo[2]];
			if(!file_exists($imgPath.$fileName) && !file_exists($thumbPath.$fileName)) {
				break;
			}
		}

		$this->thumbnail($imgUrl, $imgPath.$fileName, $imgInfo[0], $imgInfo[1]);
		$this->thumbnail($imgUrl, $thumbPath.$fileName, $cfg[$imgType]);

		if(file_exists($imgPath.$fileName) || file_exists($thumbPath.$fileName)) {
			@chmod($imgPath.$fileName, 0707);
			@chmod($thumbPath.$fileName, 0707);
		}

		return $fileName;
	}


	// �ʼ� �׸� �� �׸� ���� �˻�
	function requiredCheck($arData, $arRequ) {
		$requErr = ""; // �ʼ� �׸� ����
		$typeErr = ""; // �׸� ���� ����

		for($j = 0, $jmax = count($arRequ); $j < $jmax; $j++) {
			$requInfo = explode("|", $arRequ[$j]);

			if(!$arData[$requInfo[0]] && $arData[$requInfo[0]] != "0") {
				if($requErr) $requErr .= ", ";
				$requErr .= $requInfo[1];
			}
			else {
				switch($requInfo[2]) {
					case "enum" :
						$tmpEnumList = explode(";", $requInfo[3]);
						if(!in_array($arData[$requInfo[0]], $tmpEnumList)) {
							if($typeErr) $typeErr .= ", ";
							$typeErr .= $requInfo[1];
						}
					break;
					case "int" :
						if(!is_numeric($arData[$requInfo[0]])) {
							if($typeErr) $typeErr .= ", ";
							$typeErr .= $requInfo[1];
							break;
						}
					case "str" :
						if(strlen($arData[$requInfo[0]]) > $requInfo[3] && $requInfo[3]) {
							if($typeErr) $typeErr .= ", ";
							$typeErr .= $requInfo[1];
						}
					break;
				}
			}
		}

		return $rtnVal = array($requErr, $typeErr);
	}


	// ��ǰ ������ ��� �� ����
	function makeGoodResult() {
		$db = &$GLOBALS['db']; // DB ���� Ŭ����

		$this->rXml .= "	<return>\n";

		// �ʼ��׸� & ���������� & �����ͱ��� �˻�
				$tmpRequiredList = ""; // �ʼ��׸� ���� ����Ʈ
				$tmpDTypeErrList = ""; // ������ ���� �� ���� ���� ����Ʈ
		for($i = 0, $imax = count($this->reqData['g']); $i < $imax; $i++) {

			$arFieldsError = $this->requiredCheck($this->reqData['g'][$i], $this->required['g']);
			if($arFieldsError[0]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "898", "(".$arFieldsError[0].")"); // �ʼ� �׸� ����
				continue;
			}
			if($arFieldsError[1]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "896", "(".$arFieldsError[1].")"); // �׸� ���� �̻�
				continue;
			}

			if($this->reqData['g'][$i]['category']) {
				$sqlc = "SELECT category FROM gd_category WHERE category = '".$this->reqData['g'][$i]['category']."'";
				$rsc = $db->query($sqlc);
				if(!$db->count_($rsc)) {
					$this->addResMsg($this->reqData['g'][$i]['seq'], "894"); // ���� ī�װ� ����
					continue;
				}
			}

			if(count($this->reqData['g'][$i]['option']) > 1) $use_option = '1';
			else $use_option = '0';

			$sql = "INSERT INTO gd_goods SET
				goodsnm = '".addslashes($this->reqData['g'][$i]['goodsnm'])."',
				goods_price = '".$this->reqData['g'][$i]['sale_price']."',
				keyword = '".$this->reqData['g'][$i]['keyword']."',
				maker = '".$this->reqData['g'][$i]['maker']."',
				tax = '".(($this->reqData['g'][$i]['tax'] == "2") ? "0" : "1")."',
				origin = '".$this->reqData['g'][$i]['origin']."',
				delivery_type = '".$this->reqData['g'][$i]['delivery_type']."',
				goods_delivery = '".$this->reqData['g'][$i]['goods_delivery']."',
				strprice = '".$this->reqData['g'][$i]['strprice']."',
				use_option = '".$use_option."',
				optnm = '".$this->reqData['g'][$i]['optnm']."',
				longdesc = '".addslashes($this->reqData['g'][$i]['longdesc'])."',
				shortdesc = '".addslashes($this->reqData['g'][$i]['shortdesc'])."',
				extra_info = '".$this->reqData['g'][$i]['extra_info']."',
				open = '0',
				regdt = NOW()";

			for($j = 0; $j < 5; $j++) {
				if($this->reqData['g'][$i]['img'.$j]) {
					if(!$tmpImg_i && $tmpImg_i == '') $tmpImg_i = $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_i");
					if(!$tmpImg_s && $tmpImg_s == '') $tmpImg_s = $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_s");
					if($tmpImg_m) $tmpImg_m .= "|";
					$tmpImg_m .= $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_m");
					if($tmpImg_l) $tmpImg_l .= "|";
					$tmpImg_l .= $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_l");
				}
			}
			if($tmpImg_i) $sql .= ", img_i = '".$tmpImg_i."'";
			if($tmpImg_s) $sql .= ", img_s = '".$tmpImg_s."'";
			if($tmpImg_m) $sql .= ", img_m = '".$tmpImg_m."'";
			if($tmpImg_l) $sql .= ", img_l = '".$tmpImg_l."'";

			if(!count($this->reqData['g'][$i]['option'])) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "899"); // �ɼ� ����
				continue;
			}

			$totstock = 0;
			for($j = 0, $jmax = count($this->reqData['g'][$i]['option']); $j < $jmax; $j++) {
				$tmpOption = $this->reqData['g'][$i]['option'][$j];

				$arOptionFieldsError = $this->requiredCheck($this->reqData['g'][$i]['option'][$j], $this->required['o']);
				if($arOptionFieldsError[0]) {
					$this->addResMsg($this->reqData['g'][$i]['seq'], "897", "(".$arOptionFieldsError[0].")"); // �ɼ� �ʼ� �׸� ����
					break;
				}
				if($arOptionFieldsError[1]) {
					$this->addResMsg($this->reqData['g'][$i]['seq'], "895", "(".$arOptionFieldsError[1].")"); // �ɼ� �׸� ���� �̻�
					break;
				}


				$sqlo[$j] = "INSERT INTO gd_goods_option SET
					goodsno = 'ENReplaceGoodsNo',
					opt1 = '".$tmpOption['opt1']."',
					opt2 = '".$tmpOption['opt2']."',
					price = '".$tmpOption['price']."',
					consumer = '".$tmpOption['consumer']."',
					supply = '".$tmpOption['supply']."',
					reserve = '".$tmpOption['reserve']."',
					stock = '".$tmpOption['stock']."'";

				if($j == 0) $sqlo[$j] .= ", link = 1";

				$totstock = $totstock + $tmpOption['stock'];
			}
			if($arOptionFieldsError[0]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "897", "[".$arOptionFieldsError[0]."]"); // �ɼ� �ʼ� �׸� ����
				continue;
			}
			if($arOptionFieldsError[1]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "895", "[".$arOptionFieldsError[1]."]"); // �ɼ� �׸� ���� �̻�
				continue;
			}

			if($totstock) $sql .= ", totstock = '".$totstock."'"; // ��ǰ �� ���

			// ��� �˻翡 ����ϸ� ���� ����
			$rs = $db->query($sql);
			$tmpThisSeq = $db->lastID();

			// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
			foreach (getHighCategoryCode($this->reqData['g'][$i]['category']) as $val) {
				$db->query("INSERT INTO gd_goods_link SET goodsno='".$tmpThisSeq."', category='".$val."', hidden='0', sort=-UNIX_TIMESTAMP()"); // ī�װ� ���
			}

			foreach($sqlo as $k => $v) {
				$v = str_replace("ENReplaceGoodsNo", $tmpThisSeq, $v);
				$db->query($v);
			}

			$this->rXml .= "		<item>\n";
			$this->rXml .= "			<seq>".$this->reqData['g'][$i]['seq']."</seq>\n";
			$this->rXml .= "			<goodsno>".$tmpThisSeq."</goodsno>\n";
			$this->rXml .= "			<code>000</code>\n";
			$this->rXml .= "			<msg><![CDATA[����]]></msg>\n";
			$this->rXml .= "		</item>\n";

		}

		$this->rXml .= "	</return>\n";
		$this->rXml .= "</data>\n";
	}


	// xml ����
	function makeXml($ar) {
		$this->reqData['h']['cust_id'] = $ar['DATA'][0]['child']['HEADER'][0]['child']['CUST_ID'][0]['data'];
		$this->reqData['h']['cust_pw'] = $ar['DATA'][0]['child']['HEADER'][0]['child']['CUST_PW'][0]['data'];
		if(!$this->idCheck()) {
			$this->errMsg[] = "[function.convertArray] ������ �����߽��ϴ�..";
			return false;
		}
		else $this->makeHeader();

		$this->convertArray($ar);

		switch($this->type) {
			case 3 :
				$this->makeGoodResult();
			break;
		}

		echo $this->rXml;
	}
}
?>
