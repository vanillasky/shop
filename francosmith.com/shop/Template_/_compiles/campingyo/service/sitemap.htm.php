<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/service/sitemap.htm 000008988 */  $this->include_("dataCategory","dataBoard");?>
<?php $this->print_("header",$TPL_SCP,1);?>


<style><!--
.b_cate	 {font:bold 9pt ����; color:#464646;border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#EEEEEE; height:29}
.s_cate	 {font:8pt ����; color:#464646; line-height:18px; border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#D8D8D8; height:29; padding-left:15}
.bb_cate	 {font:bold 9pt ����; color:#464646; line-height:18px; border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#D8D8D8; height:29;}
--></style>


<!-- ����̹��� || ������ġ -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_sitemap.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>����Ʈ��</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<TABLE width=100% cellpadding=0 cellspacing=0 border=0 style="table-layout:fixed; border-width:1; border-style:solid; border-color:#E4E4E4;">
<TR>
	<TD style="padding:6 6 0 6"><img src="/shop/data/skin/campingyo/img/common/sitemap_01.gif"></TD>
</TR>
<tr>
	<td align=center>
	<!-- ī�װ� : Start -->
	<TABLE width=97% cellpadding=3 cellspacing=0 border=0>
<?php if((is_array($TPL_R1=dataCategory(true))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
	<TR>
		<TD class=b_cate><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?></a></TD>
	</TR>
<?php if($TPL_V1["sub"]){?>
	<tr>
		<td style="padding-left:20" class=s_cate>
<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_S2=count($TPL_R2);$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
		<a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>"><?php echo $TPL_V2["catnm"]?></a><nobr>
<?php if(($TPL_I2+ 1)!=$TPL_S2){?>&nbsp;<span style="font:8pt ����; color:#464646">|</span>&nbsp;<nobr><?php }?>
<?php }}?>
		</td>
	</tr>
<?php }?>
<?php }}?>
	</TABLE><br>
	<!-- ī�װ� : END -->
	</td>
</tr>
</TABLE>

<br>

<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR>
	<TD valign=top>
	<!-- Membership : Start -->
	<TABLE width=215 cellpadding=0 cellspacing=0 border=0 style="table-layout:fixed; border-width:1; border-style:solid; border-color:#E4E4E4;">
	<TR>
		<TD style="padding:6 6 0 6"><img src="/shop/data/skin/campingyo/img/common/sitemap_02.gif"></TD>
	</TR>
	<tr>
		<td align=center>
		<TABLE width=195 cellpadding=3 cellspacing=0 border=0>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle>ȸ������</TD>
		</tr>
		<tr>
			<td class=s_cate>
			��<A HREF="<?php echo url("member/login.php")?>&">�α���</A></a><br>
			��<A HREF="<?php echo url("service/agreement.php")?>&">���ڻ�ŷ� �̿���</A></a><br>
			��<A HREF="<?php echo url("member/join.php")?>&">ȸ������</A></a><br>
			��<A HREF="<?php echo url("member/find_id.php")?>&">���̵�ã��</A></a><br>
			��<A HREF="<?php echo url("member/find_pwd.php")?>&">��й�ȣã��</A></a><br>
			��<A HREF="<?php echo url("member/myinfo.php")?>&">ȸ����������</A></a><br>
			��<A HREF="<?php echo url("member/hack.php")?>&">ȸ��Ż��</A></a><br>
			</td>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle>���Ǳ۸���</TD>
		</tr>
		<tr>
			<td class=s_cate>
			��<A HREF="<?php echo url("mypage/mypage_qna.php")?>&">1:1����</A></a><br>
			��<A HREF="<?php echo url("mypage/mypage_qna_goods.php")?>&">��ǰQ&A</A></a><br>
			��<A HREF="<?php echo url("mypage/mypage_review.php")?>&">�̿��ı�</A></a><br>
			</td>
		</tr>
		</TABLE>
		<br>
		</td>
	</tr>
	</TABLE>
	<!-- Membership : Start -->
	</TD>
	<td valign=top>
	<!-- Service : Start -->
	<TABLE width=215 cellpadding=0 cellspacing=0 border=0 style="table-layout:fixed; border-width:1; border-style:solid; border-color:#E4E4E4;">
	<TR>
		<TD style="padding:6 6 0 6"><img src="/shop/data/skin/campingyo/img/common/sitemap_03.gif"></TD>
	</TR>
	<tr>
		<td align=center>
		<TABLE width=195 cellpadding=3 cellspacing=0 border=0>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("goods/goods_cart.php")?>&">��ٱ���</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("mypage/mypage_orderlist.php")?>&">�ֹ�/�����ȸ</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("goods/goods_search.php")?>&">�󼼰˻�</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("mypage/mypage_today.php")?>&">�ֱٺ���ǰ</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("mypage/mypage_emoney.php")?>&">������</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("mypage/mypage_wishlist.php")?>&">��ǰ������</A></TD>
		</tr>
		</TABLE>
		<br>
		</td>
	</tr>
	</table>
	<!-- Service : END -->

	<br>

	<!-- Community : Start -->
	<TABLE width=215 cellpadding=0 cellspacing=0 border=0 style="table-layout:fixed; border-width:1; border-style:solid; border-color:#E4E4E4;">
	<TR>
		<TD style="padding:6 6 0 6"><img src="/shop/data/skin/campingyo/img/common/sitemap_05.gif"></TD>
	</TR>
	<tr>
		<td align=center>
		<TABLE width=195 cellpadding=3 cellspacing=0 border=0>
<?php if((is_array($TPL_R1=dataBoard())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
		<tr>
			<td class=bb_cate><a href="<?php echo url("board/list.php?")?>&id=<?php echo $TPL_V1["id"]?>"><?php echo $TPL_V1["name"]?></a></td>
		</tr>
<?php }}?>
<?php if($TPL_VAR["fb"]->pageUseYn=='y'){?>
		<tr>
			<td class=bb_cate><a href="<?php echo url("goods/facepage.php")?>&" >���̽���</a></td>
		</tr>
<?php }?>
		</TABLE>
		<br>
		</td>
	</tr>
	</table><br>
	<!-- Community : END -->

	</td>
	<td valign=top>
	<!-- ������ : Start -->
	<TABLE width=215 cellpadding=0 cellspacing=0 border=0 style="table-layout:fixed; border-width:1; border-style:solid; border-color:#E4E4E4;">
	<TR>
		<TD style="padding:6 6 0 6"><img src="/shop/data/skin/campingyo/img/common/sitemap_04.gif"></TD>
	</TR>
	<tr>
		<td align=center>
		<TABLE width=195 cellpadding=3 cellspacing=0 border=0>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("service/customer.php")?>&">������</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("mypage/mypage_qna.php")?>&">1:1����</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("member/myinfo.php")?>&">����������</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("service/company.php")?>&">ȸ��Ұ�</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("service/private.php")?>&">����������ȣ��å</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("service/faq.php")?>&">�����ϴ�����</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("service/cooperation.php")?>&">����/���޹���</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("service/guide.php")?>&">�̿�ȳ�</A></TD>
		</tr>
		<tr>
			<TD class="bb_cate"><img src="/shop/data/skin/campingyo/img/common/sitemap_icon.gif" align=absmiddle><A HREF="<?php echo url("service/sitemap.php")?>&">����Ʈ��</A></TD>
		</tr>
		</TABLE>
		<br>
		</td>
	</tr>
	</table><br>
	<!-- ������ : END -->
	</td>
</TR>
</TABLE>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>