<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/side/mypage.htm 000003118 */  $this->include_("dataBanner");?>
<div style="width:190px; border-bottom:solid 1px #a4a4a4; padding:17px 0 0 0;">
	<div style="padding:0px 0px 10px 17px; font-size:12px; font-weight:bold; color:#333; border-bottom:solid 1px #ccc;">����������</div>
	<!-- ���������ڽ� : START -->
	<div style="padding:20px 15px 8px 15px; font-size:11px; color:#7a7a7a;">
		<span style="padding-left:10px; font-weight:bold; color:#343434;"><?php echo $GLOBALS["member"]["name"]?></span> ���� ��������
		<table cellpadding=0 cellspacing=0 border=0 style="padding:8px 0; border-top:solid 1px #e8e8e8;">
		<tr><td><?php $this->print_("myBox",$TPL_SCP,1);?></td></tr>
		</table>
	</div>
	<!-- ���������ڽ� : END -->

	<!-- ���������� �޴� ���� -->
	<div style="width:100%; border-bottom:dashed 1px #e0e0e0; font-size:0px;"></div>
	<table width="100%" cellpadding=0 cellspacing=0 border=0 class="lnbMyMenu">
	<tr>
		<th>��������</th>
	</tr>
	<tr>
		<td>
			<div><a href="<?php echo url("member/myinfo.php")?>&" class="lnbmenu">��ȸ����������</a></div>
			<div><a href="<?php echo url("member/hack.php")?>&" class="lnbmenu">��ȸ��Ż��</a></div>
		</td>
	</tr>
	<tr>
		<th>�� ��������</th>
	</tr>
	<tr>
		<td>
			<div><a href="<?php echo url("mypage/mypage_orderlist.php")?>&" class="lnbmenu">���ֹ�����/�����ȸ</a></div>
			<div><a href="<?php echo url("mypage/mypage_emoney.php")?>&" class="lnbmenu">�������ݳ���</a></div>
			<div><a href="<?php echo url("mypage/mypage_coupon.php")?>&" class="lnbmenu">��������������</a></div>
			<div><a href="<?php echo url("mypage/mypage_wishlist.php")?>&" class="lnbmenu">����ǰ������</a></div>
		</td>
	</tr>
	<tr>
		<th><a href="<?php echo url("mypage/mypage_qna.php")?>&" style="color:#525252">1:1 ���ǰԽ���</a></th>
	</tr>
	<tr>
		<th><a href="<?php echo url("mypage/mypage_review.php")?>&" style="color:#525252">���� ��ǰ�ı�</a></th>
	</tr>
	<tr>
		<th><a href="<?php echo url("mypage/mypage_qna_goods.php")?>&" style="color:#525252">���� ��ǰ����</a></th>
	</tr>
	<tr>
		<th class="unline"><a href="<?php echo url("mypage/mypage_today.php")?>&" style="color:#525252">�ֱ� �� ��ǰ ���</a></th>
	</tr>
	</table>
	<!-- ���������� �޴� �� -->
</div>

<!-- ���ο��ʹ�� : Start -->
<table cellpadding="0" cellspacing="0" border="0"width=100%>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 4))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 5))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
</table>
<!-- ���ο��ʹ�� : End -->

<div style="padding-top:80px"></div>