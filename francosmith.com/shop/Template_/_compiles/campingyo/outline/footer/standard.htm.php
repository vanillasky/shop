<?php /* Template_ 2.2.7 2014/04/02 01:49:33 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/footer/standard.htm 000006195 */  $this->include_("dataBanner","displaySSLSeal","displayEggBanner");?>
<table cellpadding='0' cellspacing='0' border='0' width='100%' height="172" style="border-top:solid 1px #353535; border-bottom:solid 1px #d5d5d5">
	<tr>
		<td valign="top" height="36" style="padding-top:3px; background-color:#f7f7f7; border-bottom:solid 1px #d5d5d5;">
			<table border='0' cellpadding='0' width="<?php echo $GLOBALS["cfg"]['shopSize']?>" align="<?php echo $GLOBALS["cfg"]['shopAlign']?>" cellspacing='0' style="font-size:12px;" >
				<tr>
					<td height="36">
						<div style="line-height:17px;"> <a href="<?php echo url("service/company.php")?>&" style="color:#333;">ȸ��Ұ� </a> <font color="#333"> | </font> <a href="<?php echo url("service/agreement.php")?>&" style="color:#333;">�̿���</a> <font color="#333"> | </font> <a href="<?php echo url("service/private.php")?>&" style="color:#333;"><strong>����������޹�ħ</strong></a> <font color="#333"> | </font> <a href="<?php echo url("service/guide.php")?>&" style="color:#333;">�̿�ȳ�</a> <font color="#333"> | </font> <a href="<?php echo url("service/cooperation.php")?>&" style="color:#333;">���޾ȳ�</a> <font color="#333"> | </font> <a href="<?php echo url("service/sitemap.php")?>&" style="color:#333;">����Ʈ��</a> </div>
					</td>
					<td align="right"><a href="#top"><img src="/shop/data/skin/campingyo/img/main/btn_top.gif"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table border='0' cellpadding='0' width="<?php echo $GLOBALS["cfg"]['shopSize']?>" align="<?php echo $GLOBALS["cfg"]['shopAlign']?>" cellspacing='0' style="font-size:12px;" >
				<tr>
					<td style="padding-top:10px;"> 
						<!------------ �ϴ� ī�Ƕ���Ʈ  ���� --------------->
						<table width='100%' border='0'>
							<tr>
								<td style="font-size:12px; color:#666;">
									<div style="line-height:17px;">
									<font color='#33a5c0'>ȸ��</font> <?php echo $GLOBALS["cfg"]['compName']?>

									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>�ּ�</font> <?php echo $GLOBALS["cfg"]['address']?>

									</div>
									<div style="line-height:17px;"> 
									<font color='#33a5c0'>��ǥ</font> <?php echo $GLOBALS["cfg"]['ceoName']?>

									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>����ڵ�Ϲ�ȣ</font> <?php echo $GLOBALS["cfg"]['compSerial']?> <a href="http://www.ftc.go.kr/info/bizinfo/communicationList.jsp" target="_blank"><img src="/shop/data/skin/campingyo/img/common/btn_business.gif" style="vertical-align:top;"></a>
									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>����Ǹž��Ű��ȣ</font> <?php echo $GLOBALS["cfg"]['orderSerial']?>

									<!--<font color="#e0e0e0"> | </font> -->
									</div>
									
									<div style="line-height:17px;"> 
									<font color='#33a5c0'>��ȭ��ȣ</font> <?php echo $GLOBALS["cfg"]['compPhone']?>

									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>�ѽ���ȣ</font> <?php echo $GLOBALS["cfg"]['compFax']?>

									<!--<font color="#e0e0e0"> | </font> -->
									
									</div>
									<div style="line-height:17px;">
										<font color='#33a5c0'>��������������</font> <?php echo $GLOBALS["cfg"]['adminName']?>

										<font color='#33a5c0'>����</font> <a href="javascript:popup('../proc/popup_email.php?to=<?php echo $GLOBALS["cfg"]['adminEmail']?>&hidden=1',650,600)"><?php echo $GLOBALS["cfg"]['adminEmail']?></a> 
									</div>
									<!--
									<div style="line-height:17px;"> 
									<font color='#33a5c0'>ȣ��������</font> (��)������Ʈ 
									<img src="/shop/data/skin/campingyo/img/common/footer_godomall.png" style="vertical-align:middle;">
									</div>
									<div style="line-height:20px;"> Copyright �� <b><?php echo $GLOBALS["cfg"]['shopUrl']?></b> All right reserved </div>
									-->
								</td>
								<td><!-- KB����ũ�� ��ü ������ũ ���� ���� -->
									<script>
									function onPopKBAuthMark()
									{
									window.open('','KB_AUTHMARK','height=604, width=648, status=yes, toolbar=no, menubar=no,location=no');
									document.KB_AUTHMARK_FORM.action='http://escrow1.kbstar.com/quics';
									document.KB_AUTHMARK_FORM.target='KB_AUTHMARK';
									document.KB_AUTHMARK_FORM.submit();
									}
									</script>
									<form name="KB_AUTHMARK_FORM" method="get">
									<input type="hidden" name="page" value="B009111"/>
									<input type="hidden" name="cc" value="b010807:b008491"/>
									<input type="hidden" name="mHValue" value='6dd1f98de45d450a592f0aa96b9c7784201403211310930'/>
									</form>
									<a href="#" onclick="javascript:onPopKBAuthMark();return false;">
									<img src="http://img1.kbstar.com/img/escrow/escrowcmark.gif" border="0"/>
									</a>
									<!-- KB����ũ����ü ������ũ ���� ���� -->
								</td>
								<!------------------ ��ܷΰ� ���� ------------------->
								<td align="right"><!-- ��ʰ������� �������� --><?php if((is_array($TPL_R1=dataBanner( 91))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
								<!------------------ ��ܷΰ� �� -------------------> 
							</tr>
						</table>
						<!------------ �ϴ� ī�Ƕ���Ʈ  �� ---------------> 
					</td>
					<!------------------ SSL seal ���� -------------------> 
					<?php echo displaySSLSeal()?>

					<!------------------ SSL seal �� -------------------> 
					<!------------------ ���ž������� ǥ�� ���� -------------------> 
					<?php echo displayEggBanner()?>

					

					<!------------------ ���ž������� ǥ�� �� -------------------> 
					<!------------------ ���� iPay Logo ǥ�� ���� -------------------> 
					<?php echo auctionIpayLogo()?>

					<!------------------ ���� iPay Logo ǥ�� �� -------------------> 
				</tr>
			</table>
		</td>
	</tr>
</table>