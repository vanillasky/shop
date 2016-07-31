<?php

class NaverCheckoutMobile
{
	var $checkoutCfg, $banWord, $db;
	var $msg = '';

	function NaverCheckoutMobile()
	{
		@include dirname(__FILE__).'/../conf/naverCheckout.cfg.php';
		@include dirname(__FILE__).'/../conf/naverCheckout.banWords.php';

		$this->checkoutCfg = $checkoutCfg;
		$this->banWord = $checkoutBan;
		$this->db = &load_class('db', 'db', dirname(__FILE__).'/../conf/db.conf.php');

		$this->checkoutCfg['mobileImgType'] = isset($this->checkoutCfg['mobileImgType']) ? $this->checkoutCfg['mobileImgType'] : 'MA';
		$this->checkoutCfg['mobileImgColor'] = isset($this->checkoutCfg['mobileImgColor']) ? $this->checkoutCfg['mobileImgColor'] : '1';
	}

	/*
	 * @name isAvailable
	 * @param Array memberStatus
	 * @return boolean
	 * @descriptoin �������� üũ�ƿ����� ��밡�ɿ��θ� ��ȯ
	 */
	function isAvailable()
	{
		$memberStatus = isset($_SESSION['sess'])?$_SESSION['sess']:null;
		if($this->checkoutCfg['useYn']!='y') return false;
		if($this->checkoutCfg['testYn']=='y' && $memberStatus['level']<=79) return false;
		elseif($this->checkoutCfg['testYn']=='y' && $memberStatus['level']>=80) return true;
		if($this->checkoutCfg['ncMemberYn']=='n' && $memberStatus['m_id']) return false; // !(üũ�ƿ� �ΰ����� �̿�) AND (Login)
		return true;
	}

	/*
	 * @name checkGoods
	 * @param int goodsno, String goodsnm
	 * @return boolean { true:�ǸŰ���, false:�ǸźҰ� }
	 * @description ���޹��� ��ǰ��ȣ�� ��ǰ���� ���Ͽ� �ǸŰ��ɿ��θ� ��ȯ
	 */
	function checkGoods($goodsno, $goodsnm)
	{
		if($this->checkoutCfg['e_exceptions'])
		{
			if(in_array($goodsno, $this->checkoutCfg['e_exceptions'])) return false;
		}

		if($this->checkoutCfg['e_category'])
		{
			$resultSet = $this->db->query("SELECT `category` FROM `".GD_GOODS_LINK."` WHERE `goodsno`=".$goodsno." AND `category`");
			while($goodsLink = $this->db->fetch($resultSet, true))
			{
				foreach ($this->checkoutCfg['e_category'] as $e_category) {
					if (preg_match('/^'.$e_category.'/', $goodsLink['category'])) return false;
				}
			}
		}

		foreach((array)$this->banWord as $word)
		{
			if(strlen($word)>0 && preg_match('/'.$word.'/', $goodsnm)) return false;
		}

		$goodsBuyable = getGoodsBuyable($goodsno);
		if($goodsBuyable === "buyable2")
		{
			$this->msg = "ȸ�� ���� ���� ��ǰ�Դϴ�. �α��� �� �õ����ּ���.";
		}
		else if($goodsBuyable === "buyable3") {
			$this->msg = "Ư�� ȸ�� ���� ���� ��ǰ�Դϴ�.";
		}
		return true;
	}

	/*
	 * @name checkCart
	 * @param Cart cart
	 * @return boolean { true:�ǸŰ���, false:�ǸźҰ� }
	 * @description ��ٱ��Ͼȿ� ��ǰ�� �߿� �ǸŰ� �Ұ����� ��ǰ�� �ִ��� üũ�Ͽ� �ǸŰ��ɿ��� Ȯ��
	 */
	function checkCart($cart)
	{
		if(count($cart->item)<1) return false;
		foreach($cart->item as $goods)
		{
			if($this->checkGoods($goods['goodsno'], $goods['goodsnm'])===false) return false;
		}
		return true;
	}

