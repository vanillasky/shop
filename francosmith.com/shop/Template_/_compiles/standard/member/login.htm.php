<?php /* Template_ 2.2.7 2015/03/02 10:30:53 /www/francotr3287_godo_co_kr/shop/data/skin/standard/member/login.htm 000005231 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script>
window.onload = function(){ document.form.m_id.focus(); }
</script>

<!-- ����̹��� || ������ġ -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/standard/img/common/title_login.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>�α���</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<div style="padding-top:10; padding-bottom:30; text-align:center"><img src="/shop/data/skin/standard/img/common/login_01.gif"></div>

<div><img src="/shop/data/skin/standard/img/common/login_02.gif" border=0></div>
<div style="border:1px solid #DEDEDE;" class="hundred">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3;">
	<div style="margin-left: 40px; margin-top: 20px; background: url('/shop/data/skin/standard/img/login_09.gif') no-repeat; height: 12px; font-size: 0; text-indent: -9999px; display: block;">ȸ�� �α���</div>
	<div style="margin:10 0 10 0; float:left; width:310;">
	<form method=post action="<?php echo $TPL_VAR["loginActionUrl"]?>" id=form name=form>
	<input type=hidden name=returnUrl value="<?php echo $GLOBALS["returnUrl"]?>">
	<table cellpadding=2 border=0 align=right>
<?php if($GLOBALS["cfg"]["ssl"]=="1"){?>
	<tr>
		<td></td>
		<td><div><div style="float:left;font-family:'����';font-size:11;color:#666666;">����</div><div style="font-family:'VERDANA';font-size:9;color:#4499fa;font-weight:bold;">ON</div></div></td>
	</tr>
<?php }?>

	<tr>
		<td class="input_txt">���̵�</td>
		<td><input type=text name=m_id size=20 tabindex=1></td>
		<td rowspan=2 class=noline><input type=image src="/shop/data/skin/standard/img/common/btn_login.gif" tabindex=3></td>
	</tr>
	<tr>
		<td class="input_txt">��й�ȣ</td>
		<td><input type=password name=password size=20 tabindex=2></td>
	</tr>
	</table>
	</form>
	</div>

	<div style="margin:10 0 10 0; float:right; width:315;">
	<table>
	<tr>
		<td rowspan=3 style="padding-right:10"><img src="/shop/data/skin/standard/img/common/login_div.gif"></td>
		<td><A HREF="<?php echo url("member/join.php")?>&" onFocus="blur()"><img src="/shop/data/skin/standard/img/common/login_04.gif"></A></td>
	</tr>
	<tr><td><A HREF="<?php echo url("member/find_id.php")?>&" onFocus="blur()"><img src="/shop/data/skin/standard/img/common/login_05.gif"></A></td></tr>
	<tr><td><A HREF="<?php echo url("member/find_pwd.php")?>&" onFocus="blur()"><img src="/shop/data/skin/standard/img/common/login_06.gif"></A></td></tr>
	</table>
	</div>

<?php if($TPL_VAR["SocialMemberEnabled"]){?>
	<div style="overflow: hidden; float: left; width: 100%;">
		<div style="background: url('/shop/data/skin/standard/img/sns_vertical_bar.gif') repeat-x center; width: 600px; height: 10px; border: none; margin-left: 25px;">
			<hr style="display: none;"/>
		</div>
		<div style="margin-left: 40px; margin-top: 20px; background: url('/shop/data/skin/standard/img/login_10.gif') no-repeat; height: 12px; font-size: 0; text-indent: -9999px; display: block;">SNS ���� �α���</div>
		<div style="padding: 20px 60px;">
<?php if($TPL_VAR["FacebookLoginURL"]){?>
			<button style="background: url('/shop/data/skin/standard/img/login_sns_facebook_wide.gif') no-repeat; border: none; width: 239px; height: 31px; font-size: 0; cursor: pointer; text-indent: -9999px; display: block;" onclick="popup('<?php echo $TPL_VAR["FacebookLoginURL"]?>', 400, 300);">FACEBOOK</button>
<?php }?>
		</div>
	</div>
<?php }?>
	</td>
</tr>
</table>
</div>

<?php if($TPL_VAR["guest_disabled"]!='y'){?>
<div style="padding-top:15;"><img src="/shop/data/skin/standard/img/common/login_03.gif" border=0></div>
<div style="border:1px solid #DEDEDE;" class="hundred">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3;padding:10">

<?php if($_GET["guest"]){?>
	<div align=center style="padding:8px"><a href="<?php echo url("order/order.php?")?>&guest=1">��ȸ������ �����ϱ�</a></div>
<?php }else{?>
	<form id=form method=post action="<?php echo url("member/login_ok.php")?>&" onsubmit="return chkForm(this)">
	<input type=hidden name=mode value="guest">
	<input type=hidden name=returnUrl value="<?php echo $GLOBALS["returnUrl"]?>">
	<table cellpadding=1 border=0 align=center>
	<tr><td align=center><img src="/shop/data/skin/standard/img/common/login_07.gif" border=0></td></tr>
	<tr>
		<td align=center>

		<table>
		<tr>
			<td class="input_txt">�ֹ��ڸ�</td>
			<td style="padding:0 10px 0 5px"><input type=text name=nameOrder size=10 required label='�ֹ��ڸ�'></td>
			<td class="input_txt">�ֹ���ȣ</td>
			<td style="padding-left:5px"><input type=text name=ordno size=20 required label='�ֹ���ȣ'></td>
			<td><input type=image src="/shop/data/skin/standard/img/common/btn_ok.gif"></td>
		</tr>
		</table>

		</td>
	</tr>
	</table>
	</form>
<?php }?>

	</td>
</tr>
</table>
</div><p>
<?php }?>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>