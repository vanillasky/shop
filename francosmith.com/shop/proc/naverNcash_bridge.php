<?
/**
*	���̹� ����Ʈ �긮�� ������
**/

include "../_header.php";

if(!$_GET) exit;

setCookie('reqTxId', $_GET['reqTxId'], time()+600, '/');
$naverNcash = Core::loader('naverNcash');
$naverCommonInflowScript = Core::loader('naverCommonInflowScript');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

function hash_hmac_php4($algo,$data,$passwd){
	/* php4 �� md5 and sha1 only */
	$algo=strtolower($algo);
	$p=array('md5'=>'H32','sha1'=>'H40');
	if(strlen($passwd)>64) $passwd=pack($p[$algo],$algo($passwd));
	if(strlen($passwd)<64) $passwd=str_pad($passwd,64,chr(0));
	$ipad=substr($passwd,0,64) ^ str_repeat(chr(0x36),64);
	$opad=substr($passwd,0,64) ^ str_repeat(chr(0x5C),64);
	return($algo($opad.pack($p[$algo],$algo($ipad.$data))));
}

$totalAccumRate = $_GET['baseAccumRate'] + $_GET['addAccumRate'];

if(isset($_GET['useAmount']))
{
	exit('<script type="text/javascript">location.href="naverNcash_bridge_old.php?reqTxId='.$_GET['reqTxId'].'&baseAccumRate='.$_GET['baseAccumRate'].'&addAccumRate='.$_GET['addAccumRate'].'&useAmount='.$_GET['useAmount'].'&balanceAmount='.$_GET['balanceAmount'].'&sig='.$_GET['sig'].'";</script>');
}

### signature check
if($_GET['resultCode']=='OK')
{
	$signature = hash_hmac_php4('sha1',$_GET['resultCode'].$_GET['reqTxId'].($_GET['baseAccumRate']*10).($_GET['addAccumRate']*10).$_GET['mileageUseAmount'].$_GET['cashUseAmount'].$_GET['totalUseAmount'].$_GET['balanceAmount'],$naverNcash->api_key);

	if($_GET['sig'] != $signature ){
		echo '<script type="text/javascript">alert("���������� �߻��Ͽ����ϴ�. �ٽ� �ѹ� �õ����ֽñ� �ٶ��ϴ�.");location.href="../proc/naverNcash_use.php";</script>';
	}
}
?>
<html>
<head>
<?php echo $naverCommonInflowScript->getCommonInflowScript(); ?>
</head>
<body>
<script type="text/javascript">
<?php if($_GET['resultCode']=='OK'){ ?>
var baseAccumRate = "<?php echo $_GET['baseAccumRate']; ?>";
var addAccumRate = "<?php echo $_GET['addAccumRate']; ?>";
<?php }else{ ?>
var baseAccumRate = "<?php echo $naverNcash->get_base_accum_rate(); ?>";
var addAccumRate = "<?php echo $naverNcash->get_add_accum_rate(); ?>";
<?php } ?>
var mileageUseAmount = "<?php echo $_GET['mileageUseAmount']; ?>";
var cashUseAmount = "<?php echo $_GET['cashUseAmount']; ?>";
var totalUseAmount = "<?php echo $_GET['totalUseAmount']; ?>";
var balanceAmount = "<?php echo $_GET['balanceAmount']; ?>";
var reqTxId = "<?php echo $_GET['reqTxId']; ?>";
var totalAccumRate = parseFloat(baseAccumRate) + parseFloat(addAccumRate);
var tplSkin = "<?php echo $cfg['tplSkin']; ?>";

var view_use = "";
var view_save = "";

