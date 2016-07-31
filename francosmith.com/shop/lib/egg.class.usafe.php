<?

/**
 * Egg class
 * ���ں������� Ŭ����
 *    - PHP ������ allow_url_fopen is enabled ���Ǿ�� �մϴ�.
 *    - TEST URL : http://gateway2.usafe.co.kr/esafe/guartrn.asp
 *    - SERVICE URL : https://gateway.usafe.co.kr/esafe/guartrn.asp
 */

class Egg
{
	var $eggData, $set, $isErr, $errMsg;

	function Egg($act, $eggData)
	{
		$this->eggData = $eggData;

		### ȯ������
		ob_start();
		include dirname(__FILE__)."/../conf/egg.usafe.php";
		$this->set = $egg;
		ob_end_clean();

		### �Լ�����
		$this->$act();
		if ( $this->errMsg ) $this->isErr = true;
	}

	### ���ڿ� �ڸ��� �Լ�
	function strcut($str,$len)
	{
		$str = str_replace(" ", "", $str);
		if (strlen($str) > $len){
			$len = $len-2;
			for ($pos=$len;$pos>0 && ord($str[$pos-1])>=127;$pos--);
			if (($len-$pos)%2 == 0) $str = substr($str, 0, $len);
			else $str = substr($str, 0, $len+1);
		}
		return $str;
	}

