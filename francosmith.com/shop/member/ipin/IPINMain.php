<?php
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	$sSiteCode	= $ipin['code'];			// IPIN 憮綠蝶 餌檜お 囀萄		(NICE褐辨ゎ陛薑爾縑憮 嫦晝и 餌檜お囀萄)
	$sSitePw	= $ipin['password'];			// IPIN 憮綠蝶 餌檜お ぬ蝶錶萄	(NICE褐辨ゎ陛薑爾縑憮 嫦晝и 餌檜おぬ蝶錶萄)
	$sEncData	= "";			// 懍���� 脹 等檜顫
	$sRtnMsg	= "";			// 籀葬唸婁 詭撮雖
	
	
	
	/*
	忙 sType 滲熱縑 渠и 撲貲  式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
		等檜顫蒂 蹺轎ж晦 嬪и 掘碟高.
		
		SEQ : 蹂羶廓�� 儅撩
		REQ : 蹂羶 等檜顫 懍����
		RES : 蹂羶 等檜顫 犒����
	戌式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
	*/
	$sType						= "";
	
	
	/*
	忙 sModulePath 滲熱縑 渠и 撲貲  式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
		賅菊 唳煎撲薑擎, '/瞰渠唳煎/賅菊貲' 戲煎 薑曖п 輿敷撿 м棲棻.
		
		+ FTP 煎 賅菊 機煎萄衛 瞪歎⑽鷓蒂 'binary' 煎 雖薑п 輿衛堅, 掏и擎 755 煎 撲薑п 輿撮蹂.
		
		+ 瞰渠唳煎 �挫庣皝�
		  1. Telnet 傳朝 SSH 蕾樓 ��, cd 貲滄橫蒂 檜辨ж罹 賅菊檜 襄營ж朝 夠梱雖 檜翕м棲棻.
		  2. pwd 貲滄橫擊 檜辨ж賊 瞰渠唳煎蒂 �挫恉牮� 熱 氈蝗棲棻.
		  3. �挫庰� 瞰渠唳煎縑 '/賅菊貲'擊 蹺陛煎 薑曖п 輿撮蹂.
	戌式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
	*/
	$self_filename = basename($_SERVER['PHP_SELF']);
	$loc = strpos($_SERVER['PHP_SELF'], $self_filename);
	$loc = substr($_SERVER['PHP_SELF'], 0, $loc);
	$sModulePath = $_SERVER['DOCUMENT_ROOT'].$loc."IPINClient";
	// $sModulePath = $_SERVER['DOCUMENT_ROOT']."/shop/member/ipin/IPINClient";
	
	/*
	忙 sReturnURL 滲熱縑 渠и 撲貲  式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
		NICE褐辨ゎ陛薑爾 で機縑憮 檣隸嫡擎 餌辨濠 薑爾蒂 懍���倆狤� 敝餌煎 葬欐м棲棻.
		評塭憮 懍���音� 唸婁 等檜顫蒂 葬欐嫡戲褒 URL 薑曖п 輿撮蹂.
		
		* URL 擎 http 睡攪 殮溘п 輿敷撿ж貊, 諼睡縑憮紫 蕾樓檜 嶸�褲� 薑爾罹撿 м棲棻.
		* 渡餌縑憮 寡んп萄萼 價Ыむ檜雖 醞, ipin_process.jsp む檜雖陛 餌辨濠 薑爾蒂 葬欐嫡朝 蕨薯 む檜雖殮棲棻.
		
		嬴楚朝 URL 蕨薯檜貊, 敝餌曖 憮綠蝶 紫詭檣婁 憮幗縑 機煎萄 脹 價Ыむ檜雖 嬪纂縑 評塭 唳煎蒂 撲薑ж衛晦 夥奧棲棻.
		蕨 - http://www.test.co.kr/ipin_process.jsp, https://www.test.co.kr/ipin_process.jsp, https://test.co.kr/ipin_process.jsp
	戌式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
	*/
	if($_SERVER['SERVER_PORT'] == 80) {
		$Port = "";
	} elseif($_SERVER['SERVER_PORT'] == 443) {
		$Port = "";
	} else {
		$Port = $_SERVER['SERVER_PORT'];
	}
	if (strlen($Port) > 0) $Port = ":".$Port;
	$Protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

	$host = parse_url($_SERVER['HTTP_HOST']);
	if ($host['path']) {
		$Host = $host['path'];
	} else {
		$Host = $host['host'];
	}

	$sReturnURL = $Protocol.$Host.$Port.$loc."IPINProcess.php";
	if($_GET['callType'] == 'applyipin') $sReturnURL = $Protocol.$Host.$Port.$loc."IPINApply.php";
	
	
	/*
	忙 sCPRequest 滲熱縑 渠и 撲貲  式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
		[CP 蹂羶廓�β煎 敝餌縑憮 等檜顫蒂 歜曖煎 薑曖ж剪釭, 渡餌縑憮 寡ん脹 賅菊煎 等檜顫蒂 儅撩й 熱 氈蝗棲棻. (譆渠 30byte 梱雖虜 陛棟)
		
		CP 蹂羶廓�ㄣ� 檣隸 諫猿 ��, 懍���音� 唸婁 等檜顫縑 л眷 薯奢腎貊
		等檜顫 嬪滲褻 寞雖 塽 か薑 餌辨濠陛 蹂羶и 匙歜擊 �挫恉炱� 嬪и 跡瞳戲煎 檜辨ж褒 熱 氈蝗棲棻.
		
		評塭憮 敝餌曖 Щ煎撮蝶縑 擬辨ж罹 檜辨й 熱 氈朝 等檜顫檜晦縑, в熱高擎 嬴椎棲棻.
	戌式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式式
	*/
	$sCPRequest					= "";
	
	
	
	
	
	$sType		= "SEQ";			// CP 蹂羶廓�� 掘碟高
	
	// 擅憮 撲貲萄萼 夥諦偽檜, CP 蹂羶廓�ㄣ� 寡ん脹 賅菊擊 鱔п 嬴楚諦 偽檜 儅撩й 熱 氈蝗棲棻.
	// 褒ч寞徹擎 諒旋蘭攪(`) 諼縑紫, 'exec(), system(), shell_exec()' 蛔蛔 敝餌 薑疇縑 蜃啪 籀葬ж衛晦 夥奧棲棻.
	$sCPRequest = exec("$sModulePath $sType $sSiteCode");
	
	
	// CP 蹂羶廓�ㄧ� 撮暮縑 盪濰м棲棻.
	// ⑷營 蕨薯煎 盪濰и 撮暮擎 ipin_result.php む檜雖縑憮 等檜顫 嬪滲褻 寞雖蒂 嬪п �挫恉炱� 嬪л殮棲棻.
	// в熱餌о擎 嬴棲貊, 爾寰擊 嬪и 掏堅餌о殮棲棻.
    $_SESSION['CPREQUEST'] = $sCPRequest;
    
    
    
    $sType		= "REQ";			// 等檜顫 懍���� 掘碟高
    
    // 葬欐 唸婁高縑 評塭, Щ煎撮蝶 霞ч罹睡蒂 だ學м棲棻.
    // 褒ч寞徹擎 諒旋蘭攪(`) 諼縑紫, 'exec(), system(), shell_exec()' 蛔蛔 敝餌 薑疇縑 蜃啪 籀葬ж衛晦 夥奧棲棻.
    $sEncData	= exec("$sModulePath $sType $sSiteCode $sSitePw $sCPRequest $sReturnURL");
    
    // 葬欐 唸婁高縑 評艇 籀葬餌о
    if ($sEncData == -9)
    {
    	$sRtnMsg = "殮溘高 螃盟 : 懍���� 籀葬衛, в蹂и だ塭嘐攪高曖 薑爾蒂 薑�旁炾� 殮溘п 輿衛晦 夥奧棲棻.";
    } else {
    	$sRtnMsg = "$sEncData 滲熱縑 懍���� 等檜顫陛 �挫庰К� 薑鼻, 薑鼻檜 嬴棋 唳辦 葬欐囀萄 �挫� �� NICE褐辨ゎ陛薑爾 偃嫦 氬渡濠縑啪 僥曖п 輿撮蹂.";
    }

	$strOrderNo = date("Ymd") . rand(100000000000,999999999999); //輿僥廓�� 20濠葬 .. 衙 蹂羶葆棻 醞犒腎雖 彊紫煙 嶸曖

	// п韁寞雖蒂 嬪п 蹂羶薑爾 撮暮縑 盪濰
	//session_start();		//library縑憮 л.
	$sess_OrderNo = $strOrderNo;
	session_register("sess_OrderNo");
	$_SESSION['sess_OrderNo'] = $strOrderNo;
	session_register("sess_callType");
	$_SESSION['sess_callType'] = $_GET['callType'];

	// �蛾灠㊣埣� 陛殮唳煎陛 賅夥橾檣雖 羹觼, 賅夥橾曖 嬴檜б羹觼縑憮 GET戲煎 陛螳褥
	session_register("joinGubun");
	$_SESSION['joinGubun'] = $_GET['joinGubun'];

	// returnUrl 薑曖
	if ($_REQUEST['returnUrl'] != '') {
		$returnUrl = $_REQUEST['returnUrl'];
	}
	else {
		parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $output);
		$returnUrl = $output['returnUrl'];
	}
