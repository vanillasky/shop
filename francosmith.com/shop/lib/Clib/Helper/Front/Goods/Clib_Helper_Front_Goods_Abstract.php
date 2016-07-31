<?php
/**
 * Clib_Helper_Front_Goods_Abstract
 */
class Clib_Helper_Front_Goods_Abstract extends Clib_Helper_Abstract
{
	public function getIconHtml(Clib_Model_Goods_Abstract $goodsModel)
	{
		return setIcon($goodsModel['icon'], $goodsModel['regdt']);
	}

	public function getGoodsCollection($params)
	{
		$collection = Clib_Application::getCollectionClass('goods');
		$collection = $this->prepareGoodsCollection($collection, $params);
		$collection->load();

		return $collection;
	}

	/**
	 *
	 */
	public function getIdsArray($collection)
	{
		$ids = array();
		foreach ($collection as $item) {
			$ids[] = $item->getId();
		}
		return $ids;
	}

	public function prepareGoodsCollection($collection, $params)
	{
		global $cfg_soldout, $cfgMobileShop;

		// ������
		$collection->setCurrentPage($params['page']);
		$collection->setPageSize($params['page_num']);

		// @todo : ó����� ��ü
		$GLOBALS['selected']['page_num'][$params['page_num']] = "selected";

		// ���������� ���ٸ�, ���� ��ǰ�� ������� ����
		if ( ! Clib_Application::session()->isAdmin()) {
			if( Clib_Application::isMobile()) {
				if ($cfgMobileShop['vtype_category'] == 0) {
					// ����ϼ� ī�װ� ���� ������ '�¶��� ���θ�(PC����)�� ���⼳�� �����ϰ� ����'�� ���
					$collection->addFilter('goods_link.hidden', 0);
				}
				else {
					// ����ϼ� ī�װ� ���� ������ '����ϼ� ���� ���⼳�� ����'�� ���
					$collection->addFilter('goods_link.hidden_mobile', 0);
				}
			}
			else {
				$collection->addFilter('goods_link.hidden', 0);
			}
		}

		if ($params['category']) {

			if ( ! $params['category'] instanceof Clib_Model_Category_Category) {
				$params['category'] = $this->getCategoryModel($params['category']);
			}

			$collection->setCategoryFilter($params['category']->getId());

			foreach ($params['category']->getExcludeCategoryCollection() as $exclude) {

				if ($_level = Clib_Application::session()->getMemberLevel()) {
					// ī�װ� ���� ���Ѻ��� ���� ����� ȸ����, ���� ī�װ��� ��ǰ���� ����
					if ($_level < $exclude->getLevel()) {
						$collection->addFilter('goods_link.category', Clib_Application::database()->wildcard($exclude->getId(), 1), 'not like');
					}
				}
				else {
					// ��ȸ���� �ش�ī�װ��� ����
					$collection->addFilter('goods_link.category', $exclude->getId(), '<>');
				}
			}
		}

		//������� ��ǰ �˻� �� ��� �����ϰ� ī�װ� ���� ������ üũ�� �ش�.
		if( Clib_Application::isMobile() &&  ! $params['category'] instanceof Clib_Model_Category_Category) {

			$category_collection = Clib_Application::getCollectionClass('category');
			$category_collection->addExpressionFilter("level <> 0");
			$category_collection->load();

			foreach($category_collection as $category_model) {
				foreach ($category_model->getExcludeCategoryCollection() as $exclude) {

					if ($_level = Clib_Application::session()->getMemberLevel()) {

						// ī�װ� ���� ���Ѻ��� ���� ����� ȸ����, ���� ī�װ��� ��ǰ���� ����
						if ($_level < $exclude->getLevel()) {
							$collection->addFilter('goods_link.category', Clib_Application::database()->wildcard($exclude->getId(), 1), 'not like');
						}
					}
					else {
						$collection->addFilter('goods_link.category', $exclude->getId(), '<>');
					}
				}
			}
		}

		// ���� ����
		if ( ! Clib_Application::isMobile()) {
			$collection->addFilter('goods.open', 1);
		}
		else {
			// �����
			$collection->addFilter('goods.open_mobile', 1);

			if ($params['keyword'] != '') {
				$collection->addExpressionFilter("CONCAT( goods.goodsnm, goods.keyword, goods.shortdesc, goods.origin, goods.maker, goods.mlongdesc ) LIKE '%" . $params['keyword'] . "%'");
			}

		}

		// ǰ�� ��ǰ ���� ���� Ű
		switch (basename($_SERVER['PHP_SELF'])) {
			case 'goods_brand.php':
			case 'brand.php':
				$cfg_soldout_key = 'brand';
				break;

			case 'goods_event.php':
				$cfg_soldout_key = 'event';
				break;

			case 'goods_search.php':
				$cfg_soldout_key = 'search';
				break;

			case 'index.php':
				$cfg_soldout_key = 'main';
				break;
			case 'list.php' :
			case 'list.add.php' :
			case 'mAjaxAction.php' :
				if(Clib_Application::isMobile()) {
					if(Clib_Application::request()->get('kw')) {
						$cfg_soldout_key = 'search';
					}
					else {
						if(Clib_Application::request()->get('brand')) {
							$cfg_soldout_key = 'brand';
						} else {
							$cfg_soldout_key = 'category';
						}
					}
				}
				break;
			case 'goods_list.php':
			default :
				$cfg_soldout_key = 'category';
				break;
		}

		if ($cfg_soldout['exclude_'.$cfg_soldout_key]) {
			$collection->addExpressionFilter("!( goods.runout = 1 OR ( goods.usestock = 'o' AND goods.usestock IS NOT NULL AND goods.totstock < 1))");
		}
		// ���ܽ�Ű�� �ʴ� �ٸ�, �� �ڷ� �������� ����
		else if ($cfg_soldout['back_'.$cfg_soldout_key]) {
			$collection->addExpressionOrder("IF ( goods.runout = 1 , 1, IF ( goods.usestock = 'o' AND goods.totstock = 0, 1, 0))", 'asc');
		}

		if ($params['inpk_prdno']) {
			$collection->addFilter('goods.inpk_prdno', '', '!=');
		}

		if (array_key_exists('mode', $params)) {// 0 �� ���Ǳ� ������ Ű�� �ִ��� üũ��

			$collection->addFilter('goods_display.mode', $params['mode']);

			if (array_key_exists('tplSkin', $params)) {
				if ($params['tplSkin']) {
					$collection->addFilter('goods_display.tplSkin', $params['tplSkin']);
				}
				else {
					$collection->addExpressionFilter(' goods_display.tplSkin = \'\' OR goods_display.tplSkin IS NULL ');
				}
			}

		}

		// ����Ʈ �˻� �߰� ����
		// @todo : ����Ʈ �˻��� ���𵨸��� �ʿ���
		global $smartSearch;
		if(is_object($smartSearch)) {
			if ($ssQuery = $smartSearch->ssQuery()) {
				$collection->addExpressionFilter($ssQuery);
			}
		}


		// �귣��
		if ($params['brandno']) {
			$collection->addFilter('goods.brandno', $params['brandno']);
			// @todo : ó����� ��ü
			$GLOBALS['brand'] = $params['brandno'];
		}

		// ����
		if (array_key_exists('sort', $params)) {

			// correct dot notation.
			$params['sort'] = $this->_getCorrectedDotNotation($params['sort']);

			$collection->setOrder($params['sort']);
		}

		if (isset($params['sort'])) {
			$_GET['sort'] = $params['sort'];
		}

		if (isset($params['page_num'])) {
			$_GET['page_num'] = $params['page_num'];
		}

		// ��ü�� �缳��
		if (isset($params['resetRelationShip']) && !empty($params['resetRelationShip'])) {
			foreach ($params['resetRelationShip'] as $property => $config) {
				$collection->getValueModel()->setRelationShip($property, $config);
			}
		}

		return $collection;
	}

