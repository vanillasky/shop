<?
class Partner
{
	function getGodoCfg(){
		global $db;
		$file	= dirname(__FILE__)."/../conf/godomall.cfg.php";
		$file	= file($file);
		$godo	= decode($file[1],1);
		return $godo;
	}

	function getBasicDc(){
		global $db;
		### �⺻ ȸ�� ������
		@include dirname(__FILE__)."/../conf/fieldset.php";
		if($joinset['grp'] != ''){
			$memberdc = $db->fetch("select dc,excep,excate from ".GD_MEMBER_GRP." where level='".$joinset['grp']."' limit 1");
		}
		return $memberdc;
	}

	function getCatnm(){
		global $db;
		### ī�װ��� �迭
		$query = "select catnm,category from ".GD_CATEGORY;
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $catnm[$data['category']] = $data['catnm'];
		return $catnm;
	}

	function getGoodsSql(){
		// ��ǰ ����Ÿ
		$query = "SELECT a.* , b.category ,d.brandnm FROM ".GD_GOODS." a ";
		$query .= "LEFT JOIN ".GD_GOODS_BRAND." d on a.brandno=d.sno, ";
		$query .= "(SELECT c.goodsno, ".getCategoryLinkQuery('c.category', null, 'max')." FROM ".GD_GOODS_LINK." c left join gd_category gc on gc.category=left(c.category,3) WHERE c.category!='' and gc.category is not null GROUP BY c.goodsno) b ";
		$query .= "WHERE a.goodsno=b.goodsno ";
		$query .= "AND a.open=1 ";
		$query .= "AND !( a.runout = 1 OR (a.usestock = 'o' AND a.usestock IS NOT NULL AND a.totstock < 1) ) ";
		$query .= "AND ( (a.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < a.sales_range_end) ";
		$query .= "OR (a.sales_range_start < UNIX_TIMESTAMP() AND a.sales_range_end = '') ";
		$query .= "OR (UNIX_TIMESTAMP() < a.sales_range_end AND a.sales_range_start = '') ";
		$query .= "OR (a.sales_range_start = '' AND a.sales_range_end = '') )";
		
		return $query;
	}
	
	// ���� EP ������Ʈ ����
	function getGoodsSqlNew($columns){
		// ��ǰ ����Ÿ
		$query = "SELECT ".implode(',',$columns)." , b.category FROM ".GD_GOODS." a ,";
		$query .= "(SELECT c.goodsno, ".getCategoryLinkQuery('c.category', null, 'max')." FROM ".GD_GOODS_LINK." c left join gd_category gc on gc.category=left(c.category,3) WHERE c.category!='' and gc.category is not null and c.hidden='0' GROUP BY c.goodsno) b ";
		$query .= "WHERE a.goodsno=b.goodsno ";
		$query .= "AND a.open=1 ";
		$query .= "AND !( a.runout = 1 OR (a.usestock = 'o' AND a.usestock IS NOT NULL AND a.totstock < 1) ) ";
		$query .= "AND ( (a.sales_range_start < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP() < a.sales_range_end) ";
		$query .= "OR (a.sales_range_start < UNIX_TIMESTAMP() AND a.sales_range_end = '') ";
		$query .= "OR (UNIX_TIMESTAMP() < a.sales_range_end AND a.sales_range_start = '') ";
		$query .= "OR (a.sales_range_start = '' AND a.sales_range_end = '') )";

		return $query;
	}
	
	function getGoodsOption($goodsno){
		global $db;
		$query ="select price,reserve from ".GD_GOODS_OPTION." where goodsno='".$goodsno."' and link  and go_is_deleted <> '1' and go_is_display = '1' limit 1";
		$v = $db->fetch($query);
		return $v;
	}

	function getGoodsnm($partner,$param){
		### ��ǰ�� �Ӹ��� ����
		if($partner['goodshead'])$param['goodsnm'] = str_replace(array('{_maker}','{_brand}'),array($param['maker'],$param['brandnm']),$partner['goodshead']).$param['goodsnm'];
		return strip_tags($param['goodsnm']);
	}

	function getGoodsImg($img,$url){
		global $cfg;
		list($img) = explode("|",$img);
		if(preg_match('/http:\/\//',$img))$img_url = $img;
		else $img_url = $url.'/data/goods/'.$img;
		return $img_url;
	}

	function getReviewCnt($goodsno){
		global $db;
		### review ����
		$query = "select count(*) from ".GD_GOODS_REVIEW." where goodsno='".$goodsno."'";
		list($review) = $db->fetch($query);
		return $review;
	}

