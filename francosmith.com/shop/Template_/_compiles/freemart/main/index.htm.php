<?php /* Template_ 2.2.7 2016/04/02 15:10:04 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/main/index.htm 000008749 */  $this->include_("dataPopup","dataBanner","dataDisplayGoods");?>
<?php $this->print_("main_header",$TPL_SCP,1);?>


<!-- �����˾�â --> 
<?php if((is_array($TPL_R1=dataPopup())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?> 

<?php if($TPL_V1["type"]=='layer'&&isset($_COOKIE['blnCookie_'.$TPL_V1["code"]])===false){?>
<div id="<?php echo 'blnCookie_'.$TPL_V1["code"]?>" STYLE="position:absolute; width:<?php echo $TPL_V1["width"]?>px; height:<?php echo $TPL_V1["height"]?>px; left:<?php echo $TPL_V1["left"]?>px; top:<?php echo $TPL_V1["top"]?>px; z-index:200;"> 
	<?php echo eval("\$_GET[code]='blnCookie_".$TPL_V1["code"]."';")?>

	<?php echo $this->define('tpl_include_file_1',$TPL_V1["file"])?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

</div>
<?php }?> 

<?php if($TPL_V1["type"]=='layerMove'&&isset($_COOKIE['blnCookie_'.$TPL_V1["code"]])===false){?> 
<!-- �̵����̾� �˾�â ���� -->
<div id="<?php echo 'blnCookie_'.$TPL_V1["code"]?>" STYLE="position:absolute; width:<?php echo $TPL_V1["width"]?>px; height:<?php echo $TPL_V1["height"]?>px; left:<?php echo $TPL_V1["left"]?>px; top:<?php echo $TPL_V1["top"]?>px; z-index:200;">
	<div onmousedown="Start_move(event,'<?php echo 'blnCookie_'.$TPL_V1["code"]?>');" onmouseup="Moveing_stop();" style='cursor:move;'>
		<table>
			<tr>
				<td> 
					<?php echo eval("\$_GET[code]='blnCookie_".$TPL_V1["code"]."';")?>

					<?php echo $this->define('tpl_include_file_1',$TPL_V1["file"])?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

				</td>
			</tr>
		</table>
	</div>
</div>
<!-- �̵����̾� �˾�â �� --> 
<?php }?> 

<?php }}?> 

<script type="text/JavaScript"><!--
<?php if((is_array($TPL_R1=dataPopup())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<?php if($TPL_V1["type"]==''){?>
if ( !getCookie( "blnCookie_<?php echo $TPL_V1["code"]?>" ) ) { // <?php echo $TPL_V1["name"]?> �˾�ȣ��
var property = 'width=<?php echo $TPL_V1["width"]?>, height=<?php echo $TPL_V1["height"]?>, top=<?php echo $TPL_V1["top"]?>, left=<?php echo $TPL_V1["left"]?>, scrollbars=no, toolbar=no';
var win=window.open( './html.php?htmid=<?php echo $TPL_V1["file"]?>&code=blnCookie_<?php echo $TPL_V1["code"]?>', '<?php echo $TPL_V1["code"]?>', property );
if(win) win.focus();
}
<?php }?>
<?php }}?>
//--></script> 

