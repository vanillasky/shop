<?
// �α��ǰ
class populate {

	var $cfg;
	var $cache_path;

	var $sites = array();

	function populate() {

		$_dir = dirname(__FILE__);
		$cfg_populate = array();

		// cache directory
		if (!is_dir($_dir.'/../data/statistics')) {
			@mkdir($_dir.'/../data/statistics');
			@chmod($_dir.'/../data/statistics', 0777);
		}

		$this->cache = $_dir.'/../data/statistics/populate_goods_data.cached.txt';

		$this->cfg = $this->getConf();
	}

	function getConf() {
		if (is_file(dirname(__FILE__)."/../conf/config.populate.php")) {
			include dirname(__FILE__)."/../conf/config.populate.php";
		}

		// �⺻ ���� ��
		if (isset($cfg_populate) === false || count($cfg_populate) < 1) {
			$cfg_populate['type'] = 'order'; // ����Ÿ��(order:��ǰ�Ǹż���, pageview:�����������)
			$cfg_populate['limit'] = 20; // ���� ����(1~20)
			$cfg_populate['range'] = 'hour'; // �����ֱ�(hour:1�ð�, week:������, month:����)
			$cfg_populate['range_month'] = ''; // �����ֱ� ���� �Ⱓ
			$cfg_populate['collect'] = 'hour'; // �����Ⱓ(hour:1�ð�, week:������, month:����)
			$cfg_populate['collect_month'] = ''; // �����Ⱓ ���� �Ⱓ
			$cfg_populate['include_soldout'] =  1; // ǰ����ǰ(0:ǰ����ǰ ����, 1:ǰ����ǰ ����)
			$cfg_populate['title'] = '';
			$cfg_populate['design'] = 'expand'; // ���ø� ����(expand:��ħ��, rollover:�ѿ�����)
		}

		// �����Ⱓ �׸� �߰� ���� ���Ž� (2014.10)
		if ($cfg_populate['range'] != '' && $cfg_populate['collect'] == '') {
			if ($cfg_populate['range'] == 'hour' && $cfg_populate['type'] == 'pageview') {
				// �������� �����ʹ� ��¥������ ����ǹǷ� �����ֱⰡ 1�ð��̸� �����Ⱓ�� �����Ϸ� ����
				$cfg_populate['collect'] = 'week';
			}
			else {
				$cfg_populate['collect'] = $cfg_populate['range'];
			}
			$cfg_populate['collect_month'] = $cfg_populate['range_month'];
		}

		return $cfg_populate;
	}

	function getData() {

		if (empty($this->cfg)) return false;

		$_update = false;

		// ĳ�� ������ ���ٸ� ������Ʈ
		if (!is_file($this->cache)) {
			$data = $this->_getLiveData();
		}
		else {

			// �����Ⱓ �� ��� ĳ�ø� �ҷ�����, �������� ����
			$_now = time();

			$_mtime = filemtime( $this->cache );

			switch ($this->cfg['range']) {

				case 'hour':
					if (date('H',$_now) != date('H',$_mtime)) $_update = true;
					break;
				case 'week':
					if (date('W',$_now) != date('W',$_mtime)) $_update = true;
					break;
				case 'month':
					$_mtime = strtotime('+'.$this->cfg['range_month'].' month',$_mtime);
					if (date('Ymd',$_now) > date('Ymd',$_mtime)) $_update = true;
					break;
			}

			if ($_update == true) {
				$data = $this->_getLiveData();
			}
			else {
				$data = $this->_getCache( $this->cache );
			}
		}

		return array_slice($data, 0, $this->cfg['limit']);

	}

