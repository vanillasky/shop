<?php /* Template_ 2.2.7 2014/06/19 23:40:02 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/side/mainleft.htm 000006584 */  $this->include_("dataBank","dataBanner");?>
<!-- ī�װ� �޴� ���� -->
<!-- ���� ���μҽ��� '��Ÿ/�߰�������(proc) > ī�װ��޴�- menuCategory.htm' �ȿ� �ֽ��ϴ� -->
<?php $this->print_("menuCategory",$TPL_SCP,1);?>

<!-- ī�װ� �޴� �� -->

<!-- ���ΰ��̵� : Start -->
<table cellpadding='0' cellspacing='0'>
<tr>
  <td><a href='/shop/mypage/mypage_qna.php?&&'><img src="/shop/data/images/bn_guid01.gif"></a></td>
  <td><a href='/shop/goods/goods_qna.php?&&'><img src="/shop/data/images/bn_guid02.gif"></a></td>
  <td><a href='/shop/service/faq.php?&&'><img src="/shop/data/images/bn_guid03.gif"></a></td>
 </tr>
<tr>
	<td><a href='/shop/mypage/mypage_orderlist.php?&&'><img src="/shop/data/images/bn_guid04.gif"></a></td>
  <td><a href='/shop/goods/goods_review.php?&&'><img src="/shop/data/images/bn_guid05.gif"></a></td>
  <td><a href='/shop/mypage/mypage_wishlist.php?&&'><img src="/shop/data/images/bn_guid06.gif"></a></td>
 </tr>
</table>

<!-- ���ο��� ������ 01 : Start -->
<div style="width:190px; height:95px; background:url(/shop/data/skin/campingyo/img/main/bn_cs.jpg) no-repeat;">
	<div style="padding:19px 0px 2px 66px;"><img src="/shop/data/skin/campingyo/img/main/txt_cs.gif"></div>
	<div style="padding-left:66px; font-size:14px; font-weight:bold; line-height:23px; color:#333; font-family:Tahoma, Geneva, sans-serif"><?php echo $GLOBALS["cfg"]['compPhone']?></div>
	<dl style="margin:0px; padding-left:66px; color:#888; font-size:11px;">
		<dd style="margin:0px; line-height:12px;font-family:formatta,Tahoma">MON - FRI</dd>
		<dd style="margin:0px; line-height:12px;font-family:formatta,Tahoma"">10:00 - 18:00</dd>
	</dl>
</div>
<!-- ���ο��� ������ 01 : End -->
<!-- �������Ա� : Start -->
<div style="width:190px; background:url(/shop/data/skin/campingyo/img/main/bn_bankinfo.jpg) no-repeat 0px 25px;">
	<div style="padding:19px 0px 7px 66px;"><img src="/shop/data/skin/campingyo/img/main/txt_bankinfo.gif"></div>
	<div style="padding:0 0 15px 66px; font-size:11px; color:#666;">
<?php if((is_array($TPL_R1=dataBank())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_S1=count($TPL_R1);$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
	<p style="margin:0px; padding:0px;"><?php echo $TPL_V1["bank"]?></p>
	<p style="margin:0px; padding:0px; font-weight:bold;"><?php echo $TPL_V1["account"]?></p>
	<p style="margin:0px; padding:0px;"><?php echo $TPL_V1["name"]?></p>
<?php if($TPL_I1!=$TPL_S1- 1){?>
	<p style="margin:0px; padding:0px; border-top:solid 1px #EBEBEB;height:1px;font-size:0px; margin:7px 0 6px"></p>
<?php }?>
<?php }}?>
	</div>
</div>
<!-- �������Ա� : End -->

																											   
<!-- ���ο��ʹ�� : Start -->
<table cellpadding="0" cellspacing="0" border="0" width=100%>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 5))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 11))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 12))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 13))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 14))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 15))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 16))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 19))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 20))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 21))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 24))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (��ʰ������� ��������) --><?php if((is_array($TPL_R1=dataBanner( 30))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
</table>
<!-- ���ο��ʹ�� : End -->