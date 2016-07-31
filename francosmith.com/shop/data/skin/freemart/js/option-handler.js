/**
 * 
 */


function GMultiOption(isSoldout) {
	
	this.size = function(e) {
		var cnt = 0;
		var type = '';
		for (var i in e) {
			cnt++;
		}
		return cnt;
	}
				
	//alert(isSoldout);
	
	this._soldout = isSoldout;
	this.data = [];
	this.data_size = 0;
	this._optJoin = 
		function(opt) {
			var a = [];
			for (var i=0,m=opt.length;i<m ;i++) {
				if (typeof opt[i] != 'undefined' && opt[i] != '') {
					a.push(opt[i]);
				}
			}
				
			return a.join(' / ');
				
		};
	
	
	this.getFieldTag = 
		function (name, value) {
			var el = document.createElement('input');
			el.type = "hidden";
			el.name = name;
			el.value = value;
			return el;
		};
		
	this.clearField = 
		function() {
			var form = document.getElementsByName('frmView')[0];
			var el;
			
			for (var i=0,m=form.elements.length;i<m ;i++) {
				el = form.elements[i];
				if (typeof el == 'undefined' || el.tagName == "FIELDSET") continue;
				if (/^multi\_.+/.test(el.name)) {
					el.parentNode.removeChild(el);
					i--;
				}
			}
		};
		
	this.addField =  
		function(obj, idx) {
			var _tag;
			var form = document.getElementsByName('frmView')[0];
		
			for(var k in obj) {
				if (typeof obj[k] == 'undefined' || typeof obj[k] == 'function' || (k != 'opt' && k != 'addopt' && k != 'ea' && k != 'addopt_inputable')) continue;
				
				switch (k)
				{
					case 'ea':
						_tag = this.getFieldTag('multi_'+ k +'['+idx+']', obj[k]);
						form.appendChild(_tag);
						break;
					case 'addopt_inputable':
					case 'opt':
					case 'addopt':
						//hasOwnProperty
						for(var k2 in obj[k]) {
							if (typeof obj[k][k2] == 'function') continue;
							_tag = this.getFieldTag('multi_'+ k +'['+idx+'][]', obj[k][k2]);
							form.appendChild(_tag);
						}
						break;
					default :
						continue;
					break;
				}
			}
		};
		
		
	this.set = 
		function() {
			var add = true;
			// ���� �ɼ�
			var opt = document.getElementsByName('opt[]');
			for (var i=0,m=opt.length;i<m ;i++ )
			{
				if (typeof(opt[i])!="undefined") {
					if (opt[i].value == '') add = false;
				}
			}
			
			// �߰� �ɼ�?
			var addopt = document.getElementsByName('addopt[]');
			for (var i=0,m=addopt.length;i<m ;i++ )
			{
				if (typeof(addopt[i])!="undefined") {
					if (addopt[i].value == '' /*&& addopt[i].getAttribute('required') != null*/) add = false;
				}
			}
			
			// �Է� �ɼ��� �̰����� üũ ���� �ʴ´�.
			if (add == true) {
				this.add();
			}
		};
		
	this.del =
		function(key) {
			this.data[key] = null;
			var tr = document.getElementById(key);
			tr.parentNode.removeChild(tr);
			this.data_size--;
			// �� �ݾ�
			this.totPrice();
			if (this.data_size == 0) {
				document.getElementById("el-multi-option-display").style.display = 'none';
			}
		};
		
	this.add = function() {
			var self = this;
			if (self._soldout) {
				alert("ǰ���� ��ǰ�Դϴ�.");
				return;
			}
			
			var form = document.frmView;
			if(!(form.ea.value>0))
			{
				alert("���ż����� 1�� �̻� �����մϴ�");
				return;
			}
			else
			{
				try
				{
					var step = form.ea.getAttribute('step');
					if (form.ea.value % step > 0) {
						alert('���ż����� '+ step +'�� �����θ� �����մϴ�.');
						return;
					}
				}
				catch (e)
				{}
			}
			
			if (chkGoodsForm(form)) {
				var _data = {};
			
				_data.ea = document.frmView.ea.value;
				_data.sales_unit = document.frmView.ea.getAttribute('step') || 1;
				_data.opt = new Array;
				_data.addopt = new Array;
				_data.addopt_inputable = new Array;
			
				// �⺻ �ɼ�
				var opt = document.getElementsByName('opt[]');
			
				if (opt.length > 0) {
					_data.opt[0] = opt[0].value;
					_data.opt[1] = '';
					if (typeof(opt[1]) != "undefined") _data.opt[1] = opt[1].value;
			
					var key = _data.opt[0] + (_data.opt[1] != '' ? '|' + _data.opt[1] : '');
			
					// ����
					if (opt[0].selectedIndex == 0) key = fkey;
					
					key = self.get_key(key);	// get_js_compatible_key ����
		
					if (typeof(price[key])!="undefined"){
						_data.price = price[key];
						_data.reserve = reserve[key];
						_data.consumer = consumer[key];
						_data.realprice = realprice[key];
						_data.couponprice = couponprice[key];
						_data.coupon = coupon[key];
						_data.cemoney = cemoney[key];
						_data.memberdc = memberdc[key];
						_data.special_discount_amount = special_discount_amount[key];

					}
					else {
						// @todo : �޽��� ����
						alert('�߰��� �� ����.');
						return;
					}
	
				}
				else {
					// �ɼ��� ���� ���(or �߰� �ɼǸ� �ִ� ���) �̹Ƿ� ��Ƽ �ɼ� ������ �Ұ�.
					return;
				}
			
				// �߰� �ɼ�
				var addopt = document.getElementsByName('addopt[]');
				for (var i=0,m=addopt.length;i<m ;i++ ) {

					if (typeof addopt[i] == 'object') {
						_data.addopt.push(addopt[i].value);
					}

				}
			
				// �Է� �ɼ�
				var addopt_inputable = document.getElementsByName('addopt_inputable[]');
				for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

					if (typeof addopt_inputable[i] == 'object') {
						var v = addopt_inputable[i].value.trim();
						if (v) {
							var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
							tmp[2] = v;
							_data.addopt_inputable.push(tmp.join('^'));
						}

						// �ʵ尪 �ʱ�ȭ
						addopt_inputable[i].value = '';

					}

				}
			
				// �̹� �߰��� �ɼ�����
				if (self.data[key] != null)
				{
					alert('�̹� �߰��� �ɼ��Դϴ�.');
					return false;
				}

				// �ɼ� �ڽ� �ʱ�ȭ
				for (var i=0,m=addopt.length;i<m ;i++ )
				{
					if (typeof addopt[i] == 'object') {
						addopt[i].selectedIndex = 0;
					}
				}
				//opt[0].selectedIndex = 0;
				//subOption(opt[0]);

				document.getElementById('el-multi-option-display').style.display = 'block';
			
				// �� �߰�
				var childs = document.getElementById('el-multi-option-display').childNodes;
				for (var k in childs)
				{
					if (childs[k].tagName == 'TABLE') {
						var table = childs[k];
						break;
					}
				}

				var td, tr = table.insertRow(0);
				var html = '';

				tr.id = key;

				// �Է� �ɼǸ�
				td = tr.insertCell(-1);
				html = '<div class="prod-opt">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				// �ɼǸ�
				html += '<div class="prod-opt">';
				html += self._optJoin(_data.opt);
				html += '</div>';

				// �߰� �ɼǸ�
				html += '<div style="font-size:11px;color:#A0A0A0;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';
												
				td.innerHTML = html;
			
				// ����
				td = tr.insertCell(-1);
				html = '';
				html += '<div style="float:left; margin-right:3px; padding-top:3px;"><input type=text name=_multi_ea[] id="el-ea-'+key+'" size=2 value='+ _data.ea +' style="border:1px solid #D3D3D3;width:30px;text-align:right;height:20px;" onblur="nsGodo_MultiOption.ea(\'set\',\''+key+'\',this.value);"></div>';
				html += '<div style="float:left;padding-left:3;padding-top:2px;">';
				html += '<div style="padding:1 0 2 0"><img src="/shop/data/skin/freemart/img/common/btn_multioption_ea_up.gif" onClick="nsGodo_MultiOption.ea(\'up\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '<div><img src="/shop/data/skin/freemart/img/common/btn_multioption_ea_down.gif" onClick="nsGodo_MultiOption.ea(\'down\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '</div>';
				td.innerHTML = html;

				// �ɼǰ���
				//_data.opt_price = _data.price;
				_data.opt_price = _data.realprice;
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				td = tr.insertCell(-1);
				td.style.cssText = 'padding-right:10px;text-align:right;font-weight:bold;color:#6A6A6A;';
				html = '';
				html += '<span id="el-price-'+key+'" style="padding-right:5px;">'+comma( _data.opt_price *  _data.ea) + '��</span>';
				html += '<button class="button-small button-dark" onClick="nsGodo_MultiOption.del(\''+key+'\');return false;">����</button>';
				td.innerHTML = html;
				
				//console.log(table.innerHTML);
				
				self.data[key] = _data;
				self.data_size++;

				// �� �ݾ�
				self.totPrice();
							
							
			
			} //chkGoodsForm
		}; //ADD
			
		
		this.ea = 
			function(dir, key,val) {	// up, down
				var min_ea = 0, max_ea = 0, remainder = 0;
				if (document.frmView.min_ea) min_ea = parseInt(document.frmView.min_ea.value);
				if (document.frmView.max_ea) max_ea = parseInt(document.frmView.max_ea.value);
			
				if (dir == 'up') {
					this.data[key].ea = (max_ea != 0 && max_ea <= this.data[key].ea) ? max_ea : parseInt(this.data[key].ea) + parseInt(this.data[key].sales_unit);
				}
				else if (dir == 'down')
				{
					if ((parseInt(this.data[key].ea) - 1) > 0)
					{
						this.data[key].ea = (min_ea != 0 && min_ea >= this.data[key].ea) ? min_ea : parseInt(this.data[key].ea) - parseInt(this.data[key].sales_unit);
					}
	
				}
				else if (dir == 'set') {
	
					if (val && !isNaN(val))
					{
						val = parseInt(val);
	
						if (max_ea != 0 && val > max_ea)
						{
							val = max_ea;
						}
						else if (min_ea != 0 && val < min_ea) {
							val = min_ea;
						}
						else if (val < 1)
						{
							val = parseInt(this.data[key].sales_unit);
						}
	
						remainder = val % parseInt(this.data[key].sales_unit);
	
						if (remainder > 0) {
							val = val - remainder;
						}
	
						this.data[key].ea = val;
	
					}
					else {
						alert('������ 1 �̻��� ���ڷθ� �Է��� �ּ���.');
						return;
					}
				}
			
				document.getElementById('el-ea-'+key).value = this.data[key].ea;
				document.getElementById('el-price-'+key).innerText = comma(this.data[key].ea * this.data[key].opt_price) + '��';
	
				// �ѱݾ�
				this.totPrice();
			
			};
			
		this.totPrice = function() {
				var self = this;
				var totprice = 0;
				var totCupon = 0;
				var totCuponPrice = 0;
				
				for (var i in self.data)
				{
					if (self.data[i] !== null && typeof self.data[i] == 'object') {
						totprice += self.data[i].opt_price * self.data[i].ea;
						totCuponPrice += self.data[i].couponrice * self.data[i].ea;
						totCupon += self.data[i].coupon * self.data[i].ea;;
					}
				}
	
				document.getElementById('el-multi-option-total-price').innerText = comma(totprice) + '��';
				
				if (totCupon > 0) {
					document.getElementById("prod-price-discount").style.display = "block";
					document.getElementById('el-multi-option-discount-amount').innerText = '- '+comma(totCupon) + '��';
					document.getElementById('el-multi-option-payment-amount').innerText = comma(totprice - totCupon) + '��';
					document.getElementById("prod-price-amount").className = "amount-noline";
				} else {
					document.getElementById("prod-price-discount").style.display = "none";
					document.getElementById("prod-price-amount").className = "amount";
				}
				
		};
		
		
		this.get_key = function(str) {
	
			str = str.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

			var _key = "";

			for (var i=0,m=str.length;i<m;i++) {
				_key += str.charAt(i) != '|' ? str.charCodeAt(i) : '|';
			}

			return _key.toUpperCase();
		};
		
}
		
