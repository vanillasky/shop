<?php /* Template_ 2.2.7 2013/08/21 21:00:54 /www/francotr3287_godo_co_kr/shop/conf/email/tpl_0.php 000006092 */  $this->include_("dataBanner");?>
<DIV 
style="BORDER-RIGHT: #cfcfcf 2px solid; PADDING-RIGHT: 5px; BORDER-TOP: #cfcfcf 2px solid; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; BORDER-LEFT: #cfcfcf 2px solid; WIDTH: 100px; PADDING-TOP: 5px; BORDER-BOTTOM: #cfcfcf 2px solid">
<TABLE style="FONT: 9pt ����">
<TBODY>
<TR>
<TD><IMG src="/shop/admin/img/mail/mail_bar_order.gif"></TD></TR>
<TR>
<TD 
style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" 
vAlign=top height=400>
<DIV 
style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; LINE-HEIGHT: 150%; PADDING-TOP: 10px">����, 
���� ���θ��� �̿��� �ּż� �����մϴ�.<BR><?php echo $TPL_VAR["nameOrder"]?>�Բ��� �ֹ��Ͻ� ��ǰ�� �ֹ� ���� �Ǿ����ϴ�.<BR>�ֹ����� �� ��������� 
MY Shopping���� �ֹ�/�����ȸ���� Ȯ���Ͻ� �� �ֽ��ϴ�.<BR>���Բ� ������ ��Ȯ�ϰ� ��ǰ�� ���޵� �� �ֵ��� �ּ��� 
���ϰڽ��ϴ�.</DIV>
<DIV 
style="BORDER-RIGHT: #efefef 5px solid; PADDING-RIGHT: 5px; BORDER-TOP: #efefef 5px solid; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; BORDER-LEFT: #efefef 5px solid; PADDING-TOP: 5px; BORDER-BOTTOM: #efefef 5px solid">
<DIV 
style="PADDING-RIGHT: 0px; PADDING-LEFT: 10px; BACKGROUND: #f7f7f7; PADDING-BOTTOM: 0px; PADDING-TOP: 7px; HEIGHT: 25px"><B>- 
�ֹ��� ����</B></DIV>
<TABLE style="FONT: 9pt ����" cellPadding=2>
<COLGROUP>
<COL width=100>
<TBODY>
<TR>
<TD height=5></TD></TR>
<TR>
<TD>�ֹ���ȣ</TD>
<TD><B><?php echo $TPL_VAR["ordno"]?></B></TD></TR>
<TR>
<TD>�ֹ��Ͻô� ��</TD>
<TD><?php echo $TPL_VAR["nameOrder"]?></TD></TR>
<TR>
<TD>��ȭ��ȣ</TD>
<TD><?php echo $TPL_VAR["phoneOrder"]?></TD></TR>
<TR>
<TD>�ڵ���</TD>
<TD><?php echo $TPL_VAR["mobileOrder"]?></TD></TR>
<TR>
<TD>�������</TD>
<TD><?php echo $TPL_VAR["str_settlekind"]?></TD></TR>
<TR>
<TD>�����ݾ�</TD>
<TD><STRONG><?php echo number_format($TPL_VAR["settleprice"])?>��</STRONG></TD></TR>
<TR>
<TD height=10><STRONG></STRONG></TD></TR></TBODY></TABLE>
<DIV 
style="PADDING-RIGHT: 0px; PADDING-LEFT: 10px; BACKGROUND: #f7f7f7; PADDING-BOTTOM: 0px; PADDING-TOP: 7px; HEIGHT: 25px"><B>- 
��� ����</B></DIV>
<TABLE style="FONT: 9pt ����" cellPadding=2>
<COLGROUP>
<COL width=100>
<TBODY>
<TR>
<TD height=5></TD></TR>
<TR>
<TD>�����ô� ��</TD>
<TD><?php echo $TPL_VAR["nameReceiver"]?></TD></TR>
<TR>
<TD>�ּ�</TD>
<TD>[<?php echo $TPL_VAR["zipcode"]?>] <?php echo $TPL_VAR["address"]?></TD></TR>
<TR>
<TD>��ȭ��ȣ</TD>
<TD><?php echo $TPL_VAR["phoneReceiver"]?></TD></TR>
<TR>
<TD>�ڵ���</TD>
<TD><?php echo $TPL_VAR["mobileReceiver"]?></TD></TR>
<TR>
<TD>��۸޼���</TD>
<TD><?php echo $TPL_VAR["memo"]?></TD></TR>
<TR>
<TD height=10></TD></TR></TBODY></TABLE>
<DIV 
style="PADDING-RIGHT: 0px; PADDING-LEFT: 10px; BACKGROUND: #f7f7f7; PADDING-BOTTOM: 0px; PADDING-TOP: 7px; HEIGHT: 25px"><B>- 
���Ż�ǰ ����</B></DIV>
<TABLE style="FONT: 9pt ����" cellSpacing=0 cellPadding=0 width="100%">
<TBODY>
<TR>
<TD bgColor=#303030 colSpan=10 height=2></TD></TR>
<TR bgColor=#f0f0f0 height=23>
<TH class=input_txt colSpan=2>��ǰ����</TH>
<TH class=input_txt>������</TH>
<TH class=input_txt>�ǸŰ�</TH>
<TH class=input_txt>����</TH>
<TH class=input_txt>�հ�</TH></TR>
<TR>
<TD bgColor=#d6d6d6 colSpan=10 height=1></TD></TR>
<COLGROUP>
<COL width=60>
<COL>
<COL width=60>
<COL width=80>
<COL width=50>
<COL width=80><?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<TBODY>
<TR>
<TD align=middle height=60><?php echo goodsimg($TPL_V1["img"], 40,'', 3)?></TD>
<TD>
<DIV><?php echo $TPL_V1["goodsnm"]?> <?php if($TPL_V1["opt"]){?>[<?php echo implode("/",$TPL_V1["opt"])?>]<?php }?></DIV><?php if((is_array($TPL_R2=$TPL_V1["addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?> 
<?php if($TPL_V1["delivery_type"]== 1){?>
<DIV>(������)</DIV><?php }?></TD>
<TD align=middle><?php echo number_format($TPL_V1["reserve"])?>��</TD>
<TD style="PADDING-RIGHT: 10px" align=right><?php echo number_format($TPL_V1["price"]+$TPL_V1["addprice"])?>��</TD>
<TD align=middle><?php echo $TPL_V1["ea"]?>��</TD>
<TD style="PADDING-RIGHT: 10px" align=right><?php echo number_format(($TPL_V1["price"]+$TPL_V1["addprice"])*$TPL_V1["ea"])?>��</TD></TR>
<TR>
<TD bgColor=#dedede colSpan=10 height=1></TD></TR><?php }}?>
<TR>
<TD align=right bgColor=#f7f7f7 colSpan=10 height=60>��ǰ�հ�ݾ� &nbsp;<B 
id=cart_goodsprice><?php echo number_format($TPL_VAR["cart"]->goodsprice)?></B>�� &nbsp; + &nbsp; 
��ۺ�&nbsp;<?php if($TPL_VAR["deli_msg"]){?><?php echo $TPL_VAR["deli_msg"]?><?php }else{?><B 
id=cart_delivery><?php echo number_format($TPL_VAR["cart"]->delivery)?></B>��<?php }?>&nbsp; = 
&nbsp; ���ֹ��ݾ� &nbsp;<B class=red 
id=cart_totalprice><?php echo number_format($TPL_VAR["cart"]->totalprice)?></B>�� &nbsp; </TD></TR>
<TR>
<TD bgColor=#efefef colSpan=10 
height=1></TD></TR></TBODY></TABLE></DIV></TD></TR>
<TR>
<TD bgColor=#cfcfcf height=1></TD></TR>
<TR>
<TD 
style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" 
align=middle>
<TABLE>
<TBODY>
<TR>
<TD rowSpan=2><?php if((is_array($TPL_R1=dataBanner( 92))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></TD>
<TD><IMG src="/shop/admin/img/mail/mail_bottom.gif"></TD></TR>
<TR>
<TD style="FONT: 8pt tahoma">Copyright(C) <STRONG><FONT color=#585858><?php echo $TPL_VAR["cfg"]["shopName"]?>

</FONT></STRONG>All right reserved.</TD></TR></TBODY></TABLE></TD></TR>
<TR>
<TD bgColor=#cfcfcf height=10></TD></TR></TBODY></TABLE></DIV>