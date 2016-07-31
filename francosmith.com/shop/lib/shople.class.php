<?php
define('_SHOPLE_SOAP_NAME_SPACE_',	'godo.shople');

class shople {

 // protected
	var $_service_url	= '';
	var $_wsdl			= true;
	var $_auth			= false;
	var $_shop_sno		= null;
	var $_stats			= null;
	var $_debug			= false;
	var $_test			= false;

 // public
	var $cfg = array();
	var $subscribe = null;
	var $openmarket='11st';	// mixed;

 // constructor
	function shople() {
		$file = SHOPROOT."/conf/godomall.cfg.php";

		if (!is_file($file)) return false;
		$file = file($file);
		$this->godo = decode($file[1],1);
		$this->_shop_sno	= $this->godo['sno'];
		$this->_stats		= $this->godo['shople'];

		$this->config		= Core::loader('config');
		$this->subscribe	= Core::loader('subscribe', $this->_shop_sno);

		$this->cfg['shop'] = $this->config->load('config');
		$this->cfg['shople'] = $this->config->load('shople');

		$this->auth();

		unset($this->godo);

	}

 // authority
	function auth() {

		if (strtoupper($this->_stats) == 'Y') {
			$this->_auth = true;
		}
		else {

			$this->_auth = false;

			$_protectlist = array(
			'modify.php',
			'config.sellerinfo.php',
			'config.category.php',
			'goods.list.php',
			'goods.11st.list.php',
			'order.11st.list.php',
			'claim.11st.list.php',
			'cs.qna.php',
			'cs.review.php',
			'calculate.php',
			'calculate.detail.php',
			);

			if (in_array( basename($_SERVER['PHP_SELF']) , $_protectlist )) {

				switch($this->_stats) {
					case 'cancel' :
						msg("���� �����Ǹ� ���񽺰� �����Ǿ� ����Ͻ� �� �����ϴ�.",-1);
						exit;
						break;
					default :
						msg("���� �����Ǹ� ���񽺸� ��û�ϼž� ����Ͻ� �� �ֽ��ϴ�.",-1);
						exit;
						break;

				}

			}
			//
		}

		unset($this->godo);
		//unset($this->config);
		//unset($this->cfg);
	}

