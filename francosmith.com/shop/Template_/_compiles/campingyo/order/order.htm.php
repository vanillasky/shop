<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/order/order.htm 000039780 */  $this->include_("displayEggBanner");
if (is_array($GLOBALS["r_deli"])) $TPL__r_deli_1=count($GLOBALS["r_deli"]); else if (is_object($GLOBALS["r_deli"]) && in_array("Countable", class_implements($GLOBALS["r_deli"]))) $TPL__r_deli_1=$GLOBALS["r_deli"]->count();else $TPL__r_deli_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<style>
#orderbox {border:5px solid #F3F3F3; height:100%;}
#orderbox div {float:left; width:150; height:100%; background-color:#F3F3F3; text-align:right;}
#orderbox table {float:left; margin:10px 0px 10px 20px; }
#orderbox table th {width:100; text-align:left; font-weight:normal; height:25;}
#orderbox table td {padding-left:10px}
.scroll {
scrollbar-face-color: #FFFFFF;
scrollbar-shadow-color: #AFAFAF;
scrollbar-highlight-color: #AFAFAF;
scrollbar-3dlight-color: #FFFFFF;
scrollbar-darkshadow-color: #FFFFFF;
scrollbar-track-color: #F7F7F7;
scrollbar-arrow-color: #838383;
}
#boxScroll{width:96%; height:130px; overflow: auto; BACKGROUND: #ffffff; COLOR: #585858; font:9pt ����;border:1px #dddddd solid; overflow-x:hidden;text-align:left; }
.n_mileage{
	cursor: pointer;
	vertical-align: middle;
}
.mileage_button{
	cursor: pointer;
	vertical-align: middle;
}
#save_button, #ncash_view{
	margin: 4px 0 0 24px;
}
#ncash_view{
	display: none;
}
</style>
<script id="delivery"></script>

<!-- ����̹��� || ������ġ -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_order_01.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>�ֹ��ϱ�</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<div><img src="/shop/data/skin/campingyo/img/common/order_txt_01.gif" border=0></div>
<?php echo $this->define('tpl_include_file_1',"proc/orderitem.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>


<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
<!-- ��ȸ�� �������� ��޹�ħ ���� -->
<div style="margin-top:20;"><img src="/shop/data/skin/campingyo/img/common/order_txt_non.gif" border=0></div>
<div style="padding-top:10; background:#F1F1F1;  text-align:center;">
<div align="left" style="height:26;padding:3px 0 0 10px;">
<b>�� ��ȸ�� �ֹ��� ���� �������� ������ ���� ����</b> (�ڼ��� ������ ��<a href="<?php echo url("service/private.php")?>&">����������޹�ħ</a>���� Ȯ���Ͻñ� �ٶ��ϴ�)
</div>
<div id="boxScroll" class="scroll">
<?php echo $this->define('tpl_include_file_2',"/service/_private_non.txt")?> <?php $this->print_("tpl_include_file_2",$TPL_SCP,1);?>

</div>
<div align=center class=noline style="height:30;margin-top:10px;" >
<input type="radio" name="private" value="y" onclick="javascript:document.frmOrder.private.value='y';"> �����մϴ� &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="private" value="n" onclick="javascript:document.frmOrder.private.value='';"> �������� �ʽ��ϴ�
</div>
</div>
<div style="font-size:0;height:5px"></div>
<?php }?>

<div style="margin-top:20;"><img src="/shop/data/skin/campingyo/img/common/order_txt_02.gif" border=0></div>
<form id=form name=frmOrder action="<?php echo $TPL_VAR["orderActionUrl"]?>" method=post onsubmit="return chkForm2(this)">
<input type=hidden name=ordno value="<?php echo $TPL_VAR["ordno"]?>">
<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
<input type=hidden name=private value="" required msgR="��ȸ�� �������� ������ ���Ǹ� �ϼž߸� �ֹ��� �����մϴ�.">
<?php }?>
<?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<input type=hidden name=item_apply_coupon[]>
<?php }}?>

<div id=apply_coupon></div>

<!-- 01 �ֹ������� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_01.gif"></td>
	<td style="padding:10px">

	<table>
	<col width=100>
	<tr>
		<td>�ֹ��Ͻôº�</td>
		<td><input type=text name=nameOrder value="<?php echo $TPL_VAR["name"]?>" style="font-weight:bold" <?php echo $GLOBALS["style_member"]?> required msgR="�ֹ��Ͻôº��� �̸��� �����ּ���"></td>
	</tr>
