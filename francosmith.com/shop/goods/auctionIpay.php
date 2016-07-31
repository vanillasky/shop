<?php
/*******************************************************************************************
 * ����iPay���� �ֹ�ID�� �߱޹ް� �ֹ������� iPay���� �ѱ� �� iPay �������� ��â���� ���
 *******************************************************************************************/
/*
-- -----------------------------------------------------
-- Table `gd_auctionipay`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `gd_auctionipay` (
  `ipaysno` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `ipaycartnos` varchar(20),
  `paymentrule` INT NOT NULL ,
  `payprice` int(11) UNSIGNED NOT NULL ,
  `shippingprice` INT(10) UNSIGNED NOT NULL ,
  `shippingtype` INT NOT NULL ,
  `backurl` VARCHAR(300) NULL ,
  `serviceurl` VARCHAR(300) NULL ,
  `redirecturl` VARCHAR(300) NULL ,
  `stepstock` CHAR(1) NULL,
  `buyername` VARCHAR(30) NULL,
  `auctionpayno` BIGINT(20) NULL,
  `paymenttype` CHAR(1) NULL,
  `expiredate` DATE NULL,
  `regdt` DATETIME NULL ,
  PRIMARY KEY (`ipaySno`) )
ENGINE = MyISAM;

-- -----------------------------------------------------
-- Table `gd_auctionipay_item`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `gd_auctionipay_item` (
  `itemsno` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `ipaysno` INT(11) UNSIGNED NULL ,
  `goodsno` INT(10) UNSIGNED NULL ,
  `goodsnm` VARCHAR(255) NOT NULL ,
  `price` INT(10) UNSIGNED NOT NULL ,
  `ea` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `option` VARCHAR(255) NOT NULL ,
  `optionsno` INT(11) NOT NULL ,
  `stockable` ENUM('y','n') NOT NULL DEFAULT 'n' ,
  `stockstatus` ENUM('y','n') NOT NULL DEFAULT 'n' ,
  `responsetype` TINYINT(4) NULL ,
  `canceldate` DATETIME NULL,
  `regdt` DATETIME NULL ,
  PRIMARY KEY (`itemSno`) ,
  INDEX `fk_auctionipay_goods` (`goodsno` ASC) ,
  INDEX `fk_auctionipayItem_auctionipay` (`ipaysno` ASC) ,
  CONSTRAINT `fk_auctionipayItem_gd_goods`
    FOREIGN KEY (`goodsno` )
    REFERENCES `gd_goods` (`goodsno` ),
  CONSTRAINT `fk_auctionipayItem_auctionipay`
    FOREIGN KEY (`ipaysno` )
    REFERENCES `gd_auctionipay` (`ipaysno` ))
ENGINE = MyISAM;
*/

include "../lib/library.php";
include "../lib/load.class.php";
@include "../conf/auctionIpay.cfg.php";
require "../lib/auctionIpay.class.php";
require "../conf/config.php";
include "../lib/cart.class.php";

$auctionIpay = Core::loader('auctionIpay');
if (!$auctionIpay->check_use()) $errmsg="iPay ����� �ߴ� �Ͽ����ϴ�.";

$tblGoods = GD_GOODS;
$tblGoodsOption = GD_GOODS_OPTION;
$tbIpay = 'gd_auctionipay';
$tbIpayItem = 'gd_auctionipay_item';

$optEscape = array('/',':');
$addprice = 0;
$totalPrice = 0;

if($_GET[mode]!='cart'){

	// ��ٱ��� ��尡 �ƴϸ�, ���� ��ǰ�� ��ٱ��Ͽ� ��� ó�� �Ѵ�.
	$_COOKIE['gd_isDirect'] = 1;
	$cart = new Cart($_COOKIE['gd_isDirect']);	// isdirect = 1

	// ��Ƽ�ɼ�
	if ($_POST[multi_ea]) {
		$_keys = array_keys($_POST[multi_ea]);
		for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
			$_opt = $_POST[multi_opt][ $_keys[$i] ];
			$_ea = $_POST[multi_ea][ $_keys[$i] ];
			$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
			$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];
			$cart->addCart($_POST[goodsno],$_opt,$_addopt,$_addopt_inputable,$_ea,$_POST[goodsCoupon]);
		}
	}
	else {
		$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],$_POST[_addopt_inputable],$_POST[ea],$_POST[goodsCoupon]);
	}
}else{
	$cart = new Cart;
}

$_GET[idxs] = isset($_GET[idxs]) ? $_GET[idxs] : 'all';

