<?php /* Template_ 2.2.7 2014/07/30 21:42:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/board/gallery/delete.htm 000002160 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?> <?php echo $GLOBALS["bdHeader"]?>


<table width=<?php echo $GLOBALS["bdWidth"]?> align=<?php echo $GLOBALS["bdAlign"]?> cellpadding=0 cellspacing=0><tr><td>

<form name=frmDelete action="<?php echo url("board/delete_ok.php")?>&" method=post onSubmit="return chkForm(this)">
<input type=hidden name=id value=<?php echo $_GET["id"]?>>
<input type=hidden name=sel[] value=<?php echo $GLOBALS["no"]?>>
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=returnUrl value="<?php echo $GLOBALS["returnUrl"]?>">

<table width=100%>
<tr><td height=3 bgcolor=#efefef></td></tr>
<tr>
	<td height=150 align=center bgcolor=#f7f7f7>

<?php if(($GLOBALS["m_no"]&&$GLOBALS["m_no"]==$GLOBALS["sess"]["m_no"])||$GLOBALS["ici_admin"]){?>
	<b>������ �����Ͻðڽ��ϱ�?</b><p>
	���� �����մϴ�. ������ ������ ������ �Ұ��� �մϴ�<br>
	<font color=#FF6600>���� �� �Խù��� �ۼ��ڰ� <b>���ε��� �̹����� ���� ����</b>�˴ϴ�.<br>
	���ε� �Ǿ� �ִ� �̹����� �ٸ� �������� ���ǰ� ���� �� �����Ƿ�<br>
	<b>������ Ȯ���Ͻð� �����ϼ���.</b></font>
<?php }elseif($GLOBALS["m_no"]){?>
	<b>���������� �����ϴ�</b><p>
	���� �����Ҽ� �ִ� ������ ������ ���� �ʽ��ϴ�
<?php }else{?>
	<input type=password name=password required class=line><p>
	���� �����մϴ�. ��й�ȣ�� �Է��Ͽ� �ֽʽÿ�<br>
	������ ������ ������ �Ұ��� �մϴ�<br>
	<font color=#FF6600>���� �� �Խù��� �ۼ��ڰ� <b>���ε��� �̹����� ���� ����</b>�˴ϴ�.<br>
	���ε� �Ǿ� �ִ� �̹����� �ٸ� �������� ���ǰ� ���� �� �����Ƿ�<br>
	<b>������ Ȯ���Ͻð� �����ϼ���.</b></font>
<?php }?>

	</td>
</tr>
<tr><td height=2 bgcolor=#efefef></td></tr>
<tr>
	<td align=center><br>
	<input type=image src="/shop/data/skin/freemart/board/gallery/img/board_btn_ok.gif">
	<a href="javascript:history.back()"><img src="/shop/data/skin/freemart/board/gallery/img/board_btn_back.gif"></a>
	</td>
</tr>
</table>

</form>

</td></tr></table>

<?php echo $GLOBALS["bdFooter"]?> <?php $this->print_("footer",$TPL_SCP,1);?>