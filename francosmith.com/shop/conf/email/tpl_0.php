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
���� ���θ��� �̿��� �ּż� �����մϴ�.<BR>{nameOrder}�Բ��� �ֹ��Ͻ� ��ǰ�� �ֹ� ���� �Ǿ����ϴ�.<BR>�ֹ����� �� ��������� 
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
<TD><B>{ordno}</B></TD></TR>
<TR>
<TD>�ֹ��Ͻô� ��</TD>
<TD>{nameOrder}</TD></TR>
<TR>
<TD>��ȭ��ȣ</TD>
<TD>{phoneOrder}</TD></TR>
<TR>
<TD>�ڵ���</TD>
<TD>{mobileOrder}</TD></TR>
<TR>
<TD>�������</TD>
<TD>{str_settlekind}</TD></TR>
<TR>
<TD>�����ݾ�</TD>
<TD><STRONG>{=number_format(settleprice)}��</STRONG></TD></TR>
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
<TD>{nameReceiver}</TD></TR>
<TR>
<TD>�ּ�</TD>
<TD>[{zipcode}] {address}</TD></TR>
<TR>
<TD>��ȭ��ȣ</TD>
<TD>{phoneReceiver}</TD></TR>
<TR>
<TD>�ڵ���</TD>
<TD>{mobileReceiver}</TD></TR>
<TR>
<TD>��۸޼���</TD>
<TD>{memo}</TD></TR>
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
<COL width=80><!--{ @ cart->item }-->
<TBODY>
<TR>
<TD align=middle height=60>{=goodsimg(.img,40,'',3)}</TD>
<TD>
<DIV>{.goodsnm} <!--{ ? .opt }-->[{=implode("/",.opt)}]<!--{ / }--></DIV><!--{ @ .addopt }-->[{..optnm}:{..opt}]<!--{ / }--> 
<!--{ ? .delivery_type == 1 }-->
<DIV>(������)</DIV><!--{ / }--></TD>
<TD align=middle>{=number_format(.reserve)}��</TD>
<TD style="PADDING-RIGHT: 10px" align=right>{=number_format(.price + 
.addprice)}��</TD>
<TD align=middle>{.ea}��</TD>
<TD style="PADDING-RIGHT: 10px" align=right>{=number_format((.price + 
.addprice)*.ea)}��</TD></TR>
<TR>
<TD bgColor=#dedede colSpan=10 height=1></TD></TR><!--{ / }-->
<TR>
<TD align=right bgColor=#f7f7f7 colSpan=10 height=60>��ǰ�հ�ݾ� &nbsp;<B 
id=cart_goodsprice>{=number_format(cart->goodsprice)}</B>�� &nbsp; + &nbsp; 
��ۺ�&nbsp;<!--{ ? deli_msg }-->{deli_msg}<!--{ : }--><B 
id=cart_delivery>{=number_format(cart->delivery)}</B>��<!--{ / }-->&nbsp; = 
&nbsp; ���ֹ��ݾ� &nbsp;<B class=red 
id=cart_totalprice>{=number_format(cart->totalprice)}</B>�� &nbsp; </TD></TR>
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
<TD rowSpan=2><!--{ @ dataBanner( 92 ) }-->{.tag}<!--{ / }--></TD>
<TD><IMG src="/shop/admin/img/mail/mail_bottom.gif"></TD></TR>
<TR>
<TD style="FONT: 8pt tahoma">Copyright(C) <STRONG><FONT color=#585858>{cfg.shopName} 
</FONT></STRONG>All right reserved.</TD></TR></TBODY></TABLE></TD></TR>
<TR>
<TD bgColor=#cfcfcf height=10></TD></TR></TBODY></TABLE></DIV>