<?php /* Template_ 2.2.7 2016/04/30 15:28:45 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/header/standard.htm 000004319 */  $this->include_("dataBanner");?>
<a name="top"></a>
<div id="shop_top_area">
	<div id="top_area_menu">
		<ul id="top_links" class="ul_right">
			
<?php if(!$GLOBALS["sess"]){?>
			<li><a href="<?php echo url("member/login.php")?>&">�α���</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("member/join.php")?>&">ȸ������</a></li>
			<li class="top_ul_line">|</li>
<?php }else{?>
			<li><a href="<?php echo url("member/logout.php")?>&">�α׾ƿ�</a></li>
			<li class="top_ul_line">|</li>
<?php }?>
			<li><a href="<?php echo url("mypage/mypage.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?>>����������</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("mypage/mypage_orderlist.php")?>&">�ֹ�/���</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("service/customer.php")?>&">������</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="/shop/goods/goods_list.php?&category=029002" class="top_ul_font">���ΰ���</a>
			</li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("goods/goods_cart.php")?>&">��ٱ���(<?php echo $GLOBALS["sess"]["cart_count"]?>)</a></li>
			<li class="top_ul_line">
				<a href="#" id="contact"><span>1599-7835<span></a>	
			</li>
		</ul>
	</div>
</div>

<div id="header_main">
	<div id="top_1">
		<div id="top_board_layer" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
			<div id="top_logo"><!-- ��ʰ������� �������� --><?php if((is_array($TPL_R1=dataBanner( 90))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>	
			<div id="top_search">
				<script>
				function add_param_submit (param_nm, param_val){
					var frm = document.searchForm;
					if( param_nm == 'sword') frm.sword.value = param_val;
					else frm.disp_type.value = param_val;
					frm.submit();
				}
				</script>
				<!-- �˻� ����----------------------------------->
				<form name="searchForm" action="<?php echo url("goods/goods_search.php")?>&" onsubmit="return chkForm(this)">
				<input type=hidden name=searched value="Y">
				<input type=hidden name=log value="1">
				<input type=hidden name=skey value="all">
				<input type="hidden" name="hid_pr_text" value="<?php echo $GLOBALS["s_type"]['pr_text']?>" />
				<input type="hidden" name="hid_link_url" value="<?php echo $GLOBALS["s_type"]['link_url']?>" />
				<input type="hidden" id="edit" name="edit" value=""/>
<?php if($GLOBALS["s_type"]['keyword_chk']=='on'&&$GLOBALS["s_type"]['pr_text']&&!$_GET['sword']){?>
				<?php
					 $TPL_VAR["id"] = "sword";
					 $TPL_VAR["onkeyup"] = "document.getElementById('edit').value='Y'";
					 $TPL_VAR["onclick"] = "document.getElementById('sword').value=''";
					 $TPL_VAR["value"] =  $GLOBALS["s_type"]['pr_text'];
				?>
<?php }else{?>
				<?php
					 $TPL_VAR["value"] =  $_GET['sword'];
				?>
<?php }?>
				<div id="top_keywords">
					<span><?php if($GLOBALS["s_type"]['keyword']){?>�α�˻���: <?php echo $GLOBALS["s_type"]['keyword']?><?php }?></span>	
				</div>
				<table cellpadding="0" cellspacing="0" border="0" class="search_table">
				<tr>
					<td class="search_td"><input name="sword" type=text id="<?php echo $TPL_VAR["id"]?>" class="search_input" onkeyup="<?php echo $TPL_VAR["onkeyup"]?>" onclick="<?php echo $TPL_VAR["onclick"]?>" value="<?php echo $TPL_VAR["value"]?>" required label="�˻���"></td>
					<td class="search_btn_top top_red"><button type="submit" title="Search" class="button search-button"></td>
				</tr>
				</table>
				</form>
				<!-- �˻� ��-------------------------------------->
			</div>
			<div id="top_right">
			</div>
		</div>
	</div>
	

	<!-- ī�װ� �޴� ���� -->
	<!-- ���� ���μҽ��� '��Ÿ/�߰�������(proc) > ī�װ��޴�- menuCategory.htm' �ȿ� �ֽ��ϴ� -->
<?php $this->print_("menuCategory",$TPL_SCP,1);?>

	<!-- ī�װ� �޴� �� -->
			
</div>


		
<!-- ������� ���� -->