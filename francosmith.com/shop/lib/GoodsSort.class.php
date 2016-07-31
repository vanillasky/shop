<?php

/**
 * Copyright (c) 2013 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 * workingparksee    2013.06.03        First Draft.
 * workingparksee    2013.09.25        changeManualSortOnLinkGoodsPosition �޼��� �߰�
 */

/**
 * ��ǰ���� Ŭ����
 *
 * @author GoodsSort.class.php workingparksee <parksee@godo.co.kr>
 * @version 1.0
 * @date 2013-06-03, 2013-09-25
 */
class GoodsSort
{
	private $_dbo, $_configurer, $_config;

	public $limitSet = array(10, 30, 50, 100, 200, 300);

	public function __construct()
	{
		$this->_dbo = Core::loader('db');
		$this->_configurer = Core::loader('config');

		$this->_config = $this->_configurer->load('GOODS_SORT');
		if (!$this->_config['viewType']) $this->_config['viewType'] = 'LIST';
		if (!$this->_config['imageSize']) $this->_config['imageSize'] = 25;
		if (!$this->_config['limitRows']) $this->_config['limitRows'] = 10;
	}

	/**
	 * ��ǰ�������� �������� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $name
	 * @param string $value
	 * @date 2013-07-10, 2013-07-10
	 */
	function saveConfig($name, $value)
	{
		$this->_configurer->save('GOODS_SORT', array($name => $value));
		$this->_config[$name] = $value;
	}

	/**
	 * ��ǰ�������� �������� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $name
	 * @return string
	 * @date 2013-07-10, 2013-07-10
	 */
	function getConfig($name)
	{
		return $this->_config[$name];
	}

	/**
	 * ������ ī�װ��� ����Ÿ���� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @param string $sortType ���� �ΰ��� ���� �ϳ��� �;� �� MANUAL, AUTO
	 * @return boolean
	 * @date 2013-06-03, 2013-09-25
	 */
	function changeCategorySortType($category, $sortType)
	{
		if (!in_array($sortType, array('AUTO', 'MANUAL'), true)) return false;
		$result = $this->_dbo->query('UPDATE gd_category SET sort_type="'.$sortType.'" WHERE category="'.$category.'"');
		if ($sortType === 'MANUAL' && $this->_dbo->affected()) $this->optimizeManualSort($category);
		return ($result ? true : false);
	}

