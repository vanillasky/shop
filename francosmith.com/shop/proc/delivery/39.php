<?
 # �浿�ù� http://www.kdexp.com/rerere.asp?stype=1&p_item=
 	$out = split_betweenStr($out,'<div id="printme">','</div>');
	$out = str_replace('<a href="javascript:printIt(document.getElementById(\'printme\').innerHTML)"><font color="blue"><b>����ϱ�</b></font></a>', '', $out[0]);
?>
<table width="100%">
	<tr>
		<td>
			<?=$out;?>
		</td>
	</tr>
</table>