function chkGoodsForm(form) {

	if (form.min_ea)
	{
		if (parseInt(form.ea.value) < parseInt(form.min_ea.value))
		{
			alert('�ּұ��ż����� ' + form.min_ea.value+'�� �Դϴ�.');
			return false;
		}
	}

	if (form.max_ea)
	{
		if (parseInt(form.ea.value) > parseInt(form.max_ea.value))
		{
			alert('�ִ뱸�ż����� ' + form.max_ea.value+'�� �Դϴ�.');
			return false;
		}
	}

	try
	{
		var step = form.ea.getAttribute('step');
		if (form.ea.value % step > 0) {
			alert('���ż����� '+ step +'�� ������ �����մϴ�.');
			return false;
		}
	}
	catch (e)
	{}

	var res = chkForm(form);

	// �Է¿ɼ� �ʵ尪 ����
	if (res)
	{
		var addopt_inputable = document.getElementsByName('addopt_inputable[]');
		for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

			if (typeof addopt_inputable[i] == 'object') {
				var v = addopt_inputable[i].value.trim();
				if (v) {
					var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
					tmp[2] = v;
					v = tmp.join('^');
				}
				else {
					v = '';
				}
				document.getElementsByName('_addopt_inputable[]')[i].value = v;
			}
		}
	}

	return res;

}
		

