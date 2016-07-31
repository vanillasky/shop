<?
if(!preg_match('/^[0-9]*$/',$_GET['category'])) exit;

### �����Ҵ�
$goodsno = $_GET['goodsno'];

include "../_header.php";
include "../conf/config.pay.php";
@include "../conf/coupon.php";
@include "../conf/config.plusCheeseCfg.php"; //�÷���ġ��
require "../lib/load.class.php";
require "../lib/nateClipping.class.php";
require "../lib/plusCheese.class.php";
@include "../conf/naverCheckout.cfg.php";
@include "../conf/auctionIpay.cfg.php";
@include "../conf/sns.cfg.php";
@include "../conf/qr.cfg.php";
include "../lib/cart.class.php";
@include "../../shop/setGoods/data/config/setGoodsConfig.php";
include '../lib/Lib_Robot.php';

if (is_file("../conf/config.related.goods.php")) include "../conf/config.related.goods.php";
else {
	// �⺻ ���� ��
	$cfg_related['horizontal'] =  5;
	$cfg_related['vertical'] =  1;
	$cfg_related['size'] =  $cfg[img_s];

	$cfg_related['dp_image'] = 1;	// ����
	$cfg_related['dp_goodsnm'] =  1;
	$cfg_related['dp_price'] = 1;
	$cfg_related['dp_shortdesc'] = $cfg[img_s];

	$cfg_related['use_cart'] = 0;
	$cfg_related['cart_icon'] = 1;

	$cfg_related['exclude_soldout'] =  0;
	$cfg_related['link_type'] = 'self';
}
if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
	include dirname(__FILE__) . "/../conf/config.soldout.php";

if(!$set['emoney']['cut'])$set['emoney']['cut']=0;
$set['emoney']['base'] = pow(10,$set['emoney']['cut']);

//��ǰ���䰳��
list ($review_count) = $db->fetch("select count(*) as cnt from gd_goods_review where goodsno = '".$goodsno."'");
//��ǰ���ǰ���
list ($qna_count) = $db->fetch("select count(*) as cnt from gd_goods_qna where goodsno = '".$goodsno."'");

try {

	$goodsHelper   = Clib_Application::getHelperClass('front_goods');

	$goodsModel    = $goodsHelper->getGoodsModel(Clib_Application::request()->get('goodsno'));
	if (!$goodsModel->hasLoaded()) {
		throw new Clib_Exception('��ǰ������ �����ϴ�.');
	}

	$categoryModel = $goodsHelper->getCategoryModel(Clib_Application::request()->get('category'), $goodsModel);

	// ���� ������ �ʿ��� ��ǰ�� ���
	if ($goodsModel->getUseOnlyAdult() && !Clib_Application::session()->canAccessAdult()) {
		Clib_Application::response()->redirect(
			$goodsHelper->getGoodsViewUrl($goodsModel)
		);
	}

	// ī�װ� ���� ����, ���� ���� üũ
	if (!$goodsHelper->canAccessLinkedCategory($goodsModel)) {
		throw new Clib_Exception('�̿� ������ �����ϴ�.\\nȸ������� ���ų� ȸ�������� �ʿ��մϴ�.');
	}

	// ��ǰ ���� ���� üũ
	if(!$goodsModel->getData('open')) {
		Clib_Application::response()->jsAlert('�ش��ǰ�� ������ ���� ��ǰ�� �ƴմϴ�.')->historyBack();
	}

	// ������ũ ���𷺼�
	if (strpos($_SERVER['HTTP_HOST'], ".godo.interpark.com") !== false) {
		$url = sprintf("http://www.interpark.com/product/MallDisplay.do?_method=detail&sc.shopNo=0000100000&sc.dispNo=%s&sc.prdNo=%s", $goodsModel['inpk_dispno'], $goodsModel['inpk_prdno']);
		Clib_Application::response()->redirect($url);
	}
	else if ( ! $goodsModel->hasLoaded() && Clib_Application::cookie()->get('cc_inflow') == 'openstyleOutlink') {
		Clib_Application::response()->jsAlert('�ش��ǰ�� ������ ���� ��ǰ�� �ƴմϴ�.')->redirect('/shop');
	}
	else {
		// nothing to do;
	}

	// ������ȯ�� ��Ű
	$params = array(
		'nv_pchs' => Clib_Application::request()->get('nv_pchs'),
		'ref' => Clib_Application::request()->get('ref'),
		'clickid' => Clib_Application::request()->get('clickid'),
		'clickDate' => Clib_Application::request()->get('clickDate'),
		'clickNo' => Clib_Application::request()->get('clickNo'),
		'adType' => Clib_Application::request()->get('adType'),
		'cpcType' => Clib_Application::request()->get('cpcType'),
	);
	$goodsHelper->setMarketingCookies($params);

// �������� ī����
if (Lib_Robot::isRobotAccess() === false) {
	$db->silent(true);
	$db->query("INSERT INTO ".GD_GOODS_PAGEVIEW." SET date = CURDATE(), goodsno = $goodsno, cnt = 1 ON DUPLICATE KEY UPDATE cnt = cnt + 1");
	$db->silent();
}

	$data = $goodsHelper->getGoodsDataArray($goodsModel, $categoryModel);

	$tpl->assign(array('clevel'	=> $categoryModel->getLevel(),
					   'slevel'=> Clib_Application::session()->getMemberLevel(),
					   'level_auth'=> $categoryModel->getLevelAuth()));

	$tpl->assign('systemHeadTagEnd', $systemHeadTagEnd);

	Clib_Application::storage()->toGlobal();

	### ���ø� ���
	$tpl->assign('cartCfg',$cart = new Cart);
	$tpl->assign($data);
	$tpl->assign('returnUrl', $_SERVER['REQUEST_URI']);
	$tpl->assign('customHeader', $customHeader);
	$tpl->assign('category', $categoryModel->getId());
	$_GET['category'] = $category = $categoryModel->getId();
	$lstcfg = $categoryModel->getConfig();
 	$tpl->assign('lstcfg', $lstcfg);

	$tpl->assign('review_count', $review_count);
	$tpl->assign('qna_count', $qna_count);

	if (Clib_Application::request()->get('preview') == 'y' && is_file($tpl->template_dir.'/'.'goods/goods_preview.htm')) {
		$tpl->define('tpl','goods/goods_preview.htm');
	}

	if (Clib_Application::storage()->get('about_coupon')) {
		$tpl->assign('about_coupon', Clib_Application::storage()->get('about_coupon'));
	}

	### ���½�Ÿ���� ���
	if(Clib_Application::cookie()->get('cc_inflow') == "openstyleOutlink") {
		echo "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
	}

	$goodsBuyable = getGoodsBuyable($goodsno); 
	$tpl->assign('goodsBuyable', $goodsBuyable);
	$tpl->print_('tpl');

### ���̿��� ��ũ�� ��ũ��Ʈ
$nate = Core::loader('NateClipping');
$nateyn = $nate->chk_goods($_GET['goodsno'],$cfg,&$db);
if($nateyn){
	$src = $nate->get_cyopenscrap($goodsno,$cfg['rootDir']);
?>
<script type="text/javascript">
function open_cyword(){
	var src = "<?php echo $src; ?>";
	window.open(src,'cyopenscrap','width=450,height=410,scrollbars=0');
}
</script>
<?php
	}
}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}