<!-- ���� ��� ��� ���� -->
<!-- ���� ��� 3����� (��ʰ������� ��������) -->
<div id="main-banner-container">
	<div class="main-banner" id="main-banner-wrapper"> 
		<div class="banner-item" style="display:none;">
			<div class="img-holder">
				<a href="/shop/goods/goods_view.php?goodsno=211&category=033003"><img class="main-banner-img" src="http://francosmith.godohosting.com/main/vicmarc_main_vl300.jpg"></a>
			</div>
			<div class="bar">
				<div class="content">
			  	<div class="text-holder">
							<h1>�������� ���� High-End ������� VICMARC</h1>
							<p class="last">�����԰� ������ ��ũ�� ��� �񼱹�</p>
					</div>
					<!--
					<div class="button-holder">
							<div class="button btn-lg" role="button">Find out more</div>
					</div>
					-->
				</div>
			</div>
		</div>
		
		<div class="banner-item" style="display:none;">
			<div class="img-holder">
				<a href="/shop/goods/goods_view.php?goodsno=211&category=033003"><img class="main-banner-img" src="http://francosmith.godohosting.com/main/kreg_k5_main.jpg"></a>
			</div>
			<div class="bar">
				<div class="content">
			  	<div class="text-holder">
							<h1>XXXXXXXXXXXXXX</h1>
							<p class="last">How good would it feel to get your furniture back into shape?</p>
					</div>
					<!--
					<div class="button-holder">
							<div class="button btn-lg" role="button">Find out more</div>
					</div>
					-->
				</div>
			</div>
		</div>
	</div>
	
	<div class="main-banner-prev">
		<a id="prev_banner" href="javascript:;" ><img border="0" src="/shop/data/skin/freemart/img/main/prev_btn.png" onmouseover="this.src='/shop/data/skin/freemart/img/main/prev_btn_over.png'" onmouseout="this.src='/shop/data/skin/freemart/img/main/prev_btn.png'" ></a>
	</div>
	<div class="main-banner-fwd">
		<a id="next_banner" href="javascript:;" ><img border="0" src="/shop/data/skin/freemart/img/main/fwd_btn.png" onmouseover="this.src='/shop/data/skin/freemart/img/main/fwd_btn_over.png'" onmouseout="this.src='/shop/data/skin/freemart/img/main/fwd_btn.png'"></a>
	</div>
	
	<script src="/shop/lib/js/ierotator.js" type="text/javascript"></script> 
		<script>
		function bannerController(iter) {
				var $j = jQuery.noConflict();	
				$j("#prev_banner").bind("click", function() {
					iter.prevDiv();
				});
				
				$j("#next_banner").bind("click", function() {
					iter.nextDiv();
				});
		}
		
		var fwidth = screen.availWidth;	
		var config = {
		'id':'main-banner-wrapper',
		'effect':'FILTER: progid:DXImageTransform.Microsoft.Fade(Overlap=0.25,Duration=0.7)',
		'width':'100%',
		'height':'auto',
		'wait':5000,
		'numDisplay':'block',
		'numimg':[
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		['/shop/data/skin/freemart/img/main/main_rolling_on.png','/shop/data/skin/freemart/img/main/main_rolling_off.png'],
		]
		}
		ier = new ierotator(config);
		var banner_navi = new bannerController(ier);
		
		</script> 
		
</div>
	

<div id="main_contents" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px">
	<div id="three_banner">
		<div><?php if((is_array($TPL_R1=dataBanner( 11))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
		<div><?php if((is_array($TPL_R1=dataBanner( 12))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
		<div><?php if((is_array($TPL_R1=dataBanner( 13))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
	</div>
	<div id="best_item">
		<div class="title-block">
			<div class="title"><a href="<?php echo url("goods/goods_grp_01.php")?>&"><h2><span style="color:#C9242B">BEST</span> SELLERS</h2></a></div>
			<div class="more_link"><a href="<?php echo url("goods/goods_grp_01.php")?>&"><img src="/shop/data/skin/freemart/img/main/icon_more.gif"></a></div>
		</div>
		<div class="contents">
<?php if($GLOBALS["cfg_step"][ 0]["chk"]){?> 
		<?php echo $this->assign('loop',dataDisplayGoods( 0,$GLOBALS["cfg_step"][ 0]["img"],$GLOBALS["cfg_step"][ 0]["page_num"]))?>

		<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 0]["cols"])?>

		<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 0]["img"]])?>

		<?php echo $this->assign('id',"main_list_01")?>

		<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 0])?>

		<?php echo $this->define('tpl_include_file_3',"goods/list/".$GLOBALS["cfg_step"][ 0]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_3",$TPL_SCP,1);?>

<?php }?> 
		</div>
	</div>
	
</div>

<?php $this->print_("footer",$TPL_SCP,1);?>