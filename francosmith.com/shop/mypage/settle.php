<?php
include '../_header.php';

$ordno = $_GET['ordno'] ? $_GET['ordno'] : $_POST['ordno'];
$reOrder = new reOrder;

// �ֹ��� üũ �Լ� ȣ��
$result = $reOrder->chk_order($ordno);

if ($result['itemCount'] == $result['added'] && $result['price_result'] == 0) {
	msg($result['added']."���� ��ǰ�� ��ٱ��Ͽ� ��ҽ��ϴ�.","../goods/goods_cart.php");
}
else if ($result['itemCount'] == $result['added'] && $result['price_result'] > 0) {
	msg($result['added']."���� ��ǰ�� ��ٱ��Ͽ� ��ҽ��ϴ�. \\n�Ǹ� ������ ����� ��ǰ�� ��ٱ��Ͽ��� Ȯ���Ͻ� �� �ֽ��ϴ�.","../goods/goods_cart.php");
}
else if ($result['added'] > 0 && $result['price_result'] == 0) {
	msg("���ֹ� ������ ��ǰ�� ��ٱ��Ͽ� ��ҽ��ϴ�. \\n(ǰ��, �ɼǺ��� ��ǰ�� ���ֹ� �Ұ�)","../goods/goods_cart.php");
}
else if ($result['added'] > 0 && $result['price_result'] > 0) {
	msg("���ֹ� ������ ��ǰ�� ��ٱ��Ͽ� ��ҽ��ϴ�. \\n(ǰ��, �ɼǺ��� ��ǰ�� ���ֹ� �Ұ�)\\n�Ǹ� ������ ����� ��ǰ�� ��ٱ��Ͽ��� Ȯ���Ͻ� �� �ֽ��ϴ�.","../goods/goods_cart.php");
}
else if ($result['added'] == 0) {
	msg("�Ǹ��ߴ�, �ɼ� �������� ��� ��ǰ�� �� �ֹ��� �Ұ��� �մϴ�.",-1);
}
else {}

?>