	private function _getCorrectedDotNotation($str)
	{

		if (strpos($str, '.') !== false) {
			$tmp = explode('.', $str);
			if (! class_exists(Clib_Application::getClassName('model', $tmp[0], true))) {
				return '';
			}
		}
		else {
			$tmp = preg_split('/\s+/',$str);

			switch ($tmp[0]) {
				case 'price':
					$tmp[0] = 'goods_price';
					break;
				case 'reserve':
					$tmp[0] = 'goods_reserve';
					break;
			}

			$str = implode(' ', $tmp);
		}

		return $str;


	}

	public function getEventModel($id)
	{
		$model = Clib_Application::getModelClass('event');
		$model->load($id);
		return $model;
	}

	public function getBrandModel($id)
	{
		$model = Clib_Application::getModelClass('goods_brand');
		$model->load($id);
		return $model;
	}

	public function getGoodsModel($id)
	{
		$model = Clib_Application::getModelClass('goods');
		$model->load($id);
		return $model;
	}

	/**
	 *
	 */
	public function getCategoryModel($id = null, $goodsModel = null)
	{

		$categoryModel = Clib_Application::getModelClass('category');

		if ($id) {
			if ($id instanceof Clib_Model_Category_Category) {
				return $id;
			}
			else if ($goodsModel instanceof Clib_Model_Goods_Abstract) {
				foreach ($goodsModel->categories as $link) {
					if (strncmp($link->getCategory(), $id, strlen($id)) !== 0) {
						Clib_Application::request()->set('category', null);
					}
					else if ( ! $categoryModel->hasLoaded()) {
						$categoryModel = $link->category;
					}
				}
			}
			else {
				$categoryModel->load($id);
			}
		}
		else if ($goodsModel instanceof Clib_Model_Goods_Abstract && $goodsModel->hasLoaded()) {
			$categories = $goodsModel->categories->getIterator();

			if ($categories->count() > 0) {
				// ���� ī�װ� ������ ó��
				foreach ($goodsModel->categories as $link) {
					$arrCategory[]	= $link->getCategory();
				}
				$maxCategory		= max($arrCategory);

				if($categoryModel->load($maxCategory) == null || $categoryModel->load($maxCategory) == ''){
					$categoryModel	= $categories->current()->category;
				} else {
					$categoryModel	= $categoryModel->load($maxCategory);
				}
				Clib_Application::request()->set('category', $maxCategory);
			}

		}
		else {

			$categories = Clib_Application::getCollectionClass('category');
			$categories->setPageSize(1);
			$categories->addFilter('level', 0);
			$categories->addFilter('hidden', 0);
			$categories->addExpressionFilter('length( category ) = 3');
			$categories->setOrder('category');
			$categories->load();
			$categories = $categories->getIterator();

			if ($categories->count() > 0) {
				$categoryModel = $categories->current();
				// event���������� ��ǰ ������ ���� �Ʒ� ������ goods_list �������� �ű�
				//Clib_Application::request()->set('category', $categoryModel->getId());
			}

			// throw new Clib_Exception('ī�װ��� �ҷ��� �� ����.');
		}

		return $categoryModel;
	}

	public function canAccessLinkedCategory($goodsModel)
	{

		foreach ($goodsModel->categories as $link) {

			if ($_level = $link->category->getLevel()) {

				if ((int)$_level > (int)Clib_Application::session()->getMemberLevel()) {
					//@formatter:off
					switch($link->category->getLevelAuth()) {
						case '1' : //��μ���
						case '2' : //ī�װ���
						case '3' : //��ǰ����Ʈ
							return false;
							break;
					}
					//@formatter:on
				}
			}
		}

		return true;

	}

	public function canAccessAdultOnly(Clib_Model_Goods_Abstract $goodsModel)
	{
		if ($goodsModel['use_only_adult'] && ! (Clib_Application::session()->isAdult() || Clib_Application::session()->isAdmin())) {
			return false;
		}
		else {
			return true;
		}
	}

	public function getGoodsCollectionArray(Clib_Collection_Goods_Abstract $goodsCollection, $category = null, $listYn = false)
	{
		$goodsCollection = $this->setGoodsCollectionAttributes($goodsCollection, $category, $listYn);
		return $goodsCollection->toArray();
	}

	public function getGoodsDataArray(Clib_Model_Goods_Abstract $goodsModel, Clib_Model_Category_Category $categoryModel)
	{
		$goodsModel = $this->setGoodsAttributes($goodsModel, $categoryModel);
		return $goodsModel->getData();
	}

	public function getNateScrapButtonHtml(Clib_Model_Goods_Abstract $goodsModel)
	{
		$nate = Core::loader('NateClipping');
		$nateyn = $nate->chk_goods($goodsModel->getId(), Clib_Application::getConfig('config'), Clib_Application::database());

		if ($nateyn) {
			return $nate->get_scrapBt($goodsModel->getId(), Clib_Application::getConfig('config'));
		}

		return false;

	}

	public function getCategoryAuthStep(Clib_Model_Category_Category $categoryModel)
	{
		$cauth_step = explode(':', $categoryModel->getAuthStep());

		$tmp = array(
			0 => (in_array('1', $cauth_step) ? 'Y' : 'N'),
			1 => (in_array('2', $cauth_step) ? 'Y' : 'N'),
			2 => (in_array('3', $cauth_step) ? 'Y' : 'N'),
		);

		return $tmp;
	}

	public function getGoodsStatus($status)
	{
		switch ($status) {
			case 'R' :
				return '��ǰ/����ǰ';
				break;
			case 'U' :
				return '�߰��ǰ';
				break;
			case 'N' :
			default :
				return '�Ż�ǰ';
				break;
		}
	}

