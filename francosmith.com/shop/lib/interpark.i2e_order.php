<?

@include "../lib/library.php";
@include "../conf/config.php";
@include dirname(__FILE__)."/interpark.class.php";

switch ($_GET['mode']){

	case "orderListForComm": # �ֹ�������ȸ
	case "orderListDelvForComm": # �ֹ�������ȸ2
	case "cnclNClmNExchReqListForComm": # �ֹ����/��ǰ/��ȯ��û����Ʈ
	case "clmListForComm": # Ŭ���Ӹ���Ʈ
	case "orderCompListForComm": # ����Ȯ����ȸ
		include dirname(__FILE__)."/interpark.i2e_order.class.php";
		$i2e_order_api = new i2e_order_api();
		break;
}

?>