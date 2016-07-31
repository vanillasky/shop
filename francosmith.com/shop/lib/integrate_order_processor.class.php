<?
class integrate_order_processor {

    var $db;
    var $limit;
    var $sleep;
    var $model;
	var $now;

    var $temp_table_order;
    var $temp_table_item;

    var $channel;

    var $_error;

    function integrate_order_processor() {

        $this->db = Core::loader('GODO_DB');
        $this->limit = 10000;
        $this->sleep = 1;
        $this->model = array();
		$this->now = time();

        $this->temp_table_order = GD_INTEGRATE_ORDER.'__temporary__';
        $this->temp_table_item = GD_INTEGRATE_ORDER_ITEM.'__temporary__';

        $this->channel = null;
    }

    function & getInstance() {

        static $ins = null;

        if ($ins === null)
            $ins = & new integrate_order_processor();

        return $ins;
    }

    function _createTemporyTable() {

        static $created = null;

        if ($created === null) {

            // �ֹ� ����
            $this->db->query("CREATE TEMPORARY TABLE ".$this->temp_table_order." like ".GD_INTEGRATE_ORDER);

            // �ֹ���ǰ ����
            $this->db->query("CREATE TEMPORARY TABLE ".$this->temp_table_item." like ".GD_INTEGRATE_ORDER_ITEM);

            $created = true;

        }

    }

    function _getAvailableChannels() {

        static $channels = null;

        if ($channels === null) {

            $channels = array();

            // �̳���
            $channels['enamoo'] = 1;

            // ����
            $shople = Core::loader('shople');
            $channels['shople'] = $shople->_stats == 'y' ? 1 : 0;

            // üũ�ƿ�
            if (is_file(dirname(__FILE__).'/../conf/naverCheckout.cfg.php')) {
                include (dirname(__FILE__).'/../conf/naverCheckout.cfg.php');
                $channels['checkout'] = $checkoutCfg['useYn'] == 'y' ? 1 : 0;
            }

            // ��������
            if (is_file(dirname(__FILE__).'/../conf/auctionIpay.cfg.php')) {
                include (dirname(__FILE__).'/../conf/auctionIpay.cfg.php');
                $channels['ipay'] = $auctionIpayCfg['useYn'] == 'y' ? 1 : 0;
            }

			// �÷���ġ��
			if (is_file(dirname(__FILE__).'/../conf/config.plusCheeseCfg.php')) {
                include (dirname(__FILE__).'/../conf/config.plusCheeseCfg.php');
                $channels['pluscheest'] = $auctionIpayCfg['use'] == 'Y' ? 1 : 0;
            }

			// SELLY
			$selly = Core::loader('selly');
			if($selly->cust_cd) {
				$channels['selly'] = 1;
			}
			else {
				$channels['selly'] = 0;
			}

        }

		return $channels;

    }


    function _syncronize($var = null) {

        set_time_limit(0);

        $this->_createTemporyTable();

        // ����ȭ ������ �о� ����ȭ�� ���� �ҷ�����
        $_cfg = $this->_getAvailableChannels();

        $_path = dirname(__FILE__);

        //
        foreach ($_cfg as $model=>$status) {

            if (!$status)
                continue;

            $class_name = 'integrate_order_processor_'.$model;

            require_once ($_path.'/integrate_order_processor.model.'.$model.'.class.php');

            $this->model[$model] = & new $class_name;

            if ($this->model[$model]->extractData($var))
                $this->model[$model]->setSyncComplete();

        }

        return true;

    }

    // ���� ����ȭ (���� ����) ���� �޼���
    function getManualSyncStatus($filename) {

        $class = &integrate_order_processor::getInstance();
        if (is_object($class))
            return $class->_getManualSyncStatus($filename);
    }

