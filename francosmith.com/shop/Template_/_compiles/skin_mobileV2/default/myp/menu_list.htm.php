<?php /* Template_ 2.2.7 2016/01/09 11:38:41 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/myp/menu_list.htm 000008280 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<?php  $TPL_VAR["page_title"] = "����������";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>


<?php if($GLOBALS["sess"]){?>
<script type="text/javascript">
$(document).ready(function(){

	
	$("section#mypageinfo button.mypageinfo-view-benefit").click(function(){
		
		$("section#mypageinfo article.mypageinfo-detail").fadeIn("fast");
		$("section#mypageinfo #background").fadeIn("fast");
	});
	$("section#mypageinfo .mypageinfo-detail-close").click(function(){
		
		$("section#mypageinfo article.mypageinfo-detail").fadeOut("fast");
		$("section#mypageinfo #background").fadeOut("fast");
	});

	$("#background").click(function(){
		$("section#mypageinfo article.mypageinfo-detail").fadeOut("fast");
		$("section#mypageinfo #background").fadeOut("fast");
	});
});
</script>
<style type="text/css">
section#mypagelist {background:#FFFFFF;}
section#mypageinfo .top_level {height:33px; line-height:33px; background:url('/shop/data/skin_mobileV2/default/common/img/nmyp/bdtit_bg.png') repeat-x; font-size:12px; color:#4d4d4d; padding-left:14px;}
section#mypageinfo .top_level .name{color:#353535; font-weight:bold;}
section#mypageinfo .top_level .level{color:#436693; font-weight:bold;}
.mypageinfo-view-benefit { background:url('/shop/data/skin_mobileV2/default/common/img/nmyp/btn_view_off.png') no-repeat; font-weight:bold; font-size:12px; color:#FFFFFF; width:73px; height:25px; border:none;}
.mypageinfo-view-benefit:active { background:url('/shop/data/skin_mobileV2/default/common/img/nmyp/btn_view_on.png') no-repeat; }


#benefit-layer {
	position : absolute;
	left : 10%;
	width : 80%;
	background : #ffffff;
	display : block;
	border-radius:1em;
	box-shadow:2px 2px 4px #7f7f7f;
	z-index:99;
}

#benefit-layer .benefit_content{padding:16px 18px 25px 14px;}
#benefit-layer .benefit_content .mypageinfo-detail-benefit{margin-left:15px;}

.benefit_close {
	background:#313030;
	width:100%;
	border-bottom-left-radius:1em;
	border-bottom-right-radius:1em;
	height:45px;
	border-bottom:solid 1px #b2b2b2;
	margin-top:6px;
	text-align:center;
	color:#FFFFFF;
	font-size:14px;
	font-weight:bold;
	line-height:45px;
	bottom:0px;
}

#background {
	position : absolute;
	left : 0;
	top : 0;
	width : 100%;
	height : 100%;
	background : rgba(0, 0, 0, 0.2);
	display : none;
	z-index:98;
}


</style>
<section class="content" id="mypageinfo">
	<div class="top_level">
	<span class="name"><?php echo $TPL_VAR["grp_profit"]["name"]?></span>
	<span>���� �����</span>
	<span class="level"><?php echo $TPL_VAR["grp_profit"]["grpnm"]?></span>
	<span>�Դϴ�.</span>
<?php if($TPL_VAR["grp_profit"]["dc_type"]!='N'||$TPL_VAR["grp_profit"]["add_emoney_type"]!='N'||$TPL_VAR["grp_profit"]["free_deliveryfee"]!='N'){?>
	<button class="mypageinfo-view-benefit" type="button">���ú���</button>
<?php }?>
	</div>
	
	<article class="mypageinfo-detail" id="benefit-layer" style="display: none;">
		<div class="benefit_content">
		<div><?php echo $TPL_VAR["grp_profit"]["name"]?> ȸ������ ȸ���׷��� <?php echo $TPL_VAR["grp_profit"]["grpnm"]?>�̽ø�,<br/>�׷������� ������ �����ϴ�.</div>
		<ul class="mypageinfo-detail-benefit">
<?php if($TPL_VAR["grp_profit"]["dc_type"]!=='N'){?>
			<li>
<?php if($TPL_VAR["grp_profit"]["dc_std_amt"]){?><?php echo number_format($TPL_VAR["grp_profit"]["dc_std_amt"])?>�� �̻� ���Ž� <?php }?>
<?php switch($TPL_VAR["grp_profit"]["dc_type"]){case 'goods':?>��ǰ �Ǹűݾ�<?php break;case 'settle_amt':?>�� �����ݾ�<?php }?>�� <?php echo $TPL_VAR["grp_profit"]["dc"]?>%����</br />
			</li>
<?php }?>
		
<?php if($TPL_VAR["grp_profit"]["add_emoney_type"]!='N'){?>
			<li>
<?php if($TPL_VAR["grp_profit"]["add_emoney_std_amt"]){?><?php echo number_format($TPL_VAR["grp_profit"]["add_emoney_std_amt"])?>�� �̻� ���Ž� <?php }?>
<?php switch($TPL_VAR["grp_profit"]["add_emoney_type"]){case 'goods':?>��ǰ �Ǹűݾ�<?php break;case 'settle_amt':?>�� �����ݾ�<?php }?>�� <?php echo $TPL_VAR["grp_profit"]["add_emoney"]?>% �߰� ����<br />
			</li>
<?php }?>
		
<?php if($TPL_VAR["grp_profit"]["free_deliveryfee"]!='N'&&$TPL_VAR["grp_profit"]["free_deliveryfee"]!='Y'){?>
			<li>
<?php switch($TPL_VAR["grp_profit"]["free_deliveryfee"]){case 'goods':?>��ǰ �Ǹűݾ�<?php break;case 'settle_amt':?>�� �����ݾ�<?php }?>
<?php if($TPL_VAR["grp_profit"]["free_deliveryfee_std_amt"]){?><?php echo number_format($TPL_VAR["grp_profit"]["free_deliveryfee_std_amt"])?>�� �̻�<?php }?>			
			�ֹ��� ��ۺ� ����<br />
			</li>
<?php }elseif($TPL_VAR["grp_profit"]["free_deliveryfee"]=='Y'){?>
			<li>��� ��ǰ �ֹ��� ��ۺ� ����</li>
<?php }?>
		</ul>
		</div>
	
		<div class="mypageinfo-detail-close benefit_close">�ݱ�</button>
	</article>
	<div id="background"></div>
</section>
<?php }?>

<section class="content" id="mypagelist">
	<div class="mypagelist_ord">
		<div class="roundbox">
			<div class="m_list" onclick="javascript:location.href='./orderlist.php';"><div class="m_title">�ֹ�����</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?><?php echo number_format($TPL_VAR["data_cnt"]["order"])?> ��<?php }?></div></div>
			<div class="m_list" onclick="javascript:location.href='./emoneylist.php';"><div class="m_title">�����ݳ���</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?><?php echo number_format($TPL_VAR["data_cnt"]["emoney"])?> ����Ʈ<?php }?></div></div>
			<div class="m_list" onclick="javascript:location.href='./couponlist.php';"><div class="m_title">��������</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?>(��밡������ ��) <?php echo number_format($TPL_VAR["data_cnt"]["coupon"])?> ��<?php }?></div></div>
			<div class="m_list" style="border:none;" onclick="javascript:location.href='./wishlist.php';"><div class="m_title">�򸮽�Ʈ</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?> <?php echo number_format($TPL_VAR["data_cnt"]["wish"])?> ��<?php }?></div></div>
		</div>
	</div>
	<div class="mypagelist_mem">
		<div class="roundbox">
			<div class="m_list" onclick="javascript:location.href='./qna.php';"><div class="m_title">1:1���ǳ���</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?><?php echo number_format($TPL_VAR["data_cnt"]["qna"])?> ��<?php }?></div></div>
			<div class="m_list" onclick="javascript:location.href='./review.php';"><div class="m_title">���ǻ�ǰ�ı�</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?><?php echo number_format($TPL_VAR["data_cnt"]["review"])?> ��<?php }?></div></div>
<?php if($GLOBALS["cfg"]["compPhone"]){?>
			<div class="m_list" onclick="javascript:location.href='../goods/goods_qna_list.php';"><div class="m_title">���ǻ�ǰ����</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?><?php echo number_format($TPL_VAR["data_cnt"]["goods_qna"])?> ��<?php }?></div></div>
			<a href="../service/customer.php" target="_self"><div class="m_list" style="border:none;"><div class="m_title">������</div><div class="m_right"></div><div class="m_content"><?php echo $GLOBALS["cfg"]["customerPhone"]?></div></div></a>
<?php }else{?>
			<div class="m_list" onclick="javascript:location.href='../goods/goods_qna_list.php';" style="border:none;"><div class="m_title">���ǻ�ǰ����</div><div class="m_right"></div><div class="m_content"><?php if($GLOBALS["sess"]){?><?php echo number_format($TPL_VAR["data_cnt"]["goods_qna"])?> ��<?php }?></div></div>
<?php }?>
		</div>
	</div>

<?php if($GLOBALS["sess"]){?>
	<div class="mypagelist_mem">
		<div class="roundbox">
			<div class="m_list" onclick="javascript:location.href='../mem/myinfo.php';"><div class="m_title">ȸ����������</div><div class="m_right"></div></div>
			<div class="m_list" onclick="javascript:location.href='../mem/hack.php';"><div class="m_title">ȸ��Ż��</div><div class="m_right"></div></div>
		</div>
	</div>
<?php }?>
</section>
<?php $this->print_("footer",$TPL_SCP,1);?>