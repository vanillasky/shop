<?
// �̳����� ���̺��� �����ϸ�, pCheeseOrdNo �ʵ尪 ������ �÷��� ġ�� ���� �Ǵ���.
// �ؼ�, Ŭ���� ������ �̳����� ����.
class integrate_order_processor_pluscheese extends integrate_order_processor {

    function extractData($var = null) {

        // �ֹ� ������
        $query = "
			INSERT
			INTO ".$this->temp_table_order."
			(
				channel,ordno,old_ordno,m_no,m_id_out,ord_name,ord_email,ord_phone,ord_mobile,rcv_name,rcv_phone,rcv_mobile,rcv_zipcode,rcv_address,pay_amount,ord_amount,dis_amount,res_amount,dlv_amount,dlv_type,dlv_company,dlv_no,dlv_message,ord_date,dlv_date,pay_date,fin_date,pay_bank_name,pay_bank_account,pay_method,dlv_method,flg_escrow,flg_egg,flg_cashbag,flg_inflow,ori_status,pg,
				reg_date,mod_date,
				flg_cashreceipt, flg_aboutcoupon,flg_mobile
			)

			SELECT
				'pluscheese',O.ordno,O.oldordno,O.m_no,'',O.nameOrder,O.email,O.phoneOrder,O.mobileOrder,O.nameReceiver,O.phoneReceiver,O.mobileReceiver,O.zipcode,O.address,O.prn_settleprice,O.settleprice,O.memberdc,O.reserve,O.delivery,O.deli_type,O.deliveryno,O.deliverycode,O.memo,O.orddt,O.ddt,O.cdt,O.confirmdt,O.bankSender,IF (BNK.bank IS NOT NULL, CONCAT(BNK.bank,' ', BNK.account), ''),O.settlekind, O.deli_title,O.escrowyn,O.eggyn,O.cbyn,O.inflow,CONCAT(O.step,'_', O.step2),O.pg,
				NOW(), null,
				O.cashreceipt, O.about_coupon_flag,IF(O.mobilepay = 'y', 1 , 0)

			FROM ".GD_ORDER." AS O

			LEFT JOIN ".GD_LIST_BANK." AS BNK
				ON O.bankAccount = BNK.sno

			WHERE
				O.sync_ = 0 AND O.pCheeseOrdNo > ''
		";

        if ($var !== null) {
            $query .= "
			AND O.orddt >= '$var[startdt]' AND O.orddt < '$var[enddt]'
			";
        }
        $this->db->query($query);

		if ($this->db->affected() < 1)
			return false;



        // �ֹ� ��ǰ ����
        $query = "
			INSERT
			INTO ".$this->temp_table_item."
			(
				channel,ordno,goodsnm,goodsno,`option`,ea,price,emoney,
				cs
			)
			SELECT
				'pluscheese', B.ordno,C.goodsnm,C.goodsno,CONCAT(C.opt1,' / ', C.opt2, ' / ', C.addopt),C.ea,C.price,C.reserve,
			  	IF (C.istep = 41, 'y' ,
				IF (C.istep = 42, 'y' ,
				IF (C.istep = 44, 'f' , 'n'
				)))
			FROM ".$this->temp_table_order." AS B

			INNER JOIN ".GD_ORDER_ITEM." AS C
				ON B.ordno = C.ordno

		";
        $this->db->query($query);

		// ���� ��� ����
		$query = "
			UPDATE ".$this->temp_table_order." AS O

			INNER JOIN ".GD_COUPON_ORDER." AS CP
				ON O.ordno = CP.ordno
			SET
				O.flg_coupon = 1
		";
        $this->db->query($query);

        // ���ڵ� �� ����
        $this->adjustData();

        return true;

    }

