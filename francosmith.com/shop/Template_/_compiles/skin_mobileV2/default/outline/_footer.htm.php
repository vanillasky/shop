<?php /* Template_ 2.2.7 2013/11/26 16:49:10 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/outline/_footer.htm 000002367 */ ?>
<section id='footer' class='content' style='padding-bottom:70px;'>
<?php if($GLOBALS["mainpage"]){?>
	<div class='button'>
		<a href="<?php echo $GLOBALS["cfg"]["rootDir"]?>/?pc" title="PC�������� ����" class="btn_pcmode"><span class="hidden">PC�������� ����</span></a>
	</div>
<?php }?>
	<div class='company'>
		<div class="bottom_menu">
			<div class="bottom_menu_contents">
				<div class="bottom_menu_left">
					<a href="<?php echo $GLOBALS["mobileRootDir"]?>/proc/faq.php">FAQ</a>
					<div class="bar_area"><img src="/shop/data/skin_mobileV2/default/common/img/bottom/menubar.png" /></div>
				</div>
				<div class="bottom_menu_center">
					<a href="<?php echo $GLOBALS["mobileRootDir"]?>/proc/guide.php">�̿�ȳ�</a>
					<div class="bar_area"><img src="/shop/data/skin_mobileV2/default/common/img/bottom/menubar.png" /></div>
				</div>
				<div class="bottom_menu_right">
					<a href="<?php echo $GLOBALS["cfg"]["rootDir"]?>/?pc" title="PC�������� ����" class="btn_pcmode">PC����</a>
				</div>
			</div>
		</div>
		<div class='lineinfo'>
			<a href="<?php echo $GLOBALS["mobileRootDir"]?>/service/agrmt.php">�̿���</a>  |  <a href="<?php echo $GLOBALS["mobileRootDir"]?>/service/private.php" style="font-weight:bold;">����������޹�ħ</a>
		</div>
		<div class='lineinfo'>
			<?php echo $GLOBALS["cfg"]["shopName"]?> | ��ǥ�̻�:<?php echo $GLOBALS["cfg"]["ceoName"]?>

		</div>
		<div class='lineinfo'>
			�ּ�:<?php echo $GLOBALS["cfg"]["address"]?>

		</div>
		<div class='lineinfo'>
			����ڹ�ȣ:<?php echo $GLOBALS["cfg"]["compSerial"]?> | ����Ǹž��Ű�:<?php echo $GLOBALS["cfg"]["orderSerial"]?>

		</div>
		<div class='lineinfo'>
			������: <?php echo $GLOBALS["cfg"]["compPhone"]?>

		</div>
	</div>

	<div class="copyright" style='padding-top:5'>
		<div class='lineinfo'>COPYRIGHT (C) <span style='font-weight:bold;color:#600;'><?php echo $GLOBALS["cfg"]["shopName"]?></span> ALL RIGHTS RESERVED.</div>
		<div class='lineinfo'>SYSTEM BY <span style='color:blue'>Godo</span>Mall</div>
	</div>
</section>

</div>
<iframe class="" id="ifrmHidden" name="ifrmHidden" src='<?php echo $GLOBALS["cfg"]["rootDir"]?>/blank.php'></iframe>
</body>
</html>