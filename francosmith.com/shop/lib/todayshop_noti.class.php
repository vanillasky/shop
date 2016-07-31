<?php

@include_once 'todayshop.class.php';

/**
	2011-02-08 by x-ta-c
	�����̼� �ֹ��ܰ躰 sms, email �߼� Ŭ����
 */

class todayshop_noti extends todayshop {

	var $context = array();
	var $data;
	var $shop_cfg;

    function todayshop_noti() {
		parent::todayshop();

		$this->shop_cfg = & $this->config->_loaded['config'];	// ����.

	}


	// ��Ȳ�� ���� sms, �̸��� ������ �����´�
	function set($var, $method='') {

		// ġȯ�� ������(=�ֹ�����)
		if (is_array($var))
			$this->data = $var;
		else
			$this->data = $this->_getdata($var);

		if ($method == '' || !in_array($method,array('order','cancel','sale','delivery'))) $this->_ai();

		if ($this->data === false) return;	// �����Ͱ� ������ �� ���͵� ����.

		$_smsReceiver = $this->data['mobileOrder'];

		switch($method) {
			case 'order': {
				if ($this->data['goodstype'] == 'coupon' && $this->data['processtype'] == 'i') {
					if ($this->data['mobileOrder'] != $this->data['mobileReceiver']) { // ��ù߱����� �����ϱ�
						$type['sms'] = $method.($this->data['goodstype'][0]);
					}
				}
				else {
					$type['sms'] = $method.($this->data['goodstype'][0]);
					$type['mail'] = $method.($this->data['goodstype'][0]);
				}
				break;
			}
			case 'sale': {
				if ($this->data['today_suc'] != 'y' || $this->data['step'] < 1 || $this->data['step2'] >= 40) return; // �ǸſϷᰡ �ƴϰų� �ֹ����°� ���������� ������� �Ⱥ���.
				if (!$this->data['couponNo']) { // ���� ��ȣ ������ �����ؼ� ����.
					$couponGenerator = Core::loader('couponGenerator');
					$_unique = false;

					$_db = & $GLOBALS['db'];

					do {
						$couponGenerator->make();
						$_coupon = array_pop($couponGenerator->coupon);

						list($cnt) = $_db->fetch("SELECT COUNT(cp_sno) FROM gd_todayshop_order_coupon WHERE cp_num = '$_coupon'");
						if ($cnt < 1) {
							// ������
							$query = "
							INSERT INTO ".GD_TODAYSHOP_ORDER_COUPON." SET
								ordno = '".$this->data['ordno']."',
								cp_num = '$_coupon',
								cp_ea = '".$this->data['ea']."',
								cp_publish = 'y',
								regdt = NOW()
							";

							if ($_db->query($query)) {
								$_unique = true;
							}
						}

					} while (!$_unique);

					$this->data['couponNo'] = $_coupon;
				}

				if ($this->data['mobileOrder'] != $this->data['mobileReceiver'] && $this->data['goodstype'] == 'coupon') {
					$type['sms'] = 'giftc';
					$type['mail'] = $method.($this->data['goodstype'][0]);
				}
				else {
					$type['sms'] = $type['mail'] = $method.($this->data['goodstype'][0]);
				}
				$_smsReceiver = $this->data['mobileReceiver'];
				break;
			}
			case 'delivery': {
				$type['sms'] = $type['mail'] = $method.($this->data['goodstype'][0]);
				break;
			}
			default: {
				$type['sms'] = $type['mail'] = $method;
				break;
			}
		}

		$this->context['mode'] = $type;
		//
		$formatter = Core::loader('stringFormatter');

		// sms
		if ($this->cfg['smsUse_'.$type['sms']] == 'y') {
			// ������ �ڵ��� ��ȣüũ
			if ($_phone = $formatter->get($_smsReceiver,'dial','-')) {
				$this->context['sms']['body'] = $this->cfg['smsMsg_'.$type['sms']];
				$this->context['sms']['receiver'] = $_phone;
				$this->context['sms']['callback'] = $this->shop_cfg['smsRecall'];
			}
			else {
				$this->context['sms'] = false;
			}
		}
		else {
			$this->context['sms'] = false;
		}


		// email (30,40,50)
		if ($this->cfg['mailUse_'.$type['mail']] == 'y') {
			if ($_mail = $formatter->get($this->data['email'],'email')) {
				include_once dirname(__FILE__)."/../Template_/Template_.class.php";
				$_tpl = new Template_;

				$_tpl->template_dir = dirname(__FILE__)."/../conf/email";
				$_tpl->compile_dir = dirname(__FILE__)."/../Template_/_compiles/".$this->shop_cfg[tplSkin]."/conf/email";

				$_tpl->define('tpl',$this->cfg['mailMsg_'.$type['mail']]);
				$_tpl->assign($this->data);
				$_tpl->assign("cfg",$this->shop_cfg);


				$this->context['email']['address'] = $this->data['email'];
				$this->context['email']['body'] = $_tpl->fetch('tpl');
				$this->context['email']['subject'] = $this->cfg['mailSbj_'.$type['mail']];
			}
		}
		else {
			$this->context['email'] = false;
		}
	} // eof set;



