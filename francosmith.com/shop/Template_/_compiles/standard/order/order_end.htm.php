<?php /* Template_ 2.2.7 2015/07/01 09:55:51 /www/francotr3287_godo_co_kr/shop/data/skin/standard/order/order_end.htm 000006241 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/standard/img/common/title_order_finish.gif" border=0></td>
</tr>
<tr>
	<td class="path">home > <b>�ֹ��Ϸ�</b></td>
</tr>
<tr>
	<td align=center style="padding:10 0 10 0"><img src="/shop/data/skin/standard/img/common/order_complete.gif" border=0></td>
</tr>
</table><p>


<div class="indiv"><!-- Start indiv -->

<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:5px"><img src="/shop/data/skin/standard/img/common/order_step_end.gif"></td>
	<td style="border:5px solid #F3F3F3; padding:5px 10px;">

	<table width=100% cellpadding=2>
	<col width=100>
<?php if($TPL_VAR["settleInflow"]=="payco"){?>
	<tr>
		<td>��������</td>
		<td>������ ����</td>
	</tr>
	<tr>
		<td>��������</td>
		<td><?php echo $TPL_VAR["paycoSettleKind"]?></td>
	</tr>
<?php }?>
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
	<tr>
		<td>�Աݱݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<td>����ī��</td>
		<td><?php echo $_GET["card_nm"]?></td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<td>�������</td>
		<td>������ü</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
	<tr>
		<td>�������</td>
		<td><?php echo $TPL_VAR["vAccount"]?></td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="y"){?>
	<tr>
		<td>�������</td>
		<td>��������</td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="d"){?>
	<tr>
		<td>�������</td>
		<td>�������� ���� (������ ���)</td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="u"){?>
	<tr>
		<td>����ī��</td>
		<td><?php echo $_GET["card_nm"]?></td>
	</tr>
	<tr>
		<td>�����ݾ�</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }?>
	<tr>
		<td>��ǰ����</td>
		<td><?php echo number_format($TPL_VAR["goodsprice"])?>��</td>
	</tr>
	<tr>
		<td>��ۺ�</td>
		<td><?php if($TPL_VAR["deli_msg"]){?><?php echo $TPL_VAR["deli_msg"]?><?php }else{?><?php echo number_format($TPL_VAR["delivery"])?>��<?php }?></td>
	</tr>
<?php if($TPL_VAR["o_special_discount_amount"]){?>
	<tr>
		<td>��ǰ����</td>
		<td><?php echo number_format($TPL_VAR["o_special_discount_amount"])?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<td>ȸ������</td>
		<td><?php echo number_format($TPL_VAR["memberdc"])?>��</td>
	</tr>
<?php }?>
<?php if($GLOBALS["naver_mileage"]){?>
	<tr>
		<td>���̹����ϸ���</td>
		<td><?php echo number_format($GLOBALS["naver_mileage"])?>��</td>
	</tr>
<?php }?>
<?php if($GLOBALS["naver_cash"]){?>
	<tr>
		<td>���̹�ĳ��</td>
		<td><?php echo number_format($GLOBALS["naver_cash"])?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr>
		<td>��������</td>
		<td><?php echo number_format($TPL_VAR["coupon"])?>��<?php if($TPL_VAR["about_coupon"]){?> (��ٿ����� <?php echo number_format($TPL_VAR["about_coupon"])?>��)<?php }?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
	<tr>
		<td>��������</td>
		<td><?php echo number_format($TPL_VAR["coupon_emoney"])?>��</td>
	</tr>
<?php }?>
	<tr>
		<td>�����ݰ���</td>
		<td><b><?php echo number_format($TPL_VAR["emoney"])?>��</b></td>
	</tr>
<?php if($TPL_VAR["eggyn"]=='y'){?>
	<tr>
		<td>���ں�������</td>
		<td><a href="javascript:popupEgg('<?php echo $GLOBALS["egg"]["usafeid"]?>', '<?php echo $TPL_VAR["ordno"]?>')"><font color=#0074BA><b><u><?php echo $TPL_VAR["eggno"]?> <font class=small><b>[���������]</b></font></u></b></font></a><div style="padding-top:5px"><font class=small color=444444>������������ <A HREF="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_VAR["ordno"]?>"><font class=small color=#0074BA><b><u>�ֹ��󼼳���</u></b></font></A>������ ������ ����� �����մϴ�.</font></div></td>
	</tr>
<?php }elseif($TPL_VAR["eggyn"]=='f'){?>
	<tr>
		<td>���ں�������</td>
		<td>������ �߱��� ���еǾ����ϴ�. ������������ <A HREF="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_VAR["ordno"]?>"><font color=#0074BA><b><u>�ֹ��󼼳���</u></b></font></A>���� ��߱��� �����մϴ�.</td>
	</tr>
<?php }?>
	<tr><td height=3></td></tr>
	<tr><td height=1 bgcolor=#efefef colspan=2 style="font-size:0px;"></td></tr>
	<tr><td height=3></td></tr>
	<tr>
		<td>�ֹ���ȣ</td>
		<td><?php echo $TPL_VAR["ordno"]?></td>
	</tr>
	<tr>
		<td>�ֹ��ڸ�</td>
		<td><?php echo $TPL_VAR["nameOrder"]?></td>
	</tr>
	<tr>
		<td>�ֹ�����</td>
		<td><?php echo $TPL_VAR["orddt"]?></td>
	</tr>
	<tr>
		<td>�ֹ��ݾ�</td>
		<td><?php echo number_format($TPL_VAR["settleprice"])?>��</td>
	</tr>
<?php if($TPL_VAR["cashreceipt_useopt"]=='0'||$TPL_VAR["cashreceipt_useopt"]=='1'){?>
	<tr>
		<td>���ݿ�����</td>
		<td>
<?php if($TPL_VAR["cashreceipt_useopt"]=='0'){?>
			�ҵ������
<?php }else{?>
			����������
<?php }?>
			���ݿ����� ��û
		</td>
	</tr>
<?php }?>
	</table>
	</td>
</tr>
</table><p>

<div style="width:100%; text-align:center; padding:10"><A HREF="<?php echo url("index.php")?>&"><img src="/shop/data/skin/standard/img/common/btn_confirm.gif" border=0></A></div>

</div><!-- End indiv -->

<?php echo $TPL_VAR["naverCommonInflowScript"]->getOrderCompleteData($_GET["ordno"])?>


<?php $this->print_("footer",$TPL_SCP,1);?>