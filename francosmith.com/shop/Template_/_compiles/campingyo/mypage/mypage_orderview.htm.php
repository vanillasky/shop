<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/mypage_orderview.htm 000032081 */  $this->include_("displayEggBanner");
if (is_array($TPL_VAR["item"])) $TPL_item_1=count($TPL_VAR["item"]); else if (is_object($TPL_VAR["item"]) && in_array("Countable", class_implements($TPL_VAR["item"]))) $TPL_item_1=$TPL_VAR["item"]->count();else $TPL_item_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<style>
#orderbox {border:5px solid #F3F3F3; padding:5px 10px;}
#orderbox table th {width:100;}
</style>
<script id="delivery"></script>

<!-- ����̹��� || ������ġ -->
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td><img src="/shop/data/skin/campingyo/img/common/title_orderdetail.gif" border=0></td></tr>
<tr><td class="path">home > ���������� > <b>�ֹ������󼼺���</b></td></tr>
</table><p>

<div class="indiv"><!-- Start indiv -->

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=2 bgcolor="#303030" colspan=10></td></tr>
<tr bgcolor=#F0F0F0 height=23>
	<th colspan=2 class="input_txt">��ǰ����</th>
	<th class="input_txt">�ǸŰ�</th>
	<th class="input_txt">����</th>
	<th class="input_txt">��ۻ���</th>
	<th class="input_txt">�������<br/>/�̿��ı�</th>
</tr>
<tr><td height=1 bgcolor="#D6D6D6" colspan=10></td></tr>
<?php if($TPL_item_1){foreach($TPL_VAR["item"] as $TPL_V1){?>
<tr>
	<td align=center width=60 height=60><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"], 50)?></a></td>
	<td>
	<a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["goodsnm"]?>

<?php if($TPL_V1["opt1"]){?>[<?php echo $TPL_V1["opt1"]?><?php if($TPL_V1["opt2"]){?>/<?php echo $TPL_V1["opt2"]?><?php }?>]<?php }?>
<?php if($TPL_V1["addopt"]){?><div>[<?php echo str_replace("^","] [",$TPL_V1["addopt"])?>]</div><?php }?></a>
	</td>
	<td align=center><?php echo number_format($TPL_V1["price"])?>��</td>
	<td align=center><?php echo number_format($TPL_V1["ea"])?>��</td>
	<td align=center class=stxt><FONT COLOR="#007FC8"><?php echo $GLOBALS["r_istep"][$TPL_V1["istep"]]?></FONT></td>
	<td align=center>
<?php if($GLOBALS["set"]["delivery"]["basis"]&&$TPL_V1["dvcode"]){?>
	<a href="javascript:popup('mypage_delivery.php?item_sno=<?php echo $TPL_V1["sno"]?>',600,600)"><img src="/shop/data/skin/campingyo/img/common/btn_chase.gif"></a>
<?php }elseif(!$GLOBALS["set"]["delivery"]["basis"]&&$TPL_VAR["deliverycode"]){?>
	<a href="javascript:popup('mypage_delivery.php?ordno=<?php echo $TPL_VAR["ordno"]?>',600,600)"><img src="/shop/data/skin/campingyo/img/common/btn_chase.gif"></a>
<?php }?>
<?php if($TPL_V1["istep"]== 4){?><a href="javascript:;" onclick="popup_register( 'add_review', '<?php echo $TPL_V1["goodsno"]?>' )"><img src="/shop/data/skin/campingyo/img/common/btn_review.gif"></a><?php }?>
	</td>
</tr>
<tr><td colspan=10 height=1 bgcolor=#DEDEDE></td></tr>
<?php }}?>
</table><p>

