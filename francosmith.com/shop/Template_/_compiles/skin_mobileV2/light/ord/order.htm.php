<?php /* Template_ 2.2.7 2014/02/03 20:16:22 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/light/ord/order.htm 000025576 */ 
if (is_array($GLOBALS["r_deli"])) $TPL__r_deli_1=count($GLOBALS["r_deli"]); else if (is_object($GLOBALS["r_deli"]) && in_array("Countable", class_implements($GLOBALS["r_deli"]))) $TPL__r_deli_1=$GLOBALS["r_deli"]->count();else $TPL__r_deli_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "�ֹ��ϱ�";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#m_order{background:#FFFFFF;}

section#nm_order {background:#FFFFFF; padding:12px;font-family:dotum;font-size:12px;}
section#nm_order .sub_title{height:22px; line-height:22px; color:#436693; font-weight:bold; font-size:12px;}
section#nm_order .sub_title .point {width:4px; height:22px; background:url('/shop/data/skin_mobileV2/light/common/img/bottom/icon_guide.png') no-repeat center left; float:left; margin-right:7px;}
section#nm_order table{border:none; border-top:solid 1px #dbdbdb;width:100%; margin-bottom:20px;}
section#nm_order table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#nm_order table th{text-align:center; background:#f5f5f5; width:70px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}
section#nm_order table .img{padding:5px; width:60px;}
section#nm_order table .img img{border:solid 1px #d9d9d9;}
section#nm_order table td input[type=text], input[type=password], input[type=email], input[type=number], select{height:21px;}

section#nm_order table td.phone input[type=number]{width:45px;height:21px;}
section#nm_order table td.zipcode input[type=text]{width:60px;height:21px;}
section#nm_order table td.zipcode input[type=number]{width:45px;height:21px;}
section#nm_order table td.coupon input[type=number]{width:100px;height:21px;}
section#nm_order table td.emoney input[type=number]{width:100px;height:21px;}
section#nm_order table td textarea{width:95%;height:116px;}
section#nm_order .btn_center {margin:auto; width:198px; height:34px; margin-top:20px; margin-bottom:20px;}
section#nm_order .btn_center .btn_payment{border:none; background:#f35151; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:left; font-family:dotum; line-height:34px; border-radius:3px;}
section#nm_order .btn_center .btn_prev{border:none; background:#808591;  color:#FFFFFF; font-size:14px; width:94px; height:34px; float:right; font-family:dotum; line-height:34px; border-radius:3px;}
section#nm_order .goods-nm{color:#353535; font-weight:bold; fonst-size:14px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
section#nm_order .goods-price{color:#f03c3c; font-size:12px;}
#zipcode_list ul {list-style:none;}
#zipcode_list li {padding:5px; 0px;}
.btn_zipcode {background:#808591; width:73px; height:25px; border:none; color:#FFFFFF; text-align:center; margin-left:10px;line-height:25px; border-radius:3px;vertical-align:middle;}
.coupon-btn-area {margin-bottom:10px;}
.btn_coupon {background:#808591; width:73px; height:25px; border:none; color:#FFFFFF; text-align:center; margin-right:10px;line-height:25px; border-radius:3px;}
.max_width{width:95%;}
</style>


<?php echo $TPL_VAR["NaverMileageScript"]?>

<script id="delivery"></script>
<form id="form" name="frmOrder" action="settle.php" method="post" onsubmit="return chkForm2(this)">
<input type="hidden" name="ordno" value="<?php echo $TPL_VAR["ordno"]?>">
<div id="apply_coupon"></div>
<section id="m_order" class="content">
<?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<input type="hidden" name=item_apply_coupon[]>
<?php }}?>
<?php echo $this->define('tpl_include_file_1',"proc/orderitem.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

</section>
<section id="nm_order" class="content">

<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../../shop/data/skin_mobileV2/%s/service/_private_non.txt",$GLOBALS["cfgMobileShop"]["tplSkinMobile"]))){?>
<!-- �������� ���� ���� -->
<div class="sub_title"><div class="point"></div>�������� ���� ����</div>
<div style="padding-top: 10px; background: #f1f1f1; margin-bottom: 20px;">
	<div style="font-size: 11px; padding: 5px;">
		<span style="font-weight: bold;">�� ��ȸ�� �ֹ��� ���� �������� ������ ���� ����</span>
		<span>(�ڼ��� ������ ��<a href="../service/private.php">����������޹�ħ</a>���� Ȯ���Ͻñ� �ٶ��ϴ�)</span>
	</div>
	<div class="agreement-content" style="height: 100px; overflow-y: scroll; border: solid #dddddd 1px; background: #ffffff; padding: 5px;">
		<?php echo $this->define('tpl_include_file_2',"/service/_private_non.txt")?> <?php $this->print_("tpl_include_file_2",$TPL_SCP,1);?>

	</div>
	<div style="text-align: center; padding: 5px;">
		<input id="guest-private-agreement" type="hidden"/>
		<input id="private-agree" type="radio" name="private" value="y"/>
		<label for="private-agree">�����մϴ�</label>
		<input id="private-disagree" type="radio" name="private" value=""/>
		<label for="private-disagree">�������� �ʽ��ϴ�</label>
	</div>
</div>
<?php }?>