	### ������ �߱�
	function create()
	{
		if ( $this->eggData['payInfo1'] != '' && $this->eggData['payInfo2'] != '' ){
			ob_start();
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET eggpginfo='{$this->eggData['payInfo1']}|{$this->eggData['payInfo2']}' WHERE ordno='{$this->eggData['ordno']}'");
			ob_end_clean();
		}

		$this->paymethod	= array(
				"a"	=> "MON", # ������
				"c"	=> "CAD", # �ſ�ī��
				"o"	=> "BMC", # ������ü
				"v"	=> "CAS", # �������
				);

		if ( $this->set['use'] != 'Y' ){ $this->errMsg = '��뼳�� �ʿ�'; return; }
		if ( $this->set['usafeid'] == '' ){ $this->errMsg = 'U-Safe ID �ʿ�'; return; }
		if ( $this->set['scope'] == 'P' && $this->eggData['issue'] != 'Y' ){ $this->errMsg = '�߱޵��� �ʿ�'; return; }
		if ( $this->eggData['agree'] != 'Y' ){ $this->errMsg = '������������ �ʿ�'; return; }

		### �ֹ��� üũ
		ob_start();
		$data = $GLOBALS[db]->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno='{$this->eggData['ordno']}'", "ASSOC");
		if ( ob_get_clean() ) return;
		if ( $data[ordno] == '' ){ $this->errMsg = '�ֹ���ȣ �ʿ�'; return; }

		/***************************************************************************************************
		*  ���ǹ߱� ���� ����
		*    - 0|���ǹ�ȣ		, ������
		*    - 1|Error Message	, ���н�
		***************************************************************************************************/
		$gubun 			= "A0";										# (*) �������� : �߱�
		$mallId			= $this->set[usafeid];						# (*) ���θ� ���̵�(U-Safe ID)
		$oId			= $this->strcut($data[ordno], 30);			# (*) �ֹ���ȣ
		$totalMoney		= $this->strcut($data[settleprice] - $data[eggFee], 9);		# (*) �����ݾ�
		$pId			= $this->strcut(decode($this->eggData['resno1'],1) . decode($this->eggData['resno2'],1), 13);	# (*) �ֹε�Ϲ�ȣ
		$paymethod		= $this->paymethod[ $data[settlekind] ];	# (*) �������

		if ( in_array($data[settlekind],array("c","o","v")) ){
			if ( $data[eggpginfo] != '' && $this->eggData['payInfo1'] == '' && $this->eggData['payInfo2'] == '' ){
				$tmp = explode("|", $data[eggpginfo]);
				$this->eggData['payInfo1'] = $tmp[0];
				$this->eggData['payInfo2'] = $tmp[1];
			}
			$payInfo1 = $this->strcut($this->eggData['payInfo1'], 5);	# (*) ��������(ī��� / ����� / �����)
			$payInfo2 = $this->strcut($this->eggData['payInfo2'], 20);	# (*) ��������(���ι�ȣ / ���ι�ȣ / ���¹�ȣ)
		}
		else if ( $data[settlekind] == "a" ){
			ob_start();
			list($payInfo1, $payInfo2) = $GLOBALS[db]->fetch("SELECT bank, account FROM ".GD_LIST_BANK." WHERE sno='{$data[bankAccount]}'");
			$payInfo1 = $this->strcut($payInfo1, 5);	# (*) ��������(�����)
			$payInfo2 = $this->strcut($payInfo2, 20);	# (*) ��������(���¹�ȣ)
			ob_end_clean();
		}
		else {
			$this->errMsg = '������ ��������';
			return;
		}

		$orderNm		= $this->strcut($data[nameOrder], 20);			# (*) �ֹ��ڸ�
		$orderHomeTel	= $this->strcut($data[phoneOrder], 20);			# (*) �ֹ�����ȭ1
		$orderHpTel		= $this->strcut($data[mobileOrder], 20);		# �ֹ�����ȭ2
		$orderZip		= $this->strcut(str_replace("-", "", $data[zipcode]), 6);	# (*) �ֹ��ڿ����ȣ
		$orderAddress	= $this->strcut(urlencode($data[address]), 80);	# (*) �ֹ����ּ�
		$orderEmail		= $this->strcut($data[email], 40);				# �ֹ���email

		$acceptor		= $this->strcut($data[nameReceiver], 20);		# �����θ�
		$deliveryTel1	= $this->strcut($data[phoneReceiver], 20);		# ��������ȭ1
		$deliveryTel2	= $this->strcut($data[mobileReceiver], 20);		# ��������ȭ2
		$sign			= "YNN";										# (*) ������������(������������/Email���ŵ���/SMS���ŵ���)

		ob_start();
		$tmp = array();
		$ires = $GLOBALS[db]->query("select goodsnm, price, ea from ".GD_ORDER_ITEM." where ordno='{$data[ordno]}'");
		while ($idata=$GLOBALS[db]->fetch($ires)){
			$tmp[goodsName][]		= urlencode($this->strcut(strip_tags($idata[goodsnm]), 100));
			$tmp[goodsPrice][]		= $this->strcut($idata[price], 9);
			$tmp[goodsQuantity][]	= $this->strcut($idata[ea], 4);
		}
		$goodsName		= implode("|", $tmp[goodsName]);				# (*) ��ǰ��
		$goodsPrice		= implode("|", $tmp[goodsPrice]);				# (*) ��ǰ�ܰ�
		$goodsQuantity	= implode("|", $tmp[goodsQuantity]);			# (*) ��ǰ����
		$goodsCount		= count($tmp[goodsName]);						# (*) ��ǰ������
		ob_end_clean();

		ob_start();
		$url = "https://gateway.usafe.co.kr/esafe/guartrn.asp?gubun=$gubun&mallId=$mallId&oId=$oId&totalMoney=$totalMoney&pId=$pId&paymethod=$paymethod&pay_Info1=$payInfo1&pay_Info2=$payInfo2&order_Nm=$orderNm&order_HomeTel=$orderHomeTel&order_HpTel=$orderHpTel&order_Zip=$orderZip&order_Address=$orderAddress&order_Email=$orderEmail&goodsCount=$goodsCount&acceptor=$acceptor&deliveryTel1=$deliveryTel1&deliveryTel2=$deliveryTel2&goodsName=$goodsName&goodsPrice=$goodsPrice&goodsQuantity=$goodsQuantity&sign=$sign";
		$out = readurl($url);
		$err = ob_get_clean();

		list($result_code, $result_msg) = explode("|", $out);
		if ( $result_code != '' && $result_msg != '' ){
			$settlelog .= "\n���ں������� �߱� (".date('Y:m:d H:i:s').")\n";
			$settlelog .= "----------------------------------------\n";
			$settlelog .= "����ڵ� : {$result_code} (0(����) �׿� ����)\n";
			$settlelog .= "����޼��� : {$result_msg}\n";
			$settlelog .= "----------------------------------------\n";
		}

		if ( $result_code == 0 && $result_msg != '' && $err == '' ){
			ob_start();
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET eggyn='y', eggno='{$result_msg}', settlelog=concat(ifnull(settlelog,''),'{$settlelog}') WHERE ordno='{$data[ordno]}'");
			ob_end_clean();
		}
		else {
			if ( $result_msg != '' ){
				$this->errMsg = $result_msg;
			}
			else if ( $err != '' ){
				$settlelog .= "\n���ں������� �߱� (".date('Y:m:d H:i:s').")\n";
				$settlelog .= "----------------------------------------\n";
				$settlelog .= "����޼��� : ������\n";
				$settlelog .= "----------------------------------------\n";
				$this->errMsg = '������';
			}
			ob_start();
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET eggyn='f', settlelog=concat(ifnull(settlelog,''),'{$settlelog}') WHERE ordno='{$data[ordno]}'");
			ob_end_clean();
		}
	}

}

?>