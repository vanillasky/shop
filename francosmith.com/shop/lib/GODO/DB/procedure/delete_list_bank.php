<?
class delete_list_bank extends GODO_DB_procedure {

	function execute() {

		// �������� �ʰ�, useyn �÷��� ������Ʈ.

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->update();
		$builder->from(GD_LIST_BANK);
		$builder->set(array('useyn' => 'n'))->where('sno = ?', $sno);

		return $builder->query();

	}

}
?>