	public function getNaverMileageAccumHtml(Clib_Model_Goods_Abstract $goodsModel)
	{
		if (Clib_Application::isMobile()) {
			$naverNcash = Core::loader('naverNcash');
			if ($naverNcash->canUseMobile() === false) $naverNcash->useyn = 'N';
			if ($naverNcash->useyn == 'Y' && $naverNcash->baseAccumRate) {
				$exceptionYN = $naverNcash->exception_goods(array(array('goodsno' => $goodsModel->getId())));
				if ($exceptionYN == 'N') {
					$N_ba = preg_replace('/\.0$/', '', $naverNcash->get_base_accum_rate());
					$N_aa = preg_replace('/\.0$/', '', $naverNcash->get_add_accum_rate());
				}
				else if($exceptionYN == 'Y') {
					$N_ex = '���� �� ��� ���� ��ǰ';
				}
				return include SHOPROOT.'/proc/naver_mileage/goods_accum_rate_type_1.php';
			}
		}
		else {
			return null;
		}
	}

	public function getNaverCheckoutButtonHtml(Clib_Model_Goods_Abstract $goodsModel)
	{
		global $checkoutCfg;

		// ���ݴ�ü���� ���� �� üũ�ƿ���ư ��� ����
		if (strlen($goodsModel->getData('strprice')) > 0) {
			return false;
		}

		if ( ! Clib_Application::isMobile() && $checkoutCfg['useYn'] == 'y') {
			$NaverCheckout = Core::loader('NaverCheckout');
			if ($goodsModel->getRunout()) {
				$on = false;
			}
			else {
				$on = true;
			}

			return $NaverCheckout->get_GoodsViewTag($goodsModel->getId(), $goodsModel['goodsnm'], $on, 'ǰ�� �Դϴ�.');
		}
		else {
			$naverCheckout = Core::loader('naverCheckoutMobile');
			if($naverCheckout->isAvailable() && $naverCheckout->checkGoods($goodsModel->getId()))
			{
				if ($goodsModel->getRunout()) {
					$on = false;
				}
				else {
					$on = true;
				}

				return $naverCheckout->getButtonTag('GOODS_VIEW', $on);
			}
		}
	}

	public function getExtraInformationHtml(Clib_Model_Goods_Abstract $goodsModel)
	{

		if ($goodsModel['extra_info']) {

			$extra_info = gd_json_decode(stripslashes($goodsModel['extra_info']));
			$keys = array_keys($extra_info);

			for ($i = min($keys), $m = max($keys); $i <= $m; $i++) {

				$next_key = $i + 1 <= $m ? $i + 1 : null;

				if ( ! isset($extra_info[$i]))
					continue;

				if ($i % 2 == 1 && ! isset($extra_info[$next_key])) {
					$colspan = 3;
				}
				else {
					$colspan = 1;
				}

				$extra_info[$i]['nkey'] = $next_key;
				$extra_info[$i]['colspan'] = $colspan;
				$extra_info[$i]['title'] = htmlspecialchars($extra_info[$i]['title']);
				$extra_info[$i]['desc'] = htmlspecialchars($extra_info[$i]['desc']);
			}

		}
		else {
			$extra_info = array();
		}

		Clib_Application::storage()->set('extra_info', $extra_info);

		return $extra_info;
	}

	public function setPrevAndNextGoodsNavigationHtml(Clib_Model_Goods_Abstract $goodsModel, Clib_Model_Category_Category $categoryModel)
	{
		global $cfg;

		$sortField = Core::loader('GoodsSort')->getSortField($categoryModel->getId());

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$categoryWhere	= getCategoryLinkQuery('category', $categoryModel->getId(), 'where');

		list($currentSort) = Clib_Application::database()->fetch("SELECT " . $sortField . " FROM " . GD_GOODS_LINK . " WHERE goodsno = '" . $goodsModel->getId() . "' AND ".$categoryWhere);
		if (empty($currentSort) === false){
			list($prevGoodsId) = Clib_Application::database()->fetch("SELECT L.goodsno FROM " . GD_GOODS_LINK . " AS L LEFT JOIN " . GD_GOODS . " AS G ON L.goodsno = G.goodsno WHERE L." . $sortField . " < $currentSort AND L.".$categoryWhere." AND G.open = '1' ORDER BY " . $sortField . " DESC LIMIT 1");
			list($nextGoodsId) = Clib_Application::database()->fetch("SELECT L.goodsno FROM " . GD_GOODS_LINK . " AS L LEFT JOIN " . GD_GOODS . " AS G ON L.goodsno = G.goodsno WHERE L." . $sortField . " > $currentSort AND L.".$categoryWhere." AND G.open = '1' ORDER BY " . $sortField . " ASC LIMIT 1");
		}

		$S = Clib_Application::storage();
		$S->set('viewPageMoveList', true);
		$S->set('prevView', $prevGoodsId ? '<a href="../goods/goods_view.php?goodsno=' . $prevGoodsId . '&category=' . $categoryModel->getId() . '"><img src="../data/skin/' . $cfg['tplSkin'] . '/img/common/skin_btn_left_off.gif" onmouseover="this.src=\'../data/skin/' . $cfg['tplSkin'] . '/img/common/skin_btn_left_on.gif\';" onmouseout="this.src=\'../data/skin/' . $cfg['tplSkin'] . '/img/common/skin_btn_left_off.gif\';" /></a>' : '');
		$S->set('nextView', $nextGoodsId ? '<a href="../goods/goods_view.php?goodsno=' . $nextGoodsId . '&category=' . $categoryModel->getId() . '"><img src="../data/skin/' . $cfg['tplSkin'] . '/img/common/skin_btn_right_off.gif" onmouseover="this.src=\'../data/skin/' . $cfg['tplSkin'] . '/img/common/skin_btn_right_on.gif\';" onmouseout="this.src=\'../data/skin/' . $cfg['tplSkin'] . '/img/common/skin_btn_right_off.gif\';" /></a>' : '');

	}

	public function getEventQrCodeHtml(Clib_Model_Event_Event $eventModel)
	{
		global $qrCfg;

		$qr = Clib_Application::getModelClass('qrcode');
		$qr->loadEventCode($eventModel->getId());

		if ($qrCfg['useEvent'] == 'y' && $qr->hasLoaded()) {
			require "../lib/qrcode.class.php";
			$QRCode = Core::loader('QRCode');
			return $QRCode->get_GoodsViewTag($eventModel->getId(), "event_view");
		}
		return false;
	}

	public function getGoodsQrCodeHtml(Clib_Model_Goods_Abstract $goodsModel)
	{
		global $qrCfg;

		$qr = Clib_Application::getModelClass('qrcode');
		$qr->loadGoodsCode($goodsModel->getId());

		if ($qrCfg['useGoods'] == 'y' && $qr->hasLoaded()) {
			require "../lib/qrcode.class.php";
			$QRCode = Core::loader('QRCode');
			return $QRCode->get_GoodsViewTag($goodsModel->getId(), "goods_view");
		}
		return false;
	}

