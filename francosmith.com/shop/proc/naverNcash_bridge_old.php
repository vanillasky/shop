<?
/**
*	���̹� ����Ʈ �긮�� ������
**/

include "../_header.php";

if(!$_GET) exit;

$naverNcash = &load_class('naverNcash','naverNcash');

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

### signature check

$signature = hash_hmac_php4('sha1',$_GET['reqTxId'].($_GET['baseAccumRate']*10).($_GET['addAccumRate']*10).$_GET['useAmount'].$_GET['balanceAmount'],$naverNcash->api_key);

if($_GET['sig'] != $signature ){
	echo "<script>alert('���������� �߻��Ͽ����ϴ�. �ٽ� �ѹ� �õ����ֽñ� �ٶ��ϴ�.');location.href='../proc/naverNcash_use.php';</script>";
}
?>
<html>
<head></head>
<body>
<script> 
var baseAccumRate = "<?php echo $_GET['baseAccumRate']; ?>"; 
var addAccumRate = "<?php echo $_GET['addAccumRate']; ?>"; 
var useAmount = "<?php echo $_GET['useAmount']; ?>"; 
var balanceAmount = "<?php echo $_GET['balanceAmount']; ?>"; 
var reqTxId = "<?php echo $_GET['reqTxId']; ?>"; 
var totalAccumRate = parseFloat(baseAccumRate) + parseFloat(addAccumRate);
var tplSkin = "<?php echo $cfg['tplSkin']; ?>";

var view_use = "";
var view_save = "";

try { 
	if (window.opener && !window.opener.closed && window.opener.open) { 
		//����� ���̹�����Ʈ �ݾ��� �Է��� �Է¶� Ȯ��
		if (opener.document.getElementById('naverCashUseAmount<?=$naverNcash->api_id;?>')) { 

			//���� �� ��� �������κ��� ���� ���� �ֹ��� �������� �Է� 
			opener.document.getElementById('naverCashUseAmount<?=$naverNcash->api_id;?>').value = <?=$totalAccumRate?>;
			opener.document.getElementById('reqTxId<?=$naverNcash->api_id;?>').value = reqTxId;
			opener.document.getElementById('useAmount<?=$naverNcash->api_id;?>').value = useAmount;
			opener.document.getElementById('baseAccumRate').value = baseAccumRate;
			opener.document.getElementById('addAccumRate').value = addAccumRate;
			opener.document.getElementById('save_button').style.display = 'none';

			var result = opener.calcu_settle();	// �� �����ݾ� ���

			if(result != 0 ){
				if( parseFloat(useAmount) != '0' ) view_use = opener.comma(useAmount)+"���� ���";
				if( parseFloat(totalAccumRate) != '0' ){ if(view_use.length != '0') view_save = " + "; view_save = view_save + "<span style='font:bold;color:#1ec228;'>" + totalAccumRate+"% ����</span> "; }
				opener.document.getElementById('ncash_view').innerHTML = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + view_use + view_save + "<img src='../data/skin/"+tplSkin+"/img/nmileage/n_mileage_modify.gif' onClick='javascript:cash_save_use();' style='vertical-align:middle;' style='cursor:pointer'> ";
			}else{
				opener.document.getElementById('naverCashUseAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('reqTxId<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('useAmount<?=$naverNcash->api_id;?>').value = "";
				opener.document.getElementById('baseAccumRate').value = "";
				opener.document.getElementById('addAccumRate').value = "";
				opener.document.getElementById('ncash_view').innerHTML = "<img src='../data/skin/"+tplSkin+"/img/nmileage/n_mileage_use.gif' onClick='javascript:cash_save_use();'>&nbsp;";
			}
			window.close(); 
		} else { 
			alert('������ �ֹ��� �������� �������� �ʽ��ϴ�. �ֹ��� �������� ���� �ִٸ� �ٽ� �� �� ���̹�����Ʈ ���� �� ����� Ŭ���� �ּ���.'); 
		} 
	} else { 
		document.write('������ �ֹ��� �������� �������� �ʽ��ϴ�. �ֹ��� �������� ���� �ִٸ� �ٽ� �� �� ���̹�����Ʈ ���� �� ����� Ŭ���� �ּ���.'); 
	}
} catch (error) { 
	document.write('������ �ֹ��� �������� �������� �ʽ��ϴ�. �ֹ��� �������� ���� �ִٸ� �ٽ� �� �� ���̹�����Ʈ ���� �� ����� Ŭ���� �ּ���.');
} 
</script>
</body>
</html>