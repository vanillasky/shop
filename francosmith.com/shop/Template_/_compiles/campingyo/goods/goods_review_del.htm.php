<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/goods_review_del.htm 000001559 */ ?>
<html>
<head>
<title>��ǰ�������</title>
<script src="/shop/data/skin/campingyo/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
</head>
<body>

<form method=post action="<?php echo url("goods/indb.php")?>&" onSubmit="return chkForm(this)">
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=sno value="<?php echo $GLOBALS["sno"]?>">

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td height=200 style="border:10px solid #000000" valign=top>

	<div style="width:100%; background:#000000; border-bottom:2px solid #DDDDDD"><img src="/shop/data/skin/campingyo/img/common/title_delete.gif"></div>
	<div style="text-align:center;margin-top:20px;">�ش� ���� �����Ͻðڽ��ϱ�?<br>������ ������ ������ �Ұ��� �մϴ�.</div>
	<div style="text-align:center;margin-top:10px;">
<?php if(!$GLOBALS["sess"]||empty($GLOBALS["data"]['m_no'])){?>
	<span class="input_txt">��й�ȣ</span> <span id=form><input type=password name=password style="width:100" required label="��й�ȣ"></span>
<?php }?>
	<input type=image src="/shop/data/skin/campingyo/img/common/btn_delete2.gif" align="absmiddle">
	</div>

	<div style="width:100%; text-align:right; padding-top:20"><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/campingyo/img/common/popup_close.gif"></A></div>

	</td>
</tr></form>
</table>

</body>
</html>