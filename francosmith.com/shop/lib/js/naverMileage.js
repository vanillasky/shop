// �Ķ���� Parsing
var GET = (function(){
	var
	scriptParam = new Object(),
	scriptTag = document.getElementById("naver-mileage-script"),
	queryIndex = scriptTag.src.indexOf("?");
	if (queryIndex > -1) {
		var scriptTagParam = scriptTag.src.substr(scriptTag.src.indexOf("?")+1).split(/&/gi);
		for (var a = 0; a<scriptTagParam.length; a++) {
			var
			splitIndex = scriptTagParam[a].indexOf("="),
			paramName = (splitIndex<0)?scriptTagParam[a]:scriptTagParam[a].substr(0, splitIndex),
			paramValue = (splitIndex<0)?null:scriptTagParam[a].substr(splitIndex+1);
			if (paramName.match(/(.+)\[(.*)\]$/)) {
				if (RegExp.$2 && RegExp.$2.trim().length > 0) {
					if (scriptParam[RegExp.$1] === undefined) scriptParam[RegExp.$1] = new Object();
					scriptParam[RegExp.$1][RegExp.$2] = paramValue;
				}
				else {
					if (scriptParam[RegExp.$1] === undefined) scriptParam[RegExp.$1] = new Array(paramValue);
					else scriptParam[RegExp.$1].push(paramValue);
				}
			}
			else {
				scriptParam[paramName] = paramValue;
			}
		}
	}
	return scriptParam;
})();

