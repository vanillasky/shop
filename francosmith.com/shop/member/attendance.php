<?php
include "../_header.php";

if(!$session->m_no) {
	msg("�α����ϼž� �� ���񽺸� �̿��Ͻ� �� �ֽ��ϴ�",$sitelink->link("member/login.php"));
	exit;
}

// �����۾�     
$attendance_no = (int)$_GET['attendance_no'];
$attd = Core::loader('attendance');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}     


// ��ȿ�� ����
$query = "select * from gd_attendance where attendance_no='{$attendance_no}'";
$result = $db->_select($query);
$attd_info = $result[0];
if($attd_info['manual_stop']!='n') {
	msg("��ȿ���� ���� �⼮üũ�������Դϴ�",-1);
	exit;
}

if($attd_info['check_method']!='stamp' && $attd_info['check_method']!='comment') {
	msg("��ȿ���� ���� �⼮üũ�������Դϴ�",-1);
	exit;
}

$attd_info['int_start_date']=(int)str_replace('-','',$attd_info['start_date']);
$attd_info['int_end_date']=(int)str_replace('-','',$attd_info['end_date']);




// ��� ������ ���ϱ�
$tpl->assign('design_head_type',$attd_info['design_head_type']);
$tpl->assign('design_head_html',$attd_info['design_head_html']);
$tpl->assign('design_head_image',$attd->design_head_image[$attd_info['design_head_image']]);
$tpl->assign('design_head_upload',"../data/attendance/custom/{$attendance_no}_head.jpg");


// �⼮ Ÿ��
$tpl->assign('condition_type',$attd_info['condition_type']);

// �⼮ Ƚ�����ϱ�
$query = $db->_query_print("
	select 
		check_period
	from 
		gd_attendance_check
	where 
		attendance_no=[s] and member_no=[s]
",$attendance_no,$session->m_no);
$result = $db->_select($query);
$result = $result[0];


$tpl->assign('check_period',(int)$result['check_period']);	




if($attd_info['check_method']=='comment') { //�⼮üũ������ �ڸ�Ʈ�� ���
	// �⼮üũ ���ø� �Ҵ�
	$tpl->define('attd',$attd->design_body[$attd_info['design_body']]);
	
	$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;

	$query = $db->_query_print("
		select 
			c.*,
			m.name,m.nickname
		from 
			gd_attendance_comment as c
			left join gd_member as m on c.member_no=m.m_no
		where 
			c.attendance_no=[s]
		order by
			c.comment_no desc
	",$attendance_no);
	$comment_list = $db->_select_page(15,$page,$query);
	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		foreach ($comment_list['record'] as $index => $comment) {
			$comment_list['record'][$index] = validation::xssCleanArray($comment, array(
			    validation::DEFAULT_KEY => 'text',
			    'comment' => array('html', 'ent_noquotes'),
			));
		}
	}
	
	$tpl->assign('comment_list',$comment_list);	
}
else { // �⼮üũ������ �޷����� ���

	// �⼮üũ ���ø� �Ҵ�
	$tpl->define('attd',$attd->design_body[$attd_info['design_body']]);
	
	// ���� ��,�� ���ϱ�
	$yearmonth = (int)$_GET['yearmonth'] ? (int)$_GET['yearmonth'] : (int)date('Ym');
	$year = (int)($yearmonth/100);
	$month = (int)($yearmonth%100);
	$tpl->assign('year',$year);
	$tpl->assign('month',$month);
	
	
	// �⼮üũ�� ��¥�� ���ؼ� �޷� ���ϱ�
	$query = $db->_query_print("
		select 
			*
		from 
			gd_attendance_check
		where 
			attendance_no=[s] and
			member_no=[s]
	",$attendance_no,$session->m_no);
	$result=$db->_select($query);
	$check_info=$result[0];
	$check_info['ar_check_date']=explode(',',$check_info['check_date_all']);
	foreach($check_info['ar_check_date'] as $k=>$v) {
		$check_info['ar_check_date'][$k]=(int)str_replace('-','',$v);
	}
	
	$start_week = (int)date('w',mktime(0, 0, 0, $month,1,$year));
	$number_month_days = (int)date('t',mktime(0, 0, 0,$month,1,$year));
	
	if($start_week) {
		$ar_calendar=array_fill(0,$start_week,null);
	}
	else {
		$ar_calendar=array();
        
	}

	for($i=1;$i<=$number_month_days;$i++) {
		$compare_date = ($year*10000)+($month*100)+$i;
		
		if(in_array($compare_date,$check_info['ar_check_date'])) {
			$ar_calendar[]=array('day'=>$i,'stamp'=>true);
		}
		else {
			$ar_calendar[]=array('day'=>$i,'stamp'=>false);
		}
	}
	$tpl->assign('calendar',$ar_calendar);
	
	
	// �����̹��� ��� ���ϱ�
	if($attd_info['design_stamp']=='default') {
		$tpl->assign('stamp_img_path',"../data/attendance/stamp_img.gif");
	}
	else {
		$tpl->assign('stamp_img_path',"../data/attendance/custom/{$attendance_no}_stamp.jpg");
	}
	
	// ����,������ ��� ���ϱ�
	
	$tpl->assign('prev_month',"?attendance_no={$attendance_no}&yearmonth=".date('Ym',mktime(0, 0, 0,$month-1,1,$year)));
	
	$tpl->assign('next_month',"?attendance_no={$attendance_no}&yearmonth=".date('Ym',mktime(0, 0, 0,$month+1,1,$year)));
    
    
}	




$tpl->assign('attendance_no',$attendance_no);
$tpl->print_('attd');



?>