<!-- 01 �ֹ������� -->
<div class="sub_title"><div class="point"></div>�ֹ�������</div>
<table>
	<tr>
		<th>�ֹ��ڸ�</th>
		<td>
			<input type="text" name="nameOrder" value="<?php echo $TPL_VAR["name"]?>" <?php echo $GLOBALS["style_member"]?> required msgR="�ֹ��Ͻôº��� �̸��� �����ּ���" class="max_width"/>
		</td>
	</tr>
<?php if($TPL_VAR["address"]){?>
	<tr>
		<th>�ּ�</th>
		<td>
			<?php echo $TPL_VAR["address"]?><br /><?php echo $TPL_VAR["address_sub"]?>

<?php if($TPL_VAR["road_address"]){?><div style="padding-top:5px;font:12px dotum;color:#999;"><?php echo $TPL_VAR["road_address"]?> <?php echo $TPL_VAR["address_sub"]?></div><?php }?>
		</td>
	</tr>
<?php }?>
	<tr>
		<th>��ȭ��ȣ</th>
		<td class="phone">
			<input type="number" name="phoneOrder[]" value="<?php echo $TPL_VAR["phone"][ 0]?>" size="3" maxlength="3" required /> -
			<input type="number" name="phoneOrder[]" value="<?php echo $TPL_VAR["phone"][ 1]?>" size="4" maxlength="4" required /> -
			<input type="number" name="phoneOrder[]" value="<?php echo $TPL_VAR["phone"][ 2]?>" size="4" maxlength="4" required />
		</td>
	</tr>
	<tr>
		<th>�޴���</th>
		<td class="phone">
			<input type="number" name="mobileOrder[]" value="<?php echo $TPL_VAR["mobile"][ 0]?>" size="3" maxlength="3" required /> -
			<input type="number" name="mobileOrder[]" value="<?php echo $TPL_VAR["mobile"][ 1]?>" size="4" maxlength="4" required /> -
			<input type="number" name="mobileOrder[]" value="<?php echo $TPL_VAR["mobile"][ 2]?>" size="4" maxlength="4" required />
		</td>
	</tr>
	<tr>
		<th>�̸���</th>
		<td class="email">
			<input type="text" name="email" value="<?php echo $TPL_VAR["email"]?>" required option=regEmail class="max_width" />
		</td>
	</tr>
