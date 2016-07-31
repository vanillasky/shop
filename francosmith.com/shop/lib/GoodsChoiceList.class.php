<?php
/**
 * Copyright (c) 2015 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author           Date             Description
 * ---------------   --------------    ------------------
 * bumyul2000       2015.06.25       First Draft.
 * bumyul2000       2015.07.30       add function.
 * bumyul2000       2015.08.27       adjust max limit.
 */

/**
 * ��ǰ���� LIST
 *
 * @author GoodsChoiceList.class.php,  bumyul2000, bumyul2000@godo.co.kr
 * @version 1.0
 * @date 2015-06-25, 2015-07-30, 2015-08-27
 *
*/
class GoodsChoiceList {

	private $goodsChoiceCfg, $postData, $maxLimit;

	/**
	* ������
	*
	* @author bumyul2000@godo.co.kr
	* @param void
	* @return void
	* @date 2015-06-25, 2015-08-27
	*
	*/
	public function __construct()
	{
		global $_POST;

		@include dirname(__FILE__) . '/../conf/config.goodsChoice.php';

		//��ǰ���� ȯ�漳��
		$this->goodsChoiceCfg = $goodsChoiceCfg;
		//requeset parameter
		$this->postData = $_POST;
		$this->maxLimit = ((int)$_POST['maxLimit'] > 0) ? $_POST['maxLimit'] : $this->goodsChoiceCfg['defaultLimit'];
		$this->postData['goodsArr'] = array_filter(@explode(",", $_POST['goodsArr']));
		$this->postData['registeredGoodsno'] = array_filter(@explode(",", $_POST['registeredGoodsno']));
	}

	/**
	* ��ϵ� ��ǰ goodsno ����
	*
	* @author bumyul2000@godo.co.kr
	* @param void
	* @return array [goodsArr - ��ǰgoodsno], [msg - �����ų �޽��� ����]
	* @date 2015-06-25, 2015-08-27
	*
	*/
	private function getGoodsArrAdd()
	{
		$msg = '';
		//������ ��ǰ
		$goodsArr = array();
		//���� ��ǰ ����
		$registeredGoodsnoCount = count($this->postData['registeredGoodsno']);
		//����� ��ǰ�� ����
		$goodsChoiceCount = count(array_filter($this->postData['goodsArr']));

		$goodsArr = array_diff($this->postData['goodsArr'], (array)$this->postData['registeredGoodsno']);
		if(count($goodsArr) < 1 && $registeredGoodsnoCount > 0){
			$this->responseResult('ERROR', '', '�̹� ��ϵ� ��ǰ�Դϴ�.');
		}
		if($goodsChoiceCount != count($goodsArr)){
			$msg = '�̹� ��ϵ� ��ǰ�� �����ϰ� �߰��˴ϴ�.';
		}

		$goodsArr = array_values($goodsArr);
		$goodsArr = array_slice($goodsArr, 0, (int)$this->maxLimit);

		return array($goodsArr, $msg);
	}

	/**
	* html ����
	*
	* @author bumyul2000@godo.co.kr
	* @param void
	* @return void
	* @date 2015-06-25
	*
	*/
	public function getRegisteredListHtml()
	{
		$msg = '';
		$registeredListHtml = '';
		switch($this->postData['mode']){
			case 'setting' :
				$registeredListHtml .= $this->getOutlineHtml('header');
				$registeredListHtml .= $this->getListHtml($this->postData['goodsArr']);
				$registeredListHtml .= $this->getOutlineHtml('footer');
			break;

			case 'add' : case 'dbclick_add' :
				$goodsArr = array();
				list($goodsArr, $msg) = $this->getGoodsArrAdd();
				$registeredListHtml .= $this->getListHtml($goodsArr);
			break;

			case 'addAll' :
				$goodsArr = array();
				list($goodsArr, $msg) = $this->getGoodsArrAsFromQuery($this->postData['queryString']);
				$registeredListHtml .= $this->getListHtml($goodsArr);
			break;

			case 'confirm' :
				$registeredListHtml .= $this->getConfirmHtml($this->postData['goodsArr'], $this->postData['eHiddenName']);
			break;
		}

		$this->responseResult('OK', $registeredListHtml, $msg);
	}

