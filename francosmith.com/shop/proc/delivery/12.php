<? ### �����ù� http://www.hanjinexpress.hanjin.net/customer/plsql/hddcw07.result?wbl_num=
$out = preg_replace('/(<html.+?)+(<body.+?>)/is', '', $out);
$out = preg_replace('/<\/(BODY|html)>/is', '', $out);
$out = preg_replace('/(<form.+?)+(<\/form>)/is', '', $out);
$out = preg_replace('/(<script.+?)+(<\/script>)/is', '', $out);
$out = preg_replace('/(<a href="javascript:go_cashback\(\);">.+?)+(<\/a>)/is', '', $out);

echo ('<TABLE width=440 align="center">');
echo($out);
?>
<!-- ����ȸ : Start -->
<form name="form1" action="http://www.hanjin.co.kr/Delivery_html/inquiry/result_waybill.jsp" method="get" target="_blank">
<input type="hidden" name="wbl_num" value="<?=$deliverycode?>">
<div style="text-align:center;"><input type="submit" value="������� �󼼺���" style="height:40px; font-weight:bold;"></div>
</form>
<!-- ����ȸ : End -->