<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/order/cash_receipt/lgdacom.htm 000004994 */ ?>
<!-- eCredit.js���� �����ϴ� ���ݿ���������Լ�(showCashReceipts) ��� -->
<script language="JavaScript" src="http://pgweb.dacom.net/WEB_SERVER/js/receipt_link.js"></script>
<script language="javascript">
function chkreceiptFrm(fobj)
{
	var certNo = fobj.ssn.value;
	if (fobj.usertype[0].checked)
	{
		if (certNo.length != 10 && certNo.length != 11)
		{
			alert("�޴�����ȣ�� ��Ȯ�� �Է��� �ֽñ� �ٶ��ϴ�.");
			fobj.ssn.focus();
			return false;
		}
		else if ((certNo.length == 11 ||certNo.length == 10) &&  certNo.substring(0,2) != "01" )
		{
			alert("�޴��� ��ȣ�� ������ �ֽ��ϴ�. �ٽ� Ȯ�� �Ͻʽÿ�. ");
			fobj.ssn.focus();
			return false;
		}
	}
	else if (fobj.usertype[1].checked)
	{
		if (certNo.length != 10)
		{
			alert("����ڹ�ȣ�� ��Ȯ�� �Է��� �ֽñ� �ٶ��ϴ�.");
			fobj.ssn.focus();
			return false;
		}
		var sum = 0;
		var getlist = new Array(10);
		var chkvalue = new Array("1","3","7","1","3","7","1","3","5");
		for (var i=0; i<10; i++) { getlist[i] = certNo.substring(i, i+1); }
		for (var i=0; i<9; i++) { sum += getlist[i]*chkvalue[i]; }
		sum = sum + parseInt((getlist[8]*5)/10);
		sidliy = sum % 10;
		sidchk = 0;
		if (sidliy != 0) { sidchk = 10 - sidliy; }
		else { sidchk = 0; }
		if (sidchk != getlist[9]) {
			alert("����ڵ�Ϲ�ȣ�� ������ �ֽ��ϴ�. �ٽ� Ȯ���Ͻʽÿ�.");
			fobj.ssn.focus();
			return false;
		}
	}

	var chr;
	for (var i=0; i < certNo.length; i++){
		chr = certNo.substr(i, 1);
		if( chr < '0' || chr > '9') {
			alert("���ڰ� �ƴ� ���ڰ� �߰��Ǿ� �ֽ��ϴ�. �ٽ� Ȯ�� �Ͻʽÿ�.");
			fobj.ssn.focus();
			return false;
		}
	}

	if(confirm("���ݿ������� �����Ͻðڽ��ϱ�?") === false) return false;

	return true;
}

function  display_cert(robj)
{
	if (robj.checked && robj.value == "1")
	{
		_ID('cert_0').style.display = "block";
		_ID('cert_1').style.display = "none";
	}
	else if (robj.checked && robj.value == "2")
	{
		_ID('cert_0').style.display = "none";
		_ID('cert_1').style.display = "block";
	}
}
</script>
<table width="100%" style="border:1px solid #DEDEDE" cellpadding="0" cellspacing="0">
<tr>
	<td width="150" valign="top" align="right" bgcolor="#F3F3F3"></td>
	<td id="orderbox">

	<table>
	<col width="100">
	<tr>
		<td>���ݿ�����</td>
		<td>
<?php if($TPL_VAR["cashreceipt"]&&$TPL_VAR["settlekind"]=="o"){?><!-- ������ü -->
		<a href="javascript:showCashReceipts('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["ordno"]?>','001','BANK','service')">���ݿ��������</a>

<?php }elseif($TPL_VAR["cashreceipt"]&&$TPL_VAR["settlekind"]=="v"){?><!-- �������Ա�(�������) -->
		<a href="javascript:showCashReceipts('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["ordno"]?>','001','CAS','service')">���ݿ��������</a>

<?php }elseif($TPL_VAR["cashreceipt"]&&$TPL_VAR["settlekind"]=="a"){?><!-- ��ü �������Ա� -->
		<a href="javascript:showCashReceipts('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["ordno"]?>','001','CR','service')">���ݿ��������</a>

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step"]== 0){?>
		�Ա��ϼž� ���ݿ������� �߱��Ͻ� �� �ֽ��ϴ�.

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step2"]){?>
		������̰ų� ��ҵ� �ֹ��� ���ݿ������� �߱��Ͻ� �� �����ϴ�.

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step"]&&!$TPL_VAR["step2"]&&$GLOBALS["set"]["receipt"]["period"]&&$TPL_VAR["orddt"]&&(strtotime($TPL_VAR["orddt"])+( 86400*$GLOBALS["set"]["receipt"]["period"]))<time()){?>
		�ֹ��Ϸκ��� <?php echo $GLOBALS["set"]["receipt"]["period"]?>���� ����Ͽ� ������ �� �����ϴ�. (<?php echo date('y-m-d H:i',(strtotime($TPL_VAR["orddt"])+( 86400*$GLOBALS["set"]["receipt"]["period"])))?>)

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step"]&&!$TPL_VAR["step2"]){?>
		<form name="receiptFrm" method="post" action="<?php echo url("order/card/lgdacom/CashReceipt.php")?>&" onSubmit="return chkreceiptFrm(this)" target="ifrmHidden">
		<input type="hidden" name="ordno" value="<?php echo $TPL_VAR["ordno"]?>">
		<input type="hidden" name="method" value="auth">

		<table>
		<tr>
			<td width="100">����뵵</td>
			<td>
			<input type="radio" name="usertype" value="1" onClick="display_cert(this)" checked>�ҵ������
			<input type="radio" name="usertype" value="2" onClick="display_cert(this)">����������
			</td>
		</tr>
		<tr>
			<td>
			<span id="cert_0" style="display:block;">�޴�����ȣ</span>
			<span id="cert_1" style="display:none;">����ڹ�ȣ</span>
			</td>
			<td><input type="text" name="ssn" value="<?php echo str_replace('-','',$TPL_VAR["mobileOrder"])?>" class="line"> <span class="small">("-" ����)</span></td>
		</tr>
		</table>
		</form>
		<input type="button" value="���ݿ������߱޿�û" name="app_btn" onClick="javascript:if (chkreceiptFrm(document.receiptFrm)) document.receiptFrm.submit();">
<?php }?>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>