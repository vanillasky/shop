<?php
/*******************************************************************************************
 * ���̹�üũ�ƿ����� �ֹ�ID�� �߱޹ް� �ֹ������� üũ�ƿ����� �ѱ� �� üũ�ƿ� �������� ��â���� ���
 *******************************************************************************************/
/*
-- -----------------------------------------------------
-- Table `gd_navercheckout`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `gd_navercheckout` (
  `checkoutSno` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `shipping_type` ENUM('PAYED','ONDELIVERY','FREE') NOT NULL ,
  `shipping_price` INT(10) UNSIGNED NOT NULL ,
  `total_price` int(11) UNSIGNED NOT NULL ,
  `regdt` DATETIME NOT NULL ,
  `orderId` varchar(20),
  PRIMARY KEY (`checkoutSno`) )
ENGINE = MyISAM;

-- -----------------------------------------------------
-- Table `gd_navercheckout_item`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `gd_navercheckout_item` (
  `itemSno` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `goodsno` INT(10) UNSIGNED NULL ,
  `checkoutSno` INT(11) UNSIGNED NULL ,
  `goodsnm` VARCHAR(255) NOT NULL ,
  `price` INT(10) UNSIGNED NOT NULL ,
  `ea` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `option` VARCHAR(255) NOT NULL ,
  `stockable` ENUM('y','n') NOT NULL DEFAULT 'n' ,
  `stockstatus` ENUM('y','n') NOT NULL DEFAULT 'n' ,
  `donedt` DATETIME NULL ,
  PRIMARY KEY (`itemSno`) ,
  INDEX `fk_navercheckoutItem_goods` (`goodsno` ASC) ,
  INDEX `fk_navercheckoutItem_navercheckout` (`checkoutSno` ASC) ,
  CONSTRAINT `fk_navercheckoutItem_gd_goods`
    FOREIGN KEY (`goodsno` )
    REFERENCES `gd_goods` (`goodsno` ),
  CONSTRAINT `fk_navercheckoutItem_navercheckout`
    FOREIGN KEY (`checkoutSno` )
    REFERENCES `gd_navercheckout` (`checkoutSno` ))
ENGINE = MyISAM;
*/

include "../lib/library.php";
include "../lib/load.class.php";
@include "../conf/naverChecout.cfg.php";
require "../lib/naverCheckout.class.php";
require "../conf/config.php";
include "../lib/cart.class.php";

class ItemStack {
	var $id;
	var $name;
	var $tprice;
	var $uprice;
	var $option, $option_code;
	var $count;

	//option�� ���� �������, ���õ� �ɼ��� ������(/)�� �����ؼ� ǥ���ϴ� ���� �����Ѵ�.
	function ItemStack($_id, $_name, $_tprice, $_uprice, $_option, $_option_code, $_count) {
		$this->id = $_id;
		$this->name = $_name;
		$this->tprice = $_tprice;
		$this->uprice = $_uprice;
		$this->option = $_option;
		$this->option_code = $_option_code;
		$this->count = $_count;
	}

	function makeQueryString() {
		$ret .= 'ITEM_ID=' . urlencode($this->id);
		$ret .= '&ITEM_NAME=' . urlencode($this->name);
		$ret .= '&ITEM_COUNT=' . $this->count;
		$ret .= '&ITEM_OPTION=' . urlencode($this->option);
		$ret .= '&ITEM_OPTION_CODE=' . urlencode(serialize($this->option_code));
		$ret .= '&ITEM_TPRICE=' . $this->tprice;
		$ret .= '&ITEM_UPRICE=' . $this->uprice;
		$ret .= '&EC_MALL_PID=' . urlencode($this->id);
		return $ret;
	}
};

$NaverCheckout = Core::loader('NaverCheckout');
if(!$NaverCheckout->check_use()) $errmsg="üũ�ƿ� ����� �ߴ� �Ͽ����ϴ�.";

$tblGoods = GD_GOODS;
$tblGoodsOption = GD_GOODS_OPTION;
$tblCheckout = GD_NAVERCHECKOUT;
$tblCheckoutItem = GD_NAVERCHECKOUT_ITEM;