$cart->setOrder($_GET[idxs]);
$cartitem = $cart->item;
chkOpenYn($cartitem,"D", 'close');
foreach($cartitem as $key => $goods){
    $optArr = $optnm = array();
    $query =	" SELECT a.goodsno, a.goodsnm, a.img_s, b.price, a.delivery_type, a.goods_delivery, optnm, a.usestock, b.stock, a.runout, a.longdesc, b.optno AS optionsno ";
	$query .=	" FROM $tblGoods AS a LEFT JOIN $tblGoodsOption AS b ON a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1' ";
	$query .=	" WHERE a.goodsno='$goods[goodsno]' AND opt1='{$goods[opt][0]}' AND opt2='{$goods[opt][1]}' LIMIT 1";
    $data = $db->fetch($query);

    if (!$auctionIpay->check_banWords($data['goodsnm'])) $errmsg=$data['goodsnm']."�� iPay ���񽺿� ��밡���� ��ǰ�� �ƴմϴ�.";
    if (!$auctionIpay->check_exceptions($data['goodsno'])) $errmsg=$data['goodsnm']."�� iPay ���񽺿� ��밡���� ��ǰ�� �ƴմϴ�.";
    if ($data['optnm']) $optnm = explode('|',$data['optnm']);
    if (!$data['goodsno']) $errmsg="�ùٸ� ������ �ƴմϴ�.";
    if ($data['usestock'] && $data['stock']<$goods['ea']) $errmsg = $data['goodsnm']."�� ��� ���ڶ��ϴ�.";

    if (isset($goods['opt'])){
		foreach($goods['opt'] as $optKey => $optVal)
		{
			if (!$optVal);
			else $optArr[] = ($optnm[$optKey])? $optnm[$optKey].'-'.str_replace($optEscape,'',$optVal) : str_replace($optEscape,'',$optVal);
		}
		unset($optKey, $optVal);
    }

	$addprice = 0;
    if (isset($goods['addopt'])){
		foreach($goods['addopt'] as $optVal)
		{
			$optMsg = '';
			if ($optVal['optnm'] && $optVal['opt']) $optMsg = str_replace($optEscape,'',$optVal['optnm']).'-'.str_replace($optEscape,'',$optVal['opt']);
			if ($optVal['price']) {
				$addprice += $optVal['price'];
				$optMsg .= ' ('.$optVal['price'].')��';
			}
			if ($optMsg) $optArr[] = $optMsg;

		}
		unset($optVal);
    }
    if ($errmsg) msg($errmsg,-1);

	$items[$key]['goodsno'] = $data['goodsno'];
    $items[$key]['goodsnm'] = str_replace(array('\'', '`', '^', '*', '|', '\n', '\0'), '', strip_tags($data['goodsnm']));
    $items[$key]['price'] = $data['price'] + $addprice;
	$items[$key]['ea'] = $goods['ea'];
	$items[$key]['tprice'] = ($data['price'] + $addprice) * $goods['ea'];
	$items[$key]['goods_delivery'] = $data['goods_delivery'];
	$items[$key]['delivery_type'] = $data['delivery_type'];
	$items[$key]['longdesc'] = $data['longdesc'];
	$items[$key]['optionsno'] = $data['optionsno'];
	$items[$key]['stockable'] = ($data['usestock'] == 'o')? 'y' : 'n';

    if ($optArr) $items[$key]['options'] = implode('/',$optArr);

	// ����Ʈ �̹���.
	$items[$key]['thumbnailurl'] = '';
	if ($data['img_s']) {
		$tmpImg = array_shift(explode('|', $data['img_s']));
		if (preg_match('/^http(s)?:\/\//',$tmpImg))
			$items[$key]['thumbnailurl'] = $tmpImg;
		else
			$items[$key]['thumbnailurl'] = 'http://'.$_SERVER['SERVER_NAME'].'/shop/data/goods/'.$tmpImg;
	}

}
unset($data, $cartitem);

### ��ۺ� ����ϱ�
//marketingType > getDeliveryMode function
$param = array(
'mode' => '0',
'deliPoli' => 0,
'marketingType' => 'auctionIpay'
);


$deliveryMode = getDeliveryMode($param);

if (empty($items) === false && is_array($items)) {
	for($i = 0; $i < count($items); $i++) {
		if (!$deliveryMode['freeDelivery']){
			$r_deli_msg = array('�⺻���','������','�������','�����������','�������','���������');
			if ($items[$i]['delivery_type'] > 0){
				if ($items[$i]['options']) $items[$i]['options'] .= '/'.$r_deli_msg[$items[$i]['delivery_type']];
				else $items[$i]['options'] = $r_deli_msg[$items[$i]['delivery_type']];
				if ($items[$i]['goods_delivery'] && $items[$i]['delivery_type'] > 1) $items[$i]['options'] .= ' ('.number_format($items[$i]['goods_delivery']).'�� ����)';
				if ($items[$i]['delivery_type'] == 3) $collect_price += $items[$i]['goods_delivery'];
			}
		}
		$totalPrice += $items[$i]['tprice'];

		### ���� ���� ����
		$itemSql[] = "INSERT INTO $tbIpayItem SET `goodsno`='".$items[$i]['goodsno']."', `goodsnm`='".$items[$i]['goodsnm']."', `price`='".$items[$i]['price']."', `ea`='".$items[$i]['ea']."', `option`='".$items[$i]['options']."', `optionsno`='".$items[$i]['optionsno']."', `stockable`='".$items[$i]['stockable']."', `regdt`=now()";
	}
}

