<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/mypage_emoney.htm 000002811 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- ����̹��� || ������ġ -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_point.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > ���������� > <B>�����ݳ���</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<div style="width:100%; text-align:left"><img src="/shop/data/skin/campingyo/img/common/mypoint_01.gif"></div>
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


<div style="width:100%; text-align:left; padding-top:20"><img src="/shop/data/skin/campingyo/img/common/mypoint_02.gif"></div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" style="clear:both;border-top-style:solid;border-top-color:#303030;border-top-width:2;border-bottom-style:solid;border-bottom-color:#D6D6D6;border-bottom-width:1;">
<tr height="23" bgcolor="#F0F0F0" class=input_txt>
	<th width=10%>��ȣ</th>
	<th width=15%>�߻��Ͻ�</th>
	<th>�󼼳���</th>
	<th width=15%>�����ݾ�</th>
	<th width=15%>���ݾ�</th>
</tr>
<tr><td colspan=5 height=1 bgcolor="#D6D6D6" style="padding:0px;"></td></tr>
<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="" style="border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;">
	<td align="center"><?php echo $TPL_V1["idx"]?></td>
	<td align="center"><?php echo $TPL_V1["regdts"]?></td>
	<td><?php echo $TPL_V1["memo"]?></td>
	<td align="center"><?php if($TPL_V1["emoney"]> 0){?><?php echo number_format($TPL_V1["emoney"])?><?php }else{?>��<?php }?></td>
	<td align="center"><?php if($TPL_V1["emoney"]< 0){?><?php echo number_format($TPL_V1["emoney"])?><?php }else{?>��<?php }?></td>
</tr>
<tr><td colspan=5 height=1 bgcolor="#EEEEEE" style="padding:0px;"></td></tr>
<?php }}?>
</table>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>