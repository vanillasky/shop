<?php /* Template_ 2.2.7 2016/04/21 13:53:40 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/service/customer.htm 000003581 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>




<!-- ����̹��� || ������ġ -->
<div class="page_title_div">
	<div class="page_title">&nbsp;</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <span class='bold'>������</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>


<div class="indiv" style="padding-bottom:20px;"><!-- Start indiv -->

<div id="cs-banner"></div>

<div class="top-15" >
	<div class="center-comment-box" style="width:90%">
		<span class="large-comment">ģ���� ���ξȳ�, �ּ��� ���� ģ���ϰ� ����ص帳�ϴ�.</span>
		<div class="dotted-bline"></div>
	</div>
	
	<div id="cs-faq-search-box" class="position:relative;">
		<form name="search-form" action="/shop/service/faq.php">
		<div style="width:160px; float:left; display:inline-block; padding-top:20px;padding-left:10px">
			<span class="large-comment">������ ���͵帱���?</span>
		</div>
		
		<div style="float:left;display:inline-block;padding-top:10px;">
			<table class="cs-search_table">
			<tr>
				<td class="cs-search_td"><input name=sword type=text id="<?php echo $TPL_VAR["id"]?>" class="cs-search_input" onkeyup="<?php echo $TPL_VAR["onkeyup"]?>" onclick="<?php echo $TPL_VAR["onclick"]?>" value="<?php echo $TPL_VAR["value"]?>" required label="�˻���"></td>
				<td class="search_btn_top top_red"><button type="submit" title="Search" class="button cs-search-button"></td>
			</tr>
			</table>
		</div>
		</form>		
		<div style="width:100px; margin-left:3px; float:left; padding-top:8px;">
			<button class="button-big-cs button-dark" onClick='javascript:goFaq();'>FAQ ������</button>
		</div>
		
	</div>
</div>

<div class="top-15" style="width:90%; margin:0 auto;">
	<div class="left-comment-box" style="text-align:left">
		<p><span class="cs-title">Email</span><span class="cs-data">cs@francosmith.com</span></p>
		<p><span class="cs-title">ī�� �ֹ�/���</span><span class="cs-data">@francosmith</span></p>
		<p><span class="cs-title">����/���Ź���</span><span class="cs-data">mailroom@francosmith.com</span></p>
		<p><span class="cs-title">���/�񼱹� ����</span><span class="cs-data">shkim@francosmith.com</span></p>
		<p><span class="cs-title">����â�� ������</span><span class="cs-data">shkim@francosmith.com</span></p>
	</div>
</div>

<script type="text/javascript">
	function goFaq() {
		location.href="/shop/service/faq.php?faq_sword=";
	}
</script>


<div class="top-15" style="width:90%; margin:0 auto;">
	<div class="left-comment-box" style="text-align:left; border-top:1px dotted #C9242B">
		<p class="bline"><span class="cs-title boldf">�ֹ����</span><span class="cs-title">���� 09:30 ~ ���� 6��(��~��)</span></p>
		<p class="bline"><span class="cs-title boldf">��ǰ����</span><span class="cs-title">���� 09:30 ~ ���� 4��(��~��)</span></p>
		<p class="bline"><span class="cs-title boldf">����Ͻ�</span><span class="cs-title">���� 09:30 ~ ���� 4��(��~��)</span></p>
		<p class="bline"><span class="cs-title boldf">��������</span><span class="cs-title">���� 10:00 ~ ���� 5��(��~��)</span><span class="cs-title">TEL) 010-5472-4755</span></p>
		<p class="bline"><span class="cs-title boldf">����â��</span><span class="cs-title">���� 10:00 ~ ���� 5��(��~��)</span><span class="cs-title">TEL) 010-5472-4755</span></p>
	</div>
</div>


</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>