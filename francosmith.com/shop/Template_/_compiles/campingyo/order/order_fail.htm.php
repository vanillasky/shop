<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/order/order_fail.htm 000002609 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_order_finish.gif" border=0></td>
</tr>
<tr>
	<td class="path">home > <b>�ֹ�����</b></td>
</tr>
<!--
<tr>
	<td align=center style="padding:10 0 10 0"><img src="/shop/data/skin/campingyo/img/common/order_complete.gif" border=0></td>
</tr>
-->
</table><p>


<div class="indiv"><!-- Start indiv -->

<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:5px"><img src="/shop/data/skin/campingyo/img/common/order_step_end.gif"></td>
	<td style="border:5px solid #F3F3F3; padding:5px 10px;">

	<table width=100% cellpadding=2>
	<col width=100>
<?php if($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<td>�������</td>
		<td>������ü</td>
	</tr>
	<tr>
		<td>���</td>
		<td>�ֹ��� ���еǾ����ϴ�</td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<td>�������</td>
		<td>�ſ�ī��</td>
	</tr>
	<tr>
		<td>���</td>
		<td>�ֹ��� ���еǾ����ϴ�</td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="y"){?>
	<tr>
		<td>�������</td>
		<td>��������</td>
	</tr>
	<tr>
		<td>���</td>
		<td>�ֹ��� ���еǾ����ϴ�</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]> 50&&in_array($TPL_VAR["settlekind"],array('c','o','v','y'))&&$TPL_VAR["pgfailreason"]){?><!-- �������л��� -->
	<tr>
		<td>�������л���</td>
		<td><?php echo $TPL_VAR["pgfailreason"]?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["eggyn"]=='f'){?>
	<tr>
		<td>���ں�������</td>
		<td>������ �߱� ���з� �ֹ��� ���еǾ����ϴ�.</td>
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
	</table>

	</td>
</tr>
</table><p>

<div style="width:100%; text-align:center; padding:10"><A HREF="<?php echo url("index.php")?>&"><img src="/shop/data/skin/campingyo/img/common/btn_confirm.gif" border=0></A></div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>