<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/service/agreement.htm 000001085 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_agreement.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>�̿���</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<div id="agreePaste" style="text-align:left;" class="hundred"></div>

</div><!-- End indiv -->


<!-- ������ ������ - agreePaste�� Text ����� �ٿ��ִ� �ҽ��� : start -->
<textarea style="width:0; height:0; display:none;" id="agreeCopy"><?php echo $this->define('tpl_include_file_1',"proc/_agreement.txt")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></textarea>
<script language="javascript">document.getElementById('agreePaste').innerHTML = document.getElementById('agreeCopy').value.replace( /\n/ig, "<br>");</script>
<!-- ������ ������ : end -->

<?php $this->print_("footer",$TPL_SCP,1);?>