<?
// �ʿ����� �ε�
	include "../lib/library.php";

// �Ķ���� ���� �� ����
	$_GET = $_POST;		// ī�װ�

// Ŭ���� �ε�
	$smartSearch = Core::loader('smartSearch');

// �ش� ī�װ��� �ڵ�
	$smartSearch->menuName = iconv("utf-8", "euc-kr", $_POST['menuName']);
	$smartSearch->searchID = iconv("utf-8", "euc-kr", $_POST['searchID']);
	$smartSearch->loadTheme();
	echo $smartSearch->getOption();
?>