	function _microtime() {	// (from php.net)

		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	function _sql_bind($query, $val) {

		if (is_array($val))
			foreach ($val as $k => $v) if (!is_array($v)) $query = str_replace('{:'.$k.':}','\''.mysql_real_escape_string($v).'\'',$query);

		return preg_replace('/\{\:[a-zA-Z0-9_]+\:\}/','\'\'',$query);
	}

 // ȯ�漳�� ����
	function saveConfig($cfg = null) {
		if (!$cfg) $cfg = $this->cfg['shople'];

		// cfg �� �� ������ merged �����̹Ƿ� ���ܳ�����.

		$this->config->save('shople', $cfg);
	}

 // ���¸��� API ȣ��� ÷���� ������ ���̱�
	function setData($var, $val = '') {
		if (is_array($var)) {
			foreach($var as $k=>$v) $this->$k = $v;
		}
		else {
			$this->$var = $val;
		}
	}

 // ���¸��� API ȣ��
	function _request() { }
	function request($method='',$param='',$data='',$test=false) {

		$_old_test = $this->_test;

		if ($test === true) {
			$this->_test = true;
		}

		$_start = $this->_microtime();

		if (is_array($this->openmarket)) {

			foreach ($this->openmarket as $openmarket) {

				$_class = 'openmarket_'.$openmarket;

				$this->$_class = Core::loader($_class);

				if ($method != '') $this->$_class->method = $method;
				if ($param != '') $this->$_class->param = $param;
				if ($data != '' && $this->cfg != null) {
					$this->$_class->data		= $data;
					$this->$_class->data['cfg'] = $this->cfg;
				}
							 $this->$_class->_shop_sno	= $this->_shop_sno;
							 $this->$_class->_debug	= $this->_debug;
						 	 $this->$_class->_test	= $this->_test;
				$_result[] = $this->$_class->request();

			}

			return $_result;

		}
		else {

			$_class = 'openmarket_'.$this->openmarket;

			$this->$_class = Core::loader($_class);

			if ($method != '') $this->$_class->method = $method;
			if ($param != '')  $this->$_class->param = $param;

			if ($data != '' && $this->cfg != null) {
				$this->$_class->data		= $data;
				$this->$_class->data['cfg'] = $this->cfg;
			}

						$this->$_class->_shop_sno= $this->_shop_sno;
						$this->$_class->_debug	= $this->_debug;
						$this->$_class->_test	= $this->_test;
			$_result =  $this->$_class->request();

		}

		$_result['excuted'] = $this->_microtime() - $_start;

		$this->_test = $_old_test;

		return $_result;

	}

 // ��ǰ���� ����
	function setGoods($goodsinfo) {

		global $db;

	 // ��ǰ ���� ����
		if (($_data = $db->fetch("SELECT goodsno FROM ".GD_SHOPLE_GOODS." WHERE goodsno = '".$goodsinfo['goodsno']."'")) !== false) {
			$query = "
				UPDATE ".GD_SHOPLE_GOODS." SET

					goodsnm			= {:goodsnm:},
					goodscd			= {:goodscd:},

					origin_kind		= {:origin_kind:},
					origin_name		= {:origin_name:},

					maker			= {:maker:},
					brandnm			= '',				/*brandno ? */
					tax				= {:tax:},
					shortdesc		= {:shortdesc:},
					longdesc		= {:longdesc:},
					img_m			= {:img_m:},
					category		= {:category:},
					max_count		= {:max_count:},	 /* �ִ뱸�� ��� ���� */
					optnm			= {:optnm:},
					price			= {:price:},
					consumer		= {:consumer:},	 /* ���󰡰� */
					age_flag		= {:age_flag:},
					dispno			= {:dispno:},


					runout			= {:runout:},
					usestock		= {:usestock:},

				 /* ���� ��ۺ�? */
					as_info			= {:as_info:},
					delivery_price	= {:delivery_price:},
					delivery_type	= {:delivery_type:},
					moddt			= NOW()

				WHERE goodsno = {:goodsno:}
			";

		}
		else {
			$query = "
				INSERT INTO ".GD_SHOPLE_GOODS." SET

					goodsno			= {:goodsno:},
					goodsnm			= {:goodsnm:},
					goodscd			= {:goodscd:},

					origin_kind		= {:origin_kind:},
					origin_name		= {:origin_name:},

					maker			= {:maker:},
					brandnm			= '',				/*brandno ? */
					tax				= {:tax:},
					shortdesc		= {:shortdesc:},
					longdesc		= {:longdesc:},
					img_m			= {:img_m:},
					category		= {:category:},
					max_count		= {:max_count:},	 /* �ִ뱸�� ��� ���� */
					optnm			= {:optnm:},
					price			= {:price:},
					consumer		= {:consumer:},	 /* ���󰡰� */
					age_flag		= {:age_flag:},
					dispno			= {:dispno:},

					runout			= {:runout:},
					usestock		= {:usestock:},

					as_info			= {:as_info:},
					delivery_price	= {:delivery_price:},
					delivery_type	= {:delivery_type:},
					regdt			= NOW()
			";
		}

		$query = $this->_sql_bind($query, $goodsinfo);
		$db->query($query);

	 // �ɼ� ���� ���� (���� ��ǰ �ɼ� ���̺� ���ڵ尡 �������� ����)
		list($_opt_cnt) = $db->fetch("SELECT COUNT(*) as cnt FROM ".GD_SHOPLE_GOODS_OPTION." WHERE goodsno = '".$goodsinfo['goodsno']."'");
		if ($_opt_cnt == 0) {
			$db->query("DELETE FROM ".GD_SHOPLE_GOODS_OPTION." WHERE goodsno = '".$goodsinfo['goodsno']."'");
			$rs = $db->query("SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$goodsinfo['goodsno']."' and go_is_deleted <> '1' and go_is_display = '1'");
			while ($opt = $db->fetch($rs,1)) {

				$query = "
					INSERT INTO ".GD_SHOPLE_GOODS_OPTION." SET
						goodsno	= {:goodsno:},
						opt1	= {:opt1:},
						opt2	= {:opt2:},
						price	= {:price:},
						consumer= {:consumer:},
						stock	= {:stock:},
						link	= {:link:}
					";
				$query = $this->_sql_bind($query,$opt);
				$db->query($query);
			}
		}
	}

 // ��ǰ���� �ҷ�����
	function getGoods($goodsno) {

		global $db;

		$query = "
			SELECT

				G.goodsno,

				COALESCE(SG.goodsnm, G.goodsnm) AS goodsnm,
				COALESCE(SG.goodscd, G.goodscd) AS goodscd,

				COALESCE(SG.origin_kind, null) AS origin_kind,
				COALESCE(SG.origin_name, null) AS origin_name,

				COALESCE(SG.tax, G.tax) AS tax,
				COALESCE(SG.shortdesc, G.shortdesc) AS shortdesc,
				COALESCE(SG.longdesc, G.longdesc) AS longdesc,
				COALESCE(SG.img_m, G.img_m) AS img_m,
				COALESCE(SG.max_count, G.max_ea) AS max_count,

				COALESCE(SG.optnm, G.optnm) AS optnm,
				IF (SG.optnm IS NOT NULL,	'sg',	'g') AS optnm_target,

				COALESCE(SG.age_flag, 'N') AS age_flag,

				COALESCE(SG.as_info, null) AS as_info,
				COALESCE(SG.rtnexch_info, null) AS rtnexch_info,

				COALESCE(SG.delivery_type, G.delivery_type) AS delivery_type,		/* ��ۺ� ���� ( ����, ����, ���Ǻ� ���) */
				COALESCE(SG.delivery_price, G.goods_delivery) AS delivery_price,	/* ��ۺ� */

				COALESCE(SG.runout, G.runout) AS runout,
				COALESCE(SG.usestock, G.usestock) AS usestock,

				GS.11st as is11st,

				SUB.full_dispno, SUB.full_name, SUB.category

			FROM ".GD_GOODS." AS G

			LEFT JOIN ".GD_SHOPLE_GOODS." AS SG
			ON G.goodsno = SG.goodsno

			LEFT JOIN ".GD_SHOPLE_GOODS_MAP." AS GS
			ON G.goodsno = GS.goodsno

			LEFT JOIN ".GD_GOODS_LINK." AS GL
			ON G.goodsno = GL.goodsno

			LEFT JOIN (

					SELECT
						SCM.category,
						SUB2.full_dispno,
						SUB2.full_name

					FROM ".GD_SHOPLE_CATEGORY_MAP." AS SCM

					INNER JOIN (
								SELECT

									CONCAT_WS('|', SC1.dispno, SC2.dispno, SC3.dispno, SC4.dispno ) as full_dispno,
									CONCAT_WS(' > ', SC1.name, SC2.name, SC3.name, SC4.name ) as full_name

								FROM	 ".GD_SHOPLE_CATEGORY." AS SC1

								LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC2
								ON SC1.dispno = SC2.p_dispno

								LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC3
								ON SC2.dispno = SC3.p_dispno

								LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC4
								ON SC3.dispno = SC4.p_dispno

								WHERE SC1.depth = 1
					) AS SUB2
					ON SCM.11st = SUB2.full_dispno

			) AS SUB
			ON GL.category = SUB.category

			WHERE
				G.goodsno = '$goodsno'
		";

		$data = $db->fetch($query,1);

		$data['maker']		= '����';
		$data['brandnm']	= $this->cfg['shop']['shopName'];
		$data['modelNm']	= $this->cfg['shop']['shopName'];

		$data['options'] = array();
		$data['optnm'] = !empty($data['optnm']) ? $data['optnm'] : '����';	// �ɼǸ��� ������� �⺻�� (�ʼ���)

		if ($data['as_info'] === null) $data['as_info'] = $this->cfg['shople']['as_info'];
		if ($data['rtnexch_info'] === null) $data['rtnexch_info'] = $this->cfg['shople']['rtnexch_info'];

		// �ɼ��� �����ͼ� �Ʒ����� ����
		$data['stock'] = 0;
		$data['price'] = 0;
		$data['consumer'] = 0;

		$optnm = explode("|",$data['optnm']);

		// �ɼ� (������ �̱� �ɼ����θ�)
		$query = "select * from ".($data['optnm_target'] == 'sg' ? GD_SHOPLE_GOODS_OPTION : GD_GOODS_OPTION)." where goodsno='$goodsno'";
		if ($data['optnm_target'] != 'sg') {
			$query .= "  and go_is_deleted <> '1' and go_is_display = '1' ";
		}

		$res = $db->query($query);
		$idx=0; while ($tmp=$db->fetch($res,1)){

			$_option = array(
				'price'=>$tmp['price'],
				'consumer'=>$tmp['consumer'],
				'name'=>$tmp['opt1'].($tmp['opt2'] ? ' / '.$tmp['opt2'] : ''),
				'stock'=>$tmp['stock'],
			);

			$data['options'][] = array_map("htmlspecialchars",$_option);

			// ����, ���, ����
			if ($tmp['link'] == 1) {
				$data['price'] = $tmp['price'];
				$data['consumer'] = $tmp['consumer'];
			}
			$data['stock'] += $tmp['stock'];
		}

		// ��ۺ�� �������� ������
		$data['delivery_type'] = 2;

		if (empty($data['delivery_price'])) {
			include(SHOPROOT."/conf/config.pay.php");
			$data['delivery_price'] = $set['delivery']['default'];
		}

		// �󼼺��� ������ �̹��� ��� parse;
		$data['longdesc'] = $this->_parseImageSrc($data['longdesc']);
		return $data;

	}

	function _parseImageSrc($html) {

		$imgs = array();

		$i=0;

		while (preg_match('/<img[^>]+>/i', $html, $match)) {
			$imgs[$i] = $match[0];
			$html = str_replace($match[0], '$$__IMG_TAG#'.$i.'__$$', $html);
			++$i;
		}

		for ($i=0,$m=sizeof($imgs);$i<$m;$i++) {

			if (preg_match('/src="([^\>^ ]*?)"/i',$imgs[$i],$match)) {
				$_tmp = parse_url($match[1]);

				$img_url = ($_tmp['scheme'] ? $_tmp['scheme'] : 'http').'://'.($_tmp['host'] ? $_tmp['host'] : $_SERVER['HTTP_HOST']).''.$_tmp['path'];

				$img_tag = str_replace($match[1], $img_url, $imgs[$i]);

				$html = str_replace('$$__IMG_TAG#'.$i.'__$$', $img_tag, $html);
			}
			else {
				$html = str_replace('$$__IMG_TAG#'.$i.'__$$', $imgs[$i], $html);
			}
		}

		return $html;

	}

//

	### �ʼ������� ���� (from openmarket.class.php)
	function verifyData($data) {

		$needs = array();
		if ($data['category'] == '') $needs[] = '11���� ī�װ��� �������ּ���.';
		if ($data['goodsnm'] == '') $needs[] = '��ǰ���� �Է����ּ���.';
		if ($data['goodscd'] == '') $needs[] = '�𵨸��� �Է����ּ���.';
		if ($data['price'] == '') $needs[] = '�ǸŰ��� �Է����ּ���.';
		if ($data['consumer'] == '') $needs[] = '������ �Է����ּ���.';
		if ($data['tax'] == '') $needs[] = '����/������� �Է����ּ���.';

		### ��ǰ����
		if ($data['longdesc'] == '') $needs[] = '��ǰ�������� �Է����ּ���.';
		//if ($this->imgStatus($data['longdesc'])) $needs[] = '��ǰ�������� �̹���ȣ�������� ��ȯ�� �ʿ��� �̹����� �ֽ��ϴ�.';

		### ��ǰ�̹���
		if ($data['imgs'] == '') $needs[] = '��ǰ�̹���(���̹���)�� �Է����ּ���.';
		$imgs = explode("|",$data['imgs']);
		if (!preg_match('/\.(jpg|gif)$/i', $imgs[0])) $needs[] = '��ǰ�̹���(���̹���) �߿��� ù��° �̹����� JPG�� GIF ���ϸ� ����� �� �ֽ��ϴ�.('. $imgs[0] . ')';

		// ��ۺ�
		/*
		if ($data['delivery_type'] == '')											$needs[] = '��ۺ� �������ּ���.';
		elseif ($data['delivery_type'] == '2' && $data['delivery_type'] == '')		$needs[] = '��ۺ� �������ּ���.';
		elseif ($data['delivery_type'] == '3' && $data['delivery_price2'] == '')	$needs[] = '��ۺ� �������ּ���.';
		*/

		return (sizeof($needs) > 0) ? $needs : false;

	}

	### Array imgStatus(): �̹������ ��Ȳ(from openmarket.class.php)
	function imgStatus($source) {
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

	### Array _split(): �̹������ �������� ����(from openmarket.class.php)
	function _split($source) {
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


	function json_encode($array=false) {

		if (is_null($array)) return 'null';
		if ($array === false) return 'false';
		if ($array === true) return 'true';
		if (is_scalar($array)) {

			if (is_float($array)) {
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($array)));
			}

			if (is_string($array)) {

				$array = preg_replace('{(</)(script)}i', "$1'+'$2", $array);
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $array) . '"';
			}

			else return $array;
		}

		$isList = true;
		for ($i = 0, reset($array); $i < count($array); $i++, next($array)) {
			if (key($array) !== $i) {
				$isList = false;
				break;
			}
		}

		$result = array();
		if ($isList) {
			foreach ($array as $v) $result[] = $this->json_encode($v);
			return '[ ' . join(", \n", $result) . ' ]';
		}
		else
		{
			foreach ($array as $k => $v) $result[] = $this->json_encode($k).': '.$this->json_encode($v);
			return '{ ' . join(", \n", $result) . ' }';
		}

	}
//

} // eof shople;

class subscribe {