try {
	if (window.opener && !window.opener.closed && window.opener.open) {
		//����� ���̹�����Ʈ �ݾ��� �Է��� �Է¶� Ȯ��
		if (opener.document.getElementById('reqTxId<?=$naverNcash->api_id;?>')) {

			//���� �� ��� �������κ��� ���� ���� �ֹ��� �������� �Է�
			opener.document.getElementById('reqTxId<?=$naverNcash->api_id;?>').value = reqTxId;
			opener.document.getElementById('mileageUseAmount<?=$naverNcash->api_id;?>').value = mileageUseAmount;
			opener.document.getElementById('cashUseAmount<?=$naverNcash->api_id;?>').value = cashUseAmount;
			opener.document.getElementById('totalUseAmount<?=$naverNcash->api_id;?>').value = totalUseAmount;
			opener.document.getElementById('baseAccumRate').value = baseAccumRate;
			opener.document.getElementById('addAccumRate').value = addAccumRate;

			var
			result = opener.calcu_settle(),	// �� �����ݾ� ���
			mileage_info = '<img class="n_mileage" src="/shop/data/skin/<?php echo $cfg['tplSkin']; ?>/img/nmileage/n_mileage_info2.gif" onclick="mileage_info();">';
<?php if($_GET['resultCode']=='OK'){ ?>
			if(result != 0 ){
				var use_amount = new Array();
				if( parseFloat(mileageUseAmount) != '0' ) use_amount.push("���ϸ��� "+opener.comma(mileageUseAmount)+"��");
				if( parseFloat(cashUseAmount) != '0' ) use_amount.push("ĳ�� "+opener.comma(cashUseAmount)+"��");
				if(use_amount.length>0) view_use = use_amount.join(" + ")+" ���";
				if( parseFloat(totalAccumRate) != '0' )
				{
					if(use_amount.length>0) view_save = "<strong>���̹� ���ϸ���</strong> <span style='font:bold;color:#1ec228;'>"+totalAccumRate+"% ���� / </span><br/>";
					else view_save = "<strong>���̹� ���ϸ���</strong> <span style='font:bold;color:#1ec228;'>"+totalAccumRate+"%</span> ����";
				}
				opener.document.getElementById('ncash_view').style.display = "block";
				opener.document.getElementById('save_button').style.display = "none";
				opener.document.getElementById('ncash_view').innerHTML = view_save + view_use + " <img src='../data/skin/"+tplSkin+"/img/nmileage/n_mileage_modify.gif' onclick='cash_save_use();' style='vertical-align:middle;' style='cursor:pointer'> "+mileage_info;
			}else{
				opener.document.getElementById('reqTxId<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('mileageUseAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('cashUseAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('totalUseAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('baseAccumRate').value = "";
				opener.document.getElementById('addAccumRate').value = "";
				opener.document.getElementById('ncash_view').style.display = "none";
				opener.document.getElementById('save_button').style.display = "block";
				opener.document.getElementById('save_button').innerHTML = "<img src='../data/skin/"+tplSkin+"/img/nmileage/n_mileage_use.gif' onclick='cash_save_use();' class='mileage_button'>&nbsp;��ư�� Ŭ���ؼ� <span style=\"color: #1ec228; font-weight: bold;\">"+totalAccumRate+"%</span> �����ް� ����ϼ��� "+mileage_info;
			}
<?php }else if($_GET['resultCode']=='CANCEL' || $_GET['resultCode']=='ERROR'){ ?>
				opener.document.getElementById('reqTxId<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('mileageUseAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('cashUseAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('totalUseAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('baseAccumRate').value = "";
				opener.document.getElementById('addAccumRate').value = "";
				opener.document.getElementById('ncash_view').style.display = "none";
				opener.document.getElementById('save_button').style.display = "block";
				opener.document.getElementById('save_button').innerHTML = "<img src='../data/skin/"+tplSkin+"/img/nmileage/n_mileage_use.gif' onclick='cash_save_use();' class='mileage_button'>&nbsp;��ư�� Ŭ���ؼ� <span style=\"color: #1ec228; font-weight: bold;\">"+totalAccumRate+"%</span> �����ް� ����ϼ��� "+mileage_info;
<?php } ?>
			window.close();
		} else {
			alert('������ �ֹ��� �������� �������� �ʽ��ϴ�. �ֹ��� �������� ���� �ִٸ� �ٽ� �� �� ���̹����ϸ��� ���� �� ����� Ŭ���� �ּ���.');
		}
	} else {
		document.write('������ �ֹ��� �������� �������� �ʽ��ϴ�. �ֹ��� �������� ���� �ִٸ� �ٽ� �� �� ���̹����ϸ��� ���� �� ����� Ŭ���� �ּ���.');
	}
} catch (error) {
	document.write('������ �ֹ��� �������� �������� �ʽ��ϴ�. �ֹ��� �������� ���� �ִٸ� �ٽ� �� �� ���̹����ϸ��� ���� �� ����� Ŭ���� �ּ���.');
}
</script>
</body>
</html>
