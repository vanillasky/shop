<?php ob_start(); ?>
<?php if ($load_config_ncash['ncash_emoney'] > 0) { ?>
<dt>���̹�<br/>���ϸ���</dt>
<dd><?php echo number_format($load_config_ncash['ncash_emoney']); ?>��</dd>
<?php } ?>
<?php if ($load_config_ncash['ncash_cash'] > 0) { ?>
<dt>���̹� ĳ��</dt>
<dd><?php echo number_format($load_config_ncash['ncash_cash']); ?>��</dd>
<?php } ?>
<?php return ob_get_clean(); ?>