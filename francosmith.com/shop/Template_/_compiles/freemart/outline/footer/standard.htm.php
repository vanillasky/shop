<?php /* Template_ 2.2.7 2016/05/12 10:46:15 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/footer/standard.htm 000004730 */  $this->include_("dataBank");?>
<div id="footer_wrapper" class="bottom_grey" align="center" >
	<div style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px; padding:0; margin:0" > 
		<div class="footer-block" style="width:220px;">
			<div class="block-title">
				<span>�Աݾȳ�</span>
			</div>
			<div class="content-wrapper">
<?php if((is_array($TPL_R1=dataBank())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
				<span class="footer-content"><?php echo $TPL_V1["bank"]?></span>
				<span class="footer-content"><?php echo $TPL_V1["account"]?></span>
				<span class="footer-content">������ <?php echo $TPL_V1["name"]?></span>
<?php }}?> 
			</div>
			
		</div>
		
		<div class="footer-block">
			<div class="block-title">
				<span>���/����</span>
			</div>
			<div class="footer-phone">
				<span>TEL. <?php echo $GLOBALS["cfg"]['compPhone']?></span>
				<span style="margin-left:10px;">FAX. <?php echo $GLOBALS["cfg"]['compFax']?></span>
			</div>
			<div class="footer-biz-time">
				<ul>
						<li>���ð�: AM 9:30 ~ PM 6:00 (��~��)</li>
				</ul>
			</div>
		</div>
		
		
		<div class="footer-block social-footer">
			<div class="block-title">
				<span style="padding-left:10px;">Keep In Touch</span>
			</div>
			<ul>
				<li><a id="facebook" target="_blank" href="#"></a></li>
				<li><a id="twitter" target="_blank" href="#"></a></li>
				<li><a id="googleplus" target="_blank" href="#"></a></li>
				<li><a id="pinterest" target="_blank" href="#""></a></li>
				<li><a id="instagram" target="_blank" href="#"/"></a></li>
				<!--<li><a id="youtube" target="_blank" href="#""></a></li>-->
			</ul>
		</div>
		
		
	</div>	
	
	<p style="clear:both; height:10px;padding:0; margin:0">&nbsp;</p>
	<hr class="line-white">
	
	<div style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px; padding:0; margin:0;" > 	
		<div class="footer-block" style="width:220px;">
				<div class="block-title">
					<span>YOUR ORDER</span>
				</div>
				<div class="footer-biz-time">
					<ul>
							<li class="quick-link"><a href="/shop/mypage/mypage.php">My Account</a></li>
							<li class="quick-link"><a href="/shop/mypage/mypage_orderlist.php">�ֹ���ȸ</a></li>
							<li class="quick-link"><a href="https://www.doortodoor.co.kr/parcel/pa_004.jsp" target="new">�����ȸ</a></li>
							<li class="quick-link"><a href="/shop/service/customer.php">������</a></li>
							<li class="quick-link"><a href="/shop/main/html.php?htmid=service/return_policy.htm">���/��ǰ�ȳ�</a></li>
					</ul>
				</div>
		</div>
		
		
		<div class="footer-block">
				<div class="block-title">
					<span>THE KNOWLEDGE</span>
				</div>
				<div class="footer-biz-time">
					<ul>
							<li class="quick-link"><a href="http://blog.naver.com/francosmith">��α�</a></li>
							<li class="quick-link"><a href="/shop/board/list.php?id=qna">Q&amp;A</a></li>
							<li class="quick-link"><a href="http://francosmith.com/shop/service/faq.php?&faq_sword=">FAQ</a></li>
							<li class="quick-link"><a href="/shop/board/list.php?id=bbs">�ڷ��</a></li>
							<li class="quick-link"><a href="/shop/board/list.php?id=buyingguide">���Ű��̵�</a></li>
							<li class="quick-link"><a href="/shop/board/list.php?id=notice">��������</a></li>
					</ul>
				</div>
		</div>
	</div>
</div>

<div style="width:100%; padding:0; margin:0 auto;" align="center" >
<div style="width:990px; background-color:#fff" > 	
		
		<div id="footer_company">
			<p class="line_h20">
				<span class="txt">ȸ��:</span><?php echo $GLOBALS["cfg"]['compName']?><span class="bar"></span>
				<span class="txt">��ǥ:</span><?php echo $GLOBALS["cfg"]['ceoName']?> <span class="bar"></span> 
				<span class="txt">����ڵ�Ϲ�ȣ:</span><?php echo $GLOBALS["cfg"]['compSerial']?> <a href="http://www.ftc.go.kr/info/bizinfo/communicationList.jsp" target="_blank">
				[��ȸ]</a><span class="bar"></span>
				<span class="txt">����Ǹž��Ű�:</span><?php echo $GLOBALS["cfg"]['orderSerial']?><span class="bar"></span> 
				<!--<span class="txt">��������������:</span><?php echo $GLOBALS["cfg"]['adminName']?> -->
				<span class="txt">�ּ�:</span><?php echo $GLOBALS["cfg"]['address']?> <span class="bar"></span>
				<p>
					<span style="clear:both;">COPYRIGHT �� <script type="text/javascript">document.write(new Date().getFullYear())</script> FRANCOSMITH.COM ALL RIGHTS RESERVED</span>
				</p>
			</p>
			
		</div>
</div>
</div>