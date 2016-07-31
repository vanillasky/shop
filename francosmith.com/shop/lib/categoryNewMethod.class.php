<?php
/**
 * ��ǰ�з� ������ Class
 */
class categoryNewMethod
{
	private		$_db;
	private		$_limitCount		= 200;			// �ѹ��� �ҷ��� ��������
	private		$_categoryLength	= 3;			// ī�װ� ���� ����
	private		$_categoryDepth		= 4;			// ī�װ� ����

	/**
	 * ������
	 */
	public function __construct()
	{
		// ��� ����
		if (!is_object($this->_db)) {
			$this->_db 	= Core::loader('db');
		}
	}

	/**
	 * [�ӽ�] link_chk �ʵ��� ���θ� üũ �� link_chk �ʵ� �߰�
	 */
	public function setLinkCheckFieldAdd()
	{
		// link_chk �ʵ��� ���θ� üũ �� link_chk �ʵ� �߰�
		$strSQL		= "DESC ".GD_GOODS_LINK;
		$result		= $this->_db->query($strSQL);
		$fieldChk	= false;
		while (is_resource($result) && $data = $this->_db->fetch($result,1))
		{
			if ($data['Field'] == 'link_chk') {
				$fieldChk	= true;
			}
		}
		if ($fieldChk === false) {
			$strSQL	= "ALTER TABLE ".GD_GOODS_LINK." ADD link_chk ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n'";
			$this->_db->query($strSQL);
		}
	}

	/**
	 * ��ǰ�з� ������ ��ȯ ó��
	 * @param1  string $page �ʱ������� �缳��
	 * @param2  string $completCnt �Ϸ�� �������� ���� �缳��
	 * @param3  string $totnum �� ��ǰ ����
	 * @return  array $result query
	 */
	public function setCategoryLinkAdd($page = 1, $completCnt = 0, $totnum)
	{

		if(!$page) $page = 1;				// �ʱ������� �缳��
		if(!$completCnt) $completCnt = 0;	// �Ϸ�� �������� ���� �缳��

		if ($totnum == 0) {
			$getData['limitStart']	= 0;
			$getData['dataCnt']		= 0;
			$getData['completCnt']	= 0;

			return $getData;
		}

		$limitStart	= ($page - 1) * $this->_limitCount;		// ���۹�ȣ
		$totpage	= ceil($totnum / $this->_limitCount);
		$strSQL		= "SELECT goodsno FROM ".GD_GOODS." ORDER BY goodsno ASC LIMIT ".$limitStart.", ".$this->_limitCount;
		$result		= $this->_db->query($strSQL);
		$updateArr	= array();	// ������Ʈ �� ī�װ�
		$excludeArr	= array();	// �ߺ� ���� �� ī�װ�
		$field		= array('sort', 'sort1', 'sort2', 'sort3', 'sort4', 'hidden', 'hidden_mobile');	// gd_goods_link �ʵ� �� (����4��)
		$dataCnt	= 0;
		$goodsnoArr	= array();

		while (is_resource($result) && $data = $this->_db->fetch($result,1))
		{
			$goodsnoArr[]	= $data['goodsno'];
			$dataCnt++;
		}

		if (count($goodsnoArr) > 0 ){

			$whereStr	= "'".implode("','", $goodsnoArr)."'";

			// �ش� goodsno �� ī�װ� ����
			$strSQL		= "SELECT goodsno, category, ".implode(', ', $field)." FROM ".GD_GOODS_LINK." WHERE goodsno IN (".$whereStr.") ORDER BY goodsno ASC, sno ASC";
			$result2	= $this->_db->query($strSQL);

			while (is_resource($result2) && $getCode = $this->_db->fetch($result2,1))
			{
				$length	= strlen($getCode['category']);		// ī�װ� �ڵ��� ����

				// �ߺ� ������ ī�װ�
				$excludeArr[$getCode['goodsno']][] = $getCode['category'];

				// ī�װ� ������ ���� ����
				for ($i = 1; $i <= ($length / $this->_categoryLength); $i++)
				{
					$getCate	= substr($getCode['category'], 0, ($i * $this->_categoryLength));			// ������ ���� �ش� ī�װ� �ڵ�

					// ���� ī�װ� �ڵ�� ������ ���� �ش� ī�װ� �ڵ带 �񱳸� �ؼ� ���� ������ ����ī�װ� ����
					if ($getCate != $getCode['category']) {
						$updateArr[$getCode['goodsno']][$getCate]['category']	= $getCate;			// tmp �迭�� ��ǰ��ȣ -> ī�װ� -> category ���� ����

						foreach ($field as $key => $val) {
							$updateArr[$getCode['goodsno']][$getCate][$val]	= $getCode[$val];	// tmp �迭�� ��ǰ��ȣ -> ī�װ� -> �� ���� ����
						}
					}
				}
			}
		}

		// ī�װ� ���� insert
		foreach ($updateArr as $goodsno => $val)
		{
			foreach ($val as $cKey => $cVal)
			{
				// �ߺ� ī�װ��� ����
				if (in_array($cVal['category'], $excludeArr[$goodsno])) {
					continue;
				} else {
					$querycnt = "SELECT count(1) FROM ".GD_GOODS_LINK."  WHERE goodsno='".$goodsno."' AND category='".$cVal['category']."'";
					list($chkCate) = $this->_db->fetch($querycnt);
					if ($chkCate == 0) {
						$strSQL	= "INSERT INTO ".GD_GOODS_LINK." (goodsno, link_chk, category, ".implode(', ', $field).") VALUES ('".$goodsno."','y','".implode('\', \'', $cVal)."')";
						$this->_db->query($strSQL);
						$completCnt++;
					}
				}
			}
		}

		$getData['limitStart']	= $limitStart;
		$getData['dataCnt']		= $dataCnt;
		$getData['completCnt']	= $completCnt;

		return $getData;
	}

