<?php /* Template_ 2.2.7 2015/08/31 17:51:33 /www/francotr3287_godo_co_kr/shop/data/skin/standard/order/card/lgdacom.htm 000008505 */ ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta http-equiv="Cache-Control" content="no-cache"/> 
<meta http-equiv="Expires" content="0"/> 
<meta http-equiv="Pragma" content="no-cache"/>
<script language = 'javascript'>
<!--
/*
 * �������� ������û�� PAYKEY�� �޾Ƽ� �������� ��û.
 */
function doPay_ActiveX(){
	ret = xpay_check(document.getElementById('LGD_PAYINFO'), '<?php echo $TPL_VAR["LGD"]["PLATFORM"]?>');

	if (ret=="00"){	 //ActiveX �ε� ����
		var LGD_RESPCODE		= dpop.getData('LGD_RESPCODE');		//����ڵ�
		var LGD_RESPMSG			= dpop.getData('LGD_RESPMSG');		//����޼���

		if( "0000" == LGD_RESPCODE ) { //��������
			var LGD_PAYKEY	  = dpop.getData('LGD_PAYKEY');			//LG������ ����KEY
			var msg = "������� : " + LGD_RESPMSG + "\n";
			msg += "LGD_PAYKEY : " + LGD_PAYKEY +"\n\n";
			document.getElementById('LGD_PAYKEY').value = LGD_PAYKEY;
			//alert(msg);
			document.getElementById('LGD_PAYINFO').submit();
		} else { //��������
			alert("������ �����Ͽ����ϴ�. " + LGD_RESPMSG);
		}
	} else {
		alert("LG U+ ���������� ���� XPayPlugin ����� ��ġ���� �ʾҽ��ϴ�.");
		xpay_showInstall(); //��ġ�ȳ� �˾������� ǥ�� �ڵ� �߰�
	}
}

function doPay_CUPS() {
	var f = document.getElementById('LGD_PAYINFO');
	f.action=f.instance.value + f.page.value;
	f.target = "Window";
	f.submit();
}

// �÷����� ��ġ�� �ùٸ��� Ȯ��
function chkPgFlag(){
	if(!hasXpayObject()){
		alert('LG������ ���ڰ����� ���� �÷����� ��ġ �� �ٽ� �õ� �Ͻʽÿ�.');
		return false;
	}
	return true;
}
//-->
</script>
<form id="LGD_PAYINFO" method="POST" action="<?php echo $GLOBALS["cfg"]["rootDir"]?>/order/card/lgdacom/card_return.php">
<input type="hidden" name="CST_PLATFORM"				value="<?php echo $TPL_VAR["LGD"]["PLATFORM"]?>">					<!-- �׽�Ʈ, ���� ���� -->
<input type="hidden" name="CST_MID"						value="<?php echo $TPL_VAR["LGD"]["CMID"]?>">						<!-- �������̵� -->
<input type="hidden" name="LGD_MID"						value="<?php echo $TPL_VAR["LGD"]["MID"]?>">						<!-- �������̵� -->
<input type="hidden" name="LGD_OID"						value="<?php echo $TPL_VAR["LGD"]["OID"]?>">						<!-- �ֹ���ȣ -->
<input type="hidden" name="LGD_PRODUCTINFO"				value="<?php echo $TPL_VAR["LGD"]["PRODUCTINFO"]?>">				<!-- ��ǰ���� -->
<input type="hidden" name="LGD_AMOUNT"					value="<?php echo $TPL_VAR["LGD"]["AMOUNT"]?>">					<!-- �����ݾ� -->
<input type="hidden" name="LGD_TAXFREEAMOUNT"			value="">								<!-- �鼼�ݾ� -->
<input type="hidden" name="LGD_BUYER"					value="<?php echo $TPL_VAR["nameOrder"]?>">					<!-- ������ -->
<input type="hidden" name="LGD_BUYERID"					value="<?php if($GLOBALS["sess"]["m_id"]){?><?php echo $GLOBALS["sess"]["m_id"]?><?php }elseif($TPL_VAR["email"]){?><?php echo $TPL_VAR["email"]?><?php }else{?>guest<?php }?>">	<!-- ������ ID -->
<input type="hidden" name="LGD_BUYERPHONE"				value="<?php echo implode('-',$TPL_VAR["mobileOrder"])?>">	<!-- ������ ��ȭ -->
<input type="hidden" name="LGD_BUYEREMAIL"				value="<?php echo $TPL_VAR["email"]?>">						<!-- ������ �̸��� -->
<input type="hidden" name="LGD_BUYERADDRESS"			value="<?php echo $TPL_VAR["address"]?> <?php echo $TPL_VAR["address_sub"]?>">		<!-- ���ó -->
<input type="hidden" name="LGD_RECEIVER"				value="<?php echo $TPL_VAR["nameReceiver"]?>">					<!-- ������ -->
<input type="hidden" name="LGD_RECEIVERPHONE"			value="<?php echo implode('-',$TPL_VAR["mobileReceiver"])?>">	<!-- ������ ��ȭ��ȣ -->

