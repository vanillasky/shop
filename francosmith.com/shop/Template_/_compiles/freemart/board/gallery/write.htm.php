<?php /* Template_ 2.2.7 2016/04/25 15:18:40 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/board/gallery/write.htm 000012109 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?> <?php echo $GLOBALS["bdHeader"]?>


<?php if($GLOBALS["bdPrivateYN"]=="Y"){?>
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
<div><img src="/shop/data/skin/freemart/img/common/order_txt_non.gif" border=0></div>
<div style="padding-top:10px; background:#F1F1F1;  text-align:center;">
<div align="left" style="height:26;padding:3px 0 0 10px;">
<b>�� ��ȸ�� ���ۼ��� ���� �������� ������ ���� ����</b> (�ڼ��� ������ ��<a href="<?php echo url("service/private.php")?>&">����������޹�ħ</a>���� Ȯ���Ͻñ� �ٶ��ϴ�)
</div>
<div id="boxScroll" class="scroll">
<?php echo $TPL_VAR["termsPolicyCollection3"]?>

</div>
<div align=center class=noline style="height:30px;margin-top:10px;" >
<input type="radio" name="private" value="y" onclick="javascript:document.frmWrite.private.value='y';"> �����մϴ� &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="private" value="n" onclick="javascript:document.frmWrite.private.value='';"> �������� �ʽ��ϴ�
</div>
</div>
<div style="font-size:0;height:5px"></div>
<?php }?>

<table width=<?php echo $GLOBALS["bdWidth"]?> align=<?php echo $GLOBALS["bdAlign"]?> cellpadding=0 cellspacing=0><tr><td style="padding-top:20px">

<form name=frmWrite action="<?php echo $TPL_VAR["boardwriteActionUrl"]?>" method=post enctype="multipart/form-data" onsubmit="return htmlEncodeContent(this)">
<input type=hidden name=tmp>
<input type=hidden name=id value="<?php echo $TPL_VAR["id"]?>">
<input type=hidden name=category_pre value="<?php echo $TPL_VAR["category"]?>">
<input type=hidden name=no value="<?php echo $TPL_VAR["no"]?>">
<input type=hidden name=mode value="<?php echo $TPL_VAR["mode"]?>">
<input type=hidden name='page' value="<?php echo $TPL_VAR["page"]?>">
<input type=hidden name='encode' value="">

<input type=hidden name=chkSpamKey>
<?php if($GLOBALS["bdPrivateYN"]=="Y"){?>
<input type=hidden name=private value="" required fld_esssential msgR="��ȸ�� �������� ������ ���Ǹ� �ϼž߸� ���ۼ��� �����մϴ�.">
<?php }?>
<table width=100% cellpadding=4 cellspacing=0 border=0>
<col width=80>
<tbody>
<tr><td height=2 bgcolor="#303030" colspan=2 style="font-size:0;padding:0px;"></td></tr>

<tr style="height:30px;">
	<td align=right class=input_txt>* �̸�</td>
	<td class=cell_L><input name=name value="<?php echo $TPL_VAR["name"]?>" class=linebg required fld_esssential <?php echo $TPL_VAR["readonly"]["name"]?>></td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<?php if(!($TPL_VAR["readonly"]["name"]||$TPL_VAR["ici_admin"])){?>
<tr>
	<td align=right class=input_txt>* ��й�ȣ</td>
	<td class=cell_L><input type=password name=password class=linebg required fld_esssential></td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<?php }?>
<?php if(!$GLOBALS["bdEmailNo"]){?>
<tr>
	<td align=right class=input_txt>�̸���</td>
	<td class=cell_L><input name=email style="width:400" value="<?php echo $TPL_VAR["email"]?>" class=linebg></td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<?php }?>
<?php if(!$GLOBALS["bdHomepageNo"]){?>
<tr>
	<td align=right class=input_txt>Ȩ������</td>
	<td class=cell_L><input name=homepage style="width:400" value="<?php echo $TPL_VAR["homepage"]?>" class=linebg></td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<?php }?>
<?php if($GLOBALS["bdUseSubSpeech"]){?>
<tr>
	<td align=right class=input_txt>* ���Ӹ�</td>
	<td class=cell_L><?php echo $TPL_VAR["subSpeech"]?></td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<?php }?>
<?php if($GLOBALS["bdTitleCChk"]||$GLOBALS["bdTitleSChk"]||$GLOBALS["bdTitleBChk"]){?>
<tr>
	<td align=right class=input_txt>����ȿ��</td>
	<td class=cell_L>
<?php if($GLOBALS["bdTitleCChk"]){?>
	<select name="titleStyle[C]" class=box>
	<option value="">���� ���ڻ�</option>
	<option value="#000000" style="color:#000000" <?php echo $TPL_VAR["titleStyle"]['C']['#000000']?>>����</option>
	<option value="#7F7F7F" style="color:#7F7F7F" <?php echo $TPL_VAR["titleStyle"]['C']['#7F7F7F']?>>ȸ��</option>
	<option value="#FFA300" style="color:#FFA300" <?php echo $TPL_VAR["titleStyle"]['C']['#FFA300']?>>���</option>
	<option value="#FF600F" style="color:#FF600F" <?php echo $TPL_VAR["titleStyle"]['C']['#FF600F']?>>��Ȳ</option>
	<option value="#ff0000" style="color:#ff0000" <?php echo $TPL_VAR["titleStyle"]['C']['#ff0000']?>>����</option>
	<option value="#A03F00" style="color:#A03F00" <?php echo $TPL_VAR["titleStyle"]['C']['#A03F00']?>>����</option>
	<option value="#FF08A0" style="color:#FF08A0" <?php echo $TPL_VAR["titleStyle"]['C']['#FF08A0']?>>��ȫ</option>
	<option value="#5000AF" style="color:#5000AF" <?php echo $TPL_VAR["titleStyle"]['C']['#5000AF']?>>����</option>
	<option value="#B0008F" style="color:#B0008F" <?php echo $TPL_VAR["titleStyle"]['C']['#B0008F']?>>����</option>
	<option value="#7FC700" style="color:#7FC700" <?php echo $TPL_VAR["titleStyle"]['C']['#7FC700']?>>����</option>
	<option value="#009FAF" style="color:#009FAF" <?php echo $TPL_VAR["titleStyle"]['C']['#009FAF']?>>û��</option>
	<option value="#0000ff" style="color:#0000ff" <?php echo $TPL_VAR["titleStyle"]['C']['#0000ff']?>>�Ķ�</option>
	</select>
<?php }?>
<?php if($GLOBALS["bdTitleSChk"]){?>
	<select name="titleStyle[S]" class=box>
	<option value="">���� ����ũ��</option>
	<option value="8px" <?php echo $TPL_VAR["titleStyle"]['S']['8px']?>>�����۰� [8px]</option>
	<option value="10px" <?php echo $TPL_VAR["titleStyle"]['S']['10px']?>>�۰� [10px]</option>
	<option value="12px" <?php echo $TPL_VAR["titleStyle"]['S']['12px']?>>���� [12px]</option>
	<option value="18px" <?php echo $TPL_VAR["titleStyle"]['S']['18px']?>>ũ�� [18px]</option>
	<option value="24px" <?php echo $TPL_VAR["titleStyle"]['S']['24px']?>>���� ũ�� [24px]</option>
	</select>
<?php }?>
<?php if($GLOBALS["bdTitleBChk"]){?>
	<select name="titleStyle[B]" class=box>
	<option value="">���� ���ڱ���</option>
	<option value="default" <?php echo $TPL_VAR["titleStyle"]['B']['default']?>>����</option>
	<option value="bold" <?php echo $TPL_VAR["titleStyle"]['B']['bold']?>>����</option>
	</select>
<?php }?>
	</td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<?php }?>
<tr>
	<td align=right class=input_txt>* ����</td>
	<td class=cell_L><input name=subject style="width:400" value="<?php echo $TPL_VAR["subject"]?>" class=linebg required fld_esssential></td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<tr>
	<td align=right class=input_txt>* ����</td>
	<td class=cell_L>
	<div>
<?php if($GLOBALS["chk"]["notice"]){?><?php echo $GLOBALS["chk"]["notice"]?> NOTICE<?php }?>
	<?php echo $GLOBALS["chk"]["secret"]?>

<?php if($GLOBALS["bdSecretChk"]==""||$GLOBALS["bdSecretChk"]=="0"||$GLOBALS["bdSecretChk"]== 1){?>
	<img src="/shop/data/skin/freemart/board/gallery/img/board_secret.gif" align=absmiddle>
<?php }elseif($GLOBALS["bdSecretChk"]=="3"){?>
	�ش���� ��б۷θ� �ۼ��� �˴ϴ�.
<?php }?>
	<!--
	<?php echo $GLOBALS["chk"]["html"]?> HTML
	<?php echo $GLOBALS["chk"]["br"]?> BR
	-->
	</div>
	<div style="height:400px;padding-top:5px;position:relative;z-index:99">
	<!-- mini editor -->
	<textarea name=contents style="width:100%;height:350px" type=editor fld_esssential label="����"><?php echo htmlspecialchars($TPL_VAR["contents"])?></textarea>
	<script src=../lib/meditor/mini_editor.js></script>
	<script>mini_editor("../lib/meditor/",<?php echo $GLOBALS["bdEditorChk"]?>)</script>
	</div>
	</td>
</tr>
<?php if($GLOBALS["bdUseLink"]){?>
<tr>
	<td align=right class=input_txt>��ũ</td>
	<td class=cell_L>
	<input type=text name=urlLink style="width:450" value="<?php echo $TPL_VAR["urlLink"]?>" class=linebg>
	</td>
</tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<?php }?>
<?php if($GLOBALS["bdUseFile"]){?>
<tr>
	<td align=right class=input_txt valign=top style="padding-top:10">���ε�</td>
	<td class=cell_L>

	<table width=100% id=table cellpadding=0 cellspacing=0 border=0>
	<col class=engb align=center>
	<?php echo $TPL_VAR["prvFile"]?>

<?php if(count($TPL_VAR["file"])< 12){?>
	<tr>
		<td width=20 nowrap>1</td>
		<td width=100%>
		<input type=file name="file[]" style="width:80%" class=linebg onChange="preview(this.value,0)">
		<a href="javascript:add()"><img src="/shop/data/skin/freemart/board/gallery/img/btn_upload_plus.gif" align=absmiddle></a>
		</td>
		<td id=prvImg0></td>
	</tr>
<?php }?>
	</table>

	<table><tr><td height=2></td></tr></table>
	<div width=100% style="padding:5;" class=stxt>
	- ������ �ִ� 12������ ���߾��ε尡 �����˴ϴ�<br>
	- Sourceâ���� ������ �̹����� Ŭ���ϸ� �̹���ġȯ�ڵ尡 �Էµ˴ϴ�
<?php if($GLOBALS["bdMaxSize"]){?><div>- ���� ���ε� �ִ� ������� <?php echo byte2str($GLOBALS["bdMaxSize"])?>�Դϴ�</div><?php }?>
	</div>

	</td>
</tr>
<?php }?>
<?php if($GLOBALS["bdSpamBoard"]& 2){?>
<tr>
	<td align=right class=input_txt>�ڵ���Ϲ���</td>
	<td class=cell_L><?php echo $this->define('tpl_include_file_1',"proc/_captcha.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></td>
</tr>
<?php }?>

<tr><td height=10 style="font-size:0;padding:0px;"></td></tr>
<tr><td height=1 bgcolor="#E0DFDF" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<tr><td height=3 bgcolor="#F7F7F7" colspan=2 style="font-size:0;padding:0px;"></td></tr>
<tr height=30>
	<td id=avoidDbl colspan=2 align=center style="padding-top:10">
	<input type="image" src="/shop/data/skin/freemart/board/gallery/img/btn_confirm.jpg" />
	<a href="javascript:document.frmWrite.reset()"><img src="/shop/data/skin/freemart/board/gallery/img/btn_init.jpg"></a>
	<a href="javascript:history.back()"><img src="/shop/data/skin/freemart/board/gallery/img/btn_back.jpg"></a>
	</td>
</tr>
</table><p>
</form>

</td></tr></table>

<script>
function encodeContent(form) {
	form.subject.value = encodeURIComponent(form.subject.value);
	form.contents.value = encodeURIComponent(form.contents.value);
	form.encode.value = 'y';
	return chkForm(form);
}

/*------------------------------ Ư�� �ѱ� ȣȯ 2013-05-08 �߰� ------------------------------*/
function htmlspecialchars (string) {
	return string.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function htmlEncodeContent(form) {
	var htmlEncodeC = chkForm(form);
	 
	if(htmlEncodeC == true){
		form.subject.value = htmlspecialchars(form.subject.value);
		form.contents.value = htmlspecialchars(form.contents.value);
		form.encode.value = 'htmlencode';
	}
	return htmlEncodeC; 
}
/*--------------------------------------------------------------------------------------------*/

function add(){
	var table = document.getElementById('table');
	if (table.rows.length>11){
		alert("���� ���ε�� �ִ� 12���� �����մϴ�");
		return;
	}
	date	= new Date();
	oTr		= table.insertRow( table.rows.length );
	oTr.id	= date.getTime();
	oTr.insertCell(0);
	oTd		= oTr.insertCell(1);
	tmpHTML = "<input type=file name='file[]' style='width:80%' class=line onChange='preview(this.value," + oTr.id +")'> <a href='javascript:del(" + oTr.id + ")'><img src='/shop/data/skin/freemart/board/gallery/img/btn_upload_minus.gif' align=absmiddle></a>";
	oTd.innerHTML = tmpHTML;
	oTd = oTr.insertCell(2);
	oTd.id = "prvImg" + oTr.id;
	calcul();
}
</script>

<?php echo $GLOBALS["bdFooter"]?> <?php $this->print_("footer",$TPL_SCP,1);?>