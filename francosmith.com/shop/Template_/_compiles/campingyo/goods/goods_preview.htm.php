<?php /* Template_ 2.2.7 2014/04/01 01:41:24 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/goods_preview.htm 000042318 */ 
if (is_array($GLOBALS["opt"])) $TPL__opt_1=count($GLOBALS["opt"]); else if (is_object($GLOBALS["opt"]) && in_array("Countable", class_implements($GLOBALS["opt"]))) $TPL__opt_1=$GLOBALS["opt"]->count();else $TPL__opt_1=0;
if (is_array($GLOBALS["opt1img"])) $TPL__opt1img_1=count($GLOBALS["opt1img"]); else if (is_object($GLOBALS["opt1img"]) && in_array("Countable", class_implements($GLOBALS["opt1img"]))) $TPL__opt1img_1=$GLOBALS["opt1img"]->count();else $TPL__opt1img_1=0;
if (is_array($TPL_VAR["t_img"])) $TPL_t_img_1=count($TPL_VAR["t_img"]); else if (is_object($TPL_VAR["t_img"]) && in_array("Countable", class_implements($TPL_VAR["t_img"]))) $TPL_t_img_1=$TPL_VAR["t_img"]->count();else $TPL_t_img_1=0;
if (is_array($TPL_VAR["ex"])) $TPL_ex_1=count($TPL_VAR["ex"]); else if (is_object($TPL_VAR["ex"]) && in_array("Countable", class_implements($TPL_VAR["ex"]))) $TPL_ex_1=$TPL_VAR["ex"]->count();else $TPL_ex_1=0;
if (is_array($GLOBALS["addopt_inputable"])) $TPL__addopt_inputable_1=count($GLOBALS["addopt_inputable"]); else if (is_object($GLOBALS["addopt_inputable"]) && in_array("Countable", class_implements($GLOBALS["addopt_inputable"]))) $TPL__addopt_inputable_1=$GLOBALS["addopt_inputable"]->count();else $TPL__addopt_inputable_1=0;
if (is_array($GLOBALS["optnm"])) $TPL__optnm_1=count($GLOBALS["optnm"]); else if (is_object($GLOBALS["optnm"]) && in_array("Countable", class_implements($GLOBALS["optnm"]))) $TPL__optnm_1=$GLOBALS["optnm"]->count();else $TPL__optnm_1=0;
if (is_array($GLOBALS["addopt"])) $TPL__addopt_1=count($GLOBALS["addopt"]); else if (is_object($GLOBALS["addopt"]) && in_array("Countable", class_implements($GLOBALS["addopt"]))) $TPL__addopt_1=$GLOBALS["addopt"]->count();else $TPL__addopt_1=0;?>
<html>
<head>
<title>��ǰ �̸�����</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="/shop/data/skin/campingyo/common.js"></script>
<script src="/shop/lib/js/prototype.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
<style>
/* goods_spec list */
#goods_spec table {
	width:100%;
}
#goods_spec .top {
	border-top-width:1; border-top-style:solid;border-top-color:#DDDDDD;
	border-bottom-width:1; border-bottom-style:solid;border-bottom-color:#DDDDDD;
	background:#f7f7f7;
}
#goods_spec .sub {
	border-bottom-width:1; border-bottom-style:solid;border-bottom-color:#DDDDDD;
	margin-bottom:10;
}
#goods_spec th, #goods_spec td {
	padding:3px;
}
#goods_spec th {
	width: 80px;
	text-align:right;
	font-weight:normal;
}
#goods_spec td {
	text-align:left;
}
</style>
<script>
function fitwin()
{
	window.resizeTo(750,150);
	var borderY = document.body.clientHeight;

	width	= 750;
	height	= document.body.scrollHeight + borderY + 10;

	windowX = (window.screen.width-width)/2;
	windowY = (window.screen.height-height)/2;

	if(width>screen.width){
		width = screen.width;
		windowX = 0;
	}
	if(height>screen.height){
		height = screen.height;
		windowY = 0;
	}

	window.moveTo(windowX,windowY);
	window.resizeTo(width,height);
}
</script>
<script>

