<?php /* Template_ 2.2.7 2016/04/30 15:17:00 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_search.htm 000015535 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<!-- ����̹��� || ������ġ -->

<?php if($_GET['disp_type']=='gallery'){?>
<?  $TPL_VAR["lstcfg"]["tpl"] = "tpl_01"; ?>
<?php }elseif($_GET['disp_type']=='list'){?>
<?  $TPL_VAR["lstcfg"]["tpl"] = "tpl_02"; ?>
<?php }?>

<style>
.paletteColor { width:16px; height:16px; cursor:pointer; float:left; margin:2px; border:1px solid #dfdfdf;}
.paletteColor_selected { width:16px; height:16px; cursor:pointer; float:left; margin:2px; border:1px solid #dfdfdf; }
</style>
<div class="page-wrapper">
	<p>&nbsp;</p>
	<div class="search_page_title">
		<span class="search_for" >Search for</span>
		<span class="search_word">"<?php echo $_GET['sword']?>"</span>
	</div>

<!--
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><span class="search_for">Search for</span><span class="search_word">"<?php echo $_GET['sword']?>"</span></td>
</tr>
<TR>
<td class="path">HOME > <B>��ǰ�˻�</B></td>
</TR>
</TABLE>
-->

	<div class="indiv"><!-- Start indiv -->

	<form name=frmList id=form>
	<input type="hidden" name="disp_type" value="<?php echo $_GET['disp_type']?>" />
	<input type="hidden" name="hid_sword" value="<?php echo $_GET['hid_sword']?>" />
	<input type=hidden name=searched value="Y">
	<input type=hidden name=log value="1">
	<input type=hidden name=sort value="<?php echo $_GET['sort']?>">
	<input type=hidden name=page_num value="<?php echo $_GET['page_num']?>">

	<div class="search_bar">
		<select name="skey">
			<option value="all" <?php echo $GLOBALS["selected"]["skey"]['all']?>> ���հ˻� </option>
			<option value="goodsnm" <?php echo $GLOBALS["selected"]["skey"]['goodsnm']?>> ��ǰ�� </option>
			<option value="goodscd" <?php echo $GLOBALS["selected"]["skey"]['goodscd']?>> ��ǰ�ڵ� </option>
			<option value="maker" <?php echo $GLOBALS["selected"]["skey"]['maker']?>> ������ </option>
			<option value="brand" <?php echo $GLOBALS["selected"]["skey"]['brand']?>> �귣�� </option>
		</select>
		<input type="text" NAME="sword" value="<?php echo $_GET['sword']?>" size="32" style="background-color:#FFFFFF; font-color:#555555;font-face:dotum">	
		<span class=noline><input type="image" src="/shop/data/skin/freemart/img/common/btn_search.gif" align=absmiddle></span>
		<label><input type="checkbox" name="refind" style="border:0" onclick="search_refind(this)" <?php echo $GLOBALS["hid_checked"]?>/>��� �� ��˻�</label>
	</div>
	
	<div style="width:98%; margin-left:10px; border-bottom:1px solid #cccccc; padding-bottom:5px;text-align:center;" >
		<span class="popular_keywords; "><?php if($GLOBALS["s_type"]['keyword']){?>�α�˻��� : <?php echo $GLOBALS["s_type"]['keyword']?><?php }?></span>
	</div>	


<?php if($GLOBALS["s_type"]['detail_type']){?>
	<div style="border:1px solid #DEDEDE;">
		<table width=100% cellpadding=10 cellspacing=0 border=0>
		<tr>
			<td style="text-align:center;">
				<table cellpadding="0" cellspacing="0" border="0" >
				<col /><col width="100px">
				<tr>
					<td style="text-align:center;">
					<!-- �˻� : Start -->
						<table cellpadding="2" cellspacing="0" border="0" style="text-align:center">
<?php if((is_array($TPL_R1=$GLOBALS["s_type"]['detail_type'])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
						<tr>
<?php switch($TPL_V1){case 'category':?>
							<td style="text-align:right;">��ǰ�з� : </td>
							<td style="text-align:left;">
							<div id=dynamic></div>
							<script src="/shop/lib/js/categoryBox.js"></script>
							<script>new categoryBox('cate[]',4,'<?php echo $GLOBALS["category"]?>','','frmList');</script>
							</td>
<?php break;case 'price':?>
							<td style="text-align:right;">���� : </td>
							<td style="text-align:left;">
								<input type=text name=price[] style="width:100px;background-color:#FFFFFF;" value="<?php echo $_GET['price'][ 0]?>">�� ~
								<input type=text name=price[] style="width:100px;background-color:#FFFFFF;" value="<?php echo $_GET['price'][ 1]?>">��
							</td>
<?php break;case 'add':?>
							<td style="text-align:right;">���Ǽ��� : </td>
							<td style="text-align:left;">
<?php if((is_array($TPL_R2=$GLOBALS["s_type"]['detail_add_type'])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
								<label><input type="checkbox" name="detail_add_type[]" value="<?php echo $TPL_V2?>" style="border:0" <?php echo $GLOBALS["add_checked"][$TPL_V2]?>/>
<?php switch($TPL_V2){case 'free_deliveryfee':?>������<?php break;case 'dc':?>��������<?php break;case 'save':?>��������<?php break;case 'new':?>�Ż�ǰ<?php break;case 'event':?>�̺�Ʈ��ǰ<?php }?>
								</label>
<?php }}?>
							</td>
<?php break;case 'color':?>
							<td style="text-align:right;">���� : </td>
							<td style="text-align:left;">
								<input type="hidden" name="ssColor" id="ssColor" value="<?php echo $GLOBALS["_GET"]["ssColor"]?>" />
								<table border="0" cellpadding="0" cellspacing="2">
								<tr>
									<td>
<?php if((is_array($TPL_R2=($GLOBALS["colorList"]))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
										<div class="paletteColor" style="background-color:#<?php echo $TPL_V2?>;" onclick="ssSelectColor(this.style.backgroundColor)"></div>
<?php }}?>
									</td>
								</tr>
								<tr>
									<td id="selectedColor" title="���õ� ���� ����Ŭ������ �����Ͻ� �� �ֽ��ϴ�."></td>
								</tr>
								</table>
							</td>
<?php break;default:?>
<?php }?>
						</tr>
<?php }}?>
						</table>
					<!-- �˻� : End -->
					</td>
					<td style="text-align:right">
						<span class=noline><input type="image" src="/shop/data/skin/freemart/img/common/btn_search_b.gif" align=absmiddle></span>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
<?php }?>

	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr><td height=18></td></tr>
	<tr>
		<td class=small height=27 style="padding:0 0 0 5">
			<table width=100% border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td style="text-align:left;">
					<font color="#555555">
<?php if($_GET['hid_sword']&&$_GET['sword']){?>
					<font style="font-weight:bold; font-size:11px;"><?php echo $_GET['hid_sword']?></font> �˻� ��� ��,<font style="font-weight:bold; font-size:11px"><?php echo $_GET['sword']?></font>�� ������ �˻����
<?php }elseif(!$_GET['hid_sword']&&$_GET['sword']){?>
					<font style="font-weight:bold; font-size:11px;"><?php echo $_GET['sword']?></font> �� �˻��� ���
<?php }?>
					�� <font style="font-weight:bold; font-size:11px"><?php echo $TPL_VAR["pg"]->recode['total']?></font>���� ��ǰ�� �ֽ��ϴ�.</FONT>
				</td>
				<td style="text-align:right;">
<?php if($GLOBALS["s_type"]['disp_type']=='Y'){?>
					<a href="javascript: add_param_submit('disp_type','list')">
<?php if($_GET['disp_type']=='list'){?>
						<img name="disp_list" src="/shop/data/skin/freemart/img/common/btn_list_on.gif">
<?php }else{?>
						<img name="disp_list" src="/shop/data/skin/freemart/img/common/btn_list_off.gif">
<?php }?>
					</a>
					<a href="javascript: add_param_submit('disp_type', 'gallery')">
<?php if($_GET['disp_type']=='gallery'){?>
						<img name="disp_gallery" src="/shop/data/skin/freemart/img/common/btn_gallery_on.gif">
<?php }else{?>
						<img name="disp_gallery" src="/shop/data/skin/freemart/img/common/btn_gallery_off.gif">
<?php }?>
					</a>
<?php }?>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<!-- capture_start("list_top") -->
			<table width=100% cellpadding=0 cellspacing=0 border="0">
			<tr><td height=1 bgcolor=#DDDDDD style="padding:0px"></td></tr>
			<tr>
				<td>
					<div class="sort_area">
						<ul id="sort_ul" class="sort_list">
							<li class="" id="sort_price"><a href="javascript:sort('price')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_price_desc"><a href="javascript:sort('price desc')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_goodsnm_desc"><a href="javascript:sort('goodsnm desc')"><span></span>��ǰ���</a></li>
							<li class="" id="sort_goodsnm"><a href="javascript:sort('goodsnm')"><span></span>��ǰ���</a></li>
							<li class="" id="sort_reserve_desc"><a href="javascript:sort('reserve desc')"><span></span>�����ݡ�</a></li>
							<li class="" id="sort_reserve"><a href="javascript:sort('reserve')"><span></span>�����ݡ�</a></li>
							<li class="" id="sort_maker_desc"><a href="javascript:sort('maker desc')"><span></span>�������</a></li>
							<li class="" id="sort_maker"><a href="javascript:sort('maker')"><span></span>�������</a></li>
						</ul>
					</div>
					<div class="sort_area_items_per_page">
						<span>Items per page:</span>
						<select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt ����"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?><?php }}?></select>
					</div>
				</td>
			</tr>
			</table>

			<script>
				color2Tag('selectedColor');

				function remove_txt(obj){
					obj.style.cssText = "text-align:left;";
					obj.value="";
				}
				function search_refind(obj){
					var frm = document.frmList;
					if(obj.checked) tmp = frm.sword.value;
					else tmp = '';
					frm.sword.value = '';
					frm.hid_sword.value = tmp;
				}
				function add_param_submit (param_nm, param_val){
					var frm = document.frmList;
					if( param_nm == 'sword') frm.sword.value = param_val;
					else frm.disp_type.value = param_val;
					frm.submit();
				}
				function sort(sort)
				{
					var fm = document.frmList;
					fm.sort.value = sort;
					fm.submit();
				}

				//function sort_chk(sort)
				//{
				//if (!sort) return;
				//sort = sort.replace(" ","_");
				//var obj = document.getElementsByName('sort_'+sort);
				//if (obj.length){
				//		div = obj[0].src.split('list_');
				//		for (i=0;i<obj.length;i++){
				//		chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
				//			obj[i].src = div[0] + "list_" + chg;
				//	}	
				//}
				//}
	
				function sort_chk(sort)
				{
					if (!sort) return;
					sort = sort.replace(" ","_");
					
					
					var jq = jQuery.noConflict();
					var sort_by = jq("#sort_" + sort);
					var sort_ul = jq("#sort_list");
					if (sort_by.length) {
						//sort_ul.children("li" a span").removeClass("on");
						sort_by.find("a span").addClass("on");
					} 
				
				}
	
<?php if($_GET['sort']){?>
				if (is_sort) sort_chk('<?php echo $_GET['sort']?>');
<?php }?>
				var is_sort = 1;
			</script>
		<!-- capture_end ("list_top") -->
		</td>
	</tr>
	<tr><td height=1 bgcolor=#DDDDDD style="padding:0px"></td></tr>
	<tr>
		<td style="padding:15 0">
		<?php echo $this->assign('cols',$TPL_VAR["lstcfg"]["cols"])?>

		<?php echo $this->assign('size',$TPL_VAR["lstcfg"]["size"])?>

		<?php echo $this->define('tpl_include_file_1',"goods/list/".$TPL_VAR["lstcfg"]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

		</td>
	</tr>
	<tr><td height=1 bgcolor=#DDDDDD style="padding:0px"></td></tr>
	<tr>
		<td>
		<!-- capture_start("list_top") -->
			<table width=100% cellpadding=0 cellspacing=0 border="0">
			<tr><td height=1 bgcolor=#DDDDDD style="padding:0px"></td></tr>
			<tr>
				<td>
					<div class="sort_area">
						<ul id="sort_ul" class="sort_list">
							<li class="" id="sort_price"><a href="javascript:sort('price')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_price_desc"><a href="javascript:sort('price desc')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_goodsnm_desc"><a href="javascript:sort('goodsnm desc')"><span></span>��ǰ���</a></li>
							<li class="" id="sort_goodsnm"><a href="javascript:sort('goodsnm')"><span></span>��ǰ���</a></li>
							<li class="" id="sort_reserve_desc"><a href="javascript:sort('reserve desc')"><span></span>�����ݡ�</a></li>
							<li class="" id="sort_reserve"><a href="javascript:sort('reserve')"><span></span>�����ݡ�</a></li>
							<li class="" id="sort_maker_desc"><a href="javascript:sort('maker desc')"><span></span>�������</a></li>
							<li class="" id="sort_maker"><a href="javascript:sort('maker')"><span></span>�������</a></li>
						</ul>
					</div>
					<div class="sort_area_items_per_page">
						<span>Items per page:</span>
						<select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt ����"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?><?php }}?></select>
					</div>
				</td>
			</tr>
			</table>

			<script>
				color2Tag('selectedColor');

				function remove_txt(obj){
					obj.style.cssText = "text-align:left;";
					obj.value="";
				}
				function search_refind(obj){
					var frm = document.frmList;
					if(obj.checked) tmp = frm.sword.value;
					else tmp = '';
					frm.sword.value = '';
					frm.hid_sword.value = tmp;
				}
				function add_param_submit (param_nm, param_val){
					var frm = document.frmList;
					if( param_nm == 'sword') frm.sword.value = param_val;
					else frm.disp_type.value = param_val;
					frm.submit();
				}
				function sort(sort)
				{
					var fm = document.frmList;
					fm.sort.value = sort;
					fm.submit();
				}

				//function sort_chk(sort)
				//{
				//if (!sort) return;
				//sort = sort.replace(" ","_");
				//var obj = document.getElementsByName('sort_'+sort);
				//if (obj.length){
				//		div = obj[0].src.split('list_');
				//		for (i=0;i<obj.length;i++){
				//		chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
				//			obj[i].src = div[0] + "list_" + chg;
				//	}	
				//}
				//}
	
				function sort_chk(sort)
				{
					if (!sort) return;
					sort = sort.replace(" ","_");
					
					
					var jq = jQuery.noConflict();
					var sort_by = jq("#sort_" + sort);
					var sort_ul = jq("#sort_list");
					if (sort_by.length) {
						//sort_ul.children("li" a span").removeClass("on");
						sort_by.find("a span").addClass("on");
					} 
				
				}
	
<?php if($_GET['sort']){?>
				if (is_sort) sort_chk('<?php echo $_GET['sort']?>');
<?php }?>
				var is_sort = 1;
			</script>
		<!-- capture_end ("list_top") -->
		</td>
	</tr>
	<tr><td height=2 bgcolor=#DDDDDD></td></tr>
	<tr><td align=center height=30 bgcolor="#F3F3F3"><?php echo $TPL_VAR["pg"]->page['navi']?></td></tr>
	</table>

	</form>
	<br><br>

	</div><!-- End indiv -->
</div>

<?php $this->print_("footer",$TPL_SCP,1);?>