<?php
/**
 * Clib_Helper_Admin_Goods
 */
class Clib_Helper_Admin_Goods extends Clib_Helper_Front_Goods
{

	public function getGoodsCollection($params)
	{
		$collection = Clib_Application::getCollectionClass('admin_goods');
		$collection = $this->prepareGoodsCollection($collection, $params);
		$collection->load();

		return $collection;
	}

	public function prepareGoodsCollection($collection, $params)
	{

		$collection->setCurrentPage($params['page']);
		$collection->setPageSize($params['page_num']);

		if ($params['indicate'] == 'main') {
			// ��ǰ ��������

			$collection->setDisplayAreaFilter($params['smain']);
		}
		else if ($params['indicate'] == 'event') {
			// �̺�Ʈ

			$collection->setEventFilter($params['sevent']);
		}
		else {
			// �⺻ (�˻�)

			// ī�װ�
			if ($category = $params['cate']) {
				$category = array_pop(array_notnull($category));
				$collection->setCategoryFilter($category, basename($_SERVER['PHP_SELF']) == 'adm_goods_manage_link.php' ? false : true );
			}

			// Ű���� �˻�
			if (($skey = $params['skey']) && ($sword = $params['sword'])) {

				$sword = trim($sword);

				if (strpos($sword, 'goodsno') !== 0 && preg_match('/^A([0-9]+)$/', $sword, $matches)) {
					$sword = (int) $matches[1];
				}

				$collection->setKeywordFilter($sword, $skey, 'like');
			}

			// ��� �˻�
			if ($stock = $params['stock']) {
				$collection->setStock($params['stock']);
			}

			// �����
			$collection->setRegdtFilter($params['regdt']);

			// ����
			$collection->setPriceFilter($params['price']);

			// ��ǥ����
			$collection->setGoodsPriceFilter($params['goods_price']);

			// ��ǰ��������
			$collection->setOpenFilter($params['open']);

			// ǰ����ǰ
			$collection->setSoldoutFilter($params['soldout']);

			// �귣��
			$collection->setBrandFilter($params['brandno']);

			// ��ǰ�� ��ۺ�
			$collection->setDeliveryTypeFilter($params['delivery_type']);

			// ���ο���
			$collection->setDiscountFilter($params['discount']);

			// ���αⰣ
			$collection->setDiscountRangeFilter($params['discount_range']);

			// ����
			if ($params['searchColor']) {
				$collection->setColorFilter($params['searchColor']);
			}

			// �з� �̿��� ��ǰ
			if ($params['unlink'] == 'Y') {
				$collection->setCategoryUnlinkedFilter();
			}

			// �귣�� �̿��� ��ǰ
			if ($params['unbrand'] == 'Y') {
				$collection->setBrandUnlinkedFilter();
			}

			// �Ǹ���� �˻�
			$collection->setStockFilter($params['stock_amount'], $params['stock_type']);

			// ������
			$collection->setOriginFilter($params['origin']);

			// ������
			$collection->setIconFilter((array)$params['sicon']);



		}

		// ����
		$collection->setOrder($params['sort']);

		return $collection;

	}

}