var price = new Array();
var reserve = new Array();
var consumer = new Array();
var memberdc = new Array();
var realprice = new Array();
var couponprice = new Array();
var special_discount_amount = new Array();
var coupon = new Array();
var cemoney = new Array();
var opt1img = new Array();
var opt2icon = new Array();
var opt2kind = "<?php echo $TPL_VAR["optkind"][ 1]?>";
var oldborder = "";
<?php if($TPL__opt_1){$TPL_I1=-1;foreach($GLOBALS["opt"] as $TPL_V1){$TPL_I1++;?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
<?php if($TPL_I1== 0&&$TPL_I2== 0){?>
var fkey = '<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>';
<?php }?>
price['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["price"]?>;
reserve['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["reserve"]?>;
consumer['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["consumer"]?>;
memberdc['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["memberdc"]?>;
realprice['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["realprice"]?>;
coupon['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["coupon"]?>;
couponprice['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["couponprice"]?>;
cemoney['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["coupon_emoney"]?>;
special_discount_amount['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["special_discount_amount"]?>;
<?php }}?><?php }}?>
<?php if($TPL__opt1img_1){foreach($GLOBALS["opt1img"] as $TPL_K1=>$TPL_V1){?>
opt1img['<?php echo $TPL_K1?>'] = "<?php echo $TPL_V1?>";
<?php }}?>
<?php if((is_array($TPL_R1=$GLOBALS["opticon"][ 1])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_K1=>$TPL_V1){?>
opt2icon['<?php echo $TPL_K1?>'] = "<?php echo $TPL_V1?>";
<?php }}?>

/* �ʼ� �ɼ� �и��� ��ũ��Ʈ start */
var opt = new Array();
opt[0] = new Array("('1���ɼ��� ���� �������ּ���','')");
<?php if($TPL__opt_1){$TPL_I1=-1;foreach($GLOBALS["opt"] as $TPL_V1){$TPL_I1++;?>
opt['<?php echo $TPL_I1+ 1?>'] = new Array("('== �ɼǼ��� ==','')",<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_S2=count($TPL_R2);$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>"('<?php echo $TPL_V2["opt2"]?><?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?> (<?php echo number_format($TPL_V2["price"])?>��)<?php }?><?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [ǰ��]<?php }?>','<?php echo $TPL_V2["opt2"]?>','<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?>soldout<?php }?>')"<?php if($TPL_I2!=$TPL_S2- 1){?>,<?php }?><?php }}?>);
<?php }}?>
function subOption(obj)
{
	var el = document.getElementsByName('opt[]');
	var sub = opt[obj.selectedIndex];
	while (el[1].length>0) el[1].options[el[1].options.length-1] = null;
	for (i=0;i<sub.length;i++){
		var div = sub[i].replace("')","").split("','");
		eval("el[1].options[i] = new Option" + sub[i]);
		if (div[2]=="soldout"){
			el[1].options[i].style.color = "#808080";
			el[1].options[i].setAttribute('disabled','disabled');
		}
	}
	el[1].selectedIndex = el[1].preSelIndex = 0;
	if (el[0].selectedIndex == 0) chkOption(el[1]);
}
/* �ʼ� �ɼ� �и��� ��ũ��Ʈ end */

function chkOptimg(){
	var opt = document.getElementsByName('opt[]');
	var key = opt[0].selectedIndex;
	var opt1 = opt[0][key].value;
	var ropt = opt1.split('|');
	chgOptimg(ropt[0])
}

function chgOptimg(opt1){
	if(opt1img[opt1]){
		objImg.src = (/^http(s)?:\/\//.test(opt1img[opt1])) ? opt1img[opt1] : "../data/goods/"+opt1img[opt1];
	}else{
		objImg.src = (/^http(s)?:\/\//.test('<?php echo $TPL_VAR["r_img"][ 0]?>')) ? '<?php echo $TPL_VAR["r_img"][ 0]?>' : "../data/goods/<?php echo $TPL_VAR["r_img"][ 0]?>";
	}
}

function chkOption(obj)
{
	if (!selectDisabled(obj)) return false;
}

function act(target,frmTarget)
{
	var form = document.frmView;
	var actClose=false;
	form.action = target + ".php";
	if (frmTarget=='opener'){
		opener.name="mainPage";
		form.target="mainPage";
		actClose=true;
	}

	var opt_cnt = 0, data;

	nsGodo_MultiOption.clearField();

	for (var k in nsGodo_MultiOption.data) {
		data = nsGodo_MultiOption.data[k];
		if (data && typeof data == 'object') {
			nsGodo_MultiOption.addField(data, opt_cnt);
			opt_cnt++;
		}
	}
	
	if (opt_cnt > 0) {
		form.submit();
	}
	else {
		if (chkGoodsForm(form))
			form.submit();
		else
			return;
	}
	
	if(actClose)self.close();

	return;
}

function chgImg(obj)
{
	var objImg = document.getElementById('objImg');
	if (obj.getAttribute("ssrc")) objImg.src = obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("ssrc");
	else objImg.src = obj.src.replace("/t/","/");
<?php if($TPL_VAR["detailView"]=='y'){?>
	// �����Ϻ� �߰����� 2010.11.09
	if (obj.getAttribute("lsrc")) objImg.setAttribute("lsrc", obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("lsrc"));
	else objImg.setAttribute("lsrc", obj.getAttribute("src").replace("/t/", "/").replace("_sc.", '.'));
	ImageScope.setImage(objImg, beforeScope, afterScope);
	// �����Ϻ� �߰����� 2010.11.09
<?php }?>
}

function innerImgResize()	// ���� �̹��� ũ�� ������¡
{
	var objContents = document.getElementById('contents');
	var innerWidth = 645;
	var img = objContents.getElementsByTagName('img');
	for (var i=0;i<img.length;i++){
		img[i].onload = function(){
			if (this.width>innerWidth) this.width = innerWidth;
		};
	}
}

<?php if($TPL_VAR["detailView"]=='y'){?>
// �����Ϻ� �߰����� 2010.11.09
function beforeScope() {
	document.getElementsByName("frmView")[0].style.visibility = "hidden";
}

function afterScope() {
	document.getElementsByName("frmView")[0].style.visibility = "visible";
}
// �����Ϻ� �߰����� 2010.11.09
<?php }?>

<?php if($TPL_VAR["naverNcash"]=='Y'){?>
// ���̹� ���ϸ��� �߰� ���� 2011.06.10
function mileage_info(status) {
	document.getElementById("n_mileage").style.display = status;
	document.getElementById("n_mileage").style.left = document.body.scrollLeft + event.clientX;
	document.getElementById("n_mileage").style.top = document.body.scrollTop + event.clientY;
}
// ���̹� ���ϸ��� �߰� ���� 2011.06.10
<?php }?>

function fnRequestStockedNoti(goodsno) {

	popup('./popup_request_stocked_noti.php?goodsno='+goodsno,360,160);

}

</script>
<?php echo $TPL_VAR["systemHeadTagEnd"]?>

</head>
<body onload="fitwin()">


<div class="indiv"><!-- Start indiv -->

<!-- ��ǰ �̹��� -->
<div style="margin:0px auto 0px auto">
<div style="width:49%;float:left;text-align:center;">
<div style="padding-bottom:10"><span onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer"><!--�����Ϻ����--><?php if($TPL_VAR["detailView"]=='y'){?><?php if($TPL_VAR["sc_img"][ 0]){?><?php echo goodsimg($TPL_VAR["sc_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id=objImg')?><?php }?><!--�����Ϻ����--></span></div>
<div style="padding-bottom:10">
<img src="/shop/data/skin/campingyo/img/common/btn_zoom.gif" onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer" align=absmiddle>
</div>
<div align=center>
<?php if($TPL_t_img_1){$TPL_I1=-1;foreach($TPL_VAR["t_img"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_VAR["detailView"]=='y'){?>
<?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' ssrc='".$TPL_VAR["sc_img"][$TPL_I1]."' lsrc='".$TPL_VAR["r_img"][$TPL_I1]."' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

<?php }else{?>
<?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

<?php }?>
<?php }}?>
</div>
</div>

<!-- ��ǰ ���� ����Ʈ -->
<div id=goods_spec style="width:49%;float:left;">
<!--�����Ϻ����--><?php if($TPL_VAR["detailView"]=='y'){?><div id="zoom_view" style="display:none; position:absolute; width:340px; height:370px;"></div><?php }?><!--�����Ϻ����-->
<form name=frmView method=post onsubmit="return false" target="ifrmHidden">
<input type=hidden name=preview value="y">
<input type=hidden name=mode value="addItem">
<input type=hidden name=goodsno value="<?php echo $TPL_VAR["goodsno"]?>">
<input type=hidden name=goodsCoupon value="<?php echo $TPL_VAR["coupon"]?>">
<?php if($TPL_VAR["min_ea"]> 1){?><input type="hidden" name="min_ea" value="<?php echo $TPL_VAR["min_ea"]?>"><?php }?>
<?php if($TPL_VAR["max_ea"]!='0'){?><input type="hidden" name="max_ea" value="<?php echo $TPL_VAR["max_ea"]?>"><?php }?>
<div style="padding:10px 0 10px 5px" align=left>
<b style="font:bold 12pt ����;">
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
<?php echo $TPL_VAR["goodsnm"]?>

<?php }elseif($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 1]=='Y'){?>
<?php echo $TPL_VAR["goodsnm"]?>

<?php }?>
</b>
</div>
<div style="padding:0 0 10px 5px;font:11px dotum;letter-spacing:-1px;color:#666666"><?php echo $TPL_VAR["shortdesc"]?></div>
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]||($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 2]=='Y')){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
	<tr><td height=2></td></tr>
<?php if($TPL_VAR["sales_status"]=='ing'){?>
	<!--tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">������ �Ǹ���!!</span></td></tr-->
<?php }elseif($TPL_VAR["sales_status"]=='range'){?>
	<tr><th>�����ð� :</th><td><span id="el-countdown-1" style="padding-bottom:5px;font:13pt bold;color:#EF1C21"></span></td></tr>
	<script type="text/javascript">
	Countdown.init('<?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_end"])?>', 'el-countdown-1');
	</script>
<?php }elseif($TPL_VAR["sales_status"]=='before'){?>
	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21"><?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_start"])?> �ǸŽ����մϴ�.</span></td></tr>
<?php }elseif($TPL_VAR["sales_status"]=='end'){?>
	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">�ǸŰ� ����Ǿ����ϴ�.</span></td></tr>
<?php }?>
<?php if($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='image'){?>
	<tr><th>�ǸŰ��� :</th><td><img src="../data/goods/icon/custom/soldout_price"></td></tr>
<?php }elseif($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='string'){?>
	<tr><th>�ǸŰ��� :</th><td><b><?php echo $GLOBALS["cfg_soldout"]["price_string"]?></b></td></tr>
<?php }elseif(!$TPL_VAR["strprice"]){?>
	<tr>
		<th>�ǸŰ��� :</th>
		<td>
<?php if($TPL_VAR["consumer"]){?>
		<strike><span id=consumer><?php echo number_format($TPL_VAR["consumer"])?></span></strike> ��
<?php }?>
		<b><span id=price><?php echo number_format($TPL_VAR["price"])?></span>��</b>
		</td>
	</tr>
<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<th>ȸ�����ΰ� :</th>
		<td style="font-weight:bold"><span id=obj_realprice><?php echo number_format($TPL_VAR["realprice"])?>��&nbsp;(-<?php echo number_format($TPL_VAR["memberdc"])?>��)</span></b></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr><th>�������밡 :</th>
	<td>
	<span id=obj_coupon style="font-weight:bold;color:#EF1C21"><?php echo number_format($TPL_VAR["couponprice"])?>��&nbsp;(-<?php echo number_format($TPL_VAR["coupon"])?>��)</span>
	<div><?php echo $TPL_VAR["about_coupon"]?></div>
	</td></tr>
<?php }?>
	<tr><th>������ :</th><td><span id=reserve><?php echo number_format($TPL_VAR["reserve"])?></span>��</td></tr>
<?php if($TPL_VAR["naverNcash"]=='Y'){?>
	<tr><th>���̹�&nbsp;&nbsp;<br/>���ϸ��� :</th><td><?php if($TPL_VAR["exception"]){?><?php echo $TPL_VAR["exception"]?><?php }else{?><?php if($TPL_VAR["N_ba"]){?><span style="font:bold;color:#1ec228;"><?php echo $TPL_VAR["N_ba"]?>%</span><?php }?> <?php if($TPL_VAR["N_aa"]){?><span style="font:bold;color:#1ec228;">+ �߰� <?php echo $TPL_VAR["N_aa"]?>%</span><?php }?> ����<?php }?>&nbsp;&nbsp;<img src="/shop/data/skin/campingyo/img/nmileage/n_mileage_info2.gif" onmouseover="javascript:mileage_info('block');"></td></tr>
	<div onmouseover="this.style.display='block';" onmouseout="this.style.display='none';" style='width:210px; height:100px; display:none; position:absolute; z-index:50; border:1px solid #1ec228; background-color:#ffffff; padding:10px; line-height:150%;' id='n_mileage' class='stxt'>���̹����ϸ����� ������ ��𼭳�<br/>���̹� ���̵� �ϳ��� ���ϰ� �����ް�<br/>����� �� �ִ� ���������� �Դϴ�.<br/><a href="http://mileage.naver.com/customer/introduction/serviceIntroduction" target="_blank"><img src="/shop/data/skin/campingyo/img/nmileage/n_mileage_help.gif" border="1px" vspace="5"></a></div>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
	<tr><th>���������� :</th>
	<td>
	<span id=obj_coupon_emoney style="font-weight:bold;color:#EF1C21"></span> &nbsp;<span style="font:bold 9pt tahoma; color:#FF0000" ><?php echo number_format($TPL_VAR["coupon_emoney"])?>��</span>
	</td></tr>
<?php }?>
<?php if($TPL_VAR["delivery_type"]== 1){?>
	<tr><th>��ۺ� :</th><td>������</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 2){?>
	<tr><th>������ۺ� :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>��</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 3){?>
	<tr><th>���ҹ�ۺ� :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>��</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 4){?>
	<tr><th>������ۺ� :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>��</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 5){?>
	<tr><th>��������ۺ� :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>�� (������ ���� ��ۺ� �߰��˴ϴ�.)</td></tr>
<?php }?>
<?php }else{?>
	<tr><th>�ǸŰ��� :</th><td><b><?php echo $TPL_VAR["strprice"]?></b></td></tr>
<?php }?>
</table>
<?php }?>
<table border=0 cellpadding=0 cellspacing=0>
	<tr><td height=5></td></tr>