	function send() {
		if ($this->data === false) return;	// �����Ͱ� ������ �� ���͵� ����.

		// sms �߼�
		if ($this->context['sms'] !== false) {
			$sms = Core::loader('Sms');

			// ���� ���� 80����Ʈ�� ������ ������.
			$msg = parent::makeSmsMsg(  $this->_parser( $this->context['sms']['body']) );
			//$msg = array_reverse($msg);
			$smsPt = (int)preg_replace('/[^0-9]*/', '', $sms->smsPt);
			if ($smsPt >= count($msg)) {
				/*/
				for($i = 0; $i < count($msg); $i++)
					debug($msg[$i]);
				/*/
				$scnt = 0;
				for($i = 0; $i < count($msg); $i++)
					if($rtn = $sms->send($msg[$i],$this->context['sms']['receiver'],$this->context['sms']['callback'])) $scnt++;

				if ($scnt == count($msg)) $sms->update();
				/**/
			}

		}	// sms

		// email �߼�
		if ($this->context['email'] !== false) {

			$mail = Core::loader('mail','');

			$_header['To'] = $this->context['email']['address'];
			$_header['From'] = $this->shop_cfg[adminEmail];
			$_header['Name'] = $this->shop_cfg[shopName];
			$_header['Subject'] = $this->context['email']['subject'];

			/*/
			debug($_header);
			debug($this->context['email']['body']);
			/*/
			$mail->send($_header, $this->context['email']['body']);
			/**/
		}	// email

		return true;
	} // eof send;


	function getorderinfo($ordno) {
		$_db = & $GLOBALS['db'];
		$query = "
			SELECT
				A.ordno, A.mobileOrder, A.mobileReceiver, A.step, A.step2,
				C.usestock, C.totstock,
				D.tgsno, D.goodstype, D.processtype, D.startdt, D.enddt, D.fakestock, D.buyercnt, D.limit_ea,
				IF ((D.enddt IS NOT NULL AND D.enddt < now()) OR C.runout=1, 'y', 'n') AS tgout
			FROM ".GD_ORDER." AS A
			INNER JOIN ".GD_ORDER_ITEM." AS B
			ON A.ordno = B.ordno
			INNER JOIN ".GD_GOODS." AS C
			ON B.goodsno = C.goodsno
			INNER JOIN ".GD_TODAYSHOP_GOODS." AS D
			ON C.goodsno = D.goodsno
			WHERE A.ordno = '$ordno'
		";

		return $_db->fetch($query, 1);
	}


  /**
	private functions.
   */
	function _ai() {
		$this->data = false;
		return false;

	} // eof _ai;