</table>
<div class="sub_title"><div class="point"></div>�������</div>
<table>
	<tr>
		<th>�����</th>
		<td>
			<label><input type="checkbox" onclick="ctrl_field(this.checked)" <?php if($GLOBALS["sess"]){?>checked<?php }?> /> �ֹ��� ������ �����մϴ�</label>
		</td>
	</tr>
	<tr>
		<th>�����Ǻ�</th>
		<td>
			<input type="text" name="nameReceiver" value="<?php echo $TPL_VAR["name"]?>" required class="max_width" fld_esssential label="�����Ǻ�" />
		</td>
	</tr>
	<tr>
		<th>�����ȣ</th>
		<td class="zipcode">
			<div>
			<input type="number" name="zipcode[]" id="zipcode0" size=3 readonly value="<?php echo $TPL_VAR["zipcode"][ 0]?>" required /> -
			<input type="number" name="zipcode[]" id="zipcode1" size=3 readonly value="<?php echo $TPL_VAR["zipcode"][ 1]?>" required />
			<button id="zipcode-btn" class="btn_zipcode" type="button" onClick="window.open('<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/popup_address.php?isMobile=true','','scrollbars=1');">�����ȣ</button>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">

		</td>
	</tr>
	<tr>
		<th>�ּ�</th>
		<td>
			<div><input type="text" name="address" id="address" value="<?php echo $TPL_VAR["address"]?>"  required class="max_width" onFocus="search_zipcode();this.blur();" fld_esssential label="�ּ�" /></div>
		</td>
	</tr>
	<tr>
		<th>�����ּ�</th>
		<td>
			<input type="text" name="address_sub" id="address_sub" value="<?php echo $TPL_VAR["address_sub"]?>"  label="�����ּ�" class="max_width" label="�����ּ�" onkeyup="SameAddressSub(this)" oninput="SameAddressSub(this)"/>
			<input type="hidden" name="road_address" id="road_address" style="width:100%" value="<?php echo $TPL_VAR["road_address"]?>" class="line">
			<div style="padding:5px 5px 0 1px;font:12px dotum;color:#999;" id="div_road_address"><?php echo $TPL_VAR["road_address"]?></div>
			<div style="padding:5px 0 0 1px;font:12px dotum;color:#999;" id="div_road_address_sub"><?php if($TPL_VAR["road_address"]){?><?php echo $TPL_VAR["address_sub"]?><?php }?></div>
		</td>
	</tr>
	<tr>
		<th>��ȭ��ȣ</th>
		<td class="phone">
			<input type="number" name="phoneReceiver[]" value="<?php echo $TPL_VAR["phone"][ 0]?>" size="3" maxlength="3" /> -
			<input type="number" name="phoneReceiver[]" value="<?php echo $TPL_VAR["phone"][ 1]?>" size="4" maxlength="4" /> -
			<input type="number" name="phoneReceiver[]" value="<?php echo $TPL_VAR["phone"][ 2]?>" size="4" maxlength="4" />
		</td>
	</tr>
	<tr>
		<th>�޴���</th>
		<td class="phone">
			<input type="number" name="mobileReceiver[]" value="<?php echo $TPL_VAR["mobile"][ 0]?>" size="3" maxlength="3" required fld_esssential label="�����Ǻ� �޴�����ȣ" /> -
			<input type="number" name="mobileReceiver[]" value="<?php echo $TPL_VAR["mobile"][ 1]?>" size="4" maxlength="4" required fld_esssential label="�����Ǻ� �޴�����ȣ" /> -
			<input type="number" name="mobileReceiver[]" value="<?php echo $TPL_VAR["mobile"][ 2]?>" size="4" maxlength="4" required fld_esssential label="�����Ǻ� �޴�����ȣ" />
		</td>
	</tr>
	<tr>
		<th>�޽���</th>
		<td>
			<textarea name="memo"><?php echo $TPL_VAR["memo"]?></textarea>
		</td>
	</tr>
</table>

<div class="sub_title"><div class="point"></div>��ۼ���</div>
<table>
	<tr>
		<th>��ۼ���</th>
		<td>
			<div><input type="radio" name="deliPoli" value="0" checked onclick="getDelivery()" onblur="chk_emoney(document.frmOrder.emoney)" /> �⺻���</div>
<?php if($TPL__r_deli_1){$TPL_I1=-1;foreach($GLOBALS["r_deli"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_V1){?>
			<div><input type="radio" name="deliPoli" value="<?php echo $TPL_I1+ 1?>" onclick="getDelivery()" onblur="chk_emoney(document.frmOrder.emoney)" /> <?php echo $TPL_V1?></div>
<?php }?>
<?php }}?>
		</td>
	</tr>
</table>

<div class="sub_title"><div class="point"></div>�����ݾ�</div>
<table>
	<tr>
		<th>�հ�ݾ�</th>
		<td>
			<span id="paper_goodsprice"><?php echo number_format($TPL_VAR["cart"]->goodsprice)?></span> ��
		</td>
	</tr>
	<tr>
		<th>��ۺ�</th>
		<td>
			<div id="paper_delivery_msg1"><span id="paper_delivery"></span> ��</div>
			<div id="paper_delivery_msg2"></div>
		</td>
	</tr>