<?php if($TPL_VAR["goodscd"]){?><tr height><th>��ǰ�ڵ� :</th><td><?php echo $TPL_VAR["goodscd"]?></td></tr><?php }?>
<?php if($TPL_VAR["origin"]){?><tr><th>������ :</th><td><?php echo $TPL_VAR["origin"]?></td></tr><?php }?>
<?php if($TPL_VAR["maker"]){?><tr><th>������ :</th><td><?php echo $TPL_VAR["maker"]?></td></tr><?php }?>
<?php if($TPL_VAR["brand"]){?><tr><th>�귣�� :</th><td><?php echo $TPL_VAR["brand"]?> <a href="<?php echo url("goods/goods_brand.php?")?>&brand=<?php echo $TPL_VAR["brandno"]?>">[�귣��ٷΰ���]</a></td></tr><?php }?>
<?php if($TPL_VAR["launchdt"]){?><tr><th>����� :</th><td><?php echo $TPL_VAR["launchdt"]?></td></tr><?php }?>
<?php if($TPL_ex_1){foreach($TPL_VAR["ex"] as $TPL_K1=>$TPL_V1){?><tr><th><?php echo $TPL_K1?> :</th><td><?php echo $TPL_V1?></td></tr><?php }}?>

<?php if(!$GLOBALS["opt"]){?>
	<tr><th>���ż��� :</th>
	<td>
<?php if(!$TPL_VAR["runout"]){?>
	<div style="float:left;"><input type=text name=ea size=2 value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?> class=line style="text-align:right;height:18px"></div>
	<div style="float:left;padding-left:3">
	<div style="padding:1 0 2 0"><img src="/shop/data/skin/campingyo/img/common/btn_plus.gif" onClick="chg_cart_ea(frmView.ea,'up')" style="cursor:pointer"></div>
	<div><img src="/shop/data/skin/campingyo/img/common/btn_minus.gif" onClick="chg_cart_ea(frmView.ea,'dn')" style="cursor:pointer"></div>
	</div>
	<div style="padding-top:3; float:left">��</div>
	<div style="padding-left:10px;float:left" class="stxt">
<?php if($TPL_VAR["min_ea"]> 1){?><div>�ּұ��ż��� : <?php echo $TPL_VAR["min_ea"]?>��</div><?php }?>
<?php if($TPL_VAR["max_ea"]!='0'){?><div>�ִ뱸�ż��� : <?php echo $TPL_VAR["max_ea"]?>��</div><?php }?>
	</div>
<?php }else{?>
	ǰ���� ��ǰ�Դϴ�
<?php }?>
	</td></tr>
<?php }else{?>
	<input type=hidden name=ea value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>>
<?php }?>

