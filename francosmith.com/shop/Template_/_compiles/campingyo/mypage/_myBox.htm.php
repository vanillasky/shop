<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/_myBox.htm 000005136 */ ?>
<table width=150 cellpadding=2 cellspacing=0 border=0>
<col class=stxt width=65><col class=stxt align=right>
<tr>
<td>��ȸ���׷�
<?php if($GLOBALS["sess"]["dc_type"]!='N'||$GLOBALS["sess"]["add_emoney_type"]!='N'||$GLOBALS["sess"]["free_deliveryfee"]!='N'){?>
<br/>
&nbsp;&nbsp;&nbsp;<a href="javascript: member_grp_pop();"><img src="/shop/data/skin/campingyo/img/common/btn_benefit.gif"></a>
<?php }?>
</td>
<td>
<?php if($GLOBALS["sess"]["grpnm_disp_type"]=='icon'){?><img src="../data/member/icon/<?php echo $GLOBALS["sess"]["grpnm_icon"]?>"><?php }?> <?php echo $GLOBALS["sess"]["grpnm"]?>

</td>
</tr>
<tr>
<td>���ѱ��ž�</td>
<td><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["sum_sale"])?></font> ��</td>
</tr>
<tr>
<td>��������</td>
<td><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["emoney"])?></font> ��</td>
</tr>
<tr>
<td>����������</td>
<td><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["cnt_coupon"])?></font> ��</td>
</tr>
<tr>
<td>����ٱ���</td>
<td><a href="<?php echo url("goods/goods_cart.php")?>&"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["cart_count"])?></font></a> ��</td>
</tr>
<tr>
<td>�����ø���Ʈ</td>
<td><a href="<?php echo url("mypage/mypage_wishlist.php")?>&"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["wish_count"])?></font></a> ��</td>
</tr>
</table>

<div id="MyMemberGrpBpx" style="z-index:10000;position:absolute;width:374px;height:220px;display:none;text-align:center;">

<div><img src="/shop/data/skin/campingyo/img/common/benefit_01.gif"></div>
<div style="padding:0 25px 0 30px;width:374px;background:url(/shop/data/skin/campingyo/img/common/benefit_02.gif) repeat-y top left;text-align:left;line-height:150%;font-size:12px;font-family:����; color:#464646; letter-spacing:-1; ">
	<div>
		<strong><?php echo $TPL_VAR["grp_profit"]["name"]?></strong> ȸ���� <br />
		ȸ���׷��� <strong><?php echo $TPL_VAR["grp_profit"]["grpnm"]?></strong> �̽ø�,<br />
		�׷������� ������ �����ϴ�.<br />
		<div style="height:10px;"></div>
		<font style="color:#C00000; font-weight:bold">
<?php if($TPL_VAR["grp_profit"]["dc_type"]!=='N'){?>
<?php if($TPL_VAR["grp_profit"]["dc_std_amt"]){?><?php echo number_format($TPL_VAR["grp_profit"]["dc_std_amt"])?>�� �̻� ���Ž� <?php }?>
<?php switch($TPL_VAR["grp_profit"]["dc_type"]){case 'goods':?>��ǰ �Ǹűݾ�<?php break;case 'settle_amt':?>�� �����ݾ�<?php }?>�� <?php echo $TPL_VAR["grp_profit"]["dc"]?>%����</br />
<?php }?>
		
<?php if($TPL_VAR["grp_profit"]["add_emoney_type"]!='N'){?>
<?php if($TPL_VAR["grp_profit"]["add_emoney_std_amt"]){?><?php echo number_format($TPL_VAR["grp_profit"]["add_emoney_std_amt"])?>�� �̻� ���Ž� <?php }?>
<?php switch($TPL_VAR["grp_profit"]["add_emoney_type"]){case 'goods':?>��ǰ �Ǹűݾ�<?php break;case 'settle_amt':?>�� �����ݾ�<?php }?>�� <?php echo $TPL_VAR["grp_profit"]["add_emoney"]?>% �߰� ����<br />
<?php }?>
		
<?php if($TPL_VAR["grp_profit"]["free_deliveryfee"]!='N'&&$TPL_VAR["grp_profit"]["free_deliveryfee"]!='Y'){?>
<?php switch($TPL_VAR["grp_profit"]["free_deliveryfee"]){case 'goods':?>��ǰ �Ǹűݾ�<?php break;case 'settle_amt':?>�� �����ݾ�<?php }?>
<?php if($TPL_VAR["grp_profit"]["free_deliveryfee_std_amt"]){?><?php echo number_format($TPL_VAR["grp_profit"]["free_deliveryfee_std_amt"])?>�� �̻�<?php }?>			
			�ֹ��� ��ۺ� ����<br />
<?php }elseif($TPL_VAR["grp_profit"]["free_deliveryfee"]=='Y'){?>
		��� ��ǰ �ֹ��� ��ۺ� ����
<?php }?>
		</font>
		<div style="height:10px;"></div>
		<strong><?php echo $GLOBALS["cfg"]["shopName"]?></strong>�� ����� �ּż� �����մϴ�.
	</div>
</div>

<div style="background: url('/shop/data/skin/campingyo/img/common/benefit_03.gif'); width:374px; height:75px;">
	<div style="padding-top:25px;"></div>
	<a href="javascript:void(0);" onClick="document.getElementById('MyMemberGrpBpx').style.display='none'"><img src="/shop/data/skin/campingyo/img/common/btn_benefit_check.gif"></a>
</div>

</div>

</div>

<script>

function member_grp_pop (grp_sno){
	fnMyMemberGrpBpxPosition(296, 200);
}

function fnMyMemberGrpBpxPosition(w,h) {	// ����, ����
	var _doc_size = {
		width : document.body.scrollWidth || document.documentElement.scrollWidth,
		height: document.body.scrollHeight || document.documentElement.scrollHeight
	}

	var _win_size = {
		width : window.innerWidth	|| (window.document.documentElement.clientWidth	|| window.document.body.clientWidth),
		height: window.innerHeight	|| (window.document.documentElement.clientHeight|| window.document.body.clientHeight)
	}
	
	with (document.getElementById('MyMemberGrpBpx').style) {
		position = "absolute";
		width = w + 'px';
		height = h + 'px';
		zIndex = 10000;
		left = (_win_size.width + w) / 2.7 - w  + 'px';
		top = ((_win_size.height + h) / 2.7 - h) + document.body.scrollTop + 'px'
		display = "block";
	};
}

</script>