<?
include "../lib/library.php";
include "../conf/config.php";

$date = date("Y-m-d H:i:s");

## $_GET[couponcd]�� ���� ���
if(!$_GET[couponcd] && $_GET[goodsno]){
	$query = "select a.couponcd from
				".GD_COUPON." a
				left join ".GD_COUPON_CATEGORY." b on a.couponcd = b.couponcd
				left join ".GD_COUPON_GOODSNO." c on a.couponcd = c.couponcd
			where
				a.coupontype = '1'
				AND c.goodsno = '$_GET[goodsno]' OR goodstype='0'
				AND ((a.sdate <= '$date' AND a.edate >= '$date' AND a.priodtype='0') OR (a.priodtype='1'))
				order by couponcd
				limit 1";
	list($_GET[couponcd]) = $db->fetch($query);
}

## �������� �ҷ�����
$query = "select category,char_length(category) clen from ".GD_GOODS_LINK." where  goodsno='$_GET[goodsno]'";
$res = $db->query($query);
while($tmp = $db->fetch($res)) for($i=3;$i<=$tmp[clen];$i+=3) $arrCategory[] = "'".substr($tmp[category],0,$i)."'";

if($_GET[goodsno]) $where[] = "c.goodsno = '$_GET[goodsno]'";
if($arrCategory) $where[] = "b.category in(".implode(',',$arrCategory).")";
if($where)$strWhere = "OR ((".implode(' OR ', $where).") AND goodstype='1')";

$query = "select a.* from
				".GD_COUPON." a
				left join ".GD_COUPON_CATEGORY." b on a.couponcd = b.couponcd
				left join ".GD_COUPON_GOODSNO." c on a.couponcd = c.couponcd
			where
				a.coupontype = '1'
				AND (goodstype='0' $strWhere)
				AND ((a.sdate <= '".$date."' AND a.edate >= '".$date."' AND a.priodtype='0') OR (a.priodtype='1'))
				AND a.couponcd='$_GET[couponcd]' limit 1";
$data = $db->fetch($query);

if(!$data[couponcd]){
	
	msg("�����߱��� ����Ǿ����ϴ�",0);
	exit;
}

### ���� ���� ���� üũ
if (!$sess){
	msg("ȸ���� ������ �ٿ���մϴ�",0);
	exit;
}

### �̻�� ���� ���� üũ
list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_APPLY." a LEFT JOIN ".GD_COUPON_APPLY."member b ON a.sno=b.applysno where b.m_no='$sess[m_no]' and goodsno='$_GET[goodsno]' and couponcd = '{$_GET[couponcd]}' and status='0'");
if($cnt){
	msg("�̻�� ���� ������ �̹� �ֽ��ϴ�.",0);
	exit;
}

### �ߺ��ٿ�ε� �� �ٿ�ε� �ѷ� üũ
list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_APPLY." a LEFT JOIN ".GD_COUPON_APPLY."member b ON a.sno=b.applysno where b.m_no='$sess[m_no]' and a.couponcd = '{$_GET[couponcd]}'");

if( $data[dncnt] && $cnt >= $data[dncnt]){
	msg("���� �ٿ�ε� Ƚ���� ���ѵǾ����ϴ�.",0);
	exit;
}

###  ���� ��ǰ�� �ٿ�ε� Ƚ�� üũ
if($data[duplctl] && $data[edncnt]){
	list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_APPLY." a LEFT JOIN ".GD_COUPON_APPLY."member b ON a.sno=b.applysno where b.m_no='$sess[m_no]' and a.goodsno='$_GET[goodsno]' and a.couponcd = '{$_GET[couponcd]}' ");
	if($cnt >= $data[edncnt]){
		msg("���� �ٿ�ε� Ƚ���� ���ѵǾ����ϴ�.",0);
		exit;
	}
}


### ���� ����
list($goodsnm) = $db->fetch("select goodsnm from ".GD_GOODS." where goodsno='$_GET[goodsno]'");
$goodsnm = addslashes($goodsnm);
$setquery = "
		couponcd		= '$_GET[couponcd]',
		membertype		= '2',
		member_grp_sno  = '',
		goodsno			='$_GET[goodsno]',
		goodsnm			='$goodsnm',
		regdt			= now(),
		status			= '0'
	";

$query = "insert into ".GD_COUPON_APPLY." set  ".$setquery;
$db->query($query);

$query = "select max(sno) from ".GD_COUPON_APPLY;
list($newapplysno) = $db->fetch($query);

$query = "insert into ".GD_COUPON_APPLY."member set m_no='$sess[m_no]', applysno ='$newapplysno'";
$db->query($query);

msg("������ ���������� �߱޵Ǿ����ϴ�.\\n���������� ���� Ȯ���ϼ���.");
if($_GET[emailcoupon]) go('http://'.$cfg[shopUrl].'/goods/goods_view.php?goodsno='.$_GET[goodsno]);
?>