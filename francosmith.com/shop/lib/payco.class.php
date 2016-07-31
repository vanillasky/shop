<?php
/*
 * payco CLASS
 *
 * @author payco.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2015-06-09
 */
class payco {
	//������
	var $paycoCfg, $cfg, $set, $cfgMobileShop, $screenType;

	//button display area
	var $paycoDisplayID = 'paycoDisplayID';

	// payco script plugin url
	var $pluginUrl = 'https://static-bill.nhnent.com/payco/checkout/js/payco.js';

	// payco script plugin url - mobile
	var $pluginMobileUrl = 'https://static-bill.nhnent.com/payco/checkout/js/payco_mobile.js';

	// payco button image url
	var $buttonImgUrl = 'https://payco.godo.co.kr/image/btn/';

	function payco()
	{
		global $paycoCfg, $cfg, $set, $cfgMobileShop;

		if(!$cfg) $cfg = Core::loader('config')->load('config');
		if(!$set) $set = Core::loader('config')->load('configpay');
		if(!$cfgMobileShop && is_file(dirname(__FILE__) . '/../conf/config.mobileShop.php')){
			include dirname(__FILE__) . '/../conf/config.mobileShop.php';
		}
		//���DB payco.config.php ���Ϸ� ����
		if(!$paycoCfg && !is_file(dirname(__FILE__) . '/../conf/payco.cfg.php')){
			Core::loader('paycoConfig')->restorePaycoBackupData();
		}
		if(!$paycoCfg){
			@include dirname(__FILE__) . '/../conf/payco.cfg.php';
		}

		$this->screenType	= $this->getScreenType();
		$this->paycoCfg		= $paycoCfg;
		$this->setDefaultPaycoCfg();
		$this->cfg			= $cfg;
		$this->set			= $set;
		$this->cfgMobileShop= $cfgMobileShop;
	}

	/*
	* '������ ���� ��ǰ ����', '������ ���� �̿� ����' �ʱⰪ ����
	* @param
	* @return
	* @date 2015-06-09
	*/
	function setDefaultPaycoCfg()
	{
		if(!$this->paycoCfg['useYn']) $this->paycoCfg['useYn'] = 'all';
		if(!$this->paycoCfg['button_checkout']) $this->paycoCfg['button_checkout'] = 'A';
		if(!$this->paycoCfg['button_checkoutDetail_A']) $this->paycoCfg['button_checkoutDetail_A'] = 'A1';
		if(!$this->paycoCfg['button_checkoutDetail_B']) $this->paycoCfg['button_checkoutDetail_B'] = 'B1';
		if(!$this->paycoCfg['button_checkoutDetail_C']) $this->paycoCfg['button_checkoutDetail_C'] = 'C1';
		switch($this->paycoCfg['button_checkout']){
			case 'A':
				$this->paycoCfg['button_checkoutDetail'] = $this->paycoCfg['button_checkoutDetail_A'];
			break;

			case 'B':
				$this->paycoCfg['button_checkoutDetail'] = $this->paycoCfg['button_checkoutDetail_B'];
			break;

			case 'C':
				$this->paycoCfg['button_checkoutDetail'] = $this->paycoCfg['button_checkoutDetail_C'];
			break;
		}
		if(!$this->paycoCfg['button_easypay']) $this->paycoCfg['button_easypay'] = 'A1';
	}

	/*
	* HTML ��ư
	* @param string [type : CHECKOUT-������(üũ�ƿ�)��, EASYPAY-���������], [mobile : true-����ϼ�, false-PC��], [checkoutType : goodsView-��ǰ��������, goodsCart-��ٱ���]
	* @return string
	* @date 2015-06-09
	*/
	function getButtonHtmlCode($type, $mobile, $checkoutType='')
	{
		//��뼳�� ���� üũ
		if($this->check_useAble($type, $mobile) == true && $this->check_level($type, $GLOBALS['sess']) == true){
			$buttonHtml = $this->getButtonPlugin($type, $mobile, $checkoutType);

			return $buttonHtml;
		}

		return false;
	}

	/*
	* ��ư plugin
	* @param string [type : CHECKOUT-������(üũ�ƿ�)��, EASYPAY-���������], [mobile : true-����ϼ�, false-PC��], [checkoutType : goodsView-��ǰ��������, goodsCart-��ٱ���]
	* @return string
	* @date 2015-06-09
	*/
	function getButtonPlugin($type, $mobile, $checkoutType='')
	{
		if($type != 'CHECKOUT' && $type != 'EASYPAY') return false;
		if($type == 'CHECKOUT'){
			if($checkoutType != 'goodsView' && $checkoutType != 'goodsCart') return false;
		}

		if($mobile){
			$url = $this->pluginMobileUrl;
		}
		else {
			$url = $this->pluginUrl;
		}

		if($mobile){
			//����ϼ� ��ư
			if($type == 'CHECKOUT') $styleLayout = 'width: 100%;';

			$button = '
				<div id="'.$this->paycoDisplayID.'" style="display: inline-block; text-align: center; '.$styleLayout.'"></div>
				<script type="text/javascript" src="'.$url.'" charset="UTF-8"></script>
			';

			if($type == 'CHECKOUT') {
				//������(üũ�ƿ�)��
				if($checkoutType == 'goodsView'){
					//��ǰ��������
					$button .= $this->getCheckoutScriptMobile_view();
				}
				else {
					//��ٱ���������
					$button .= $this->getCheckoutScriptMobile_cart();
				}
			}
			else {
				//���������
				$button .= $this->getEasypayScript();
			}
		}
		else {
			//PC�� ��ư
			$button = '
				<div id="'.$this->paycoDisplayID.'" style="display: inline;"></div>
				<script type="text/javascript" src="'.$url.'" charset="UTF-8"></script>
			';

			if($type == 'CHECKOUT') {
				//������(üũ�ƿ�)��
				if($checkoutType == 'goodsView'){
					//��ǰ��������
					$button .= $this->getCheckoutScript_view();
				}
				else if ($checkoutType == 'goodsCart'){
					//��ٱ���������
					$button .= $this->getCheckoutScript_cart();
				}
			}
			else {
				//���������
				$button .= $this->getEasypayScript();
			}
		}

		return $button;
	}

