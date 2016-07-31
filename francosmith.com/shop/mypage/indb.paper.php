<?
include "../lib/library.php";

## �α�üũ
if(!$sess['m_no']){
	echo "<script>alert('�α��� �ϼž� �մϴ�.');self.close();</script>";
	exit;
}

$m_no = (int) $sess[m_no];
$number = $db->_escape(implode('-',$_POST['coupon_number']));

$today = date("YmdH");

$query = "
SELECT coupon.*,paper.sno paper_sno,down.sno download_sno,down.m_no
FROM gd_offline_coupon coupon,gd_offline_paper paper
	LEFT JOIN gd_offline_download down ON paper.sno=down.paper_sno AND down.m_no='$m_no'
WHERE coupon.sno=paper.coupon_sno
	AND paper.number='$number'
	AND CONCAT(coupon.start_year,coupon.start_mon,coupon.start_day,coupon.start_time) <= '$today'
	AND CONCAT(coupon.end_year,coupon.end_mon,coupon.end_day,coupon.end_time) >= '$today'
	AND coupon.`status`!='disuse'";

list($arCoupon) = $db->_select($query);

if(!$arCoupon[sno]||!$arCoupon[paper_sno]){
	echo "<script>alert('�ùٸ��� ���� ������ȣ �Դϴ�.');self.close();</script>";
	exit;
}

if($arCoupon['number_type']=='auto' && $arCoupon['download_sno']){
	echo "<script>alert('���� ������ȣ �Դϴ�.');self.close();</script>";
	exit;
}

if($arCoupon['publish_limit']=='limited' && $arCoupon['limit_paper'] > 0){
	$query = "SELECT count(*) cnt FROM gd_offline_download WHERE coupon_sno='$arCoupon[sno]'";
	list($arDowload) = $db->_select($query);
	if($arDowload['cnt'] >= $arCoupon['limit_paper']){
		echo "<script>alert('���� ��뷮�� �ʰ� �Ǿ����ϴ�.');self.close();</script>";
		exit;
	}
}

$query = "select count(*) from gd_offline_download down,
				gd_offline_paper paper,
				gd_offline_coupon coupon
			where down.m_no='$sess[m_no]'
				AND down.paper_sno=paper.sno
				AND paper.coupon_sno=coupon.sno
				AND coupon.sno='$arCoupon[sno]'";
list($cnt) = $db->fetch($query);
if($cnt){
	echo "<script>alert('�̹� �Է��Ͻ� ���� �Դϴ�.');self.close();</script>";
	exit;
}

if($arCoupon['number_type'] != 'duplication'){
	$query = "select count(*) from gd_offline_download where paper_sno='$arCoupon[paper_sno]'";
	list($cnt) = $db->fetch($query);
	if($cnt){
		echo "<script>alert('�̹� �Է��Ͻ� ���� �Դϴ�.');self.close();</script>";
		exit;
	}
}


$query = "insert into gd_offline_download set coupon_sno='$arCoupon[sno]',paper_sno='$arCoupon[paper_sno]',m_no='$m_no',regdt=now(),updatedt=now()";
$db->query($query);

$query = "update gd_offline_coupon set `status`='done',updatedt=now() where sno='$arCoupon[sno]'";
$db->query($query);

echo "<script>alert('������ȣ�� ���� �Ǿ����ϴ�..');self.close();</script>";
?>
