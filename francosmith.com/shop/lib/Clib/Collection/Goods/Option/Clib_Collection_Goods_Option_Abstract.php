<?php
class Clib_Collection_Goods_Option_Abstract extends Clib_Collection_Abstract
{

	/**
	 * {@inheritdoc}
	 */
	protected $valueModel = 'goods_option';

	protected function construct()
	{
		$this->addFilter('goods_option.go_is_deleted', '1', '<>');
		// go_sort ������ ������ �ϰ� �Ǹ�,
		// ������ ������ �ɼ� �̹��� ������ ���� ���� ���� �����Ƿ�,
		// front ������ ������ �ɼ� ������ �����ؾ� �Ѵ�.
		//$this->setOrder('go_sort asc');
		$this->setOrder('sno asc');
	}

	/**
	 * ���� ���� nth ° �ɼǸ��� �迭�� �����Ѵ�.
	 * @param object $nth
	 * @return
	 */
	public function getNthNames($nth)// 1~
	{
		$names = array();

		foreach ($this as $option) {
			if (($name = $option->getNthName($nth)) !== '') {
				if ( ! in_array($name, $names)) {
					$names[] = $name;
				}
			}
		}

		return $names;
	}

	/**
	 * ���� ���� nth ° �ɼǸ��� �̹���(or �÷��ڵ�)�� �����Ѵ�.
	 * @param object $nth
	 * @return
	 */
	public function getNthIcons($nth)// 1~
	{
		$names = array();

		foreach ($this as $option) {
			if (($name = $option->getNthIcon($nth)) !== '') {
				if ( ! in_array($name, $names)) {
					$names[$option->getNthName($nth)] = $name;
				}
			}
		}

		return $names;
	}

	/**
	 * 1�� �ɼ��� �̹����� �����´�.
	 * �̹����� ���� ��� �������� ä������.
	 * @return array
	 */
	public function getImages()
	{
		$names = array();

		foreach ($this as $option) {
			if (($name = $option->getImage()) !== '') {
				if ( ! in_array($name, $names)) {
					$names[$option->getNthName(1)] = $name;
				}
			}
		}

		return $names;

	}

	public function getEmptyStockCount()
	{
		$cnt = 0;
		foreach ($this as $option) {
			if ((int)$option->getStock() === 0) {
				$cnt++;
			}
		}
		return $cnt;
	}

	public function sort()
	{
		$sortedItems = $this->getIterator();

		$sortkey = 0;
		foreach($sortedItems as $item) {
			$sortkey++;
			$item[go_sort] = $item[go_sort] ? $item[go_sort] : $sortkey;
		}
		$sortedItems->uasort(array($this, 'compareSort'));

		$this->resetItems();
		foreach($sortedItems as $item) {
			$this->addItem($item);
		}
		return $this;
	}

	public function compareSort($a, $b)
	{
		if ( $a['go_sort'] == $b['go_sort'] )
			return 0;
		else if ( $a['go_sort'] < $b['go_sort'] )
			return -1;
        else
			return 1;
	}

	public function removeInvisible()
	{

		foreach($this as $k => $item) {

			if (!$item['go_is_display']) {
				unset($sortedItems[$k]);
			}
		}

		return $this;
	}

}
