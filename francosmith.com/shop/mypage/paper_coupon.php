<?
$noDemoMsg = 1;
include "../_header.php";

## ���� ���� ����
@include "../conf/coupon.php";

## �α�üũ
if(!$sess['m_no'])echo "<script>alert('�α��� �ϼž� �մϴ�.');self.close();</script>";

## ���� ��뿩��
if(!$cfgCoupon['use_yn'])echo "<script>alert('��������� �Ұ� �մϴ�.');self.close();</script>";

$today = date("YmdH");

$query = "
SELECT count(*) cnt
FROM gd_offline_coupon
WHERE `status`!='disuse'
	AND	concat(start_year,start_mon,start_day,start_time) <= '$today'
	AND concat(end_year,end_mon,end_day,end_time) >= '$today'";

list($data) = $db->_select($query);

if($data[cnt] < 1) echo "<script>alert('�Է��Ͻ� ������ �����ϴ�.');self.close();</script>";

$tpl->print_('tpl');
?>