	public function getAuctionIpayButtonHtml(Clib_Model_Goods_Abstract $goodsModel)
	{
		global $auctionIpayCfg;

		if ($auctionIpayCfg['useYn'] == 'y') {
			$tmpImg = explode('|', $goodsModel['img_s']);
			$thumbimg = $tmpImg[0];
			if ($thumbimg && (file_exists(SHOPROOT . '/data/goods/' . $thumbimg) || preg_match('/^http(s)?:\/\//', $thumbimg))) {
				$AuctionIpay = Core::loader('AuctionIpay');
				if ($goodsModel->getRunout())
					$on = false;
				else
					$on = true;
				return $AuctionIpay->get_GoodsViewTag($goodsModel->getId(), $goodsModel['goodsnm'], $on, 'ǰ�� �Դϴ�.');
			}
		}
	}

	public function setMarketingCookies($params)
	{
		$expire = 86400;
		// 1��

		// ���̹� ���ļ��� ������ ��Ű����
		if ($params['nv_pchs']) {
			Clib_Application::cookie()->set('nv_pchs', $params['nv_pchs'], $expire * 30);
			// 30�� ����
		}

		//  ��ٿ� Ŭ�� ��Ű����
		if (in_array($params['ref'], array(
			'about_ad',
			'about_open'
		))) {
			foreach (array('ref','clickDate','clickNo','adType','cpcType') as $key) {
				Clib_Application::cookie()->set($key, $params[$key], $expire);
			}
		}

		//  ���� ���¼��� ������ ��Ű����
		if ($params['clickid']) {
			Clib_Application::cookie()->set('aos_clickid', $params['clickid'], $expire);
		}
	}


