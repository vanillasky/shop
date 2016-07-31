<?
final class category {

	/**
	 * 1 depth �� ��� ������ �ڸ���
	 */
	const DEPTH_PER = 3;

	/**
	 * �ִ� depth
	 */
	const DEPTH_MAX = 4;

	/**
	 * �Է� ī�װ��� n��° ī�װ��� ����
	 * @param string $category
	 * @param integer $depth [optional]
	 * @return string
	 */
	public static function div($category, $depth=1) {

		return substr($category, 0, self::DEPTH_PER * $depth);

	}

	/**
	 * �Է�ī�װ��� depth �� ����
	 * @param string $category [optional]
	 * @return
	 */
	public static function getDepth($category = '') {

		if (($len = strlen($category)) >= 3)
			return ceil($len / self::DEPTH_PER);
		else
			return 0;

	}

	/**
	 * �Է� ī�װ��� ���� ī�װ��� ����
	 * @param string $category
	 * @return
	 */
	public static function getSubCategory($category) {

		$db = Core::loader('GODO_DB');

		$depth = self::getDepth($category) + 1;

		$builder = $db->builder()->select();
		$builder
			->from(GD_CATEGORY, $db->expression('MAX(category)'))
			->where("category like ?", $db->wildcard($category,1))
			->where("depth = ?", $depth)
		;

		list($max) = $builder->fetch();
		if (!$max) $max = $category.str_repeat('0', self::DEPTH_PER);

		$_offset = ($depth - 1) * self::DEPTH_PER;

		$_parent = substr($max, 0, $_offset);
		$_child  = substr($max, $_offset,self::DEPTH_PER);

		//$_child_max = pow(36, self::DEPTH_PER) - 1;	// 36 ����
		$_child_max = pow(10, self::DEPTH_PER) - 1;	// 10 ����

		if (++$_child >= $_child_max)
			return false;	// ���̻� ���� ī�װ��� ������ �� ����
		else
			return $_parent.sprintf('%0'.self::DEPTH_PER.'s', $_child);

	}

}
?>