$optEscape = array('/',':');
$addprice = 0;
$totalMoney = 0;

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
$item = $cart->item;
chkOpenYn($item,"D", -1);
foreach($item as $key => $goods){
	$optArr = $optnm = array();
	$query = "select a.goodsno,a.goodsnm,a.img_i,a.img_s,a.img_m,a.img_l,b.price,b.reserve,a.delivery_type,a.goods_delivery,optnm,a.usestock,b.stock,a.runout,a.min_ea,a.max_ea from $tblGoods as a left join $tblGoodsOption as b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1' where a.goodsno='$goods[goodsno]' and opt1='{$goods[opt][0]}' and opt2='{$goods[opt][1]}' limit 1";
	$data = $db->fetch($query);
	$item[$key]['goodsnm'] = $data['goodsnm'];
	$item[$key]['price'] = $data['price'];
	$item[$key]['reserve'] = $data['reserve'];
	if(!$NaverCheckout->check_banWords($data['goodsnm']))$errmsg=$data['goodsnm']."�� üũ�ƿ� ���񽺿� ��밡���� ��ǰ�� �ƴմϴ�.";
	if(!$NaverCheckout->check_exceptions($data['goodsno']))$errmsg=$data['goodsnm']."�� üũ�ƿ� ���񽺿� ��밡���� ��ǰ�� �ƴմϴ�.";
	if($data['optnm'])$optnm = explode('|',$data['optnm']);

	if(!$data['goodsno'])$errmsg="�ùٸ� ������ �ƴմϴ�.";
	if ($data['usestock'] && $data['stock']<$goods['ea'])$errmsg = $data['goodsnm']."�� ��� ���ڶ��ϴ�.";

	if (empty($goods[opt][0])) unset($goods[opt][0]);
	if (empty($goods[opt][1])) unset($goods[opt][1]);

	if(isset($goods['opt'])){
		foreach($goods['opt'] as $k => $v)
		{
			if(!$v)$errmsg="�ɼ��� �������ּ���";
			else $optArr[] = ($optnm[$k])? $optnm[$k].':'.str_replace($optEscape,'',$v) : str_replace($optEscape,'',$v);
		}
	}

	// �ּ�,�ִ뱸�ż���üũ
	$min_ea = $data['min_ea'];
	$max_ea = $data['max_ea'];
	if($goods['ea'] < $min_ea) {
		$errmsg=$data['goodsnm']." ��ǰ�� �ּұ��ż����� {$min_ea}�� �Դϴ�.";
	}
	else if($max_ea!='0' && $goods['ea'] > $max_ea) {
		$errmsg=$data['goodsnm']." ��ǰ�� �ִ뱸�ż����� {$max_ea}�� �Դϴ�.";
	}

	$addprice = 0;
	if(isset($goods['addopt'])){
		foreach($goods['addopt'] as $v)
		{
			if($v['optnm']&&$v['opt'])$optArr[] = str_replace($optEscape,'',$v['optnm']).':'.str_replace($optEscape,'',$v['opt']);
			if($v['price']) $addprice += $v['price'];
		}
	}
	if($optArr) $item[$key]['options'] = implode('/',$optArr);
	if($errmsg) msg($errmsg,-1);
	$item[$key]['tprice'] = ($data['price'] + $addprice) * $goods['ea'];

	if ($goods['delivery_type'] == '3') {
		$onDeliveryShippingPrice += (int)$goods['goods_delivery'];
	}
}

### ��ۺ� ����ϱ�
//marketingType > getDeliveryMode function
$param = array(
'mode' => '0',
'deliPoli' => 0,
'marketingType' => 'naverCheckout'
);


$tmp = getDeliveryMode($param);

if ($onDeliveryShippingPrice > 0 && $tmp['price'] == '0' && ($tmp['type'] === '�ĺ�' || $tmp['type'] === '����')) {
	$tmp['_price'] = $onDeliveryShippingPrice;
	$tmp['type'] = '�ĺ�';
}

