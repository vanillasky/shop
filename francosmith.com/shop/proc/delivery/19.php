<?
 ### õ���ù� http://www.cyber1001.co.kr/kor/taekbae/HTrace.jsp?transNo=
 $out = split_betweenStr($out,"<td style=\"padding-top:16px\"><img src=\"img/HTrace_title_1.gif\"></td>","<td class=\"txtTitle\">�� ������ȭ </td>");
 $out[0] = str_replace("<input type=\"submit\" name=\"Submit\" value=\"��   ȸ\" />","",$out[0]);
 $out[0] = str_replace("(������ȣ�� �Է��ϼ���)","",$out[0]);
?>
<table><tr>
<?=$out[0]?>
<td></td></tr></table>