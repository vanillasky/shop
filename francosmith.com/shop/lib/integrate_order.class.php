<?
require_once (dirname(__FILE__).'/integrate_order_processor.class.php');

class integrate_order {

    var $db;
    var $now; // ���� �ð� (��=timestamp)
    var $uptdt; // ������Ʈ �ð� (��=timestamp)
    var $cycle; // doSync �ֱ� (300��)

    var $checkfile;
    var $setupfile;

    function integrate_order() {

        $this->db = &$GLOBALS['db'];
        $this->now = time();
        $this->cycle = 300; // ��(=5��)

        $this->checkfile = dirname(__FILE__).'/../data/chkIntegrateOrderSyncronized';
        $this->setupfile = dirname(__FILE__).'/../data/chkIntegrateOrderSetup';


    }

    function & getInstance() {

        static $ins = null;

        if ($ins === null)
            $ins = & new integrate_order();

        return $ins;
    }

    // ����ȭ�� �ʿ����� üũ�Ͽ� ���� (boolean)
    function _isRequireSync() {

        $_sync = false;

        if (!file_exists($this->checkfile) || !file_exists($this->setupfile)) {
            echo '
			<script type="text/javascript">
			if (confirm("�ֹ� ���� ������ ����Ͻ÷���, ���� �ֹ��� �����͸� ��� ����ȭ �ϼž� �մϴ�.\n\nȮ���� �����ø� ����ȭ �������� �̵��մϴ�.")) {
				top.window.location.replace("./data.syncronize.php");
			}
			else {
				window.history.back();
			}
			</script>
			';
            exit;
        } else {

            $file = @file($this->checkfile);
            $mtime = (int) $file[0];

            if (($mtime + $this->cycle) <= $this->now) {

				$this->uptdt = date('Y-m-d H:i:s', $mtime);

		// ����iPay �ֹ����� �Ա�Ȯ��, ����غ���, ������� �ֹ��ǵ��� ��� ��ũ������� ����
		$uncompletedIpayOrdno = array();
		$uncompletedIpayOrderRes = $this->db->query("SELECT ordno FROM ".GD_INTEGRATE_ORDER." WHERE channel='ipay' AND ord_status IN(1, 2, 3)");
		while ($uncompletedIpayOrderData = $this->db->fetch($uncompletedIpayOrderRes, 1)) {
			$uncompletedIpayOrdno[] = $uncompletedIpayOrderData['ordno'];
		}
		if (count($uncompletedIpayOrdno) > 0) {
			$this->db->query("UPDATE ".GD_AUCTIONIPAY." SET uptdt_=NOW() WHERE auctionpayno IN(".implode(',', $uncompletedIpayOrdno).")");
		}

                // �̹� ����ȭ �� ������ ��, ����Ǿ� �ٽ� ����ȭ �ؾ� �ϴ� ������ ó��
                $this->db->query("UPDATE ".GD_ORDER." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
                $this->db->query("UPDATE ".GD_AUCTIONIPAY." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
                $this->db->query("UPDATE ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
				$this->db->query("UPDATE ".GD_MARKET_ORDER." SET sync_ = 0 WHERE sync_ = 1 AND uptdt_ > '".$this->uptdt."'");
                $_sync = true;
            }
        }

        return $_sync;
    }

    function checkManualSync() {

        $status = $this->getManualSyncStatus();

        if ($status === false)
            return false;
        else {

            if (is_array($status) && sizeof($status) > 0)
                foreach ($status as $step)
                    if ($step['result'] == 0)
                        return true;
        }

        return false;

    }


    function doManualSyncComplete() {

        if ($fp = @fopen($this->setupfile, 'w')) {
            fwrite($fp, '');
            fclose($fp);
            @chmod($this->setupfile, 0707);
        }

        if ($fp = @fopen($this->checkfile, 'w')) {
            fwrite($fp, $this->uptdt);
            fclose($fp);
            @chmod($this->checkfile, 0707);
        }
    }


    function getManualSyncStatus() {

        $status = &integrate_order_processor::getManualSyncStatus($this->setupfile);

        return $status;
    }

    // ���� ���� (��� ������ ����ȭ)
    function doManualSync($step) {

        $status = $this->getManualSyncStatus();

        if ($step !== false && (int) $status[$step]['result'] === 0) {

            $_sync = &integrate_order_processor::doSync($status[$step]);

            $status[$step]['result'] = (int) $_sync;

        }

        if ($fp = @fopen($this->setupfile, 'w')) {
            fwrite($fp, serialize($status));
            fclose($fp);
            @chmod($this->setupfile, 0707);
        }

        $result = $this->checkManualSync();

        if ($result === false) {

            $this->doManualSyncComplete();
            return false;
        }

        return true;

    }


    // ������ ����ȭ
    function doSync($var = null) {

        if ($this->_isRequireSync() === false)
            return;

        $_sync = &integrate_order_processor::doSync($var);

        if ($_sync && ($fp = @fopen($this->checkfile, 'w'))) {
            fwrite($fp, $this->now);
            fclose($fp);
            @chmod($this->checkfile, 0707);
        }
    }

    // ����ȭ ����
    function reserveSync() {

        $reserve = $this->now - ($this->cycle + 60); // 60 : network delay

        $file = @file($this->checkfile);
        $mtime = (int) $file[0];

        $tmp = $this->now - ($mtime + $this->cycle);
        if ($tmp >= 0)
            $reserve = $reserve - $tmp;

        if (($fp = @fopen($this->checkfile, 'w'))) {
            fwrite($fp, $reserve);
            fclose($fp);
            @chmod($this->checkfile, 0707);
        }

    }

    /*
     * �ֹ� �ܰ躰 ó��
     */
    function setOrder($channel = null, $ordno = null, $status = null, $extra = null) {

        $class = &integrate_order_processor::getInstance();
        if (is_object($class))
            $class->setOrder($channel, $ordno, $status, $extra);

    }

    function getOrderStatus($status, $cs = 'n') {
        $class = &integrate_order::getInstance();
        if (is_object($class))
            return $class->_getOrderStatus($status, $cs);
    }

    function _getOrderStatus($status, $cs = 'n') {

        global $integrate_cfg; // lib.func.php �� ������ ����

        $mod = 0;
        // y : Ŭ���� ó�� ��, f : Ŭ���� ó�� �Ϸ�, n or '' : Ŭ���� ���� �ƴ�
        if (($cs == 'y' || $cs == 'f') && $status < 10) {

            switch ((int) $status) {
                case 0:
                    $status = 10;
                    break;
                case 1:
                    $status = 20;
                    break;
                case 2:
                    $status = 20;
                    break;
                case 3:
                    $status = 30;
                    break;
                case 4:
                    $status = 30;
                    break;

            }
            if ($cs == 'f')
                $status++;
        }

        return $integrate_cfg['step'][$status];

    }

    function getCSStatus($status, $channel) {
        $class = &integrate_order::getInstance();
        if (is_object($class))
            return $class->_getCSStatus($status, $channel);
    }

    function _getCSStatus($status, $channel) {

        global $integrate_cfg;

        return $channel == 'checkout' ? $status : $integrate_cfg['claim_code'][$channel][$status];

    }

}
?>