if ($tmp['type'] == '����') {

	if ($tmp['price'] > 0) {
		$shippingType = 'PAYED';
		$shippingPrice = $tmp['price'];
	}
	else {
		$shippingType = 'FREE';
		$shippingPrice = 0;
	}
}
else if ($tmp['type'] == '�ĺ�') {

	if ($tmp['_price'] > 0) {
		$shippingType = 'ONDELIVERY';
		$shippingPrice = $tmp['_price'];
	}
	else {

		if ($tmp['on_delivery_each_goods'] === 1 || $tmp['free_each_goods'] === 1 ) {
			$shippingType = 'FREE';
			$shippingPrice = 0;
		}
		else {
			$shippingType = 'ONDELIVERY';
			$shippingPrice = $checkoutCfg['collect'];
		}

	}

}
else if ($tmp['type'] == '����') {
	$shippingType = 'FREE';
	$shippingPrice = 0;
}

$r_deli_msg = array('�⺻���','������','�������','�����������','�������','���������');
foreach($item as $goods){
	$ea = $goods['ea'];

	// ����
	if ($goods['delivery_type'] > 0) {
		$goods['options'] .= ' ('.$r_deli_msg[$goods['delivery_type']];
		if($goods['goods_delivery'] && $goods['delivery_type'] > 1) $goods[options] .= ' '.number_format($goods['goods_delivery'] * ($goods['delivery_type'] == 5 ? $ea : 1)).'��';
		$goods['options'] .= ')';
		if($goods['delivery_type'] == 3) {
			$collect_price += $goods['goods_delivery'];
		}
	}
	// �⺻
	else {
		if ($tmp['default_type_conditional_free'] === 1) {
			$goods['options'] .= ' (���Ǻι���)';
		}
		elseif ($tmp['default_type'] === '�ĺ�' && ($tmp['free_each_goods'] === 1 || $tmp['on_delivery_each_goods'] === 1 )) {
			$goods['options'] .= ' (����';
			$goods['options'] .= ' '.number_format($checkoutCfg['collect']).'��';
			$goods['options'] .= ')';
		}
	}

	### ASCII(�����) ���ڰ� �ִ� ��� ���̹� üũ�ƿ� API���� �ֹ���ȸ�� ���� �ʾ� ��ǰ���� ���۽� ���� - 2015/04
	$not_string = Array('%01','%02','%03','%04','%05','%06','%07','%08','%09','%0A','%0B','%0C','%0D','%0E','%0F','%10','%11','%12','%13','%14','%15','%16','%17','%18','%19','%1A','%1B','%1C','%1D','%1E','%1F');
	$goods['goodsnm'] = urldecode(str_replace($not_string, '', urlencode($goods['goodsnm'])));
	$goods['options'] = urldecode(str_replace($not_string, '', urlencode($goods['options'])));

	### $goods['opt']�� �ֹ��Ϸ� �� ������� ���ȴ�.
	if(isset($goods['opt'])){
		for($os = 0; $os < count($goods['opt']); $os++) {
			$goods['opt'][$os] = urldecode(str_replace($not_string, '', urlencode($goods['opt'][$os])));
		}
	}

	$IS = new ItemStack($goods['goodsno'], strip_tags($goods['goodsnm']), $goods['tprice'], $goods['price'], $goods['options'], $goods['opt'], $ea);
	$totalMoney += $goods['tprice'];
	$item_queryString .= '&'.$IS->makeQueryString();

	### ���� ���� ����
	$arr_query[] = "insert into $tblCheckoutItem set `goodsno`='$data[goodsno]',`goodsnm`='$data[goodsnm]',`price`='$data[price]',`ea`='$ea',`option`='$goods[options]',`stockable`='$stockable'";
}

$backUrl = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$data['goodsno'];
$queryString .= 'SHOP_ID='.urlencode($checkoutCfg['naverId']);
$queryString .= '&CERTI_KEY='.urlencode($checkoutCfg['connectId']);
$queryString .= '&SHIPPING_TYPE='.$shippingType;
$queryString .= '&SHIPPING_PRICE='.$shippingPrice;
$queryString .= '&RESERVE1=&RESERVE2=&RESERVE3=&RESERVE4=&RESERVE5=';
$queryString .= '&BACK_URL='.urlencode($backUrl);
if (!empty($_COOKIE['NVADID'])) $queryString .= '&SA_CLICK_ID='.$_COOKIE["NVADID"]; //CTS
$queryString .= '&CPA_INFLOW_CODE='.urlencode($_COOKIE["CPAValidator"]);
$queryString .= '&NAVER_INFLOW_CODE='.$_COOKIE["NA_CO"];