<img src="/shop/data/skin/campingyo/img/common/order_txt_01.gif" border=0>
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
		<td><?php echo $TPL_VAR["phoneOrder"]?></td>
	</tr>
	<tr>
		<td>�ֹ��� �ڵ���</td>
		<td><?php echo $TPL_VAR["mobileOrder"]?></td>
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
<?php if($TPL_VAR["step"]<= 1){?>

	<form name="frmOrder" method="post" action="<?php echo url("mypage/indb.php")?>&" onsubmit="return chkForm(this)">
	<input type="hidden" name="mode" value="modReceiver">
	<input type="hidden" name="ordno" value="<?php echo $TPL_VAR["ordno"]?>">

<?php }?>
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
		<td><?php echo $TPL_VAR["phoneReceiver"]?></td>
	</tr>
	<tr>
		<td>�޴��� �ڵ���</td>
		<td><?php echo $TPL_VAR["mobileReceiver"]?></td>
	</tr>
	<tr>
		<td>�����ȣ</td>
		<td><?php echo $TPL_VAR["zipcode"]?></td>
	</tr>
	<tr>
		<td>�ּ�</td>
		<td><?php echo $TPL_VAR["address"]?> <?php echo $TPL_VAR["address_sub"]?><div style="padding-top:5px;font:12px dotum;color:#999;"><?php echo $TPL_VAR["road_address"]?> <?php echo $TPL_VAR["address_sub"]?></div></td>
	</tr>
<?php if($TPL_VAR["memo"]){?>
	<tr>
		<td>��۸޼���</td>
		<td><?php echo $TPL_VAR["memo"]?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["deliverycode"]){?>
	<tr>
		<td>�����ȣ</td>
		<td><?php echo $TPL_VAR["deliverycomp"]?> <?php echo $TPL_VAR["deliverycode"]?></td>
	</tr>
<?php }?>
	</table>

	</td>
</tr>
</table>

<div style="font-size:0;height:5px"></div>

<!-- 03 �����ݾ� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_03.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100><col align="right">
	<tr>
		<td>���ֹ��ݾ�</td>
		<td><span id="paper_goodsprice"><?php echo number_format($TPL_VAR["goodsprice"])?></span>��</td>
	</tr>
	<tr>
		<td>��ۺ�</td>
		<td><div id="paper_delivery_msg1" <?php if($TPL_VAR["deli_msg"]){?>style="display:none"<?php }?>><span id="paper_delivery"><?php echo number_format($TPL_VAR["delivery"])?></span>��</div>
		<div id="paper_delivery_msg2" style="float:left;margin:0;" <?php if(!$TPL_VAR["deli_msg"]){?>style="display:none"<?php }?>><?php echo $TPL_VAR["deli_msg"]?></div></td>
	</tr>
<?php if($TPL_VAR["item"][ 0]['todaygoods']!='y'){?>
	<tr>
		<td>ȸ������</td>
		<td>- <span id="paper_memberdc"><?php echo number_format($TPL_VAR["memberdc"])?></span>��</td>
	</tr>
	<tr>
		<td>��������</td>
		<td>- <span id="paper_coupon"><?php echo number_format($TPL_VAR["coupon"])?></span>��</td>
	</tr>
	<tr>
		<td>������ ���</td>
		<td>- <span id="paper_emoney"><?php echo number_format($GLOBALS["data"]["emoney"])?></span>��</td>
	</tr>
<?php if($GLOBALS["data"]["ncash_emoney"]){?>
	<tr>
		<td>���̹����ϸ���</td>
		<td>- <span id="paper_emoney"><?php echo number_format($GLOBALS["data"]["ncash_emoney"])?></span>��</td>
	</tr>
<?php }?>
<?php if($GLOBALS["data"]["ncash_cash"]){?>
	<tr>
		<td>���̹�ĳ��</td>
		<td>- <span id="paper_emoney"><?php echo number_format($GLOBALS["data"]["ncash_cash"])?></span>��</td>
	</tr>
<?php }?>
<?php }?>
<?php if($TPL_VAR["eggFee"]){?>
	<tr>
		<td>�������� ������</td>
		<td><span id="paper_eggfee"><?php echo number_format($TPL_VAR["eggFee"])?></span>��</td>
	</tr>
<?php }?>
	<tr>
		<td>�����ݾ�</td>
		<td><b><span id="paper_settlement"><?php echo number_format($TPL_VAR["settleprice"])?></span>��</b></td>
	</tr>
<?php if($TPL_VAR["canceled_price"]){?>
	<tr>
		<td>��ұݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["canceled_price"])?>��</b></td>
	</tr>
