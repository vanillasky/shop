<?php
/**
 * Clib_Model_Goods_Abstract
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Model_Goods_Abstract extends Clib_Model_Abstract
{

	/**
	 * A00000xx ������ ��ǰ ID �� ����
	 * @return
	 */
	abstract function getReadableId();

	/**
	 * �������� ����
	 * @return string
	 */
	abstract function getReserve();

	/**
	 *
	 * @return
	 */
	abstract function getListImage();

	/**
	 *
	 * @return
	 */
	abstract function getPrice();

	/**
	 *
	 * @return
	 */
	abstract function getStatus();

	/**
	 *
	 * @return
	 */
	abstract function getIcons();

	/**
	 *
	 * @return
	 */
	abstract function getGoodsName();

	/**
	 *
	 * @param object $depth [optional]
	 * @return
	 */
	abstract function getCategory($depth = 0);

	/**
	 *
	 * @return
	 */
	abstract function getStockCode();

	/**
	 *
	 * @param object $stock
	 * @return
	 */
	abstract function addStock($stock);

	/**
	 *
	 * @param object $stock
	 * @return
	 */
	abstract function cutStock($stock);

}