	var $_sno = null;

	function subscribe($sno) {
		$this->_sno = $sno;
	}

	function getInfo() {
		$param = array(
			'shop_sno'=>$this->_sno,
			'mode'=>'getinfo'
		);
		return $this->_curl('https://shoppingtong2.godo.co.kr/subscribe/index.php',$param);
	}

	function cancel($action='') {

		$param = array(
			'action'=>$action,
			'shop_sno'=>$this->_sno,
			'mode'=>'cancel'
		);
		return $this->_curl('https://shoppingtong2.godo.co.kr/subscribe/index.php',$param);

	}


	function request($param) {

		$param['mode'] = 'request';
		return $this->_curl('https://shoppingtong2.godo.co.kr/subscribe/index.php',$param);

	}

	function result() {
		$param = array(
			'shop_sno'=>$this->_sno,
			'mode'=>'result'
		);

		// �������� �����ؼ� ��û�� ��������.
		return $this->_curl('https://shoppingtong2.godo.co.kr/subscribe/index.php',$param);

	}

	function modify() {
		$param = array(
			'shop_sno'=>$this->_sno,
			'mode'=>'modify'
		);

		// �������� �����ؼ� ��û�� ��������.
		return $this->_curl('https://shoppingtong2.godo.co.kr/subscribe/index.php',$param);

	}



