<?

include "../lib/library.php";
include "../conf/config.php";
@include_once "../conf/config.pay.php";

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text',
		'contents' => array('html', 'ent_quotes'),
		'subject'=> array('html', 'ent_quotes'),
	));
}

$mobile		= @implode("-",$_POST[mobile]);
$mailling	= ($_POST[mailling]) ? "y" : "n";
$sms		= ($_POST[sms]) ? "y" : "n";
$_POST[mode] = ($_GET[mode] ? $_GET[mode] : $_POST[mode] );

if ( $_POST[mode] != 'taxprint' && $_POST[mode] != 'getTaxbill' ) echo '<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">';

switch ($_POST[mode]){

	### �� �ֹ����Ȯ�� ó��
	case "confirm":
		include "../conf/pg.escrow.php";
		$order_confirm = "client";
		$data = $db->fetch("select * from ".GD_ORDER." where ordno='$_POST[ordno]'");
		if ($data[step]!=3 || $data[step2]) msg("���Ȯ���� ������ �ֹ����°� �ƴմϴ�",-1);
		ctlStep($_POST[ordno],4);
		setStock($_POST[ordno]);
		set_prn_settleprice($_POST[ordno]);
		msg("�ֹ�Ȯ��ó���� �Ϸ�Ǿ����ϴ�");
		if ($data[escrowyn]=="y"){
			$db->fetch("update ".GD_ORDER." set escrowconfirm=2 where ordno='$_POST[ordno]'");
			switch ($cfg[settlePg]){
				case "allat":
					echo "<script>window.open('https://www.allatpay.com/servlet/AllatBiz/helpinfo/escrow_buy_confirm.jsp?menu_id=idH26')</script>";
					break;
				case "allatbasic":
					echo "<script>window.open('https://www.allatpay.com/servlet/AllatBiz/helpinfo/escrow_buy_confirm.jsp?menu_id=idH26')</script>";
				break;
				case "inicis":
					if($escrow[type] == "INI") {
						echo "<script>window.open('../order/card/inicis/ini_escrow_confirm.php?tid=$data[escrowno]&ordno=$_POST[ordno]','','width=640,height=250')</script>";
					} else {
						echo "<script>window.open('../order/card/inicis/escrow_confirm.php?tid=$data[escrowno]','','width=520,height=550')</script>";
					}
					break;
				case "inipay":
					echo "<script>window.open('../order/card/inipay/escrow_confirm.php?ordno=$_POST[ordno]','','width=630,height=250')</script>";
					break;
				case "agspay":
					echo "<script>window.open('../order/card/agspay/escrow_confirm.php?ordno=$_POST[ordno]','','width=520,height=200')</script>";
					break;
				case "dacom":
					echo "<SCRIPT language=JavaScript>document.location.replace('../order/card/dacom/escrow_buy_gate.php?ordno={$_POST[ordno]}&ret_path=" . urlencode($_SERVER[HTTP_REFERER]) . "');</SCRIPT>";
					exit;
					break;
				case "lgdacom":
					echo "<SCRIPT language=JavaScript>document.location.replace('../order/card/lgdacom/escrow_buy_gate.php?ordno={$_POST[ordno]}&ret_path=" . urlencode($_SERVER[HTTP_REFERER]) . "');</SCRIPT>";
					exit;
					break;
				case "kcp":
					echo "<script>window.open('../order/card/kcp/escrow_confirm.php?ordno=".$_POST['ordno']."','','width=620,height=440')</script>";
					break;
			}
		}
		break;

	### 1:1 ���� �Խ��� ����
	case "add_qna":

		$query = "
		insert into ".GD_MEMBER_QNA." set
			itemcd		= '$_POST[itemcd]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			m_no		= '$sess[m_no]',
			email		= '$_POST[email]',
			mobile		= '$mobile',
			mailling	= '$mailling',
			sms			= '$sms',
			ordno		= '$_POST[ordno]',
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);

		$db->query("update ".GD_MEMBER_QNA." set parent=sno where sno='" . $db->lastID() . "'");

		/* ����ġ push ��� �߰� 2012-03-03 dn */
		@include_once "../lib/pAPI.class.php";
		$pAPI = new pAPI();
		$push_arr = Array();
		$item_arr = codeitem('question');
		$push_arr['title'] = $cfg['shopName'];
		$push_arr['msg'] = '['.$item_arr[$_POST['itemcd']].']1:1���ǰ� �����Ǿ����ϴ�.';
		$push_arr['msg_type'] = 'qna';
		$pAPI->noticePush($push_arr);


		if($cfg['ssl_type'] == "free") {//���Ẹ�ȼ���
			$write_end_url = $sitelink->link("mypage/indb.php?mode=wirte_end","regular");
			echo "<script>location.href='$write_end_url';</script>";
		} else {
			echo "<script>alert('���������� ��ϵǾ����ϴ�');opener.location.reload();opener.focus();window.close()</script>";
		}
		exit;

		break;

	case "mod_qna":
		// @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ��
		list( $m_no ) = $db->fetch("select m_no from ".GD_MEMBER_QNA." where sno = '$_POST[sno]'");
		if ( isset($sess) && $sess['level'] < 80 && $sess['m_no'] != $m_no ) msg('������ �ۼ��� 1:1���Ǹ� �����Ͻ� �� �ֽ��ϴ�.',$code=-1);
		
		$query = "
		update ".GD_MEMBER_QNA." set
			itemcd		= '$_POST[itemcd]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			email		= '$_POST[email]',
			mobile		= '$mobile',
			mailling	= '$mailling',
			sms			= '$sms',
			ordno		= '$_POST[ordno]'
		where sno = '$_POST[sno]'
		";
		$db->query($query);


		if($cfg['ssl_type'] == "free") {//���Ẹ�ȼ���
			$write_end_url = $sitelink->link("mypage/indb.php?mode=modify_end","regular");
			echo "<script>location.href='$write_end_url';</script>";
		} else {
			echo "<script>alert('���������� �����Ǿ����ϴ�');opener.location.reload();opener.focus();window.close()</script>";
		}
		exit;


		break;

	case "wirte_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���������� ��ϵǾ����ϴ�');opener.location.reload();opener.focus();window.close()</script>";
		exit;

		break;
	case "modify_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���������� �����Ǿ����ϴ�');opener.location.reload();opener.focus();window.close()</script>";
		exit;

		break;

	case "del_qna":
		// @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ��
		list( $m_no ) = $db->fetch("select m_no from ".GD_MEMBER_QNA." where sno = '$_POST[sno]'");
		if ( isset($sess) && $sess['level'] < 80 && $sess['m_no'] != $m_no ) msg('������ �ۼ��� 1:1���Ǹ� �����Ͻ� �� �ֽ��ϴ�.','close');

		$query = "delete from ".GD_MEMBER_QNA." where sno = '$_POST[sno]'";
		$db->query($query);
		echo "<script>alert('���������� �����Ǿ����ϴ�');opener.location.reload();opener.focus();window.close()</script>";
		exit;

		break;

	case "reply_qna":

		$query = "
		insert into ".GD_MEMBER_QNA." set
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			parent		= '$_POST[sno]',
			m_no		= '$sess[m_no]',
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);

		if($cfg['ssl_type'] == "free") {//���Ẹ�ȼ���
			$write_end_url = $sitelink->link("mypage/indb.php?mode=wirte_end","regular");
			echo "<script>location.href='$write_end_url';</script>";
		} else {
			echo "<script>alert('���������� ��ϵǾ����ϴ�');opener.location.reload();opener.focus();window.close()</script>";
		}
		exit;

		break;

	case "taxapp":
		//�ֹ�������
		if(!is_object($order)){
			$order = Core::loader('order');
			$order->load($_POST['ordno']);
		}

		### ���� ���
		$goodsnm = '';
		$taxPrice = array();
		$price = $price = $supply = $surtax = 0;
		$taxPrice = $order->getRealTaxAmountsPaycoAdd(0, true); //��������
		$price = $taxPrice['taxall']; //�����ݾ�
		$supply = $taxPrice['tax']; //���ް�
		$surtax = $taxPrice['vat']; //�ΰ���

		if($set['tax']['tax_delivery'] == 'n') {
			list($price, $supply, $surtax) = $order->getRedifineDeliveryExclude($taxPrice);
		}

		if($price < 0 || $supply < 0){
			$price = $supply = $surtax = 0;
		}

		foreach ($order->getOrderItems() as $item) {
			if ($item->hasCancelCompleted()) continue;

			if ( empty($goodsnm) ) $goodsnm = addslashes(strip_tags($item[goodsnm]));

			$cnt++;
		}

		### ��ǰ��
		$cnt -= 1;
		$goodsnm .= ( $cnt ? " �� {$cnt}��" : "" );

		### ����� ��ȣ '-' ����
		$_POST[busino] = str_replace('-','',$_POST[busino]);

		$query = "
		insert into ".GD_TAX." set
			ordno		= '$_POST[ordno]',
			m_no		= '$order[m_no]',
			name		= '$_POST[name]',
			company		= '$_POST[company]',
			service		= '$_POST[service]',
			item		= '$_POST[item]',
			busino		= '$_POST[busino]',
			address		= '$_POST[address]',
			goodsnm		= '$goodsnm',
			price		= '$price',
			supply		= '$supply',
			surtax		= '$surtax',
			issuedate	= curdate(),
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		msg($msg="���ݰ�꼭 ��û�� �Ϸ�Ǿ����ϴ�." . "\\n" . "������ ����ó�� �� �μ��Ͻ� �� �ֽ��ϴ�.",$code=$_SERVER[HTTP_REFERER]);
		exit;

		break;

	case "taxprint":

		$query = "
		update ".GD_TAX." set
			step		= '2',
			printdt		= now()
		where
			sno		= '$_GET[sno]'
		";
		$db->query($query);
		exit;

		break;

	case "getTaxbill":

		header("Content-type: text/html; charset=euc-kr");
		include_once dirname(__FILE__)."/../lib/tax.class.php";
		$etax = new eTax();
		$out = $etax->getTaxbill($_GET);
		if (preg_match("/^false/i",$out[1])) header("Status: " . trim(preg_replace("/^false[ |]*-[ |]*/i", "", $out[1])), true, $out[0]);
		else echo trim(preg_replace("/^true[ |]*-[ |]*/i", "", $out[1]));
		echo ""; # ��������
		exit;

		break;

	case "eggcreate":

		### ���ں������� �߱�
		if ($_POST[resno][0] != '' && $_POST[resno][1] != '' && $_POST[eggAgree] == 'Y'){
			include '../lib/egg.class.usafe.php';
			$eggData = array('ordno' => $_POST[ordno], 'issue' => 'Y', 'resno1' => encode($_POST[resno][0],1), 'resno2' => encode($_POST[resno][1],2), 'agree' => $_POST[eggAgree]);
			$eggCls = new Egg( 'create', $eggData );
			if ( $eggCls->isErr == true ){
				msg($msg="������ �߱��� ���еǾ����ϴ�. ��߱� ��������." . "\\n" . "[����] : {$eggCls->errMsg}",$code=$_SERVER[HTTP_REFERER]);
				exit;
			}
		}

		break;

	case "modReceiver":

		$db->query("update ".GD_ORDER." set nameReceiver='".$_POST['nameReceiver']."',phoneReceiver='".implode('-',$_POST['phoneReceiver'])."',mobileReceiver='".implode('-',$_POST['mobileReceiver'])."',zipcode='".implode('-',$_POST['zipcode'])."',address='".trim($_POST['address']." ".$_POST['address_sub'])."',memo='".$_POST['memo']."' where ordno='".$_POST['ordno']."'");

		break;

	case "orderCancel":

		$res = $db->query("select ordno from ".GD_ORDER." where ordno='".$_POST['ordno']."' and step='0' and step2='0'");
		$arr = array(
			'name'=>'�����',
			'code'=>'9',
			'memo'=>'������ֹ����',
			'bankcode'=>'',
			'bankaccount'=>'',
			'bankuser'=>''
		);
		while($tmp = $db->fetch($res)){

			$arr[sno] = $arr[ea] = '';
			$res2 = $db->query("select sno,ea from ".GD_ORDER_ITEM." where ordno='".$_POST['ordno']."'");
			while($tmp2 = $db->fetch($res2)){
				$arr[sno][] = $tmp2[sno];
				$arr[ea][] = $tmp2[ea];
			}

			### �ֹ����
			chkCancel($_POST['ordno'],$arr);
			### �������
			setStock($_POST['ordno']);
			set_prn_settleprice($_POST['ordno']);

			$db->query("update ".GD_CASHRECEIPT." set moddt=now(),status='RFS' where ordno='{$_POST['ordno']}' and status='RDY'");
		}
		go('mypage_orderlist.php');

		break;

	### ���ݿ����� ��û
	case "add_cashreceipt":

		include '../lib/cashreceipt.class.php';
		$cashreceipt = new cashreceipt();
		$resid = $cashreceipt->putUserReceipt($_POST);

		if ($resid){
			echo "<script>alert('���������� ��û�Ǿ����ϴ�'); parent.location.reload();</script>";
		}

		break;

	// �������� 150129 �߰�
	case "recoverCoupon":

		restore_coupon($_POST[ordno]);
		break;
}

go($_SERVER[HTTP_REFERER]);

?>