<?php if($TPL_VAR["cart"]->special_discount_amount){?>
	<tr>
		<th>��ǰ����</th>
		<td><span id='special_discount_amount' style="width:145;text-align:right"><?php echo number_format($TPL_VAR["cart"]->special_discount_amount)?></span> ��</td>
	</tr>
<?php }?>
<?php if($GLOBALS["sess"]){?>
	<tr>
		<th>ȸ������</th>
		<td>
			<span id='memberdc'><?php echo number_format($TPL_VAR["cart"]->dcprice)?></span> ��
		</td>
	</tr>
	<tr>
		<th>��������</th>
		<td class="coupon">
			<div class="coupon-btn-area">
				<button class="btn_coupon" type="button" onClick="removeCoupon();">�������</button>
				<button class="btn_coupon" type="button" onClick="getCoupon();">������ȸ</button>
			</div>
			<div id="coupon_list"></div>
			<div style="height:32px;">����:<input type="text" id="coupon" name="coupon" size="5" style="text-align:right" value="0" readonly> ��</div>
			<div style="height:32px;">����:<input type="text" id="coupon_emoney" name="coupon_emoney" size="5" style="text-align:right" value="0" readonly> ��</div>
		</td>
	</tr>
	<tr>
		<th>������</th>
		<td class="emoney">
			<input type="text" name="emoney"  size="5" style="text-align:right" value="0" onblur="chk_emoney(this);" onkeyup="calcu_settle();" onkeydown="if (event.keyCode == 13) {return false;}" <?php if($GLOBALS["set"]["emoney"]["totallimit"]>$TPL_VAR["cart"]->goodsprice){?>disabled<?php }?>> �� (���������� : <?php echo number_format($GLOBALS["member"]["emoney"])?>��)

<?php if($GLOBALS["member"]["emoney"]<$GLOBALS["set"]["emoney"]["hold"]){?>

			<div style="font-size:12px;color:#436693;margin-top:7px;">
			������������ <?php echo number_format($GLOBALS["set"]["emoney"]["hold"])?>���̻� �� ��� ����Ͻ� �� �ֽ��ϴ�.
			</div>
<?php if($GLOBALS["set"]["emoney"]["totallimit"]>$TPL_VAR["cart"]->goodsprice){?>
			<div style="font-size:12px;color:#436693;margin-top:7px;">
			<?php echo number_format($GLOBALS["set"]["emoney"]["totallimit"])?>�� �̻� �ֹ��� ������ ��� ����.
			</div>
<?php }?>


<?php }else{?>

			<div style="font-size:12px;color:#436693;margin-top:7px;">
<?php if($GLOBALS["emoney_max"]&&$GLOBALS["emoney_max"]>=$GLOBALS["set"]["emoney"]["min"]){?>
				<?php echo number_format($GLOBALS["set"]["emoney"]["min"])?>������ <span id=print_emoney_max><?php echo number_format($GLOBALS["emoney_max"])?></span>������ ����� �����մϴ�.
<?php }elseif($GLOBALS["emoney_max"]&&$GLOBALS["emoney_max"]<$GLOBALS["set"]["emoney"]["min"]){?>
				�ּ� <?php echo number_format($GLOBALS["set"]["emoney"]["min"])?>�� �̻� ����Ͽ��� �մϴ�.
<?php }elseif(!$GLOBALS["emoney_max"]){?>
				���������ݸ�ŭ ��밡���մϴ�.
<?php }?>
			</div>

<?php }?>
			<input type="hidden" name="emoney_max" value="<?php echo $GLOBALS["emoney_max"]?>">
		</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["NaverMileageForm2"]){?>
	<tr>
		<td colspan="3">
		<?php echo $TPL_VAR["NaverMileageForm2"]?>

		</td>
	</tr>
<?php }?>
	<tr>
		<th>�����ݾ�</th>
		<td>
			<span id=paper_settlement style="width:146px;text-align:right; color:FF6C68;"><?php echo number_format($TPL_VAR["cart"]->totalprice-$TPL_VAR["cart"]->dcprice-$TPL_VAR["cart"]->special_discount_amount)?></span> ��
		</td>
	</tr>
</table>


<div class="sub_title"><div class="point"></div>��������</div>
<table>
	<tr>
		<th>��������</th>
		<td>
			<input type="hidden" name="escrow" value="N" />