<?php if($GLOBALS["sess"]){?>
	<tr>
		<td>�ּ�</td>
		<td>
			<?php echo $TPL_VAR["address"]?> <?php echo $TPL_VAR["address_sub"]?>

<?php if($TPL_VAR["road_address"]){?><div style="padding-top:5px;font:12px dotum;color:#999;"><?php echo $TPL_VAR["road_address"]?> <?php echo $TPL_VAR["address_sub"]?></div><?php }?>
		</td>
	</tr>
<?php }?>
	<tr>
		<td>��ȭ��ȣ</td>
		<td>
		<input type=text name=phoneOrder[] value="<?php echo $TPL_VAR["phone"][ 0]?>" size=3 maxlength=3 option=regNum required label="�ֹ��� ��ȭ��ȣ"> -
		<input type=text name=phoneOrder[] value="<?php echo $TPL_VAR["phone"][ 1]?>" size=4 maxlength=4 option=regNum required label="�ֹ��� ��ȭ��ȣ"> -
		<input type=text name=phoneOrder[] value="<?php echo $TPL_VAR["phone"][ 2]?>" size=4 maxlength=4 option=regNum required label="�ֹ��� ��ȭ��ȣ">
		</td>
	</tr>
	<tr>
		<td>�ڵ�����ȣ</td>
		<td>
		<input type=text name=mobileOrder[] value="<?php echo $TPL_VAR["mobile"][ 0]?>" size=3 maxlength=3 option=regNum required label="�ֹ��� �ڵ�����ȣ"> -
		<input type=text name=mobileOrder[] value="<?php echo $TPL_VAR["mobile"][ 1]?>" size=4 maxlength=4 option=regNum required label="�ֹ��� �ڵ�����ȣ"> -
		<input type=text name=mobileOrder[] value="<?php echo $TPL_VAR["mobile"][ 2]?>" size=4 maxlength=4 option=regNum required label="�ֹ��� �ڵ�����ȣ">
		</td>
	</tr>
	<tr>
		<td>�̸���</td>
		<td><input type=text name=email value="<?php echo $TPL_VAR["email"]?>" required option=regEmail></td>
	</tr>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<!-- 02 ������� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_02.gif"></td>
	<td style="padding:10px">

	<table>
	<col width=100>
	<tr>
		<td>����� Ȯ��</td>
		<td class=noline>
		<input type=checkbox onclick="ctrl_field(this.checked)" <?php if($GLOBALS["sess"]){?>checked<?php }?>> �ֹ��� ������ �����մϴ�
		</td>
	</tr>
	<tr>
		<td>�����Ǻ�</td>
		<td><input type=text name=nameReceiver value="<?php echo $TPL_VAR["name"]?>" required></td>
	</tr>
	<tr>
		<td>�����ǰ�</td>
		<td>
		<input type=text name=zipcode[] id="zipcode0" size=3 class=line readonly value="<?php echo $TPL_VAR["zipcode"][ 0]?>" required> -
		<input type=text name=zipcode[] id="zipcode1" size=3 class=line readonly value="<?php echo $TPL_VAR["zipcode"][ 1]?>" required>
		<a href="javascript:popup('../proc/popup_address.php',500,432)"><img src="/shop/data/skin/campingyo/img/common/btn_zipcode.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type=text name=address id="address" class=lineBig readonly value="<?php echo $TPL_VAR["address"]?>" required></td>
	</tr>
	<tr>
		<td></td>
		<td>
		<input type=text name=address_sub id="address_sub" class=lineBig value="<?php echo $TPL_VAR["address_sub"]?>" onkeyup="SameAddressSub(this)" oninput="SameAddressSub(this)" label="�����ּ�">
		<input type="hidden" name="road_address" id="road_address" style="width:100%" value="<?php echo $TPL_VAR["road_address"]?>" class="line">
		<div style="padding:5px 5px 0 1px;font:12px dotum;color:#999;" id="div_road_address"><?php echo $TPL_VAR["road_address"]?></div>
		<div style="padding:5px 0 0 1px;font:12px dotum;color:#999;" id="div_road_address_sub"><?php if($TPL_VAR["road_address"]){?><?php echo $TPL_VAR["address_sub"]?><?php }?></div>
		</td>
	</tr>
	<tr>
		<td>��ȭ��ȣ</td>
		<td>
		<input type=text name=phoneReceiver[] value="<?php echo $TPL_VAR["phone"][ 0]?>" size=3 maxlength=3 option=regNum required label="������ ��ȭ��ȣ"> -
		<input type=text name=phoneReceiver[] value="<?php echo $TPL_VAR["phone"][ 1]?>" size=4 maxlength=4 option=regNum required label="������ ��ȭ��ȣ"> -
		<input type=text name=phoneReceiver[] value="<?php echo $TPL_VAR["phone"][ 2]?>" size=4 maxlength=4 option=regNum required label="������ ��ȭ��ȣ">
		</td>
	</tr>
	<tr>
		<td>�ڵ�����ȣ</td>
		<td>
		<input type=text name=mobileReceiver[] value="<?php echo $TPL_VAR["mobile"][ 0]?>" size=3 maxlength=3 option=regNum required label="������ �ڵ�����ȣ"> -
		<input type=text name=mobileReceiver[] value="<?php echo $TPL_VAR["mobile"][ 1]?>" size=4 maxlength=4 option=regNum required label="������ �ڵ�����ȣ"> -
		<input type=text name=mobileReceiver[] value="<?php echo $TPL_VAR["mobile"][ 2]?>" size=4 maxlength=4 option=regNum required label="������ �ڵ�����ȣ">
		</td>
	</tr>
	<tr>
		<td>����� ����</td>
		<td><input type=text name=memo style="width:100%"></td>
	</tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr id="paper_delivery_menu">
		<td>��ۼ���</td>
		<td class="noline">

		<div style='float:left'><input type="radio" name="deliPoli" value="0" checked onclick="getDelivery()" onblur="chk_emoney(document.frmOrder.emoney)"> �⺻���</div>