	/**
	 * CHECKOUT�� script - ��ǰ��������
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- �ֹ����� : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- ��ưŸ�� ���� - default : A1
	 * BUTTON_HANDLER		- �����ϱ� ��ư �̺�Ʈ Handler �Լ� ���
	 * BUTTON_HANDLER_ARG	- handler �Լ� argument
	 * DISPLAY_PROMOTION	- �̺�Ʈ ���� ��� ����
	 * DISPLAY_ELEMENT_ID	- ����� element id
	 */
	function getCheckoutScript_view()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				var paycoPopup;
				function paycoCheckoutSubmit()
				{
					var f = window.document.frmView;
					var _chk = false;
					var screenType = "'.$this->screenType.'";
					var submitTarget = "";

					if(!f || f == undefined){
						alert("Payco ���� ����� �Ұ����մϴ�.\n��Ų�� Ȯ���Ͽ� �ּ���.");
						return false;
					}

					//form ���� ����
					var oriAction	= f.action;
					var oriTarget	= f.target;
					var oriMode		= f.mode.value;

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

					if (_chk || (typeof chkGoodsForm == "undefined" ? chkForm(f) : chkGoodsForm(f))) {

						if(screenType == "MOBILE"){
							submitTarget = "ifrmHidden";
						}
						else {
							'.$this->getVoidPopupOpenScript().'

							submitTarget = "paycoPopup";
						}

						f.action		= "paycoCheckout.php";
						f.mode.value	= "goodsView";
						f.target		= submitTarget;
						f.submit();

						//�������� ����
						f.action		= oriAction;
						f.mode.value	= oriMode;
						f.target		= oriTarget;
					}
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
			';

			return $script;
	}

	/**
	 * CHECKOUT�� script - ��ٱ��� ������
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- �ֹ����� : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- ��ưŸ�� ���� - default : A1
	 * BUTTON_HANDLER		- �����ϱ� ��ư �̺�Ʈ Handler �Լ� ���
	 * BUTTON_HANDLER_ARG	- handler �Լ� argument
	 * DISPLAY_PROMOTION	- �̺�Ʈ ���� ��� ����
	 * DISPLAY_ELEMENT_ID	- ����� element id
	 */
	function getCheckoutScript_cart()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				var paycoPopup;
				function paycoCheckoutSubmit()
				{
					var screenType = "'.$this->screenType.'";
					var idxs = document.getElementsByName("idxs[]");
					var param = "";
					var submitTarget = "";

					if(idxs.length < 1){
						alert("������ ��ǰ�� �����ϴ�.");
						return false;
					}

					for (var i=0,m=idxs.length;i<m;i++) {
						if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
					}

					if(screenType == "MOBILE"){
						submitTarget = window;
					}
					else {
						'.$this->getVoidPopupOpenScript().'

						submitTarget = paycoPopup;
					}

					submitTarget.location.href="./paycoCheckout.php?mode=goodsCart"+param;
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
			';

			return $script;
	}

