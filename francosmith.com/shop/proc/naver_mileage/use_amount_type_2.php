<?php ob_start(); ?>
<?php if ($data['ncash_emoney']) { ?>
<tr>
	<th>���̹�<br/>���ϸ���</th>
	<td><?php echo number_format($data['ncash_emoney']); ?>��</td>
</tr>
<?php } ?>
<?php if ($data['ncash_cash']) { ?>
<tr>
	<th>���̹� ĳ��</th>
	<td><?php echo number_format($data['ncash_cash']); ?>��</td>
</tr>
<?php } ?>
<?php return ob_get_clean(); ?>