	function _curl($url, $param) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

		$rs=curl_exec($ch);

		return (! curl_errno($ch)) ? $rs : false;

	}

} // eof subscribe;


class _openmarket {

	var $_debug = false;
	var $_test = false;

	function _openmarket() { }

	function _setVar($str) {
		return base64_encode(serialize($str));
	}

	function _getVar($str) {
		return unserialize(base64_decode($str));
	}

	function encrypt($var) {
		return $var;

	}

	function decrypt($var) {
		return $var;

	}

} // eof _openmarket;

class openmarket_11st extends _openmarket {

	var $endpoint;
	var $data = array();
	var $param = array();
	var $method='';
	var $_shop_sno = null;	// ���� ��ȣ godo[sno] ���.

	// function openmarket_11st() {}

	function request() {

		if (!class_exists('nusoap_client', false)) require_once(dirname(__FILE__).'/nusoap/nusoap.php');

		// soap endpoint ���
		$this->endpoint = 'https://shoppingtong2.godo.co.kr/_service/relay.11st.php';
		$this->endpoint.'?wsdl';

		$client = new nusoap_client($this->endpoint, false, '', '', '', '');
		if ($client->getError()) return false;

		$params = array(
			// api ���� ����
			'method'=> $this->_setVar( strtoupper($this->method) ),
			'data'=> $this->_setVar($this->data),
			'param'=> $this->_setVar($this->param),

			// ���� ����
			'sno'=>$this->_setVar($this->_shop_sno),

			// �׽�Ʈ ���
			'test'=>$this->_setVar($this->_test)
		);

		// ���� ȣ��
		// function aaa
		// instance aaa.aaa
		// class aaa..aaa
		$result = $client->call(
								"apiProcess",
								$params ,
								"uri:"._SHOPLE_SOAP_NAME_SPACE_,
								"uri:"._SHOPLE_SOAP_NAME_SPACE_."#apiProcess"
								);

		// ��� ó��
		//$this->_debug = true;
		if ($this->_debug === true) {

			$result = $this->_getVar($result);

			if ($client->fault) {
				echo '<h2>Fault</h2><pre>'; print_r($result); echo '</pre>';
			} else {
				$err = $client->getError();
				if ($err) {
					echo '<h2>Error</h2><pre>' . $err . '</pre>';
				} else {
					echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
				}
			}

			echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
			echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
		}
		else {
			return $this->_getVar($result);
		}

	}


} // eof openmarket_11st;

?>