<?php if($TPL_VAR["chk_point"]){?>
	<tr><th>����ȣ�� :</th><td><?php if((is_array($TPL_R1=array_fill( 0,$TPL_VAR["chk_point"],''))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>��<?php }}?></td></tr>
<?php }?>
<?php if($TPL_VAR["icon"]){?><tr><th>��ǰ���� :</th><td><?php echo $TPL_VAR["icon"]?></td></tr><?php }?>
</table>

<?php if(!$TPL_VAR["strprice"]){?>

<!-- �߰� �ɼ� �Է��� -->
<?php if($GLOBALS["addopt_inputable"]){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
<?php if($TPL__addopt_inputable_1){$TPL_I1=-1;foreach($GLOBALS["addopt_inputable"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
	<tr><th><?php echo $TPL_K1?> :</th>
	<td>
		<input type="hidden" name="_addopt_inputable[]" value="">
		<input type="text" name="addopt_inputable[]" label="<?php echo $TPL_K1?>" option-value="<?php echo $TPL_V1["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V1["opt"]?>^<?php echo $TPL_V1["addprice"]?>" value="" <?php if($GLOBALS["addopt_inputable_req"][$TPL_I1]){?>required fld_esssential<?php }?> maxlength="<?php echo $TPL_V1["opt"]?>">
	</td></tr>
<?php }}?>
</table>
<?php }?>

<!-- �ʼ� �ɼ� ��ü�� -->
<?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="single"){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
	<tr><td height=6></td></tr>
	<tr><th  valign="top"><?php echo $TPL_VAR["optnm"]?> :</th>
	<td>
	<div>
	<select name="opt[]" onchange="chkOption(this);chkOptimg();nsGodo_MultiOption.set();" required msgR="<?php echo $TPL_VAR["optnm"]?> ������ ���ּ���">
	<option value="">== �ɼǼ��� ==
<?php if($TPL__opt_1){foreach($GLOBALS["opt"] as $TPL_V1){?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<option value="<?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>|<?php echo $TPL_V2["opt2"]?><?php }?>" <?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> disabled class=disabled<?php }?>><?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>/<?php echo $TPL_V2["opt2"]?><?php }?> <?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?>(<?php echo number_format($TPL_V2["price"])?>��)<?php }?>
<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [ǰ��]<?php }?>
<?php }}?><?php }}?>
	</select></div>
	</td>
	</tr>
	<tr><td height=6></td></tr>
