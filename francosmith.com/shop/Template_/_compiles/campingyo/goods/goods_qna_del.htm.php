<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/goods_qna_del.htm 000001980 */ ?>
<html>
<head>
<title>��ǰ���ǻ���</title>
<script src="/shop/data/skin/campingyo/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
<script language="javascript">
function fitwin()
{
	window.resizeTo(50,50);
	var borderY = document.body.clientHeight;

	width	= 400;
	height	= document.body.scrollHeight + borderY + 40 ;

	windowX = (window.screen.width-width)/2;
	windowY = (window.screen.height-height)/2;

	if(width>screen.width){
		width = screen.width;
		windowX = 0;
	}
	if(height>screen.height){
		height = screen.height;
		windowY = 0;
	}

	window.moveTo(windowX,windowY);
	window.resizeTo(width,height);
}
</script>
</head>
<body onload="fitwin()">

<form method=post action="<?php echo url("goods/indb.php")?>&" onSubmit="return chkForm(this)">
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=sno value="<?php echo $GLOBALS["sno"]?>">

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td height=200 style="border:10px solid #000000" valign=top>

	<div style="width:100%; background:#000000; border-bottom:2px solid #DDDDDD"><img src="/shop/data/skin/campingyo/img/common/title_delete.gif"></div>
	<div style="text-align:center;margin-top:20px;">��б��Դϴ�.<br>�ش� ���� ��й�ȣ�� �Է��Ͽ� �ּ���.</div>
	<div style="text-align:center;margin-top:10px;">
	<span class="input_txt">��й�ȣ</span> <span id=form><input type=password name=password style="width:100" required label="��й�ȣ"></span>
	<input type=image src="/shop/data/skin/campingyo/img/common/btn_delete2.gif" align="absmiddle">
	</div>

	<div style="width:100%; text-align:right; padding-top:20"><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/campingyo/img/common/popup_close.gif"></A></div>

	</td>
</tr>
</table>

</form>

</body>
</html>