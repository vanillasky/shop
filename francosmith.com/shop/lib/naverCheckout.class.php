<?php
class NaverCheckout {
	var $checkoutCfg;
	var $imgTags;
	var $fpath;

	function NaverCheckout()
	{
		global $checkoutCfg;
		$this->fpath = dirname(__FILE__);
		if(!$checkoutCfg):
			if(file_exists($fpath."../conf/naverCheckout.cfg.php"))
				require $fpath."../conf/naverCheckout.cfg.php";
		endif;
		if(!$checkoutBan):
			if(file_exists($fpath."../conf/naverCheckout.banWords.php"))
				require $fpath."../conf/naverCheckout.banWords.php";
		endif;
		$this->checkoutCfg = $checkoutCfg;
		$this->banWords = $checkoutBan;
	}

	function get_imgTags($mode,$active,$msg=''){

		$imgTags = "<script type=\"text/javascript\" src=\"http://checkout.naver.com/customer/js/checkoutButton2.js\" charset=\"UTF-8\"></script>";
		//$imgTags = "<script type=\"text/javascript\" src=\"http://alpha-checkout.naver.com/customer/js/checkoutButton.js\" charset=\"UTF-8\"></script>";

		if($mode == 2){
			if($active == 'Y'){
				$imgTags .= '
				<script type="text/javascript">
				function naverCheckout(){
					var f = document.frmView;

					var _chk = false;

					// ��Ƽ�ɼ� �����
					if (typeof nsGodo_MultiOption == "object") {

						var opt_cnt = 0, data;

						nsGodo_MultiOption.clearField();

						for (var k in nsGodo_MultiOption.data) {
							data = nsGodo_MultiOption.data[k];
							if (data && typeof data == "object") {
								nsGodo_MultiOption.addField(data, opt_cnt);
								opt_cnt++;
							}
						}

						if (opt_cnt > 0) _chk = true;

					}

					f.action = "naverCheckout.php";
					f.mode.value="buy";
					f.target = "ifrmHidden";

					if (_chk || (typeof chkGoodsForm == "undefined" ? chkForm(f) : chkGoodsForm(f))) {
						f.submit();
					}

					f.mode.value="addItem";
					f.target = "";
					f.action = "";
				}
				var naverCheckoutWin="";
				function wishCheckout(){
					var f = document.frmView;
					naverCheckoutWin = window.open("","naverCheckoutWish","width=100,height=100,scrollbars=0");
					f.action = "naverCheckout_wish.php";
					f.mode.value="wish";
					f.target = "ifrmHidden";
					f.submit();
					f.mode.value="addItem";
					f.target = "";
					f.action = "";
				}
				</script>';
			}else{
				$imgTags .= '
				<script type="text/javascript">
				function naverCheckout(){
					alert("'.$msg.'");
				}
				function wishCheckout(){
					alert("'.$msg.'");
				}
				</script>
				';
			}
		}else{
			$imgTags .= '
			<script type="text/javascript">
			function naverCheckout(){
				var idxs = document.getElementsByName("idxs[]");
				var param = "";
				for (var i=0,m=idxs.length;i<m;i++) {
					if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
				}
				ifrmHidden.location.href="naverCheckout.php?mode=cart"+param;
			}
			</script>';
		}
		$imgTags .= '
		<script type="text/javascript" >//<![CDATA[
			nhn.CheckoutButton.apply({
		        BUTTON_KEY: "'.$this->checkoutCfg['imageId'].'", // üũ�ƿ����� �������� ��ư ���� Ű �Է�
		        TYPE: "'.$this->checkoutCfg['imgType'].'", // ��ư ���� ���� ����
		        COLOR: '.$this->checkoutCfg['imgColor'].', // ��ư ������ �� ����
		        COUNT: '.$mode.', // ��ư ���� ����. �����ϱ� ��ư�� ������(��ٱ��� ������) 1, ���ϱ� ��ư�� ������(��ǰ �� ������) 2�� �Է�.
		        ENABLE: "'.$active.'", // ǰ�� ���� ������ ��ư ������ ��Ȱ��ȭ�� ������ "N" �Է�
		        BUY_BUTTON_HANDLER: naverCheckout,';
		if($mode == 2){
			$imgTags .= '
        		WISHLIST_BUTTON_HANDLER: wishCheckout,';
		}

		$imgTags .= '
		        "":""
			});
		//]]></script>';
		return $imgTags;
	}