var NaverMileage = new function()
{

	var self = this;

	self.initExecCount = 0;
	self.elementRegisted = false;
	self.moduleLoaded = false;
	self.maxUseAmount = 0;
	self.enabled = undefined;

	/*
	 * @name registElement
	 * @return void
	 * @description ������ �ε� ���� ���̹� ���ϸ��� ���� ������Ʈ�� �޸𸮿� ����
	 */
	self.registElement = function()
	{
		self.reqTxid = document.getElementById("reqTxId"+GET.ApiId);
		self.mileageUseAmount = document.getElementById("mileageUseAmount"+GET.ApiId);
		self.cashUseAmount = document.getElementById("cashUseAmount"+GET.ApiId);
		self.totalUseAmount = document.getElementById("totalUseAmount"+GET.ApiId);
		self.baseAccumRate = document.getElementById("baseAccumRate");
		self.addAccumRate = document.getElementById("addAccumRate");
		self.orderSettlePrice = document.getElementById("paper_settlement");
		self.elementRegisted = true;
	};

	/*
	 * @name initializeStatus
	 * @return void
	 * @description ����� ���̹� ���ϸ��� ������Ʈ���� ���� �ʱ�ȭ
	 */
	self.initializeStatus = function()
	{
		self.reqTxid.value = "";
		self.mileageUseAmount.value = 0;
		self.cashUseAmount.value = 0;
		self.totalUseAmount.value = 0;
		self.baseAccumRate.value = "";
		self.addAccumRate.value = "";
	};

	/*
	 * @name isUsed
	 * @return boolean ���̹� ���ϸ����� ����/����Ͽ��ٸ� true �ƴϸ� false
	 * @description ���̹� ���ϸ����� ����/����Ͽ����� ���θ� ��ȯ
	 */
	self.isUsed = function()
	{
		if (this.reqTxid.value.trim().length) return true;
		else return false;
	};

	/*
	 * @name useMileageCash
	 * @param rsd {
	 *   resultCode : ����ڵ�(OK, CANCEL, ERROR),
	 *   reqTxId : �������� API���� ���� ��ûƮ����� ���̵�,
	 *   baseAccumRate : �⺻������,
	 *   addAccumRate : �߰�������,
	 *   mileageUseAmount : ���ϸ������ݾ�,
	 *   cashUseAmount : ĳ�����ݾ�,
	 *   totalUseAmount : ��ü ���ݾ�(���ϸ������ݾ� + ĳ�����ݾ�),
	 *   balanceAmount : ������� ���ϸ��� �ܾ�,
	 *   sig : sig ���� ������ �Ķ���� ���� ������ ����Ű�� ����Ű�� ����Ͽ� ������ HMAC-SHA1 Signature
	 * }
	 * @return void
	 * @description ����/���� ���̹� ���ϸ����� �ֹ����� ����
	 */
	self.useMileageCash = function(rsd)
	{
		self.reqTxid.value = rsd.reqTxId;
		self.mileageUseAmount.value = rsd.mileageUseAmount;
		self.cashUseAmount.value = rsd.cashUseAmount;
		self.totalUseAmount.value = rsd.totalUseAmount;
		self.baseAccumRate.value = rsd.baseAccumRate;
		self.addAccumRate.value = rsd.addAccumRate;

		if (GET.SaveMode === "ncash") {
			document.getElementsByName("save_mode").item(0).value = "ncash";
		}

		calcu_settle();
	};

	/*
	 * @name cancelMileageCash
	 * @return void
	 * @description ����/���� ���̹� ���ϸ����� �ֹ����󿡼� ����
	 */
	self.cancelMileageCash = function()
	{
		self.initializeStatus();
		if (self.enabled === true) self.disable();
		calcu_settle();
	};

	/*
	 * @name getSaveMode
	 * @return string "ncash"�� ���̹� ���ϸ����� ����, "both"�� ���̹� ���ϸ����� ���θ� ������ ��� ����, "unused"�� ���̹� ���ϸ����� ���θ� ������ ��� �������� ����, ""�� ���θ� ���������� ����
	 * @description �������� �����Ǵ� ��ġ�� ��ȯ
	 */
	self.getSaveMode = function()
	{
		var saveMode = document.getElementsByName("save_mode");
		if (saveMode.length) {
			for (var index = 0; index < saveMode.length; index++) {
				if (saveMode[index].getAttribute("type")=="radio") {
					if (saveMode[index].checked) return saveMode[index].value;
				}
				else {
					return saveMode[index].value;
				}
			}
			return null;
		}
		else {
			return saveMode.value;
		}
	};

	/*
	 * @name enable
	 * @return void
	 * @description ���̹� ���ϸ��� ����� Ȱ��ȭ
	 */
	self.enable = function()
	{
		self.enabled = true;
		if (GET.SaveMode === "choice" && self.getSaveMode() === "ncash") {
			self.reqTxid.setAttribute("required", "required");
			self.reqTxid.setAttribute("msgR", "���̹� ���ϸ����� ���� �Ǵ� ����Ͽ� �ֽñ� �ٶ��ϴ�.");
			nbp.mileage.active();
		}
	};

	/*
	 * @name disable
	 * @return void
	 * @description ���̹� ���ϸ��� ����� ��Ȱ��ȭ ��Ű��, �ֹ����� ����� ���ϸ��� ������¸� �ʱ�ȭ
	 */
	self.disable = function()
	{
		self.initializeStatus();
		self.enabled = false;
		if (GET.SaveMode === "choice") {
			self.reqTxid.removeAttribute("required");
			self.reqTxid.removeAttribute("msgR");
			nbp.mileage.inactive();
		}
	};

	/*
	 * @name getMaxUseAmount
	 * @return int ���̹� ���ϸ����� �ִ� ���ݾ�
	 * @description ���̹� ���ϸ��� ��⿡�� ����� �� �ִ� ���ϸ���, ĳ���� �ִ� ���ݾ��� ��ȯ
	 */
	self.getMaxUseAmount = function()
	{
		return parseInt(self.maxUseAmount);
	};

	/*
	 * @name setMaxUseAmount
	 * @param int maxUseAmount ���̹� ���ϸ����� �ִ� ���ݾ�
	 * @return void
	 * @description ���̹� ���ϸ��� ��⿡�� ����� �� �ִ� ���ϸ���, ĳ���� �ִ���ݾ��� ����
	 */
	self.setMaxUseAmount = function(maxUseAmount)
	{
		self.maxUseAmount = maxUseAmount;
		if (self.elementRegisted) {
			if (self.moduleLoaded === true) {
				nbp.mileage.setMaxUseAmount(maxUseAmount);
			}
		}
	};

	/*
	 * @name getTotalUseAmount
	 * @return int totalUseAmount
	 * @description �ֹ����� ����� ���̹� ���ϸ����� ĳ���� ������ ��ȯ
	 */
	self.getTotalUseAmount = function()
	{
		if (self.elementRegisted) {
			var totalUseAmount = parseInt(self.totalUseAmount.value);
			if (isNaN(totalUseAmount)) totalUseAmount = 0;
			return totalUseAmount;
		}
		else {
			return 0;
		}
	};

	/*
	 * @name loadModule
	 * @return void
	 * @description ���̹� ���ϸ��� ����� �ֹ����� �ε���
	 */
	self.loadModule = function()
	{
		self.moduleLoaded = nbp.mileage.initWithWcs({
			"sId" : GET.ElId,
			"sApiId" : GET.ApiId,
			"sDoneUrl" : GET.DoneUrl,
			"nMaxUseAmount" : self.getMaxUseAmount(),
			"sSig" : GET.Signature,
			"nTimestamp" : parseInt(GET.Timestamp),
			"nBaseAccumRate" : parseFloat(GET.BaseAccumRate),
			"event" : {
				"accum" : function(rsd)
				{
					if (rsd.resultCode === "OK") self.useMileageCash(rsd);
					else if (rsd.resultCode === "CANCEL") self.cancelMileageCash();
				},
				"beforeAccum" : function(rsd)
				{
					if (rsd.bActive === false) {
						if (rsd.nMaxUseAmount > 0) {
							alert("������ ���� ��ġ�� ���̹� ���ϸ����� �����Ͽ��ֽñ� �ٶ��ϴ�.");
						}
						else {
							alert("���̹� ���ϸ����� ������ �� �ִ� �ݾ��� �����ϴ�.\r\n(��ۺ� ������ �ݾ׸� ���̹� ���ϸ����� �������� �մϴ�.)");
						}
					}

					if(self.checkSettlekind() === false){
						self.cancelSettlekind();
						return false;	
					}
				}
			}
		});
	};

	/*
	 * @name load
	 * @return void
	 * @description �ֹ����� ��ۺ� �ε�� �������� ��ٸ� �� ���̹� ���ϸ�������� �غ�����ְ� ������ ������ġ ������ ���� ����� �ʱ�ȭ
	 */
	self.load = function()
	{
		self.registElement();
		
		/*
		 * ��ۺ��� ��� ��ũ��Ʈ������ ����ϱ� ������ �ε尡 �Ϸ�ɶ����� üũ�ؾ���.
		 * ��ۺ� ��ũ��Ʈ�� ���ð��� 3�ʸ� �ʰ��ϸ� ����ó��.
		 */
		if (self.initExecCount>30) {
			alert("���̹� ���ϸ��� �ε忡 �����Ͽ����ϴ�.\r\n�ֹ����� ���ΰ�ħ �Ͽ��ֽñ� �ٶ��ϴ�.");
			return false;
		}
		self.initExecCount++;
		if (_ID("paper_delivery").innerHTML.trim().length<1) return setTimeout(self.load, 100);

		var naverMileageAccum = document.createElement("div");
		naverMileageAccum.id = "_mileage_acc";
		document.getElementById("naver-mileage-container").appendChild(naverMileageAccum);

		self.loadModule();

		if (GET.SaveMode === "choice") self.disable();
		else self.enable();

		calcu_settle();
	};

	self.checkSettlekind = function ()
	{
		var settlekindObj = document.getElementsByName("settlekind");
		var settlekind = '';
		for(var i=0; i<settlekindObj.length; i++){
			if(settlekindObj[i].checked === true){
				settlekind = settlekindObj[i].value;
				break;
			}
		}

		if (GET.SaveMode === 'choice') {
			if(document.getElementById("save-mode-ncash").checked == true && settlekind == 't') {
				return false;
			}
		}
		else {
			if(settlekind == 't') return false;
		}
		return true;
	};

	self.cancelSettlekind = function ()
	{
		if (GET.SaveMode === 'choice') {
			document.getElementById("save-mode-default").checked = true;			
		}
		else {
			nbp.mileage.inactive();
			nbp.mileage.active();
		}
		self.cancelMileageCash();

		alert('PAYCO ���������� ���̹� ���ϸ��� �� ĳ�� ����� �Ұ��մϴ�.');
	}
};

