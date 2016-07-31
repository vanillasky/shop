<?php
include '../_header.php';
include '../conf/config.php';
include '../conf/config.cart.php';

$cart = Core::loader('Cart');
$estimate = new estimate();

$idxs = $_POST['idxs'];

// ȸ������ ��������
if ($sess) {
	$memberName = $estimate->getName($sess['m_no']);
}

// üũ �� ��ǰ�� ��������
$item = $estimate->getGoods($cart->item,$idxs);

// ���� ���� ��������
$item = $estimate->getTax($item,$idxs);

// �հ� �ݾ� ���ϱ�
$totalPrice = $estimate->totalPrice($item);

// �հ� �ݾ� �ѱ۷� ��ȯ�ؼ� ��������
$priceKor = $estimate->transNum($totalPrice);

// ���ް��� ���
$item = $estimate->supplyPrice($item,$idxs);

// ���ް��� �ջ�
$totalSupplyPrice = $estimate->totalSupplyPrice($item);

// ��� �޼��� ���Ͱ�
$cartCfg['estimateMessage'] = nl2br($cartCfg['estimateMessage']);

// ��ǰ�� HTML �±� ����
$item = $estimate->tagStrip($item);

// \����
$cartCfg['estimateMessage'] = stripslashes($cartCfg['estimateMessage']);

$tpl->assign('item',$item);
$tpl->assign('cfg',$cfg);
$tpl->assign('cartCfg',$cartCfg);
$tpl->assign('memberName',$memberName);
$tpl->assign('totalPrice',$totalPrice);
$tpl->assign('totalSupplyPrice',$totalSupplyPrice);
$tpl->assign('priceKor',$priceKor);
$tpl->print_('tpl');

?>