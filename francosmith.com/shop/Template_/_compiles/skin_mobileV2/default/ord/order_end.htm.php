<?php /* Template_ 2.2.7 2013/05/28 10:37:12 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/ord/order_end.htm 000005032 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "�ֹ��Ϸ�";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#order_end {background:#FFFFFF; padding:none; margin:none;}
section#order_end {background:#FFFFFF; padding:12px;font-family:dotum;font-size:12px;}
section#order_end .sub_title{height:22px; line-height:22px; color:#436693; font-weight:bold; font-size:12px;}
section#order_end .sub_title .point {width:4px; height:22px; background:url('/shop/data/skin_mobileV2/default/common/img/bottom/icon_guide.png') no-repeat center left; float:left; margin-right:7px;}
section#order_end table{border:none; border-top:solid 1px #dbdbdb;width:100%; margin-bottom:20px;}
section#order_end table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#order_end table th{text-align:center; background:#f5f5f5; width:100px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}

section#order_end table td input[type=text], input[type=password], input[type=email], input[type=number], select{height:21px;}
section#order_end table td textarea{width:95%;height:116px;}
section#order_end .btn_center {margin:auto; width:198px; height:34px; margin-top:20px; margin-bottom:20px;}
section#order_end .btn_center .submit{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_red01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:left; font-family:dotum; line-height:34px;}
section#order_end .btn_center .cancel{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_black01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:right; font-family:dotum; line-height:34px;}

.max_width{width:95%;}
</style>
<section id="order_end">

	<table>
	<col width="100" />
<?php if($TPL_VAR["settlekind"]=="a"){?>
	<tr>
		<th>�Ա�����</h>
		<td><?php echo $TPL_VAR["bank"]?></td>
	</tr>
	<tr>
		<th>�Աݰ���</th>
		<td><?php echo $TPL_VAR["account"]?></td>
	</tr>
	<tr>
		<th>�����ָ�</th>
		<td><?php echo $TPL_VAR["name"]?></td>
	</tr>
	<tr>
		<th>�Ա��ڸ�</th>
		<td><?php echo $TPL_VAR["bankSender"]?></td>
	</tr>
	<tr>
		<th>�Աݱݾ�</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<th>����ī��</th>
		<td><?php echo $_GET["card_nm"]?></td>
	</tr>
	<tr>
		<th>�����ݾ�</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<th>�������</th>
		<td>������ü</td>
	</tr>
	<tr>
		<th>�����ݾ�</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
	<tr>
		<th>�������</th>
		<td><?php echo $TPL_VAR["vAccount"]?></td>
	</tr>
	<tr>
		<th>�����ݾ�</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>��</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="d"){?>
	<tr>
		<th>�������</th>
		<td>�������� ���� (������ ���)</td>
	</tr>
<?php }?>
	<tr>
		<th>��ǰ����</th>
		<td><?php echo number_format($TPL_VAR["goodsprice"])?>��</td>
	</tr>
	<tr>
		<th>��ۺ�</th>
		<td><?php if($TPL_VAR["deli_msg"]){?><?php echo $TPL_VAR["deli_msg"]?><?php }else{?><?php echo number_format($TPL_VAR["delivery"])?>��<?php }?></td>
	</tr>
<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<th>ȸ������</th>
		<td><?php echo number_format($TPL_VAR["memberdc"])?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr>
		<th>��������</th>
		<td><?php echo number_format($TPL_VAR["coupon"])?>��</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
	<tr>
		<th>��������</th>
		<td><?php echo number_format($TPL_VAR["coupon_emoney"])?>��</td>
	</tr>
<?php }?>
	<tr>
		<th>�����ݰ���</th>
		<td><b><?php echo number_format($TPL_VAR["emoney"])?>��</b></td>
	</tr>

	<?php echo $TPL_VAR["NaverMileageAmount"]?>

	</table>

	<table>
	<col width="100" />
	<tr>
		<th>�ֹ���ȣ</th>
		<td><?php echo $TPL_VAR["ordno"]?></td>
	</tr>
	<tr>
		<th>�ֹ��ڸ�</th>
		<td><?php echo $TPL_VAR["nameOrder"]?></td>
	</tr>
	<tr>
		<th>�ֹ�����</th>
		<td><?php echo $TPL_VAR["orddt"]?></td>
	</tr>
	<tr>
		<th>�ֹ��ݾ�</th>
		<td><?php echo number_format($TPL_VAR["settleprice"])?>��</td>
	</tr>
<?php if($TPL_VAR["cashreceipt_useopt"]=='0'||$TPL_VAR["cashreceipt_useopt"]=='1'){?>
	<tr>
		<th>���ݿ�����</th>
		<td>
<?php if($TPL_VAR["cashreceipt_useopt"]=='0'){?>
			�ҵ������
<?php }else{?>
			����������
<?php }?>
			���ݿ����� ��û
		</td>
	</tr>
<?php }?>
	</table>
</section>

<?php echo $TPL_VAR["NaverOrderCompleteData"]?>


<?php $this->print_("footer",$TPL_SCP,1);?>