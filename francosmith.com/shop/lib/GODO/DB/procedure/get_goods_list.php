<?
class get_goods_list extends GODO_DB_procedure {

	private function getSortString($str) {

		$_tmp = explode(' ', trim($str));
		$order_column = trim($_tmp[0]);
		$order_direction = isset($_tmp[1]) ? $_tmp[1] : 'asc';

		switch ($order_column) {
			case 'goodsno' :
				$sort = 'G.goodsno '.$order_direction;
				break;
			case 'regdt' :
				$sort = 'G.regdt '.$order_direction;
				break;
			case 'goodsnm' :
				$sort = 'G.goodsnm '.$order_direction;
				break;
			case 'price' :
				$sort = 'GO.price '.$order_direction;
				break;
			case 'brandno' :
				$sort = 'G.brandno '.$order_direction;
				break;
			case 'maker' :
				$sort = 'G.maker '.$order_direction;
			case 'sort' :
				$sort = 'LNK.sort '.$order_direction;
				break;
			default :
				$sort = $order_column.' '.$order_direction;
				break;

		}

		return $sort;

	}

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		// ��ǰ ī�װ�
		if ($param[category]) {

			$builder->option('straight_join');
			$builder->from(array('CT'=>GD_CATEGORY) , array('level','level_auth','auth_step'));
			$builder->join(array('LNK'=>GD_GOODS_LINK)	, 'LNK.category = CT.category', null);
			$builder->join(array('G'=>GD_GOODS)	, 'LNK.goodsno = G.goodsno');
			$builder->join(array('GO'=>GD_GOODS_OPTION)	, 'LNK.goodsno = GO.goodsno AND GO.link = 1', array('price','reserve','opt1','opt2','consumer','link'));
			$builder->leftjoin(array('GB'=>GD_GOODS_BRAND)	, 'G.brandno = GB.sno' , 'brandnm');

			$builder->where('CT.category like ?' , $this->db->wildcard($param[category],1));

			$builder->group('LNK.goodsno');

			// ī�װ� ���� ����
			if (isset($param['hidden']))
				$builder->where('LNK.hidden = ?', $param['hidden']);

			if($param['notCategory']){

				global $sess;

				for($i=0; $i<count($param['notCategory']['level']); $i++){
					if(!$sess['level']){//��ȸ���ϰ�� ���� ī�װ��� ����
						$builder->where('CT.category != ?', $param['notCategory']['category'][$i]);
					}
					else if($sess['level'] < $param['notCategory']['level'][$i]) { //����ī�װ����� ����
						$builder->where('CT.category not like ?', $this->db->wildcard($param['notCategory']['category'][$i],1));
					}
				}
			}

		}
		else {
			$builder->from(
				array('G'=>GD_GOODS)
				,array('goodsno','goodsnm','img_s','img_m','icon','open','regdt','runout','usestock','inpk_prdno','totstock','use_emoney','delivery_type','goods_delivery','color','shortdesc','maker')
			);
			$builder->join(
				array('GO'=>GD_GOODS_OPTION), 'G.goodsno = GO.goodsno'
				,array('price','reserve','link')
			);

			$builder->leftjoin(
				array('GB'=>GD_GOODS_BRAND), 'G.brandno = GB.sno'
				,array('brandnm')
			);

			$builder->group('G.goodsno');

		}

		// �����̼� ��ǰ
		if ($param['todaygoods'])
			$builder->where('G.todaygoods = ?',$param['todaygoods']);

		// �˻���
		if ($param['sword']) {

			switch ($param['skey']) {
				case 'all' :
					$_columns = array('goodsnm','goodsno','goodscd','keyword','maker');
					break;
				case 'brand' :
					//$_columns = array('brandnm');
					break;
				default :
					$_columns = array($param['skey']);
					break;
			}

			$builder->search(GD_GOODS, 'G.goodsno', $_columns, $param['sword']);
		}

		// ����
		if ($param['price'][0])
			$builder->where('GO.price >= ?', $param['price'][0]);

		if ($param['price'][1])
			$builder->where('GO.price <= ?', $param['price'][1]);

		// �귣��
		if ($param['brandno'])
			$builder->where('G.brandno = ?',$param['brandno']);

		// �����
		if ($param['regdt'][0] ) {
			$builder->where('G.regdt >= ? ', Core::helper('Date')->min($param['regdt'][0]));
		}

		if ($param['regdt'][1]) {
			$builder->where('G.regdt <= ? ', Core::helper('Date')->max($param['regdt'][1]));
		}

		// ��¿���
		if ($param['open'])
			$builder->where('G.open = ?',$param['open']);

		// ��α׿���
		if ($param['blog'])
			$builder->where('G.useblog = ?','y');

		// ����
		if ($param['searchColor']) {
			$arr_searchColor = explode("#", $param['searchColor']);
			$subWhere = array();
			foreach($arr_searchColor as $k => $v) {
				if($v) $subWhere[] = $builder->parse('G.color LIKE ?', $this->db->wildcard($v));
			}

			if(count($subWhere)) {
				$builder->where( '('.implode(' OR ',$subWhere).')' );
			}
		}

		// ������ũ ��ǰ (use : goods/goods_list.php)
		if ($param['connInterpark']) {
			$builder->where('G.inpk_prdno != ?','');
		}

		// ǰ�� ��ǰ ����
		// ���� ���� : ����, ī�װ�, �˻�, �귣��, �̺�Ʈ ������ �� ���� �ٸ�
		if (isset($param['cfg_soldout'])) {

			if ($param['cfg_soldout']['exclude']) {
				$builder->where('!( G.runout = 1 OR (G.usestock = ? AND G.totstock < 1) )','o');
			}
			// ���ܽ�Ű�� �ʴ� �ٸ�, �� �ڷ� �������� ����
			else if ($param['cfg_soldout']['back']) {
				$param[sort] = "soldout ASC, ".$param[sort];

				$builder->columns(array(
					'soldout' => $this->db->expression('IF (G.runout = 1 , 1, IF (G.usestock = \'o\' AND G.totstock = 0, 1, 0))')
				));

			}
		}

		// ����Ʈ �˻� �߰� ����
		if($param['smartSearch']) {
			$ssQuery = Core::loader('smartSearch')->ssQuery();
			if($ssQuery) $builder->where($ssQuery);
		}

		// ����
		$sort = array();

		if ($param['sort']) {
			$_tmp = explode(',',$param['sort']);

			foreach($_tmp as $_sort) {
				$sort[] = $this->getSortString($_sort);
			}
		}
		else {
			$sort[] = $this->getSortString('goodsno desc');
		}

		$builder->order( implode(',', $sort) );

		if ($param['nolimit']) {
			$result = $this->db->utility()->getAll($builder);
		}
		else {
			$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
			$param['page'] = !$param['page'] ? 1 : $param['page'];

			$result = $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);
		}

		return $result;

	}
}

?>