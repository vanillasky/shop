<?php /* Template_ 2.2.7 2014/07/30 21:43:01 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/member/confirm_password.htm 000001988 */ ?>
<script language="JavaScript">
	addOnloadEvent(function() {_ID('confirm_password').focus()});
</script>
<style type="text/css">
	#cp_body form		{ padding:0px; margin:0px; }
	#cp_title			{ padding:20px 0px; text-align:center; }
	#cp_form_border		{ border:1px solid #DEDEDE; }
	#cp_form			{ border:4px solid #F3F3F3; color:#404040; padding:33px 0px; text-align:center; }
	.cp_formTitle		{ color:#5D5D5D; font-weight:bold; }
	.cp_formValue		{ color:#008AE0; }
	#confirm_password	{ border:1px solid #CCCCCC; width:120px; }
	#cp_text1			{ color:#FF0000; font-family:dotum; font-size:12px; padding-top:10px; }
	#cp_button			{ padding:20px 0px; text-align:center; }
</style>
<div id="cp_body">
	<form name="confirmForm" method="post" action="<?php echo url("member/indb.confirm.php")?>&">
	<input type="hidden" name="mode" id="mode" value="confirmPassword" />
	<div id="cp_title"><img src="/shop/data/skin/freemart/img/common/h_pass_rember.gif" /></div>
	<div id="cp_form_border">
		<div id="cp_form">
			<table cellpadding="4" cellspacing="0" border="0" align="center">
			<tr align="left">
				<td width="70" class="cp_formTitle">���̵�</td>
				<td width="130" class="cp_formValue"><?php echo $TPL_VAR["m_id"]?></td>
				<td width="70" class="cp_formTitle">��й�ȣ</td>
				<td width="130"><input type="password" name="confirm_password" id="confirm_password" /></td>
			</tr>
			<tr align="left">
				<td colspan="4" id="cp_text1">ȸ������ ������ �����ϰ� ��ȣ�ϱ� ���� ��й�ȣ�� �ٽ� �� �� Ȯ���մϴ�.</td>
			</tr>
			</table>
		</div>
	</div>
	<div id="cp_button">
		<input type="image" src="/shop/data/skin/freemart/img/common/btn_gr_enter.gif" />
		<a href="javascript:;" onclick="history.go(-1);"><img src="/shop/data/skin/freemart/img/common/btn_gr_cancle.gif" border="0" /></a>
	</div>
	</form>
</div>