<?php if($TPL_VAR["settlekind"]=="c"){?>
<!-- �Һΰ��� ����â ��� ���� �������� hidden���� -->
<input type="hidden" name="LGD_INSTALLRANGE"			value="<?php echo $TPL_VAR["pg"]["quota"]?>">						<!-- �Һΰ��� ����-->
<!-- ������ �Һ�(������ �����δ�) ���θ� �����ϴ� hidden���� -->
<input type="hidden" name="LGD_NOINTINF"				value="<?php if($TPL_VAR["pg"]["zerofee"]=="1"){?><?php echo $TPL_VAR["pg"]["zerofee_period"]?><?php }?>">			<!-- �ſ�ī�� ������ �Һ� �����ϱ� -->
<?php }?>

<?php if($TPL_VAR["settlekind"]=="o"||$TPL_VAR["settlekind"]=="v"){?>
<!--������ü|�������Ա�(�������)-->
<input type="hidden" name="LGD_CASHRECEIPTYN"   value="<?php if($TPL_VAR["pg"]["receipt"]!="Y"){?>N<?php }else{?>Y<?php }?>"> <!-- ���ݿ����� ��뿩��(Y:���,N:�̻��) -->
<?php }?>

<?php if($TPL_VAR["settlekind"]=="v"){?>
<!-- �������(������) ���������� �Ͻô� ���  �Ҵ�/�Ա� ����� �뺸�ޱ� ���� �ݵ�� LGD_CASNOTEURL ������ LG �����޿� �����ؾ� �մϴ� . -->
<input type="hidden" name="LGD_CASNOTEURL"				value="<?php echo $TPL_VAR["LGD"]["CASNOTEURL"]?>">				<!-- ������� NOTEURL -->
<?php }?>

<input type="hidden" name="LGD_CUSTOM_SKIN"				value="<?php echo $TPL_VAR["LGD"]["CUSTOM_SKIN"]?>">				<!-- ����â SKIN -->
<input type="hidden" name="LGD_CUSTOM_PROCESSTYPE"		value="<?php echo $TPL_VAR["LGD"]["CUSTOM_PROCESSTYPE"]?>">		<!-- Ʈ����� ó����� -->
<input type="hidden" name="LGD_TIMESTAMP"				value="<?php echo $TPL_VAR["LGD"]["TIMESTAMP"]?>">				<!-- Ÿ�ӽ����� -->
<input type="hidden" name="LGD_HASHDATA"				value="<?php echo $TPL_VAR["LGD"]["HASHDATA"]?>">					<!-- MD5 �ؽ���ȣ�� -->
<input type="hidden" name="LGD_CUSTOM_USABLEPAY"		value="<?php echo $TPL_VAR["LGD"]["USABLEPAY"]?>">				<!-- �������ǰ������ɼ��� (�ſ�ī��:SC0010,������ü:SC0030,������:SC0040,�޴���:SC0060)-->
<input type="hidden" name="LGD_CUSTOM_PROCESSTIMEOUT"	value="<?php echo $TPL_VAR["LGD"]["CUSTOM_PROCESSTIMEOUT"]?>">	<!-- TWOTRŸ�Ӿƿ� �ð� -->
<input type="hidden" name="LGD_PAYKEY" id="LGD_PAYKEY">								<!-- LG������ PAYKEY(������ �ڵ�����)-->
<input type="hidden" name="LGD_VERSION"					value="PHP_XPay_1.0">					<!-- �������� (�������� ������) -->

