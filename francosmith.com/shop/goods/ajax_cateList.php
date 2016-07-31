<?
// �ʿ����� �ε�
	include "../lib/library.php";
	@include dirname(__FILE__) . "/../lib/tplSkinView.php";

// �Ķ���� ���� �� ����
	$goodsno	= ($_GET['goodsno'])	? $_GET['goodsno']		: "";	// ���� ��ǰ ��ȣ
	$category	= ($_GET['category'])	? $_GET['category']		: "";	// ī�װ� �ڵ�
	$page_num	= ($_GET['page_num'])	? $_GET['page_num']		: 5;	// �������� ��� ��ǰ ��
	$page		= $_GET['page'];										// ������

// ��ǰ ��ȣ�� ������ �۵����� ����
	if(!$goodsno) exit();

// ī�װ��� ���ٸ� ī�װ� �б�
	if(!$category) list($category) = $db->fetch("SELECT category FROM ".GD_GOODS_LINK." WHERE goodsno = '$goodsno' ORDER BY LENGTH(category) DESC, sno ASC LIMIT 1");

// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
	$whereArr	= getCategoryLinkQuery('L.category', $category, null, 'L.goodsno');

// ���� ���� �� ����
	$query = "
SELECT
	".$whereArr['distinct']." L.goodsno, L.category, G.img_l, G.goodsnm, O.price, G.use_only_adult
FROM
	".GD_GOODS_LINK." AS L
	LEFT JOIN ".GD_GOODS." AS G
		ON L.goodsno = G.goodsno
	LEFT JOIN ".GD_GOODS_OPTION." AS O
		ON L.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1' and go_is_display = '1'
WHERE
	".$whereArr['where']."
	AND G.open = '1'
".$whereArr['group']."
ORDER BY L.sort";
	$total = $db->count_($db->query($query)); // ��ü ���ڵ�

	// �������� �������� ���� ��� ���� ��ǰ�� �ִ� �������� ǥ��
	if(!$page) {
		list($this_sort) = $db->fetch("SELECT sort FROM ".GD_GOODS_LINK." WHERE goodsno = '$goodsno'");
		list($temp_cnt) = $db->fetch("SELECT COUNT(sort) AS cnt FROM ".GD_GOODS_LINK." WHERE sort < $this_sort AND ".getCategoryLinkQuery('category', $category, 'where'));
		$page = ceil(($temp_cnt + 1) / $page_num);
	}

	$query .= "
LIMIT ".($page_num * ($page - 1)).", $page_num";
	$result = $db->query($query);

	$count = $db->count_($result);
	if(!$count) exit;

// �ϴ� ������ �κ�
	// ���� ����Ʈ ��ȣ
		$first_num = ($page_num * ($page - 1)) + 1;
		$last_num = ($page_num * ($page - 1)) + $count;
		$now_num = ($first_num == $last_num) ? $first_num : $first_num."-".$last_num;

// �ϴ� ��ư
	$total_page = ceil($total / $page_num); // �� ������
	$prev_page = ($page == 1) ? "1" : $page - 1; // ���� ������
	$next_page = ($page == $total_page) ? $total_page : $page + 1; // ���� ������
	$qStr = "goodsno={$goodsno}&category={$category}";
?>
<div style="width:90;background-image:url('../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_bg_list.gif'); background-repeat:repeat-y;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_h_list.gif" style="cursor:pointer;" onclick="toggle('leftCateList');toggle('leftCatePage');" /></td></tr>
	<tr>
		<td align="center"><div id="leftCateList" style="">
<?
	while($data=$db->fetch($result)) {
		$ar_temp = explode("|", $data['img_l']);
		$data['img'] = $ar_temp[0];
		//�������� ��ǰ û�ҳ� ��ȣ�� ����
		if($data['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
			$data['img'] = "../skin/" . $cfg['tplSkin'] . '/img/common/19.gif';
		}
?>
		<div><a href="../goods/goods_view.php?goodsno=<?=$data['goodsno']?>&category=<?=$category?>" onmouseover="scrollTooltipShow(this)" onmousemove="scrollTooltipShow(this)" onmouseout="scrollTooltipHide(this)" tooltip="<span style='color:#333232;'><?=htmlspecialchars($data['goodsnm'], ENT_QUOTES)?></span><br><span style='color:#EF1C21;font-weight:bold;'><?=number_format($data['price'])?>��</span>"><?=goodsimg($data['img'], 70)?></a></div>
		<div style="height:3px;font-size:0"></div>
<?
	}
?>
		</div></td>
	</tr>
	<tr>
		<td align="center" id="leftCatePage"><div style="width:88px;margin:0px 1px;padding:5px 0px;font-weight:bold;color:#333232;"><font color="#EF1C21"><?=$now_num?></font> of <?=$total?></div></td>
	</tr>
	<tr><td><a href="javascript:scrollCateList_ajax('<?=$qStr?>', <?=$prev_page?>, <?=$page_num?>)" onfocus="blur()"><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_left_list_off.gif" onmousemove="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_left_list_on.gif';" onmouseout="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_left_list_off.gif';" border="0"></a><a href="javascript:scrollCateList_ajax('<?=$qStr?>', <?=$next_page?>, <?=$page_num?>)" onfocus="blur()"><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_right_list_off.gif" onmousemove="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_right_list_on.gif';" onmouseout="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_right_list_off.gif';" border='0'></a></td></tr>
	<tr><td><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_bn_foot_list.gif" border='0'></td></tr>
</table>
</div>
