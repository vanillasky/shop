<?
$noDemoMsg = 1;
include "../_header.php";
include "../lib/cart.class.php";
## ���� ���� ����
@include "../conf/config.pay.php";
@include "../conf/coupon.php";

if(!$cfgCoupon['use_yn']) $cfgCoupon['use_yn'] = 0;	// ���� ��뿩��(0:����������� 1:���)
if(!$cfgCoupon['range']) $cfgCoupon['range'] = 0;	// �ߺ����ο���(0:��������, ȸ������ ���û��  1:ȸ�����θ� ��� 2: �������θ� ���)
if(!$cfgCoupon['double']) $cfgCoupon['double'] = 0;	// ���� �������(0:�� �ֹ��� ���� ���� ��밡��   1:�� �ֹ��� �Ѱ� ������ ���)

## ȸ������ ���뿩��
if(!$cfgCoupon['use_yn'] || ($cfgCoupon['range'] != '2' && $cfgCoupon['use_yn']))$ableDc = true;
else $ableDc = false;

## ���� ���뿩��
if($cfgCoupon['range'] != '1' && $cfgCoupon['use_yn'])$ableCoupon = true;
else $ableCoupon = false;

$Cart = Core::loader('Cart', $_COOKIE[gd_isDirect]);
$Goods = Core::loader('Goods');
$coupon_price = Core::loader('coupon_price');
$coupon_price->set_config($cfgCoupon);

if($Cart -> item)foreach($Cart -> item as $v) {
	if($abledc) $dc = getDcPrice($v[price],$Cart->dc);
	else $dc = 0;
	$arCategory = $Goods->get_goods_category($v['goodsno']);
	$coupon_price->set_item($v['goodsno'],$v['price'],$v['ea'],$arCategory,$v['opt'][0],$v['opt'][1],$v['addopt'],$v['goodsnm']);
	$goodsPrice += ($v['price'] + $v['addprice']) * $v['ea'];
}

$coupon_price->get_goods_coupon('order');
if($coupon_price->arCoupon)foreach($coupon_price->arCoupon as $data){
	if($data['excPrice'] && $data['excPrice'] > $goodsPrice) continue;
	if($data['pay_limit'] == "limited" && $data['limit_amount'] && $goodsPrice < $data['limit_amount']) continue;
	$data['apr']=0;
	if($data['sale']) $data['apr'] = @array_sum($data['sale']);
	if($data['reserve']) $data['apr'] = @array_sum($data['reserve']);
	$data['pay_method'] = $data['payMethod'] ? $data['payMethod'] : $data['pay_method'];
	$loop[] = $data;
}
$cart->dc = $member[dc]."%";
if(!$sess['m_no'])	echo("<script>alert('ȸ���� ��������� �����մϴ�.!');self.close();</script>");
if(!$ableCoupon)	echo("<script>alert('��������� �Ұ� �մϴ�.!');self.close();</script>");

$tpl->assign(array('cart' => $cart));
$tpl->print_('tpl');
?>
