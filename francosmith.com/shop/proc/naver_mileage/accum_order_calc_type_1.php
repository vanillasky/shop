<?php ob_start(); ?>
var nMaxUseAmount = settlement - ((emoney<=delivery) ? delivery-emoney : 0), totalUseAmount = NaverMileage.getTotalUseAmount();
if ((settlement - totalUseAmount) >= 0) {
	if (NaverMileage.isUsed() === false) NaverMileage.enable();
	NaverMileage.setMaxUseAmount(settlement);
	settlement -= totalUseAmount;
}
else {
	if (NaverMileage.isUsed()) {
		alert("������ ���ݾ��� �ʰ��Ͽ����ϴ�.");
		document.frmOrder.emoney.value = 0;
		return calcu_settle();
	}
	else {
		NaverMileage.setMaxUseAmount(0);
		NaverMileage.disable();
	}
}
<?php return ob_get_clean(); ?>