	function check_exceptions($goodsno)
	{
		$db = & $GLOBALS['db'];
		if($this->checkoutCfg['e_exceptions'] && in_array($goodsno,$this->checkoutCfg['e_exceptions']) ) return false;
		$res = $db->query("select category from ".GD_GOODS_LINK." where goodsno='$goodsno' and category");
		while($data = $db->fetch($res)){
			for($i=3;$i<=strlen($data['category']);$i=$i+3)
			{
				$category = substr($data['category'],0,$i);
				if($this->checkoutCfg['e_category'] && in_array($category,$this->checkoutCfg['e_category']) ) return false;
			}
		}
		return true;
	}

	function check_use(){
		global $sess;
		if($this->checkoutCfg['useYn'] != 'y') return false;
		if($this->checkoutCfg['testYn'] == 'y' && $sess['level']<=79) return false;
		elseif($this->checkoutCfg['testYn'] == 'y' && $sess['level']>=80) return true;
		if($this->checkoutCfg['ncMemberYn'] == "n" && $sess['m_id']) return false; // !(üũ�ƿ� �ΰ����� �̿�) AND (Login)
		return true;
	}

	function get_GoodsViewTag($goodsno,$goodsnm,$on=true,$msg='')
	{
		if(!$this->check_use())	return false;
		if(!$this->check_exceptions($goodsno)){
			$on = false;
			$msg = "�˼��մϴ�. ���̹� üũ�ƿ����� ���Ű� �Ұ��� ��ǰ�Դϴ�.";
		}
		if(!$this->check_banWords($goodsnm)){
			$on = false;
			$msg = "�˼��մϴ�. ���̹� üũ�ƿ����� ���Ű� �Ұ��� ��ǰ�Դϴ�.";
		}

		$goodsBuyable = getGoodsBuyable($goodsno);
		if($goodsBuyable === "buyable2")
		{
			$on = false;
			$msg = "ȸ�� ���� ���� ��ǰ�Դϴ�. �α��� �� �õ����ּ���.";
		}
		else if($goodsBuyable === "buyable3") {
			$on = false;
			$msg = "Ư�� ȸ�� ���� ���� ��ǰ�Դϴ�.";
		}

		$script  = '';
		$script .= $this->get_cts_script();
		$script .= $on ? $this->get_imgTags(2,'Y') : $this->get_imgTags(2,'N',$msg);

		return $script;

	}

	function get_GoodsCartTag($item)
	{
		if(!$this->check_use()) return false;
		if(!$item) return false;
		$on = true;
		if($item)foreach($item as $goods)
		{
			if(!$this->check_exceptions($goods['goodsno'])) $on = false;
			if(!$this->check_banWords($goods['goodsnm'])) $on = false;
		}

		$script  = '';
		$script .= $this->get_cts_script();
		$script .= $on ? $this->get_imgTags(1,'Y') : $this->get_imgTags(1,'N');

		return $script;
	}

	function check_banWords($goodsnm)
	{
		if($this->banWords && $goodsnm)foreach($this->banWords as $word)
		{
			if(preg_match('/'.$word.'/',$goodsnm) && $word) return false;
		}
		return true;
	}

