<?
/*------------------------------------------------------------------------------
�� Copyright 2005, Flyfox All right reserved.
@���ϳ���: ������޽���
@��������/������/������:
------------------------------------------------------------------------------*/

{ // ���� ��� �̹���
	$demo_site = array( 'dev2.godo.co.kr', 'enamoofix.godo.co.kr', 'enamooself.godo.co.kr', 'enamooselffix.godo.co.kr', 'enamoofree.godo.co.kr', 'enamoofreefix.godo.co.kr' );
	if ( in_array( $_SERVER['HTTP_HOST'], $demo_site ) ){
		$_COOKIE['shop_authenticate'] = 'Y';
		$demo_img = (strpos($_SERVER['HTTP_HOST'], 'fix') !== false) ? "<img src=\"../admin/img/demo_warning.gif\" />" : "<a href=\"http://" . str_replace('.godo', 'fix.godo', $_SERVER['HTTP_HOST']) . "\" target=\"_blank\"><img src=\"../admin/img/demo_warning2.gif\" border=\"0\" /></a>";
		echo "<div align='center'>$demo_img</div>";
	}
}

if ( $_COOKIE['shop_authenticate'] != 'Y' ) { // � ��Ʈ��

	$shop_authenticate = 'Y'; // ���� �ʱⰪ

	### ���� ȯ���ڵ�
	$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
	if (!is_file($file)) msg("���� ���������� ����ϼ���",1);
	$file = file($file);
	$godo = decode($file[1],1);

	if ( preg_match( "/^rental_mx/i", $godo['ecCode'] ) ){ // �Ӵ���
		if ( $godo['freeType'] == "y" ){
			$g_shopstop_day		= 1;		# �����ȭ�� ���� ������ - ��������
		}else{
			$g_shopstop_day		= 30;		# �����ȭ�� ���� ������ - �Ӵ��������
		}
	}

	### � üũ
	if ( preg_match( "/^rental_mxfree/i", $godo['ecCode'] ) ){ // ������
		$nowDay = betweenDate(date('Ymd'),$godo['edate']);

		if ( $godo['edate'] != 0 && $nowDay <= 0 ){ # ����ȸ�� & �����ȭ�� ���� ����
			$shop_authenticate = 'N'; // ��������
		}
	}else if ( preg_match( "/^rental_mx/i", $godo['ecCode'] ) ) { // �Ӵ���
		$nowDay = betweenDate(date('Ymd'),$godo['edate']);

		if ( $nowDay <= ( 0 - $g_shopstop_day ) ){ # �����ȭ�� ���� ����
			$shop_authenticate = 'N'; // ��������
		}
	}

	### ��Ű ����
	setCookie('shop_authenticate',$shop_authenticate,0,'/');

	### ���� ���� �޽��� ���
	if ( $shop_authenticate == 'N' ){

		$err_msg = "<br>���θ� �̿뿡 ���� ���Ǵ� ";
		if ( $cfg['adminEmail'] ) $err_msg .= "<a href='mailto:" . $cfg['adminEmail'] . "'>" . $cfg['adminEmail'] . "</a>,<br>";
		if ( $cfg['compPhone'] ) $err_msg .= $cfg['compPhone'];
		$err_msg .= " ���� ���ֽñ� �ٶ��ϴ�.<br><br>";
?>
<style><!--
body {margin:0}
body,table {font:12px dotum}
img	{border:0}

a	{text-decoration:none;color:#000000}
a:hover {text-decoration:none;color:#007FC8}
--></style>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
	  <table width="100%" height="501" border="0" cellpadding="0" cellspacing="0">
	    <tr>
	  	  <td>&nbsp;</td>
	    </tr>
	  </table>
	</td>
	<td width="501">
	  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	    <tr>
	  	  <td>
		    <table width="100%" border="0" cellpadding="0" cellspacing="0">
		      <tr>
		    	<td></td>
		      </tr>
			  <tr>
		    	<td height="231" align="center" valign="top" style="padding-top:20px;">���� ���θ� ������ �������� �ʽ��ϴ�.<br>
				<?=$err_msg?>
				</td>
		      </tr>
		    </table>
		  </td>
	    </tr>
	  </table>
	<td>
	  <table width="100%" height="501" border="0" cellpadding="0" cellspacing="0">
	    <tr>
	  	  <td>&nbsp;</td>
	    </tr>
	  </table>
	</td>
  </tr>
</table>

<?
		exit();
	}
}
?>