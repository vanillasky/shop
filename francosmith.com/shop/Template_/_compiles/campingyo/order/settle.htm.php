<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/order/settle.htm 000013673 */ 
if (is_array($_POST)) $TPL__POST_1=count($_POST); else if (is_object($_POST) && in_array("Countable", class_implements($_POST))) $TPL__POST_1=$_POST->count();else $TPL__POST_1=0;
if (is_array($GLOBALS["bank"])) $TPL__bank_1=count($GLOBALS["bank"]); else if (is_object($GLOBALS["bank"]) && in_array("Countable", class_implements($GLOBALS["bank"]))) $TPL__bank_1=$GLOBALS["bank"]->count();else $TPL__bank_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<style>
#orderbox {border:5px solid #F3F3F3; padding:5px 10px;}
#orderbox table th {width:100;}
</style>

<!-- ����̹��� || ������ġ -->
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td><img src="/shop/data/skin/campingyo/img/common/title_payment.gif" border=0></td></tr>
<tr><td class="path">home > <b>�����ϱ�</b></td></tr>
</table>


<div class="indiv"><!-- Start indiv -->

<?php echo $this->define('tpl_include_file_1',"proc/orderitem.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>


<p>

<form name=frmSettle method=post action="<?php echo $TPL_VAR["orderActionUrl"]?>" target=ifrmHidden>
<?php if($TPL__POST_1){foreach($_POST as $TPL_K1=>$TPL_V1){?>
<?php if(is_array($TPL_V1)){?>
<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<input type=hidden name="<?php echo $TPL_K1?>[]" value="<?php echo $TPL_V2?>">
<?php }}?>
<?php }else{?>
	<input type=hidden name="<?php echo $TPL_K1?>" value="<?php echo $TPL_V1?>">
<?php }?>
<?php }}?>

<img src="/shop/data/skin/campingyo/img/common/payment_txt_01.gif" border=0>
<!-- 01 �ֹ������� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_01.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>�ֹ��ڸ�</td>
		<td><?php echo $TPL_VAR["nameOrder"]?></td>
	</tr>
	<tr>
		<td>�ֹ��� ��ȭ</td>
		<td><?php echo implode("-",$TPL_VAR["phoneOrder"])?></td>
	</tr>
	<tr>
		<td>�ֹ��� �ڵ���</td>
		<td><?php echo implode("-",$TPL_VAR["mobileOrder"])?></td>
	</tr>
	<tr>
		<td>�̸���</td>
		<td><?php echo $TPL_VAR["email"]?></td>
	</tr>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 02 ������� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_02.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>�޴��ڸ�</td>
		<td><?php echo $TPL_VAR["nameReceiver"]?></td>
	</tr>
	<tr>
		<td>�޴��� ��ȭ</td>
		<td><?php echo implode("-",$TPL_VAR["phoneReceiver"])?></td>
	</tr>
	<tr>
		<td>�޴��� �ڵ���</td>
		<td><?php echo implode("-",$TPL_VAR["mobileReceiver"])?></td>
	</tr>
	<tr>
		<td>�����ȣ</td>
		<td><?php echo implode("-",$TPL_VAR["zipcode"])?></td>
	</tr>
	<tr>
		<td>�ּ�</td>
		<td>
			<?php echo $TPL_VAR["address"]?> <?php echo $TPL_VAR["address_sub"]?>

<?php if($TPL_VAR["road_address"]){?><div style="padding-top:5px;font:12px dotum;color:#999;"><?php echo $TPL_VAR["road_address"]?> <?php echo $TPL_VAR["address_sub"]?></div><?php }?>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 03 �����ݾ� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_03.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>���ֹ��ݾ�</td>
		<td><?php echo number_format($TPL_VAR["cart"]->goodsprice)?>��</td>
	</tr>
	<tr>
		<td>��ۺ�</td>
		<td>
			<?php echo $GLOBALS["msg_delivery"]?>

<?php if($GLOBALS["delivery"]["extra_price"]> 0){?>
			<div class="small red">(������ ��ۺ� <?php echo number_format($GLOBALS["delivery"]["extra_price"])?> �� ����)</div>
<?php }?>
		</td>
	</tr>

