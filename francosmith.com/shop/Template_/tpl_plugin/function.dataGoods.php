<?php

/* Return Goods Data Function */

function dataGoods( $category='', $spc=0, $limit=10 )
{
	global $db, $cfg;

	if ($category){
		$add_table = "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$orderby = "order by c.sort";

	 	// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
	} else $orderby = "order by a.regdt desc";
	if ($spc){
		$where[] = "icon & $spc";
	}
	if (!$limit) $limit = 1;

	$where[] = "a.open";

	if ($where) $where = "where ".implode(" and ",$where);

	$query = "
	select * from
		".GD_GOODS." a
		left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
		$add_table
	$where $orderby
	limit $limit
	";

	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ){

		### �Ｎ�������� ��ȿ�� �˻�
		list($data[coupon],$data[coupon_emoney]) = getCouponInfo($data[goodsno],$data[price]);
		$data[reserve] += $data[coupon_emoney];

		### ������
		$data[icon] = setIcon($data[icon],$data[regdt]);

		$loop[] = $data;
	}

	return $loop;
}

?>