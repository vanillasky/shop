<?php ob_start(); ?>
<div class="naver-mileage-accum-place m_list naver-mileage-accum">
	<div class="m_title" style="line-height: 25px; padding: 0;">������<br/>������ġ</div>
	<div class="m_right" style="line-height: 18px; padding: 5px; font-size: 12px; position: absolute; left: 70px;">
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
		<label for="save-mode-default" style="overflow-x: hidden;"> �������� ���θ� ���������� �����մϴ�.</label><br/>
		<input type="radio" name="save_mode" id="save-mode-ncash" value="ncash"/>
		<label for="save-mode-ncash" style="overflow-x: hidden;"> �������� ���̹� ���ϸ����� �����մϴ�.</label>
		<?php } ?>
	</div>
</div>
<div class="naver-mileage-accum-set m_list naver-mileage-accum" style="height: 90px;">
	<div class="m_title" style="line-height: 25px; padding: 20px 0 20px 0;">���̹�<br/>���ϸ��� <img src="<?php echo $GLOBALS['cfg']['rootDir']; ?>/proc/naver_mileage/images/n_mileage_on.png"/></div>
	<div class="m_right" style="padding: 5px;">
		<div id="naver-mileage-container"></div>
		<input type="hidden" id="reqTxId<?php echo $load_config_ncash['api_id']; ?>" name="reqTxId<?php echo $load_config_ncash['api_id']; ?>" value="">
		<input type="hidden" id="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
		<input type="hidden" id="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
		<input type="hidden" id="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
		<input type="hidden" id="exception_price" name="exception_price" value="<?php echo $load_config_ncash['exception_price']; ?>">
		<input type="hidden" id="baseAccumRate" name="baseAccumRate" value="">
		<input type="hidden" id="addAccumRate" name="addAccumRate" value="">
	</div>
</div>
<script type="text/javascript">
(function(){
	// ���̹� ���ϸ��� ������ġ�� ���� �ȳ����� ���̰� ����ʿ� ���� ������Ʈ ���̵� �ڵ� ����
	var titleHeight = $('div.naver-mileage-accum-place div.m_title').height();
	var rightHeight = $('div.naver-mileage-accum-place div.m_right').height()+10;
	var maxHeight = (titleHeight > rightHeight) ? titleHeight : rightHeight;
	var minHeight = (titleHeight > rightHeight) ? rightHeight : titleHeight;
	if ($('div.naver-mileage-accum-place').height() < maxHeight) {
		$('div.naver-mileage-accum-place div.m_title').css('paddingTop', (maxHeight-minHeight)/2).css('paddingBottom', (maxHeight-minHeight)/2);
		$('div.naver-mileage-accum-place').height(maxHeight);
	}
})();
</script>
<?php return ob_get_clean(); ?>