(function(NMSL){
	if (document.attachEvent) window.attachEvent("onload", NMSL);
	else window.addEventListener("load", NMSL, false);
})
(function(){
	if (GET.Controller === "ord" && GET.Action === "order.php") {
		NaverMileage.load();
		if (GET.SaveMode === "choice") {
			document.getElementById("save-mode-default").onclick = function()
			{
				if(NaverMileage.checkSettlekind() === false){
					NaverMileage.cancelSettlekind();
				}
				if (NaverMileage.isUsed()) {
					if (this.checked && confirm("���θ� �������� �����Ͻø� ���̹� ���ϸ�����\r\n��� �� ������ ��ҵ˴ϴ�.\r\n����Ͻðڽ��ϱ�?")) {
						NaverMileage.disable();
						calcu_settle();
					}
					else {
						document.getElementById("save-mode-ncash").checked = true;
					}
				}
				else {
					NaverMileage.disable();
					calcu_settle();
				}
			};
			document.getElementById("save-mode-ncash").onclick = function()
			{
				if(NaverMileage.checkSettlekind() === false){
					NaverMileage.cancelSettlekind();
				}
				NaverMileage.enable();
				calcu_settle();
			};
		}
		if(document.getElementsByName("settlekind").length > 0){
			for(var i=0; i<document.getElementsByName("settlekind").length; i++){
				document.getElementsByName("settlekind")[i].onclick = function ()
				{
					if(NaverMileage.reqTxid.value){
						if(NaverMileage.checkSettlekind() === false){
							NaverMileage.cancelSettlekind();
						}
					}
					
				};
			}
		}	
	}
});