    function _getManualSyncStatus($filename) {

        if (!file_exists($filename)) {

            // ���� ������ ����, �ʱ�ȭ
            $this->db->query("truncate table ".GD_INTEGRATE_ORDER);
            $this->db->query("truncate table ".GD_INTEGRATE_ORDER_ITEM);

            $this->db->query("update ".GD_ORDER." set sync_ = 0 ");
            $this->db->query("update ".GD_NAVERCHECKOUT_ORDER." set sync_ = 0 ");
            $this->db->query("update ".GD_AUCTIONIPAY." set sync_ = 0 ");

            list($last_orddt) = $this->db->fetch("SELECT MAX(orddt) FROM ".GD_ORDER);

            $rs = $this->db->query("SELECT orddt FROM ".GD_ORDER." WHERE orddt <= '".$last_orddt."' ORDER BY orddt ASC");
			$max = mysql_num_rows($rs);

			if ($last_orddt != null && $max > $this->limit) {

                $steps = 0;

                for ($i = 0; $i < $max; $i = $i + $this->limit) {

                    if (mysql_data_seek($rs, $i)) {
                        $tmp = mysql_fetch_assoc($rs);
                        $status[$steps]['startdt'] = $tmp['orddt'];

                        if (isset($status[$steps - 1]))
                            $status[$steps - 1]['enddt'] = $status[$steps]['startdt'];

                        $status[$steps]['result'] = 0;

                        $steps++;
                    }
                }

                $status[sizeof($status) - 1]['enddt'] = $last_orddt;

			}
			else {
				$status = false;
			}

            if ($fp = @fopen($filename, 'w')) {
                fwrite($fp, serialize($status));
                fclose($fp);
                @chmod($filename, 0707);
            }

        } else {
            $file = file($filename);
            $status = unserialize($file[0]);
        }


        return $status;

    }



    function doSync($var = null) {
        $class = &integrate_order_processor::getInstance();
        if (is_object($class))
            return $class->_syncronize($var);
    }

    function update($channel = '') {

        static $prepared_query = null;

        if (!$channel)
            return false;

        if ($prepared_query === null) {

            $prepared_query = array();

            // �ֹ� ����
            $rs = $this->db->query("DESC ".GD_INTEGRATE_ORDER);
            $_fields = array();
            while ($row = $this->db->fetch($rs, 1)) {
                if ($row['Field'] == 'seq')
                    continue;
                $row['Field'] = '`'.$row['Field'].'`';
                $_fields['a'][] = $row['Field'];
                $_fields['b'][] = 'B.'.$row['Field'];
            }

            $prepared_query['order'] = " INSERT INTO ".GD_INTEGRATE_ORDER;
            $prepared_query['order'] .= ' ('.implode(',', $_fields['a']).') ';
            $prepared_query['order'] .= ' SELECT ';
            $prepared_query['order'] .= implode(',', $_fields['b']);
            $prepared_query['order'] .= ' FROM '.$this->temp_table_order.' AS B';
            $prepared_query['order'] .= ' WHERE B.channel = \'{:channel:}\' ON DUPLICATE KEY UPDATE ';
            for ($i = 0, $m = sizeof($_fields['a']); $i < $m; $i++) {
                if (in_array($_fields['a'][$i], array('`mod_date`', '`reg_date`', '`channel`', '`ordno`')))
                    continue;
                $prepared_query['order'] .= $_fields['a'][$i].' = '.$_fields['b'][$i].',';
            }

            $prepared_query['order'] .= 'mod_date = NOW()';

            // �ֹ� ��ǰ
            $rs = $this->db->query("DESC ".GD_INTEGRATE_ORDER_ITEM);
            $_fields = array();
            while ($row = $this->db->fetch($rs, 1)) {
                $row['Field'] = '`'.$row['Field'].'`';
                $_fields['a'][] = $row['Field'];
                $_fields['b'][] = 'B.'.$row['Field'];
            }

            $prepared_query['item'] = " INSERT INTO ".GD_INTEGRATE_ORDER_ITEM;
            $prepared_query['item'] .= ' ('.implode(',', $_fields['a']).') ';
            $prepared_query['item'] .= ' SELECT ';
            $prepared_query['item'] .= implode(',', $_fields['b']);
            $prepared_query['item'] .= ' FROM '.$this->temp_table_item.' AS B';
            $prepared_query['item'] .= ' WHERE B.channel = \'{:channel:}\' ';

        }

        $query = str_replace('{:channel:}', $channel, $prepared_query['order']);
        $this->db->query($query);

        // ��ǰ DB ����
        $query = "
			DELETE A FROM ".GD_INTEGRATE_ORDER_ITEM." AS A
			INNER JOIN ".$this->temp_table_item." AS B
				ON A.ordno = B.ordno AND A.channel = B.channel
			WHERE
				A.channel = '$channel'
		";
        $this->db->query($query);

        $query = str_replace('{:channel:}', $channel, $prepared_query['item']);
        $this->db->query($query);

        return true;

    }

