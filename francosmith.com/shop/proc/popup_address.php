<?php
@include "../lib/library.php";
$godojuso = Core::loader('godojuso');
$gubun = $_GET['gubun'];
if (isset($_GET['isMobile'])) $godojuso->setMobile();

echo $godojuso->getCurlData($gubun);

/* ��) ���� Bridge ���� �����ϴ� ���
 * $returnurl = ProtocolPortDomain().$cfg['rootDir']."/proc/popup_address_bridge.php";
 * $godojuso->getCurlData($gubun, $returnurl);
 */
?>

<!-- ����Ͽ��� ���̾�� ���� ���½� ���� -->
<?if($_GET['isMobile']=="true"){?>
<script type="text/javascript">
$(function(){
	// �ּҰ˻��� �˻��� �ּҸ� �������� �� ������ �� ���� �̵�
	$(document).on("click",".select",function(){
		$(window.parent.document).scrollTop(0,0);
	});
	//id�� close�� ���ڸ� Ŭ������ �� ���̾� ����
	$(document).on("click","#close",function(){
		$(window.parent.document).find("#frmMask").remove();
		$(window.parent.document).find("#searchZipcode_area").remove();
	});
})
</script>
<?}?>