    function adjustData() {

        $_tmp = array(
			0=>array('0_0'), // �ֹ�����
			1=>array('1_0'), // �Ա�Ȯ�� (�ݵ�� �������� �Է�)
			2=>array('2_0'), // ����غ���
			3=>array('3_0'), // �����
			4=>array('4_0'), // ��ۿϷ�(�ǸſϷ�, �۱ݿϷ� ����)
			// ���
			10	=> array('0_40','0_41','0_42'),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
			11	=> array('0_44'),	// �Ϸ�
			// ȯ��
			20	=> array('1_40','1_41','2_40','2_41','3_42','4_42'),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
			21	=> array('1_44','2_44','3_44','4_44'),	// �Ϸ�
			// ��ǰ
			30	=> array('3_40','3_41','4_40','4_41'),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
			/*31	=> array('��ǰ�Ϸ�'),		// �Ϸ� -> ȯ�ҷ� �Ѿ*/
			// ��ȯ
			/*40	=> array('��ȯ��û','��ȯ����','��ȯ�߼ۿϷ�','��ȯ����','��ȯ����','��ȯ�����ź�','��ȯ�ź�','��ȯöȸ'),	// ��û,����,������,��� �Ϸᰡ �ƴ� ���
			41	=> array('��ȯ�Ϸ�'),	// �Ϸ�*/

			// ��������
			50=>array('0_50'), 51=>array('0_51'), 54=>array('0_54'));

        // �ֹ����� ����
        foreach ($_tmp as $_status=>$_cond) {
            if ( empty($_cond)) continue;

            $_cond = array_map(create_function('$var', 'return "\'".$var."\'";'), $_cond);
            $query = "
			UPDATE ".$this->temp_table_order." SET
				ord_status = $_status
			WHERE ori_status IN (".implode(',', $_cond).")";
			$this->db->query($query);
        }

      // CS ���� ������Ʈ
        $query = "
			UPDATE ".$this->temp_table_order." AS O

			INNER JOIN ".GD_ORDER_ITEM." AS OI
				ON OI.ordno = O.ordno

			INNER JOIN ".GD_ORDER_CANCEL." AS CS
				ON CS.ordno = O.ordno AND CS.sno = OI.cancel

			SET
				O.cs_regdt = CS.regdt,
				O.cs_confirmdt = CS.ccdt,
				O.cs_reason_type = CS.code,
				O.cs_reason = CS.memo

		";
        $this->db->query($query);
    }

    function setSyncComplete() {
        if ($this->update('pluscheese')) {
            $query = "
			UPDATE ".GD_ORDER." AS A
			INNER JOIN ".$this->temp_table_order." AS B
			ON A.ordno = B.ordno

			SET A.sync_ = 1,A.uptdt_ = '".date('Y-m-d H:i:s',$this->now)."'
			";
            $this->db->query($query);
            $this->db->query("TRUNCATE TABLE ".$this->temp_table_order);
			$this->db->query("TRUNCATE TABLE ".$this->temp_table_item);
        }
    }

	// �ֹ� ó�� ���� �޼��� (������� �ʰų� �������� �ʴ� �޼���� �����ص� ������)
    function setOrderAccept($ordno) {
		ctlStep($ordno,0,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderPayConfirm($ordno) {
		ctlStep($ordno,1,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderDeliveryReady($ordno) {
		ctlStep($ordno,2,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderDelivery($ordno,$extra) {

		// �����ȣ ����
		if ($extra['dlv_company'] && $extra['dlv_no']) {
			$query = "UPDATE ".GD_ORDER." SET deliveryno = '".$extra['dlv_company']."', deliverycode = '".$extra['dlv_no']."' WHERE ordno = '".$ordno."'";
			$this->db->query($query);
		}

		ctlStep($ordno,3,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    function setOrderComplete($ordno) {
		ctlStep($ordno,4,'stock');
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

	// cs ó��
    function setOrderReturnFin($ordno,$extra) {

		// ��ǰ�Ϸ�ó��
		$rs = $this->db->query("select sno from ".GD_ORDER_CANCEL." where ordno=$ordno");

		while ($row = $this->db->fetch($rs,1)) {
			$v = $row['sno'];

			if (empty($v)) continue;

			### �ֹ������� ó��
			$query = "update ".GD_ORDER_ITEM." set istep=42,dyn='r' where cancel='$v' and ordno='$ordno'";
			$this->db->query($query);

			### �ֹ� �ϰ� ó��
			$query = "update ".GD_ORDER." set step2=42,dyn='r' where ordno='$ordno' and step2=41";
			$this->db->query($query);

			### �������
			setStock($ordno);
		}

    }

    function setOrderExchangeFin($ordno,$extra) {

    	// ��ǰ �Ϸ��� ���ֹ� �ֱ�.
		$rs = $this->db->query("select sno from ".GD_ORDER_CANCEL." where ordno=$ordno");

		while ($row = $this->db->fetch($rs,1)) {
			$v = $row['sno'];

			### �ֹ������� ó��
			$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where cancel='$v' and ordno='$ordno'";
			$this->db->query($query);

			### �ֹ� �ϰ� ó��
			$query = "update ".GD_ORDER." set step2=44,dyn='e',cyn='e' where ordno='$ordno' and step2=41";
			$this->db->query($query);

			### ���ֹ�
			$newOrdno = reorder($ordno,$v);

		}

        return false;
    }


}
?>
