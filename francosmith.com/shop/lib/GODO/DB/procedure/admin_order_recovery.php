<?
class admin_order_recovery extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder();

		$builder->select()
		->from(array('OI'=>GD_ORDER_ITEM),array('goodsnm','price','ea','sno','istep','goodsno','opt1','opt2','addopt','reserve'))
		->join(array('ORD'=>GD_ORDER),'OI.ordno = ORD.ordno',array('ordno','step','step2','m_no','emoney'))
		->where('OI.sno = ?', $sno);
		$param = $builder->fetch(1);

		$param[goodsnm] = addslashes($param[goodsnm]);

		### ��ü �ֹ��ܰ谡 ��Ҵܰ�� �Ϲ� �ֹ��ܰ�� �ܰ躹��
		if ($param[step2] && $param[m_no] && $param[emoney]){
			list($member_emoney) = $this->db -> fetch("select emoney from ".GD_MEMBER." where m_no='".$param['m_no']."' limit 1");
			if($param[emoney] > $member_emoney) {
				return 'OVER_THE_EMONEY';
			}
		}

		### ����ó�������� ������ �ƴҰ��
		if($param[istep]!=41 && ($param[istep]!=44 || $param[cyn].$param[dyn]!="nn") ) {
			return 'DUPLICATE';
		}

		### ������ ���ϴܰ��� �������� �����ϴ��� üũ
		$query = "
		select sno from
			".GD_ORDER_ITEM."
		where
			ordno = '$param[ordno]'
			and istep = '$param[step]'
			and goodsno = '$param[goodsno]'
			and opt1 = '$param[opt1]'
			and opt2 = '$param[opt2]'
			and addopt = '$param[addopt]'
			and price = '$param[price]'
		";

		list ($sno) = $this->db->fetch($query);

		if ($sno){
			$this->db->query("update ".GD_ORDER_ITEM." set ea=ea+$param[ea] where sno='$sno'");
			$this->db->query("delete from ".GD_ORDER_ITEM." where sno='$param[sno]'");
		} else {
			$this->db->query("update ".GD_ORDER_ITEM." set istep=$param[step],cancel=0 where sno='$param[sno]'");
		}

		### �ֹ����� ���� ����
		$query = "
		insert into ".GD_ORDER_CANCEL." set
			ordno	= '$param[ordno]',
			name	= '{$_COOKIE[member][name]}',
			regdt	= now()
		";
		$this->db->query($query);
		$no_cancel = $this->db->lastID();

		### ��ҹ�ȣ ������
		list($max_cancel) = $this->db->fetch("select max(cancel)+1 from gd_order_item where cancel>0");
		if ($max_cancel > $no_cancel) {
			$this->db->query("update ".GD_ORDER_CANCEL." set sno='$max_cancel' where sno='$no_cancel'");
			$no_cancel = $max_cancel;
		}

		### ���(����) �α� ����
		$query = "
		insert into ".GD_LOG_CANCEL." set
			ordno	= '$param[ordno]',
			itemno	= '$param[sno]',
			cancel	= '$no_cancel',
			`prev`	= '$param[istep]',
			`next`	= '$param[step]',
			goodsnm	= '$param[goodsnm]',
			ea		= '$param[ea]'
		";
		$this->db->query($query);

		### ��ü �ֹ��ܰ谡 ��Ҵܰ�� �Ϲ� �ֹ��ܰ�� �ܰ躹��
		if ($param[step2]){
			$query = "update ".GD_ORDER." set step2='' where ordno='$param[ordno]'";
			$this->db->query($query);

			### ��ǰ���Խ� ��������� ������
			if ($param[m_no] && $param[emoney]){
				setEmoney($param[m_no],-$param[emoney],"�ֹ��������� ���� ��������� ����",$param[ordno]);
			}
		}

		### �������
		setStock($param[ordno]);
		set_prn_settleprice($param[ordno]);

		if($param[step] > 3){

			### ��һ�ǰ ���������� ȯ��

			if($param[reserve] && $param[m_no]){

				$msg = "�ֹ� �������� ���� ���������� ����";
				$query = "update ".GD_MEMBER." set emoney = emoney + $param[reserve] where m_no='$param[m_no]'";

				$this->db->query($query);
				$query = "
				insert into ".GD_LOG_EMONEY." set
					m_no	= '$param[m_no]',
					ordno	= '$ordno',
					emoney	= '$param[reserve]',
					memo	= '$msg',
					regdt	= now()
				";

				$this->db->query($query);
			}
		}

		return true;

	}

}
?>