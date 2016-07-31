<?php
class Clib_Form_Admin_Goods_Search extends Clib_Form_Abstract
{
	// dont't create construct & destruct.

	protected function initialize()
	{
		/*
		 * $this->input(columnName, attributes, $validationOption);
		 *
		 * $validationOption like this :
		 *
		 * array(
		 *       'required' => Clib_Validation::REQUIRED_YES,
		 *       'type' => Clib_Validation::TYPE_STRING,
		 *       'length' => 9,
		 *       'length_type' => Clib_Validation::TYPE_LENGTH_EQUAL
		 * );
		 */

		$this->input('sword');
		$this->select('skey', array('value' => array(
				'��ǰ��' => 'goods.goodsnm',
				'��ǰ��ȣ' => 'goods.goodsno',
				'���������ڵ�' => 'goods.goodscd',
				'������' => 'goods.maker',
				'����˻���' => 'goods.keyword',
				'�̺�Ʈ����' => 'goods.naver_event',
			)));

		$brandnoValue = Clib_Application::getCollectionClass('goods_brand')->setOrder('sort')->load();
		$brandnoValue->unshiftItem(Clib_Application::getModelClass('goods_brand')->setData(array('brandnm' => '- �귣�� ���� -', )));
		$this->select('brandno', array('value' => $brandnoValue));

		$this->radio('open', array(
			'default_value' => '',
			'value' => array(
				'��ü' => '',
				'������ǰ' => '1',
				'��������ǰ' => '0'
			)
		));

		$this->radio('soldout', array(
			'default_value' => '',
			'value' => array(
				'��ü' => '',
				'ǰ��' => '1',
				'ǰ����ǰ����' => '0'
			)
		));

		$this->radio('discount', array(
			'default_value' => '',
			'value' => array(
				'��ü' => '',
				'���λ�ǰ' => '1',
				'���λ�ǰ����' => '2'
			)
		));

		$this->input('origin');

		$this->radio('stock_type', array(
			'default_value' => 'product',
			'value' => array(
				'��ǰ���(ǰ����� ��)' => 'product',
				'ǰ�����' => 'item',
			)
		));

		$this->input('stock', array('style'=>'width:50px'));
	}

}