<?php if($GLOBALS["set"]["use"]["a"]){?>
			<div><label><input type=radio name=settlekind value="a" onclick="input_escrow(this,'N')" style="height:30px"/>�������Ա�</label></div>
<?php }?>
<?php if($GLOBALS["set"]["use_mobile"]["c"]){?>
			<div><label><input type=radio name=settlekind value="c" onclick="input_escrow(this,'N')" style="height:30px"/>�ſ�ī��</label></div>
<?php }?>
<?php if($GLOBALS["set"]["use_mobile"]["v"]){?>
			<div><label><input type=radio name=settlekind value="v" onclick="input_escrow(this,'N')" style="height:30px"/>�������</label></div>
<?php }?>
<?php if($GLOBALS["set"]["use_mobile"]["h"]){?>
			<div><label><input type=radio name=settlekind value="h" onclick="input_escrow(this,'N')" style="height:30px"/>�ڵ���</label></div>
<?php }?>
		</td>
	</tr>
</table>

<?php if($GLOBALS["pg_mobile"]["receipt"]=='Y'&&$GLOBALS["set"]["receipt"]["order"]=='Y'){?>
<!-- 05 ���ݿ��������� -->
<div  id="cash">
<div class="sub_title"><div class="point"></div>���ݿ���������</div>
<?php echo $this->define('tpl_include_file_3',"proc/_cashreceiptOrder.htm")?> <?php $this->print_("tpl_include_file_3",$TPL_SCP,1);?>

</div>
<?php }?>
<div class="m_ord">
<div class="btn_center">
	<div class="btn_pay"><button type="submit" id="payment-btn" class="btn_payment">�����ϱ�</button></div>
	<div class="btn_pre"><button type="button" id="prev-btn" class="btn_prev"  onclick="history.back();">���</button></div>
</div>
</div>

</section>

</form>

<div id=dynamic></div>

<script>
var emoney_max = <?php echo $GLOBALS["emoney_max"]?>;
function chkForm2(fm)
{
	var guestPrivateAgreement = document.getElementById("guest-private-agreement");
	var privateAgreement = jQuery(fm).find("[name=private]:checked").val();
	if (guestPrivateAgreement != null) {
		if (privateAgreement !== "y") {
			alert("��ȸ�� �������� ������ ���Ǹ� �ϼž߸� �ֹ��� �����մϴ�.");
			return false;
		}
	}

	if (typeof(fm.settlekind)=="undefined"){
		alert("���������� Ȱ��ȭ�� �� �Ǿ����ϴ�. �����ڿ��� �����ϼ���.");
		return false;
	}

	var obj = document.getElementsByName('settlekind');
	if (obj[0].getAttribute('required') == undefined){
		obj[0].setAttribute('required', '');
		obj[0].setAttribute('label', '��������');
	}
	// ���⿡�� ���� ����ó��
	var checked_count =0;
	var chks = document.getElementsByName('coupon_[]');
	for (var i=0,m=chks.length;i<m ;i++) {
		if (chks[i].checked == true) {
			checked_count ++;
		}
	}
	// ���õ� ������ �ϳ��� ���ٸ� , ���������� ���� �����ؾ� �Ѵ�.
	if (checked_count == 0) {
		removeCoupon();
	}

<?php if($TPL_VAR["Mobilians_PaymentLimitPrice"]){?>
	var mobilians_paymentLimitPrice = parseInt("<?php echo $TPL_VAR["Mobilians_PaymentLimitPrice"]?>"), settleprice = parseInt(uncomma(_ID('paper_settlement').innerHTML)), checkedSettlekind;
	for (var i = 0; i < fm.settlekind.length; i++) {
		if (fm.settlekind[i].checked && fm.settlekind[i].value == "h") {
			mobilians_paymentLimitPrice = (isNaN(mobilians_paymentLimitPrice) ? 0 : mobilians_paymentLimitPrice);
			settleprice = (isNaN(settleprice) ? 0 : settleprice);
			if (mobilians_paymentLimitPrice > 0 && mobilians_paymentLimitPrice < settleprice) {
				alert('�޴��� ���� ���� �ݾ��� <?php echo number_format($TPL_VAR["Mobilians_PaymentLimitPrice"])?>�� ���� �Դϴ�.\r\n(�ѵ��ݾ��� ���� ���� �Ǵ� ��Ż纰�� �ݾ� ���̰� �ֽ��ϴ�.)');
				return false;
			}
		}
	}
<?php }?>
	return chkForm(fm);
}

function input_escrow(obj,val)
{
	obj.form.escrow.value = val;
	if (typeof(cash_required) == 'function') cash_required();
}

