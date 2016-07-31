<?php
include '../lib/library.php';
include '../conf/payco.cfg.php';
@include '../conf/config.mobileShop.php';
include '../lib/cart.class.php';

//goodsView - ��ǰ��������, goodsCart - ��ٱ���
$checkoutType = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$payco = Core::loader('payco');
if(!$checkoutType) $payco->msgLocate('������ Ÿ���� Ȯ���Ͽ� �ּ���.', 'CHECKOUT', $_GET['isMobile']);
if(!$payco) $payco->msgLocate('payco.class.php ������ Ȯ���� �ּ���.', 'CHECKOUT', $_GET['isMobile']);
if(!function_exists('getordno')) $payco->msgLocate('�ֹ���ȣ�� ������ �� �����ϴ�.', 'CHECKOUT', $_GET['isMobile']);
if(!$payco->check_level('CHECKOUT', $sess)) $payco->msgLocate('ȸ���� ������ ������ ���񽺸� �̿��� �� �����ϴ�.', 'CHECKOUT', $_GET['isMobile']);
$imgPath = $cfg['rootDir'] . '/order/card/payco/img/';

//�ֹ�����
$ordno = getordno();

if($_GET['isMobile']){
	$actionUrl = $cfgMobileShop['mobileShopRootDir']. '/ord/indb.php';
}
else {
	$actionUrl = '../order/indb.php';
}

switch($checkoutType){
	case 'goodsView':
		if(!$_POST['goodsno']) $payco->msgLocate('��ǰ��ȣ�� �����ϴ�.', 'CHECKOUT', $_GET['isMobile']);

		//�ֹ����ɿ��� üũ
		$errorMsg = $payco->check_paycoOrderAble('CHECKOUT', $_POST['goodsno'], $_GET['isMobile']);
		if($errorMsg) $payco->msgLocate($errorMsg, 'CHECKOUT', $_GET['isMobile']);

		// ��ٱ��� ��尡 �ƴϸ�, ���� ��ǰ�� ��ٱ��Ͽ� ��� ó�� �Ѵ�.
		if(!$_COOKIE['gd_isDirect']) setcookie('gd_isDirect', 1, 0, '/');

		$cart = new Cart(1);

		// ��Ƽ�ɼ�
		if ($_POST[multi_ea]) {
			$_keys = array_keys($_POST[multi_ea]);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				$_opt = $_POST[multi_opt][ $_keys[$i] ];
				$_ea = $_POST[multi_ea][ $_keys[$i] ];
				$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
				$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];

				$cart->addCart($_POST[goodsno], $_opt, $_addopt, $_addopt_inputable, $_ea, $_POST[goodsCoupon]);
			}
		}
		else {
			$cart->addCart($_POST[goodsno], $_POST[opt], $_POST[addopt], $_POST[_addopt_inputable], $_POST[ea], $_POST[goodsCoupon]);
		}
	break;

	case 'goodsCart':
		$_GET[idxs] = isset($_GET[idxs]) ? $_GET[idxs] : 'all';

		$cart = new Cart();
		$cart->setOrder($_GET[idxs]);

		foreach($cart->item as $key => $goodsData){
			//�ֹ����ɿ��� üũ
			$errorMsg = $payco->check_paycoOrderAble('CHECKOUT', $goodsData['goodsno'], $_GET['isMobile']);
			if($errorMsg) $payco->msgLocate($errorMsg, 'CHECKOUT');
		}

	break;
}
?>
<html>
<head>
	<style>
	body				{ margin: 0px; padding: 0px; overflow: hidden;}
	.layout				{ width: 100%; height: 600px; text-align: center; }
	.layoutTop			{ width: 100%; text-align: left; padding: 20px 0px 20px 20px; }
	.layoutSolid		{ width: 100%; height:4px; background-color: #ff0008; }
	.progressImage		{ width: 100%; text-align: center; margin-top: 150px;}
	.progressImageSub1	{ margin-top: 39px;}
	.progressImageSub2	{ margin-top: 33px;}
	</style>
</head>
<body>
	<script type="text/javascript">
	window.onload = function(){
		document.paycoForm.submit();
	}
	</script>

	<div class="layout">
		<div class="layoutTop"><img src="<?php echo $imgPath; ?>payco_logo.gif"></div>
		<div class="layoutSolid"></div>
		<div class="progressImage">
			<div><img src="<?php echo $imgPath; ?>payco_img.gif"></div>
			<div class="progressImageSub1"><img src="<?php echo $imgPath; ?>payco_icon_loading.gif"></div>
			<div class="progressImageSub2"><img src="<?php echo $imgPath; ?>payco_text_loading.gif"></div>
		</div>
	</div>

	<form name="paycoForm" method="post" action="<?php echo $actionUrl; ?>">
		<input type="hidden" name="ordno" value="<?php echo $ordno; ?>" />
		<input type="hidden" name="isMobile" value="<?php echo $_GET['isMobile']; ?>" />
		<input type="hidden" name="settlekind" value="t" />
		<input type="hidden" name="paycoType" value="CHECKOUT" />
		<input type="hidden" name="paycoCheckoutType" value="<?php echo $checkoutType; ?>" />
	</form>
</body>
</html>