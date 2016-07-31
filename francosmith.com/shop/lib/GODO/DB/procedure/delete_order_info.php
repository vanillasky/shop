<?
class delete_order_info extends GODO_DB_procedure {

	function execute() {

		$ordno = @func_get_arg(0);

		// �� �ֹ� ������
		$param = $this->db->procedure('get_order_info', $ordno);

		// �ֹ� ������ ��� (gd_order_del ���̺� - ����ȭ �Ǿ� ���� ����)
		$columns = $this->db->desc(GD_ORDER_DEL);

		$_body = array();

		foreach($columns as $column) {
			if (isset($param[$column]))
				$_body[$column] = $param[$column];
		}

		if (sizeof($_body) > 0) {

			$builder = $this->db->builder()->insert();
			$builder->into(GD_ORDER_DEL)->set($_body);
			$builder->query();

			$this->db->query("delete from ".GD_ORDER." where ordno='$ordno'");

			### ���� �ֹ�����Ÿ ����
			$this->db->query("delete from ".GD_INTEGRATE_ORDER." where ordno='$ordno' AND channel = 'enamoo'");
			$this->db->query("delete from ".GD_INTEGRATE_ORDER_ITEM." where ordno='$ordno' AND channel = 'enamoo'");

			### ���������� ������ �����մϴ�.
			list($applysno) = $this->db -> fetch("select applysno  from ".GD_COUPON_ORDER." where ordno='$ordno'");

			if($applysno){
				$this->db->query("update ".GD_COUPON_APPLY." set status = '0' where sno='$applysno'");
				$this->db->query("delete from ".GD_COUPON_ORDER." where ordno='$ordno'");
			}

		}

		return true;

	}

}
?>