<?
class admin_goods_price extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		if (!in_array($param[indicate], array('search', 'main', 'event'))) return;

		$builder = $this->db->builder()->select();

		$builder
				->from(array('G'=>GD_GOODS),array('goodsno','goodsnm','open','img_s'))
				->join(array('GO'=>GD_GOODS_OPTION),'G.goodsno = GO.goodsno','*');

		$builder->where('G.todaygoods = ?','n');

		// �˻�
		if ($param[indicate] == 'search'){

			$param['multi_rows_option'] = true;	// ��� �ɼ��� ������
			$param['wild_card_select'] = true;	// ��� �÷��� ������

			return $this->db->procedure('admin_goods_list', $param);

		}

		// ���� ���� ����
		else if ($param[indicate] == 'main'){

			global $cfg_step;	// �������� ������ �������� ���°� �ٸ�...

			$builder->join(array('GD'=>GD_GOODS_DISPLAY),'G.goodsno = GD.goodsno',null);

			// ����
			if ($cfg_step[$param[smain]][tpl] == 'tpl_07') {

				$tmp = array();

				for ($i=1,$m=(int)$cfg_step[$param[smain]][tabNum];$i<=$m;$i++) {
					$tmp[] = $i.'_'.$param[smain];
				}

				$builder->where('GD.mode IN (?)', array($tmp));
			}
			// �׿�
			elseif ($param[smain] != ''){
				$builder->where('GD.mode = ?',$param[smain]);
			}

			$builder->order('GD.sort');	// GO.sno

		}

		// �̺�Ʈ
		else if ($param[indicate] == 'event'){

			$builder->join(array('GD'=>GD_GOODS_DISPLAY),'G.goodsno = GD.goodsno',null);

			if ($param[smain] != '')
				$builder->where('GD.mode = ?',$param[sevent]);

			$builder->order('GD.sort');	// GO.sno
		}

		$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}
}

?>