	/*
	 * @name getButtonTag
	 * @param Enum type { CART, GOODS_VIEW }   <-- CART�϶��� �����ϱ��ư�� ��µǰ�, GOODS_VIEW�϶��� �����ϱ�� ���ϱ��ư�� �Բ� ��� -->
	 * @param boolean enable   <-- true�϶��� Ȱ��ȭ false�϶��� ��Ȱ��ȭ -->
	 * @return String
	 * @description üũ�ƿ� ��ư�±׸� ��ȯ
	 */
	function getButtonTag($type, $enable)
	{
	    if (class_exists('Services_JSON', false)===false)
		  require dirname(__FILE__).'/json.class.php';

		$json = new Services_JSON();
		$buttonData = array(
			'BUTTON_KEY'         => $this->checkoutCfg['imageId'],  // üũ�ƿ����� �������� ��ư ���� Ű �Է�
			'TYPE'               => $this->checkoutCfg['mobileImgType'],  // ��ư ���� ���� ����
			'COLOR'              => $this->checkoutCfg['mobileImgColor'],  // ��ư ������ �� ����
			'COUNT'              => ($type==='CART'?'1':'2'), // ��ٱ��� �������� 1, ��ǰ �� �������� 2
			'ENABLE'             => $enable?'Y':'N'  // ��ư Ȱ��ȭ��"Y", ��Ȱ��ȭ��"N"
		);
		return '
		<section id="checkout_area">
		<script type="text/javascript" charset="UTF-8" src="http://'.($this->checkoutCfg['testYn']=='y'?'test-':'').'checkout.naver.com/customer/js/mobile/checkoutButton.js"></script>
		<script type="text/javascript">
		(function(){
			var buttonTarget = "'.($this->checkoutCfg['mobileButtonTarget']=='new'?'new':'self').'";
			var targetWindow = '.($type==='MULTI_GOODS_VIEW' ? 'parent.window' : 'window').';
			var checkoutParam = '.$json->encode($buttonData).';
			var checkFormCheckout = function(form)
			{
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
					if (_chk === false && (typeof chkEAForm != "undefined" ? chkEAForm("") : true) === false) { //��Ƽ�ɼ� �������� �ʾҴµ��� ����â ȣ�� ���μ����� ����Ǿ� �������� �߰�
						return false;
					}

				}
				else{
					if (targetWindow.m2CheckForm2) {
						return targetWindow.m2CheckForm2("goodsorder-hide");
					}
					else if (targetWindow.m2CheckForm) {
						return targetWindow.m2CheckForm("goodsorder-hide");
					}
					else {
						var res = targetWindow.chkForm(form);
						// �Է¿ɼ� �ʵ尪 ����
						if (res) {
							var addopt_inputable = targetWindow.document.getElementsByName("addopt_inputable[]");
							for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

								if (typeof addopt_inputable[i] == "object") {
									var v = addopt_inputable[i].value.trim();
									if (v) {
										var tmp = addopt_inputable[i].getAttribute("option-value").split("^");
										tmp[2] = v;
										v = tmp.join("^");
									}
									else {
										v = "";
									}
									targetWindow.document.getElementsByName("_addopt_inputable[]")[i].value = v;
								}
							}
							return true;
						}
						else {
							return false;
						}
					}
				}
				return true;
			};
			checkoutParam["BUY_BUTTON_HANDLER"] = function()
			{
				if("'.$this->msg.'"!=""){
					alert("'.$this->msg.'");
					return;
				}
				var form = targetWindow.document.frmView;
				if(checkoutParam.ENABLE==="Y")
				{
					if(checkoutParam.COUNT==2)
					{
						if(checkFormCheckout(form))
						{
							var oriAction = form.action, oriMode = form.mode.value, oriTarget = form.target;
							if(buttonTarget=="new")
							{
								targetWindow.open("","naverCheckoutPurchase","");
								form.target = "naverCheckoutPurchase";
							}
							else
							{
								form.target = "_self";
							}
							form.action = "/shop/goods/naverCheckout.php?isMobile=true";
							form.mode.value = "buy";
							form.submit();
							form.action = oriAction;
							form.mode.value = oriMode;
							form.target = oriTarget;
						}
					}
					else
					{
						var param = new Array(), idxs = document.getElementsByName("idxs[]");
						for (var i=0,m=idxs.length;i<m;i++) {
							if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
						}
						if(buttonTarget=="new")
						{
							var naverCheckoutWin = window.open("/shop/goods/naverCheckout.php?mode=cart&isMobile=true"+param,"naverCheckoutPurchase");
							location.reload();
						}
						else
						{
							location.href = "/shop/goods/naverCheckout.php?mode=cart&isMobile=true"+param;
						}
					}
				}
				else
				{
					alert("ǰ�� ���� ������ ���Ͽ� ���Ÿ� �� �� �����ϴ�.");
					return false;
				}
			};
			if(checkoutParam.COUNT==2)
			{
				checkoutParam["WISHLIST_BUTTON_HANDLER"] = function()
				{
					if("'.$this->msg.'"!=""){
						alert("'.$this->msg.'");
						return;
					}
					if(checkoutParam.ENABLE==="Y")
					{
						var form = targetWindow.document.frmView;
						var oriAction = form.action, oriMode = form.mode.value, oriTarget = form.target;
						if(buttonTarget=="new")
						{
							targetWindow.naverCheckoutWin = targetWindow.open("","naverCheckoutWish","width=100,height=100,scrollbars=0");
							form.target = "naverCheckoutWish";
						}
						else
						{
							form.target = "_self";
						}
						form.action = "/shop/goods/naverCheckout_wish.php?isMobile=true";
						form.mode.value="wish";
						form.submit();
						form.action = oriAction;
						form.mode.value = oriMode;
						form.target = oriTarget;
					}
					else
					{
						alert("ǰ�� ���� ������ ���Ͽ� ���ϱ⸦ �� �� �����ϴ�.");
						return false;
					}
				};
			}
			nhn.CheckoutButton.apply(checkoutParam);
		})();
		if(window.jQuery)
		{
			$(document).ready(function(){
				var goodsorderSection = $("section#goodsorder-hide");
				if(goodsorderSection)
				{
					goodsorderSection.css({
						"height" : $("section#goodsorder-hide").height()+20,
						"padding" : "0% 1% 0% 1%"
					});
					$("section#goodsorder-hide .pop_back").css({
						"width" : "98%"
					});
					$("section#goodsorder-hide .pop_back .pop_effect .pop_body #checkout_area").css({
						"margin" : "15px -15px 15px -15px",
						"z-index" : "100"
					});
				}
			});
		}
		</script>
		</section>
		';
	}
}

?>