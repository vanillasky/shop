<?
# �⺻����(�����)
$menu_unable = array();
$menu_unable[] = "basic/agspay.php";
$menu_unable[] = "event/orderlist.php";
$menu_unable[] = "order/merchant_list.php";		#��ũ�����̽��ֹ�����Ʈ
$menu_unable[] = "linkprice/merchant.php";		#��ũ�����̽�����
$menu_unable[] = "open/om_id.php";				# ���¸��� ��������
$menu_unable[] = "open/om_category.php";			# ���¸��� �з���Ī
$menu_unable[] = "open/om_register.php";			# ���¸��� ��ǰ���
$menu_unable[] = "open/om_stock.php";			# ���¸��� ǰ������

# ���������(�ȳ��˾�ȣ��)
$menu_unfree = array();
$menu_unfree[] = "log/search.php";
$menu_unfree[] = "event/list.php";
$menu_unfree[] = "event/register.php";
$menu_unfree[] = "log/index.php?mode=referer";
$menu_unfree[] = "javascript:if( window.open('../open/om.popup.manager.php','om') );";		# ���¸��� �Ŵ���
$menu_unfree[] = "order/bankda.php";				# �������հ���
$menu_unfree[] = "order/bankmatch.php";			# �Ա���ȸ/�����Ա�ó��
$menu_unfree[] = "goods/price.php";		# ���� ���ݼ���
$menu_unfree[] = "goods/reserve.php";		# ���� �����ݼ���
$menu_unfree[] = "goods/link.php";		# ���� �̵�/����/����
$menu_unfree[] = "member/batch.php";
$menu_unfree[] = "member/batch.php?func=emoney";		# ������ �ϰ�����/����
$menu_unfree[] = "member/batch.php?func=level";		# ȸ���׷� �ϰ�����
$menu_unfree[] = "member/batch.php?func=status";		# ȸ�����λ��� �ϰ�����
$menu_unfree[] = "member/batch.php?func=sms";		# SMS �߼��ϱ�
$menu_unfree[] = "member/batch.php?func=email";		# ���� �߼��ϱ�
$menu_unfree[] = "basic/adminGroup.php";		# �����ڱ��Ѽ���
$menu_unfree[] = "event/coupon_cfg.php";		# ��������
$menu_unfree[] = "event/coupon.php";		# ��������Ʈ
$menu_unfree[] = "event/coupon_register.php";		# �������

# �Ӵ�������(�����)
$menu_unrent = array();

# ����������(�����)
$menu_unself = array();
$menu_unself[] = "open/om_category.php";			# ���¸��� �з���Ī
$menu_unself[] = "open/om_register.php";			# ���¸��� ��ǰ���
$menu_unself[] = "open/om_stock.php";			# ���¸��� ǰ������

/*---------------------------------------------------------------*/

include dirname(__FILE__)."/menu.able.php";

function able_unset($var) {
	global $menu_able;
 	return ( !in_array( $var, $menu_able ) );
}

$menu_unable = array_filter($menu_unable, "able_unset");
$menu_unfree = array_filter($menu_unfree, "able_unset");
$menu_unrent = array_filter($menu_unrent, "able_unset");
$menu_unself = array_filter($menu_unself, "able_unset");

if ( preg_match( "/^rental_mx[^free]/i", $godo[ecCode] ) ) // �Ӵ�������
	$menu_unable = array_merge ($menu_unable, $menu_unrent);
else if( $godo[ecCode]=="self_enamoo_season" ) // ����������
	$menu_unable = array_merge ($menu_unable, $menu_unself);

?>