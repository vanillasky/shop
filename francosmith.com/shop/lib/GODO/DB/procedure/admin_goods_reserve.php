<?
class admin_goods_reserve extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		/*
		 * �������� ������ �� �Ʒ��� ����
		 */
		return $this->db->procedure('admin_goods_price', $param);

	}
}

?>