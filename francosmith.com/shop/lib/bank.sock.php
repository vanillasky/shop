<?

@include "./library.php";
@include "../conf/config.php";

if (!class_exists('cashreceipt', false)) @include (dirname(__FILE__)."/cashreceipt.class.php");
$cashreceipt = new cashreceipt;

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "receiptOfMoney": # �ֹ��Ա�Ȯ�� ó��

		@include dirname(__FILE__)."/./bank.class.php";
		$bk = new Bank( 'receive', $_GET[ordno] );

		break;

	case "filterOrderStep": # �ֹ����±������� ����

		@include dirname(__FILE__)."/./bank.class.php";
		$bk = new Bank( 'filterOrderStep', '' );

		break;
}

?>
