<?
class update_member_info extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$m_no = @func_get_arg(1);
		$m_id = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_MEMBER);
		$builder->set($param);

		if ($m_no) {
			$builder->where('m_no = ?', $m_no);
		}

		if ($m_id) {
			$builder->where('m_id = ?', $m_id);
		}

		if ($builder->has('where') && $builder->query()) {

			if (! $m_no) // $m_no �� ������ $m_id �� �ݵ�� �Ѿ��
				list($m_no) = $this->db->select(GD_MEMBER, 'm_no')->where('m_id = ?', $m_id)->limit(1)->fetch();

			if (! $m_id) // $m_id �� ������ $m_no �� �ݵ�� �Ѿ��
				list($m_id) = $this->db->select(GD_MEMBER, 'm_id')->where('m_no = ?', $m_no)->limit(1)->fetch();

			$param['m_id'] = $m_id;

			// ���� ���� �� ����
			$sc = $this->db->indexer();
			$sc->generate(GD_MEMBER, $m_no, $param, array('m_id','name','nickname','email','phone','mobile','recommid','company'));

			return true;
		}
		else return false;

	}

}
?>