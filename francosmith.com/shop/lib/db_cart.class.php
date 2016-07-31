<?
class Cart extends old_Cart
{
	var $db = null;
	var $isDirect = 0;
	var $item = array();
	var $mode = 'regular';
	var $ordering = true;

	var $keepPeriod=0;	//�����Ⱓ
	var $runoutDel=0;	//ǰ����ǰ��������
	var $redirectType='Direct';	//��ٱ��ϴ���ľ׼�
	var $maxItemCnt=300;	//��ǰ �ִ뺸������
	var $act;
	var $ajaxMode=false;	//Ajax��� True:False
	var $errorMsg;	//�ַ� �޽���
	var $estimateUse='';	// ��������뿩��
	var $estimateSeal='';	// �ΰ� �̹���
	var $estimateMessage='';	// ������ �޼���

	var $msg = array(
		'maxCount' => '��ٱ��� ���� ���ɻ�ǰ�� �ʰ�! ��ٱ����� ��ǰ�� ���� �ֹ��ϰų� ���� �� �� ����ּ���'
	);

	var $is_sugi = false;

	function Cart($isDirect=0,$act=null)
	{
		global $orderitem_mode;

		if ($_GET['cart_type'] == 'todayshop') {
			$this->mode = 'todayshop';
		}

		if ($orderitem_mode == 'cart') $this->ordering = false;

		$this->db = $GLOBALS['db'];
		$this->isDirect = !$isDirect ? 0 : 1;
		$this->cartCfgSet();    //�������� ����

		$this->act=$act;
		$this->getItems();

	}

	function _get_micro() {
		$m = explode(' ',microtime());
		return $m[1].$m[0];
	}

	function get_uid($reset = false) {

		global $sess;

		$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : null;

		if ($uid == null || $reset == true) {

			$foo = false;

			do {

				$uid = md5( $this->_get_micro() . $_SERVER['REMOTE_ADDR'] );

				if ($this->db->fetch("SELECT uid FROM ".GD_CART." WHERE uid = '$uid'") == false) {

					$_SESSION['uid'] = $uid;
					$foo = true;

				}

			} while ($foo === false);

		}

		// uid�� sugi�� �����ϸ� �����ֹ�
		if (preg_match('/^sugi__/', $uid)) $this->is_sugi = true;

		return $this->arrange_cart($uid,$sess['m_id']);

	}

