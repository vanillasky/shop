<?

@include "../lib/library.php";
@include "../conf/config.php";
@include dirname(__FILE__)."/interpark.class.php";

switch ($_GET['mode']){

	case "orderListForMulti": # �ֹ�������ȸ
	case "orderListDelvForMulti": # �ֹ�������ȸ2
	case "cnclNClmReqListForMulti": # �ֹ����/��ǰ/��ȯ��û����Ʈ
	case "clmListForMulti": # Ŭ���Ӹ���Ʈ
	case "orderCompListForMulti": # ����Ȯ����ȸ
		include dirname(__FILE__)."/interpark.i2e_openstyle_order.class.php";
		$i2e_order_api = new i2e_order_api();
		break;
}

?>