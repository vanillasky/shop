<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/main/intro_adult.htm 000005292 */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $GLOBALS["meta_title"]?></title>
<script src="/shop/data/skin/campingyo/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
<style>
div.head {height:240px;text-align:center;}
div.head h2 {display:none;}
div.head p {display:block;background:url(/shop/data/skin/campingyo/img/auth/img_log_top.gif) no-repeat center center;height:200px;width:680px;text-indent:-5000px;margin:0 auto;}

div.body {width:680px;margin:0 auto 20px;overflow:hidden;border:none;}
div.body h3 {font:bold 12px dotum;color:#f7443f;background:url('/shop/data/skin/campingyo/img/auth/bullet_log_tit.gif') no-repeat top left;padding:0 10px;margin:15px 0; text-align:left;}
div.body div.forms-wrap {background:#F3F3F3;border:1px solid #DEDEDE;padding:5px;width:660px;margin:20px auto 0;}

div.body div.forms-wrap div.forms {background:#fff;}
div.body div.forms-wrap div.forms div.form {width:640px;padding:10px;display:inline-block;vertical-align:top; text-align:center;}
div.body div.forms-wrap div.forms div.form h4 {font:bold 11px dotum;color:#434343;background:url('/shop/data/skin/campingyo/img/auth/bullet_log_stit.gif') no-repeat top left;padding:0 10px;margin:5px 0; text-align:left;}

div.body div.forms-wrap div.forms div.form table {}
div.body div.forms-wrap div.forms div.form table th {font-weight:bold;color:#5D5D5D;font-size:11px;letter-spacing:-1px;width:60px;text-align:left;}
div.body div.forms-wrap div.forms div.form table td {}

div.body div.forms-wrap div.forms div.form #ipinyn {padding:5px;font-size:11px; line-height:150%;letter-spacing:-1px; color:#5d5d5d;display:none}
div.body div.forms-wrap div.forms div.form label {cursor:pointer;}
div.body div.forms-wrap div.forms div.form input.fld {border:1px solid #DEDEDE;height:18px;}

div.body p.info {line-height:150%;padding:30px 0; width:580px; margin:0 auto; font-size:11px; letter-spacing:-1px; color:#5d5d5d;text-align:left;}

div.foot {border-top:1px solid #e6e6e6;margin:0 auto;width:680px;padding:10px 100px 0 100px;}
div.foot ul.company {list-style:none; margin-bottom:5px;}
div.foot ul.company li {padding:3px;}
div.foot ul.company li span.divi {color:#cecece;}


div.foot p.copyright {text-align:center;}

</style>


<!--
<!--	==========================================================	-->
<!--	�ѱ��ſ������ֽ�ȸ�� ó�� ��� (���� �� �������� ���ʽÿ�)	-->
<!--	==========================================================	-->
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.msg.js"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>
</head>

<body>

<div class="head">
	<h2>19�� �̸��� �̼����ڴ� ������ ���մϴ�.</h2>
	<p>
	�� ���������� û�ҳ� ���ظ�ü���μ� ������Ÿ� �̿����� �� ������ȣ� ���� ���� �� û�ҳ� ��ȣ���� ������ ���Ͽ� 19�� �̸��� û�ҳ��� �̿��� �� �����ϴ�.
	</p>

	<a href="javascript:history.back();"><img src="/shop/data/skin/campingyo/img/btn_19out.gif" alt="19�� �̸� ������" /></a>
</div>

<div class="body">
	<h3>���� �����ϱ�</h3>
	<div class="forms-wrap">

		<div class="forms">

<?php $this->print_("intro_auth",$TPL_SCP,1);?>

			<div style="clear:both;"></div>

		</div>

	</div><!-- class=forms-wrap -->
</div><!-- class=body -->


<div class="foot">

	<div class="logo"><a href="<?php echo url("main/main/main.php")?>&"><?php echo $logoImage;?></a></div>

	<ul class="company">
		<li>
<?php if($GLOBALS["cfg"]['compName']){?>��ȣ�� : <?php echo $GLOBALS["cfg"]['compName']?><?php }?>
<?php if($GLOBALS["cfg"]['compSerial']){?> <span class="divi"> | </span> ����ڵ�Ϲ�ȣ : <?php echo $GLOBALS["cfg"]['compSerial']?><?php }?>
<?php if($GLOBALS["cfg"]['orderSerial']){?> <span class="divi"> | </span> ����Ǹž��Ű��ȣ : <?php echo $GLOBALS["cfg"]['orderSerial']?><?php }?>
		</li>
		<li>
<?php if($GLOBALS["cfg"]['ceoName']){?>��ǥ : <?php echo $GLOBALS["cfg"]['ceoName']?> <?php }?>
<?php if($GLOBALS["cfg"]['adminName']){?> <span class="divi"> | </span> ������������å���� : <?php echo $GLOBALS["cfg"]['adminName']?>

<?php if($GLOBALS["cfg"]['adminEmail']){?>(<a href="javascript:popup('./proc/popup_email.php?to=<?php echo $GLOBALS["cfg"]['adminEmail']?>&hidden=1',650,600)"><?php echo $GLOBALS["cfg"]['adminEmail']?></a>)
<?php }?>
<?php }?>
		</li>
		<li>
<?php if($GLOBALS["cfg"]['address']){?>�ּ� : <?php echo $GLOBALS["cfg"]['address']?> <?php }?>
<?php if($GLOBALS["cfg"]['compPhone']){?> <span class="divi"> | </span> ��ǥ��ȣ : <?php echo $GLOBALS["cfg"]['compPhone']?><?php }?>
<?php if($GLOBALS["cfg"]['compFax']){?> <span class="divi"> | </span> �ѽ���ȣ : <?php echo $GLOBALS["cfg"]['compFax']?> <?php }?>
		</li>
	</ul>

	<p class="copyright">
	Copyright �� <strong><?php echo $GLOBALS["cfg"]['shopUrl']?></strong> All right reserved
	</p>

</div>

</body>
</html>