function updatePrice(selObj, price, salePrice) {
	var index = selObj.selectedIndex;
	var selValue = selObj.options[index].value;
}

// �ʼ� �ɼ� ���� - ��ǰ���� ������Ʈ
function updateUnitPrice(selObj, oPrice, oSalePrice, optObj) {
	var key = getOptionKey(optObj);
	if (price[key] == 'undefined') {
		return;
	}
	
	var $j = jQuery.noConflict();
	$j('#nprice').html(comma(price[key]));
	
	if($j('#price-amount').length) {
		$j('#price-amount').html(comma(realprice[key]));
	}
	
	if($j('#cprice').length) {
		$j('#cprice').html(comma(couponprice[key]));
	}
	
}

function getOptionKey(obj) {
	var _data = {};
	_data.opt = new Array;

	// �⺻ �ɼ�
	var opt = document.getElementsByName('opt[]');
	
	_data.opt[0] = opt[0].value;
	_data.opt[1] = '';
	
	if (typeof(opt[1]) != "undefined") {
		_data.opt[1] = opt[1].value;
	}

	var key = _data.opt[0] + (_data.opt[1] != '' ? '|' + _data.opt[1] : '');
	if (opt[0].selectedIndex == 0) key = fkey;
	
	try {
		key = obj.get_key(key);	// get_js_compatible_key ����
	} catch (e) {
		
	}
	
	return key;
}