<?php if($TPL__r_deli_1){$TPL_I1=-1;foreach($GLOBALS["r_deli"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_V1){?>
		<div style='float:left;padding-left:10'><input type="radio" name="deliPoli" value="<?php echo $TPL_I1+ 1?>" onclick="getDelivery()" onblur="chk_emoney(document.frmOrder.emoney)"> <?php echo $TPL_V1?></div>
<?php }?>
<?php }}?>
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
	<td style="padding:10px">

	<table>
	<col width=100>
	<tr>
		<td>��ǰ�հ�ݾ�</td>
		<td><p id="paper_goodsprice" style="width:146px;text-align:right;font-weight:bold;float:left;margin:0"><?php echo number_format($TPL_VAR["cart"]->goodsprice)?></p> ��</td>
	</tr>
	<tr>
		<td>��ۺ�</td>
		<td>
		<div id="paper_delivery_msg1"><div id="paper_delivery" style="width:146px;text-align:right;font-weight:bold;float:left;margin:0"></div>��</div>
		<div id="paper_delivery_msg2" style="display:none;width:160;text-align:right"></div>
		<div id="paper_delivery_msg_extra"  class="small red" style="display:none;"></div>
		</td>
	</tr>
<?php if($TPL_VAR["view_aboutdc"]){?>
	<tr>
		<td>��ٿ� ����</td>
		<td><span style="width:145;text-align:right"><?php echo number_format($TPL_VAR["about_coupon"])?></span> ��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["cart"]->special_discount_amount){?>
	<tr>
		<td>��ǰ����</td>
		<td><span id='special_discount_amount' style="width:145;text-align:right"><?php echo number_format($TPL_VAR["cart"]->special_discount_amount)?></span> ��</td>
	</tr>
<?php }?>
<?php if($GLOBALS["sess"]){?>
	<tr>
		<td>ȸ������</td>
		<td><span id='memberdc' style="width:145;text-align:right"><?php echo number_format($TPL_VAR["cart"]->dcprice)?></span> ��</td>
	</tr>
	<tr>
		<td>���� ����</td>
		<td>

		<table cellpadding=0 cellspacing=0>
		<tr>
			<td width=60 align=right>���� :</td>
			<td style="padding-left:3px">
			<input type=text name=coupon size=12 style="text-align:right" value=0 readonly> ��
			<a href="javascript:popup('../proc/popup_coupon.php',600,700)"><img src="/shop/data/skin/campingyo/img/common/btn_coupon.gif" align=absmiddle hspace=2></a><span id="del_coupon" style="visibility:hidden"><a href='javascript:del_coupon();'><img src="/shop/data/skin/campingyo/img/common/btn_coupon_del.gif" align=absmiddle hspace=2></a></span>
			</td>
		</tr>
		<tr>
			<td width=60 align=right>���� :</td>
			<td style="padding-left:3px">
			<input type=text name=coupon_emoney size=12 style="text-align:right" value=0 readonly> ��
			</td>
		</tr>
		</table>

		</td>
	</tr>
	<tr>
		<td valign=top style="padding-top:4px">������ ����</td>
		<td>

		<table cellpadding=0 cellspacing=0>
		<div style="display:<?php if($GLOBALS["member"]["emoney"]){?>block;<?php }else{?>none;<?php }?>}">
		<tr>
			<td width=60 align=right>������ :</td>
			<td style="padding-left:3px">
			<input type=text name=emoney id="emoney" size=12 style="text-align:right" value=0 onblur="chk_emoney(this);" onkeyup="calcu_settle();" onkeydown="if (event.keyCode == 13) {return false;}"  <?php if($GLOBALS["set"]["emoney"]["totallimit"]>$TPL_VAR["cart"]->goodsprice){?>disabled<?php }?>> ��
<?php if($GLOBALS["set"]["emoney"]["totallimit"]>$TPL_VAR["cart"]->goodsprice){?>
			<span class="small red"><?php echo number_format($GLOBALS["set"]["emoney"]["totallimit"])?>�� �̻� �ֹ��� ������ ��� ����.</span>
<?php }else{?>
			(���������� : <?php echo number_format($GLOBALS["member"]["emoney"])?>��)
<?php }?>
			</td>
		</tr>
		</div>
		<tr>
			<td colspan=2 class="small red">
<?php if($GLOBALS["member"]["emoney"]<$GLOBALS["set"]["emoney"]["hold"]){?>
			<div>������������ <?php echo number_format($GLOBALS["set"]["emoney"]["hold"])?>���̻� �� ��� ����Ͻ� �� �ֽ��ϴ�.</div>
<?php }else{?>
<?php if($GLOBALS["emoney_max"]&&$GLOBALS["emoney_max"]>=$GLOBALS["set"]["emoney"]["min"]){?>
			�������� <?php echo number_format($GLOBALS["set"]["emoney"]["min"])?>������ <span id=print_emoney_max><?php echo number_format($GLOBALS["emoney_max"])?></span>������ ����� �����մϴ�.
<?php }elseif($GLOBALS["emoney_max"]&&$GLOBALS["emoney_max"]<$GLOBALS["set"]["emoney"]["min"]){?>
			�������� �ּ� <?php echo number_format($GLOBALS["set"]["emoney"]["min"])?>�� �̻� ����Ͽ��� �մϴ�.
<?php }?>
<?php }?>
			<input type=hidden name=emoney_max value="<?php echo $GLOBALS["emoney_max"]?>">
			</td>
		</tr>
		</table>

		</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["ncash"]["useyn"]=='Y'){?>
	<tr id="naver-mileage-accum" style="display: none;">
		<td>������ ����</td>
		<td class="noline">
<?php if($GLOBALS["sess"]&&$TPL_VAR["ncash"]["save_mode"]=='choice'){?>
			<input type="radio" name="save_mode" value="" onclick="naver_mileage_initialize(this);"> ���θ� ������<br/>
			<input type="radio" name="save_mode" value="ncash"> ���̹� ���ϸ��� <img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/naver_mileage/images/n_mileage_on.png"/><br/>
<?php }elseif($GLOBALS["sess"]&&$TPL_VAR["ncash"]["save_mode"]=='both'){?>
			<input type="radio" name="save_mode" value="" onclick="naver_mileage_initialize(this);"> ���θ� �����ݸ� ����<br/>
			<input type="radio" name="save_mode" value="both" checked> ���θ� ������ �� ���̹� ���ϸ��� <img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/naver_mileage/images/n_mileage_on.png"/> ���� ����<br/>
<?php }else{?>
			<input type="radio" name="save_mode" value="unused" onclick="naver_mileage_initialize(this);"> ���̹� ���ϸ��� ���/���� ����<br/>
			<input type="radio" name="save_mode" value="ncash"> ���̹� ���ϸ��� <img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/naver_mileage/images/n_mileage_on.png"/><br/>
<?php }?>
			<div id="save_button"><img src="/shop/data/skin/campingyo/img/nmileage/n_mileage_use.gif" onClick="javascript:cash_save_use();" class="mileage_button"> ��ư�� Ŭ���ؼ� <span id="naver-mileage-accum-rate" style="color: #1ec228; font-weight: bold;"></span> �����ް� ����ϼ��� <img class="n_mileage" src="/shop/data/skin/campingyo/img/nmileage/n_mileage_info2.gif" onclick="mileage_info();"></div>
			<div id="ncash_view"></div>
			<input type="hidden" id="exception_price" name="exception_price" value="<?php echo $TPL_VAR["ncash"]["exception_price"]?>">
			<input type="hidden" id="reqTxId<?php echo $TPL_VAR["ncash"]["api_id"]?>" name="reqTxId<?php echo $TPL_VAR["ncash"]["api_id"]?>" value="">
			<input type="hidden" id="mileageUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>" name="mileageUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>" value="">
			<input type="hidden" id="cashUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>" name="cashUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>" value="">
			<input type="hidden" id="totalUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>" name="totalUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>" value="">
			<input type="hidden" id="baseAccumRate" name="baseAccumRate" value="">
			<input type="hidden" id="addAccumRate" name="addAccumRate" value="">
		</td>
	</tr>
<?php }?>

