<?php ob_start(); ?>
<dt class="naver-mileage-accum-place naver-mileage-accum">������<br/>������ġ</dt>
<dd class="naver-mileage-accum-place naver-mileage-accum">
	<?php if ($load_config_ncash['save_mode'] == 'ncash') { ?>
	<span>�������� ���̹� ���ϸ����� �����˴ϴ�.</span>
	<input type="hidden" name="save_mode" value="unused"/>
	<?php } else if ($load_config_ncash['save_mode'] == 'both') { ?>
	<span>
		�������� ���̹� ���ϸ����� ���θ�<br/>
		�Ѵ� �����˴ϴ�.<br/>
		(���̹� ���ϸ����� ���������÷���<br/>
		"���� �� ����ϱ�" ��ư�� �����ּ���.)
	</span>
	<input type="hidden" name="save_mode" value="both"/>
	<?php } else if ($load_config_ncash['save_mode'] == 'choice') { ?>
	<input type="radio" name="save_mode" id="save-mode-default" value="" required="required" label="������ ���� ��ġ"/>
	<label for="save-mode-default"> �������� ���θ� ���������� �����մϴ�.</label><br/>
	<input type="radio" name="save_mode" id="save-mode-ncash" value="ncash"/>
	<label for="save-mode-ncash"> �������� ���̹� ���ϸ����� �����մϴ�.</label>
	<?php } ?>
</dd>
<dt class="naver-mileage-accum-set naver-mileage-accum">���̹�<br/>���ϸ��� <img src="<?php echo $GLOBALS['cfg']['rootDir']; ?>/proc/naver_mileage/images/n_mileage_on.png"/></dt>
<dd class="naver-mileage-accum-set naver-mileage-accum">
	<div id="naver-mileage-container"></div>
	<input type="hidden" id="reqTxId<?php echo $load_config_ncash['api_id']; ?>" name="reqTxId<?php echo $load_config_ncash['api_id']; ?>" value="">
	<input type="hidden" id="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
	<input type="hidden" id="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
	<input type="hidden" id="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
	<input type="hidden" id="exception_price" name="exception_price" value="<?php echo $load_config_ncash['exception_price']; ?>">
	<input type="hidden" id="baseAccumRate" name="baseAccumRate" value="">
	<input type="hidden" id="addAccumRate" name="addAccumRate" value="">
</dd>
<?php return ob_get_clean(); ?>