	function _parser($str) {
		// sms, �̸��� ���ø� ġȯ �ڵ�
		$_replace = array(
			'shopName' => $this->shop_cfg['shopName'],
			'nameOrder' => $this->data['nameOrder'],
			'memo' => $this->data['memo'],
			'couponNo' => $this->data['couponNo'],
			'goodsnm' => $this->data['goodsnm'],
			'option' => $this->data['option'],
			'usedt' => ($this->data['usestartdt'] && $this->data['useenddt'] ? $this->data['usestartdt']."~".$this->data['useenddt'] : '-'),
			'ordno' => $this->data['ordno'],
			'zipcode' => $this->data['zipcode'],
			'zipcode' => $this->data['zipcode'],
			'deliverycomp' => $this->data['deliverycomp'],
			'deliverycode' => $this->data['deliverycode'],
		);

		extract($_replace);
		$str = preg_replace("/{\=?([a-zA-Z]+)}/","{\$$1}",$str);
		eval("\$str = \"$str\";");
		return $str;
	} // eof _parser;

	function _getdata($ordno) {
		$_db = & $GLOBALS['db'];
		$query = "
			SELECT
				A.*,
				C.goodsnm, C.img_s AS img, C.usestock, C.totstock,
				B.ea, B.opt1, B.opt2, B.addopt, B.price,
				D.goodstype,D.usestartdt,D.useenddt,D.processtype,D.limit_ea,D.buyercnt,D.fakestock,D.fakestock2real,D.startdt,D.enddt, IF ((D.enddt IS NOT NULL AND D.enddt < now()) OR C.runout=1, 'y', 'n') AS tgout,
				E.cp_num AS couponNo, E.cp_ea,
				DL.deliverycomp, DL.deliveryurl
			FROM ".GD_ORDER." AS A
			INNER JOIN ".GD_ORDER_ITEM." AS B
			ON A.ordno = B.ordno
			INNER JOIN ".GD_GOODS." AS C
			ON B.goodsno = C.goodsno
			INNER JOIN ".GD_TODAYSHOP_GOODS." AS D
			ON C.goodsno = D.goodsno
			LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS E
			ON A.ordno = E.ordno
			LEFT JOIN ".GD_LIST_DELIVERY." AS DL
			ON DL.deliveryno = A.deliveryno
			WHERE A.ordno = $ordno
		";
		$data = $_db->fetch($query, 1);

		if (!$data) return null;

		// �ɼ�
		if ($data['opt1']) $data['option'] = $data['opt1'];
		if ($data['opt2']) $data['option'] .= (($data['option'])? '/':'').$data['opt2'];
		if ($data['addopt']) {
			$data['option'] .= (($data['option'])? '(�߰��ɼ�-'.$data['addopt'].')':'�߰��ɼ�-'.$data['addopt']);
		}
		$data['usedt'] = ($this->data['usestartdt'] && $this->data['useenddt']) ? $this->data['usestartdt']." ~ ".$this->data['useenddt'] : '-';

		// ���ֹ��ݾ�
		$data['totalprice'] = ($data['price'] * $data['ea']) + $data['delivery'];

		$data['today_suc'] = 'y';
		if ($data['processtype'] == 'b') { // �����̼� �ϰ��߼� ��ǰ.
			$curTime = time();

			// ����Ⱓ�� ����ǰ� �ŷ������.
			$data['buyercnt_'] = $data['buyercnt'] + ($data['fakestock2real'] == 1 ? $data['fakestock'] : 0);
			if ($data['tgout'] == 'y' && ($data['limit_ea'] <= $data['buyercnt_'])) $data['today_suc'] = 'y';
			else $data['today_suc'] = 'n';
		}

		//�������
		$r_settlekind	= array(
				"a"	=> "������",
				"c"	=> "�ſ�ī��",
				"o"	=> "������ü",
				"v"	=> "�������",
				"d"	=> "��������",
				"h"	=> "�ڵ���",
				"p"	=> "����Ʈ",
				"u" => "�ſ�ī�� (�߱�)",
				"y" => "��������",
				);

		$data['str_settlekind'] = $r_settlekind[$data[settlekind]];

		return $data;
	} // eof _getdata;
  //--
}	// eof todayshop_noti;
?>