<?php if($GLOBALS["egg"]["use"]=="Y"&&($GLOBALS["egg"]["scope"]=="A"||($GLOBALS["egg"]["scope"]=="P"&&$TPL_VAR["cart"]->totalprice-$TPL_VAR["cart"]->dcprice>=$GLOBALS["egg"]["min"]))&&$GLOBALS["egg"]["feepayer"]=="B"){?>
	<tr>
		<td>�������� ������</td>
		<td><p id=paper_eggFee style="width:146px;text-align:right;font-weight:bold;float:left;margin:0">0</p> ��</td>
	</tr>
<?php }?>

	<tr>
		<td>�� �����ݾ�</td>
		<td><span id=paper_settlement style="width:146px;text-align:right;font:bold 14px tahoma; color:FF6C68;"><?php echo number_format($TPL_VAR["cart"]->totalprice-$TPL_VAR["cart"]->dcprice-$TPL_VAR["cart"]->special_discount_amount)?></span> ��</td>
	</tr>
	</table>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<!-- ���ž���ǥ�� start --><?php echo displayEggBanner( 1)?><!-- ���ž���ǥ�� end -->
<!-- 04 �������� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td style="padding:10px">

	<input type=hidden name=escrow value="N">
	<table>
	<col width=100>
	<tr>
		<td>�Ϲݰ���</td>
		<td class=noline>