	protected function setGoodsAttributes(Clib_Model_Goods_Abstract $goodsModel, Clib_Model_Category_Category $categoryModel)
	{
		global $cfg, $cfgCoupon, $Acecounter, $db, $snsCfg, $cfg_related, $set;
		global $systemHeadTagEnd, $customHeader;

		// �Ⱓ����
		$discount = $goodsModel->getDiscount();

		$this->setPrevAndNextGoodsNavigationHtml($goodsModel, $categoryModel);

		$goodsModel['auth_step'] = $categoryModel->getAuthStep();
		$goodsModel['chk_point'] = $goodsModel->getReviewPoint();
		$goodsModel['point'] = $goodsModel['chk_point'] ? $goodsModel['chk_point'] : 5;
		$goodsModel['brand'] = $goodsModel->getBrandName();

		// �߰����� ����
		$goodsModel['ex_title'] = explode("|", $goodsModel['ex_title']);
		$_ex = array();
		foreach ($goodsModel['ex_title'] as $k => $v) {
			$_ex[$v] = $goodsModel["ex" . ($k + 1)];
		}
		$goodsModel['ex'] = array_notnull($_ex);

		// ������
		$goodsModel['icon'] = $this->getIconHtml($goodsModel);

		// �̹��� �迭
		$goodsModel['r_img'] = explode("|", $goodsModel['img_m']);
		$goodsModel['t_img'] = array_map("toThumb", $goodsModel['r_img']);

		if ($goodsModel['detailView'] == 'y') {
			foreach ($goodsModel['r_img'] as $key => $val) {
				if (file_exists(SHOPROOT . '/data/goods/t/' . str_replace('.', '_sc.', $val))) {
					$goodsModel['sc_img'][$key] = 't/' . str_replace('.', '_sc.', $val);
				}
				else {
					$goodsModel['r_img'][$key] = $val;
				}
			}
		}

		// ȸ������ ��������
		$member = Clib_Application::getModelClass('member_group');

		if (Clib_Application::session()->getMemberNo()) {
			$member->loadByLevel(Clib_Application::session()->getMemberLevel());
		}
		else {
			// �⺻ ������
			@include "../conf/fieldset.php";
			$member->loadByLevel($joinset['grp']);
		}

		// ȸ������ ���ܻ�ǰ üũ
		if ($member->hasLoaded()) {
			$mdc_exc = chk_memberdc_exc($member->getData(), $goodsModel->getId());
		}

		## ���� �̹��� ���
		$goodsModel['coupon_img_path'] = "/shop/data/skin/" . $cfg['tplSkin'] . "/img/common/";

		// �ʼ��ɼ� ���Ÿ�� (��ü�� single / �и��� double)
		$typeOption = $goodsModel['opttype'];

		// �ʼ��ɼ� (���� ����)
		// �ɼ� ��� ���ο� ���� ó��
		$optionCollection = Clib_Application::getCollectionClass('goods_option');
		$optionCollection->addFilter('goodsno', $goodsModel->getId());
		$optionCollection->addFilter('go_is_display', '1');
		$optionCollection->load();
		$options = $optionCollection->removeInvisible()->sort()->getIterator();

		if ( ! $goodsModel['use_option']) {
			$goodsModel['optnm'] = '';

			$options[0]['opt1'] = null;
			$options[0]['opt2'] = null;

			$m = 1;
		}
		else {
			$m = $options->count();
		}

		$optnm = explode("|", $goodsModel['optnm']);
		$idx = 0;

		for ($i = 0; $i < $m; $i++) {

			$tmp = $options[$i]->getData();
			$tmp = array_map("htmlspecialchars", $tmp);
			if ($tmp['stock'] && ! $isSelected) {
				$isSelected = 1;
				$tmp['selected'] = "selected";
				$preSelIndex = $idx++;
			}

			// �ɼǺ� ȸ�� ���ΰ�, ���� ���ΰ�, �Ⱓ ���ΰ� ���
			// ���αݾ� ��� ����
			//   �ǸŰ�
			// - �Ⱓ����
			// - ȸ������ (��ǰ ������ ���� ���� ����)
			// - ��������
			$realprice = $tmp['realprice'] = $tmp['memberdc'] = $tmp['special_discount_amount'] = $tmp['coupon'] = $tmp['coupon_emoney'] = $tmp['couponprice'] = 0;

			$tmp['special_discount_amount'] = $discount->getDiscountAmount($tmp, Clib_Application::session()->getMemberLevel());

			$group_profit = Core::loader('group_profit');
			$group_profit->getGroupProfit();
			if ( ! $goodsModel['exclude_member_discount'] && $group_profit->dc_type == 'goods') {
				if ($tmp['price'] >= $group_profit->dc_std_amt) {
					if ( ! $mdc_exc)
						$tmp['memberdc'] = getDcprice($tmp['price'], $member['dc'] . "%");
				}
			}
			$tmp['realprice'] = $tmp['price'] - $tmp['memberdc'] - $tmp['special_discount_amount'];
			if (Clib_Application::isMobile()) {
				$tmp_coupon = getCouponInfoMobile($goodsModel->getId(), $tmp['price'], 'v');
			}
			else {
				$tmp_coupon = getCouponInfo($goodsModel->getId(), $tmp['price'], 'v');
			}

			if ($cfgCoupon['use_yn'] == '1') {
				if ($tmp_coupon) {
					foreach ($tmp_coupon as $v) {
						$tp = $v['price'];
						if (substr($v['price'], - 1) == '%')
							$tp = getDcprice($tmp['price'], $v['price']);

						if ($cfgCoupon['double'] == 1) {
							if ( ! $v['ability']) {
								$tmp['coupon'] += $tp;
							}
							else {
								$tmp['coupon_emoney'] += $tp;
							}
						}
						else {
							if ( ! $v['ability'] && $tmp['coupon'] < $tp)
								$tmp['coupon'] = $tp;
							else if ($v['ability'] && $tmp['coupon_emoney'] < $tp)
								$tmp['coupon_emoney'] = $tp;
						}
					}
				}
			}

			if ($tmp['coupon'] && $tmp['memberdc'] && $cfgCoupon['range'] != '2')
				$realprice = $tmp['realprice'];
			else
				$realprice = $tmp['price'] - $tmp['special_discount_amount'];

			$tmp['couponprice'] = $realprice - $tmp['coupon'];
			if ($tmp['coupon'] && $tmp['memberdc'] && $cfgCoupon['range'] == '2')
				$tmp['realprice'] = $tmp['memberdc'] = 0;
			if ($tmp['coupon'] && $tmp['memberdc'] && $cfgCoupon['range'] == '1')
				$tmp['couponprice'] = $tmp['coupon'] = 0;
			if ( ! $optkey) {
				$optkey = $tmp['opt1'];
				$goodsModel['a_coupon'] = $tmp_coupon;
			}

			if ( ! $goodsModel['use_emoney']) {
				if ($set['emoney']['useyn'] == 'n')
					$tmp['reserve'] = 0;
				else {
					if ( ! $set['emoney']['chk_goods_emoney']) {
						$tmp['reserve'] = 0;
						if ($set['emoney']['goods_emoney'])
							$tmp['reserve'] = getDcprice($tmp['price'], $set['emoney']['goods_emoney'] . '%');
					}
					else {
						$tmp['reserve'] = $set['emoney']['goods_emoney'];
						if ( ! $tmp['reserve'])
							$tmp['reserve'] = 0;
					}
				}
			}

			if ($tmp['opt1img'])
				$opt1img[$tmp['opt1']] = $tmp['opt1img'];
			if ($tmp['opt1icon'])
				$opticon[0][$tmp['opt1']] = $tmp['opt1icon'];
			if ($tmp['opt2icon'])
				$opticon[1][$tmp['opt2']] = $tmp['opt2icon'];
			$lopt[0][$tmp['opt1']] = 1;
			$lopt[1][$tmp['opt2']] = 1;
			$opt[$tmp['opt1']][] = $tmp;
			$goodsModel['stock'] += $tmp['stock'];

		}

		## ��ٿ����� �߰����αݾ� ����
		if ($about_coupon->use && $_COOKIE['about_cp']) {
			if (count($opt) > 0) {
				foreach ($opt as $k => $optitem)
					foreach ($optitem as $subk => $v) {
						$amount_aboutdc = (int) getDcprice($v['price'], $about_coupon->sale);
						$opt[$k][$subk]['couponprice'] -= $amount_aboutdc;
						$opt[$k][$subk]['coupon'] += $amount_aboutdc;
						$opt[$k][$subk]['aboutdc_price'] = $amount_aboutdc;
					}
			}

			Clib_Application::storage()->set('about_coupon', "<span style='font-weight:bold'>��ٿ��������� ����</span>");
		}

		// find linked option;
		$optidx = 0;
		foreach($opt as $_optkey => $_opts) {
			foreach($_opts as $_k => $_opt) {
				if ($_opt['link']) {
					$optkey = $_optkey;
					$optidx = $_k;
					break(2);
				}
			}
		}

		$goodsModel['coupon'] = $goodsModel['coupon_emoney'] = 0;
		$goodsModel['price'] = $opt[$optkey][$optidx]['price'];
		$goodsModel['consumer'] = $opt[$optkey][$optidx]['consumer'];
		$goodsModel['reserve'] = $opt[$optkey][$optidx]['reserve'];
		$goodsModel['coupon'] = $opt[$optkey][$optidx]['coupon'];
		$goodsModel['couponprice'] = $opt[$optkey][$optidx]['couponprice'];
		$goodsModel['coupon_emoney'] = $opt[$optkey][$optidx]['coupon_emoney'];
		$goodsModel['memberdc'] = $opt[$optkey][$optidx]['memberdc'];
		$goodsModel['realprice'] = $opt[$optkey][$optidx]['realprice'];
		$goodsModel['special_discount_amount'] = $opt[$optkey][$optidx]['special_discount_amount'];

		$_optkind = array();
		for ($i = 0; $i < 2; $i++) {
			if (isset($opticon[$i])) {
				if (count($lopt[$i]) == count($opticon[$i])) {
					$_optkind[$i] = $goodsModel['opt' . ($i + 1) . 'kind'];
				}
				else {
					$_optkind[$i] = "select";
				}
			}
			else
				$_optkind[$i] = "select";
		}

		$goodsModel['optkind'] = $_optkind;

		$goodsModel['optnm'] = str_replace("|", "/", $goodsModel['optnm']);
		if ($opt[$optkey][$optidx]['opt1'] == null && $opt[$optkey][$optidx]['opt2'] == null)
			unset($opt);
		if ( ! $optnm[1]) {
			$typeOption = 'single';
		}

		// �̹��� �迭
		if ($opt1img) {
			$goodsModel['img_m'] .= "|" . @implode('|', $opt1img);
		}
		$goodsModel['r_img'] = explode("|", $goodsModel['img_m']);
		$goodsModel['t_img'] = array_map("toThumb", $goodsModel['r_img']);
		$goodsModel['l_img'] = explode("|", $goodsModel['img_l']);

		// ����� ���� �ڵ� ǰ�� ó��
		if ($goodsModel['usestock'] && $goodsModel['stock'] == 0)
			$goodsModel['runout'] = 1;

		// ����Ʈ ��ũ����ư
		if ( ! Clib_Application::isMobile()) {
			$goodsModel['cyworldScrap'] = $this->getNateScrapButtonHtml($goodsModel);
		}

		// qrcode view
		$goodsModel['qrcode_view'] = $this->getGoodsQrCodeHtml($goodsModel);

		// ���� iPay
		$goodsModel['auctionIpayBtn'] = $this->getAuctionIpayButtonHtml($goodsModel);

		// ���԰� �˸� ��ư ���� ����
		if ($goodsModel['usestock'] == "o" && $goodsModel['use_stocked_noti']) {
			if ($goodsModel->getOptions()->getEmptyStockCount() >= 1 || $goodsModel->getRunout()) {
				$goodsModel['stocked_noti'] = true;
			}
		}

		// SNS
		if ($snsCfg['useBtn'] == 'y') {
			require_once SHOPROOT."/lib/sns.class.php";
			$sns = new SNS();

			$goodsurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?goodsno=' . $goodsModel->getId();

			$get_post_btn = 'get_post_btn';

			if (Clib_Application::isMobile()) {
				include SHOPROOT."/conf/config.mobileShop.php";
				$sns->mobileSkin = true;
				$screen = 'm';
				$goodsurl = preg_replace('/\/m\/goods\/view/', '/shop/goods/goods_view', $goodsurl);

				if ($cfgMobileShop['mobileShopRootDir'] == '/m2') {
					$get_post_btn = 'get_post_btn_mobile';
				}
			}
			else {
				$screen = '';
			}

			$args = array(
				'shopnm' => $cfg['shopName'],
				'goodsnm' => $goodsModel['goodsnm'],
				'goodsurl' => $goodsurl,
				'img' => $goodsModel['img_s'],
				'img_l' => $goodsModel['img_l']
			);
			$snsRes = call_user_func_array(array($sns, $get_post_btn), array($args, $screen));
			$customHeader .= $snsRes['meta'];
			// ���̽��Ͽ� ���� meta tag
			$goodsModel['snsBtn'] = $snsRes['btn'];

			// ������������� īī����ũ�� ����ϸ�, ���̿��� ������ ������� ����
			if(Clib_Application::isMobile() && $snsCfg['use_kakao'] == 'y') {
				//Ver 2.0
				$goodsModel['msg_kakao1'] = $sns->msg_kakao1;
				$goodsModel['msg_kakao2'] = $sns->msg_kakao2;
				$goodsModel['msg_kakao3'] = $sns->msg_kakao3;

				//Ver 3.5
				@include_once SHOPROOT . '/lib/kakaotalkLink.class.php';
				$kakaotalkLink = new KakaotalkLink();
				$goodsModel['kakaoTalkLinkScript'] = $kakaotalkLink->getKakaoScript(get_object_vars($sns));
			}

			// īī�����丮
			if(Clib_Application::isMobile() && $snsCfg['use_kakaoStory'] == 'y') {
				$goodsModel['msg_kakaoStory_shopnm']	= $sns->msg_kakaoStory_shopnm;
				$goodsModel['msg_kakaoStory_goodsnm']	= $sns->msg_kakaoStory_goodsnm;
				$goodsModel['msg_kakaoStory_goodsurl']	= $sns->msg_kakaoStory_goodsurl;
				$goodsModel['msg_kakaoStory_img_l']		= $sns->msg_kakaoStory_img_l;
			}
		}
		// SNS

		// ��ǰ �ʼ� ����
		$goodsModel['extra_info'] = $this->getExtraInformationHtml($goodsModel);

		// �ڵ��ǰ ���� �ε�
		$goodsModel['setGoodsConfig'] = $GLOBALS['setGoodsConfig']['setconnection'];

		// ũ���׿�
		$criteo = new Criteo();
		if ($criteo->begin()) {
			$criteo->get_detail($goodsModel->getId());
			$systemHeadTagEnd .= $criteo->scripts;
		}

		$goodsModel['naverCheckout'] = $this->getNaverCheckoutButtonHtml($goodsModel);

		// ���̹� ���ϸ���
		if (Clib_Application::isMobile()) {
			$goodsModel['NaverMileageAccum'] = $this->getNaverMileageAccumHtml($goodsModel);
		}
		else {
			$goodsModel['naverNcash'] = "";
			$naverNcash = Core::loader('naverNcash');
			if ( ! $naverNcash->realyn())
				$naverNcash->useyn = "N";
			$item[0]['goodsno'] = $goodsModel->getId();
			$exceptionYN = $naverNcash->exception_goods($item);
			// ���ܻ�ǰ üũ
			if ($naverNcash->useyn == 'Y' && $exceptionYN == 'N' && $naverNcash->baseAccumRate) {
				$goodsModel['naverNcash'] = "Y";
				$goodsModel['N_ba'] = preg_replace('/\.0$/', '', $naverNcash->get_base_accum_rate());
				$goodsModel['N_aa'] = preg_replace('/\.0$/', '', $naverNcash->get_add_accum_rate());
				$goodsModel['NaverMileageAccum'] = true;
			}
			else if ($naverNcash->useyn == 'Y' && $exceptionYN == 'Y' && $naverNcash->baseAccumRate) {
				$goodsModel['naverNcash'] = "Y";
				$goodsModel['exception'] = "���� �� ��� ���� ��ǰ";
			}
			else {
				$goodsModel['naverNcash'] = $goodsModel['N_ba'] = $goodsModel['N_aa'] = "";
			}
		}

		//������
		if(is_file(SHOPROOT . '/lib/payco.class.php')){
			$goodsModel['Payco'] = Core::loader('payco')->getButtonHtmlCode('CHECKOUT', Clib_Application::isMobile(), 'goodsView');
		}

		// ���ú���ǰ ��Ű�� ����
		if ( ! Clib_Application::isMobile()) {
			$todayG = array(
				'goodsno' => $goodsModel->getId(),
				'goodsnm' => $goodsModel['goodsnm'],
				'price' => $goodsModel['price'],
				'img' => $goodsModel['img_s'],
			);
			todayGoods($todayG);
		}
		else {
			$todayG = array(
					'goodsno'	=> $goodsModel->getId(),
					'goodsnm'	=> $goodsModel['goodsnm'],
					'strprice'	=> $goodsModel['strprice'],
					'price'		=> $goodsModel['price'],
					'img'		=> $goodsModel['img_s'],
					'dc'		=> $goodsModel['discount_mobile'],
			);
			// �̹��� �߰� (img �������̵� ���Ѽ� ����Ͽ� �̹��� ó��)
			if ($goodsModel['use_mobile_img'] === '1') {
				$todayG['img'] = $goodsModel['img_x'];
			} else if ($goodsModel['use_mobile_img'] === '0') {
				$todayImg = explode('|', $goodsModel[$goodsModel['img_pc_x']]);
				$todayG['img'] = $todayImg[0];
			}
			todayGoodsMobile($todayG);
		}

		// ace ī����
		if (is_object($Acecounter)) {
			$Acecounter->goods_view($goodsModel->getId(), $goodsModel['goodsnm'], $goodsModel['price'], $categoryModel->getId());
			if ($Acecounter->scripts) {
				$systemHeadTagEnd .= $Acecounter->scripts;
			}
		}

		// �߰��ɼ�
		$r_addoptnm = explode("|", $goodsModel['addoptnm']);
		$r_addoptnm = array_map("htmlspecialchars", $r_addoptnm);
		for ($i = 0; $i < count($r_addoptnm); $i++)
			list($addoptnm[], $_addoptreq[], $_addopttype[]) = explode("^", $r_addoptnm[$i]);
		$query = "select * from " . GD_GOODS_ADD . " where goodsno='" . $goodsModel->getId() . "' order by type,step,sno";
		$res = $db->query($query);
		$_offset = 0;
		while ($tmp = $db->fetch($res, 1)) {
			$tmp['opt'] = htmlspecialchars($tmp['opt']);
			if ($tmp['type'] == 'I') {
				$_offset = (int) array_search('I', $_addopttype);
				$addopt_inputable[$addoptnm[$_offset + $tmp['step']]] = $tmp;
				$addopt_inputable_req = array_slice($_addoptreq, $_offset);
			}
			else {
				$addopt[$addoptnm[$tmp['step']]][] = $tmp;
				$addoptreq = $_offset > 0 ? array_slice($_addoptreq, 0, $_offset) : $_addoptreq;
			}
		}

		// ��Ÿ����
		if ($goodsModel['meta_title'])
			$meta_title = $cfg['shopName'] . " " . strip_tags($goodsModel['goodsnm']);
		$meta_keywords = $goodsModel['keyword'];

		// ���� ��ǰ ���� ���ø��� �߰�
		if ($cfg_related) {
			$cfg_related['max'] = $cfg_related['horizontal'] * $cfg_related['vertical'];

			// ��ٱ��� ������
			if (is_numeric($cfg_related['cart_icon'])) {
				$cfg_related['cart_icon'] = '../data/goods/icon/icon_basket' . $cfg_related['cart_icon'] . '.gif';
			}
			else {
				$cfg_related['cart_icon'] = '../data/goods/icon/custom/basket';
			}

		}

		$goodsModel['goods_status'] = $this->getGoodsStatus($goodsModel['goods_status']);

		$goodsModel['delivery_method'] = implode(',', explode('|', $goodsModel['delivery_method']));

		$goodsModel['manufacture_date'] = $goodsModel['manufacture_date'] == '0000-00-00' ? '' : $goodsModel['manufacture_date'];
		$goodsModel['effective_date_start'] = $goodsModel['effective_date_start'] == '0000-00-00' ? '' : $goodsModel['effective_date_start'];
		$goodsModel['effective_date_end'] = $goodsModel['effective_date_end'] == '0000-00-00' ? '' : $goodsModel['effective_date_end'];

		if ($goodsModel['min_ea'] < $goodsModel['sales_unit']) {
			$goodsModel['min_ea'] = $goodsModel['sales_unit'];
		}

		$goodsModel['sales_status'] = $goodsModel->getSalesStatus();
		$goodsModel['sales_range_start'] = $goodsModel->getSalesRangeStart();
		$goodsModel['sales_range_end'] = $goodsModel->getSalesRangeEnd();

		// ��ü���϶�, �ɼǰ� ������
		if (count($opt) > 0 && $typeOption == 'single') {
			$_opt = array();
			foreach ($opt as $optitem) {
				foreach ($optitem as $v) {
					$_opt[] = $v;
				}
			}

			uasort($_opt, array($optionCollection, 'compareSort'));

			$opt = array();
			$k = 0;
			foreach($_opt as $v) {
				$opt[$k++][] = $v;
			}
		}

		// launchdt 0000-00-00 �϶�, null �� ����
		if(!strtotime($goodsModel['launchdt']) || $goodsModel['launchdt'] == '0000-00-00') {
			$goodsModel['launchdt'] = null;
		}

		if($meta_title) {
			if(Clib_Application::storage()->has('meta_title')) {
				Clib_Application::storage()->del('meta_title');
			}
			Clib_Application::storage()->set('meta_title', $meta_title);
		}

		if ($goodsModel['speach_description_useyn'] === 'y' && strlen($goodsModel['speach_description']) > 0) {
			$goodsModel['tts_url'] = Core::loader('TextToSpeach')->getURL($goodsModel['speach_description']);
		}
		else {
			$goodsModel['tts_url'] = '';
		}

		Clib_Application::storage()->set('opt', $opt);
		Clib_Application::storage()->set('opt1img', $opt1img);
		Clib_Application::storage()->set('addopt', $addopt);

		Clib_Application::storage()->set('addopt_inputable_req', $addopt_inputable_req);
		Clib_Application::storage()->set('addoptreq', $addoptreq);
		Clib_Application::storage()->set('addopt_inputable', $addopt_inputable);
		Clib_Application::storage()->set('optnm', $optnm);
		Clib_Application::storage()->set('opticon', $opticon);
		Clib_Application::storage()->set('typeOption', $typeOption);

		return $goodsModel;

	}