<?php }?>
	</table>
	<div style="color: #007fc8; font-size: 11px; margin: 3px 0 0 3px">�� ���̹� ���ϸ����� ���� �����õ����� ��� ���ϸ��� ������ ������� �ʾ� ������ÿ�<br/>�����ݾ��� �ٸ��� ���� �� �ֽ��ϴ�.</div>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php if($TPL_VAR["step2"]== 50||$TPL_VAR["step2"]== 54){?>
<input type="hidden" name="settlekind" value="<?php echo $TPL_VAR["settlekind"]?>">
<input type="hidden" name="escrowyn" value="<?php echo $TPL_VAR["escrowyn"]?>">
<!-- ���ž���ǥ�� start --><?php echo displayEggBanner( 1)?><!-- ���ž���ǥ�� end -->
<?php }?>
<!-- 04 �������� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"><img src="/shop/data/skin/campingyo/img/common/order_step_04.gif"></td>
	<td id="orderbox">

	<table>
	<col width=100>
<?php if($TPL_VAR["settlekind"]=="a"){?>
	<tr>
		<td>�Ա�����</td>
		<td><?php echo $TPL_VAR["bank"]?></td>
	</tr>
	<tr>
		<td>�Աݰ���</td>
		<td><?php echo $TPL_VAR["account"]?></td>
	</tr>
	<tr>
		<td>�����ָ�</td>
		<td><?php echo $TPL_VAR["name"]?></td>
	</tr>
	<tr>
		<td>�Ա��ڸ�</td>
		<td><?php echo $TPL_VAR["bankSender"]?></td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<td>�������</td>
		<td>�ſ�ī��</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<td>�������</td>
		<td>������ü</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
	<tr>
		<td>�������</td>
		<td><?php echo $TPL_VAR["vAccount"]?></td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="h"){?>
	<tr>
		<td>�������</td>
		<td>�ڵ���</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="p"){?>
	<tr>
		<td>�������</td>
		<td>����Ʈ����</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="d"){?>
	<tr>
		<td>�������</td>
		<td>�������� ���� (������ ���)</td>
	</tr>

<?php }elseif($TPL_VAR["settlekind"]=="u"){?>
	<tr>
		<td>�������</td>
		<td>CUP (�߱� ���࿬�� ī��)</td>
	</tr>

<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<td>ȸ������</td>
		<td id="memberdc"><?php echo number_format($TPL_VAR["memberdc"])?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr>
		<td>��������</td>
		<td><?php echo number_format($TPL_VAR["coupon"])?>��</td>
	</tr>
<?php }?>
	<tr>
		<td>�����ݰ���</td>
		<td><b><?php echo number_format($TPL_VAR["emoney"])?>��</b></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]== 54&&in_array($TPL_VAR["settlekind"],array('c','o','v'))&&$TPL_VAR["pgfailreason"]){?><!-- �������л��� -->
	<tr>
		<td>�������л���</td>
		<td><?php echo $TPL_VAR["pgfailreason"]?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["eggyn"]=='y'){?>
	<tr>
		<td>���ں�������</td>
		<td><a href="javascript:popupEgg('<?php echo $GLOBALS["egg"]["usafeid"]?>', '<?php echo $TPL_VAR["ordno"]?>')"><font color=#0074BA><b><u><?php echo $TPL_VAR["eggno"]?> <font class=small>(���������)</a></td>
	</tr>
<?php }elseif($GLOBALS["egg"]["use"]=='N'&&$TPL_VAR["eggyn"]=='f'){?>
	<tr>
		<td>���ں�������</td>
		<td>������ �߱� ����.</td>
	</tr>
<?php }elseif($GLOBALS["egg"]["use"]=='Y'&&$TPL_VAR["eggyn"]=='f'){?>
	<tr>
		<td>���ں�������</td>
		<td>������ �߱� ����. ��߱� ��������.</td>
	</tr>
<?php }?>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php if($TPL_VAR["step2"]== 50||$TPL_VAR["step2"]== 54){?>
<?php if($GLOBALS["egg"]["use"]=="Y"&&($GLOBALS["egg"]["scope"]=="A"||($GLOBALS["egg"]["scope"]=="P"&&$TPL_VAR["settleprice"]>=$GLOBALS["egg"]["min"]))){?>
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
<?php }elseif($GLOBALS["egg"]["feepayer"]=="B"){?>
	<div>&#149; <font color=444444>�������� �������� ���Ž� <span style="color:#FF6C68; font-weight:bold;">������������ �߱޼������ �����ڲ��� �δ�</span>�Ͻð� �˴ϴ�.<br>
	�������� �߱޼�����(�� �����ݾ��� 0.5%) : <span id=infor_eggFee></span></font>
	</div>
	<input type=hidden name=eggFee>
<?php }?>

	<div style="padding-top:10px;">�ֹε�Ϲ�ȣ :
	<input type=text name=resno[] maxlength=6 onkeyup="if (this.value.length==6) this.nextSibling.nextSibling.focus()" onkeydown="onlynumber()" style="width:80px"> -
	<input type=password name=resno[] maxlength=7 onkeydown="onlynumber()" style="width:90px">
	</div>
	<div style="text-align:center;" class=noline><input type=checkbox name=eggAgree value="Y"> �������� �̿뿡 �����մϴ�</div>
	</td>
</tr>
</table>
<?php }?>
</form>
<?php }else{?>
</form>
<?php if($TPL_VAR["step"]> 0&&$GLOBALS["egg"]["use"]=='Y'&&$TPL_VAR["eggyn"]=='f'){?>
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:13px"><b>���ں������� ��߱�</b></td>
	<td id="orderbox">
		<form id=form name=frmTax method=post action="<?php echo url("mypage/indb.php")?>&" onsubmit="return chkForm(this)">
		<input type=hidden name=mode value="eggcreate">
		<input type=hidden name=ordno value="<?php echo $TPL_VAR["ordno"]?>">
		<div>&#149; ���ں������� �ȳ� (100% �Ÿź�ȣ ��������)<br>
		��ǰ��ݰ����� �������� ���غ�ȣ�� ���� '(��)���ﺸ������'�� �������������� �߱޵˴ϴ�.<br>
		������ �߱޵Ǵ� ���� �ǹ̴�, ��ǰ��� �����ÿ� �Һ��ڿ��� ���ﺸ�������� ���θ��������� ���ü�Ἥ�� ���ͳݻ����� �ڵ� �߱��Ͽ�, ���ع߻��� ���θ������������ν� �Ϻ��ϰ� ��ȣ���� �� �ֽ��ϴ�.<br>
		����, <span class='red'>�Է��Ͻ� ���������� ���ǹ߱��� ���� �ʿ��� �����̸� �ٸ� �뵵�� ������ �ʽ��ϴ�.</span><br>
		(���ں������� �߻��� ������ �����ᰡ �ΰ����� �ʽ��ϴ�.)
		</div>
		<div style="text-align:center; margin-top:10px;">�ֹε�Ϲ�ȣ :
		<input type=text name=resno[] maxlength=6 onkeyup="if (this.value.length==6) this.nextSibling.nextSibling.focus()" onkeydown="onlynumber()" style="width:80px" required label="�ֹε�Ϲ�ȣ" msgR="���ں��������� �߱޹����÷���, �ֹι�ȣ�� �Է��ϼž� ������ �����մϴ�."> -
		<input type=password name=resno[] maxlength=7 onkeydown="onlynumber()" style="width:90px" required label="�ֹε�Ϲ�ȣ" msgR="���ں��������� �߱޹����÷���, �ֹι�ȣ�� �Է��ϼž� ������ �����մϴ�.">
		</div>
		<div style="text-align:center;" class=noline><input type=checkbox name=eggAgree value="Y" required label="�������� �̿뵿��" msgR="���ں��������� �߱޹����÷���, �������� �̿뵿�ǰ� �ʿ��մϴ�."> �������� �̿뿡 �����մϴ�</div>
		<a href="javascript:order_print('<?php echo $TPL_VAR["ordno"]?>');"><strong><FONT COLOR="EA0095">[���ݰ�꼭 �μ�]</font></strong></a></div>
		</form>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>
<?php if($TPL_VAR["taxmode"]!=''&&!$TPL_VAR["cashreceipt"]){?>
<!-- 05 ���ݰ�꼭 -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:13px"><b>���ݰ�꼭 �����û</b></td>
	<td id="orderbox">
<?php if($TPL_VAR["taxmode"]=='taxview'){?>
		<!-- ���ݰ�꼭 �������� : start -->
		<div style="margin-top:5px; margin-bottom:3px; line-height:14pt;">
<?php if($TPL_VAR["taxed"]['step']== 0){?>
			<FONT COLOR="EA0095"><b>�����û</b> (����ó���� �μ��Ͻ� �� �ֽ��ϴ�)<br>
			<font color=444444>(�Ʒ� ���������� �ִ� ��� 1:1���� �Ǵ� ��ȭ�� ��û)</font></font><br>��û�� : <?php echo $TPL_VAR["taxed"]['regdt']?><br>
<?php }elseif($TPL_VAR["taxed"]['step']== 1){?>
			<FONT COLOR="EA0095"><b>�������</b> (�μ������ �����մϴ�)</font><br>����� : <b><?php echo number_format($TPL_VAR["taxed"]['price'])?></b>��, ������ : <b><?php echo $TPL_VAR["taxed"]['agreedt']?></b><br>
<?php }elseif($TPL_VAR["taxed"]['step']== 2){?>
			<FONT COLOR="EA0095"><b>����Ϸ�</b> (�μ������ �Ϸ�Ǿ����ϴ�)</font><br>����� : <b><?php echo number_format($TPL_VAR["taxed"]['price'])?></b>��, �Ϸ��� : <b><?php echo $TPL_VAR["taxed"]['printdt']?></b><br>
<?php }elseif($TPL_VAR["taxed"]['step']== 3){?>
			<div id="taxstep3"><FONT COLOR="EA0095"><b>���ڹ���</b></font></div>����� : <b><?php echo number_format($TPL_VAR["taxed"]['price'])?></b>��, ��û�� : <b><?php echo $TPL_VAR["taxed"]['agreedt']?></b><br>
<?php }?>

		����ڹ�ȣ : <?php echo $TPL_VAR["taxed"]['busino']?>&nbsp;&nbsp;
		ȸ��� : <?php echo $TPL_VAR["taxed"]['company']?><br>
		��ǥ�ڸ� : <?php echo $TPL_VAR["taxed"]['name']?>&nbsp;&nbsp;
		���� : <?php echo $TPL_VAR["taxed"]['service']?>&nbsp;&nbsp;
		���� : <?php echo $TPL_VAR["taxed"]['item']?><br>
		������ּ� : <?php echo $TPL_VAR["taxed"]['address']?>

		</div>

<?php if($TPL_VAR["taxed"]['step']== 1||$TPL_VAR["taxed"]['step']== 2||$TPL_VAR["taxed"]['step']== 3){?>
		<div id="taxprint" style="margin-top:8px; text-align:center;">
		<a href="javascript:order_print('<?php echo $TPL_VAR["ordno"]?>');"><strong><FONT COLOR="EA0095">[���ݰ�꼭 �μ�]</font></strong></a></div>
		<div style="padding-top:5px"></div>
<?php }?>
		<!-- ���ݰ�꼭 �������� : end -->
<?php }?>

		<!-- ���ݰ�꼭 ��û�� : start -->
		<div id="taxapply" style="display:none;">
		<form id=form name=frmTax method=post action="<?php echo url("mypage/indb.php")?>&" onsubmit="return chkForm(this)">
		<input type=hidden name=mode value="taxapp">
		<input type=hidden name=ordno value="<?php echo $TPL_VAR["ordno"]?>">
		<div>
		����ڹ�ȣ : <input type=text name="busino" value="<?php echo $TPL_VAR["taxed"]['busino']?>" class=line required  option="regNum" label="����ڹ�ȣ" size=10 maxlength=30> <font class=small1 color=444444>(���ڸ�����)</font><br>
		<font color=white>���</font>ȸ��� : <input type=text name="company" value="<?php echo $TPL_VAR["taxed"]['company']?>" class=line required label="ȸ���" size=10>&nbsp;&nbsp;&nbsp;
		��ǥ�ڸ� : <input type=text name="name" value="<?php echo $TPL_VAR["taxed"]['name']?>" class=line required label="��ǥ�ڸ�" size=10><br>
		<font color=white>�����</font>���� : <input type=text name="service" value="<?php echo $TPL_VAR["taxed"]['service']?>" class=line required label="����" size=10>&nbsp;&nbsp;&nbsp;
		<font color=white>���</font>���� : <input type=text name="item" value="<?php echo $TPL_VAR["taxed"]['item']?>" class=line required label="����" size=10><br>
		������ּ� : <input type=text name="address" value="<?php echo $TPL_VAR["taxed"]['address']?>" class=line required label="������ּ�" size=40>
		</div>
		<div style="text-align:center; margin-top:8px;"><input type="submit" value="[���ݰ�꼭 ��û�ϱ�]" style="border:0;background-color:#ffffff;color:#EA0095;font-weight:bold;"></div>
		</form>
		</div>
		<script>
		_ID('taxapply').style.display = "<?php if($TPL_VAR["taxmode"]=='taxapp'){?>block<?php }else{?>none<?php }?>"; //
		</script>
		<!-- ���ݰ�꼭 ��û�� : end -->

<?php if($TPL_VAR["taxmode"]=='taxview'&&$TPL_VAR["taxed"]['step']== 3){?>
		<script src="/shop/lib/js/prototype.js"></script>
		<script>getTaxbill("<?php echo $TPL_VAR["taxed"]['doc_number']?>", "<?php echo $TPL_VAR["taxapp"]?>");</script>
<?php }?>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<!-- 05 ���ݰ�꼭 End -->
<?php }?>

<?php if($GLOBALS["pg"]["receipt"]=="Y"&&$TPL_VAR["settlekind"]!="c"&&$TPL_VAR["settlekind"]!="h"&&$TPL_VAR["settleprice"]>= 1&&$TPL_VAR["taxmode"]!='taxview'){?><!-- ���ݿ����� �߱� -->
<?php $this->print_("cash_receipt",$TPL_SCP,1);?>

<?php }?>

<?php if($GLOBALS["ableCashbag"]){?><!-- OKĳ���� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"></td>
	<td id="orderbox">
	<div><a href="javascript:okcashbag();"><img src="/shop/data/skin/campingyo/img/common/btn_okcash.gif"></a></div>
	<div class="small">'OKĳ���������ޱ�'��ư�� Ŭ���Ͽ� ĳ���� ����Ʈ�� ���������Ǽ� �ֽ��ϴ�. ĳ���� ī���ȣ�� �Է����� �ʾҰų� �߸� �Է� �Ͻ� ������ ��� ����Ʈ ������ �Ұ��Ͽ���, �ƿ﷯ ����Ͻ� ī���ȣ�� �̵�� ī���̰ų� ������ ��쿡�� ������ �Ұ��Ͽ��� ���� �����Ͻñ� �ٶ��ϴ�. �Ϻλ�ǰ,��ۺ� �� ���� ������(��������)���� ���� �ݾ��� ����Ʈ�� �߰� �������� �ʽ��ϴ�. �������� �� ����Ʈ�� ��ۿϷ�(���Ű���) �����Դϴ�.</div>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>

<?php if($TPL_VAR["cbyn"]=='Y'&&$TPL_VAR["step"]=='4'&&$TPL_VAR["oktno"]){?><!-- OKĳ���� ���� ����-->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"></td>
	<td id="orderbox">
		<div style="padding-left:5"><b>OKĳ����</b></div>
		<div style="font:0;height:5"></div>
		<table>
		<col width=100>
		<tr>
			<td>�ŷ���ȣ</td>
			<td><?php echo $TPL_VAR["oktno"]?></td>
		</tr>
		<tr>
			<td>��������Ʈ</td>
			<td><?php echo number_format($TPL_VAR["add_pnt"])?></td>
		</tr>
		</table>
	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>
<?php }?>

<?php if($TPL_VAR["step"]&&!$TPL_VAR["step2"]&&in_array($TPL_VAR["settlekind"],array('c','o','v'))){?><!-- �ŷ� ������ �߱� -->
<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3"></td>
	<td id="orderbox">

	<table>
	<col width=100>
	<tr>
		<td>�ŷ�������</td>
		<td>
<?php if($TPL_VAR["pg"]=="ipay"&&$TPL_VAR["settlekind"]=="c"){?>
<?php if($TPL_item_1){foreach($TPL_VAR["item"] as $TPL_V1){?>
			<div><a href="javascript:void(0)" onclick="window.open('https://accounting.auction.co.kr/card/receiptlist.aspx?order_no=<?php echo $TPL_V1["ipay_ordno"]?>&seller_id=<?php echo $TPL_VAR["ipay"]["sellerid"]?>','','width=410,height=650')"><?php echo $TPL_V1["goodsnm"]?> [<?php echo $TPL_V1["opt1"]?>/<?php echo $TPL_V1["opt2"]?>] ���������</a></div>
<?php }}?>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="allat"||$GLOBALS["cfg"]["settlePg"]=="allatbasic"){?>
		<a href="javascript:void(0)" onClick="window.open('http://www.allatpay.com/servlet/AllatBizPop/member/pop_card_receipt.jsp?shop_id=<?php echo $GLOBALS["pg"]["id"]?>&order_no=<?php echo $TPL_VAR["ordno"]?>','','width=410,height=650')">���������</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="kcp"&&$TPL_VAR["settlekind"]=="c"){?>
		<a href="javascript:popup('http://admin.kcp.co.kr/Modules/Sale/Card/ADSA_CARD_BILL_Receipt.jsp?c_trade_no=<?php echo $TPL_VAR["tno"]?>',428,741)">���������</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="inicis"&&($TPL_VAR["cardtno"]||$TPL_VAR["escrowno"])){?>
		<a href="javascript:popup('https://iniweb.inicis.com/mall/cr/cm/mCmReceipt_head.jsp?noTid=<?php if($TPL_VAR["cardtno"]){?><?php echo $TPL_VAR["cardtno"]?><?php }else{?><?php echo $TPL_VAR["escrowno"]?><?php }?>&noMethod=1',428,741)">���������</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="inipay"&&($TPL_VAR["cardtno"]||$TPL_VAR["escrowno"])){?>
		<a href="javascript:popup('https://iniweb.inicis.com/mall/cr/cm/mCmReceipt_head.jsp?noTid=<?php if($TPL_VAR["cardtno"]){?><?php echo $TPL_VAR["cardtno"]?><?php }else{?><?php echo $TPL_VAR["escrowno"]?><?php }?>&noMethod=1',428,741)">���������</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="dacom"||$GLOBALS["cfg"]["settlePg"]=="lgdacom"){?>
		<script type="text/javascript" src="http://pgweb.dacom.net/WEB_SERVER/js/receipt_link.js"></script>
		<a href="javascript:showReceiptByTID('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["cardtno"]?>','<?php echo $TPL_VAR["authdata"]?>')">������ ���</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="agspay"&&$TPL_VAR["settlekind"]=="c"){?>
		<a href="javascript:popup('http://www.allthegate.com/customer/receiptLast3.jsp?sRetailer_id=<?php echo $GLOBALS["pg"]["id"]?>&approve=<?php echo $TPL_VAR["pgAppNo"]?>&send_no=<?php echo $TPL_VAR["cardtno"]?>&send_dt=<?php echo substr($TPL_VAR["pgAppDt"], 0, 8)?>',420,700)">���������</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="easypay"&&$TPL_VAR["settlekind"]=="c"){?>
		<a href="javascript:receipt('<?php echo $TPL_VAR["cardtno"]?>', '�ſ�ī��');">���������</a>
<?php }elseif($GLOBALS["cfg"]["settlePg"]=="settlebank"){?>
<?php if($TPL_VAR["settlekind"]=="c"){?>
				<a href="javascript:void(0)" onClick="window.open('https://pg.settlebank.co.kr/common/CommonMultiAction.do?_method=RcptView&mid=<?php echo $GLOBALS["pg"]["id"]?>&ordNo=<?php echo $TPL_VAR["ordno"]?>&svcCd=CD','','width=500,height=750')">���������</a>
<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
				<a href="javascript:void(0)" onClick="window.open('https://pg.settlebank.co.kr/common/CommonMultiAction.do?_method=RcptView&mid=<?php echo $GLOBALS["pg"]["id"]?>&ordNo=<?php echo $TPL_VAR["ordno"]?>&svcCd=BNK','','width=410,height=650')">���������</a>
<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
				<a href="javascript:void(0)" onClick="window.open('https://pg.settlebank.co.kr/common/CommonMultiAction.do?_method=RcptView&mid=<?php echo $GLOBALS["pg"]["id"]?>&ordNo=<?php echo $TPL_VAR["ordno"]?>&svcCd=VBANK','','width=410,height=650')">���������</a>
<?php }?>
<?php }?>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
<?php }?>
<?php }?>

<?php if($GLOBALS["sess"]){?>
<div align=right class=stxt style="padding:5px 0"><font size=2 COLOR="#007FC8">��</font> <FONT COLOR="#007FC8">�ֹ����/��ȯ/��ǰ�� ���Ͻø� ������������ <A HREF="<?php echo url("mypage/mypage_qna.php")?>&"><U>1:1���ǰԽ���</U></A>�� �̿��ϼ���</font></div>
<?php }?>

<div style="padding:20px" align=center id="avoidDblPay">
<a href="javascript:history.back();"><img src="/shop/data/skin/campingyo/img/common/btn_back.gif" border=0></a>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]== 0){?><a href="javascript:chkCancel();"><img src="/shop/data/skin/campingyo/img/common/btn_order_cancel.gif" border=0></a><?php }?>
<?php if($GLOBALS["resettleAble"]){?><a href="javascript:chkReSettle();"><img src="/shop/data/skin/campingyo/img/common/btn_again_order.gif" border=0></a></div><?php }?>
<div style="font-size:0;height:5px"></div>
</div><!-- End indiv -->
<script language="javascript">