<input type="hidden" name="LGD_ESCROW_USEYN"			value="<?php echo $_POST["escrow"]?>">					<!-- ����ũ�� ���� : ����(Y),������(N)-->
<?php if($_POST["escrow"]=="Y"){?>
<?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<input type="hidden" name="LGD_ESCROW_GOODID"			value="<?php echo $TPL_V1["goodsno"]?>">						<!-- ����ũ�λ�ǰ��ȣ -->
<input type="hidden" name="LGD_ESCROW_GOODNAME"			value="<?php echo $TPL_V1["goodsnm"]?>">						<!-- ����ũ�λ�ǰ�� -->
<input type="hidden" name="LGD_ESCROW_GOODCODE"			value="">								<!-- ����ũ�λ�ǰ�ڵ� -->
<input type="hidden" name="LGD_ESCROW_UNITPRICE"		value="<?php echo ($TPL_V1["price"]+$TPL_V1["addprice"])?>">			<!-- ����ũ�λ�ǰ���� -->
<input type="hidden" name="LGD_ESCROW_QUANTITY"			value="<?php echo $TPL_V1["ea"]?>">							<!-- ����ũ�λ�ǰ���� -->
<?php }}?>

<?php if($TPL_VAR["zonecode"]){?>
		<input type="hidden" name="LGD_ESCROW_ZIPCODE"			value="<?php echo $TPL_VAR["zonecode"]?>">						<!-- ����ũ�ι����������ȣ (�������ȣ) -->
		<input type="hidden" name="LGD_ESCROW_ADDRESS1"			value="<?php echo $TPL_VAR["road_address"]?>">					<!-- ����ũ�ι�����ּҵ����� (���θ��ּ�) -->
<?php }else{?>
		<input type="hidden" name="LGD_ESCROW_ZIPCODE"			value="<?php echo implode("-",$TPL_VAR["zipcode"])?>">		<!-- ����ũ�ι���������ȣ -->
		<input type="hidden" name="LGD_ESCROW_ADDRESS1"			value="<?php echo $TPL_VAR["address"]?>">						<!-- ����ũ�ι�����ּҵ����� -->
<?php }?>
<input type="hidden" name="LGD_ESCROW_ADDRESS2"			value="<?php echo $TPL_VAR["address_sub"]?>">					<!-- ����ũ�ι�����ּһ� -->
<input type="hidden" name="LGD_ESCROW_BUYERPHONE"		value="<?php echo implode('-',$TPL_VAR["mobileOrder"])?>">	<!-- ����ũ�α������޴�����ȣ -->
<?php }?>

<?php if($TPL_VAR["settlekind"]=="u"){?>
<!-- �߱����� ī�� ���� �ʵ� -->
<input type="hidden" name="instance" id="instance" value="<?php if(!empty($_SERVER["HTTPS"])){?>https<?php }else{?>http<?php }?>://xpay.lgdacom.net"/>
<input type="hidden" name="page" id="page" value="/xpay/Request.do"/>
<input type="hidden" name="LGD_RETURNURL" value="<?php echo $TPL_VAR["LGD"]["CUPRETURNURL"]?>"/>
<input type="hidden" name="LGD_NOTEURL"   value="<?php echo $TPL_VAR["LGD"]["CUPNOTEURL"]?>"/>
<input type="hidden" name="LGD_PAYWINDOWTYPE" value="CUPS">
<?php }?>

</form>
<script language="javascript" src="<?php if(!empty($_SERVER["HTTPS"])){?>https<?php }else{?>http<?php }?>://xpay.lgdacom.net/xpay/js/xpay_ub.js" type="text/javascript"></script>
<script language="javascript" src="<?php if(!empty($_SERVER["HTTPS"])){?>https<?php }else{?>http<?php }?>://xpay.lgdacom.net/xpay/js/xpay_install.js" type="text/javascript"></script>