	protected function setGoodsCollectionAttributes($collection, $category)
	{
		global $cfg_soldout,$displayCfg;

		if (method_exists($category, 'getConfig')) {
			$lstcfg = $category->getConfig();
		}
		else {
			global $lstcfg;
		}

		$list = array();

		foreach ($collection as $item) {

			$item['brandnm'] = $item->getBrandName();
			$item['stock'] = $item['totstock'];
			$item['price'] = $item->getPrice();
			$item['consumer'] = $item->getGoodsConsumer();
			$item['special_discount_amount'] = $item->getSpecialDiscountAmount();

			### ����� ���� �ڵ� ǰ�� ó��
			$item['runout'] = $item->getRunout();

			### ������ ��å����
			$item['reserve'] = $item->getReserve();

			### �Ｎ�������� ��ȿ�� �˻� (pc or mobile)
			list($item['coupon'], $item['coupon_emoney']) = getCouponInfo($item['goodsno'], $item['price']);
			// getCouponInfoMobile

			$item['reserve'] += $item['coupon_emoney'];

			### ��ٿ����� �ݾ�
			if (Core::loader('about_coupon')->use) {
				$item['coupon'] += (int) getDcPrice($item['price'], $about_coupon->sale);
			}

			### ������
			$item['icon'] = $this->getIconHtml($item);

			if ($category instanceof Clib_Model_Category_Category) {
				$item['auth_step'] = $category->getAuthStep();
				$item['level'] = $category->getLevel();
			}

			// ��ǰ���� ���� ǥ��
			if ($displayCfg['displayType'] === 'discount') {
				$discountModel = '';
				$goodsDiscount = '';
				if ($item['use_goods_discount'] === '1') {
					$discountModel = Clib_Application::getModelClass('Goods_Discount');
					$goodsDiscount = $discountModel->getDiscountAmountSearch($item);
				}
				if ($goodsDiscount) {
					$item['oriPrice'] = $item['price'];
					$item['goodsDiscountPrice'] = $item['price'] - $goodsDiscount;
				}
				else {
					$item['oriPrice'] = '0';
					$item['goodsDiscountPrice'] = $item['price'];
				}
			}
			
			$item['shortdesc'] = ($lstcfg['tpl'] == "tpl_10") ? htmlspecialchars($item['shortdesc']) : $item['shortdesc'];
			// ©������ ���� ����

			// ��ǰ URL
			$item['goods_view_url'] = $this->getGoodsViewUrl($item, $category);

			// ���� ���� ��ǰ�϶� �̹��� ��ü
			if ($item['use_only_adult'] && ! Clib_Application::session()->canAccessAdult()) {
				$item['img_i'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
				$item['img_s'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
				$item['img_m'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
				$item['img_l'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
			}

			// ����, ��ǰ��, ��Ÿ����(����, ������, ������ ��) ��� ����
			$item['is_open_price'] = (($item['level'] == 0 || Clib_Application::session()->getMemberLevel() >= $item['level']) || $item['auth_step'][2] == 'Y') ? true : false;
			$item['is_open_extra'] = ($item['level'] == 0 || Clib_Application::session()->getMemberLevel() >= $item['level']) ? true : false;
			$item['is_open_name'] = (($item['level'] == 0 || Clib_Application::session()->getMemberLevel() >= $item['level']) || $item['auth_step'][1] == 'Y') ? true : false;

			// �� ��� ���ο� ���� ó��
			if ($item['is_open_extra'] === false) {
				$item['icon'] = '';
				$item['coupon'] = '';
			}
			else {
				if ($item['runout'] && $cfg_soldout['icon'] == '0')
					$item['icon'] = '';
				if ($item['runout'] && $cfg_soldout['coupon'] == '0')
					$item['coupon'] = '';
			}

			// ǰ����ǰ ����
			if ($item['runout']) {

				if ($cfg_soldout['display'] == 'icon')
					$item['soldout_icon'] = ($cfg_soldout['display_icon'] == 'custom') ? 'custom' : 'skin';
				elseif ($cfg_soldout['display'] == 'overlay') {
					$item['soldout_overlay'] = ($cfg_soldout['display_overlay'] == 'custom') ? '../data/goods/icon/custom/soldout_overlay' : '../data/goods/icon/icon_soldout' . $cfg_soldout['display_overlay'];
					$item['css_selector'] = 'el-goods-soldout-image';
				}
				else
					$item['soldout_icon'] = 'skin';

				if ($cfg_soldout['display'] == 'none') {
					$item['soldout_icon'] = $item['css_selector'] = "";
				}

				if ($cfg_soldout['goodsnm'] == '0')
					$item['goodsnm'] = '';

				if ($cfg_soldout['price'] == '0') {

				}
				elseif ($cfg_soldout['price'] == 'string') {// ��ü����
					$item['price'] = '';
					$item['soldout_price_string'] = $cfg_soldout['price_string'];
				}
				elseif ($cfg_soldout['price'] == 'image') {// ��ü����
					$item['price'] = '';
					$item['soldout_price_image'] = '<img src="../data/goods/icon/custom/soldout_price">';
				}
				else {
					$item['price'] = $item['price'];
				}

			}

			if ($item['is_open_name'] === false || ($item['runout'] && $cfg_soldout['goodsnm'] == '0')) {
				$item['goodsnm'] = '';
			}

			if ($item['is_open_price'] === false) {
				$item['price'] = '';
			}

		}

		return $collection;

	}

	public function getGoodsViewUrl($goodsModel, $categoryModel = null)
	{
		$url = '../goods/goods_view.php?goodsno=' . $goodsModel->getId();

		// ����
		if (strpos($_SERVER['PHP_SELF'], 'goods/goods_view.php') !== false && $goodsModel->getUseOnlyAdult() && ! Clib_Application::session()->canAccessAdult()) {
			$returnUrl = $_SERVER['PHP_SELF'] . '?goodsno=' . $goodsModel->getId();
			$urlQuery = getReUrlQuery('goodsno,category', $_SERVER['REQUEST_URI']);
			if ($urlQuery != '') $returnUrl .= '&' . $urlQuery;

			if(Clib_Application::session()->getMemberId()){		//ȸ����������
				$auth_date = getAdultAuthDate(Clib_Application::session()->getMemberId());
				$auth_date = $auth_date['auth_date'];
				$current_date = date("Y-m-d");
				$auth_period = strtotime("+1 years", strtotime($auth_date)); 
				$auth_period = date("Y-m-d", $auth_period);

				if(($auth_date == '0000-00-00' || $current_date > $auth_period) && ((int)($session->level) < 80)){
					$url = '../main/intro_adult_login.php?returnUrl=' . urlencode($returnUrl);
				}
			}
			else{
				$url = '../main/intro_adult.php?returnUrl=' . urlencode($returnUrl);
			}
		}
		else if ($categoryModel instanceof Clib_Model_Category_Category && $categoryModel->hasLoaded()) {
			if (Clib_Application::session()->getMemberLevel() < $categoryModel['level'] && $categoryModel['level_auth'] == 3) {
				$url = 'javascript:msg_back();';
			}
			else {
				$url .= '&category=' . $categoryModel->getId();
			}
		}

		return $url;
	}
	// ����� ���� ���� URL - ����� ��ǰ ���� �������� ����
	public function getGoodsViewUrlMobile($goodsModel, $categoryModel = null)
	{
		$url = '../goods/view.php?goodsno=' . $goodsModel->getId();

		// ����
		if (strpos($_SERVER['PHP_SELF'], 'goods/view.php') !== false && $goodsModel->getUseOnlyAdult() && ! Clib_Application::session()->canAccessAdult()) {
			$returnUrl = $_SERVER['PHP_SELF'] . '?goodsno=' . $goodsModel->getId();
			$urlQuery = getReUrlQuery('goodsno,category', $_SERVER['REQUEST_URI']);
			if ($urlQuery != '') $returnUrl .= '&' . $urlQuery;

			if(Clib_Application::session()->getMemberId()){		//ȸ����������
				$auth_date = getAdultAuthDate(Clib_Application::session()->getMemberId());
				$auth_date = $auth_date['auth_date'];
				$current_date = date("Y-m-d");
				$auth_period = strtotime("+1 years", strtotime($auth_date)); 
				$auth_period = date("Y-m-d", $auth_period);

				if(($auth_date == '0000-00-00' || $current_date > $auth_period) && ((int)($session->level) < 80)){
					$url = '../intro/intro_adult_login.php?returnUrl=' . urlencode($returnUrl);
				}
			}
			else{
				$url = '../intro/intro_adult.php?returnUrl=' . urlencode($returnUrl);
			}
		}
		else if ($categoryModel instanceof Clib_Model_Category_Category && $categoryModel->hasLoaded()) {
			if (Clib_Application::session()->getMemberLevel() < $categoryModel['level'] && $categoryModel['level_auth'] == 3) {
				$url = 'javascript:msg_back();';
			}
			else {
				$url .= '&category=' . $categoryModel->getId();
			}
		}

		return $url;
	}

}