	/**
	* response
	*
	* @author bumyul2000@godo.co.kr
	* @param void
	* @return array [code - OK or ERROR], [data - ���� html], [msg - ���â �޽��� ����]
	* @date 2015-06-25
	*
	*/
	private function responseResult($code, $result='', $msg='')
	{
		$responseResult = array();
		$responseResult['code'] = $code;
		$responseResult['data'] = $result;
		$responseResult['msg'] = $msg;

		echo gd_json_encode($responseResult);
		exit;
	}

	/**
	* �ʱ� ���̾ƿ� html
	*
	* @author bumyul2000@godo.co.kr
	* @param string  [type - header, footer]
	* @return string
	* @date 2015-06-25
	*
	*/
	private function getOutlineHtml($type)
	{
		$outlineHtml = '';
		if($type == 'header'){
			$outlineHtml = '
				<table cellpadding="0" cellpadding="0" width="100%" id="goodsRegisteredTableArea" class="goodChoice_registaredTable">
				<colgroup>
					<col style="width: 30px;" />
					<col style="width: 70px;" />
					<col style="width: 50px;" />
					<col />
					<col style="width: 70px;" />
					<col style="width: 55px;" />
					<col style="width: 55px;" />
					<col style="width: 55px;" />
				</colgroup>
				<tr id="goodsRegisteredTrArea">
					<th><a href="javascript:void(0)" onclick="javascript:chkBox(document.getElementsByName(\'goodsno[]\'),\'rev\');goodsChoiceFunc.setRowHighlight(\'registered\');" class="white">����</a></th>
					<th>��������</th>
					<th>�̹���</th>
					<th>��ǰ��</th>
					<th>�ǸŰ���</th>
					<th>�Ǹ����</th>
					<th>��������</th>
					<th>ǰ������</th>
				</tr>
			';
		}
		else if($type == 'footer'){
			$outlineHtml = '
				</table>
			';
		}

		return $outlineHtml;
	}

	/**
	* ��ϵǾ��ִ� ī��Ʈ ������ ������� �ε��� ����
	*
	* @author bumyul2000@godo.co.kr
	* @param void
	* @return inteager
	* @date 2015-06-25
	*
	*/
	private function getRegisterdGoodsStartIdx()
	{
		if($this->postData['mode'] == 'setting'){
			$idx = 0;
		}
		else {
			$idx = count($this->postData['registeredGoodsno']);
		}

		return $idx;
	}

	/**
	* ������ ��ǰ row html ����
	*
	* @author bumyul2000@godo.co.kr
	* @param array [goodsnoArr - goodsno �迭]
	* @return string
	* @date 2015-06-25
	*
	*/
	private function getListHtml($goodsnoArr)
	{
		$listHtml = '';
		$goods = Clib_Application::getModelClass('goods');

		$idx = $this->getRegisterdGoodsStartIdx();
		foreach($goodsnoArr as $arr){
			$goodsno = 0;
			$goodsno = $arr;

			if($goodsno < 1){
				continue;
			}

			$goods->load($goodsno);

			$soldout = '';
			if ($goods->getSoldout()) $soldout = 'ǰ��';

			$open = '����';
			if($goods->getData('open') == '0') $open = '������';

			$idx++;

			$listHtml .= '
				<tr id="registerdGoodsTr_'.$goods->getId().'" onclick="javascript:goodsChoiceFunc.setCheckboxCheck(this, \'registered\', event);goodsChoiceFunc.shiftSelection(this, \'registered\', event);" ondblclick="javascript:goodsChoiceFunc.removeRegisteredGoodsRow(this);" class="hand registeredContentRow">
					<td><input type="checkbox" name="goodsno[]" value="'.$goods->getId().'" onclick="javascript:goodsChoiceFunc.setRowHighlight(\'registered\');" /></td>
					<td>'.$idx.'</td>
					<td><a href="../../goods/goods_view.php?goodsno='.$goods->getId().'" target="_blank">'.goodsimg($goods[img_s], '40,40', 'style="vertical-align:middle;border:1px solid #e9e9e9;"', 1).'</a></td>
					<td class="goodsName">'.strcut(strip_tags($goods->getGoodsName()), 40).'</td>
					<td>'.number_format($goods->getPrice()).'</td>
					<td>'.number_format($goods->getStock()).'</td>
					<td>'.$open.'</td>
					<td>'.$soldout.'</td>
				</tr>
			';
			$goods->resetData();
		}

		return $listHtml;
	}

