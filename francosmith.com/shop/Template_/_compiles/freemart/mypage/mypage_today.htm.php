<?php /* Template_ 2.2.7 2016/04/10 14:50:46 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_today.htm 000006828 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<div class="page_title_div">
	<div class="page_title">�ֱٺ���ǰ</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">����������</a> &gt; <span class='bold'>�ֱٺ���ǰ</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>

<div class="indiv"><!-- Start indiv -->
	<form name=frmList>
	<input type=hidden name=searched value="Y">
	<input type=hidden name=sort value="<?php echo $_GET['sort']?>">
	<input type=hidden name=page_num value="<?php echo $_GET['page_num']?>">

	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td bgcolor=9e9e9e class=small height=27 style="padding:0 0 0 5">
			<table width=100% border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td id="b_white" align="left"><span class="f_white f_indent">�� <b><?php echo $TPL_VAR["pg"]->recode['total']?></b>���� ��ǰ�� �ֽ��ϴ�.</span></td>
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
					
					<div class="sort_area" style="text-align:left">
						<ul id="sort_ul" class="sort_list">
							<li class="" id="sort_price"><a href="javascript:sort('price')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_price_desc"><a href="javascript:sort('price desc')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_goodsnm_desc"><a href="javascript:sort('goodsnm desc')"><span></span>��ǰ���</a></li>
							<li class="" id="sort_goodsnm"><a href="javascript:sort('goodsnm')"><span></span>��ǰ���</a></li>
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
					
					<div class="sort_area" style="text-align:left">
						<ul id="sort_ul" class="sort_list">
							<li class="" id="sort_price"><a href="javascript:sort('price')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_price_desc"><a href="javascript:sort('price desc')"><span></span>���� ���ݼ�</a></li>
							<li class="" id="sort_goodsnm_desc"><a href="javascript:sort('goodsnm desc')"><span></span>��ǰ���</a></li>
							<li class="" id="sort_goodsnm"><a href="javascript:sort('goodsnm')"><span></span>��ǰ���</a></li>
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

<?php $this->print_("footer",$TPL_SCP,1);?>