	/**
	 * MOBILE CHECKOUT �� script - ��ǰ��������
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- �ֹ����� : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- ��ưŸ�� ���� - default : A1
	 * BUTTON_HANDLER		- �����ϱ� ��ư �̺�Ʈ Handler �Լ� ���
	 * BUTTON_HANDLER_ARG	- handler �Լ� argument
	 * DISPLAY_PROMOTION	- �̺�Ʈ ���� ��� ����
	 * DISPLAY_ELEMENT_ID	- ����� element id
	 */
	function getCheckoutScriptMobile_view()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				function paycoCheckoutSubmit()
				{
					var f = window.document.frmView;
					var _chk = false;
					var screenType = "'.$this->screenType.'";
					var submitTarget = "";

					if(!f || f == undefined){
						alert("Payco ���� ����� �Ұ����մϴ�.\n��Ų�� Ȯ���Ͽ� �ּ���.");
						return false;
					}

					//form ���� ����
					var oriAction	= f.action;
					var oriTarget	= f.target;
					var oriMode		= f.mode.value;

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
						var f = window.document.frmView;

						if(!f || f == undefined){
							alert("Payco ���� ����� �Ұ����մϴ�.\n��Ų�� Ȯ���Ͽ� �ּ���.");
							return false;
						}

						if (window.m2CheckForm2) {
							if(!window.m2CheckForm2("goodsorder-hide")){
								return false;
							}
						}
						else if (window.m2CheckForm) {
							if(!window.m2CheckForm("goodsorder-hide")){
								return false;
							}
						}
						else {
							// �Է¿ɼ� �ʵ尪 ����
							if (chkForm(f)) {
								var addopt_inputable = window.document.getElementsByName("addopt_inputable[]");
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
										window.document.getElementsByName("_addopt_inputable[]")[i].value = v;
									}
								}
							}
							else {
								return false;
							}
						}
					}

					f.action		= "'.$this->cfg['rootDir'].'/goods/paycoCheckout.php?isMobile=Y";
					f.mode.value	= "goodsView";
					f.target		= "ifrmHidden";
					f.submit();

					//�������� ����
					f.action		= oriAction;
					f.mode.value	= oriMode;
					f.target		= oriTarget;
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
		';

		return $script;
	}

	/**
	 * MOBILE CHECKOUT �� script - ��ٱ��� ������
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- �ֹ����� : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- ��ưŸ�� ���� - default : A1
	 * BUTTON_HANDLER		- �����ϱ� ��ư �̺�Ʈ Handler �Լ� ���
	 * BUTTON_HANDLER_ARG	- handler �Լ� argument
	 * DISPLAY_PROMOTION	- �̺�Ʈ ���� ��� ����
	 * DISPLAY_ELEMENT_ID	- ����� element id
	 */
	function getCheckoutScriptMobile_cart()
	{
		global $cfg;

		$script = '
			<script type="text/javascript">
			//<![CDATA[
				function paycoCheckoutSubmit()
				{
					var idx = document.getElementsByName("idx[]");
					if(idx.length > 0){
						var idxs = idx;
					}
					else {
						var idxs = document.getElementsByName("idxs[]");
					}

					var param = "";

					if(idxs.length < 1){
						alert("������ ��ǰ�� �����ϴ�.");
						return false;
					}

					for (var i=0,m=idxs.length;i<m;i++) {
						if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
					}

					ifrmHidden.location.href="'.$this->cfg['rootDir'].'/goods/paycoCheckout.php?mode=goodsCart&isMobile=Y&"+param;
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
		';

		return $script;
	}

	/**
	 * EASYPAY �� script
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- �ֹ����� : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- ��ưŸ�� ���� - default : A1
	 * DISPLAY_PROMOTION	- �̺�Ʈ ���� ��� ����
	 * DISPLAY_ELEMENT_ID	- ����� element id
	 */
	function getEasypayScript()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "EASYPAY",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_easypay'].'",
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
		';

		return $script;
	}

	/**
	 * admin page - ID validation check
	 * @param array
	 * @return string - �ʼ�������,  false - �ʼ���������������
	 * @date 2015-06-09
	 */
	function check_paycoPostData($apiStartType)
	{
		global $_POST;

		if(!$_POST['useType'])			return '�̿��� ������ ���� ������ ������ �ּ���.';
		if(!$_POST['paycoSellerKey'])	return '������ ������ �ڵ带 ������ �ּ���.';
		if(!$_POST['paycoCpId'])		return '������ ����ID�� ������ �ּ���.';
		if(!$_POST['testYn'])			return '��� ������ ������ �ּ���.';

		return false;
	}

	/**
	 * �ֹ����ɿ��� üũ
	 * @param string, int, string [type : ��ư����Ÿ�� CHECKOUT / EASYPAY],[goodsno : ��ǰ��ȣ], [isMobile : Y - ����ϼ�]
	 * @return string
	 * @date 2015-06-09
	 */
	function check_paycoOrderAble($type, $goodsno, $isMobile='')
	{
		//��뿩�� ���� üũ
		if(!$this->check_useAble($type, $isMobile)){
			$errorMsg = '��뼳���� �Ǿ����� �ʰų� �������ڵ尡 �������� �ʽ��ϴ�.';
		}
		//���ݴ�ü���� üũ
		if($this->check_strPrice($goodsno)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}
		//���üũ
		if($this->check_runout($goodsno)){
			$errorMsg = 'ǰ���� ��ǰ�� ���ԵǾ� �ֽ��ϴ�.';
		}
		//���� ī�װ� üũ
		if($this->check_exception_category($goodsno)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}
		//���� ��ǰ üũ
		if($this->check_exception_goods($goodsno)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}
		//���Ű��� ȸ���׷�, ��������, �Ǹſ���
		if($this->check_goodsOpen($goodsno, $isMobile)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}
		//���Ű��� ȸ�����
		$goodsBuyable = getGoodsBuyable($goodsno);
		if($goodsBuyable === "buyable2")
		{
			$errorMsg = "ȸ�� ���� ���� ��ǰ�Դϴ�. �α��� �� �õ����ּ���.";
		}
		else if($goodsBuyable === "buyable3") {
			$errorMsg = "Ư�� ȸ�� ���� ���� ��ǰ�Դϴ�.";
		}

		if($errorMsg) {
			return $errorMsg;
		}

		return false;
	}

	/**
	 * �����Ϸ� ���ɿ��� üũ
	 * @param string, int, string [type : ��ư����Ÿ�� CHECKOUT / EASYPAY],[goodsno : ��ǰ��ȣ], [isMobile : Y - ����ϼ�]
	 * @return string
	 * @date 2015-06-09
	 */
	function check_paycoOrderAbleComplet($type, $goodsno, $isMobile='')
	{
		//��뿩�� ���� üũ
		if(!$this->check_useAble($type, $isMobile)){
			$errorMsg = '��뼳���� �Ǿ����� �ʰų� �������ڵ尡 �������� �ʽ��ϴ�.';
		}
		//���ݴ�ü���� üũ
		if($this->check_strPrice($goodsno)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}
		//���üũ
		if($this->check_runout($goodsno)){
			$errorMsg = 'ǰ���� ��ǰ�� ���ԵǾ� �ֽ��ϴ�.';
		}
		//���� ī�װ� üũ
		if($this->check_exception_category($goodsno)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}
		//���� ��ǰ üũ
		if($this->check_exception_goods($goodsno)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}
		//���Ű��� ȸ���׷�, ��������, �Ǹſ���
		if($this->check_goodsOpen($goodsno, $isMobile)){
			$errorMsg = '������ ���񽺷� ������ �� ���� ��ǰ�� ���ԵǾ��ֽ��ϴ�.';
		}

		if($errorMsg) {
			return $errorMsg;
		}

		return false;
	}

	/**
	 * ���Ű��� ȸ���׷�, ��������, �Ǹſ���
	 * @param int ��ǰ��ȣ
	 * @return blooean true - �ֹ��Ұ��ɻ�ǰ, false - �ֹ����ɻ�ǰ
	 * @date 2015-06-09
	 */
	function check_goodsOpen($goodsno, $isMobile='')
	{
		$goodsName = rtnOpenYn($goodsno, 'D', $isMobile);

		if(count($goodsName) > 0){
			return true;
		}

		return false;
	}

	/**
	 * ��뿩�μ��� üũ
	 * @param boolean or string [type : CHECKOUT-������(üũ�ƿ�)��, EASYPAY-���������], [mobile : true or Y - ����ϼ�, false or '' - PC�� ]
	 * @return boolean true - ��밡��, false - ���Ұ���
	 * @date 2015-06-09
	 */
	function check_useAble($type, $mobile='')
	{
		global $sess, $_SERVER;

		if(!$type) return false;

		if($mobile){
			//����ϼ���뿩��
			if($this->paycoCfg['useYn'] != 'mobile' && $this->paycoCfg['useYn'] != 'all') return false;
		}
		else {
			//PC����뿩��
			if($this->paycoCfg['useYn'] != 'pc' && $this->paycoCfg['useYn'] != 'all') return false;
		}

		if($type == 'CHECKOUT'){
			//������(üũ�ƿ�) ��뿩��
			if($this->paycoCfg['useType'] != 'CE') return false;
		}
		else if($type == 'EASYPAY'){
			//������� ��뿩��
			if($this->paycoCfg['useType'] != 'CE' && $this->paycoCfg['useType'] != 'E') return false;
		}

		return true;
	}

	/**
	 * ��ư���� ������ üũ
	 * @param
	 * @return boolean true - ���Ⱑ��, false - ����Ұ���
	 * @date 2015-06-09
	 */
	function check_level($type, $sess='')
	{
		if(!$type) return false;

		//�����ڴ� �׽�Ʈ, ������� ��� ����
		if($sess['level'] > 79){
			return true;
		}

		if($this->paycoCfg['testYn'] == 'N'){
			//���������
			if($type == 'CHECKOUT'){
				//������ - ��ȸ���� ���Ű���
				if(!$sess){
					return true;
				}
			}
			else {
				//������� - ȸ��, ��ȸ�� ��������
				return true;
			}
		}

		return false;
	}

	/**
	 * ���ݴ�ü���� üũ
	 * @param
	 * @return boolean true - ���ݴ�ü����0, false - ���ݴ�ü����X
	 * @date 2015-06-09
	 */
	function check_strPrice($goodsno)
	{
		global $goodsModel;

		if(!$goodsModel){
			$goodsModel = Clib_Application::getModelClass('Goods_Goods');
			$goodsModel->load($goodsno);
		}

		if($goodsModel){
			if(strlen($goodsModel->getData('strprice')) > 0){
				return true;
			}
		}
		else {
			list($strprice) = $GLOBALS[db]->fetch("SELECT strprice FROM ".GD_GOODS." WHERE goodsno='".$goodsno."' LIMIT 1");
			if(strlen($strprice) > 0){
				return true;
			}
		}

		return false;
	}

	/**
	 * ���üũ
	 * @param
	 * @return boolean true - ǰ��O, false - ǰ��X
	 * @date 2015-06-09
	 */
	function check_runout($goodsno)
	{
		global $goodsModel;

		if(!$goodsModel){
			$goodsModel = Clib_Application::getModelClass('Goods_Goods');
			$goodsModel->load($goodsno);
		}

		if($goodsModel){
			if($goodsModel->getRunout()) return true;
		}
		else {
			list($runout, $usestock, $totstock) = $GLOBALS[db]->fetch("SELECT runout, usestock, totstock FROM ".GD_GOODS." WHERE goodsno='".$goodsno."' LIMIT 1");
			if($runout || ($usestock && $totstock < 1)) return true;
		}

		return false;
	}

	/**
	 * ���� ��ǰ üũ
	 * @param int ��ǰ��ȣ
	 * @return boolean true - ���ܻ�ǰ ����, false - ���ܻ�ǰ ������
	 * @date 2015-06-09
	 */
	function check_exception_goods($goodsno)
	{
		global $validation;

		if(!$validation){
			include_once(dirname(__FILE__) . '/validation.class.php');
			$validation = new Validation();
		}

		if($validation->check_exception_goods($goodsno, $this->paycoCfg['e_exceptions'])){
			return true;
		}

		return false;
	}

	/**
	 * ���� ī�װ� üũ
	 * @param int ��ǰ��ȣ
	 * @return boolean true - ���ܻ�ǰ ����, false - ���ܻ�ǰ ������
	 * @date 2015-06-09
	 */
	function check_exception_category($goodsno)
	{
		global $validation;

		if(!$validation){
			include_once(dirname(__FILE__) . '/validation.class.php');
			$validation = new Validation();
		}

		if($validation->check_exception_category($goodsno, $this->paycoCfg['e_category'])){
			return true;
		}

		return false;
	}

	/**
	 * ���谨
	 * @param int �ֹ���ȣ
	 * @return
	 * @date 2015-06-09
	 */
	function adjustStock($ordno)
	{
		setStock($ordno);
	}

	/**
	 * API ���
	 * @param string api Ÿ��
	 * @return array  response data  or blooean false
	 * @date 2015-06-09
	 */
	function apiExecute($apiType)
	{
		global $paycoApi, $cart, $_POST;

		if(!$paycoApi){
			$paycoApi = Core::loader('paycoApi');
		}

		$param = array();
		$param['cart']	= $cart;
		$param['post']	= $_POST;

		switch($apiType){
			case 'auth':
				if(!$this->paycoCfg['crypt_key']){
					$this->paycoCfg['crypt_key'] = $param['post']['crypt_key'];
				}
				$requestData	= $this->setAuthData($param);
			break;

			case 'reserve' :
				$requestData	= $this->setReverseData($param);
			break;

			default :
				return false;
			break;
		}

		$responseData	= $paycoApi->request($apiType, $requestData);
		$responseData	= gd_json_decode($responseData);

		return $responseData;
	}

	/**
	 * Info API data ��ȯ
	 * @param  array  param data
	 * @return array api request data
	 * @date 2015-06-09
	 */
	function setAuthData($param)
	{
		$request['crypt_key'] = $this->paycoCfg['crypt_key'];
		$request['enc'] = $this->setAuth_EncData($param);

		return $request;
	}

	/**
	 * crypt_key ����
	 * @param void
	 * @return string
	 * @date 2015-06-09
	 */
	function setAuth_secretKeyData()
	{
		return substr(md5(microtime().rand(1, 1000)), 0, 10);
	}

	/**
	 * ID���� ������ ����
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setAuth_EncData($param)
	{
		global $godo;

		if(!$godo){
			$config = Core::loader('config');
			$godo = $config->load('godo');
		}

		$postData = $param['post'];

		//������ api key
		$encData['seller_key']	= $param['post']['paycoSellerKey'];
		//������ ���� ID
		$encData['cp_id']		= $postData['paycoCpId'];
		//���θ� ������ȣ
		$encData['sno']			= $godo['sno'];
		//���θ� ������
		$encData['shop_domain']	= ProtocolPortDomain();
		//�׽�Ʈ ����
		$encData['test']		= $postData['testYn'];

		return $encData;
	}

	/**
	 * reverse API data ��ȯ
	 * @param  array  param data
	 * @return array api request data
	 * @date 2015-06-09
	 */
	function setReverseData($param)
	{
		$request					= $this->setReverse_EncData($param);
		$request['orderProducts']	= $this->setReverse_OrderItemData($param);
		$request['extraData']		= $this->setReverse_extraData($param);
		if($param['post']['paycoType'] == 'CHECKOUT'){
			$orderItemDelivery = $this->setReverse_OrderItemDeliveryData($param['post']['ordno']);
			$request['orderProducts'] = array_merge((array)$request['orderProducts'], (array)$orderItemDelivery);
		}

		return $request;
	}

	/**
	 * ����, �ֹ������� ����
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_EncData($param)
	{
		global $sess, $_SERVER, $db, $order;

		if(!is_object($order)) {
			$order = Core::loader('order');
			$order->load($param['post']['ordno']);
		}

		$inflowPath = $orderMethod = $orderChannel = '';

		//�ֹ�����
		if($param['post']['paycoType'] == 'CHECKOUT'){
			$orderMethod = $param['post']['paycoType'];
			//���԰��
			if($param['post']['paycoCheckoutType'] == 'goodsView'){
				$inflowPath = 'PRODUCT_DETAIL';
			}
			else {
				$inflowPath = 'CART';
			}
		}
		else {
			if($sess){
				$orderMethod = 'EASYPAY_F';
			}
			else {
				$orderMethod = 'EASYPAY';
			}
		}

		//�ֹ�ä�� - ����̽��߽�
		if($this->screenType == 'MOBILE' || $param['post']['isMobile'] == 'Y'){
			$orderChannel = 'MOBILE';
		}
		else {
			$orderChannel = 'PC';
		}

		$orderData = $db->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno='".$param['post']['ordno']."'");

		list($delivery_price) = $db->fetch("SELECT delivery_price FROM ".GD_ORDER_ITEM_DELIVERY." WHERE ordno='".$param['post']['ordno']."' and delivery_type='100' LIMIT 1");
		if(!$delivery_price){
			$delivery_price = 0;
		}

		//return parameter
		$returnUrlParam['ordno']	= $orderData['ordno'];			//�ֹ���ȣ
		$returnUrlParam['isMobile'] = $param['post']['isMobile'];	//����ϼ�����
		$returnUrlParam['paycoType']= $param['post']['paycoType'];	//������ Ÿ��

		//encdata
		$encData['seller_key']		= $this->paycoCfg['paycoSellerKey'];	//�������ڵ�
		$encData['orderMethod']		= $orderMethod;							//�ֹ����� (CHECKOUT=������(üũ�ƿ�) ��, EASYPAY=������� �� (��α���), EASYPAY_F=������� �� (�α���))
		$encData['orderChannel']	= $orderChannel;						//�ֹ�ä��(PC or MOBILE)
		$encData['inflowPath']		= $inflowPath;							//���԰�� - orderMethod�� CHECKOUT�� ���(PRODUCT_DETAIL=��ǰ��, CART=��ٱ���)
		$encData['returnUrl']		= ProtocolPortDomain() . $this->cfg['rootDir'] . '/order/card/payco/card_result.php'; //������������ �б�������
		if ($param['post']['paycoType'] == 'CHECKOUT' && $this->cfg['ssl'] === '1' && $this->cfg['ssl_type'] === 'godo' && $_SERVER['HTTPS'] != 'on') {
			if ($this->cfg['ssl_port'] != '' && ($this->cfg['ssl_port'] != 443)) {
				$encData['returnUrl'] = ProtocolPortDomain() . ':' . $this->cfg['ssl_port'] . $this->cfg['rootDir'] . '/order/card/payco/card_result.php';
			}
			$encData['returnUrl'] = str_replace('http://', 'https://', $encData['returnUrl']);
		}
		$encData['returnUrlParam']	= gd_json_encode($returnUrlParam);		//�����Ϸ������� return param
		$encData['ordno']			= $orderData['ordno'];					//�ֹ���ȣ
		$encData['settleprice']		= $orderData['settleprice'];			//���ʰ����ݾ�
		$encData['prn_settleprice'] = $orderData['prn_settleprice'];		//�����ݾ�(��ҹݿ�)
		$encData['goodsprice']		= $orderData['goodsprice'];				//���ʻ�ǰ�ݾ�
		$encData['delivery']		= $orderData['delivery'];				//�ѹ�ۺ�
		$encData['step']			= $orderData['step'];					//�ֹ�����
		$encData['step2']			= $orderData['step2'];					//�ֹ�����
		$encData['orddt']			= $orderData['orddt'];					//�ֹ���
		$encData['area_delivery']	= $delivery_price;						//�� ������ ��ۺ�
		$encData['totalTaxfreeAmt'] = $order->getTaxFreeAmount();			//�鼼�ݾ�(�鼼��ǰ�� ���ް��� ��)
		$encData['totalTaxableAmt'] = $order->getTaxAmount();				//�����ݾ�(������ǰ�� ���ް��� ��)
		$encData['totalVatAmt']		= $order->getVatAmount();				//�ΰ���(������ǰ�� �ΰ��� ��)

		return $encData;
	}

	/**
	 * order item ������ ����
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_OrderItemData($param)
	{
		global $db;

		$data_orderItem = array();

		$rootPath = $this->getShopRootPath();
		$mobileRootPath = $this->getMobileRootPath();
		$goodsPath = ($param['post']['isMobile']) ? $mobileRootPath . '/goods/view.php?goodsno=' : $rootPath . '/goods/goods_view.php?goodsno=';

		$result = $db->query("SELECT * FROM ".GD_ORDER_ITEM." WHERE ordno='".$param['post']['ordno']."'");

		$i = 0;
		while($orderItem = $db->fetch($result)){
			if($orderItem['goodsno']){
				$goods = $db->fetch("SELECT * FROM ".GD_GOODS." WHERE goodsno='".$orderItem['goodsno']."' LIMIT 1");
			}

			$data_orderItem[$i]['cpId']				= $this->paycoCfg['paycoCpId'];						//CPID
			$data_orderItem[$i]['productInfoUrl']	= $goodsPath . $orderItem['goodsno'];				//��ǰ����URL (��ۺ�/�����ᰡ �ƴ� ���� �ʼ�)
			$data_orderItem[$i]['orderConfirmUrl']	= $rootPath . '/goods/goods_view.php?goodsno=' . $orderItem['goodsno']; //�ֹ��Ϸ� �� �ֹ���ǰ�� Ȯ���� �� �ִ� url
			$data_orderItem[$i]['orderConfirmMobileUrl'] = $mobileRootPath . '/goods/view.php?goodsno=' . $orderItem['goodsno']; //�ֹ��Ϸ� �� �ֹ���ǰ�� Ȯ���� �� �ִ� ����� url
			if($goods['img_s']){
				$data_orderItem[$i]['productImageUrl']	= $rootPath . '/data/goods/'.$goods['img_s'];		//�̹���URL (��ۺ�/�����ᰡ �ƴ� ���� �ʼ�)
			}
			$data_orderItem[$i]['sno']				= $orderItem['sno'];								//order item sno
			$data_orderItem[$i]['ordno']			= $param['post']['ordno'];							//�ֹ���ȣ
			$data_orderItem[$i]['goodsno']			= $orderItem['goodsno'];							//��ǰ��ȣ
			$data_orderItem[$i]['goodsnm']			= iconv('euc-kr', 'utf-8', $goods['goodsnm']);		//��ǰ��
			$data_orderItem[$i]['opt1']				= iconv('euc-kr', 'utf-8', $orderItem['opt1']);		//�ɼ�1
			$data_orderItem[$i]['opt2']				= iconv('euc-kr', 'utf-8', $orderItem['opt2']);		//�ɼ�2
			$data_orderItem[$i]['addopt']			= iconv('euc-kr', 'utf-8', $orderItem['addopt']);	//�߰��ɼ�
			$data_orderItem[$i]['price']			= $orderItem['price'];								//�ǸŰ���
			$data_orderItem[$i]['ea']				= $orderItem['ea'];									//����
			$data_orderItem[$i]['istep']			= $orderItem['istep'];								//�ֹ�����
			$data_orderItem[$i]['oi_delivery_idx']	= $orderItem['oi_delivery_idx'];					//��ۺ����̺� idx
			$data_orderItem[$i]['taxationType']		= $this->getTaxationType($goods['tax']);			//����Ÿ��

			$i++;
		}

		return $data_orderItem;
	}

	/**
	 * reverse api extradata ����
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_extraData($param)
	{
		global $db;

		$goodsno = $viewPage = $rootPath = '';
		$extraData = array();

		if($param['post']['isMobile'] == 'Y' || $this->screenType == 'MOBILE'){
			if($param['post']['isMobile'] == 'Y'){
				$rootPath = $this->getMobileRootPath();
				$viewPage = 'view';
			}
			else {
				$rootPath = $this->getShopRootPath();
				$viewPage = 'goods_view';
			}

			$result = $db->query("SELECT * FROM ".GD_ORDER_ITEM." WHERE ordno='".$param['post']['ordno']."'");
			$cnt = $db->count_($result);

			while($orderItem = $db->fetch($result)){
				$goodsno = $orderItem['goodsno'];
				if($goodsno) break;
			}

			if((int)$cnt == 1){
				$cancelMobileUrl = $rootPath . '/goods/' . $viewPage . '.php?goodsno=' . $goodsno;
			}
			else {
				$cancelMobileUrl = $rootPath . '/goods/cart.php';
			}

			$extraData['cancelMobileUrl'] = $cancelMobileUrl;
		}

		return $extraData;
	}

	/**
	 * ��� ������ ����
	 * @param  int �ֹ���ȣ
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_OrderItemDeliveryData($ordno)
	{
		global $db;

		$orderItemDeliveryData = array();
		$result = $db->query("SELECT oi_delivery_idx FROM ".GD_ORDER_ITEM." WHERE ordno='".$ordno."' GROUP BY oi_delivery_idx");
		$i=0;
		while($orderItem = $db->fetch($result)){
			if($orderItem['oi_delivery_idx'] > 0) {
				$_orderItemDelivery = array();
				$_orderItemDelivery = $db->fetch("SELECT prn_delivery_price, delivery_type FROM ".GD_ORDER_ITEM_DELIVERY." WHERE oi_delivery_idx='".$orderItem['oi_delivery_idx']."' and ordno='".$ordno."' LIMIT 1");

				$orderItemDeliveryData[$i]['cpId']					= $this->paycoCfg['paycoCpId'];				// ����ID
				$orderItemDeliveryData[$i]['oi_delivery_idx']		= $orderItem['oi_delivery_idx'];			//��ۺ� ���̺� idx
				$orderItemDeliveryData[$i]['ordno']					= $ordno;									//�ֹ���ȣ
				$orderItemDeliveryData[$i]['prn_delivery_price']	= $_orderItemDelivery['prn_delivery_price'];//��ۺ�
				$orderItemDeliveryData[$i]['delivery_type']			= $_orderItemDelivery['delivery_type'];		//���Ÿ��
				$orderItemDeliveryData[$i]['taxationType']			= 'TAXATION';		//���հ���(��ۺ�� ����ó��)
			}

			$i++;
		}

		return $orderItemDeliveryData;
	}

	/**
	 * �������ֹ���ȣ ����
	 * @param  int,string �ֹ���ȣ, �˾�url
	 * @return
	 * @date 2015-06-09
	 */
	function saveReserveOrderNo($ordno, $returnUrl)
	{
		global $db;

		$returnUrlArr = explode("/", $returnUrl);
		$payco_reserve_order_no = array_pop($returnUrlArr);

		if($payco_reserve_order_no){
			$result = $db->query("UPDATE ".GD_ORDER." SET payco_reserve_order_no='".$payco_reserve_order_no."' WHERE ordno='".$ordno."'");
		}

		if(!$payco_reserve_order_no || !$result) msg("������ �ֹ���ȣ�� ã�� �� �����ϴ�.", -1);
	}

	/*
	 * ������������ ��ư�̹���
	 * @param  string �̹�����, �̹���Ȯ����
	 * @return string url
	 * @date 2015-06-09
	 */
	function getAdminBtnImageUrl($name, $type)
	{
		return $this->buttonImgUrl . $name . '.' . $type;
	}

	/**
	 * ������ ������ ����
	 * @param  string page url
	 * @return
	 * @date 2015-06-09
	 */
	function locateSettlePopupPage($url, $isMobile='')
	{
		if($this->screenType == 'MOBILE' || $isMobile == 'Y'){
			echo '
			<script type="text/javascript">
				parent.window.location.replace("'.$url.'");
			</script>
			';
		}
		else {
			echo '
			<script type="text/javascript">
				window.location.replace("'.$url.'");
			</script>
			';
		}
		exit;
	}

	/*
	������ ������� ���� ���ó��
	*/
	function paycoCancel($arr_data, $cancel_sno='', $part='N', $msg=array(), $payco_coupon_data=array())
	{
		global $db;
		//pgcancel	pg��� ����( r = �κ����, n = ��Ҿ���, y=��ҿϷ� )

		$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
		$msg[] = '-----------------------------------';
		$msg[] = '��ұݾ�	: '.number_format($arr_data['cancelTotalAmt']).'��';
		$msg[] = 'ȯ�Ҽ�����	: '.number_format($arr_data['cancelTotalFeeAmt']).'��';
		$msg[] = '������ұݾ�	: '.number_format($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt']).'��';
		$msg[] = '-----------------------------------';

		if($part == 'Y') {//�κ����
			$order_query = "update ".GD_ORDER." set ";
			$order_query .= " pgcancel = 'r' , repayprice = ifnull(repayprice, 0) + ".$arr_data['cancelTotalAmt'];
			$order_query .= " , settlelog=concat(ifnull(settlelog,''),'\n\n".implode("\n",$msg)."') ";
			if($payco_coupon_data['payco_firsthand_refund'] === 'Y') $order_query .= " , payco_firsthand_refund='Y' ";
			if($payco_coupon_data['payco_coupon_repay'] === 'Y') $order_query .= " , payco_coupon_repay='Y' ";
			$order_query .= " where ordno='".$arr_data['ordno']."'";

			$db->query($order_query);

			$query2 = "update ".GD_ORDER_CANCEL." set rprice = '".$arr_data['cancelTotalAmt']."', rfee = '".$arr_data['cancelTotalFeeAmt']."', ccdt = '".date('Y:m:d H:i:s')."', pgcancel = 'r' where sno = '".$cancel_sno."'";
			$db->query($query2);

		}
		else {//��ü���
			$db -> query("update ".GD_ORDER." set pgcancel='y', repayprice = repayprice + ".$arr_data['cancelTotalAmt']." , settlelog=concat(ifnull(settlelog,''),'\n\n".implode("\n",$msg)."') where ordno='".$arr_data['ordno']."'");
			$db -> query("update ".GD_ORDER_CANCEL." set rprice = '".$arr_data['cancelTotalAmt']."', rfee = '".$arr_data['cancelTotalFeeAmt']."', ccdt = '".date('Y:m:d H:i:s')."', pgcancel='y' where ordno='".$arr_data['ordno']."'");
		}
	}

	/*
	������ ������� ���� �α�����
	*/
	function paycoCancelFailLog($ordno, $msg=Array())
	{
		if(empty($msg) === false) {
			global $db;
			$db -> query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'"."\n\n".implode("\n",$msg)."') where ordno='".$ordno."'");
		}
	}


	/**
	 * ��ũ��Ÿ�� - checkout ���
	 * @param
	 * @return
	 * @date 2015-06-09
	 */
	function getScreenType()
	{
		$screenType = 'PC';
		if(is_file(dirname(__FILE__) . '/../lib/nScreenPayment.php')) $nScreenPayment = Core::Loader('nScreenPayment');

		if($nScreenPayment){
			$screenType = $nScreenPayment->getScreenType();
		}
		else {
			$mobileAgentArr = array(
				'iPhone',
				'Mobile',
				'UP.Browser',
				'Android',
				'BlackBerry',
				'Windows CE',
				'Nokia',
				'webOS',
				'Opera Mini',
				'SonyEricsson',
				'opera mobi',
				'Windows Phone',
				'IEMobile',
				'POLARIS',
				'lgtelecom',
				'NATEBrowser',
			);

			foreach($mobileAgentArr as $mobileAgent) {
				if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($mobileAgent))) {
					$screenType = 'MOBILE';
					break;
				}
			}
		}

		return $screenType;
	}


	/**
	 * ������ ���� ��ȿ��üũ ��ȯ
	 * @param
	 * @return  $responseData �� ���Ұ� �׸� ��ȯ��
	 * @date 2015-06-09
	 */
	function setAdminValidateCheck($responseData)
	{
		$returnData = '';
		$_returnData = array();

		//���Ұ� �������ڵ�
		if(preg_match('/seller\_key/', $responseData)){
			$_returnData[] = 'seller_key@N'; //���Ұ� ����
			$_returnData[] = 'cp_id@'; //�޽��� �̳���
		}

		//���Ұ� ����ID
		if(preg_match('/cp\_id/', $responseData)){
			$_returnData[] = 'seller_key@Y'; //��밡�� ����
			$_returnData[] = 'cp_id@N'; //���Ұ� ����
		}

		$returnData = @implode("^", $_returnData);

		return $returnData;
	}

	/**
	 * admin page indb return msg
	 * @param string
	 * @return
	 * @date 2015-06-09
	 */
	function returnAdminMsg($result, $msg)
	{
		echo $result . '|' . $msg;
		exit;
	}

	/**
	 * get mobile root path
	 * @param
	 * @return string
	 * @date 2015-06-09
	 */
	function getMobileRootPath()
	{
		return ProtocolPortDomain() . $this->cfgMobileShop['mobileShopRootDir'];
	}

	/**
	 * get pc shop root path
	 * @param
	 * @return string
	 * @date 2015-06-09
	 */
	function getShopRootPath()
	{
		return ProtocolPortDomain() . $this->cfg['rootDir'];
	}

	/*
	 * �ֹ� ��ҹ�ȣ ��ȸ
	 * $ordno int �ֹ���ȣ
	 * $cancel_no int ������ҹ�ȣ
	*/
	function getOrderCancelNo($ordno, $cancel_no)
	{
		global $db;

		$cancel_no_query = $db->_query_print("SELECT i.cancel FROM ".GD_ORDER_ITEM." i LEFT JOIN ".GD_ORDER_CANCEL." c ON i.cancel=c.sno WHERE i.ordno=[s] ", $ordno);
		if($cancel_no) $cancel_no_query .= $db->_query_print(" AND NOT i.cancel=[i]", $cancel_no);
		$cancel_no_query .= $db->_query_print(" AND i.cancel > 0 AND (c.pgcancel='r' OR c.pgcancel='y') group by i.cancel");

		$cancel_no = $db->_select($cancel_no_query);
		return $cancel_no;
	}

	/**
	 * ���˾� ����
	 * @param
	 * @return
	 * @date 2015-06-09
	 */
	function getVoidPopupOpenScript()
	{
		$script = '
			var bodyW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
			var bodyH = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
			var posX = (bodyW - 635) / 2;
			var posY = (bodyH - 546) / 2;

			paycoPopup = window.open("about:blank", "paycoPopup", "width=635px, height=546px, top="+posY+"px, left="+posX+"px ");
			window.name = "payco_parent";
			if(paycoPopup) paycoPopup.focus();
		';

		return $script;
	}

	/**
	 * ������ ����Ÿ�� ��ȯ
	 * @param string
	 * @return string
	 * @date 2015-06-09
	 */
	function getPaycoSettleType($type)
	{
		$paycoSettleType = '';
		$paycoSettleType = (strtoupper($type) == 'CHECKOUT') ? '������ ������' : '������ �������';

		return $paycoSettleType;
	}

	/**
	 * qrTmp ��ȯ
	 * @param string
	 * @return string
	 * @date 2015-06-09
	 */
	function getqrTmp($paycoType)
	{
		$payco_settle_type = $returnField = '';
		if(strtoupper($paycoType) == 'CHECKOUT'){
			$payco_settle_type = 'CHECKOUT';
		}
		else {
			$payco_settle_type = 'EASYPAY';
		}

		$returnField =  "pg='payco', settleInflow='payco', payco_settle_type='".$payco_settle_type."',";

		return $returnField;
	}

	function getOrderData($ordno, $table, $query_type, $arr_coulms=Array(), $desc='')
	{
		global $db;

		if(empty($arr_coulms)) $coulms = '*';
		else $coulms = implode(', ', $arr_coulms);

		$query = $db->_query_print("SELECT ".$coulms." FROM ".$table." WHERE ordno=[i] ".$desc, $ordno);

		switch($query_type) {
			case 'fetch_true' :
				$order_data = $db->fetch($query, true);
				break;
			case 'fetch_false' :
				$order_data = $db->fetch($query);
				break;
			case 'query' :
				$order_data = $db->query($query);
				break;
		}
		return $order_data;
	}

	/**
	 * ���˾� �ݱ�
	 * @param
	 * @return
	 * @date 2015-06-09
	 */
	function closeVoidPopup()
	{
		echo '
		<script type="text/javascript">
		if(window.parent.paycoPopup){
			window.parent.paycoPopup.close();
		}
		</script>
		';
	}

	/**
	 * msg alert
	 * @param string �޽���, ������Ÿ��, ����Ͽ���, ������Ÿ��
	 * @return
	 * @date 2015-06-09
	 */
	function msgLocate($msg, $paycoType, $isMobile='', $pageType = '')
	{
		$secondFactor = $thirdFactor = '';
		if($paycoType == 'CHECKOUT'){
			if($this->screenType == 'MOBILE') {
				//������ ����� ����̽�
				$secondFactor = -1;
			}
			else {
				//������ PC ����̽�
				$secondFactor = 'close';
			}
		}
		else {
			if($isMobile == 'Y'){
				//������� ����ϼ�
				$secondFactor = '../../../..'.$this->cfgMobileShop['mobileShopRootDir'].'/ord/order.php';
			}
			else {
				//������� PC��

				$secondFactor = '../../../..'.$this->cfg['rootDir'].'/order/order.php';
				if($this->screenType != 'MOBILE') {
					switch($pageType){
						case 'card_gate':
							msg($msg);
							echo "
							<script type='text/javascript'>
							opener.location.replace('".$secondFactor."');
							window.close();
							</script>
							";
							exit;
						break;

						default:
							$this->closeVoidPopup();
						break;
					}
				}
			}
			if(!$thirdFactor) $thirdFactor = 'parent';
		}

		msg($msg, $secondFactor, $thirdFactor);
		exit;
	}

	/**
	 * ������ ��ȿ�� üũ (��������� �� ���������� üũ)
	 * @param  array
	 * @return boolean  true - ��밡��, false - ���Ұ�
	 * @date 2015-06-09
	 */
	function checkEmoney($orderData)
	{
		global $db;

		$memberEmoney = 0;

		$res = $db->query("SELECT emoney FROM ".GD_MEMBER." WHERE m_no='".$orderData['m_no']."' LIMIT 1");
		list($memberEmoney) = $db->fetch($res);

		if ($orderData['emoney'] <= $memberEmoney) return true;

		return false;
	}

	/**
	 * ������ ��һ��� üũ
	 * @param  integer
	 * @return boolean  true - ������ ��ҿϷ�, false - ��Ұ���
	 * @date 2015-06-09
	 */
	function checkCancelYn($cancel_sno)
	{
		if($cancel_sno) {
			global $db;
			$res = $db->query("SELECT pgcancel FROM ".GD_ORDER_CANCEL." WHERE sno='".$cancel_sno."'");
			list($pgcancel) = $db->fetch($res);
			if($pgcancel == 'n') return false;
			else return true;
		}
	}

	/**
	 * ������ ������ ���̹� ���ϸ���&ĳ�� ���Ұ�
	 * @param  string, integer
	 * @return boolean  true - ���̹� ���ϸ���&ĳ�� �õ���, false - ���̹� ���ϸ���&ĳ�� ������
	 * @date 2015-06-09
	 */
	function checkNcash($useyn, $totalUseAmount)
	{
		if($useyn == 'Y' && $totalUseAmount > 0){
			return true;
		}

		return false;
	}

	/**
	 * �鼼, ����, ���� ����
	 * @param  inteager
	 * @return
	 * @date 2015-06-09
	 */
	function getTaxationType($tax)
	{
		$returnTaxation = '';
		switch((int)$tax){
			case 0:
				$returnTaxation = 'DUTYFREE';
			break;

			case 1: default:
				$returnTaxation = 'TAXATION';
			break;
		}

		return $returnTaxation;
	}

	/**
	 * ��ҵ� �ֹ���ǰ��ȣ ��ȸ
	 * @param  str, int, int, array
	 * @return
	 * @date 2015-06-09
	 */
	function getCancelItem($payco_settle_type, $ordno, $sno, $item_data)
	{
		global $db;

		if(strtoupper($payco_settle_type) != 'CHECKOUT') {//��������� ��� ��� item�� ��ҵǾ����� ���º��� ����

			$item_query = "SELECT * FROM ".GD_ORDER_ITEM." WHERE ordno='".$ordno."' group by opt1, opt2 order by sno desc ";
			$order_res = $db->_select($item_query, true);

			foreach($order_res as $arr_item) {
				if($arr_item['cancel'] < 1) {//��������� ���� ���� item�� �ִ� ��� ���º��� ���� ����
					$cancel_item_sno = Array();
					break;
				}

				if($sno != $arr_item['cancel']) {//���� ��Ұ��� �ƴ� ��Ұ� �� ���ó������ ���� �ֹ��� �ִ� ��� ���º��� ���� ����
					$cancel_query = "SELECT pgcancel FROM ".GD_ORDER_CANCEL." WHERE sno='".$arr_item['cancel']."'";
					$cancel_res = $db->fetch($cancel_query, true);
					if($cancel_res['pgcancel'] === 'n') {
						$cancel_item_sno = Array();
						break;
					}
				}

				$cancel_item_sno[] = $arr_item['sno'];//������� �ֹ������ ��� ��� item�� sno�� ��ȯ�Ѵ�
			}
		}
		else {// �������϶� ����ϴ� item���� ��ҵǾ����� üũ
			// ������ �ֹ����º��� (���)
			foreach($item_data as $arr_item) {
				if(!$arr_item['delivery']) {
					//item�� ��� ������ ��ҵǴ��� Ȯ��
					$item_query = "SELECT goodsno, opt1, opt2, addopt FROM ".GD_ORDER_ITEM." WHERE sno='".$arr_item['sno']."'";
					$order_res = $db->fetch($item_query, true);

					$cancel_query = 'SELECT sum(ea) as ea from '.GD_ORDER_ITEM.' i LEFT JOIN '.GD_ORDER_CANCEL.' c ON i.cancel=c.sno WHERE i.ordno='.$ordno.' AND i.goodsno='.$order_res['goodsno'].' AND i.opt1="'.$order_res['opt1'].'" AND i.opt2="'.$order_res['opt2'].'" AND i.addopt="'.$order_res['addopt'].'" AND i.istep<42 AND NOT cancel=0 AND (c.pgcancel="n" OR c.pgcancel="p")';
					$cancel_res = $db->fetch($cancel_query);

					$none_cancel_query = 'SELECT sum(ea) as ea from '.GD_ORDER_ITEM.' WHERE sno='.$arr_item['sno'].' AND goodsno='.$order_res['goodsno'].' AND opt1="'.$order_res['opt1'].'" AND opt2="'.$order_res['opt2'].'" AND addopt="'.$order_res['addopt'].'" AND cancel=0';
					$none_cancel_res = $db->fetch($none_cancel_query);
					$ea = $cancel_res['ea'] + $none_cancel_res['ea'];

					// item�� ��� ������ ��ҵǴ� ��쿡�� ��һ��·� ������
					if($arr_item['ea'] == $ea) {
						$cancel_item_sno[] = $arr_item['sno'];
					}
				}
			}
		}

		return $cancel_item_sno;
	}

	/**
	 * ������ �����ݾ�
	 * @param  array
	 * @return int
	 * @date 2015-06-09
	 */
	function getPaycoCouponAmount($data)
	{
		$paycoCouponAmount = 0;
		if($data['payco_coupon_use_yn'] == 'Y' && $data['payco_coupon_repay']){
			$paycoCouponAmount = $data['payco_coupon_price'];
		}

		return $paycoCouponAmount;
	}
}
?>