function ctrl_field(val)
{
	if (val) copy_field();
	else clear_field();
}
function copy_field()
{
	var form = document.frmOrder;
	form.nameReceiver.value = form.nameOrder.value;
	form['zipcode[]'][0].value = "<?php echo $TPL_VAR["zipcode"][ 0]?>";
	form['zipcode[]'][1].value = "<?php echo $TPL_VAR["zipcode"][ 1]?>";
	form.address.value = "<?php echo $TPL_VAR["address"]?>";
	form.address_sub.value = "<?php echo $TPL_VAR["address_sub"]?>";
	form.road_address.value = "<?php echo $TPL_VAR["road_address"]?>";
	document.getElementById("div_road_address").innerHTML =  "<?php echo $TPL_VAR["road_address"]?>";
	document.getElementById("div_road_address_sub").innerHTML =  form.road_address.value ? "<?php echo $TPL_VAR["address_sub"]?>" : "";
	form['phoneReceiver[]'][0].value = form['phoneOrder[]'][0].value;
	form['phoneReceiver[]'][1].value = form['phoneOrder[]'][1].value;
	form['phoneReceiver[]'][2].value = form['phoneOrder[]'][2].value;
	form['mobileReceiver[]'][0].value = form['mobileOrder[]'][0].value;
	form['mobileReceiver[]'][1].value = form['mobileOrder[]'][1].value;
	form['mobileReceiver[]'][2].value = form['mobileOrder[]'][2].value;

	getDelivery();
}
function clear_field()
{
	var form = document.frmOrder;
	form.nameReceiver.value = "";
	form['zipcode[]'][0].value = "";
	form['zipcode[]'][1].value = "";
	form.address.value = "";
	form.address_sub.value = "";
	form.road_address.value = "";
	document.getElementById("div_road_address").innerHTML =  "";
	document.getElementById("div_road_address_sub").innerHTML =  "";
	form['phoneReceiver[]'][0].value = "";
	form['phoneReceiver[]'][1].value = "";
	form['phoneReceiver[]'][2].value = "";
	form['mobileReceiver[]'][0].value = "";
	form['mobileReceiver[]'][1].value = "";
	form['mobileReceiver[]'][2].value = "";
}
function cutting(emoney)
{
	var chk_emoney = new String(emoney);
	reg = /(<?php echo substr($GLOBALS["set"]["emoney"]["base"], 1)?>)$/g;
	if (emoney && !chk_emoney.match(reg)){
		emoney = Math.floor(emoney/<?php echo $GLOBALS["set"]["emoney"]["base"]?>) * <?php echo $GLOBALS["set"]["emoney"]["base"]?>;
	}
	return emoney;
}
function chk_emoney(obj)
{
	var form = document.frmOrder;
	var my_emoney = <?php echo $TPL_VAR["emoney"]+ 0?>;
	var max = '<?php echo $GLOBALS["set"]["emoney"]["max"]?>';
	var min = '<?php echo $GLOBALS["set"]["emoney"]["min"]?>';
	var hold = '<?php echo $GLOBALS["set"]["emoney"]["hold"]?>';

	var delivery	= uncomma(document.getElementById('paper_delivery').innerHTML);
	var goodsprice = uncomma(document.getElementById('paper_goodsprice').innerText);
<?php if($GLOBALS["set"]["emoney"]["emoney_use_range"]){?>
	var erangeprice = goodsprice + delivery;
<?php }else{?>
	var erangeprice = goodsprice;
<?php }?>
	var max_base = erangeprice - uncomma(_ID('memberdc').innerHTML) - uncomma(document.getElementsByName('coupon')[0].value);
	if( form.coupon ){
		 var coupon = uncomma(form.coupon.value);
	}
	max = getDcprice(max_base,max,<?php echo $GLOBALS["set"]["emoney"]["base"]?>);
	min = parseInt(min);

	if (max > max_base)  max = max_base;
	if( _ID('print_emoney_max') && _ID('print_emoney_max').innerHTML != comma(max)  )_ID('print_emoney_max').innerHTML = comma(max);

	var emoney = uncomma(obj.value);
	if (emoney>my_emoney) emoney = my_emoney;

	// ����/�̸Ӵ� �ߺ� ��� üũ
	var dup = <?php if($GLOBALS["set"]["emoney"]["useduplicate"]=='1'){?>true<?php }else{?>false<?php }?>;
	if (my_emoney > 0 && emoney > 0 && (parseInt(coupon) > 0 || parseInt(coupon_emoney)) > 0 && !dup) {
		alert('�����ݰ� ���� ����� �ߺ�������� �ʽ��ϴ�.');
		emoney = 0;
	}

	if(my_emoney > 0 && emoney > 0 && my_emoney < hold){
		alert("������������ "+ comma(hold) + "�� �̻� �� ��쿡�� ����Ͻ� �� �ֽ��ϴ�.");
		emoney = 0;
	}
	if (min && emoney > 0 && emoney < min){
		alert("�������� " + comma(min) + "�� ����  ����� �����մϴ�");
		emoney = 0;
	} else if (max && emoney > max && emoney > 0){
		if(emoney_max < min){
			alert("�ֹ� ��ǰ �ݾ��� �ּ� ��� ������ " + comma(min) + "�� ����  �۽��ϴ�.");
			emoney = 0;
		}else{
			alert("�������� " + comma(min) + "�� ���� " + comma(max) + "�� ������ ����� �����մϴ�");
			emoney = max;
		}
	}

	obj.value = comma(cutting(emoney));
	calcu_settle();
}