	function _getCache($file) {

		// ������ �о� �迭���·� ��ȯ�Ͽ� ����.
		$data = array();
		$_data = '';
		$LF = chr(10);

		if ($fh = @fopen($file, 'r')) {

			flock($fh, LOCK_SH);
			if (filesize($file) > 0) $_data = fread($fh, filesize($file));
			flock($fh, LOCK_UN);
			fclose($fh);

			$tmp_data = explode($LF, $_data);


			for($i=0,$m=sizeof($tmp_data);$i<$m;$i++) {
				if (empty($tmp_data[$i])) continue;
				$row = explode('|',$tmp_data[$i]);
				$data[$i]['goodsno'] = array_shift($row);
				$data[$i]['goods_cnt'] = array_pop($row);
				$data[$i]['goodsnm'] = implode('|',$row);
			}

		}

		return $data;

	}

	function _setCache($data,$file='') {

		// �����͸� ���Ͽ� ����
		$data_size = sizeof($data);

		$LF = chr(10);
		$_data = '';

		for($i=0,$m=sizeof($data);$i<$m;$i++) {
			$row = $data[$i];
			$_data .= $row['goodsno'].'|'.$row['goodsnm'].'|'.$row['goods_cnt'].$LF;
		}

		if ($fh = @fopen($file, 'w')) {

			flock($fh, LOCK_EX);
			fwrite($fh, $_data);
			flock($fh, LOCK_UN);
			fclose($fh); @chmod($file, 0777);
		}

		return;

	}

	function _getLiveData() {

		// ������� ���� �ð������� db �����͸� �о� ĳ�ÿ� �����ϰ�, ����
		global $db;

		$data = array();

		if ($this->cfg['type'] == 'pageview') {
			$query = "
			SELECT
				SUM(PGV.cnt) AS `goods_cnt`,
				G.goodsno, G.goodsnm
			FROM gd_goods_pageview AS PGV
			INNER JOIN gd_goods AS G
			ON PGV.goodsno = G.goodsno
			";

			switch ($this->cfg['collect']) {

				case 'hour':
					$query .= ' WHERE PGV.date >= DATE_SUB(CURDATE(), INTERVAL 1 HOUR)';
					break;
				case 'week':
					$query .= ' WHERE PGV.date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)';
					break;
				case 'month':
					$query .= ' WHERE PGV.date >= DATE_SUB(CURDATE(), INTERVAL '.$this->cfg['collect_month'].' MONTH)';
					break;
			}

			if ($this->cfg['include_soldout'] === '0') {
				$query .= ' AND ( G.runout <> 1 AND (G.usestock <> \'o\' OR G.totstock > 1))';
			}

			$query .= '
			GROUP BY G.goodsno

			ORDER BY `goods_cnt` DESC

			LIMIT 20
			';
		}
		else {
			$query = "
			SELECT
				OI.goodsno, OI.goodsnm, COUNT(OI.goodsno) AS `goods_cnt`
			FROM gd_order as O
				FORCE INDEX (PRIMARY, orddt)
			STRAIGHT_JOIN gd_order_item AS OI
				FORCE INDEX (idx)
				ON O.ordno = OI.ordno
			INNER JOIN gd_goods AS G
			ON OI.goodsno = G.goodsno
			";

			switch ($this->cfg['collect']) {

				case 'hour':
					$query .= ' WHERE O.orddt >= DATE_SUB(NOW(), INTERVAL 1 HOUR)';
					break;
				case 'week':
					$query .= ' WHERE O.orddt >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
					break;
				case 'month':
					$query .= ' WHERE O.orddt >= DATE_SUB(NOW(), INTERVAL '.$this->cfg['collect_month'].' MONTH)';
					break;
			}

			if ($this->cfg['include_soldout'] === '0') {
				$query .= ' AND ( G.runout <> 1 AND (G.usestock <> \'o\' OR G.totstock > 1))';
			}

			$query .= '

			AND G.open = 1

			GROUP BY OI.goodsno

			ORDER BY `goods_cnt` DESC

			LIMIT 20
			';

		}

		$res = $db->query($query);

		while($row = $db->fetch($res,1)) {
			$data[] = $row;
		}

		$this->_setCache($data, $this->cache);

		return $data;

	}

}	// eof populate;
?>