<?
class save_goods extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_GOODS);
		$builder->set($param);

		if ($builder->query()) {

			$goodsno = $builder->lastID();

			// ���� ���� �� ����
			//$sc = $this->db->indexer();
			//$sc->generate(GD_GOODS, $goodsno, $param, array('keyword','goodsnm','goodscd','maker'));

			return $goodsno;
		}
		else {
			return false;
		}

	}

}
?>