	/**
	 * �ߺ� ī�װ��� ������
	 * @return  intger $removeCnt ���� ����
	 */
	public function duplicationRemove()
	{
		// �ߺ� ī�װ��� üũ��
		//SELECT sno, count( goodsno ) , goodsno , category FROM ".GD_GOODS_LINK." GROUP BY goodsno , category HAVING count( goodsno ) >1
		$strSQL	= " SELECT c.sno FROM ".GD_GOODS_LINK." c ,
			(SELECT goodsno, category, min(sno) as m_sno FROM ".GD_GOODS_LINK." GROUP BY goodsno, category ) X
			WHERE c.goodsno = X.goodsno AND c.category = X.category AND c.sno > m_sno";
		$result		= $this->_db->query($strSQL);
		$removeCnt	= $this->_db->count_($result);

		// ó�� ������ ����
		if ($removeCnt > 0) {
			// ���� ó����
			while (is_resource($result) && $data = $this->_db->fetch($result,1))
			{
				$strSQL	= "DELETE FROM ".GD_GOODS_LINK." WHERE sno = '".$data['sno']."'";
				$this->_db->query($strSQL);
			}
		}

		return $removeCnt;
	}

	/**
	 * �ش� ī�װ��� �ش�Ǵ� ��ǰ�� ��ũ���� ������
	 * @param1  string $goodsno ��ǰ��ȣ
	 * @param2  string $category ī�װ��ڵ�
	 * @return  array $result ī�װ��ڵ�
	 */
	private function _getCategory($goodsno, $category)
	{
		// üũ
		if (empty($goodsno) || empty($category)) {
			return false;
		}

		// �ش� ī�װ��� �ش� �Ǵ� ��ǰ�� ��ũ���� ������
		$getData	= array();
		$getResult	= $this->_db->query('SELECT category FROM '.GD_GOODS_LINK.' WHERE category LIKE "'.substr($category, 0, 3).'%" AND goodsno='.$goodsno);
		while ($getLink	= $this->_db->fetch($getResult, true)) {
			$getData[]	= $getLink['category'];
		}

		return $getData;
	}

