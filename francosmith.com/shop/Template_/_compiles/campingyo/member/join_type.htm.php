<?php /* Template_ 2.2.7 2016/04/12 04:57:55 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/member/join_type.htm 000004137 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '955993964516026',
      xfbml      : true,
      version    : 'v2.5'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_join.gif" border="0"></td>
</tr>
<tr>
	<td class="path">HOME > ȸ������ > <strong>���Թ������</strong></td>
</tr>
</table>

<style type="text/css">
div.join-member-title {
	margin-left: 20px;
	margin-top: 5px;
	margin-bottom: 5px;
	background: url('/shop/data/skin/campingyo/img/join_title_01.gif') no-repeat;
	height: 23px;
	font-size: 0;
}
div.outer-border {
	margin-left: 20px;
	width: 650px;
	overflow: hidden;
	border: 1px solid #dedede;
}
div.inner-border {
	border: 5px solid #f3f3f3;
	padding: 30px;
	overflow: hidden;
}
div.join-shop {
	float: left;
	width: 300px;
}
div.join-shop div.join-shop-title {
	height: 12px;
	background: url('/shop/data/skin/campingyo/img/sns_join_01.gif') no-repeat;
	font-size: 0;
}
div.join-shop div.join-shop-select {
	margin-left: 20px;
	margin-top: 20px;
}
div.join-shop div.join-shop-select button.btn-join-shop {
	width: 219px;
	height: 54px;
	background: url('/shop/data/skin/campingyo/img/btn_shop_join.gif') no-repeat;
	border: none;
	font-size: 0;
}
div.join-shop div.join-shop-select div.join-shop-option {
	margin-top: 10px;
}
div.join-shop div.join-shop-select div.join-shop-option span {
	font: 12px gulim;
	color: #666666;
}
div.join-shop div.join-shop-select div.join-shop-option a.login-shop {
	font-weight: bold;
	color: #f7443f;
	text-decoration: underline;
}
div.join-sns {
	float: left;
	width: 280px;
}
div.join-sns div.join-sns-title {
	height: 12px;
	background: url('/shop/data/skin/campingyo/img/sns_join_02.gif') no-repeat;
	font-size: 0;
}
div.join-sns div.join-sns-select {
	margin-left: 20px;
	margin-top: 20px;
}
button.btn {
	cursor: pointer;
	border: none;
	font-size: 0;
}
div.join-sns div.join-sns-select div.sns-btn-group button.btn-facebook {
	width: 125px;
	height: 31px;
	background: url('/shop/data/skin/campingyo/img/login_sns_facebook.gif') no-repeat;
}
div.join-sns div.join-sns-select div.join-sns-description {
	margin-top: 10px;
	font: 11px dotum;
	color: #666666;
	line-height: 15px;
}
</style>

<div class="join-member-title">ȸ������</div>

<div class="hundred outer-border">
	<div class="inner-border">
		<!-- ���θ� �������� ȸ������ -->
		<div class="join-shop">
			<div class="join-shop-title">���θ� �������� ȸ������</div>
			<div class="join-shop-select">
				<button class="btn btn-join-shop" onclick="location.href='<?php echo url("member/join.php?")?>&MODE=agreement';">���θ� ȸ������</button>
				<div class="join-shop-option">
					<span>�̹� ���θ� ȸ���̼���?</span>
					<a href="<?php echo url("member/login.php")?>&" class="login-shop">�α���</a>
				</div>
			</div>
		</div>
		
		<!-- SNS �������� ȸ������ -->
		<div class="join-sns">
			<div class="join-sns-title">SNS �������� ȸ������</div>
			<div class="join-sns-select">
				<div class="sns-btn-group">
<?php if($TPL_VAR["FacebookLoginURL"]){?>
					<button class="btn btn-facebook" onclick="popup('<?php echo $TPL_VAR["FacebookLoginURL"]?>', 400, 300);">���̽���</button>
<?php }?>
				</div>
				<div class="join-sns-description">
					SNS������ �����Ͽ� ������ ���� �����ϰ� ȸ������ �� �� �ֽ��ϴ�. �� �������� ������ �����ʹ� ö���ϰ� ��ȣ�˴ϴ�.
				</div>
			</div>
		</div>
	</div>
</div>



<?php $this->print_("footer",$TPL_SCP,1);?>