<?

include "../_header.php";

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text'
	));
}

if ( $_POST['mode'] == 'send' ){

	$db->query("INSERT INTO ".GD_COOPERATION." ( itemcd, name, email, title, content, regdt ) VALUES ( '" . $_POST['itemcd'] . "', '" . $_POST['name'] . "', '" . $_POST['mail'] . "', '" . $_POST['title'] . "', '" . $_POST['content'] . "', now() )");

	msg( '�����Ͻ� ������ ���۵Ǿ����ϴ�.', $_SERVER[HTTP_REFERER] );
}

// ������������ �� �̿뿡 ���� �ȳ�
$termsPolicyCollection4 = getTermsGuideContents('terms', 'termsPolicyCollection4');
$tpl->assign('termsPolicyCollection4', $termsPolicyCollection4);
$tpl->print_('tpl');

?>