<?php if($GLOBALS["addreserve"]){?>
	<tr>
		<td>�߰�������</td>
		<td><?php echo number_format($GLOBALS["addreserve"])?>��</td>
	</tr>
<?php }?>
	<tr>
		<td>ȸ������</td>
		<td>- <?php echo number_format($TPL_VAR["cart"]->dcprice)?>��</td>
	</tr>
<?php if($TPL_VAR["cart"]->special_discount_amount){?>
	<tr>
		<td>��ǰ����</td>
		<td>- <?php echo number_format($TPL_VAR["cart"]->special_discount_amount)?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr>
		<td>��������</td>
		<td>- <?php echo number_format($TPL_VAR["coupon"])?>��<?php if($TPL_VAR["view_aboutdc"]&&$TPL_VAR["about_coupon"]){?> (��ٿ����� <?php echo number_format($TPL_VAR["about_coupon"])?>��)<?php }?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
	<tr>
		<td>��������</td>
		<td><?php echo number_format($TPL_VAR["coupon_emoney"])?>��</td>
	</tr>
<?php }?>
	<tr>
		<td>������ ���</td>
		<td>- <?php echo number_format($TPL_VAR["emoney"])?>��</td>
	</tr>
<?php if($TPL_VAR["ncash"]["ncash_emoney"]){?>
	<tr>
		<td>���̹����ϸ���</td>
		<td>- <?php echo number_format($TPL_VAR["ncash"]["ncash_emoney"])?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["ncash"]["ncash_cash"]){?>
	<tr>
		<td>���̹�ĳ��</td>
		<td>- <?php echo number_format($TPL_VAR["ncash"]["ncash_cash"])?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["ncash"]["save_mode"]=='ncash'||$TPL_VAR["ncash"]["save_mode"]=='both'){?>
	<tr>
		<td>���̹����ϸ���<br/>����</td>
		<td>
			&nbsp;&nbsp;<?php echo $TPL_VAR["ncash"]["totalAccumRate"]?>%
			<font class="small red">
<?php if($_POST["save_mode"]=='ncash'){?>
			�� �������� ������ ��� <b>���̹� ���ϸ���</b>�� ������ �˴ϴ�.
<?php }?>
			</font>
		</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["eggFee"]){?>
	<tr>
		<td>�������� ������</td>
		<td><?php echo number_format($TPL_VAR["eggFee"])?>��</td>
	</tr>
<?php }?>
	<tr>
		<td>�����ݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 04-1 �������Ա� --><?php if($TPL_VAR["settlekind"]=="a"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>�Աݰ��¼���</td>
		<td>
		<select name=bankAccount required label="�Աݰ���">
		<option value="">== �Աݰ��¸� �������ּ��� ==
<?php if($TPL__bank_1){foreach($GLOBALS["bank"] as $TPL_V1){?>
		<option value="<?php echo $TPL_V1["sno"]?>"><?php echo $TPL_V1["bank"]?> <?php echo $TPL_V1["account"]?> <?php echo $TPL_V1["name"]?>

<?php }}?>
		</select>
		</td>
	</tr>
	<tr>
		<td>�Ա��ڸ�</td>
		<td>
		<input type=text name=bankSender class=line value="<?php echo $TPL_VAR["nameOrder"]?>" required  label="�Ա��ڸ�">
		</td>
	</tr>
	<tr>
		<td>�Աݱݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>

</form>

<!-- 04-2 �ſ�ī�� --><?php if($TPL_VAR["settlekind"]=="c"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table width=100%>
	<col width=100>
	<tr>
		<td>ī�����</td>
		<td>�ſ�ī��</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

<?php $this->print_("card_gate",$TPL_SCP,1);?>


	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 04-3 ������ü --><?php }elseif($TPL_VAR["settlekind"]=="o"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table width=100%>
	<col width=100>
	<tr>
		<td>�������</td>
		<td>������ü</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

<?php $this->print_("card_gate",$TPL_SCP,1);?>


	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 04-4 ������� --><?php }elseif($TPL_VAR["settlekind"]=="v"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table width=100%>
	<col width=100>
	<tr>
		<td>�������</td>
		<td>�������</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

<?php $this->print_("card_gate",$TPL_SCP,1);?>


	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 04-5 �ڵ��� --><?php }elseif($TPL_VAR["settlekind"]=="h"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table width=100%>
	<col width=100>
	<tr>
		<td>�������</td>
		<td>�ڵ���</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

<?php if($TPL_VAR["MobiliansEnabled"]!=true){?>
<?php $this->print_("card_gate",$TPL_SCP,1);?>

<?php }?>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 04-6 ����Ʈ --><?php }elseif($TPL_VAR["settlekind"]=="p"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table width=100%>
	<col width=100>
	<tr>
		<td>�������</td>
		<td>����Ʈ</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

<?php $this->print_("card_gate",$TPL_SCP,1);?>


	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 04-7 CUP --><?php }elseif($TPL_VAR["settlekind"]=="u"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table width=100%>
	<col width=100>
	<tr>
		<td>�������</td>
		<td>CUPS (�߱�ī��� �Һ� �� �κ���Ұ� �������� �ʽ��ϴ�.)</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

<?php $this->print_("card_gate",$TPL_SCP,1);?>


	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 04-8 �������� --><?php }elseif($TPL_VAR["settlekind"]=="y"){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table width=100%>
	<col width=100>
	<tr>
		<td>�������</td>
		<td>��������</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b class=red><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
	</table>

<?php $this->print_("card_gate",$TPL_SCP,1);?>


	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>

<div style="padding:20px" align=center id="avoidDblPay">
<a href="javascript:submitSettleForm()"><img src="/shop/data/skin/campingyo/img/common/btn_payment.gif"></a>
<a href="<?php echo url("order/order.php")?>&"><img src="/shop/data/skin/campingyo/img/common/btn_back.gif"></a>
</div>

</div><!-- End indiv -->


<script>
function swapSettleButton(){
	if (document.getElementById('avoidDblPay')) document.getElementById('avoidDblPay').innerHTML = "<a href='javascript:submitSettleForm()'><img src='/shop/data/skin/campingyo/img/common/btn_payment.gif'></a><a href='<?php echo url("order/order.php")?>&'><img src='/shop/data/skin/campingyo/img/common/btn_back.gif'></a>";
}
function submitSettleForm()
{
	var fm = document.frmSettle;

	if (!chkForm(fm)) return;

	/*** �ֹ��ʼ����� üũ ***/
	if (!fm.nameOrder.value) return;
	if (!fm.ordno.value) return;

	/**���� ����� popup�� �ƴѰ�쿡 �÷������� üũ�մϴ�.**/
<?php if($GLOBALS["cfg"]["settlePgPopup"]==''){?>
		/*** PG �÷����� ��ġ���� üũ ***/
<?php if($TPL_VAR["settlekind"]!="a"){?> //������
		if (typeof(chkPgFlag) != 'undefined') {
			if (chkPgFlag() == false) {
				return;
			}
		}
<?php }?>
<?php }?>

	if (document.getElementById('avoidDblPay')) document.getElementById('avoidDblPay').innerHTML = "--- ���� ����ó�����Դϴ�. ��ø� ��ٷ��ּ���. ---<br><a href='javascript:swapSettleButton();'><img src='/shop/data/skin/campingyo/img/common/btn_cancel.gif'></a>";

<?php if($GLOBALS["cfg"]["settlePg"]=='dacom'&&$TPL_VAR["settlekind"]!="a"){?>
	window.open("","Window","width=330, height=430, status=yes, scrollbars=no,resizable=yes, menubar=no");
<?php }?>

<?php if($GLOBALS["cfg"]["settlePg"]=='lgdacom'&&$TPL_VAR["settlekind"]=="u"){?>
	window.open("","Window","width=390, height=430, status=yes, scrollbars=no,resizable=yes, menubar=no");
<?php }?>
	

	fm.submit();
	
}
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>