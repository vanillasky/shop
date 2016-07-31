<?php
include "../lib/library.php";

if(!$session->m_no) {
	msg("�α����ϼž� �� ���񽺸� �̿��Ͻ� �� �ֽ��ϴ�",$sitelink->link("member/login.php"));
	exit;
}

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$mode = $_REQUEST['mode'];

$attd = Core::loader('attendance');

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
	    validation::DEFAULT_KEY => 'text',
	    'comment' => array('html', 'ent_noquotes'),
	));
}

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_GET = validation::xssCleanArray($_GET, array(
	    validation::DEFAULT_KEY => 'text',
	    'comment' => array('html', 'ent_noquotes'),
	));
}

switch($mode) {
    case 'check':
        $attendance_no = (int)$_GET['attendance_no'];
       	
        if($result = $attd->check($attendance_no,$session->m_no)) {
            switch($result) {
                case 'NOT_VALID_ATTENDANCE':
                    msg('��ȿ���� ���� �⼮üũ�Դϴ�');
                break;
				case 'NOT_VALID_DATE':
					msg('�⼮üũ �Ⱓ�� �ƴմϴ�');
				break;
                case 'ALREADY_CHECKED':
                    msg('�̹� �⼮üũ �Ǿ����ϴ�');
                break;
            }
            exit;
        }
        
		$check_message = $attd->get_check_message($attendance_no);
        echo "
        <script>
        alert('{$check_message}');
        parent.location.href=parent.location.href;
        </script>
        ";
		exit;
    break;
    case 'comment_add':
    	$attendance_no=(int)$_POST['attendance_no'];
    	$comment = $_POST['comment'];
    
    	if($result = $attd->check($attendance_no,$session->m_no)) {
		switch($result) {
			case 'NOT_VALID_ATTENDANCE':
				msg('��ȿ���� ���� �⼮üũ�Դϴ�');
			break;
			case 'NOT_VALID_DATE':
				msg('�⼮üũ �̺�Ʈ �Ⱓ�� �ƴմϴ�');
			break;
			case 'ALREADY_CHECKED':
				msg('�̹� �⼮üũ �Ǿ����ϴ�');
			break;
			}
			exit;
		}

		$attd->add_comment($attendance_no,$session->m_no,$comment);
    	
    	$check_message = $attd->get_check_message($attendance_no);
    	
    	if($result=='') {
    		msg($check_message);
    	}
    	else {
    		msg('����� ��ϵǾ����ϴ�');
    	}
    	
    	echo "
    	<script>
    	parent.location.href=parent.location.href;
    	</script>
    	";
    	
    	
    	
    	exit;
    break;
    
}



?>