	/**
	* ��ǰ����� ���ϵ� input, �̹���
	*
	* @author bumyul2000@godo.co.kr
	* @param array, string [goodsnoArr - goodsno �迭], [eHiddenName - ����� hidden input name]
	* @return string
	* @date 2015-06-25
	*
	*/
	private function getConfirmHtml($goodsnoArr, $eHiddenName)
	{
		$confirmHtml = '';
		$goods = Clib_Application::getModelClass('goods');

		foreach($goodsnoArr as $arr){
			$goods->load($arr);

			$confirmHtml .= '
				<input type="hidden" name="'.$eHiddenName.'" value="'.$arr.'">
				<div style="z-index:100; float:left; width:40px; height:40px; border:1 solid #cccccc;margin:1px;" title="'.$goods->getGoodsName().'"><a href="../../goods/goods_view.php?goodsno='.$goods->getId().'" target="_blank">'.goodsimg($goods[img_s], '40,40', 'style="vertical-align:middle;"', 1).'</a></div>
			';
			$goods->resetData();
		}

		return $confirmHtml;
	}

	/**
	* �������� layout
	*
	* @author bumyul2000@godo.co.kr
	* @param void
	* @return string
	* @date 2015-06-25
	*
	*/
	public function getOutlineSortHtml()
	{
		$outlineSortHtml = '
			<table cellpadding="0" cellspacing="0" width="100%" height="30">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="150">
							<img src="../img/btn_downArrowMore.gif" class="goodsChoice_downArrowMore goodsChoice_arrow goodsChoice_tooltip" alt="�� �Ʒ�" tooltipContent="�� �Ʒ�" />
							<img src="../img/btn_downArrow.gif" class="goodsChoice_downArrow goodsChoice_arrow goodsChoice_tooltip" alt="�Ѵܰ� �Ʒ�" tooltipContent="�Ѵܰ� �Ʒ�" />
							<img src="../img/btn_upArrow.gif" class="goodsChoice_upArrow goodsChoice_arrow goodsChoice_tooltip" alt="�Ѵܰ� ��" tooltipContent="�Ѵܰ� ��" />
							<img src="../img/btn_upArrowMore.gif" class="goodsChoice_upArrowMore goodsChoice_arrow goodsChoice_tooltip" alt="�� ��" tooltipContent="�� ��" />
						</td>
						<td width="180">������ ��ǰ�� <input type="text" name="goodsChoice_sortText" class="goodsChoice_sortText" /> �� ��ġ��</td>
						<td width="30"><img src="../img/btn_move.gif" class="goodsChoice_moveBtn" alt="�̵�" /></td>
						<td>&nbsp;</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		';

		return $outlineSortHtml;
	}

	/**
	* ��ǰ��ü��� query ����
	*
	* @author bumyul2000@godo.co.kr
	* @param string
	* @return array
	* @date 2015-06-25, 2015-08-27
	*
	*/
	private function getGoodsArrAsFromQuery($query)
	{
		$params = array();
		$goodsArr = array();
		$msg = '';
		$overlap = false;

		parse_str($query, $params);
		$params['page'] = 1;
		$params['page_num'] = (int)$this->maxLimit + 1;

		$goodsHelper = Clib_Application::getHelperClass('admin_goods');
		$goodsList = $goodsHelper->getGoodsCollection($params);

		$i = 0;
		foreach($goodsList as $goods){
			if($i >= (int)$this->maxLimit){
				break;
			}
			if(!in_array($goods['goodsno'], $this->postData['registeredGoodsno'])){
				$goodsArr[] = $goods['goodsno'];
				$i++;
			}
			else {
				$overlap = true;
			}
		}

		if($overlap == true){
			$msg = '�̹� ��ϵ� ��ǰ�� �����ϰ� �߰��˴ϴ�.';
		}

		return array($goodsArr, $msg);
	}

	/**
	* �ִ� ��ǰ ��� ����
	*
	* @author bumyul2000@godo.co.kr
	* @param string
	* @return int
	* @date 2015-08-27
	*
	*/
	public function getLimit($fileName)
	{
		$maxLimit = 0;
		if(array_key_exists($fileName, $this->goodsChoiceCfg['etcLimit'])){
			$maxLimit = $this->goodsChoiceCfg['etcLimit'][$fileName];
		}
		else {
			$maxLimit = $this->goodsChoiceCfg['defaultLimit'];
		}

		return (int)$maxLimit;
	}
}
?>