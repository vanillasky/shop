<?
if (!preg_match('/^[0-9]*$/', $_GET['category']))
	exit ;

include "../_header.php";
include "../lib/page.class.php";
include "../conf/config.pay.php";
if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
	include dirname(__FILE__) . "/../conf/config.soldout.php";
include dirname(__FILE__) . "/../conf/config.display.php";

try {

	$goodsHelper   = Clib_Application::getHelperClass('front_goods');

	// ī�װ�
	$categoryModel = $goodsHelper->getCategoryModel(Clib_Application::request()->get('category'));

	// template_ ���� global ������ ����ϱ� ������ ���� ��.
	$_GET['category'] = $category = $categoryModel->getId();

	if (!$categoryModel->hasLoaded()) {
		throw new Clib_Exception('�з��������� ī�װ��� �������� �ʾҽ��ϴ�.');
	}

	// ���� üũ
	if (!$categoryModel->checkPermission(Clib_Application::session()->getMemberLevel())) {
		throw new Clib_Exception('�̿� ������ �����ϴ�.\\nȸ������� ���ų� ȸ�������� �ʿ��մϴ�.');
	}

	// ī�װ� ���� ��� ���� üũ
	if (!Clib_Application::session()->isAdmin() && getCateHideCnt($categoryModel->getId()) > 0) {
		throw new Clib_Exception('�ش�з��� ������ ���� �з��� �ƴմϴ�.');
	}

	// ī�װ� ��ǰ ��� ����
	$lstcfg = $categoryModel->getConfig();

	// �Ķ���� ����
	$params = array(
		'page' => Clib_Application::request()->get('page', 1),
		'page_num' => Clib_Application::request()->get('page_num', $lstcfg['page_num'][0]),
		'keyword' => Clib_Application::request()->get('keyword'),
		'sort' => Clib_Application::request()->get('sort', $categoryModel->getSortColumnName()),
		'category' => $categoryModel->getId(),
	);

	if ($tpl->var_['']['connInterpark']) {
		$params['inpk_prdno'] = true;
	}

	// ��ǰ ���
	$goodsCollection = $goodsHelper->getGoodsCollection($params);

	$selected['page_num'][$params['page_num']] = "selected";

	#####ũ���׿�######
	$criteo = new Criteo();
	if ($criteo->begin()) {
		$criteo->get_list($goodsHelper->getIdsArray($goodsCollection));
		$systemHeadTagEnd .= $criteo->scripts;
		$tpl->assign('systemHeadTagEnd', $systemHeadTagEnd);
	}
	###################

	// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
	$whereArr	= getCategoryLinkQuery('CMGL0.category', Clib_Application::request()->get('category'));

	// ī�װ� �� ��ǰ���� for paging
	$query = " SELECT ";
	$query.= " COUNT(".$whereArr['distinct']." CMGG0.goodsno) AS __CNT__ ";
	$query.= " FROM ".GD_GOODS." AS CMGG0 ";
	$query.= " INNER JOIN ".GD_GOODS_LINK." AS CMGL0 ON CMGG0.goodsno = CMGL0.goodsno ";
	$query.= " WHERE  (CMGL0.hidden = '0') ";
	$query.= " and ".$whereArr['where'];
	$query.= " and (CMGG0.open = '1') ";

	// ǰ�� ��ǰ ����
	if ($cfg_soldout['exclude_category']) {
		$query.= "and !( CMGG0.runout = 1 OR (CMGG0.usestock = 'o' AND CMGG0.usestock IS NOT NULL AND CMGG0.totstock < 1) ) ";
	}

	//DB Cache ��� 141030
	$dbCache = Core::loader('dbcache')->setLocation('goodslist');

	if (!$out = $dbCache->getCache($query)) {
  		$totalCount = $db->fetch($query); // ��ü ���ڵ�
		if ($totalCount && $dbCache) $dbCache->setCache($query, $totalCount);
  	} else {
  		$totalCount = $out;
  	}

	// ����¡ ó��
	$offset[0] = $params['page'];
	$offset[1] = $params['page_num'];
	$total_count = $totalCount['__CNT__'];
	if ($total_count % $offset[1]) {
		$totalpage = (int)($total_count / $offset[1]) + 1;
	}
	else {
		$totalpage = $total_count / $offset[1];
	}

	// ����¡
	$pg = new Page($offset[0], $offset[1]);
	$pg->recode['total'] = $total_count;
	$pg->page['total'] = $totalpage;
	$pg->idx = $pg->recode['total'] - $pg->recode['start'];
	$pg->setNavi($tpl2 = '');
	$pg->query = $query;

	$tpl->assign(array(
		'pg' => $pg,
		'loopM' => $goodsHelper->getGoodsCollectionArray($goodsCollection, $categoryModel),
		'lstcfg' => $lstcfg,
		'slevel' => Clib_Application::session()->getMemberLevel(),
	));
	$tpl->print_('tpl');

}
catch (Clib_Exception $e) {
	Clib_Application::response()->jsAlert($e)->historyBack();
}