	/**
	 * �������� ī�װ��� ��ǰ���� �� ���� ��ġ�� ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category ī�װ�
	 * @param string $manualSortOnLinkGoodsPosition ENUM(LAST, FIRST) �������� ī�װ��� ��ǰ���� �� ���� ��ġ
	 * @return boolean
	 * @date 2013-09-25, 2013-09-25
	 */
	function changeManualSortOnLinkGoodsPosition($category, $manualSortOnLinkGoodsPosition)
	{
		if (!in_array($manualSortOnLinkGoodsPosition, array('LAST', 'FIRST'), true)) return false;
		$result = $this->_dbo->query('
			UPDATE gd_category
			SET manual_sort_on_link_goods_position="'.$manualSortOnLinkGoodsPosition.'"
			WHERE category="'.$category.'"
		');
		return ($result ? true : false);
	}

	/**
	 * ������ ī�װ��� ����� ��ǰ�� ���������� 1�� ������Ų��
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category ī�װ�
	 * @param string $sortField �����ʵ�
	 * @date 2013-09-26, 2013-09-26
	 */
	function increaseCategorySort($category, $sortField)
	{
		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$categoryWhere	= getCategoryLinkQuery('category', $category, 'where');

		$this->_dbo->query('
			UPDATE gd_goods_link
			SET '.$sortField.'='.$sortField.'+1
			WHERE '.$categoryWhere
		);
	}

	/**
	 * �������� ī�װ��� ��Ʈ�� ����ȭ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @return string|boolean ���� �� true, �̿ܿ��� �����ڵ� ��ȯ
	 * @date 2013-06-03, 2013-06-03
	 */
	public function optimizeManualSort($category)
	{
		$sortField = $this->getSortField($category);

		$categoryInfo = $this->_dbo->fetch('SELECT `sort_type` FROM '.GD_CATEGORY.' WHERE `category`="'.$category.'"');
		if ($categoryInfo['sort_type'] !== 'MANUAL') return 'ERROR_CATEGORY_IS_NOT_MANUAL_SORT';

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$whereArr	= getCategoryLinkQuery('category', $category, null, '__gl.goodsno');

		$this->_dbo->query('SET @SEQUENCE = 0');
		$result = $this->_dbo->query('
			UPDATE gd_goods_link AS gl
			INNER JOIN (
				SELECT _gl.goodsno, @SEQUENCE := @SEQUENCE + 1 AS sequence
				FROM (
					SELECT __gl.goodsno FROM gd_goods_link AS __gl
					WHERE __gl.'.$whereArr['where'].'
					'.$whereArr['group'].'
					ORDER BY __gl.'.$sortField.' ASC, __gl.sort ASC
				) AS _gl,
				(SELECT @SEQUENCE := 0) AS _sequence
			) AS seqmap
			ON gl.goodsno = seqmap.goodsno AND gl.'.$whereArr['where'].'
			SET gl.'.$sortField.' = seqmap.sequence
		');
		if (!$result) return 'ERROR_OPTIMIZE';

		$result = $this->_dbo->query('UPDATE `gd_category` SET `sort_max`=@SEQUENCE WHERE `category`="'.$category.'"');
		if (!$result) return 'ERROR_SET_SORT_MAX';

		return true;
	}

	/**
	 * ������ ī�װ��� �����ʵ���� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @param string $tableAlias ������ ���̺� Alias�� ������ ���, ���̺� ������ Alias
	 * @return null
	 * @date 2013-06-03, 2013-06-03
	 */
	public function getSortField($category, $tableAlias = null)
	{
		$categoryInfo = $this->_dbo->fetch('SELECT sort_type FROM '.GD_CATEGORY.' WHERE category="'.$category.'"', true);
		if ($categoryInfo['sort_type'] === 'MANUAL') {
			$categoryDepth = (strlen($category) / 3);
			if ($categoryDepth === 2) $sortField = 'sort2';
			else if ($categoryDepth === 3) $sortField = 'sort3';
			else if ($categoryDepth === 4) $sortField = 'sort4';
			else $sortField = 'sort1';
		}
		else {
			$sortField = 'sort';
		}
		return ($tableAlias === null) ? $sortField : $tableAlias.'.'.$sortField;
	}

	/**
	 * ������ ī�װ��� �������� ���� ��ǰ����Ʈ�� ��ȸ �� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @param int $page
	 * @param int $limit
	 * @return array
	 * @date 2013-06-03, 2013-09-25
	 */
	public function fetchGoodsListByCategory($category, $page, $limit)
	{
		// page �� ��ȿ�� ����
		$page = $page ? $page : 1;

		// limit �� ��ȿ�� ����
		if (!$limit) $limit = $this->limitSet[0];
		else if ($limit > $this->limitSet[count($this->limitSet)-1]) $limit = $this->limitSet[count($this->limitSet)-1];
		else ;

		// ī�װ� ����Ʈ Ȯ��
		$categoryInfo = $this->_dbo->fetch('SELECT sort_type, manual_sort_on_link_goods_position FROM '.GD_CATEGORY.' WHERE category="'.$category.'"', true);

		$sortField = $this->getSortField($category, 'gl');

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$whereArr	= getCategoryLinkQuery('c.category', $category, null, 'gl.goodsno');

		// ��ǰ ��ȸ
		$query = '
		SELECT
			gl.goodsno, '.$sortField.' AS sort, gl.sno, g.goodsnm, g.img_s, g.icon, g.optnm, g.runout, g.usestock, g.totstock, g.open,
			IF (g.runout=1, 1, IF (g.usestock = "o" AND g.totstock = 0, 1, 0)) as soldout
		FROM
			'.GD_CATEGORY.' AS c FORCE INDEX (idx)	-- gd_category���̺��� �������� ������ ���� ��ǰ������������ ���������� �ٸ�
			LEFT JOIN '.GD_GOODS_LINK.' AS gl ON c.category=gl.category
			INNER JOIN '.GD_GOODS.' AS g on gl.goodsno=g.goodsno
		WHERE '.$whereArr['where'].'
		'.$whereArr['group'].'
		ORDER BY '.$sortField.' ASC';
		$pageGoods = $this->_dbo->_select_page($limit, $page, $query);

		$goodsnoList = array();
		foreach ($pageGoods['record'] as &$record) {
			$record['optnm'] = explode('|', $record['optnm']);
			$record['imageTag'] = goodsimg($record['img_s'], '130', '', 1);
			$goodsnoList[] = $record['goodsno'];
			$record['goodsnm'] = strip_tags($record['goodsnm']);
		}

		$lookupGoodsOption = $this->_dbo->query('SELECT goodsno, opt1, opt2, link, price FROM '.GD_GOODS_OPTION.' WHERE goodsno IN('.implode(',', $goodsnoList).') and go_is_deleted <> \'1\' and go_is_display = \'1\' ORDER BY goodsno ASC');
		while ($goodsOption = $this->_dbo->fetch($lookupGoodsOption)) {
			foreach ($pageGoods['record'] as &$record) {
				if ($record['goodsno'] == $goodsOption['goodsno']) {
					if (isset($record['option']) === false) $record['option'] = array(array(), array());
					if ($goodsOption['link'] == '1') $record['price'] = $goodsOption['price'];
					if (strlen(trim($goodsOption['opt1'])) && !in_array($goodsOption['opt1'], $record['option'][0])) $record['option'][0][] = $goodsOption['opt1'];
					if (strlen(trim($goodsOption['opt2'])) && !in_array($goodsOption['opt2'], $record['option'][1])) $record['option'][1][] = $goodsOption['opt2'];
				}
			}
		}

		$pageGoods['category'] = array(
		    'category' => $category,
		    'sortType' => $categoryInfo['sort_type'],
		    'manualSortOnLinkGoodsPosition' => $categoryInfo['manual_sort_on_link_goods_position'],
		);

		return $pageGoods;
	}

	/**
	 * ������ ����Ʈ �� �������θ� ������ ����
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @param array $modifiedSortSet ������ ���İ�
	 * @param array $modifiedOpenSet ������ ��������
	 * @return boolean
	 * @date 2013-06-03, 2013-06-03
	 */
	function applyModified($category, $modifiedSortSet, $modifiedOpenSet)
	{
		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$categoryWhere	= getCategoryLinkQuery('category', $category, 'where');

		// ī�װ� ����Ʈ Ȯ��
		$categoryInfo = $this->_dbo->fetch('SELECT sort_type FROM '.GD_CATEGORY.' WHERE category="'.$category.'"', 1);
		if ($categoryInfo['sort_type'] === 'MANUAL') {
			$sortField = $this->getSortField($category);
			foreach ($modifiedSortSet as $goodsno => $sort) {
				$result = $this->_dbo->query('UPDATE gd_goods_link SET '.$sortField.'='.$sort.' WHERE goodsno='.$goodsno.' AND '.$categoryWhere);
				if (!$result) return false;
			}
		}
		foreach ($modifiedOpenSet as $goodsno => $open) {
			$result = $this->_dbo->query('UPDATE gd_goods SET open='.$open.' WHERE goodsno='.$goodsno);
			if (!$result) return false;
		}
		return true;
	}

	/**
	 * ���õ� ��ǰ����Ʈ�� Ư�� �������� �̵�
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @param array $selectedSortSet ������ ���İ�
	 * @param int $currentPage
	 * @param int $targetPage
	 * @param int $limit
	 * @param string $position TOP�� �ֻ��, BOTTOM�� ���ϴ�
	 * @return string|boolean ������ true, �������� �����ڵ� ��ȯ
	 * @date 2013-06-03, 2013-06-03
	 */
	function movePageSelection($category, $selectedSortSet, $currentPage, $targetPage, $limit, $position)
	{
		asort($selectedSortSet);
		$target = $this->fetchGoodsListByCategory($category, $targetPage, $limit);
		$targetRecordCount = count($target['record']);
		$selectedCount = count($selectedSortSet);
		if ($targetRecordCount < $selectedCount) return 'ERROR_NOT_ENOUGH_TARGET_RECORD';
		if ($currentPage < $targetPage) {
			$sortStart = max($selectedSortSet) + 1;
			if ($position === "TOP") {
				$sortEnd = $target['record'][$selectedCount - 1]['sort'];
			}
			else {
				$sortEnd = $target['record'][$targetRecordCount - 1]['sort'];
			}
			$spacing = '-'.$selectedCount;
			$increasePoint = $sortEnd - $selectedCount + 1;
		}
		else {
			if ($position === "TOP") {
				$sortStart = $target['record'][0]['sort'];
			}
			else {
				$sortStart = $target['record'][count($target['record']) - $selectedCount]['sort'];
			}
			$sortEnd = min($selectedSortSet) - 1;
			$spacing = '+'.$selectedCount;
			$increasePoint = $sortStart;
		}

		// ī�װ� ����Ʈ Ȯ��
		$categoryInfo = $this->_dbo->fetch('SELECT sort_type FROM '.GD_CATEGORY.' WHERE category="'.$category.'"', 1);

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$categoryWhere	= getCategoryLinkQuery('category', $category, 'where');

		$sortField = $this->getSortField($category);
		$result = $this->_dbo->query('UPDATE gd_goods_link SET '.$sortField.'='.$sortField.$spacing.' WHERE '.$categoryWhere.' AND '.$sortField.' BETWEEN '.$sortStart.' AND '.$sortEnd);
		if (!$result) return 'ERROR_FILL_SPACING';
		$index = 0;
		foreach (array_keys($selectedSortSet) as $sno) {
			$result = $this->_dbo->query('UPDATE gd_goods_link SET '.$sortField.'='.($increasePoint + $index).' WHERE sno='.$sno);
			$index++;
			if (!$result) return 'ERROR_SORTING';
		}
		return true;
	}

	/**
	 * ������ ī�װ��� �������� ���İ��� �ִ�ġ�� ��ȯ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @return array
	 * @date 2013-06-03, 2013-09-25
	 */
	public function getManualSortInfoHierarchy($category)
	{
		$categoryList = array();
		for ($length = 3; $length <= strlen($category); $length+=3) {
			$categoryList[] = substr($category, 0, $length);
		}
		return $this->_dbo->_select('
			SELECT CEIL(LENGTH(category)/3) AS depth, CONCAT("sort", CEIL(LENGTH(category)/3)) AS sort_field, sort_max, category, manual_sort_on_link_goods_position
			FROM '.GD_CATEGORY.'
			WHERE category IN("'.implode('", "', $categoryList).'") AND sort_type="MANUAL"
		');
	}

	/**
	 * ������ ī�װ��� �������� ���İ��� �ִ�ġ�� ��ȯ(�Լ����� ����Ǿ� ������ ��ġ�ڵ��� ���� Alias�޼��� ����)
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @return array
	 * @date 2013-06-03, 2013-06-03
	 */
	public function getManualSortMax($category)
	{
		return $this->getManualSortInfoHierarchy($category);
	}

	/**
	 * ������ ī�װ��� ���İ��� ������ ������ ��ŭ ������Ŵ
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param string $category
	 * @param int $increaseAmount
	 * @date 2013-06-03, 2013-06-03
	 */
	public function increaseSortMax($category, $increaseAmount = 1)
	{
		if (is_array($category)) {
			$this->_dbo->query('UPDATE '.GD_CATEGORY.' SET sort_max=sort_max+'.$increaseAmount.' WHERE category IN ("'.implode('", "', $category).'")');
		}
		else {
			$this->_dbo->query('UPDATE '.GD_CATEGORY.' SET sort_max=sort_max+'.$increaseAmount.' WHERE category="'.$category.'"');
		}
	}
}

?>