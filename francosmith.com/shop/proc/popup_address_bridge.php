<?php
@include "../lib/library.php";
//POST��
$gubun			= $_POST['gubun'];
$s_type			= $_POST['s_type'];
$zipcode0		= $_POST['zipcode1'];
$zipcode1		= $_POST['zipcode2'];
$zonecode		= $_POST['zonecode'];
$road_address	= $_POST['road_address'];
$address_sub	= $_POST['address_sub'];

if($_POST['ground_address']) {
	$address	= $_POST['ground_address'];
} else {
	$address		= $_POST['address'];
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />

		<script type="text/javascript">
		var gubun = "<?=$gubun?>";

		if (gubun == "m")
		{
			var o_zipcode0				= parent.opener.document.getElementById('m_zipcode0');
			var o_zipcode1				= parent.opener.document.getElementById('m_zipcode1');
			var o_address				= parent.opener.document.getElementById('m_address');
			var o_zonecode				= parent.opener.document.getElementById('m_zonecode');
			var o_address_sub			= parent.opener.document.getElementById('m_address_sub');
			var o_road_address			= parent.opener.document.getElementById('m_road_address');
			var o_div_road_address		= parent.opener.document.getElementById("m_div_road_address");
			var o_div_road_address_sub	= parent.opener.document.getElementById("div_road_address_sub");
		}
		// ����� ����� ��Ų���� �����ȣ �˻���(���̾��˾�)
		else if (gubun == 'mobile')
		{
			var o_zipcode0				= parent.document.getElementById('zipcode0');
			var o_zipcode1				= parent.document.getElementById('zipcode1');
			var o_address				= parent.document.getElementById('address');
			var o_address_sub			= parent.document.getElementById('address_sub');
			var o_road_address			= parent.document.getElementById('road_address');
			var o_div_road_address		= parent.document.getElementById("div_road_address");
			var o_div_road_address_sub	= parent.document.getElementById("div_road_address_sub");
			var o_zonecode				= parent.document.getElementById('zonecode');
		}
		else {
			var o_zipcode0				= parent.opener.document.getElementById('zipcode0');
			var o_zipcode1				= parent.opener.document.getElementById('zipcode1');
			var o_zonecode				= parent.opener.document.getElementById('zonecode');
			var o_address				= parent.opener.document.getElementById('address');
			var o_address_sub			= parent.opener.document.getElementById('address_sub');
			var o_road_address			= parent.opener.document.getElementById('road_address');
			var o_div_road_address		= parent.opener.document.getElementById("div_road_address");
			var o_div_road_address_sub	= parent.opener.document.getElementById("div_road_address_sub");
		}

		var s_type			= "<?=$s_type?>"; //�ּҰ˻� Ÿ�� zipcode : ���� �����ȣ �˻� / road : ���θ��ּ� �˻�

		var zipcode0		= "<?=$zipcode0?>";
		var zipcode1		= "<?=$zipcode1?>";
		var zonecode		= "<?=$zonecode?>";
		var address			= "<?=$address?>";
		var address_sub		= "<?=$address_sub?>";
		var road_address	= "<?=$road_address?>";

		o_zipcode0.value	= zipcode0;
		o_zipcode1.value	= zipcode1;
		if(o_zonecode){ // ������ȣ �ʵ尡 ���� ���(��ġ�� �Ǿ��� ���)
			o_zonecode.value	= zonecode;
		}

		if(s_type == "zipcode") {
			if (o_address_sub) { //�������ּ� �ʵ尡 ���� ���� ���
				o_address.value						= address;
				o_address_sub.value					= address_sub;
				o_road_address.value				= "";
				o_div_road_address.innerHTML		= "";
				o_div_road_address_sub.innerHTML	= "";
			} else {
				o_address.value						= address+" "+address_sub;
				o_road_address.value				= "";

				if(o_div_road_address) {
					o_div_road_address.innerHTML		= "";
				}
			}
		} else {
			if (o_address_sub) { //�������ּ� �ʵ尡 ���� ���� ���
				o_address.value						= address;
				o_address_sub.value					= address_sub;
				o_road_address.value				= road_address;
				o_div_road_address.innerHTML		= road_address;
				o_div_road_address_sub.innerHTML	= address_sub;
				o_div_road_address_sub.style.display="";
			} else {
				o_address.value						= address+" "+address_sub;
				o_road_address.value				= road_address+" "+address_sub;

				if(o_div_road_address) {
					o_div_road_address.innerHTML	= road_address+" "+address_sub;
				}
			}
		}

		var _parentHeader = parent.opener;
		// ����� ����� ��Ų���� �����ȣ �˻���(���̾��˾�)
		if (gubun == 'mobile' && parent.location){
			_parentHeader = parent;
		}
		var getDeliveryType		= typeof _parentHeader.getDelivery;
		var parentUrl			= new Array();
		var parentUrlPattern	= /^(order\.php)/;
		var parentPage;

		parentUrl	= _parentHeader.location.toString().split("/");
		parentPage	= parentUrl[parentUrl.length-1];

		if(parentUrlPattern.test(parentPage) === true && (getDeliveryType == "function" || getDeliveryType == "object") && getDeliveryType != null ){
			_parentHeader.getDelivery();
		}

		if (gubun == 'mobile'){
			if (typeof(parent.frmMaskRemove) != 'undefined') parent.frmMaskRemove('searchZipcode');
			else window.parent.close();
		}else{
			window.parent.close();
		}
		</script>
	</head>
	<body>
	</body>
</html>