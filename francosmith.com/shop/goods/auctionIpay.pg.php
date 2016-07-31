<?php

include "../lib/library.php";
include "../lib/load.class.php";
@include "../conf/auctionIpay.cfg.php";
@include "../conf/auctionIpay.pg.cfg.php";
require "../lib/auctionIpay.class.php";
require "../conf/config.php";
include "../lib/cart.class.php";

$orderData = $db->fetch("SELECT * FROM `gd_order` WHERE `ordno`=".$_POST['ordno'], 1);

// �������
switch($orderData['deli_type'])
{
	case '����':
		$shipping_type = 1;
		$shipping_price = 0;
		break;
	case '����': case '�ĺ�':
		$shipping_type = 2;
		$shipping_price = 0;
		break;
	default:
		$shipping_type = 3;
		$shipping_price = $orderData['delivery'];
		break;
}

// Service Url
$serviceurl = ($_SERVER['HTTPS']=='on'?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/shop/partner/auctionIpay_svc.php';

// ������ ����
$buyer_name = $_POST['nameOrder'];
$buyer_tel_no = $_POST['mobileOrder'][0].'-'.$_POST['mobileOrder'][1].'-'.$_POST['mobileOrder'][2];
if(strlen($buyer_tel_no)<1) $buyer_tel_no = $_POST['phoneOrder'][0].'-'.$_POST['phoneOrder'][1].'-'.$_POST['phoneOrder'][2];
$buyer_email = $_POST['email'];

// <ORDER> ������Ʈ�� �Ӽ��� ����
$requestOrderElement = array();
$requestOrderElement['payment_rule']			= $auctionIpayPgCfg['paymentrule'];
$requestOrderElement['pay_price']				= $orderData['settleprice'];
$requestOrderElement['shipping_price']			= $shipping_price;
$requestOrderElement['shipping_type']			= $shipping_type;
$requestOrderElement['back_url']				= $auctionIpayCfg['backurl'];
$requestOrderElement['service_url']				= $serviceurl;
$requestOrderElement['redirect_url']			= ($_SERVER['HTTPS']=='on'?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/shop/partner/auctionIpay_pg.php?ordno='.$_POST['ordno'];
$requestOrderElement['is_address_required']		= 'false';
$requestOrderElement['buyer_name']				= $buyer_name;
$requestOrderElement['buyer_tel_no']			= $buyer_tel_no;
$requestOrderElement['buyer_email']				= $buyer_email;
$requestOrderElement['move_to_redirect_url']	= 'true';

$requestOrderAttribute = array();
foreach($requestOrderElement as $name => $value) $requestOrderAttribute[] = $name.'="'.$value.'"';

// �ֹ���ǰ
$requestItemList = array();

$orderItemResultSet = $db->query("
SELECT `oi`.`sno`, `oi`.`goodsnm`, `oi`.`goodsno`, `oi`.`opt1`, `oi`.`opt2`, `oi`.`addopt`, `oi`.`ea`, `oi`.`price`, `oi`.`memberdc`, `g`.`optnm`, `g`.`img_s`, `g`.`img_l`, `g`.`longdesc`
FROM `gd_order_item` AS `oi`
INNER JOIN `gd_goods` AS `g`
ON `oi`.`goodsno`=`g`.`goodsno`
WHERE `ordno`=".$_POST['ordno'] ." order by oi.sno");

if($_POST['save_mode'] != ''){
	$load_config_ncash = $config->load('ncash');
	$orderData['ncash_emoney'] = $_POST['mileageUseAmount'.$load_config_ncash['api_id']];
	$orderData['ncash_cash'] = $_POST['cashUseAmount'.$load_config_ncash['api_id']];
}

$tmp = array(
	'totGoodsprice' => $orderData['goodsprice'], // �ѻ�ǰ����
	'totUseEmoney' => $orderData['emoney']+$orderData['ncash_emoney']+$orderData['ncash_cash'], // �ѻ��������
	'totDcCoupon' => $orderData['coupon'], // ������������
	'resUseEmoney' => 0,
	'resDcCoupon' => 0,
	'nowItemNo' => 0,
	'endItemNo' => $db->count_($res),
);

while($orderItem = $db->fetch($orderItemResultSet, 1))
{
	$tmp['nowItemNo'] += 1;

	$orderItem = array_map('trim', $orderItem);
	$optionList = array();

	// �Ϲݿɼ� ����
	$optnm = explode('|', $orderItem['optnm']);
	if(strlen($orderItem['opt1'])>0) $optionList[] = strlen(trim($optnm[0]))>0?($optnm[0].':'.$orderItem['opt1']):$orderItem['opt1'];
	if(strlen($orderItem['opt2'])>0) $optionList[] = strlen(trim($optnm[1]))>0?($optnm[1].':'.$orderItem['opt2']):$orderItem['opt2'];

	// �߰��ɼ� ����
	if(strlen($orderItem['addopt'])>0)
	{
		foreach(explode('^', $orderItem['addopt']) as $addopt) $optionList[] = $addopt;
	}

	// ����� ����
	if(strlen($orderItem['img_s'])>0)
	{
		$smallImage = array_shift(explode('|', $orderItem['img_s']));
		if(preg_match('/^http(s?):\/\//', $smallImage)) $thumbnail_url = $smallImage;
		else $thumbnail_url = 'http://'.$_SERVER['SERVER_NAME'].'/shop/data/goods/'.$smallImage;
	}

	// ��ǥ�̹��� ����
	if(strlen($orderItem['img_l'])>0)
	{
		$largeImage = array_shift(explode('|', $orderItem['img_l']));
		if(preg_match('/^http(s?):\/\//', $largeImage)) $item_image_url = $largeImage;
		else $item_image_url = 'http://'.$_SERVER['SERVER_NAME'].'/shop/data/goods/'.$largeImage;
	}

	// ������ǰ DC����(���������, �������ΰ�, ȸ�����ΰ�), ������ǰ ����(DC���� ����� �������� ����) ���
	$goodsprice = $orderItem['price'] * $orderItem['ea'];
	$memberdc = $orderItem['memberdc']*$orderItem['ea'];
	if ($tmp['nowItemNo'] == $tmp['endItemNo'])
	{
		// ��ǰ�� ���������
		$useEmoney = $tmp['totUseEmoney'] - $tmp['resUseEmoney'];
		$tmp['resUseEmoney'] += $useEmoney;

		// ��ǰ�� ��������
		$dcCoupon = $tmp['totDcCoupon'] - $tmp['resDcCoupon'];
		$tmp['resDcCoupon'] += $dcCoupon;
	}
	else {
		// ����
		$percent = round($goodsprice / $tmp['totGoodsprice'], 2);

		// ��ǰ�� ���������
		$useEmoney = intval($percent * $tmp['totUseEmoney']);
		$tmp['resUseEmoney'] += $useEmoney;

		// ��ǰ�� ��������
		$dcCoupon = intval($percent * $tmp['totDcCoupon']);
		$tmp['resDcCoupon'] += $dcCoupon;

	}
	$ipayDcprice = $useEmoney + $dcCoupon + $memberdc;
	$db->query("update ".GD_ORDER_ITEM." set ipay_dcprice='".$ipayDcprice."' where sno='".$orderItem['sno']."'");

	$itemPrice = $goodsprice - $ipayDcprice;

	// <IpayServiceItems> ������Ʈ�� �Ӽ��� ����
	$requestItemElement = array();
	$requestItemElement['item_name']		= $orderItem['goodsnm'].' ('.$orderItem['ea'].'��)';
	$requestItemElement['ipay_itemno']		= $orderItem['goodsno'].'_'.$orderItem['sno'];
	$requestItemElement['item_option_name']	= implode(',', $optionList);
	$requestItemElement['item_price']		= $itemPrice;
	$requestItemElement['order_qty']		= '1';
	$requestItemElement['item_url']			= 'http://'.$_SERVER['SERVER_NAME'].'/shop/goods/goods_view.php?goodsno='.$orderItem['goodsno'].'&inflow=auctionIpay';
	$requestItemElement['thumbnail_url']	= isset($thumbnail_url)?$thumbnail_url:'';
	$requestItemElement['item_image_url']	= isset($item_image_url)?$item_image_url:'';
	$requestItemElement['item_description']	= htmlspecialchars($orderItem['longdesc']);
	$requestItemElement['partner_code']		= 'GODOSOFT';

	$requestItemAttribute = array();
	foreach($requestItemElement as $name => $value) $requestItemAttribute[] = $name.'="'.$value.'"';
	$requestItemList[] = '<IpayServiceItems '.implode(' ', $requestItemAttribute).'/>';
}

// <ORDER>�� <ITEMS>������Ʈ ����
$requestXml = '<ORDER '.implode(' ', $requestOrderAttribute).'/><ITEMS>'.implode($requestItemList).'</ITEMS>';

// �������� iPay�� ����
$requestCartNo = new requestCartNo($auctionIpayCfg['ticket']);
$requestResult = $requestCartNo->doService($requestXml);

// iPay�� �������� �����϶�
if((int)$requestResult['result']>0)
{
	// ��ٱ��Ͽ� �ִ� ��ǰ����� ����ó��
	$cart = new Cart;
	if (is_object($cart) && method_exists($cart, 'buy')) $cart->buy();
?>

<script type="text/javascript">
var ipayPop = null;
try
{
	parent.ipayPop = window.open('about:blank','','scrollbars=yes,toolbar=no,width=500,height=600');
}
catch(e)
{
	alert('�˾��� ���ܵǾ����ϴ�.');
}
if(parent.ipayPop)
{
	parent.ipayPop.location.href="https://ssl.auction.co.kr/ipay/IpayStdOrder.aspx?cartnos=<?php echo $requestResult['result']; ?>&sellerid=<?php echo $auctionIpayCfg['sellerid']; ?>&price=<?php echo $orderData['settleprice']; ?>";
}
</script>

<?php
}
else
{
	$msg = 'iPay ����� �ߴ� �Ͽ����ϴ�.';
	if ($requestResult['msg']) $msg = $requestResult['msg'];
	msg($msg);
}
?>