<?
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";
include "../conf/config.php";
include "../conf/config.pay.php";

## ����/ȸ������ ���� ���� �ε�
@include "../conf/coupon.php";

$mobilians = Core::loader('Mobilians');

### �ֹ� ���� �ҷ�����
$query = "select * from ".GD_ORDER." where step2 in (50,54) and ordno='".$_POST['ordno']."' limit 1";
$data = $db->fetch($query);
$ordno = $data['ordno'];


### �������� ���� �ʱ�ȭ �� ����� ����
$query = "update ".GD_ORDER." set step2='50', eggyn='', eggno='', settlelog='',eggpginfo='',orddt = now() where ordno='".$_POST['ordno']."'";
$db->query($query);

### �������� ���� �ʱ�ȭ �� ����� ����
$query = "update ".GD_ORDER_ITEM." set istep=50 where ordno='".$_POST['ordno']."'";
$db->query($query);


if(!$ordno)	msg('�ֹ���ȣ�� �����ϴ�.',-1); //�ֹ���ȣ ��üũ


### ȸ������ ��������
if ($sess){
	$query = "
	select * from
		".GD_MEMBER." a
		left join ".GD_MEMBER_GRP." b on a.level=b.level
	where
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
}


### ���ں������� �߱޿�û ��������
if (in_array($data[settlekind],array("c","o","v")) && $cfg[settlePg] != 'dacom'){
	if ($_POST[eggResno][0] != '' && $_POST[eggResno][1] != '' && $_POST[eggAgree] == 'Y'){
		@session_start();
		$eggData = array('ordno' => $ordno, 'issue' => $_POST[eggIssue], 'resno1' => $_POST[eggResno][0], 'resno2' => $_POST[eggResno][1], 'agree' => $_POST[eggAgree]);
		$_SESSION['eggData']	= $eggData;
	}
}

if ($data['settlekind'] == 'h' && $data['pg'] == 'mobilians' && $mobilians->isEnabled()) {
	$settlePg = 'mobilians';
}
else
{
	$settlePg = $cfg['settlePg'];
}