	function get_oneclickJoin($arrData)
	{
		$resData = array('mode'=>'');
		if ($arrData['join_inflow'] != 'NCOneClick' && (isset($arrData['NCUserNo']) === true || isset($_SESSION['NCOneClickInfo']) === true)) { // �̿���
			// ���̹����� ���� �޾ƿ� ���� session�� ����
			if (isset($arrData['NCUserNo']) === true && isset($arrData['NCUserName']) === true && isset($arrData['NCUserSSN']) === true) {
				session_register("NCOneClickInfo");
				$_SESSION['NCOneClickInfo']['NCUserNo']			= trim($arrData['NCUserNo']);			// ���̹� ȸ�� ���� ��ȣ
				$_SESSION['NCOneClickInfo']['NCUserName']		= trim($arrData['NCUserName']);		// ���̹� ȸ�� �̸�
				$_SESSION['NCOneClickInfo']['NCUserSSN']		= trim($arrData['NCUserSSN']);		// ���̹� ȸ�� �ֹε�Ϲ�ȣ(13�ڸ�)
				$_SESSION['NCOneClickInfo']['NCUserPhoneNo']	= trim($arrData['NCUserPhoneNo']);	// ���̹� �޴��� ��ȣ(���ڸ�)
				$_SESSION['NCOneClickInfo']['NCUserEmail']		= trim($arrData['NCUserEmail']);		// ���̹� ���� �ּ�
				$_SESSION['NCOneClickInfo']['DeliveryZipCode']	= trim($arrData['DeliveryZipCode']);	// ���̹� ȸ�� ����� �����ȣ(���ڸ�)
				$_SESSION['NCOneClickInfo']['DeliveryAddress1']	= trim($arrData['DeliveryAddress1']);	// ���̹� ȸ�� ����� �ּ� 1
				$_SESSION['NCOneClickInfo']['DeliveryAddress2']	= trim($arrData['DeliveryAddress2']);	// ���̹� ȸ�� ����� �ּ� 2
				$_SESSION['NCOneClickInfo']['RecieverTelNo1']	= trim($arrData['RecieverTelNo1']);	// ���̹� ȸ�� ����� ����ó 1(���ڸ�)
				$_SESSION['NCOneClickInfo']['RecieverTelNo2']	= trim($arrData['RecieverTelNo2']);	// ���̹� ȸ�� ����� ����ó 2(���ڸ�)
				$_SESSION['NCOneClickInfo']['RecieverName']		= trim($arrData['RecieverName']);		// ���̹� ȸ�� ����� ������ �̸�
				$_SESSION['NCOneClickInfo']['NCMallID']			= trim($arrData['NCMallID']);			// ���̹� ������ ���̵�
				$_SESSION['NCOneClickInfo']['Timestamp']		= trim($arrData['Timestamp']);		// ���� ��û �ð�
			}

			$resData['mode'] = 'agreement';
			$resData['stepHtml'] = '
			<input type="hidden" name="join_inflow" value="NCOneClick">
			<input type="hidden" name="NCUserNo" value="'.$_SESSION['NCOneClickInfo']['NCUserNo'].'">
			<script language="javascript">
			document.frmAgree.action = "";
			document.frmAgree.target = "";
			</script>
			<link href="../proc/naver_material/css/checkout_store_join.css" type="text/css" rel="STYLESHEET"/>
			<!---- nbp ���� ���� UI ���� ---->
			<div class="chk_malljoin" style="width:100%; margin-bottom:15px;">
				<div class="line"></div>
				<div class="malltitle_section">
					<div class="mall_sub">
					<div class="logo">
						<a href="http://www.naver.com" target="_blank" class="l_naver"><span class="blind"></span></a><a href="http://checkout.naver.com" target="_blank" class="l_checkout"><span class="blind"></span></a>
					</div>
					<h1 class="blind">���̹� üũ�ƿ� ��Ŭ�� ���θ� ���� ����</h1>
					<p class="blind">üũ�ƿ� ȸ���� ������ ���θ��� ���ϰ� ������ ȸ�� ���� �� �� �ֽ��ϴ�!</p>
					</div>
					<div class="img_cart">
						<img src="../proc/naver_material/img/store/bg_malltitle2.gif" width="150" height="161" alt="">
					</div>
				</div>
				<ol class="join_list">
				<li><img src="../proc/naver_material/img/store/text_joinlist.gif" width="121" height="15" alt="01. �������� ���� ����"></li>
				<li class="on">
					<img src="../proc/naver_material/img/store/text_joinlist2_on.gif" width="135" height="15" alt="02. ���θ� ȸ������ ��û">
					<div class="bg_left"></div>
					<div class="bg_right"></div>
				</li>
				<li class="end"><img src="../proc/naver_material/img/store/text_joinlist3.gif" width="134" height="15" alt="03. ���θ� ȸ������ �Ϸ�"></li>
				</ol>
			</div>
			<!---- nbp ���� ���� UI ���� ---->
			';
		} else if ($arrData['join_inflow'] == 'NCOneClick' && isset($_SESSION['NCOneClickInfo']) === true && $_SESSION['NCOneClickInfo']['NCUserNo'] == $arrData['NCUserNo']) { // �������ۼ�
			$resData['mode'] = 'form';
			$resData['stepHtml'] = '
			<input type="hidden" name="join_inflow" value="NCOneClick">
			<input type="hidden" name="NCUserNo" value="'.$_SESSION['NCOneClickInfo']['NCUserNo'].'">
			<link href="../proc/naver_material/css/checkout_store_join.css" type="text/css" rel="STYLESHEET"/>
			<!---- nbp ���� ���� UI ���� ---->
			<div class="chk_malljoin" style="width:100%; margin-bottom:15px;">
				<div class="line"></div>
				<div class="malltitle_section">
					<div class="mall_sub">
					<div class="logo">
						<a href="http://www.naver.com" target="_blank" class="l_naver"><span class="blind"></span></a><a href="http://checkout.naver.com" target="_blank" class="l_checkout"><span class="blind"></span></a>
					</div>
					<h1 class="blind">���̹� üũ�ƿ� ��Ŭ�� ���θ� ���� ����</h1>
					<p class="blind">üũ�ƿ� ȸ���� ������ ���θ��� ���ϰ� ������ ȸ�� ���� �� �� �ֽ��ϴ�!</p>
					</div>
					<div class="img_cart">
						<img src="../proc/naver_material/img/store/bg_malltitle2.gif" width="150" height="161" alt="">
					</div>
				</div>
				<ol class="join_list">
				<li><img src="../proc/naver_material/img/store/text_joinlist.gif" width="121" height="15" alt="01. �������� ���� ����"></li>
				<li class="on">
					<img src="../proc/naver_material/img/store/text_joinlist2_on.gif" width="135" height="15" alt="02. ���θ� ȸ������ ��û">
					<div class="bg_left"></div>
					<div class="bg_right"></div>
				</li>
				<li class="end"><img src="../proc/naver_material/img/store/text_joinlist3.gif" width="134" height="15" alt="03. ���θ� ȸ������ �Ϸ�"></li>
				</ol>
			</div>
			<!---- nbp ���� ���� UI ���� ---->
			';

			// ������ ����
			$data = array(
				'name' => $_SESSION['NCOneClickInfo']['NCUserName'],				// ���̹� ȸ�� �̸�
				'resno' => $_SESSION['NCOneClickInfo']['NCUserSSN'],				// ���̹� ȸ�� �ֹε�Ϲ�ȣ(13�ڸ�)
				'email' => $_SESSION['NCOneClickInfo']['NCUserEmail'],				// ���̹� ���� �ּ�
				'zipcode' => $_SESSION['NCOneClickInfo']['DeliveryZipCode'],		// ���̹� ȸ�� ����� �����ȣ(���ڸ�)
				'address' => $_SESSION['NCOneClickInfo']['DeliveryAddress1'],		// ���̹� ȸ�� ����� �ּ� 1
				'address_sub' => $_SESSION['NCOneClickInfo']['DeliveryAddress2'],	// ���̹� ȸ�� ����� �ּ� 2
				'phone' => $_SESSION['NCOneClickInfo']['RecieverTelNo1'],			// ���̹� ȸ�� ����� ����ó 1(���ڸ�)
				'mobile' => $_SESSION['NCOneClickInfo']['NCUserPhoneNo'],			// ���̹� �޴��� ��ȣ(���ڸ�)
			);

			// ������ ��ȣȭ
			$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
			foreach ($data as $k => $v) {
				if ($v != '') {
					$temp_ar = explode('|||', $naverCheckoutAPI->ncCrypt('decrypt',$v,$_SESSION['NCOneClickInfo']['Timestamp']));
					if($temp_ar[0] == 'ERRO') $data[$k] = '';
					else $data[$k] = $temp_ar[1];
				}
			}

			// ������ chracset��ȯ(UTF-8 -> EUC-KR)
			foreach ($data as $k => $v) {
				$data[$k] = iconv('UTF-8', 'EUC-KR', $v);
			}

			// ������ ����
			$resData['data'] = $data;
			$resData['data']['resno'] = array(substr($data['resno'], 0, 6), substr($data['resno'], 6, 7));
			$dy = (substr($data['resno'], 6, 1) == 1 || substr($data['resno'], 6, 1) == 2 ? '19' : '20');
			$resData['data']['birth_year'] = $dy.substr($data['resno'], 0, 2);
			$resData['data']['birth'] = array(substr($data['resno'], 2, 2), substr($data['resno'], 4, 2));
			$resData['data']['zipcode'] = array(substr($data['zipcode'], 0, 3), substr($data['zipcode'], 3, 3));
			if (substr($data['phone'], 0, 2) == '02') {
				$resData['data']['phone'] = array(substr($data['phone'], 0, 2), substr($data['phone'], 2, -4), substr($data['phone'], -4));
			} else {
				$resData['data']['phone'] = array(substr($data['phone'], 0, 3), substr($data['phone'], 3, -4), substr($data['phone'], -4));
			}
			$resData['data']['mobile'] = array(substr($data['mobile'], 0, 3), substr($data['mobile'], 3, 4), substr($data['mobile'], 7));
		}
		return $resData;
	}