	function getEvent($goodsno,$tdate){
		global $db;
		$query = "select b.subject,b.sno from ".GD_EVENT." b left join ".GD_GOODS_DISPLAY." a on a.mode = concat('e',b.sno) where a.goodsno='".$goodsno."' and b.sdate <='".$tdate."' and b.edate>='".$tdate."' limit 1";
		$ret = $db->fetch($query);
		return $ret;
	}
	
	// �귣���
	function getBrand()
	{
		global $db;
		$query = "select brandnm,sno from ".GD_GOODS_BRAND;
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $brandnm[$data['sno']] = $data['brandnm'];
		return $brandnm;
	}

	// ���� EP ���� ���Ǵ� �÷� ���� Ȯ��
	function checkColumn()
	{
		global $db;
		$columns = array();
		$daumColumns = array (
			'goodsno', 'goodsnm','goods_price','goods_reserve', 'maker', 'brandno', 'delivery_type', 'goods_delivery', 'img_m', 'use_emoney', 'open_mobile', 'model_name', 'use_goods_discount', 'use_only_adult', 'naver_event'
			);

		$query = "desc gd_goods";
		$column_name = array();

		$res = $db->query($query);
		while ($column = $db->fetch($res)) {
			if (in_array($column['Field'],$daumColumns)) {
				$columns[] = 'a.'.$column['Field'];
			}
		}
		return $columns;
	}

	// ����
	function getReview(){
		global $db;
		$query = "SELECT count(*) cnt,goodsno FROM ".GD_GOODS_REVIEW." group by goodsno ";
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $review[$data['goodsno']] = $data['cnt'];
		return $review;
	}

	// ��ǰ����
	function getDiscount()
	{
		global $db;
		$result = array();

		$query = "select gd_goodsno,gd_start_date, gd_end_date, gd_level, gd_amount, gd_unit, gd_cutting from gd_goods_discount";
		$res = $db->query($query);
		while ($data = $db->fetch($res)) {
			$result[$data['gd_goodsno']] = $data;
		}
		return $result;
	}

	// ����
	function getCouponInfo()
	{
		global $db;
		$couponPrice = array();
		$result = array();

		$today = date("Y-m-d H:i:s");

		$query = "select gc.price,gc.ability,gc.goodstype,gc.c_screen,gcc.category,gcg.goodsno from gd_coupon gc
		left join gd_coupon_goodsno gcg on gc.couponcd=gcg.couponcd
		left join gd_coupon_category gcc on gcc.couponcd=gc.couponcd
		where gc.coupontype = '1'
		and ((gc.sdate <= '$today' AND gc.edate >= '$today' AND gc.priodtype='0') or (gc.priodtype='1' and (gc.edate = '' or gc.edate >= '$today')))";

		$res = $db->query($query);
		while ($couponData = $db->fetch($res,1)) {
			$result[] = $couponData;
		}
		return $result;
	}

	// ��ۺ�
	function getDeliveryPrice($v,$price)
	{
		global $set;
		$deliv = 0;
		if($v['delivery_type'] == 0){
			if($set['delivery']['free'] && $set['delivery']['default'] && $set['delivery']['deliveryType'] != "�ĺ�"){
				if ($price >= $set['delivery']['free'])
					$deliv = 0;
				else
					$deliv = $set['delivery']['default'];
			}
		}else if($v['delivery_type'] == 1){
			$deliv = 0;
		}
		else if($v['delivery_type'] == 3){
			$deliv = -1;
		}
		else if($v['delivery_type'] == 4 || $v['delivery_type'] == 5){
			$deliv = $v['goods_delivery'];
		}
		return $deliv;
	}

	// ��ǰ ���� ���
	function getDiscountPrice($discountData,$goodsno,$price)
	{
		$time = time();
		$special = 0;

		// ��ǰ ���� ���� �Ⱓ üũ
		if ($discountData[$goodsno]['gd_start_date'] > 0 && $discountData[$goodsno]['gd_start_date'] > $time) return 0;
		if ($discountData[$goodsno]['gd_end_date'] > 0 && $discountData[$goodsno]['gd_end_date'] < $time) return 0;

		if ($discountData[$goodsno]['gd_level'] != '0' && $discountData[$goodsno]['gd_level'] != '*') return 0;

		switch ($discountData[$goodsno]['gd_unit']) {
			case '%' :
				$special = $price * $discountData[$goodsno]['gd_amount'] / 100;
				break;

			case '=' :
				$special = $discountData[$goodsno]['gd_amount'];
				break;
		}

		$use = $unit = 0;
		$method = '';
		list($use, $unit, $method) = explode(':', $discountData[$goodsno]['gd_cutting']);

		if ($use) {
			$multi = $number = 0;
			$multi = $unit ? pow(10, $unit - 1) : 1;
			$number = $special / $multi;

			switch($method) {
				case 'c' : // �ø�
					$number = ceil($number);
					break;
				case 'r' : // �ݿø�
					$number = round($number);
					break;
				case 'f' : // ����
				default :
					$number = floor($number);
					break;
			}
			$goodsDiscount = $number * $multi;
		}
		else {
			// ���� �����϶�, �Ҽ��� ���ϴ� ����
			$goodsDiscount = floor($special);
		}

		return $goodsDiscount;
	}

