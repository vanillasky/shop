<?php /* Template_ 2.2.7 2014/11/03 14:24:29 /www/francotr3287_godo_co_kr/shop/data/skin/standard/mypage/_myBoxLayer.htm 000004584 */ ?>
<?php if($TPL_VAR["page_cache_enabled"]){?>
<div id="MypageLayerBox" style="z-index:1000;position:absolute;border:1px solid #363636;background:#F6F6F6;width:187px;height:220px;display:none;text-align:center;">

	<div style="float:right;">
		<a href="javascript:void(0);" onClick="document.getElementById('MypageLayerBox').style.display='none';"><img src="/shop/data/skin/standard/img/main/close.gif"></a>
	</div>

	<div style="clear:both;font-size:11px;margin:5px 0 3px 0;letter-spacing:-1px;">
		<span id="MypageLayerBox-name"></span> ����
<?php if($GLOBALS["sess"]["grpnm_disp_type"]=='icon'){?><img src="../data/member/icon/<?php echo $GLOBALS["sess"]["grpnm_icon"]?>" align="absbottom"><?php }?>
		<span id="MypageLayerBox-grpnm" style="font-weight:bold;color:#4B4B4B;"></span> �Դϴ�.
	</div>

	<div style="width:170px;background:#ffffff;border:1px solid #E6E6E6;padding:8px;">
		<table width="100%">
		<tr>
			<td class="small1" width="60">���ѱ��ž�</td><td class="small1" align="right"><span id="MypageLayerBox-sum-sale" class=v71 style="color:#ff4810;"></span> ��</td>
		</tr>
		<tr>
			<td class="small1">��������</td><td class="small1" align="right"><span id="MypageLayerBox-emoney" class=v71 style="color:#ff4810;"></span> ��</td>
		</tr>
		<tr>
			<td class="small1">����������</td><td class="small1" align="right"><span id="MypageLayerBox-coupon-count" class=v71 style="color:#ff4810;"></span> ��</td>
		</tr>
		</table>
	</div>

	<div style="width:170px;padding:8px;">
		<table width="100%">
		<tr>
			<td class="small1" width="60">����ٱ���</td><td class="small1" align="right"><a href="<?php echo url("goods/goods_cart.php")?>&"><span id="MypageLayerBox-cart-count" class=v71 style="color:#2246F6;"></span></a> ��</td>
		</tr>
		<tr>
			<td class="small1">�����ø���Ʈ</td><td class="small1" align="right"><a href="<?php echo url("mypage/mypage_wishlist.php")?>&"><span id="MypageLayerBox-wish-count" class=v71 style="color:#2246F6;"></span></a> ��</td>
		</tr>
		</table>
	</div>

	<div>
		<a href="<?php echo url("member/myinfo.php")?>&"><img src="/shop/data/skin/standard/img/main/btn_mypage_go.gif"></a>
	</div>

</div>
<?php }else{?>
<div id="MypageLayerBox" style="z-index:1000;position:absolute;border:1px solid #363636;background:#F6F6F6;width:187px;height:220px;display:none;text-align:center;">

	<div style="float:right;">
		<a href="javascript:void(0);" onClick="document.getElementById('MypageLayerBox').style.display='none';"><img src="/shop/data/skin/standard/img/main/close.gif"></a>
	</div>

	<div style="clear:both;font-size:11px;margin:5px 0 3px 0;letter-spacing:-1px;">
	<?php echo $GLOBALS["member"]["name"]?> ���� <?php if($GLOBALS["sess"]["grpnm_disp_type"]=='icon'){?><img src="../data/member/icon/<?php echo $GLOBALS["sess"]["grpnm_icon"]?>" align="absbottom"><?php }?> <font style="font-weight:bold;" color=#4B4B4B><?php echo $GLOBALS["sess"]["grpnm"]?></font> �Դϴ�.
	</div>

	<div style="width:170px;background:#ffffff;border:1px solid #E6E6E6;padding:8px;">
		<table width="100%">
		<tr>
			<td class="small1" width="60">���ѱ��ž�</td><td class="small1" align="right"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["sum_sale"])?></font> ��</td>
		</tr>
		<tr>
			<td class="small1">��������</td><td class="small1" align="right"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["emoney"])?></font> ��</td>
		</tr>
		<tr>
			<td class="small1">����������</td><td class="small1" align="right"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["cnt_coupon"])?></font> ��</td>
		</tr>
		</table>
	</div>

	<div style="width:170px;padding:8px;">
		<table width="100%">
		<tr>
			<td class="small1" width="60">����ٱ���</td><td class="small1" align="right"><a href="<?php echo url("goods/goods_cart.php")?>&"><font class=v71 color=#2246F6><?php echo number_format($GLOBALS["sess"]["cart_count"])?></font></a> ��</td>
		</tr>
		<tr>
			<td class="small1">�����ø���Ʈ</td><td class="small1" align="right"><a href="<?php echo url("mypage/mypage_wishlist.php")?>&"><font class=v71 color=#2246F6><?php echo number_format($GLOBALS["sess"]["wish_count"])?></font></a> ��</td>
		</tr>
		</table>
	</div>

	<div>
		<a href="<?php echo url("member/myinfo.php")?>&"><img src="/shop/data/skin/standard/img/main/btn_mypage_go.gif"></a>
	</div>

</div>
<?php }?>