    // �ֹ� ������ ����ȭ ����
    function extractData() {
        $this->adjustData();
        return false;
    }

    function adjustData() {
    }

    function setSyncComplete() {
        if ($this->update()) {
        }
    }

    function setOrder($channel = null, $ordno = null, $status = null, $extra = null) {

        if (!$channel || !$ordno || ($status === null))
            return;

        require_once (dirname(__FILE__).'/integrate_order_processor.model.'.$channel.'.class.php');
        $class_name = 'integrate_order_processor_'.$channel;
        $this->model[$channel] = & new $class_name;
        //debug($status);exit;
        switch ((int) $status) {

            case 0: // �ֹ� ����
                $this->model[$channel]->setOrderAccept($ordno);
                break;

            case 1: // �Ա�Ȯ��
                $this->model[$channel]->setOrderPayConfirm($ordno);
                break;

            case 2: // ����غ���
                $this->model[$channel]->setOrderDeliveryReady($ordno);
                break;

            case 3: // �����
                $this->model[$channel]->setOrderDelivery($ordno, $extra);
                break;

            case 4: // ��ۿϷ�
                $this->model[$channel]->setOrderComplete($ordno, $extra);
                break;

            case 10: // �������
                $this->model[$channel]->setOrderCancelReq($ordno, $extra);
                break;
            case 11: // ��ҿϷ�
                $this->model[$channel]->setOrderCancelFin($ordno, $extra);
                break;

            case 20: // ȯ������
                $this->model[$channel]->setOrderRefundReq($ordno, $extra);
                break;
            case 21: // ȯ�ҿϷ�
                $this->model[$channel]->setOrderRefundFin($ordno, $extra);
                break;

            case 30: // ��ǰ����
                $this->model[$channel]->setOrderReturnReq($ordno, $extra);
                break;
            case 31: // ��ǰ�Ϸ�
                $this->model[$channel]->setOrderReturnFin($ordno, $extra);
                break;

            case 40: // ��ȯ����
                $this->model[$channel]->setOrderExchangeReq($ordno, $extra);
                break;
            case 41: // ��ȯ�Ϸ�
                $this->model[$channel]->setOrderExchangeFin($ordno, $extra);
                break;
        }

    }

    // �ֹ����� ó�� ����

    // �ֹ�����
    function setOrderAccept($ordno) {
        return false;
    }

    // �Ա�Ȯ��
    function setOrderPayConfirm($ordno) {
        return false;
    }

    function setOrderDeliveryReady($ordno) {
        return false;
    }

    // ����غ���(����Ȯ��)
    function setOrderDelivery($ordno) {
        return false;
    }

    // ��ۿϷ�(�ǸſϷ�)
    function setOrderComplete($ordno) {
        return false;
    }

    // �������
    function setOrderCancelReq($ordno, $extra) {
        return false;
    }

    // ��ҽ���
    function setOrderCancelFin($ordno, $extra) {
        return false;
    }

    // ��ǰ����
    function setOrderReturnReq($ordno, $extra) {
        return false;
    }

    // ��ǰ����
    function setOrderReturnFin($ordno, $extra) {
        return false;
    }

    // ��ȯ����
    function setOrderExchangeReq($ordno, $extra) {
        return false;
    }

    // ��ȯ����
    function setOrderExchangeFin($ordno, $extra) {
        return false;
    }

    // ȯ������
    function setOrderRefundReq($ordno, $extra) {
        return false;
    }

    // ȯ�ҽ���
    function setOrderRefundFin($ordno, $extra) {
        return false;
    }

}
?>
