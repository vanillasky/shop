<?
/*------------------------------------------------------------------------------
* Order's Delivery Price Calculate
date		2008/03/25
$_GET['mode']			0:��ٱ���,1:other
$_GET['zipcode']		�����ȣ
$_GET['emoney']			���������
$_GET['deliPoli']		�����å
$_GET['coupon']			�������αݾ�
$_GET['price']			�����ݾ�
------------------------------------------------------------------------------*/
include_once "../lib/library.php";
@include "../conf/config.pay.php";
### �޸� ����
$_GET['coupon'] = str_replace(',','',$_GET['coupon']);
$_GET['emoney'] = str_replace(',','',$_GET['emoney']);

if($_GET[mode] == 'order'){

	$param = array(
	'mode' => '0',
	'zipcode' => $_GET['zipcode'],
	'emoney' => $_GET['emoney'],
	'deliPoli' => $_GET['deliPoli'],
	'coupon' => $_GET['coupon'],
	'coupon_emoney' => $_GET['coupon_emoney'],
	'road_address'	=> $_GET['road_address'],
	'address'	=> $_GET['address'],
	'address_sub'	=> $_GET['address_sub']
	);

}else if($_GET[mode] == 'mypage'){

	$param = array(
	'mode' => '1',
	'zipcode' => $_GET['zipcode'],
	'emoney' => $_GET['emoney'],
	'deliPoli' => $_GET['deliPoli'],
	'coupon' => $_GET['coupon'],
	'coupon_emoney' => $_GET['coupon_emoney'],
	'price' => $_GET['price']
	);
}
$delivery = getDeliveryMode($param);
?>
/*<script>*/	// color coding ���ؼ�.

<? if($delivery[type]=='�ĺ�' && !$delivery[freeDelivery]){ ?>
	document.getElementById('paper_delivery_msg1').style.display='none';
	document.getElementById('paper_delivery_msg2').style.display='block';
	document.getElementById('paper_delivery_msg2').innerHTML = '<?=addslashes($delivery[msg])?>';
	try {
		document.getElementById('paper_delivery_msg_extra').style.display='none';
	}
	catch (e) { }
<? } else { ?>
	document.getElementById('paper_delivery_msg1').style.display='block';
	document.getElementById('paper_delivery_msg2').style.display='none';
	try {
	<? if ($delivery[extra_price] > 0) { ?>
		document.getElementById('paper_delivery_msg_extra').innerHTML = '(������ ��ۺ� <?=number_format($delivery[extra_price])?> �� ����)';
		document.getElementById('paper_delivery_msg_extra').style.display='block';
	<? } else { ?>
		document.getElementById('paper_delivery_msg_extra').style.display='none';
	<? } ?>
	}
	catch (e) { }
<? } ?>
<? if ($delivery['default_name']) { ?>
	try {
		var el = document.getElementById('el-default-delivery');
		var msg = '<?=addslashes($delivery['default_name'])?>';

		<? if ($delivery['default_type_conditional_free']) { ?>
			msg += '<br>(���Ǻι���)';
		<? } else if ($delivery['default_type']=='����') { ?>
			msg += '<br>(<?=number_format($delivery['default_price'])?>��)';
		<? } else if ($delivery['default_type']=='�ĺ�') { ?>
			msg += '<br>(<?=addslashes($delivery[msg])?>)';
		<? } ?>
		el.innerHTML = msg;
	}
	catch (e) { }
<? } ?>

<? if ($delivery['unableMenu']) { ?>
	if(document.getElementById('paper_delivery_menu'))document.getElementById('paper_delivery_menu').style.display='none';
	document.getElementById('paper_delivery_msg1').style.display='none';
	document.getElementById('paper_delivery_msg2').style.display='block';
	document.getElementById('paper_delivery_msg2').innerHTML = '<?=addslashes($delivery[msg])?>';
<? } ?>


document.getElementById('paper_delivery').innerHTML = '<?=number_format($delivery[price])?>';
calcu_settle();