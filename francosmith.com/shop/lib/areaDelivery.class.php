<?php

/**
 * Copyright (c) 2014 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 * workingby		 2014.10.30        First Draft.
 */

/**
 * ������ ��ۺ�
 *
 * @author areaDelivery.class.php workingby
 * @date 2014-10-30
 * @version 1.0
 */
class areaDelivery
{
	private $param, $set;

	function __construct($param)
	{
		global $set, $param;

		$this->param	= $param;
		$this->set		= $set;
	}

	/**
	 * ������ ���� ��ۺ�
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getPay()
	{
		switch($this->set['delivery']['area_deli_type']){
			case '1' :
				//������ �����å - �����ȣ
				$_extra_fee = $this->getZipcodePay();
			break;

			case '2' :
				//������ �����å - ������/���θ�
				$_extra_fee = $this->getNewAreaDeliveryPay();
			break;

			case '0' : default :
				//������ �����å - ������
				$_extra_fee = $this->getAreaDeliveryPay();
			break;
		}

		return $_extra_fee;
	}

	/**
	 * ������ �����å - �����ȣ
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getZipcodePay()
	{
		$arr1 = explode('|',trim($this->set['delivery']['areaZip1']));
		$arr2 = explode('|',trim($this->set['delivery']['areaZip2']));
		$over = explode('|',trim($this->set['delivery']['overAddZip']));
		$zip  = str_replace('-','',$this->param['zipcode']);

		foreach($arr1 as $k => $v){
			if($v <= $zip && $zip <= $arr2[$k]){
				$_extra_fee += $over[$k];
			}
		}

		return $_extra_fee;
	}

	/**
	 * ������ �����å - ������
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getAreaDeliveryPay()
	{
		$arr = explode('|',trim($this->set['delivery']['overZipcode']));
		foreach($arr as $k => $v){
			$idx = count($rzip);
			$arr1 = array_unique(explode(',',$v));
			foreach($arr1 as $v2)if($v2)$rzip[$idx][] = $v2;
		}
		if(!$this->set['delivery']['overAdd']){
			$tmpAdd = explode("|",$this->set['delivery']['over']);
			foreach($tmpAdd as $v){
				$val = $v - $this->set['delivery']['default'];
				if($val >= 0) $overAdd[] = $val;
				else $overAdd[] = $v;
			}
			$over = $overAdd;
		}else{
			$over = explode("|",$this->set[delivery][overAdd]);
		}
		
		$head = substr($this->param['zipcode'],0,3);

		$this->param['zipcode'] = preg_replace('/[^0-9]/','',$this->param['zipcode']);
		$this->param['zipcode'] = sprintf('%s-%s', substr($this->param['zipcode'],0,3), substr($this->param['zipcode'],3,3));
		$tmp = Core::loader('Zipcode')->get( array('keyword' => $this->param['zipcode'], 'where' => 'zipcode' ) )->current();
		$tmpSido = $tmp['sido'];
		$tmpGugun = $tmp['gugun'];
		$tmpThisArea = $tmpSido." ".$tmpGugun;
		@include dirname(__FILE__) . '/../conf/area.delivery.php';
		$areaNameChecker = explode("|", $r_area['deliveryArea']);
		for ($i=0,$imax=count($areaNameChecker);$i<$imax;$i++) $areaNameChecker[$i] = explode(",",$areaNameChecker[$i]);
		for ($i=0;$i<count($rzip);$i++)if(in_array($head,$rzip[$i])&& in_array($tmpThisArea, $areaNameChecker[$i])){
			$key=$i;
			$_extra_fee += $over[$key];
		}

		return $_extra_fee;
	}

	/**
	 * ������ �����å - ������/���θ�
	 * @author workingby
	 * @date 2014-10-30
	 * @param
	 * @return int $_extra_fee
	 */
	public function getNewAreaDeliveryPay()
	{
		//���θ��ּ�
		if($this->param['road_address']){
			$newAreaResult	= array();
			$road_address	= trim($this->param['road_address'] . ' ' .  $this->param['address_sub']);
			$tmpRoad_address= $this->getAddressReprocess($road_address);
			$newAreaResult	= $this->getGdAreaDelivery($tmpRoad_address);
		}

		//�����ּ�
		if($this->param['address']){
			$address	= trim($this->param['address'] . ' ' .  $this->param['address_sub']);
			$tmpAddress	= $this->getAddressReprocess($address);

			// ���θ��ּҰ� ���ٸ� �����ּҷ� gd_area_delivery ����
			if(!$this->param['road_address']){
				$newAreaResult	= array();		
				$checkSido		= array('������'=>'����', '��⵵'=>'���', '��󳲵�'=>'�泲', '���ϵ�'=>'���', '���ֱ�����'=>'����', '�뱸������'=>'�뱸', '����������'=>'����', '�λ걤����'=>'�λ�', '����Ư����'=>'����', '����Ư����ġ��'=>'����', '��걤����'=>'���', '��õ������'=>'��õ', '���󳲵�'=>'����', '����ϵ�'=>'����', '����Ư����ġ��'=>'����', '��û����'=>'�泲', '��û�ϵ�'=>'���');

				//'�õ�'�� ���Ӹ��� Ǯ�������� ��ȯ
				list($tmpAddressSido) = @array_keys(@preg_grep('/' . $tmpAddress[0] . '/', $checkSido));
				$tmpAddress[0] = ($tmpAddressSido) ? $tmpAddressSido : $tmpAddress[0];
				$newAreaResult =  $this->getGdAreaDelivery($tmpAddress);
			}
		}

		$address_cnt			= (@count($tmpRoad_address) > @count($tmpAddress)) ? @count($tmpRoad_address) : @count($tmpAddress);
		$tmp_extra_fee_roadAddr	= false;
		$tmp_extra_fee_addr		= false;
		if(count($newAreaResult) > 0){
			for($i=$address_cnt; $i>0; $i--){
				if($i!=$address_cnt){
					if(@count($tmpRoad_address) >= @count($tmpAddress)) @array_pop($tmpRoad_address);
					if(@count($tmpRoad_address) <= @count($tmpAddress)) @array_pop($tmpAddress);
				}
				foreach($newAreaResult as $newArea){
					$newAreaName	= trim($newArea['areaSido']).trim($newArea['areaGugun']).str_replace(' ', '',$newArea['areaEtc']);

					//���θ��ּ� ��
					if($tmp_extra_fee_roadAddr === false && implode('', $tmpRoad_address) == $newAreaName){
						$tmp_extra_fee_roadAddr = $newArea['areaPay'];
						$roadAddr_length = @count(@explode(' ', $newArea['areaEtc']));
					}

					//�����ּ� ��
					if($tmp_extra_fee_addr === false && implode('', $tmpAddress) == $newAreaName){
						$tmp_extra_fee_addr = $newArea['areaPay'];
						$addr_length = @count(@explode(' ', $newArea['areaEtc']));
					}
					if($tmp_extra_fee_roadAddr !== false && $tmp_extra_fee_addr !== false) break;
				}

				//���θ��ּ�, �����ּ��� ������������ �� ���� �ּҿ��� ��ۺ� �ΰ�
				if($tmp_extra_fee_roadAddr !== false || $tmp_extra_fee_addr !== false){
					$_extra_fee = ($roadAddr_length >= $addr_length) ? (int)$tmp_extra_fee_roadAddr : (int)$tmp_extra_fee_addr;
					break;
				}
			}
		}

		return (int)$_extra_fee;
	}

	/**
	 * ������ ���� ����
	 * @author workingby
	 * @date 2014-10-30
	 * @param array $tmpRoad_address
	 * @return array $newAreaResult
	 */
	public function getGdAreaDelivery($tmpRoad_address)
	{
		global $db;

		if($tmpRoad_address[0] != '����Ư����ġ��') {
			$where = " and areaGugun = '" . $tmpRoad_address[1] . "'";
		}

		$newAreaResult = $db->_select("
			SELECT 
				areaSido, areaGugun, areaEtc, areaPay 
			FROM 
				" . GD_AREA_DELIVERY . " 
			WHERE 
				areaSido = '" . $tmpRoad_address[0] . "' $where and areaPay is not null and LENGTH(areaPay) > 0
			ORDER BY 
				LENGTH(areaEtc) DESC
		");

		return $newAreaResult;
	}

	/**
	 * �ּ� �迭����
	 * @author workingby
	 * @date 2014-10-30
	 * @param string $address
	 * @return array $tmpAddress
	 */
	public function getAddressReprocess($address)
	{
		$tmpAddress = @array_values(@array_filter(@array_map('trim', (array)@explode(' ', $address))));

		return $tmpAddress;
	}
}