</table>
<?php }?>

<!-- �ʼ� �ɼ� �и��� -->
<?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="double"){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
	<tr><td height=6></td></tr>
<?php if($TPL__optnm_1){$TPL_I1=-1;foreach($GLOBALS["optnm"] as $TPL_V1){$TPL_I1++;?>
	<tr><th valign="top" ><?php echo $TPL_V1?> :</th>
	<td >

	<!-- �ɼ� ���� -->
	<div>
<?php if(!$TPL_I1){?>
	<div>
	<select name="opt[]" onchange="subOption(this);chkOptimg();selicon(this);nsGodo_MultiOption.set();" required msgR="<?php echo $TPL_V1?> ������ ���ּ���">
	<option value="">== �ɼǼ��� ==
<?php if((is_array($TPL_R2=($GLOBALS["opt"]))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_K2=>$TPL_V2){?><option value="<?php echo $TPL_K2?>"><?php echo $TPL_K2?><?php }}?>
	</select>
	</div>
<?php }else{?>
	<select name="opt[]" onchange="chkOption(this);selicon(this);nsGodo_MultiOption.set();" required msgR="<?php echo $TPL_V1?> ������ ���ּ���"><option value="">==����==</select>
<?php }?>
	</div>

	<!-- �ɼ� �̹��� ������ -->
<?php if($TPL_VAR["optkind"][$TPL_I1]=='img'){?>
<?php if(!$TPL_I1){?>
<?php if((is_array($TPL_R2=$GLOBALS["opticon"][$TPL_I1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_K2=>$TPL_V2){$TPL_I2++;?>
		<div style='width:43px;float:left;padding:5 0 5 0'><a href="javascript:click_opt_fastion('<?php echo $TPL_I1?>','<?php echo $TPL_I2?>','<?php echo $GLOBALS["opt"][$TPL_K2][$TPL_I2]["opt1"]?>');" name="icon[]"><img width="40" id="opticon0_<?php echo $TPL_I2?>" id="opticon_<?php echo $TPL_I1?>_<?php echo $TPL_I2?>" style="border:1px #cccccc solid" src='../data/goods/<?php echo $TPL_V2?>'  onmouseover="onicon(this);chgOptimg('<?php echo $TPL_K2?>');" onmouseout="outicon(this)" onclick="clicon(this)"></a></div>
<?php }}?>
<?php }else{?>
	<div id="dtdopt2"></div>
<?php }?>
<?php }?>

	<!-- �ɼ� ����Ÿ�� ������ -->
<?php if($TPL_VAR["optkind"][$TPL_I1]=='color'){?>
<?php if(!$TPL_I1){?>
<?php if((is_array($TPL_R2=$GLOBALS["opticon"][$TPL_I1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_K2=>$TPL_V2){$TPL_I2++;?>
		<div style='width:18px;float:left;padding-top:5px ' ><a href="javascript:click_opt_fastion('<?php echo $TPL_I1?>','<?php echo $TPL_I2?>','<?php echo $TPL_K2?>');" style="cursor:hand;"  name="icon[]"><span  style="float:left;width:15;height:15;border:1px #cccccc solid;background-color:#<?php echo $TPL_V2?>" onmouseover="onicon(this);chgOptimg('<?php echo $TPL_K2?>');" onmouseout="outicon(this)" onclick="clicon(this)"></span></a></div>
<?php }}?>
<?php }else{?>
	<div id="dtdopt2"></div>
<?php }?>
<?php }?>

	<input type="hidden" name="opt_txt[]" value="">
	</td></tr>
<?php }}?>
	<tr><td height=6></td></tr>
</table>
<script>subOption(document.getElementsByName('opt[]')[0])</script>
<?php }?>

<!-- �߰� �ɼ� -->
<table border=0 cellpadding=0 cellspacing=0 class=sub>
<?php if($TPL__addopt_1){$TPL_I1=-1;foreach($GLOBALS["addopt"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
	<tr><th><?php echo $TPL_K1?> :</th>
	<td>
<?php if($GLOBALS["addoptreq"][$TPL_I1]){?>
	<select name="addopt[]" required label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set();">
	<option value="">==<?php echo $TPL_K1?> ����==
<?php }else{?>
	<select name="addopt[]" label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set();">
	<option value="">==<?php echo $TPL_K1?> ����==
	<option value="-1">���þ���
<?php }?>
<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<option value="<?php echo $TPL_V2["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V2["opt"]?>^<?php echo $TPL_V2["addprice"]?>"><?php echo $TPL_V2["opt"]?>

<?php if($TPL_V2["addprice"]){?>(<?php echo number_format($TPL_V2["addprice"])?>�� �߰�)<?php }?>
<?php }}?>
	</select>
	</td></tr>
<?php }}?>
</table>


<!-- ? �ɼ� ������ -->
<script>
var nsGodo_MultiOption = function() {

	function size(e) {

		var cnt = 0;
		var type = '';

		for (var i in e) {
			cnt++;
		}

		return cnt;
	}

	return {
		_soldout : <?php if($TPL_VAR["runout"]){?>true<?php }else{?>false<?php }?>,
		data : [],
		data_size : 0,
		_optJoin : function(opt) {

			var a = [];

			for (var i=0,m=opt.length;i<m ;i++)
			{
				if (typeof opt[i] != 'undefined' && opt[i] != '')
				{
					a.push(opt[i]);
				}
			}

			return a.join(' / ');

		},
		getFieldTag : function (name, value) {
			var el = document.createElement('input');
			el.type = "hidden";
			el.name = name;
			el.value = value;

			return el;

		},
		clearField : function() {

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

		},
		addField : function(obj, idx) {

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
		},
		set : function() {

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
			if (add == true)
			{
				this.add();
			}
		},
		del : function(key) {

			this.data[key] = null;
			var tr = document.getElementById(key);
			tr.parentNode.removeChild(tr);
			this.data_size--;

			// �� �ݾ�
			this.totPrice();

		},
		add : function() {

			var self = this;

			if (self._soldout)
			{
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
				html = '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				// �ɼǸ�
				html += '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
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
				html += '<div style="float:left;"><input type=text name=_multi_ea[] id="el-ea-'+key+'" size=2 value='+ _data.ea +' style="border:1px solid #D3D3D3;width:30px;text-align:right;height:20px" onblur="nsGodo_MultiOption.ea(\'set\',\''+key+'\',this.value);"></div>';
				html += '<div style="float:left;padding-left:3">';
				html += '<div style="padding:1 0 2 0"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_ea_up.gif" onClick="nsGodo_MultiOption.ea(\'up\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '<div><img src="/shop/data/skin/campingyo/img/common/btn_multioption_ea_down.gif" onClick="nsGodo_MultiOption.ea(\'down\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '</div>';
				td.innerHTML = html;

				// �ɼǰ���
				_data.opt_price = _data.price;
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
				html += '<span id="el-price-'+key+'">'+comma( _data.opt_price *  _data.ea) + '��</span>';
				html += '<a href="javascript:void(0);" onClick="nsGodo_MultiOption.del(\''+key+'\');return false;"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_del.gif"></a>';
				td.innerHTML = html;

				self.data[key] = _data;
				self.data_size++;

				// �� �ݾ�
				self.totPrice();


			}
		},
		ea : function(dir, key,val) {	// up, down

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

		},
		totPrice : function() {
			var self = this;
			var totprice = 0;
			for (var i in self.data)
			{
				if (self.data[i] !== null && typeof self.data[i] == 'object') totprice += self.data[i].opt_price * self.data[i].ea;
			}

			document.getElementById('el-multi-option-total-price').innerText = comma(totprice) + '��';
		},
		get_key : function(str) {

			str = str.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

			var _key = "";

			for (var i=0,m=str.length;i<m;i++) {
				_key += str.charAt(i) != '|' ? str.charCodeAt(i) : '|';
			}

			return _key.toUpperCase();
		}
	}
}();

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

</script>

<style type="text/css">
.goods-multi-option {display:none;}
.goods-multi-option table {border:1px solid #D3D3D3;}
.goods-multi-option table td {border-bottom:1px solid #D3D3D3;padding:10px;}
</style>
<div id="el-multi-option-display" class="goods-multi-option">
	<table border="0" cellpadding="0" cellspacing="0">
	<col width=""><col width="50"><col width="80">
	</table>

	<div style="font-size:12px;text-align:right;padding:10px 20px 10px 0;border-bottom:1px solid #D3D3D3;margin-bottom:5px;">
		<img src="/shop/data/skin/campingyo/img/common/btn_multioption_br.gif" align="absmiddle"> �� �ݾ� : <span style="color:#E70103;font-weight:bold;" id="el-multi-option-total-price"></span>
	</div>
</div>
<!-- / -->

<?php }?>
<?php echo $TPL_VAR["cyworldScrap"]?>

<?php echo $TPL_VAR["snsBtn"]?>

<!-- ���� ��ư -->
<div style="width:330px;">
<?php if(!$TPL_VAR["strprice"]&&!$TPL_VAR["runout"]&&($TPL_VAR["sales_status"]=='ing'||$TPL_VAR["sales_status"]=='range')){?>
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
<a href="javascript:act('../order/order')"><img src="/shop/data/skin/campingyo/img/common/btn_direct_buy.gif"></a>
<a href="javascript:cartAdd(frmView,'<?php echo $TPL_VAR["cartCfg"]->redirectType?>')"><img src="/shop/data/skin/campingyo/img/common/btn_cart.gif"></a>
<a href="javascript:act('../mypage/mypage_wishlist')"><img src="/shop/data/skin/campingyo/img/common/btn_wish_m_un.gif"></a>
<?php }?>
<?php }elseif($TPL_VAR["runout"]&&$TPL_VAR["use_stocked_noti"]){?>
<a href="javascript:fnRequestStockedNoti('<?php echo $TPL_VAR["goodsno"]?>');"><img src="/shop/data/skin/campingyo/img/stocked_noti/btn_alarm.gif"></a>
<?php }?>
<a href="javascript:opener.location.href='<?php echo url("goods/goods_list.php?")?>&category=<?php echo $_GET["category"]?>';self.close()"><img src="/shop/data/skin/campingyo/img/common/btn_list.gif"></a>
</div>
<div><?php echo $TPL_VAR["naverCheckout"]?></div>
<div><?php echo $TPL_VAR["auctionIpayBtn"]?></div>
<div><?php echo $TPL_VAR["qrcode_view"]?></div>
<?php echo $TPL_VAR["plusCheeseBtn"]?>

</form>
</div>
</div>



</div><!-- End indiv -->



<!--�����Ϻ����-->
<?php if($TPL_VAR["detailView"]=='y'){?>
<script type="text/javascript">
var objImg = document.getElementById("objImg");
objImg.setAttribute("lsrc", objImg.getAttribute("src").replace("/t/", "/").replace("_sc.", '.'));
ImageScope.setImage(objImg, beforeScope, afterScope);
</script>
<?php }?>
<!--�����Ϻ����-->


<iframe name="ifrmHidden" src='../../../blank.php' style="display:none;width:100%;height:600"></iframe>

</body>
</html>