<?php /* Template_ 2.2.7 2016/04/10 14:59:14 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_emoney.htm 000002483 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<div class="page_title_div">
	<div class="page_title">YOUR REWARDS</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">����������</a> &gt; <span class='bold'>�����ݳ���</span></div>
</div>
<div class="page_title_line"></div>

<div class="indiv"><!-- Start indiv -->

	<div style="width:100%; text-align:left"><img src="/shop/data/skin/freemart/img/common/mypoint_01.gif"></div>
	<div style="width:100%; border:1px solid #DEDEDE;">
		<table width=100% cellpadding=10 cellspacing=0 border=0>
		<tr>
			<td style="border:5px solid #F3F3F3;">
			<div style="width:100%; text-align:center">
			���� <strong><?php echo $GLOBALS["name"]?></strong>���� �������� <strong><FONT COLOR="#007FC8"><?php echo number_format($GLOBALS["emoney"])?> point</font></strong>�Դϴ�
			</div>
			</td>
		</tr>
		</table>
	</div>


	<div style="width:100%; text-align:left; padding-top:20"><img src="/shop/data/skin/freemart/img/common/mypoint_02.gif"></div>
		<table class="mypage-board-table">
		<tr class="mypage-board-title">
			<th width=10%>��ȣ</th>
			<th width=15%>�߻��Ͻ�</th>
			<th>�󼼳���</th>
			<th width=15%>�����ݾ�</th>
			<th width=15%>���ݾ�</th>
		</tr>
<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
		<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="" style="border-bottom:1px solid #ededed">
			<td align="center"><?php echo $TPL_V1["idx"]?></td>
			<td align="center"><?php echo $TPL_V1["regdts"]?></td>
			<td><?php echo $TPL_V1["memo"]?></td>
			<td align="center"><?php if($TPL_V1["emoney"]> 0){?><?php echo number_format($TPL_V1["emoney"])?><?php }else{?>��<?php }?></td>
			<td align="center"><?php if($TPL_V1["emoney"]< 0){?><?php echo number_format($TPL_V1["emoney"])?><?php }else{?>��<?php }?></td>
		</tr>
<?php }}?>
		</table>

		<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>