//�м� ��ɰ��� ��ũ��Ʈ
function click_opt_fastion(idx,vidx,v){
	var el = document.getElementsByName('opt_txt[]');
	el[idx].value = v + '|' + vidx;

	if(idx == 0){
		var obj = document.getElementsByName('opt[]')[0];
		obj.selectedIndex = parseInt(vidx)+1 ;
		subOption(obj);
		chkOptimg();
	}else if(idx == 1){
		var obj = document.getElementsByName('opt[]')[1];
		obj.selectedIndex = vidx;
		chkOption(obj);
	}
}

function subOption_fashion()
{
	var el = document.getElementsByName('opt_txt[]');
	var el2 = document.getElementById('dtdopt2');
	var idx = el[0].value.split("|");
	var vidx = parseInt(idx[1])+1;
	var sub = opt[vidx];
	if(el2)el2.innerHTML = '';
	var n = 1;
	for (i=0;i<sub.length;i++){
		var div = sub[i].replace("')","").split("','");
		if(div[1]){
			if(opt2kind == 'img'){
				if(el2)el2.innerHTML += "<div style='width:43px;float:left;padding:5 0 5 0'><a href=\"javascript:click_opt_fastion('1','"+i+"','"+div[1]+"');nsGodo_MultiOption.set();\" name='icon2[]'><img id='opticon1_"+i+"' width='40' src='../data/goods/"+opt2icon[div[1]]+"' style='border:1px #cccccc solid' onmouseover=\"onicon(this);\" onmouseout=\"outicon(this)\" onclick=\"clicon(this)\"></a></div>";
			}else{
				if(el2)el2.innerHTML += "<div style='width:18px;float:left;padding-top:5px'><a href=\"javascript:click_opt_fastion('1','"+i+"','"+div[1]+"');subOption_fashion();nsGodo_MultiOption.set();\" name='icon2[]'><span style=\"float:left;width:15;height:15;border:1px #cccccc solid;background-color:#"+opt2icon[div[1]]+"\" onmouseover=\"onicon(this);\" onmouseout=\"outicon(this)\" onclick=\"clicon(this)\"></span></a></div>";
			}
		}else n++;
	}
}