function calcu_settle()
{
	var dc=0;
	var special_discount_amount = 0;
	var coupon = settleprice = 0;
	var goodsprice	= uncomma(document.getElementById('paper_goodsprice').innerHTML);
	var delivery	= uncomma(document.getElementById('paper_delivery').innerHTML);
	if(_ID('memberdc')) dc = uncomma(_ID('memberdc').innerHTML);
	if(_ID('special_discount_amount')) special_discount_amount = uncomma(_ID('special_discount_amount').innerHTML);
	var emoney = (document.frmOrder.emoney) ? uncomma(document.frmOrder.emoney.value) : 0;
	if (document.frmOrder.coupon){
		coupon = uncomma(document.frmOrder.coupon.value);
		if (goodsprice + delivery - dc - coupon - emoney < 0){
<?php if($GLOBALS["set"]["emoney"]["emoney_use_range"]){?>
			emoney = goodsprice + delivery - dc - coupon - special_discount_amount;
<?php }else{?>
			emoney = goodsprice - dc - coupon - special_discount_amount;
<?php }?>
			document.frmOrder.emoney.value = comma(cutting(emoney));
		}
		dc += coupon + emoney;
	}
	var settlement = (goodsprice + delivery - dc - special_discount_amount);
	<?php echo $TPL_VAR["NaverMileageCalc"]?>

	document.getElementById('paper_settlement').innerHTML = comma(settlement);
}

function getDelivery(){
	var form = document.frmOrder;
	var obj = form.deliPoli;
	var coupon = 0;
	var emoney = 0;

	var deliPoli = 0;
	for(var i=0;i<obj.length;i++){
		if(obj[i].checked){
			deliPoli = i;
		}
	}
	if( form.coupon ) coupon = form.coupon.value;
	if( form.emoney ) emoney = form.emoney.value;
	var zipcode = form['zipcode[]'][0].value + '-' + form['zipcode[]'][1].value;
	var mode = 'order';

	$.ajax({
		url : '<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/getdelivery.php',
		type : 'get',
		async : false,
		data : "zipcode="+zipcode+"&deliPoli="+deliPoli+"&coupon="+coupon+"&emoney="+emoney+"&mode="+mode,
		success : function(res) {
			eval(res);
		}
	});
}


function getCoupon(){

	$('#coupon_list').show();
	$.ajax({
		url : '../proc/coupon_list.php',
		dataType : 'html',
		success : function(result){

			$('#coupon_list').html(result);
		},
		error: function(){
			alert('error');

		}
	});
}

function removeCoupon(){

	$('#coupon_list').html('');
	var apply_coupon = document.getElementById('apply_coupon');
	apply_coupon.innerHTML = '';
	document.frmOrder.coupon.value = '0';
	document.frmOrder.coupon_emoney.value = '0';
	chk_emoney(document.frmOrder.emoney);
	getDelivery();
	calcu_settle();
}

/*** �������� ù��° ��ü �ڵ� ���� ***/
window.onload = function (){
	var obj = document.getElementsByName('settlekind');
	for (var i = 0; i < obj.length; i++){
		if (obj[i].checked != true) continue;
		obj[i].onclick();
		var idx = i;
		break;
	}
	if (obj[0] && idx == null){ obj[0].checked = true; obj[0].onclick(); }

	getDelivery();
	$(".m_rightlongtext").css("width", (document.body.clientWidth-70-20-14)+"px");
}


</script>

<?php $this->print_("footer",$TPL_SCP,1);?>