if ($deliveryMode['freeDelivery']) {
    $shippingType = '1';
    $shippingPrice = 0;
}
elseif ($deliveryMode['type'] == "�ĺ�" || $deliveryMode['msg'] == '���� ���� ��ۺ�' || ($collect_price && !$deliveryMode['price'])) {
    $shippingType = '2';
    $shippingPrice = 0;
}
else {
    $shippingType = '3';
	$shippingPrice = (int)$deliveryMode['price'];
	$totalPrice += (int)$shippingPrice;
}

//echo $shippingType . ' / ' . $shippingPrice;

/*
' ��ǰ�������� �ۼ�
	$ticket = API ����Ƽ��;
	$price = ��ǰ����;
	$sellerid = �̿��� ���̵�
*/
$ticket = $auctionIpayCfg['ticket'];
$price = $totalPrice;
$sellerid = $auctionIpayCfg['sellerid'];
$serviceurl = 'http://'.$_SERVER['HTTP_HOST'].'/shop/partner/auctionIpay_svc.php';

/*	��ǰ���� xml ���� */
$orderQuery = "<ORDER payment_rule='".$auctionIpayCfg['paymentrule']."' pay_price='".$price."' shipping_price='".$shippingPrice."' shipping_type='".$shippingType."' back_url='".$auctionIpayCfg['backurl']."' service_url='".$serviceurl."' ";
if ($auctionIpayCfg['redirecturl']) $orderQuery .= "redirect_url='".$auctionIpayCfg['redirecturl']."'";
$orderQuery .= " />";
$itemsQuery = "<ITEMS>";

foreach($items as $goods) {
	$itemsQuery = $itemsQuery
		. "<IpayServiceItems item_name='" . $goods['goodsnm'] . "'"
		. " ipay_itemno='" . $goods['goodsno'].'_'.$goods['optionsno'] . "' item_option_name='" . $goods['options'] . "' item_price='" . $goods['price'] . "' order_qty='" . $goods['ea'] . "'"
		. " item_url='http://" . $_SERVER['SERVER_NAME'] . '/shop/goods/goods_view.php?goodsno=' . $goods['goodsno'] . '&inflow=auctionIpay' . "' thumbnail_url='" . $goods['thumbnailurl'] . "' item_image='' item_description='" . strip_tags(str_replace(array('\'', '\n', '\r'), array('"', '', ''), $goods['longdesc'])) . "'"
		. " partner_code='GODOSOFT' /> ";
}

$orderQuery = $orderQuery . $itemsQuery . "</ITEMS>";
//echo $orderQuery;
$requestCartNo = new requestCartNo($auctionIpayCfg['ticket']);
$requestResult = $requestCartNo->doService($orderQuery);							// ���񽺸� ȣ���ϰ� �� īƮ��ȣ�� �����´�
//echo "CartNo = " . $requestResult['result'];

if ((!empty($requestResult['result'])) && ($requestResult['result'] > 0))
{
	### ���� ���� ����
	$ipaySql =	"INSERT INTO ".$tbIpay." SET `ipaycartnos`='".$requestResult['result']."', `paymentrule`=".$auctionIpayCfg['paymentrule'].", `payprice`=".$price.", `shippingtype`=".$shippingType.", `shippingprice`=".$shippingPrice.", `backurl`='".$auctionIpayCfg['backurl']."', `serviceurl`='".$serviceurl."', `redirecturl`='".$auctionIpayCfg['redirecturl']."', `regdt`=NOW()";
	$db->query($ipaySql);
	$new_sno = $db->lastID();

	foreach($itemSql as $val) {
		if ($val) $db->query($val.",ipaysno=".$new_sno);
	}

	if (is_object($cart) && method_exists($cart, 'buy')) {
		$cart->buy();
	}
?>
<html>
<body>
<form name="frm" method="get" action="https://ssl.auction.co.kr/ipay/IpayStdOrder.aspx">
<input type="hidden" name="cartnos" value="<?php echo $requestResult['result'] ?>">
<input type="hidden" name="sellerid" value="<?php echo $sellerid ?>">
<input type="hidden" name="price" value="<?php echo $price ?>">
</form>
</body>
<script>
	if (parent.ipayPop) {
		parent.ipayPop.location.href="https://ssl.auction.co.kr/ipay/IpayStdOrder.aspx?cartnos=<?php echo $requestResult['result'] ?>&sellerid=<?php echo $sellerid ?>&price=<?php echo $price ?>";
	}
	else {
		document.frm.target = "_blank";
		document.frm.submit();
	}
</script>
</html>
<?php
}
else {
?>
<script>
if (parent.ipayPop) parent.ipayPop.close();
</script>
<?
	$msg = 'iPay ����� �ߴ� �Ͽ����ϴ�.';
	if ($requestResult['msg']) $msg = $requestResult['msg'];
	msg($msg);
}
?>