function receipt(controlNo, payment)
{
    var receipt_url= "http://office.easypay.co.kr/receipt/ReceiptBranch.jsp?controlNo="+controlNo+"&payment="+payment; // �׽�Ʈ
	window.open(receipt_url,"MEMB_POP_RECEIPT", 'toolbar=0,scroll=1,menubar=0,status=0,resizable=0,width=380,height=700');
}


function chkCancel(){
	var f = document.frmOrder;
	if(confirm('�ֹ����ó�� �Ͻðڽ��ϱ�?')){
		f.mode.value='orderCancel';
		f.submit();
	}
}
function chkReSettle(){
	var f = document.frmOrder;
	if(chkForm(f)){
		f.mode.value='reSettle';
		f.action = 'settle.php';
		f.submit();
	}

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
		if (document.getElementsByName('eggFee')[0].disabled == false) eggFee = parseInt(settlement * 0.005);
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
		var settlekind =  obj.value;

		if (settlekind == null) display = 'none';
		else if (settlekind == 'h') display = 'none';
		else if (document.getElementsByName('escrow')[0].value == 'Y') display = 'none';
		else if (typeof(document.getElementsByName('eggIssue')[0]) != "undefined"){
			if (settlekind != 'a') display = 'none';
			else if(typeof(settlement) == "undefined"){
				settlement = uncomma(_ID('paper_settlement').innerHTML);
				if (typeof(document.getElementsByName('eggFee')) != "undefined") settlement -= document.getElementsByName('eggFee')[0].value;
				if (settlement < min) display = 'none';
			}
		}
	}
	if (_ID('egg').style.display == display && display =='none') return;
	_ID('egg').style.display = display;

	items = new Array();
	items.push( {name : "eggIssue", label : "���ں������� �߱޿���", msgR : ""} );
	items.push( {name : "resno[]", label : "�ֹε�Ϲ�ȣ", msgR : "���ں��������� �߱޹����÷���, �ֹι�ȣ�� �Է��ϼž� ������ �����մϴ�."} );
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
function calcu_settle()
{
	var coupon = settleprice = 0;
	var goodsprice	= uncomma(document.getElementById('paper_goodsprice').innerHTML);
	var delivery	= uncomma(document.getElementById('paper_delivery').innerHTML);
	var dc = uncomma(document.getElementById('paper_memberdc').innerHTML);
	var emoney = (document.frmOrder.emoney) ? uncomma(document.frmOrder.emoney.value) : 0;
	if (document.frmOrder.coupon){
		coupon = uncomma(document.frmOrder.coupon.value);
		if (goodsprice + delivery - dc - coupon - emoney < 0){
			emoney = goodsprice + delivery - dc - coupon;
			document.frmOrder.emoney.value = comma(cutting(emoney));
		}
		dc += coupon + emoney;
	}
	var settlement = (goodsprice + delivery - dc);

	settlement += calcu_eggFee(settlement); // ���ں������� �߱޼����� ���
	document.getElementById('paper_settlement').innerHTML = comma(settlement);
}

function okcashbag(){
	var f = document.frmOrder;
	f.target = "ifrmHidden";
	f.action = "cashbag.php";
	f.submit();
}
function popup_register( mode, goodsno, sno )
{
<?php if(empty($GLOBALS["cfg"]['reviewWriteAuth'])&&!$GLOBALS["sess"]){?>
		alert( "ȸ�������Դϴ�." );
<?php }else{?>
		var win = window.open("../goods/goods_review_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno + "&referer=orderview","review_register","width=600,height=500");
		win.focus();
<?php }?>
}
</script>
<?php $this->print_("footer",$TPL_SCP,1);?>