$totalPrice += (int)$totalMoney;
if($shippingType == 'PAYED') $totalPrice += (int)$shippingPrice;
$queryString .= '&TOTAL_PRICE='.$totalPrice . $item_queryString;

$naverNcash = Core::loader('naverNcash');
if($naverNcash->useyn == 'Y'){
	$queryString .= '&NMILEAGE_INFLOW_CODE='.base64_decode($_COOKIE['NA_MI']);
}


### ���� ���� ����
$checkout_query = "insert into $tblCheckout set `shipping_type`='$shippingType',`shipping_price`='$shippingPrice',`total_price`='$totalPrice',regdt=now()";

if($checkoutCfg['testYn']=='y'){
	$req_host = 'test-checkout.naver.com';
	$req_addr = 'ssl://test-checkout.naver.com';
}else{
	$req_host = 'checkout.naver.com';
	$req_addr = 'ssl://checkout.naver.com';
}
$req_url = 'POST /customer/api/CP949/order.nhn HTTP/1.1'; // euc-kr
//$req_url = 'POST /customer/api/order.nhn HTTP/1.1'; // utf-8
$req_port = 443;

$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
if ($nc_sock) {
	fwrite($nc_sock, $req_url."\r\n" );
	fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
	//fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n");
	fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=CP949\r\n");
	fwrite($nc_sock, "Content-length: ".strlen($queryString)."\r\n");
	fwrite($nc_sock, "Accept: */*\r\n");
	fwrite($nc_sock, "\r\n");
	fwrite($nc_sock, $queryString."\r\n");
	fwrite($nc_sock, "\r\n");

	// get header
	while(!feof($nc_sock)){
		$header=fgets($nc_sock,4096);
		if($header=="\r\n"){
			break;
		} else {
			$headers .= $header;
		}
	}

	// get body
	while(!feof($nc_sock)){
		$bodys.=fgets($nc_sock,4096);
	}

	fclose($nc_sock);

	$resultCode = substr($headers,9,3);

	if ($resultCode == 200) {
		// success
		$orderId = $bodys;
	} else {
		// fail
		msg('���ÿ� �����ϴ� �̿��� ���� ���ų� ��Ʈ��ũ ���°� �Ҿ����Ͽ�\n���� üũ�ƿ� ���� ������ �Ұ��մϴ�.\n�̿뿡 ������ �帰 �� �������� ����帮��, ��� �� �ٽ� ������ �ֽñ� �ٶ��ϴ�.');
		exit;
	}
}
else {
	echo "$errstr ($errno)<br>\n";
	exit(-1);
	//����ó��
}


//���Ϲ��� order_id�� �ֹ��� page�� ȣ���Ѵ�.
//echo ($orderId."<br>\n");

// �ֹ������� ������
if($checkout_query && $arr_query):
	$db->query($checkout_query.",`orderId`='$orderId'");
	$new_sno = $db->lastID();
	foreach($arr_query as $qry)
	{
		$db->query($qry.",`checkoutSno`='$new_sno'");
	}

	if (is_object($cart) && method_exists($cart, 'buy')) {
		$cart->buy();
	}
endif;

if(isset($_GET['isMobile']))
{
	if($checkoutCfg['testYn']=='y') $orderUrl = "https://test-m.checkout.naver.com/mobile/customer/order.nhn";
	else $orderUrl = "https://m.checkout.naver.com/mobile/customer/order.nhn";
}
else
{
	$orderUrl = "https://".$req_host."/customer/order.nhn";
}
?>
<html>
<body>
<form name="frm" method="get" action="<?=$orderUrl?>">
<input type="hidden" name="ORDER_ID" value="<?=$orderId?>">
<input type="hidden" name="SHOP_ID" value="<?=$checkoutCfg['naverId']?>">
<input type="hidden" name="TOTAL_PRICE" value="<?=$totalPrice?>">
</form>
</body>
<script>
<? if ($resultCode == 200) { ?>
<?php if(isset($_GET['isMobile'])){ ?>
document.frm.target = "_self";
document.frm.submit();
<?php }else{ ?>
document.frm.target = "_blank";
document.frm.submit();
<?php } ?>
<? } ?>
</script>
</html>
