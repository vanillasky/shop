<?
	include_once "../lib/library.php";
	include "../conf/naverCheckout.cfg.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>���θ� ȸ�� Ȯ�� :: �������� ��������</title>
<link type="text/css" rel="stylesheet" href="./naver_material/css/checkout_store.css">
<script type="text/javascript" src="./naver_material/js/checkout_store.js"></script>
</head>

<body>
<div id="pop_wrap" class="w410">
<form name="agrForm" method="post" action="indb.naver.php" onsubmit="return checkAgreeForm(this)">
	<input type="hidden" name="mode" id="mode" value="infoSupplyAgreement" />
	<div id="pop_header">
		<h1><img src="./naver_material/img/store/h1_shopconfirm.gif" width="99" height="16" alt="���θ� ȸ��Ȯ��"></h1>
		<dl class="store_logo">
		<dt><img src="./naver_material/img/store/text.gif" width="40" height="13" alt="���θ� :"></dt>
		<dd><a href="http://<?=$_SERVER['HTTP_HOST']?>" target="_blank"><img src="<?=$_SESSION['NCINFO']['NCMallLogo']?>" width="93" height="25" alt="<?=$_SESSION['NCINFO']['NCMallName']?>" /></a></dd>
		</dl>
	</div>
	<div id="pop_content">
		<dl class="confirm_text2">
		<dt class="blind">�������� ������ ������ �ּ���!</dt>
		<dd class="blind">üũ�ƿ� ���θ� ȸ�� �����̿��� ���� �������� ������ �������ּ���.</dd>
		</dl>
		<div class="section3">
			<dl>
			<dt><img src="./naver_material/img/store/text_agree.gif" width="108" height="11" alt="�����ĺ� ���� ���� �ȳ�"></dt>
			<dd>
				<p class="lh16"><?=$cfg['compName']?>������ ȸ������ ���� ������ ����, ȸ�� ���� �̿볻�� Ȯ�� �������� ������<br>
				������(�ֹε�Ϲ�ȣ)�� ������ġ������Ͻ��÷���(�ֿ��� �����ϴ� ���̹� üũ<br>
				�ƿ��� �����մϴ�.<br>
				�����Ͻ� �ֹε�Ϲ�ȣ�� ������ ��������(CI) ��ȯ�� ���� ȸ��Ȯ���� �̷������,<br>
				���̹� üũ�ƿ������� �ֹε�Ϲ�ȣ ��ü�� �������� �ʽ��ϴ�.</p>
			</dd>
			</dl>
		</div>
		<div class="agree_area">
			<input type="checkbox" id="agree" name="agree" value="y" /><label for="agree">�����ĺ����� ������ �����մϴ�. </label>
		</div>
		<div class="section3">
			<dl>
			<dt><img src="./naver_material/img/store/text_agree2.gif" width="85" height="11" alt="�������� ���� �ȳ�"></dt>
			<dd>
				<table cellspacing="0" border="1" class="shop_list">
					<col width="150"><col>
					<tbody>
					<tr>
					<th scope="row"><img src="./naver_material/img/store/text_th3.gif" width="76" height="12" alt="�������� ������"></th>
					<td><strong><?=$cfg['compName']?></strong></td>
					</tr>
					<tr>
					<th scope="row"><img src="./naver_material/img/store/text_th4.gif" width="96" height="12" alt="�������� �����޴���"></th>
					<td>������ġ������Ͻ��÷�����</td>
					</tr>
					</tbody>
					</table>
					<table cellspacing="0" border="1" class="shop_list2">
					<col width="139"><col>
					<tbody>
					<tr class="none">
					<th scope="row">�����ϴ� �������� �׸�</th>
					<td>ȸ�����̵�, ȸ����</td>
					</tr>
					<tr>
					<th scope="row">���������� �̿����</th>
					<td>���θ� - üũ�ƿ� �� ���� ȸ�� �ĺ�,<br>���θ� ȸ�� ���� �̿� ���� �ĺ�.</td>
					</tr>
					<tr>
					<th scope="row">���������� �����Ⱓ ��<br>�̿�Ⱓ</th>
					<td>���̹� üũ�ƿ� ȸ�� Ż��� ��� ����, <br>
					��, ������ɿ� ���� ������ �ʿ��� ��� <br>
					���ɿ� ���� ����.</td>
					</tr>
					</tbody>
				</table>
			</dd>
			</dl>
		</div>
		<div class="agree_area">
			<input type="checkbox" id="agree2" name="agree2" value="y" /><label for="agree2">�������� ������ �����մϴ�.</label>
		</div>
	</div>
	<div id="pop_footer">
		<div class="btn_section">
			<input type="image" src="./naver_material/img/store/btn_confirm2.gif" width="60" height="30" alt="Ȯ��" />
			<a href="javascript:top.close();"><img src="./naver_material/img/store/btn_close2.gif" width="60" height="30" alt="�ݱ�"></a>
		</div>
		<div class="logo_section">
			<img src="./naver_material/img/store/l_naver.gif" width="39" height="13" alt="NAVER" />
			<img src="./naver_material/img/store/l_checkout.gif" width="58" height="13" alt="Checkout" />
		</div>
	</div>
</form>
</div>
</body>
</html>