	/**
	 * �ΰ��� ī�װ� �迭���� �ߺ����� ���� ī�װ��� ����
	 * @param1  array $category ī�װ�1
	 * @param2  array $category ī�װ�2
	 * @return  array $result �ߺ����� ���� ī�װ�
	 */
	private function _getSingleCategory($category1, $category2)
	{
		// üũ
		if (empty($category1) || empty($category2)) {
			return false;
		}

		// ����� ī�װ��� �������� �ߺ����� ���� ī�װ��� ����
		$getData	= array();
		foreach ($category1 as $val) {
			if (in_array($val, $category2) === false) {
				$getData[]	= $val;
			}
		}

		return $getData;
	}

	/**
	 * �ش� ��ǰ�� �ش� ī�װ� ��ũ���� ���� (adm_goods_manage_link.php ������ link�� ���)
	 * @param1  string $goodsno ��ǰ��ȣ
	 * @param2  string $category ī�װ��ڵ�
	 * @return  array $result �ش� ��ǰ�� �߰��� ī�װ� �ڵ�
	 */
	public function getHighCategoryLink($goodsno, $category)
	{
		// üũ
		if (empty($goodsno) || empty($category)) {
			return false;
		}

		// ��ǰ�� ī�װ��� ������
		$goodsCategory	= $this->_getCategory($goodsno, $category);

		// �ش� ī�װ��� ���� ī�װ� �ڵ尪�� ���� ���� ī�װ� �ڵ� ����
		$getData		= array();
		foreach (getHighCategoryCode($category) as $val) {
			if(in_array($val, $goodsCategory) === false){
				$getData[]	= $val;
			}
		}

		return $getData;
	}

	/**
	 * �ش� ��ǰ�� ������ ī�װ� ��ũ���� ���� (adm_goods_manage_link.php ������ unlink�� ���)
	 * @param1  string $goodsno ��ǰ��ȣ
	 * @param2  string $category ī�װ��ڵ�
	 * @return  array $result �ش� ��ǰ�� ������ ī�װ� �ڵ�
	 */
	public function getHighCategoryUnlink($goodsno, $category)
	{
		// üũ
		if (empty($goodsno) || empty($category)) {
			return false;
		}

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		if (_CATEGORY_NEW_METHOD_ === false) {
			$getData[]	= $category;

			return $getData;
			exit();
		}

		// ��ǰ�� ī�װ��� ������
		$goodsCategory		= $this->_getCategory($goodsno, $category);

		// ī�װ��� ����
		$categoryLength		= strlen($category);

		// ī�װ��� ����
		$checkCategoryNum	= $categoryLength / $this->_categoryLength;

		// ���ŵ� ���� ī�װ��� �迭�� ����
		$tmpData			= array();
		for ($i = 1; $i <= $checkCategoryNum; $i++) {
			$tmp			= substr($category, 0, ($i * $this->_categoryLength));
			if ($tmp != $category) {
				$tmpData[]	= $tmp;
			}
		}

		// ����� ī�װ��� �������� ���ŵ� ���� ī�װ��� �迭�� ����
		foreach ($goodsCategory as $val) {
			if($category == substr($val, 0, $categoryLength)){
				$tmpData[]	= $val;
			}
		}

		// ����� ī�װ��� �������� �ߺ����� ���� ī�װ��� ����
		$singleData	= $this->_getSingleCategory($goodsCategory, $tmpData);

		// �ߺ����� �ʴ� ī�װ��� ���� ���
		if (empty($singleData) === true) {
			$getData	= $tmpData;
		}

		// �ߺ����� �ʴ� ī�װ��� �ִ� ���
		else {
			// ���ŵ� ī�װ��� ���� ī�װ��� ���� ī�װ� ��뿩�ο� ���� ���ŵ� ī�װ��� ����
			$duplicationData	= array();
			foreach ($singleData as $val) {
				$checkValNum	= strlen($val) / $this->_categoryLength;
				for ($i = 1; $i <= $checkValNum; $i++) {
					$checkVal	= substr($val, 0, ($i * $this->_categoryLength));
					if (in_array($checkVal, $tmpData)) {
						$duplicationData[]	= $checkVal;
						$duplicationData	= array_unique($duplicationData);
					}
				}
			}

			// ����� ī�װ��� �������� ���ŵ� ���� ī�װ��� �迭�� ����
			$getData	= $this->_getSingleCategory($tmpData, $duplicationData);
		}

		return $getData;
	}
}
?>