	function arrange_cart($uid='',$m_id='') {

		static $arranged = null;

		if ($arranged == null && $this->is_sugi == false) {
			if ($m_id) {
				$this->chkKeepPeriod(); //�����Ⱓ ���� üũ
				$this->chkRunoutDel();  //ǰ����ǰ ���� üũ
				// ���� �ٷα��� ��ǰ ����
				if(substr($uid,0,4) != "sugi") {
					$query = "DELETE FROM gd_cart WHERE uid <> '$uid' AND m_id = '$m_id' AND is_direct = 1 AND is_buy = 0";
					$rs = $this->db->query($query);
				}
				// ȸ�� �̸�, uid �� ���ų�, m_id �� ���� ��ٱ��� ������ ���� ������Ʈ
				list($cnt) = $this->db->fetch("SELECT count(*) FROM ".GD_CART." WHERE ((uid <> '$uid' AND m_id = '$m_id') OR (uid = '$uid' AND m_id = '')) AND is_buy = 0");
				if($cnt>$this->maxItemCnt) {
					$this->db->query("DELETE FROM ".GD_CART." WHERE   (uid = '$uid' AND m_id = '')  AND is_buy = 0" );
					echo "
						<script>
							var msg='��ٱ��� ���� ���� ��ǰ�� �ʰ�!��ٱ����� ��ǰ�� ���� �ֹ��ϰų� ���� �� �� �߰� ��Ⱑ �����մϴ�. ��ٱ��Ϸ� �̵� �Ͻðٽ��ϱ�? ';
							if(confirm(msg)){
								location.href='../goods/goods_cart.php';
							}
						</script>
					";
				}

				if ($cnt > 0) {
					//-->
					## ������ �ִ� ��ǰ�� ��ٱ��Ͽ� ���� �ʴ´�  2012-10-24 khs
					## m_id Ʋ���� uid ���� �Ŵ� .. ������ ��ٱ��Ͽ� ��� �ִ��� Ȯ���ϰ� , ȸ����ٱ��Ͽ� ����ش�
					$query = "SELECT uid, m_id, goodsno, optno, is_direct FROM ".GD_CART." WHERE uid = '$uid' AND m_id = '' AND is_buy = 0 AND is_direct = ".$this->isDirect;
					$rs = $this->db->query($query);

					while ($data = $this->db->fetch($rs,1)) {
						// ������ ��ǰ�� �ش� ���� ��ٱ��Ͽ� ��� �ִ��� Ȯ���Ѵ�.
						$cart_exist_check_query = $this->db->_query_print("SELECT count(*) FROM ".GD_CART."
						                           WHERE m_id =  [s] AND is_buy = 0 AND is_direct = [i]
						                           AND goodsno = [s] and optno = [s]",$m_id, $data['is_direct'], $data['goodsno'], $data['optno']);
						list($cart_exist_cnt) = $this->db->fetch($cart_exist_check_query);

						if ($cart_exist_cnt == 0) {
							$this->db->query("UPDATE ".GD_CART." SET m_id = '$m_id' WHERE uid='$uid' AND is_buy = 0 AND goodsno='".$data['goodsno']."' AND optno='".$data['optno']."' AND is_direct = ".$data['is_direct']);
						} else {
							$this->db->query("DELETE FROM ".GD_CART." WHERE uid='$uid' AND is_buy = 0 AND goodsno='".$data['goodsno']."' AND optno='".$data['optno']."' AND is_direct = ".$data['is_direct']);
						}
					}
					//<--
					//$this->db->query("UPDATE ".GD_CART." SET uid = '$uid', m_id = '$m_id' WHERE (m_id = '$m_id' OR uid='$uid') AND is_buy = 0");
					## uid Ʋ���� m_id�����Ŵ� �ϰ� ������Ʈ ���ش�.
					//������ > �ֹ� > �ֹ������Ͽ��� �ֹ��� �ۼ� �� ���� �ֹ� ������ �ֹ� ó�� �ϱ� ���� �ϰ�������Ʈ ������ �����Ѵ�. 2013-08-07 kth.
					if(substr($uid,0,4) != "sugi") {
						$this->db->query("UPDATE ".GD_CART." SET uid = '$uid', m_id = '$m_id' WHERE m_id = '$m_id' AND (substr(uid,1,4) != 'sugi' and uid !='$uid') AND is_buy = 0 AND is_direct = ".$this->isDirect);
					}
				}

			}
			else {
				// ���� �ֹ��� �ƴϰ�, ��ȸ���϶�, ���� uid �� ���� ȸ�� ������ ��ٱ��� �������� ���� ��� uid �� ��ü
				list($cnt) = $this->db->fetch("SELECT count(*) FROM ".GD_CART." WHERE uid = '$uid' AND m_id <> '' AND is_buy = 0");
				if ($cnt > 0) {
					$uid = $this->get_uid(true);
				}
			}

			$arranged = true;
		}

		return $uid;
	}

	// �޼��� �������̵�
	function getItems()
	{

		global $set, $sess, $cfg;
		$uid = $this->get_uid();

		$this->item = array();

		$today_query = "";

		if ($this->mode == 'todayshop') {
			$today_query = " AND TG.tgsno IS NOT NULL ";
		}
		else {
			$today_query = " AND TG.tgsno IS NULL ";
		}

		if ($this->ordering) {
			$ordering_query = " AND CT.ordering = 1";
		}
		else {
			$ordering_query = " ";
		}


		$query = "
			SELECT
				CT.optno,
				CT.addno,
				CT.ea,
				CT.seq,

				G.goodsno,
				G.goodsnm,
				G.img_s AS img,
				G.img_i,
				G.img_m,
				G.img_l,
				G.use_mobile_img,
				G.img_x,
				G.img_pc_x,
				G.delivery_type,
				G.goods_delivery,
				G.use_emoney,
				G.addoptnm,
				G.runout,
				G.usestock,
				G.exclude_member_reserve,
				G.exclude_member_discount,
				G.sales_unit,
				G.min_ea,
				G.max_ea,
				G.use_only_adult,

				G.todaygoods,

				O.sno AS go_option_sno,
				O.reserve,
				O.price,
				O.opt1,
				O.opt2,
				O.stock,

				TG.tgsno,TG.goodstype,TG.usememberdc
				,CT.c_addno_value


			FROM ".GD_CART." AS CT
			INNER JOIN ".GD_GOODS." AS G		ON CT.goodsno = G.goodsno
			LEFT JOIN ".GD_GOODS_OPTION." AS O	ON O.goodsno = CT.goodsno AND O.optno = CT.optno and go_is_deleted <> '1' and go_is_display = '1'
			LEFT JOIN ".GD_GOODS_ADD." AS A		ON A.goodsno = CT.goodsno AND A.sno = CT.addno AND CT.addno <> ''
			LEFT JOIN ".GD_TODAYSHOP_GOODS." AS TG ON CT.goodsno=TG.goodsno

			WHERE CT.uid = '".$uid."' AND CT.is_buy = 0 AND CT.is_direct = ".$this->isDirect." $ordering_query $today_query

			ORDER BY G.delivery_type ,CT.regdt
		";

		$rs = $this->db->query($query);

		while ($data = $this->db->fetch($rs,1)) {

			// �ɼ� ���̱�
			if ($data[opt1] || $data[opt2]) {
				$data['opt'][] = $data['opt1'];
				$data['opt'][] = $data['opt2'];
			}

			// 2015-07 qnibus ��ٱ��� ����� �����̹��� �������̵�
			if (Clib_Application::isMobile()) {
				if ($data['use_mobile_img'] === '1') {
					$data['img'] = $data['img_x'];
				} else if ($data['use_mobile_img'] === '0') {
					if ($data['img_pc_x'] != 'img_s') {
						$imgArr = explode('|', $data[$data['img_pc_x']]);
					} else {
						$imgArr = explode('|', $data['img']);
					}
					$data['img'] = $imgArr[0];
				}
			}

			// ���� ��ǰ - ���� ������ 19 �̹��� ������ - ���� �� ��ǰ �̹��� ������
			if($data['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
				$data['img'] = "../skin/" . $cfg['tplSkin'] . '/img/common/19.gif';
			}

			// �߰� �ɼ� ���� �ջ� �� ���̱�
			if ($data[addno]) {

				$_addoptnm = array();
				$_addopttype = array();
				$_addoptreq = array();

				$__addoptnm = explode("|",$data[addoptnm]);
				for ($i=0,$m=sizeof($__addoptnm);$i<$m;$i++) {
					list($_addoptnm[], $_addoptreq[], $_addopttype[]) = explode("^", $__addoptnm[$i]);
				}

				$_addopt = explode(',', $data['addno']);
				$_addopt_value  = explode(G_STR_DIVISION,$data['c_addno_value']);

				$_rs = $this->db->query("SELECT sno, goodsno,step, opt, addprice as price, addno, stats, type, FIND_IN_SET(sno,'".$data['addno']."') as added FROM ".GD_GOODS_ADD." WHERE goodsno = '$data[goodsno]' order by type,step,sno");

				while ($add = $this->db->fetch($_rs,1)) {

					if (!$add['added']) continue;

					$data['addprice'] += $add['price'];

					$_name_offset = $add['step'] + ($add['type'] == 'I' ? (int) array_search('I', $_addopttype) : 0);
					$_value_offset = $add['added'] - 1;

					if ($add['type'] == 'I') {
						$add['optnm'] = $_addoptnm[$_name_offset];
						$add['opt'] = $_addopt_value[$_value_offset];
						$data['input_addopt'][] = $add;
					}
					else {
						$add['optnm'] = $_addoptnm[$_name_offset];
						$data['select_addopt'][] = $add;
					}

					$data['addopt'][] = $add;
				}
			}

			### ��ǰ ������ ���
			if($set['emoney']['useyn'] != 'n'){
				if(!$data['use_emoney']){
					if($set['emoney']['chk_goods_emoney'] == 0){
						$data['reserve'] = getDcprice($data[price],($set['emoney']['goods_emoney']+0).'%');
					}else{
						$data['reserve'] = $set['emoney']['goods_emoney']+0;
					}
				}
			}else{
				$data['reserve'] = 0;
			}

			$this->item[] = $data;
		}

		return $this->item;

	}

	function buy() {

		$uid = $this->get_uid();
		$query = "UPDATE ".GD_CART." SET is_buy = 1 WHERE uid = '".$uid."' AND is_direct = ".$this->isDirect." AND ordering = 1";
		$this->db->query($query);
	}

	function reset()
	{
		$this->getItems();
	}

	function emptyCart()
	{
		$uid = $this->get_uid();
		$query = "DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND is_buy = 0";
		$this->db->query($query);
	}


	function addCart($goodsno,$opt,$addopt,$addopt_inputable,$ea,$reOrder_chk='')
	{
		global $sess;
		static $_delete_direct = null;

		if($ea < 1) {
			msg('���ż��� 1 �̻� �Է��ؾ� �մϴ�.', -1);
			exit;
		}

		$uid = $this->get_uid();

		// �Ϲ� �ɼ� ����
		if ($opt){
			$opt = get_magic_quotes_gpc() ? array_map("stripslashes",$opt) : $opt;
			$opt = explode("|",implode("|",$opt));
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and opt1='".mysql_real_escape_string($opt[0])."' and opt2='".mysql_real_escape_string($opt[1])."' and go_is_display = '1' and go_is_deleted <> '1'");
		}
		else {
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and `link` = 1 and go_is_display = '1' and go_is_deleted <> '1'");
		}

		if (empty($optno)) {
			$optno = $sno;
			$this->db->query("UPDATE ".GD_GOODS_OPTION." SET optno = '".$optno."' WHERE sno = '".$sno."'");
		}

		$r_addopt = array();
		$r_addopt_value = array();

		if ($addopt){
			$addopt = array_filter((array)$addopt);
			foreach ($addopt as $v){
				if ($v == -1) $v = '';
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}

		if ($addopt_inputable){
			foreach ($addopt_inputable as $v){
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}


		$addno = implode(',', $r_addopt);
		$addno_value = implode(G_STR_DIVISION, $r_addopt_value);

		if (!$this->isDirect && $reOrder_chk == ''){
			for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
				if ($goodsno==$data[goodsno] && $opt==$data[opt] && $addno==$data[addopt]){
					return 0;
				}
				if ($goodsno==$data[goodsno] && $opt==$data[opt]){
					$tmp_ea += $data[ea];
				}
			}
		}

		if($tmp_ea)$new_ea = $tmp_ea + $ea;
		else $new_ea = $ea;

		### ���üũ
	    $result = $this->chkStock($goodsno,$opt[0],$opt[1],$new_ea,'add',$reOrder_chk);
		if($result == 0) return 0;

		if($this->errorMsg){
			return 0;
		}

		if ($this->isDirect && $_delete_direct === null) {
			$_delete_direct = true;
			$this->db->query("DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND is_direct = '".$this->isDirect."'");
		}

		// ������ ����
		// �̹� ������ ��ǰ(�ٷα��� ����, �ɼ�, �߰��ɼ��� ����)�� ���ԵǾ� �ִ���
		$query = "SELECT COUNT(uid) FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$goodsno."' AND optno = '".$optno."' AND addno='".$addno."' AND c_addno_value = '".$this->db->escape($addno_value)."' AND is_direct = ".$this->isDirect."  AND is_buy = 0";
		list($chk) = $this->db->fetch($query);

		if ((int)$chk > 0) {
			// �̹� ���� ��ǰ�� �ִ� ���.
			if($reOrder_chk === 'true') return 1;
			else return 0;
		}

		$query = "SELECT MAX(seq) FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$goodsno."'";
		list($seq) = $this->db->fetch($query);
		$seq = (int)$seq + 1;

		$query = "
		INSERT INTO ".GD_CART." SET
			uid = '".$uid."',
			m_id = '".$sess[m_id]."',
			goodsno = '".$goodsno."',
			optno = '".$optno."',
			addno  = '".$addno."',
			ea = '".$ea."',
			is_buy = 0,
			is_direct = '".$this->isDirect."',
			seq	= '".$seq."',
			c_addno_value = '".$this->db->escape($addno_value)."',
			regdt = NOW()
		";
		$this->db->query($query);

		$this->reset();
		return 1;
	}

	function _getEaRemainder($ea, $unit = 1)
	{
		if ((int)$unit < 1) $unit = 1;
		return $ea % $unit;
	}

	function modCart($ea)
	{
		$chk_ea = 0;
		foreach($ea as $val) {
			if($val == "" || $val < 1)	$chk_ea = 1;
		}

		if($chk_ea) {
			exit('
			<script>
			window.onload = function() {
				alert("���ż��� 1 �̻� �Է��ؾ� �մϴ�.");
				location.replace("goods_cart.php?cart_type='.$_GET['cart_type'].'");
			}
			</script>
			');
		}

		$uid = $this->get_uid();

		foreach ($this->item as $k => $ici) {

			// ������ ����
			$ici = $this->item[$k];
			$ea_new = ($ea[$k]) ? $ea[$k] : $ici['sales_unit'];
			if ($remainder = $this->_getEaRemainder($ea_new, $ici['sales_unit']) > 0) {
				$ea_new = $ea_new - $remainder;
			}

			$tmp_ea[$ici['goodsno']][$ici['optno']] = $ea_new;

			### �ܿ����üũ
			$this->chkStock($ici[goodsno],$ici[opt][0],$ici[opt][1],$tmp_ea[$ici['goodsno']][$ici['optno']]);

			$query = "UPDATE ".GD_CART." SET ea = $ea_new WHERE uid = '".$uid."' AND goodsno = '".$ici['goodsno']."' AND optno = '".$ici['optno']."' AND addno = '".$ici['addno']."' AND seq = '".$ici['seq']."'";
			$this->db->query($query);
		}

		$this->reset();
	}

	function modEA($idx, $ea)
	{
		$uid = $this->get_uid();

		$item = $this->item[$idx];

		if ($item) {
			$query = "UPDATE ".GD_CART." SET ea = $ea WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
			$this->db->query($query);
		}
	}

	function chkStock($goodsno,$opt1='',$opt2='',$ea=1,$mode='',$reOrder_chk='')
	{
		if (!$ea) $ea = 1;

		// ���� ���� 2011-01-26 by ���¿�
		$query = "
		select

			a.goodsno, a.goodsnm, a.usestock, a.runout, a.todaygoods, a.min_ea, a.max_ea,
			b.stock,
			tg.tgsno, tg.startdt, tg.enddt

		from ".GD_GOODS." as a
		left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno
		left join ".GD_TODAYSHOP_GOODS." AS tg ON a.goodsno=tg.goodsno
		where
			a.goodsno='$goodsno' and b.go_is_deleted <> '1' and b.go_is_display = '1' and ((a.use_option = '0' and b.link) or (a.use_option = '1' and b.opt1='".mysql_real_escape_string($opt1)."' and b.opt2='".mysql_real_escape_string($opt2)."'))
		";

		extract($GLOBALS[db]->fetch($query)); //list ($goodsnm,$usestock,$runout, $todaygoods,$min_ea,$max_ea,$stock, $tgsno) = $GLOBALS[db]->fetch($query);	//2011-01-26 by ���¿�
		if(!$goodsnm){
			if ($reOrder_chk === 'true') return 0;
			else $this->msg("��ġ�ϴ� ��ǰ�� �����ϴ�.",-1);
		}
		$goodsnm = addslashes($goodsnm);

		### ���� ��ٱ��� ��ǰ���� ���ϻ�ǰ ���� ���� üũ
		if (!$this->isDirect || !$ea){
			for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
				if ($goodsno==$data[goodsno] && $opt1==$data[opt][0] && $opt2==$data[opt][1]) $tmp = $i;
			}
		}

		// ���� �߻��� ���� url
		if ($todaygoods == 'y') {
			$goodsurl = '../todayshop/today_goods.php?tgsno='.$tgsno;
		}
		else if (isset($_GET['isMobile'])) {
			$goodsurl = $_SERVER['HTTP_REFERER'];
		}
		else {
			$goodsurl = '../goods/goods_view.php?goodsno='.$goodsno;
		}

		// ���� ���� �Ⱓ üũ 2011-01-26
		if ($todaygoods == 'y') {
			$_now = time();

			$_startdt = ($startdt) ? strtotime($startdt) : 0;	// �����̼� ��ǰ�� ����Ⱓ ���Է½� DB���� null �̾�� ��
			$_enddt = ($enddt) ? strtotime($enddt) : 0;

			if ($_startdt > 0 && $_startdt > $_now) {
				//echo '�Ƚ����ؼ� ����';
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} ��ǰ�� {$startdt} ���� ���� �����մϴ�.",$goodsurl);
			}

			if ($_enddt > 0 && $_enddt < $_now) {
				//echo '������ ����';
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} ��ǰ�� �ǸŸ��� �Ǿ����ϴ�.",$goodsurl);
			}

		}	// if 2011-01-26

		### �ּ�,�ִ뱸�ż���üũ
		if($ea < $min_ea) {
			if($mode == 'add' || $this->isDirect){
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} ��ǰ�� �ּұ��ż����� {$min_ea}�� �Դϴ�.",$goodsurl);
			}else{
				if ($reOrder_chk === 'true') return 0; 
				else $this->msg("{$goodsnm} ��ǰ�� �ּұ��ż����� {$min_ea}�� �Դϴ�.",'../goods/goods_cart.php');
			}
		}
		else if($max_ea > 0 && $ea > $max_ea) {
			if($mode == 'add' || $this->isDirect){
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} ��ǰ�� �ִ뱸�ż����� {$max_ea}�� �Դϴ�.",$goodsurl);
			}else{
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm} ��ǰ�� �ִ뱸�ż����� {$max_ea}�� �Դϴ�.",'../goods/goods_cart.php');
			}
		}

		if( basename($_SERVER['PHP_SELF']) != "cartMove_proc.php" ){
			if ($runout){
				if ($reOrder_chk === 'true') return 0;
				else $this->msg("{$goodsnm}�� ǰ���� ��ǰ�Դϴ�",$goodsurl);
			}
			if ($usestock && $ea > $stock){
				if ($stock>0){
					$this->ritem[$tmp][3] = $stock;
					$this->reset();

					if($mode == 'add' || $this->isDirect){
						if ($reOrder_chk === 'true') return 0;
						else $this->msg("{$goodsnm} ��ǰ�� �ܿ� ���� {$stock}���Դϴ�",$goodsurl);
					}else{
						if ($reOrder_chk === 'true') return 0;
						else $this->msg("{$goodsnm} ��ǰ�� �ܿ� ���� {$stock}���Դϴ�",'../goods/goods_cart.php');
					}

				}else {
					if ($reOrder_chk === 'true') return 0;
					else $this->msg("{$goodsnm} ��ǰ�� �ܿ� ��� �������� �ʽ��ϴ�",$goodsurl);
				}
			}
		}

		return 1;
	}

	function delCart($idxs)	// array or int
	{
		$uid = $this->get_uid();

		if (is_array($idxs) && !empty($idxs)) {
			foreach($idxs as $idx) {

				$item = $this->item[$idx];
				if ($item) {

					$query = "DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
					$this->db->query($query);
				}
			}
		}
		else {

			$item = $this->item[$idxs];

			if ($item) {

				$query = "DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
				$this->db->query($query);

			}
		}
		$this->reset();
	}

	function setOrder($idxs='all') {	// array or 'all'

		// �������� ������ �߰��� �ƴ�, �̹� ����� �����͵��� ordering �ʵ� ������Ʈ

		$uid = $this->get_uid();

		$this->ordering = !$this->ordering;
		$this->reset();

		if ($idxs == 'all') {
			$this->db->query("UPDATE ".GD_CART." SET ordering = 1 WHERE uid = '".$uid."'");
		}
		else {

			$this->db->query("UPDATE ".GD_CART." SET ordering = 0 WHERE uid = '".$uid."'");

			if (is_array($idxs) && !empty($idxs)) foreach($idxs as $idx) {

				$item = $this->item[$idx];

				if ($item) {

					$query = "UPDATE ".GD_CART." SET ordering = 1 WHERE uid = '".$uid."' AND goodsno = '".$item['goodsno']."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND seq = '".$item['seq']."'";
					$this->db->query($query);
				}

			}
		}
		$this->ordering = !$this->ordering;
		$this->reset();

	}

	function editOption($data)
	{
		// addCart �޼���� ���� ����.

		global $sess;

		$item = $this->item[$data['idx']];

		if (empty($item)) return false;
		if ($data['ea'] < 1) return false;

		// ���� �Ҵ�
		$uid = $this->get_uid();

		$goodsno = $item['goodsno'];
		$opt = $data['opt'];
		$addopt = ($data['addopt']);
		$addopt_inputable = ($data['_addopt_inputable']);
		$ea = $data['ea'];

		// �Ϲ� �ɼ� ����
		if ($opt){
			$opt = get_magic_quotes_gpc() ? array_map("stripslashes",$opt) : $opt;
			$opt = explode("|",implode("|",$opt));
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and opt1='".mysql_real_escape_string($opt[0])."' and opt2='".mysql_real_escape_string($opt[1])."' and go_is_deleted <> '1' and go_is_display = '1'");
		}
		else {
			list($sno,$optno) = $this->db->fetch("SELECT sno, optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and `link` = 1 and go_is_deleted <> '1' and go_is_display = '1'");
		}

		if (empty($optno)) {
			$optno = $sno;
			$this->db->query("UPDATE ".GD_GOODS_OPTION." SET optno = '".$optno."' WHERE sno = '".$sno."'");
		}

		$r_addopt = array();
		$r_addopt_value = array();

		if ($addopt){
			foreach ($addopt as $v){
				if ($v == -1) $v = '';
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}

		if ($addopt_inputable){
			foreach ($addopt_inputable as $v){
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp[sno];
				$r_addopt_value[] = $tmp[opt];
				$addprice += $tmp[price];
			}
		}

		$addno = implode(',', $r_addopt);
		$addno_value = implode(G_STR_DIVISION, $r_addopt_value);

		// ���ϻ�ǰ���� ����Ǵ� ��� ������ ��ġ��, ������ ��� ��ǰ�� �����.
		for ($i=0;$i<count($this->item);$i++){ $data = $this->item[$i];
			if ($goodsno==$data[goodsno] && $opt==array_notnull($data[opt]) && $addno==$data[addno]){
				if ($item['seq'] !== $data['seq']) {
					// ����;
					$this->db->query("DELETE FROM ".GD_CART." WHERE uid = '".$uid."' AND goodsno = '".$goodsno."' AND optno = '".$data['optno']."' AND addno='".$data['addno']."' AND seq = '".$data['seq']."'");
					$tmp_ea += $data[ea];
				}
			}
		}

		if($tmp_ea)$new_ea = $tmp_ea + $ea;
		else $new_ea = $ea;

		### ���üũ
		$this->chkStock($goodsno,$opt[0],$opt[1],$new_ea,'add');

		// ������Ʈ
		$query = "
		UPDATE ".GD_CART." SET
			optno = '".$optno."',
			addno  = '".$addno."',
			c_addno_value = '".$this->db->escape($addno_value)."',
			ea = '".$new_ea."',
			is_buy = 0,
			is_direct = '".$this->isDirect."'
		WHERE uid = '".$uid."' AND goodsno = '".$goodsno."' AND optno = '".$item['optno']."' AND addno = '".$item['addno']."' AND c_addno_value = '".$item['c_addno_value']."' AND seq = '".$item['seq']."'
		";
		if ($this->db->query($query)) return true;
		else return false;
	}

	/**
		2011-09-28 by x-ta-c
		������ ���� ������ ���޽� ���� ��ǰ�� �������� �� ����մϴ�.
	 */
	function resetReserveAmount($settleprice = 0) {

		global $set;

		$_bonus = 0;

		if($set['emoney']['chk_goods_emoney'] == '0' && $set['emoney']['emoney_standard'] == '1') {

			$_adjust_items = array();

			foreach ($this->item as $ik => $_item) {

				if (is_array($this->coupons)) { foreach ($this->coupons as $_coupon) {
					// ����
					$_dc = $_coupon['sale'][$_item['goodsno']];
					if ($_dc > 0) {
						$_item['price'] = $_item['price'] - $_dc;
					}
				}}

				if ((int)$_item['use_emoney'] === 1) {	// ���� ���� (��, ���� �ʿ� ����)
					$_bonus = $_bonus + (int)$_item['reserve'] * $_item['ea'];
					$settleprice = $settleprice - $_item['price'] * $_item['ea'];	// ��ǰ���� ��.
				}
				else {
					$_adjust_items[$ik] = $_item;
				}
			}

			$_bonus = $_bonus + ($settleprice > 0 ? (int)getDcprice($settleprice,$set['emoney']['goods_emoney'].'%') : 0);

			// ���� ���� ��ǰ�� ������ ������ �� ���
			if (sizeof($_adjust_items) > 0  && (int)$this->bonus != (int)$_bonus) {

				$_total_price = 0;
				foreach($_adjust_items as $_item) $_total_price = $_total_price + ($_item['price'] * $_item['ea']);

				$_total_adj_reserve = 0;
				$_reserve_gap = $this->bonus - $_bonus;
				foreach($_adjust_items as $ik => $_item) {

					$_adj_reserve = floor(($_reserve_gap - floor($_reserve_gap * (1 - ($_item['price'] * $_item['ea'] / $_total_price)))) / $_item['ea']);
					$this->item[$ik]['reserve'] = $this->item[$ik]['reserve'] - $_adj_reserve;
					$_total_adj_reserve = $_total_adj_reserve + ($_adj_reserve * $_item['ea']);
				}

				// ������ �������� �� �����ݾ׿��� ����.
				$_bonus = $_bonus - ($_total_adj_reserve - $_reserve_gap);
			}

		}
		else {
			return;
		}

		$this->bonus = $_bonus;
	}

	function cartCfgSet() {

		require_once(dirname(__FILE__)."/../lib/qfile.class.php");
		$l = dirname(__FILE__)."/../conf/config.cart.php";
		if(file_exists($l) && (file_get_contents($l))!=''  ){
			require $l;
		}
		else{
			###�⺻ ���ð�
			$arr= array(
					'keepPeriod'=>$this->keepPeriod,
					'runoutDel'=>$this->runoutDel,
					'redirectType'=>$this->redirectType,
					'estimateUse'=>$this->estimateUse,
					'estimateSeal'=>$this->estimateSeal,
					'estimateMessage'=>$this->estimateMessage
				);

			$qfile = new qfile();
			$qfile->open($l);
			$qfile->write("<? \n");
			$qfile->write("\$cartCfg = array( \n");
			foreach ($arr as $k=>$v){
				$qfile->write("'$k' => '$v', \n");
			}
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chmod($l,0707);
			require $l;
		}
		$this->keepPeriod=$cartCfg['keepPeriod'];
		$this->runoutDel=$cartCfg['runoutDel'];
		$this->redirectType=$cartCfg['redirectType'];
		$this->estimateUse=$cartCfg['estimateUse'];
		$this->estimateSeal=$cartCfg['estimateSeal'];
		$this->estimateMessage=$cartCfg['estimateMessage'];

	}

	function chkKeepPeriod() {	//void �����Ⱓ ����üũ
		global $sess;
		if(!$this->keepPeriod)return;
		if(is_null($sess[m_id]))return;
		if(!$this->act[chkKeepPeriod])return;
		$where=" where m_id='".$sess[m_id]."' and  TIMESTAMPDIFF(DAY,regdt,NOW()) >=".$this->keepPeriod;
		list($cnt) = $this->db->fetch("select count(*) from ".GD_CART.$where);
		if($cnt>0){
			$this->db->query("delete from ".GD_CART.$where );
		}
	}

	function chkMaxCount() {	//Boolen ��ǰ�������� ����üũ
		global $sess;
		$itemCnt=count($this->item);
		if($itemCnt>=$this->maxItemCnt){
			return false;
		}
		return true;
	}

	function chkRunoutDel() {	//void ǰ����ǰ ��������üũ
		global $sess,$actRunoutDel;
		if(is_null($sess[m_id]))return;	//ȸ��üũ
		if(!$this->runoutDel)return;	//ǰ����ǰ��������>���ܵ��ϰ��
		if(!$this->act[chkRunoutDel])return;	//�׼�
		$query = "
			SELECT
				G.goodsno,
				G.runout,
				G.usestock,
				O.stock
			FROM ".GD_CART." AS CT
			INNER JOIN ".GD_GOODS." AS G		ON CT.goodsno = G.goodsno
			LEFT JOIN ".GD_GOODS_OPTION." AS O	ON O.goodsno = CT.goodsno AND O.optno = CT.optno and go_is_deleted <> '1' and go_is_display = '1'
			WHERE CT.uid = '".$_SESSION['uid']."' AND CT.is_buy = 0 AND (CT.is_direct = ".$this->isDirect." and  G.runout=1 or (O.stock<1 and G.usestock='o' ))
		";

		$rs = $this->db->query($query);

		while ($data = $this->db->fetch($rs)) {
			$query="delete from ".GD_CART."	where m_id='".$sess[m_id]."' and  goodsno=".$data[goodsno];
			$this->db->query($query);
		}
	}

	function goRedirectType() {	//��ٱ��� ��� ���̾� �̵�����
		echo $this->redirectType;
	}

	function getErrorMsg() {
		return $this->errorMsg;
	}

	function msg() {
		$args = func_get_args();
		if($this->ajaxMode) {
			$this->errorMsg=$args[0];
			return $args[0];
		}
		else {
			if($_POST[preview]=='y'){
				$args[1]=-1;
				return call_user_func_array('msg', $args);
			}
			else{
				return call_user_func_array('msg', $args);
			}

		}
	}

	function setAjaxMode($isAjax) {	//Ajax��� ����
		$this->ajaxMode = $isAjax;
	}

}
?>