	// ���� ���� ���
	function getCouponPrice($couponData,$category,$goodsno,$price,$open)
	{
		global $cfgCoupon;
		$arCategory = array();
		$coupon = 0;
		$mobileCoupon = 0;
		$couponOri = 0;
		$mobileCouponOri = 0;

		for($i=3; $i<=strlen($category); $i=$i+3) {
			$arCategory[] = substr($category,0,$i);
		}

		for ($i=0; $i<count($couponData); $i++) {
			$couponTemp = 0;
			$reserveTemp = 0;
			if ($couponData[$i]['goodstype'] == '0' || $goodsno == $couponData[$i]['goodsno'] || in_array($couponData[$i]['category'],$arCategory)) {
				// ������ ����
				if ($couponData[$i]['ability'] == '1') {
					if (strpos($couponData[$i]['price'],'%') == true) {
						$reserveTemp = substr($couponData[$i]['price'] , 0, -1);
						$reserveTemp = $reserveTemp/100*$price;
					}
					else $reserveTemp = $couponData[$i]['price'];

					// ���� ��� �ߺ� ����
					if ($cfgCoupon['double'] == '1' && $couponData[$i]['c_screen'] != 'm') {
						$couponReserve += $reserveTemp;
					}
					// ���� ��� �ߺ� ���� && ����� ���� ����
					else if ($cfgCoupon['double'] == '1' && $couponData[$i]['c_screen'] == 'm' && $open == '1') {
						$couponReserve += $reserveTemp;
					}
					// ���� �ߺ� ��� �Ұ�
					else if ($reserveTemp > $couponReserve && $couponData[$i]['c_screen'] != 'm') {
						$couponReserve = $reserveTemp;
					}
					// ���� �ߺ� ��� �Ұ� && ����� ���� ����
					else if ($reserveTemp > $couponReserve && $couponData[$i]['c_screen'] == 'm' && $open == '1') {
						$couponReserve = $reserveTemp;
					}
				}
				// �ݾ� ���� ����
				else if ($couponData[$i]['ability'] == '0') {
					if (strpos($couponData[$i]['price'],'%') == true) {
						$couponTemp = substr($couponData[$i]['price'] , 0, -1);
						$couponTemp = $couponTemp/100*$price;
					}
					else $couponTemp = $couponData[$i]['price'];

					// ���� ��� �ߺ� ����
					if ($cfgCoupon['double'] == '1' && $couponData[$i]['c_screen'] != 'm') {
						$coupon += $couponTemp;
						if ($couponTemp > $couponOri) {
							$couponOri = $couponTemp;
							$coupo = $couponData[$i]['price'];
						}
					}
					// ���� ��� �ߺ� ���� && ����� ���� ����
					else if ($cfgCoupon['double'] == '1' && $couponData[$i]['c_screen'] == 'm' && $open == '1') {
						$mobileCoupon += $couponTemp;
						if ($couponTemp > $mobileCouponOri) {
							$mobileCouponOri = $couponTemp;
							$mcoupon = $couponData[$i]['price'];
						}
					}
					// ���� �ߺ� ��� �Ұ�
					else if ($couponTemp > $coupon && $couponData[$i]['c_screen'] != 'm') {
						$coupon = $couponTemp;
						$coupo = $couponData[$i]['price'];
					}
					// ���� �ߺ� ��� �Ұ� && ����� ���� ����
					else if ($couponTemp > $mobileCoupon && $couponData[$i]['c_screen'] == 'm' && $open == '1') {
						$mobileCoupon = $couponTemp;
						$mcoupon = $couponData[$i]['price'];
					}
				}
			}
		}
		if ($cfgCoupon['double'] == '1') $mobileCoupon += $coupon;
		if (strpos($coupo,'%') === false) $coupo = $coupo.'��';
		if (strpos($mcoupon,'%') === false) $mcoupon = $mcoupon.'��';

		$return = array($coupon,$mobileCoupon,$couponReserve,$coupo,$mcoupon);

		return $return;
	}
}
?>