	function get_oneclickJoinOk($mode)
	{
		$resData = array('mode'=>'');
		if ($mode == 'nc') {
			$resData['mode'] = 'ok';
			$resData['stepHtml'] = '
			<link href="../proc/naver_material/css/checkout_store_join.css" type="text/css" rel="STYLESHEET"/>
			<!---- nbp ���� ���� UI ���� ---->
			<div class="chk_malljoin" style="width:100%; margin-bottom:15px;">
				<div class="line"></div>
				<div class="malltitle_section">
					<div class="mall_sub">
					<div class="logo">
						<a href="http://www.naver.com" target="_blank" class="l_naver"><span class="blind"></span></a><a href="http://checkout.naver.com" target="_blank" class="l_checkout"><span class="blind"></span></a>
					</div>
					<h1 class="blind">���̹� üũ�ƿ� ��Ŭ�� ���θ� ���� ����</h1>
					<p class="blind">üũ�ƿ� ȸ���� ������ ���θ��� ���ϰ� ������ ȸ�� ���� �� �� �ֽ��ϴ�!</p>
					</div>
					<div class="img_cart">
						<img src="../proc/naver_material/img/store/bg_malltitle2.gif" width="150" height="161" alt="">
					</div>
				</div>
				<ol class="join_list">
				<li><img src="../proc/naver_material/img/store/text_joinlist.gif" width="121" height="15" alt="01. �������� ���� ����"></li>
				<li>
					<img src="../proc/naver_material/img/store/text_joinlist2.gif" width="133" height="15"  alt="02. ���θ� ȸ������ ��û">
					<div class="bg_left"></div>
				</li>
				<li class="on end">
					<img src="../proc/naver_material/img/store/text_joinlist3_on.gif" width="136" height="15" alt="03. ���θ� ȸ������ �Ϸ�">
					<div class="bg_left"></div>
				</li>
				</ol>
				<div class="clause_section">
					<div class="join_end">
						<dl class="blind">
						<dt>��Ŭ�� ���θ� ������ �Ϸ� �Ǿ����ϴ�</dt>
						<dd>üũ�ƿ� ��Ŭ�� ���θ� ���� ������ üũ�ƿ� Ȩ > ������ ����Ʈ����Ȯ�� �Ͻ� �� �ֽ��ϴ�.
						üũ�ƿ� ���θ� ȸ�� ���� ���񽺸� �̿��Ͻø� üũ�ƿ� ���ûӸ� �ƴ϶� �������� ���õ� ������ �� �ֽ��ϴ�.</dd>
						</dl>
						<a href="http://checkout.naver.com/customer/mall.nhn" target="_blank"><span class="blind">üũ�ƿ� Ȩ&gt;������ ����Ʈ</span></a>
					</div>
					<div class="btn_right">
						<a href="http://checkout.naver.com" target="_blank"><img src="../proc/naver_material/img/store/btn_chkhome.gif" width="67" height="12" alt="üũ�ƿ� Ȩ"></a>
					</div>
				</div>
			</div>
			<!---- //nbp ���� ���� UI ���� ---->
			';
		}
		return $resData;
	}

	function get_cts_script() {

		$script = '
		<script type="text/javascript">
		if (typeof _TGV == \'undefined\') {
			function _TGV(b,a,c,d){ var f = b.split(c);for(var i=0;i<f.length; i++){ if( _TIX(f[i],(a+d))==0) return f[i].substring(_TIX(f[i],(a+d))+(a.length+d.length),f[i].length); }	return \'\'; };
		}
		try {
		var _CKNVADID = _TGV(unescape(_TGV(document.cookie,\'CTSCKURL\',\'; \',\'=\')),\'NVADID\',\'&\',\'=\');
		document.cookie = "NVADID=" + _CKNVADID +"; path=/; domain="+document.domain+"; ";
		}
		catch (e) { }
		</script>
		';

		return $script;
	}
}
?>