if (in_array($data[settlekind],array("c","o","v","h"))){

	// ���հ��� ���� - 15.04.28 - su
	$order = new order();
	$order->load($ordno);
	$tax = $order->getTaxAmount();
	$vat = $order->getVatAmount();
	$taxfree = $order->getTaxFreeAmount();

	switch ($settlePg)
	{
		case "allat":
			echo "<script>parent.ftn_app();</script>";
			exit;
		case "allatbasic":
			echo "<script>parent.ftn_approval();</script>";
			exit;
		case "inicis":
			// �̴Ͻý� 4.1 �� ���հ��� �� �ΰ����� �鼼�� ����  **����!! �ʵ�� tax�� �ΰ�����
			echo "<script>
				if(typeof parent.document.getElementsByName('tax')[0] == 'undefined') {
					var form = parent.document.getElementsByName('ini')[0];
					var taxinput = document.createElement('input');
					taxinput.setAttribute('type', 'hidden');
					taxinput.setAttribute('name', 'tax');
					taxinput.setAttribute('value', '".$vat."');
					form.appendChild(taxinput);
				} else {
					parent.document.getElementsByName('tax')[0].value = '".$vat."';
				}
				if(typeof parent.document.getElementsByName('taxfree')[0] == 'undefined') {
					var form = parent.document.getElementsByName('ini')[0];
					var taxfreeinput = document.createElement('input');
					taxfreeinput.setAttribute('type', 'hidden');
					taxfreeinput.setAttribute('name', 'taxfree');
					taxfreeinput.setAttribute('value', '".$taxfree."');
					form.appendChild(taxfreeinput);
				} else {
					parent.document.getElementsByName('taxfree')[0].value = '".$taxfree."';
				}
				var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();
				</script>";
			exit;
		case "inipay":
			// �̴Ͻý� 5.0 �� ���հ��� �� �ΰ����� �鼼�� ����  **����!! �ʵ�� tax�� �ΰ�����
			$_SESSION['INI_TAX']		= $vat;		// �ΰ���
			$_SESSION['INI_TAXFREE']	= $taxfree;	// �鼼
			echo "<script>var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();</script>";
			exit;
		case "agspay":
			echo "<script>var fm=parent.document.frmAGS_pay; if (parent.Pay(fm)) parent.Pay(fm);</script>";
			exit;
		case "dacom":
			echo "<script>parent.openWindow();</script>";
			exit;
		case "lgdacom":
			// ���������� �� ���հ��� �� �鼼�� ����
			echo "<script>
				if(typeof parent.document.getElementsByName('LGD_TAXFREEAMOUNT')[0] == 'undefined') {
					var form = parent.document.getElementById('LGD_PAYINFO');
					var taxfreeinput = document.createElement('input');
					taxfreeinput.setAttribute('type', 'hidden');
					taxfreeinput.setAttribute('name', 'LGD_TAXFREEAMOUNT');
					taxfreeinput.setAttribute('value', '".$taxfree."');
					form.appendChild(taxfreeinput);
				} else {
					parent.document.getElementsByName('LGD_TAXFREEAMOUNT')[0].value = '".$taxfree."';
				}
				parent.doPay_ActiveX();
				</script>";
			exit;
		case "kcp":
			echo "<script>var fm=parent.document.order_info; if(parent.jsf__pay(fm))fm.submit();</script>";
			exit;
		case "easypay":
			echo "<script>var fm=parent.document.frm_pay; if(parent.f_submit(fm))fm.submit();</script>";
			exit;
		case 'mobilians':
			exit('
			<script type="text/javascript">
			var f = parent.document.frmSettle;
			f.action = "'.$cfg['rootDir'].'/order/card/mobilians/card_gate.php?mode=resettle";
			f.target = "ifrmHidden";
			f.submit();
			</script>
			');
		case "settlebank":
			echo "<script>parent.submitSettleFormPopup();</script>";
			exit;
			break;
	}
	exit;
} else if ($data[settlekind]=="d"){
	ctlStep($ordno,1,"stock");
} else if ($data[settlekind]=="a"){

	### ���ں������� �߱�
	if ($_POST[eggResno][0] != '' && $_POST[eggResno][1] != '' && $_POST[eggAgree] == 'Y'){
		include '../lib/egg.class.usafe.php';
		$eggData = array('ordno' => $ordno, 'issue' => $_POST[eggIssue], 'resno1' => $_POST[eggResno][0], 'resno2' => $_POST[eggResno][1], 'agree' => $_POST[eggAgree]);
		$eggCls = new Egg( 'create', $eggData );

		if ( $eggCls->isErr == true ){
			$db->query("update ".GD_ORDER." set step2=54 where ordno='$ordno'");
			$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

			### Ncash ���� ���� ��� API ȣ��
			@include "../lib/naverNcash.class.php";
			$naverNcash = new naverNcash();
			if($naverNcash->useyn == 'Y'){
				$naverNcash->payment_approval_cancel($ordno);
			}

			echo "<script>parent.location.replace('../order/order_fail.php?ordno=$ordno');</script>";
			exit;
		}
	}

	### ������ �ֹ� �۽�
	include '../lib/bank.class.php';
	$bk = new Bank( 'send', $ordno );

	$db->query("update ".GD_ORDER." set step2=0 where ordno='$ordno'");
	$db->query("update ".GD_ORDER_ITEM." set istep=0 where ordno='$ordno'");
}

### �ֹ�Ȯ�θ���
$modeMail = 0;
$data['ordno'] = $ordno;
if ($cfg["mailyn_$modeMail"]=="y"){
	include_once "../Template_/Template_.class.php";
	include_once "../lib/mail.class.php";
	$mail = new Mail($params);
	$headers['Name']    = $cfg[shopName];
	$headers['From']    = $cfg[adminEmail];
	$headers['To']		= $data[email];
	$tpl = new Template_;
	$tpl->template_dir	= "../conf/email";
	$tpl->compile_dir	= "../Template_/_compiles/$cfg[tplSkin]/conf/email";
	$data[str_settlekind] = $r_settlekind[$data[settlekind]];
	$tpl->assign($cfg); $tpl->assign($data);
	$tpl->assign('cart',$cart);
	if ($data[settlekind]=="a"){
		$data = $db->fetch("select * from ".GD_LIST_BANK." where sno='".$data['bankAccount']."'");
		$tpl->assign($data);
	}
	include "../conf/email/subject_$modeMail.php";
	$tpl->define('tpl',"tpl_$modeMail.php");
	$mail->send($headers, $tpl->fetch('tpl'));
}

### �ֹ�Ȯ�� SMS
sendSmsCase('order',$data[mobileOrder]);

### �Աݿ�û SMS
if($data['settlekind'] == "a"){
	$data = $db->fetch("select * from ".GD_LIST_BANK." where sno='".$data['bankAccount']."'");
	$dataSms['account']		= $data['bank']." ".$data['account']." ".$data['name'];
	$GLOBALS['dataSms']		= $dataSms;
	sendSmsCase('account',$data[mobileOrder]);
}

echo "<script>parent.location.replace('../order/order_end.php?ordno=$ordno');</script>";
$db->viewLog();

?>