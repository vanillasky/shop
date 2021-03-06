<?
class admin_order_list_excel_download extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder
		->from(		array('ORD' => GD_ORDER)										,null)
		->join(		array('ITM' => GD_ORDER_ITEM)		,'ORD.ordno = ITM.ordno'	,null)
		->leftjoin(	array('MB' => GD_MEMBER)			,'ORD.m_no = MB.m_no',		null);

		// where

		// 접수유형
		if($param['sugi']) {
			if($param['sugi'] == "Y") $builder->where("ORD.inflow = ?", 'sugi');
			elseif($param['sugi'] == "N") {
				$builder->where("ORD.inflow != ? OR ORD.inflow IS null", 'sugi');
			}
		}

		if($param['regdt_start']) {
			if(!$param['regdt_end']) $param['regdt_end'] = date('Ymd', G_CONST_NOW);

			if ($param['regdt_time_start'] !== -1 && $param['regdt_time_end'] !== -1) {
				$param['regdt_start'] .= sprintf('%02d',$param['regdt_time_start']);
				$param['regdt_end'] .= sprintf('%02d',$param['regdt_time_end']);
			}

			$tmp_start = Core::helper('Date')->min($param['regdt_start']);
			$tmp_end = Core::helper('Date')->max($param['regdt_end']);

			switch($param['dtkind']) {
				case 'orddt': $builder->where("ORD.orddt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
				case 'cdt': $builder->where("ORD.cdt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
				case 'ddt': $builder->where("ORD.ddt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
				case 'confirmdt': $builder->where("ORD.confirmdt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
			}

		}

		if($param['settlekind']) {
			$builder->where("ORD.settlekind = ?", $param['settlekind']);
		}

		if(count($param['step']) || count($param['step2'])) {

			$subWhere = array();
			$co_subWhere = array();

			if(count($param['step'])) {
				$subWhere[] = $builder->parse("ORD.step IN (?) AND ORD.step2 = ?", array($param['step'],0));
			}

			if(count($param['step2'])) {
				foreach($param['step2'] as $v) {
					switch($v) {
						case 1:
							$subWhere[] = $builder->parse('ORD.step = 0 AND ORD.step2 BETWEEN ? AND ?', array(1,49));
						break;

						case 2:
							$subWhere[] = $builder->parse('(ORD.step IN (?) AND ORD.step2 != ?) OR (ORD.cyn = ? AND ORD.step2 = ? AND ORD.dyn != ?)', array(array(1,2), 0, 'r', 44, 'e'));
						break;

						case 3:
							$subWhere[] = $builder->parse('ORD.step IN (?) AND ORD.step2 != ?', array(array(3,4),0));
						break;

						case 60 :
							$subWhere[] = $builder->parse('ITM.dyn = ? AND ITM.cyn = ?', array('e','e'));
							$isOrderItemSearch=true;
						break; //교환완료

						case 61 :
							$subWhere[] = $builder->parse('ORD.oldordno > 0');
						break; //재주문

						default :
							$subWhere[] = $builder->parse('ORD.step2 = ?',$v);
					}
				}

			}

			if(count($subWhere)) {
				$builder->where( '('.implode(' OR ',$subWhere).')' );
			}

		}

		if($param['sword'] && $param['skey']) {

			switch($param['skey']) {
				case 'all':
					$subWhere = array();
					$subWhere[] = $builder->parse('ORD.ordno = ?', $param['sword']);
					$subWhere[] = $builder->parse('ORD.nameOrder like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $builder->parse('ORD.nameReceiver like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $builder->parse('ORD.bankSender like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $builder->parse('MB.m_id = ?', $param['sword']);

					$builder->where( '('.implode(' OR ',$subWhere).')' );
					break;
				case 'ordno': $builder->where('ORD.ordno = ?', $param['sword']); break;
				case 'nameOrder': $builder->where('ORD.nameOrder like ?', $this->db->wildcard($param['sword'])); break;
				case 'nameReceiver': $builder->where('ORD.nameReceiver like ?', $this->db->wildcard($param['sword'])); break;
				case 'bankSender': $builder->where('ORD.bankSender like ?', $this->db->wildcard($param['sword'])); break;
				case 'm_id': $builder->where('MB.m_id = ?', $param['sword']); break;
			}

		}

		if($param['sgword'] && $param['sgkey']) {
			$isOrderItemSearch=true;

			switch($param['sgkey']) {
				case 'goodsnm': $builder->where('ITM.goodsnm like ?', $this->db->wildcard($param['sgword'])); break;
				case 'brandnm': $builder->where('ITM.brandnm like ?', $this->db->wildcard($param['sgword'])); break;
				case 'maker': $builder->where('ITM.maker like ?', $this->db->wildcard($param['sgword'])); break;
			}

		}

		if(count($param['chk_inflow'])) {
			$es_inflow = array();
			foreach($param['chk_inflow'] as $v) {
				if($v == 'naver_price') {
					$es_inflow[] = 'naver_elec';
					$es_inflow[] = 'naver_bea';
					$es_inflow[] = 'naver_milk';
				}
				else if($v == 'plus_cheese'){
					$builder->where('ORD.pCheeseOrdNo <> ','');
				}
				else {
					$es_inflow[] = $v;
				}
			}
			if(!empty($es_inflow)){
				$builder->where('ORD.inflow IN (?)', array($es_inflow));
			}
		}
		if($param['cbyn']=='Y') {
			$builder->where('ORD.cbyn = ?','Y');
		}
		if($param['aboutcoupon']=='1') {
			$builder->where('ORD.about_coupon_flag = ?','Y');
		}
		if($param['escrowyn']) {
			$builder->where('ORD.escrowyn = ?',$param['escrowyn']);
		}
		if($param['eggyn']) {
			$builder->where('ORD.eggyn = ?',$param['eggyn']);
		}
		if($param['mobilepay']) {
			$builder->where('ORD.mobilepay = ?',$param['mobilepay']);
		}

		if ($param['todaygoods']) {

			$builder->where('GOODS.todaygoods = ?','y');

			$builder->join(
				 array('GOODS' => GD_GOODS)
				,'GOODS.goodsno = ITM.goodsno'
				,null
			);

		}

		if($param['cashreceipt']) {
			$builder->where('ORD.cashreceipt != '.'');
		}

		if($param['couponyn']) {

			$builder->join(
				 array('CUPN' => GD_COUPON_ORDER)
				,'CUPN.ordno = ORD.ordno'
				,null
			);

			$builder->where('CUPN.ordno IS NOT NULL');
		}

		 // 상품별 엑셀파일
		if ($param['data_mode'] == 'goods'){

			$builder->leftjoin(array('G'=>GD_GOODS),'ITM.goodsno = G.goodsno');

			$builder->reset('column')->columns(array(
				'ORD.*','MB.m_id','G.*','ITM.*','ORD.dyn','order_memo' => 'ORD.memo','ITM.goodsnm'
			));
		}
		// 주문별
		else {

			$builder->reset('column')->columns(array(
				'ORD.*','MB.m_id', 'ITM.goodsnm'
			));

			// gd_order_item 에서 검색조건이 발생하는 경우 상품갯수와 상품송장체크는 별도로 처리
			if($isOrderItemSearch) {
				$builder->columns(array('count_item' => $this->db->expression('(select count(sno) from '.GD_ORDER_ITEM.' as s_oi where s_oi.ordno=ORD.ordno)')));
				$builder->columns(array('count_dv_item' => $this->db->expression('(select count(sno) from '.GD_ORDER_ITEM.' as s_oi where s_oi.ordno=ORD.ordno and s_oi.dvcode!="" and s_oi.dvno!="")')));
			}
			else {
				$builder->columns(array('count_item' => $this->db->expression('count(ITM.ordno)')));
				$builder->columns(array('count_dv_item' => $this->db->expression('sum(ITM.dvcode != "" and ITM.dvno != "")')));
			}

			$builder->group('ORD.ordno');

		}

		if ($param['mode'] == 'group') {
			$builder->order('ORD.step2 * 10 + ORD.step, ORD.dyn, ORD.ordno desc');
		}
		else {
			$builder->order('ORD.ordno desc');

		}

		return $this->db->utility()->getAll($builder);

	}

}
?>