<?php if($GLOBALS["set"]["use"]["a"]){?>
		<input type=radio name=settlekind value="a" onclick="input_escrow(this,'N')"> �������Ա�
<?php }?>
<?php if($GLOBALS["set"]["use"]["c"]){?>
		<input type=radio name=settlekind value="c" onclick="input_escrow(this,'N')"> �ſ�ī��
<?php }?>
<?php if($GLOBALS["set"]["use"]["o"]){?>
		<input type=radio name=settlekind value="o" onclick="input_escrow(this,'N')"> ������ü
<?php }?>
<?php if($GLOBALS["set"]["use"]["v"]){?>
		<input type=radio name=settlekind value="v" onclick="input_escrow(this,'N')"> �������
<?php }?>
<?php if($GLOBALS["set"]["use"]["h"]){?>
		<input type=radio name=settlekind value="h" onclick="input_escrow(this,'N')"> �ڵ���
<?php }?>
<?php if($GLOBALS["set"]["use"]["p"]){?>
		<input type=radio name=settlekind value="p" onclick="input_escrow(this,'N')"> ����Ʈ
<?php }?>
<?php if($GLOBALS["set"]["use"]["u"]){?>
		<input type=radio name=settlekind value="u" onclick="input_escrow(this,'N')"> CUP (�߱�ī��)
<?php }?>
<?php if($GLOBALS["set"]["use"]["y"]){?>
		<input type=radio name=settlekind value="y" onclick="input_escrow(this,'N')"> ��������
<?php }?>
		</td>
	</tr>

<?php if($GLOBALS["escrow"]["use"]=="Y"&&$TPL_VAR["cart"]->totalprice-$TPL_VAR["cart"]->dcprice>=$GLOBALS["escrow"]["min"]){?>
	<tr>
		<td>����ũ�ΰ���</td>
		<td class=noline>
<?php if($GLOBALS["escrow"]["c"]){?>
		<input type=radio name=settlekind value="c" onclick="input_escrow(this,'Y')"> �ſ�ī��
<?php }?>
<?php if($GLOBALS["escrow"]["o"]){?>
		<input type=radio name=settlekind value="o" onclick="input_escrow(this,'Y')"> ������ü
<?php }?>
<?php if($GLOBALS["escrow"]["v"]){?>
		<input type=radio name=settlekind value="v" onclick="input_escrow(this,'Y')"> �������
<?php }?>
		</td>
	</tr>
<?php }?>

<?php if($TPL_VAR["useIpayPg"]===true){?>
	<tr>
		<td>���� iPay</td>
		<td class="noline">
			<input type=radio name=settlekind value="i" onclick="input_escrow(this,'Y')"> �������� ��������
			<div class="small" style="padding-left:25px;">
				- ���������� iPay ����â���� �����մϴ�.<br>
				- iPay ����â���� ��ǰ������ ���θ� ���������� ����� �����Դϴ�.
			</div>
		</td>
	</tr>
<?php }?>

<?php if($GLOBALS["set"]["use"]["a"]){?>
	<tr>
		<th></th>
		<td class="small red">(�������Ա��� ��� �Ա�Ȯ�� �ĺ��� ��۴ܰ谡 ����˴ϴ�)</td>
	</tr>
<?php }?>
	<tr>
		<th></th>
		<td class="small red"><div id="coupon_typinfo" style="display:none">�������Աݿ����� ��밡���� ������ �����Ͽ����ϴ�. <br>�ٸ� ���� ������ �����Ͻ÷��� ������ ���� �Ͽ� �ֽʽÿ�.</div></td>
	</tr>
	</table>

<?php if($GLOBALS["egg"]["use"]=="Y"&&($GLOBALS["egg"]["scope"]=="A"||($GLOBALS["egg"]["scope"]=="P"&&$TPL_VAR["cart"]->totalprice-$TPL_VAR["cart"]->dcprice>=$GLOBALS["egg"]["min"]))){?>
	<table id="egg" style="display:none; margin-top:10px;">
	<col width=100>
	<tr>
		<td valign=top style="padding-top:4px">���ں�������</td>
		<td>
<?php if($GLOBALS["egg"]["scope"]=="P"){?>
		<div>���� �� �����ŷ�(�Ÿź�ȣ) ��������� ���� �����Ͻ� �� �ֽ��ϴ�.</div>
<?php }?>

		<div style="color:#FF6C68; font-weight:bold; margin-bottom:5px;">�Ʒ��� ���ǻ����� �� Ȯ���ϼ���!</div>

<?php if($GLOBALS["egg"]["scope"]=="P"){?>
		<div class=noline>&#149; ���ں������� �߱޿��� : <input type=radio name=eggIssue value="Y" onclick="egg_required()"> �߱� <input type=radio name=eggIssue value="N" onclick="egg_required()"> �̹߱�</div>
<?php }?>

		<div>&#149; <font color=444444>���ں������� �ȳ� (100% �Ÿź�ȣ ��������)<br>
		��ǰ��ݰ����� �������� ���غ�ȣ�� ���� '(��)���ﺸ������'�� �������������� �߱޵˴ϴ�. ������ �߱޵Ǵ� ���� �ǹ̴�,
		��ǰ��� �����ÿ� �Һ��ڿ��� ���ﺸ�������� ���θ��������� ���ü�Ἥ�� ���ͳݻ����� �ڵ� �߱��Ͽ�,
		���ع߻��� ���θ������������ν� �Ϻ��ϰ� ��ȣ���� �� �ֽ��ϴ�.<br>
		����, <span class='red'>�Է��Ͻ� ���������� ���ǹ߱��� ���� ������ ���Ǹ� �ٸ� �뵵�δ� ������ �ʽ��ϴ�.</span>
		</font></div>

<?php if($GLOBALS["egg"]["feepayer"]!="B"){?>
		<div>&#149; <font color=444444>�������� �������� ���Ž� ������ �����ᰡ �ΰ����� �ʽ��ϴ�.</font></div>
<?php }elseif($GLOBALS["egg"]["feepayer"]=="B"&&$GLOBALS["egg"]["feerate"]> 0){?>
		<div>&#149; <font color=444444>�������� �������� ���Ž� <span style="color:#FF6C68; font-weight:bold;">������������ �߱޼������ �����ڲ��� �δ�</span>�Ͻð� �˴ϴ�.<br>
		�������� �߱޼�����(�� �����ݾ��� <?php echo $GLOBALS["egg"]["feerate"]?>%) : <span id=infor_eggFee></span></font>
		</div>
		<input type=hidden name=eggFee>
		<input type=hidden name=eggFeeRateYn value=Y>
<?php }?>

		<div style="padding-top:10px;">&#149; ������� :
			<input type="text"t name="eggBirthYear" size=4 maxlength=4 />��
			<select name="eggBirthMon">
			<option value="">����</option>
			<? for ($i = 1; $i <= 12; $i++ ){ echo '<option value="'.$i.'">'.$i.'</option>'; } ?>
			</select>��
			<select name="eggBirthDay">
			<option value="">����</option>
			<? for ($i = 1; $i <= 31; $i++ ){ echo '<option value="'.$i.'">'.$i.'</option>'; } ?>
			</select>��
		</div>
		<div class=noline>&#149; ���� : <input type=radio name=eggSex value="1"> ���� <input type=radio name=eggSex value="2"> ����</div>
		<div style="text-align:center;" class=noline><input type=checkbox name=eggAgree value="Y"> �������� �̿뿡 �����մϴ�</div>
		</td>
	</tr>
	</table>
<?php }?>

