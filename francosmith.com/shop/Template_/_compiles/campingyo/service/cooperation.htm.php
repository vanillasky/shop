<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/service/cooperation.htm 000005337 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_ad.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>����/���޹���</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
<style>
.scroll	{
scrollbar-face-color: #FFFFFF;
scrollbar-shadow-color: #AFAFAF;
scrollbar-highlight-color: #AFAFAF;
scrollbar-3dlight-color: #FFFFFF;
scrollbar-darkshadow-color: #FFFFFF;
scrollbar-track-color: #F7F7F7;
scrollbar-arrow-color: #838383;
}
#boxScroll{width:96%; height:130px; overflow: auto; BACKGROUND: #ffffff; COLOR: #585858; font:9pt ����;border:1px #dddddd solid; overflow-x:hidden;text-align:left; }
</style>
<!-- ��ȸ�� �������� ��޹�ħ ���� -->
<div><img src="/shop/data/skin/campingyo/img/common/order_txt_non.gif" border=0></div>
<div style="padding-top:10px; background:#F1F1F1;  text-align:center;">
<div align="left" style="height:26;padding:3px 0 0 10px;">
<b>�� ��ȸ�� ���ۼ��� ���� �������� ������ ���� ����</b> (�ڼ��� ������ ��<a href="<?php echo url("service/private.php")?>&">����������޹�ħ</a>���� Ȯ���Ͻñ� �ٶ��ϴ�)
</div>
<div id="boxScroll" class="scroll">
<?php echo $this->define('tpl_include_file_1',"/service/_private_non.txt")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

</div>
<div align=center class=noline style="height:30px;margin-top:10px;" >
<input type="radio" name="private" value="y" onclick="javascript:document.fm.private.value='y';"> �����մϴ� &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="private" value="n" onclick="javascript:document.fm.private.value='';"> �������� �ʽ��ϴ�
</div>
</div>
<div style="font-size:0;height:5px"></div>
<?php }?>

<form name=fm method=post action="<?php echo $TPL_VAR["cooActionUrl"]?>" onsubmit="return fm_save( this );" id="form">
<input type="hidden" name="mode" value="send">
<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
<input type=hidden name=private value="">
<?php }?>

<div style="border:1px solid #DEDEDE;" class="hundred">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3; padding:10 0 10 0" align=center>

	<table border="0" cellspacing="0" cellpadding="5" width=95%>
	<tr>
		<col class=input_txt align=right width=100>
		<th>�̸�</th>
		<td><input type=text name='name' size=15></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>�̸���</th>
		<td><input type=text name=mail size=56></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>�о�</th>
		<td>
		<select name="itemcd" class=select>
		<option selected>------ ���Ǻо� ------</option>
<?php if((is_array($TPL_R1=codeitem('cooperation'))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_K1=>$TPL_V1){?>
		<option value="<?php echo $TPL_K1?>">�� <?php echo $TPL_V1?></option>
<?php }}?>
		</select>
		</td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>����</th>
		<td><input type=text name=title size=56></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0px;"></td></tr>
	<tr>
		<th>����</th>
		<td><textarea cols=75 rows=12 name=content></textarea></td>
	</tr>
	</table>

	</td>
</tr>
</table>
</div>

<div style="padding-top:15;text-align:center" class=noline>
<input type="image" src="/shop/data/skin/campingyo/img/common/btn_send.gif" border=0 align="absmiddle">
<a href="javascript:history.back();"><img src="/shop/data/skin/campingyo/img/common/btn_cancel.gif" align="absmiddle"></a>
</div>
</form>

</div><!-- End indiv -->



<script language=javascript>
<!--
/*-------------------------------------
 ���
-------------------------------------*/
function fm_save( fobj ){

<?php if(!$GLOBALS["sess"]&&is_file(sprintf("../skin/%s/service/_private_non.txt",$GLOBALS["cfg"]["tplSkin"]))){?>
	if ( fobj["private"].value != "y" ){

		alert( "��ȸ�� �������� ������ ���Ǹ� �ϼž߸� ���ۼ��� �����մϴ�." );
		return false;
	}
<?php }?>

	if ( fobj["name"].value == "" ){

		alert( "�̸��� �Է����� �ʾҽ��ϴ�.\n�̸��� �Է��ϼ���!!" );
		fobj["name"].focus() ;
		return false;
	}

	if ( fobj["mail"].value == "" ){

		alert( "������ �Է����� �ʾҽ��ϴ�.\n������ �Է��ϼ���!!" );
		fobj["mail"].focus() ;
		return false;
	}

	if ( fobj["itemcd"].selectedIndex == 0 ){

		alert( "���Ǻо߸� �������� �ʾҽ��ϴ�.\n���Ǻо߸� �����ϼ���!!" );
		fobj["itemcd"].focus() ;
		return false;
	}

	if ( fobj["title"].value == "" ){

		alert( "������ �Է����� �ʾҽ��ϴ�.\n������ �Է��ϼ���!!" );
		fobj["title"].focus() ;
		return false;
	}

	if ( fobj["content"].value == "" ){

		alert( "������ �Է����� �ʾҽ��ϴ�.\n������ �Է��ϼ���!!" );
		fobj["content"].focus() ;
		return false;
	}

	return true;
}
//-->
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>