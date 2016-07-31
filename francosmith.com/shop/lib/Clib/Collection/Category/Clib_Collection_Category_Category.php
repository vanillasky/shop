<?php
/**
 * Clib_Collection_Category_Category
 * @author extacy @ godosoft development team.
 */
class Clib_Collection_Category_Category extends Clib_Collection_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'category';

	/**
	 *
	 * @param object $categoryId
	 * @return
	 */
	public function setCategoryFilter($categoryId)
	{
		$this->addFilter('category', $categoryId);
	}

	/**
	 * �ش� �÷��ǳ�, ���� ī�װ��� �ִ��� üũ�Ͽ� �� ��θ� �����Ͽ� ����
	 * @param Clib_Model_Category_Category $category
	 * @return string
	 */
	public function getCategoryRoute($category, $glue = ' > ')
	{

		$depth = $category->getDepth();

		if ($depth > 1) {

			$parents = array();
			$route = array();

			for ($i = 1; $i <= $depth - 1; $i++) {
				$id = substr($category->getId(), 0, $i * 3);

				if ( ! $parents[$_id]) {
					foreach ($this as $parent) {
						if ($parent->getId() == $id) {
							$parents[$id] = $parent->getData('catnm');
							break;
						}
					}
				}
				$route[] = $parents[$id];
			}

			$route[] = $category->getData('catnm');

			return implode($glue, $route);
		}
		else {
			return $category->getData('catnm');
		}
	}

}