?>

<html>
<head>
	<title>NICE褐辨ゎ陛薑爾 陛鼻輿團廓�� 憮綠蝶</title>
	
	<script language='javascript'>
	window.name ="Parent_window";
	
	function fnPopup(){
		document.form_ipin.target = "popupCertKey";
		document.form_ipin.action = "https://cert.vno.co.kr/ipin.cb";
		document.form_ipin.submit();
	}
	</script>
</head>

<body onload="fnPopup();">

<!-- 陛鼻輿團廓�� 憮綠蝶 で機擊 ��轎ж晦 嬪п憮朝 棻擠婁 偽擎 form檜 в蹂м棲棻. -->
<form name="form_ipin" method="post">
	<input type="hidden" name="m" value="pubmain">						<!-- в熱 等檜顫煎, 援塊ж衛賊 寰腌棲棻. -->
    <input type="hidden" name="enc_data" value="<?= $sEncData ?>">		<!-- 嬪縑憮 機羹薑爾蒂 懍���� и 等檜顫殮棲棻. -->
    
    <!-- 機羹縑憮 擬港嫡晦 錳ж朝 等檜顫蒂 撲薑ж晦 嬪п 餌辨й 熱 氈戲貊, 檣隸唸婁 擬港衛 п渡 高擊 斜渠煎 歎褐м棲棻.
    	 п渡 だ塭嘐攪朝 蹺陛ж褒 熱 橈蝗棲棻. -->
    <input type="hidden" name="param_r1" value="<?=urlencode($returnUrl)?>">
    <input type="hidden" name="param_r2" value="">
    <input type="hidden" name="param_r3" value="">
</form>

<!-- 陛鼻輿團廓�� 憮綠蝶 で機 む檜雖縑憮 餌辨濠陛 檣隸擊 嫡戲賊 懍���音� 餌辨濠 薑爾朝 п渡 で機璽戲煎 嫡啪腌棲棻.
	 評塭憮 睡賅 む檜雖煎 檜翕ж晦 嬪п憮朝 棻擠婁 偽擎 form檜 в蹂м棲棻. -->
<form name="vnoform" method="post">
	<input type="hidden" name="enc_data">								<!-- 檣隸嫡擎 餌辨濠 薑爾 懍���� 等檜顫殮棲棻. -->
	
	<!-- 機羹縑憮 擬港嫡晦 錳ж朝 等檜顫蒂 撲薑ж晦 嬪п 餌辨й 熱 氈戲貊, 檣隸唸婁 擬港衛 п渡 高擊 斜渠煎 歎褐м棲棻.
    	 п渡 だ塭嘐攪朝 蹺陛ж褒 熱 橈蝗棲棻. -->
    <input type="hidden" name="param_r1" value="<?=urlencode($returnUrl)?>">
    <input type="hidden" name="param_r2" value="">
    <input type="hidden" name="param_r3" value="">
</form>

</body>
</html>
