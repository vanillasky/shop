<?php /* Template_ 2.2.7 2013/07/22 16:59:57 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/light/ord/settle.htm 000005495 */ 
if (is_array($_POST)) $TPL__POST_1=count($_POST); else if (is_object($_POST) && in_array("Countable", class_implements($_POST))) $TPL__POST_1=$_POST->count();else $TPL__POST_1=0;
if (is_array($GLOBALS["bank"])) $TPL__bank_1=count($GLOBALS["bank"]); else if (is_object($GLOBALS["bank"]) && in_array("Countable", class_implements($GLOBALS["bank"]))) $TPL__bank_1=$GLOBALS["bank"]->count();else $TPL__bank_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "�����ϱ�";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#settle {background:#FFFFFF; padding:none; margin:none;}
section#nsettle {background:#FFFFFF; padding:12px;font-family:dotum;font-size:12px;}
section#nsettle .sub_title{height:22px; line-height:22px; color:#436693; font-weight:bold; font-size:12px;}
section#nsettle .sub_title .point {width:4px; height:22px; background:url('/shop/data/skin_mobileV2/light/common/img/bottom/icon_guide.png') no-repeat center left; float:left; margin-right:7px;}
section#nsettle table{border:none; border-top:solid 1px #dbdbdb;width:100%; margin-bottom:20px;}
section#nsettle table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#nsettle table th{text-align:center; background:#f5f5f5; width:100px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}

section#nsettle table td input[type=text], input[type=password], input[type=email], input[type=number], select{height:21px;}
section#nsettle table td textarea{width:95%;height:116px;}
section#nsettle .btn_center {margin:auto; width:198px; height:34px; margin-top:20px; margin-bottom:20px;}
section#nsettle .btn_center .submit{border:none; background:url('/shop/data/skin_mobileV2/light/common/img/layer/btn_red01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:left; font-family:dotum; line-height:34px;}
section#nsettle .btn_center .cancel{border:none; background:url('/shop/data/skin_mobileV2/light/common/img/layer/btn_black01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:right; font-family:dotum; line-height:34px;}

.max_width{width:95%;}


</style>
<section id="nsettle" class="content">

<form name="frmSettle" method=post action="indb.php" target="ifrmHidden">
<?php if($TPL__POST_1){foreach($_POST as $TPL_K1=>$TPL_V1){?>
<?php if(is_array($TPL_V1)){?>
<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<input type=hidden name="<?php echo $TPL_K1?>[]" value="<?php echo $TPL_V2?>">
<?php }}?>
<?php }else{?>
	<input type=hidden name="<?php echo $TPL_K1?>" value="<?php echo $TPL_V1?>">
<?php }?>
<?php }}?>

<!-- �������Ա� -->
<?php if($TPL_VAR["settlekind"]=="a"){?>

<table>
	<tr>
		<th>�Աݰ��¼���</th>
		<td>
			<select name="bankAccount" required label="�Աݰ���" class="max_width">
				<option value="">== �Աݰ��¸� �������ּ��� ==
<?php if($TPL__bank_1){foreach($GLOBALS["bank"] as $TPL_V1){?>
				<option value="<?php echo $TPL_V1["sno"]?>"><?php echo $TPL_V1["bank"]?> <?php echo $TPL_V1["account"]?> <?php echo $TPL_V1["name"]?>

<?php }}?>
			</select>
		</td>
	</tr>
	<tr>
		<th>�Ա��ڸ�</th>
		<td>
			<input type=text name="bankSender" value="<?php echo $TPL_VAR["nameOrder"]?>" required  label="�Ա��ڸ�" class="max_width">
		</td>
	</tr>
	<tr>
		<th>�Աݱݾ�</th>
		<td>
			<?php echo number_format($TPL_VAR["settleprice"])?>��
		</td>
	</tr>
</table>
<div id="avoidDblPay" class="btn_center">
	<button type="button" class="submit" onclick="submitSettleForm()">�����ϱ�</button>
	<button class="cancel" onclick="history.back();">����ϱ�</button>
</div>


<?php }elseif($TPL_VAR["settlekind"]=="h"&&$TPL_VAR["MobiliansEnabled"]==true){?>
<?php  $TPL_VAR["page_title"] = "�����ϱ�";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<div>������� ������ �������Դϴ�.</div>
<script type="text/javascript">
window.onload = function()
{
	frmSettle.submit();
};
</script>
<?php }?>
</form>
</section>

<?php if($TPL_VAR["settlekind"]!="a"&&($TPL_VAR["settlekind"]!="h"||$TPL_VAR["MobiliansEnabled"]!=true)){?>
<?php echo $TPL_VAR["card_gate"]?>


<script>
	$(document).ready(function(){
		var fm = document.frmSettle; fm.submit();
	});
</script>
<?php }?>

<script>
function swapSettleButton(){
	if (document.getElementById('avoidDblPay')) document.getElementById('avoidDblPay').innerHTML = '<button type="button" class="submit" onclick="submitSettleForm()">�����ϱ�</button></li>\
		<button class="cancel" onclick="history.back();">����ϱ�</button>';
}
function submitSettleForm()
{
	var fm = document.frmSettle;

	if (!chkForm(fm)) return;

	/*** �ֹ��ʼ����� üũ ***/
	if (!fm.nameOrder.value) return;
	if (!fm.ordno.value) return;

	if (document.getElementById('avoidDblPay')) document.getElementById('avoidDblPay').innerHTML = "--- ���� ����ó�����Դϴ�. ��ø� ��ٷ��ּ���. ---<br><a href='javascript:swapSettleButton();'><img src='/shop/data/skin_mobileV2/shop/data/skin_mobileV2/default/common/img/btn_cancel.gif'></a>";

	fm.submit();
}
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>