<?php if($GLOBALS["pg"]["receipt"]=='Y'&&$GLOBALS["set"]["receipt"]["order"]=='Y'){?>
	<?php echo $this->define('tpl_include_file_3',"proc/_cashreceiptOrder.htm")?> <?php $this->print_("tpl_include_file_3",$TPL_SCP,1);?>

<?php }?>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>

<div style="padding:20px" align=center class="noline">
<input type="image" src="/shop/data/skin/campingyo/img/common/btn_payment.gif">
<img src="/shop/data/skin/campingyo/img/common/btn_cancel.gif" onclick="history.back()" style="cursor:pointer">
</div>

</form>

</div><!-- End indiv -->
<div id=dynamic></div>

<script>
var emoney_max = <?php echo $GLOBALS["emoney_max"]?>;
function chkForm2(fm)
{
	if (typeof(fm.settlekind)=="undefined"){
		alert("���������� Ȱ��ȭ�� �� �Ǿ����ϴ�. �����ڿ��� �����ϼ���.");
		return false;
	}

	var obj = document.getElementsByName('settlekind');
	if (obj[0].getAttribute('required') == undefined){
		obj[0].setAttribute('required', '');
		obj[0].setAttribute('label', '��������');
	}

	var obj = document.getElementsByName('save_mode');
	if (obj.length > 0 && obj[0].getAttribute('required') == undefined){
		obj[0].setAttribute('required', '');
		obj[0].setAttribute('label', '���� ��ġ');
	}

	var save_mode = "";

	for(var i=0;i<obj.length;i++){
		if(obj[i].checked){
			save_mode = obj[i].value;
		}
	}

	if( (save_mode == 'ncash' || save_mode == 'both') && document.getElementById('reqTxId<?php echo $TPL_VAR["ncash"]["api_id"]?>').value == ''){
		alert("���̹� ���ϸ��� ���� �� ���̹� ���ϸ��� ���� �� ��� ��ư�� Ŭ�����ּ���.");
		return false;
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
	if (typeof(egg_required) == 'function') egg_required();
	if (typeof(cash_required) == 'function') cash_required();
}
function popup_zipcode(form)
{
	window.open("../proc/popup_zipcode.php?form="+form,"","width=400,height=500");
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
	var limit = '<?php echo $GLOBALS["set"]["emoney"]["totallimit"]?>';

	var delivery	= uncomma(document.getElementById('paper_delivery').innerHTML);
	var goodsprice = uncomma(document.getElementById('paper_goodsprice').innerHTML);
<?php if($GLOBALS["set"]["emoney"]["emoney_use_range"]){?>
	var erangeprice = goodsprice + delivery;
<?php }else{?>
	var erangeprice = goodsprice;
<?php }?>
	var max_base = erangeprice - uncomma(_ID('memberdc').innerHTML) - uncomma(document.getElementsByName('coupon')[0].value);
	var coupon = coupon_emoney = 0;
	if( form.coupon ){
		 coupon = uncomma(form.coupon.value);
	}
	if( form.coupon_emoney ){
		 coupon_emoney = uncomma(form.coupon_emoney.value);
	}
	max = getDcprice(max_base,max,<?php echo $GLOBALS["set"]["emoney"]["base"]?>);
	min = parseInt(min);

	if (max > max_base)  max = max_base;
	if( _ID('print_emoney_max') && _ID('print_emoney_max').innerHTML != comma(max)  )_ID('print_emoney_max').innerHTML = comma(max);

	var emoney = uncomma(obj.value);
	if (emoney>my_emoney) emoney = my_emoney;

	// �ߺ� ��� üũ
	var dup = <?php if($GLOBALS["set"]["emoney"]["useduplicate"]=='1'){?>true<?php }else{?>false<?php }?>;
	if (my_emoney > 0 && emoney > 0 && (parseInt(coupon) > 0 || parseInt(coupon_emoney)) > 0 && !dup) {
		alert('�����ݰ� ���� ����� �ߺ�������� �ʽ��ϴ�.');
		emoney = 0;
	}
	if(my_emoney > 0 && emoney > 0 && limit > goodsprice){
		alert("��ǰ �ֹ� �հ���� "+ comma(limit) + "�� �̻� �� ��� ����Ͻ� �� �ֽ��ϴ�.");
		emoney = 0;
	}
	if(my_emoney > 0 && emoney > 0 && my_emoney < hold){
		alert("������������ "+ comma(hold) + "�� �̻� �� ��쿡�� ����Ͻ� �� �ֽ��ϴ�.");
		emoney = 0;
	}
	if (min && emoney > 0 && emoney < min){
		alert("�������� " + comma(min) + "�� ���� " + comma(max) + "�� ������ ����� �����մϴ�");
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
		if (coupon >= (goodsprice + delivery - dc)) coupon = goodsprice + delivery - dc;
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

<?php if($TPL_VAR["view_aboutdc"]){?>
	settlement = settlement - <?php echo $TPL_VAR["about_coupon"]?>;
<?php }?>

<?php if($TPL_VAR["ncash"]["useyn"]=='Y'){?>
	if (document.getElementById('totalUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>')) settlement = settlement - document.getElementById('totalUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>').value;
<?php }?>

	settlement += calcu_eggFee(settlement); // ���ں������� �߱޼����� ���
	document.getElementById('paper_settlement').innerHTML = comma(settlement);
}
function egg_required()
{
	egg_display();
	calcu_settle();
}
function calcu_eggFee(settlement)
{
	egg_display(settlement);
	var eggFee = 0;
	if (typeof(document.getElementsByName('eggFee')[0]) != "undefined"){
		var feerate = (<?php echo $GLOBALS["egg"]["feerate"]?> / 100);
		if (document.getElementsByName('eggFee')[0].disabled == false) eggFee = parseInt(settlement * feerate);
		document.getElementsByName('eggFee')[0].value = eggFee;
	}
	if (_ID('paper_eggFee') != null) _ID('paper_eggFee').innerHTML = comma(eggFee);
	if (_ID('infor_eggFee') != null){
		_ID('infor_eggFee').innerHTML = '<b>' + comma(eggFee) + '</b> ��';
		if (eggFee) _ID('infor_eggFee').innerHTML += ' (�� �����ݾ׿� ���ԵǾ����ϴ�.)';
	}
	return eggFee;
}
function egg_display(settlement)
{
	var min = parseInt('<?php echo $GLOBALS["egg"]["min"]?>');
	var display = 'block';
	if (_ID('egg') == null) return;
	if (typeof(settlement) != "undefined"){
		if (settlement < min && typeof(document.getElementsByName('eggIssue')[0]) != "undefined") display = 'none';
		else if (settlement <= 0) display = 'none';
		else if (_ID('egg').style.display != 'none') return;
	}
	if (display != 'none'){
		var obj = document.getElementsByName('settlekind');
		for (var i = 0; i < obj.length; i++){
			if (obj[i].checked != true) continue;
			var settlekind = obj[i];
			break;
		}
		if (settlekind == null) display = 'none';
		else if (settlekind.value == 'h') display = 'none';
		else if (document.getElementsByName('escrow')[0].value == 'Y') display = 'none';
		else if (typeof(document.getElementsByName('eggIssue')[0]) != "undefined"){
			if (settlekind.value != 'a') display = 'none';
			else if(typeof(settlement) == "undefined"){
				settlement = uncomma(_ID('paper_settlement').innerHTML);
				if (typeof(document.getElementsByName('eggFee')[0]) != "undefined") settlement -= document.getElementsByName('eggFee')[0].value;
				if (settlement < min) display = 'none';
			}
		}
	}
	if (_ID('egg').style.display == display && display =='none') return;
	_ID('egg').style.display = display;

	items = new Array();
	items.push( {name : "eggIssue", label : "���ں������� �߱޿���", msgR : ""} );
	items.push( {name : "eggBirthYear", label : "�������(��)", msgR : "���ں��������� �߱޹����÷���, �������(��)�� �Է��ϼž� ������ �����մϴ�."} );
	items.push( {name : "eggBirthMon", label : "�������(��)", msgR : "���ں��������� �߱޹����÷���, �������(��)�� �Է��ϼž� ������ �����մϴ�."} );
	items.push( {name : "eggBirthDay", label : "�������(��)", msgR : "���ں��������� �߱޹����÷���, �������(��)�� �Է��ϼž� ������ �����մϴ�."} );
	items.push( {name : "eggSex", label : "����", msgR : "���ں��������� �߱޹����÷���, ������ �Է��ϼž� ������ �����մϴ�."} );
	items.push( {name : "eggAgree", label : "�������� �̿뵿��", msgR : "���ں��������� �߱޹����÷���, �������� �̿뵿�ǰ� �ʿ��մϴ�."} );
	items.push( {name : "eggFee", label : "�߱޼�����", msgR : ""} );
	for (var i = 0; i < items.length; i++){
		var obj = document.getElementsByName(items[i].name);
		if (display == 'block' && items[i].name != 'eggIssue' && typeof(document.getElementsByName('eggIssue')[0]) != "undefined")
			state = (document.getElementsByName('eggIssue')[0].checked ? 'block' : 'none');
		else state = display;
		for (var j = 0; j < obj.length; j++){
			if (state == 'block'){
				obj[j].setAttribute('required', '');
				obj[j].setAttribute('label', items[i].label);
				obj[j].setAttribute('msgR', items[i].msgR);
				obj[j].disabled = false;
			}
			else {
				obj[j].removeAttribute('required');
				obj[j].removeAttribute('label');
				obj[j].removeAttribute('msgR');
				obj[j].disabled = true;
			}
		}
	}
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

	gd_ajax({
		url : '../proc/getdelivery.php',
		type : 'get',
		param : "zipcode="+zipcode+"&deliPoli="+deliPoli+"&coupon="+coupon+"&emoney="+emoney+"&mode="+mode,
		success : function(data) {
			eval(data);
		}
	});
}

function del_coupon(){
	var apply_coupon = document.getElementById('apply_coupon');
	apply_coupon.innerHTML = '';

	document.frmOrder.coupon.value = '0';
	document.frmOrder.coupon_emoney.value = '0';
	chk_emoney(document.frmOrder.emoney);
	getDelivery();
	calcu_settle();

	var settlekind = document.getElementsByName('settlekind');
	for(var j=0;j<settlekind.length;j++){
		settlekind[j].disabled = false;
	}

	var coupon_typinfo = document.getElementById('coupon_typinfo');
	coupon_typinfo.style.display = "none";
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
}

function cash_save_use(){
	var
	goodsprice = uncomma(document.getElementById('paper_goodsprice')[document.getElementById('paper_goodsprice').innerHTML?"innerHTML":"textContent"]),
	delivery_price = uncomma(document.getElementById('paper_delivery')[document.getElementById('paper_delivery').innerHTML?"innerHTML":"textContent"]),
	member_dc = _ID('memberdc') ? uncomma(_ID('memberdc').innerHTML) : 0,
	coupon = document.frmOrder.coupon ? uncomma(document.frmOrder.coupon.value) : 0,
	emoney = document.frmOrder.emoney ? uncomma(document.frmOrder.emoney.value) : 0,
	exception_price = uncomma(document.getElementById('exception_price').value),
	max_ncash_use = (goodsprice + delivery_price) - exception_price - member_dc - coupon - emoney;
	var r_save_mode = document.getElementsByName('save_mode');
	var save_mode = '';
	for( var i = 0 ; i < r_save_mode.length; i++ ){
		if(r_save_mode[i].checked){
			save_mode = r_save_mode[i].value;
		}
	}
	if(save_mode != 'ncash' && save_mode != 'both'){ alert('���̹� ���ϸ��� ������ �������ּ���.'); return; }
	var reqTxId = document.getElementById('reqTxId<?php echo $TPL_VAR["ncash"]["api_id"]?>').value;
	window.open('../proc/naverNcash_use.php?reqTxId='+reqTxId+"&maxUseAmount="+max_ncash_use,'cashPopup<?php echo $TPL_VAR["ncash"]["api_id"]?>','width=496,height=434,status=no,resizeable=no');
}
function mileage_info() {
	window.open("http://static.mileage.naver.net/static/20130708/ext/intro.html", "mileageIntroPopup", "width=404, height=412, status=no, resizable=no");
}
function naver_mileage_initialize(radiobox)
{
	if(document.getElementById('reqTxId<?php echo $TPL_VAR["ncash"]["api_id"]?>').value.trim().length)
	{
		if(radiobox.checked && confirm("���θ� ���������� �����Ͻø� ���̹� ���ϸ�����\r\n��� �� ������ ��ҵ˴ϴ�.\r\n����Ͻðڽ��ϱ�?"))
		{
			document.getElementById('reqTxId<?php echo $TPL_VAR["ncash"]["api_id"]?>').value = "";
			document.getElementById('mileageUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>').value = "";
			document.getElementById('cashUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>').value = "";
			document.getElementById('totalUseAmount<?php echo $TPL_VAR["ncash"]["api_id"]?>').value = "";
			document.getElementById('baseAccumRate').value = "";
			document.getElementById('addAccumRate').value = "";
			document.getElementById('ncash_view').style.display = "none";
			document.getElementById('save_button').style.display = "block";
			calcu_settle();
		}
		else
		{
			var save_mode = document.getElementsByName('save_mode');
			for(var i=0; i<save_mode.length; i++)
			{
				if(/^(ncash|both)$/.test(save_mode[i].value)) save_mode[i].checked = true;
			}
		}
	}
}
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>