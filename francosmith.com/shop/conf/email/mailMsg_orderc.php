<DIV 
style="BORDER-BOTTOM: #cfcfcf 2px solid; BORDER-LEFT: #cfcfcf 2px solid; PADDING-BOTTOM: 5px; PADDING-LEFT: 5px; WIDTH: 100px; PADDING-RIGHT: 5px; BORDER-TOP: #cfcfcf 2px solid; BORDER-RIGHT: #cfcfcf 2px solid; PADDING-TOP: 5px">
<TABLE style="FONT: 9pt ����">
<TBODY>
<TR>
<TD><IMG src="/shop/admin/img/mail/mail_bar_order.gif"></TD></TR>
<TR>
<TD 
style="PADDING-BOTTOM: 5px; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; PADDING-TOP: 5px" 
height=400 vAlign=top>
<DIV 
style="PADDING-BOTTOM: 10px; LINE-HEIGHT: 150%; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; PADDING-TOP: 10px">����, 
���� ���θ��� �̿��� �ּż� �����մϴ�.<BR>{nameOrder}�Բ��� �ֹ��Ͻ� ��ǰ�� �ֹ� ���� �Ǿ����ϴ�.<BR>�ֹ����� �� ���� ������ 
�������������� �ֹ�/�����ȸ���� Ȯ���Ͻ� �� �ֽ��ϴ�.<BR>���� ��ǥ���� �ִ� ������ ���,&nbsp;������ȣ�� ���� ��ǥ���� �����Ͽ� �Ǹ� 
�����ÿ� �ϰ� �߼۵˴ϴ�.<BR></DIV>
<DIV 
style="BORDER-BOTTOM: #efefef 5px solid; BORDER-LEFT: #efefef 5px solid; PADDING-BOTTOM: 5px; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; BORDER-TOP: #efefef 5px solid; BORDER-RIGHT: #efefef 5px solid; PADDING-TOP: 5px">
<DIV 
style="PADDING-BOTTOM: 0px; PADDING-LEFT: 10px; PADDING-RIGHT: 0px; BACKGROUND: #f7f7f7; HEIGHT: 25px; PADDING-TOP: 7px"><B>- 
���Ż�ǰ ����</B></DIV>
<TABLE style="FONT: 9pt ����" cellSpacing=0 cellPadding=0 width="100%">
<TBODY>
<TR>
<TD bgColor=#303030 height=2 colSpan=5></TD></TR>
<TR height=23 bgColor=#f0f0f0>
<TH class=input_txt colSpan=2>��ǰ����</TH>
<TH class=input_txt>�ǸŰ�</TH>
<TH class=input_txt>����</TH>
<TH class=input_txt>�հ�</TH></TR>
<TR>
<TD bgColor=#d6d6d6 height=1 colSpan=5></TD></TR>
<COLGROUP>
<COL width=60>
<COL>
<COL width=80>
<COL width=50>
<COL width=80></COLGROUP>
<TBODY>
<TR>
<TD height=60 align=middle>{=goodsimg(img,40,'',3)}</TD>
<TD>
<DIV>{goodsnm} {option}</DIV></TD>
<TD style="PADDING-RIGHT: 10px" align=right>{=number_format(price)}��</TD>
<TD align=middle>{ea}��</TD>
<TD style="PADDING-RIGHT: 10px" 
align=right>{=number_format((price)*ea)}��</TD></TR>
<TR>
<TD bgColor=#dedede height=1 colSpan=5></TD></TR>
<TR>
<TD bgColor=#f7f7f7 height=60 colSpan=5 align=right>���ֹ��ݾ� &nbsp;<B 
id=cart_totalprice class=red>{=number_format(totalprice)}</B>�� &nbsp; </TD></TR>
<TR>
<TD bgColor=#efefef height=1 colSpan=5></TD></TR>
<TR>
<TD height=10 colSpan=5><STRONG></STRONG></TD></TR></TBODY></TABLE>
<DIV 
style="PADDING-BOTTOM: 0px; PADDING-LEFT: 10px; PADDING-RIGHT: 0px; BACKGROUND: #f7f7f7; HEIGHT: 25px; PADDING-TOP: 7px"><B>- 
�ֹ��� ����</B></DIV>
<TABLE style="FONT: 9pt ����" cellPadding=2>
<COLGROUP>
<COL width=100></COLGROUP>
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
style="PADDING-BOTTOM: 0px; PADDING-LEFT: 10px; PADDING-RIGHT: 0px; BACKGROUND: #f7f7f7; HEIGHT: 25px; PADDING-TOP: 7px"><B>- 
�޴� ��� ����</B></DIV>
<TABLE style="FONT: 9pt ����" cellPadding=2>
<COLGROUP>
<COL width=100>
<COLGROUP>
<TBODY>
<TR>
<TD height=5></TD></TR>
<TR>
<TD>�����ô� ��</TD>
<TD>{nameReceiver}</TD></TR>
<TR>
<TD>�ڵ���</TD>
<TD>{mobileReceiver}</TD></TR>
<TR>
<TD>���ϴ� �޼���</TD>
<TD>{memo}</TD></TR>
<TR>
<TD height=10></TD></TR></TBODY></TABLE></DIV></TD></TR>
<TR>
<TD bgColor=#cfcfcf height=1></TD></TR>
<TR>
<TD 
style="PADDING-BOTTOM: 10px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; PADDING-TOP: 10px" 
align=middle>
<TABLE>
<TBODY>
<TR>
<TD rowSpan=2><!--{ @ dataBanner( 92 ) }-->{.tag}<!--{ / }--></TD>
<TD><IMG src="/shop/admin/img/mail/mail_bottom.gif"></TD></TR>
<TR>
<TD style="FONT: 8pt tahoma">Copyright(C) <STRONG><FONT 
color=#585858>{cfg.shopName} </FONT></STRONG>All right 
reserved.</TD></TR></TBODY></TABLE></TD></TR>
<TR>
<TD bgColor=#cfcfcf height=10></TD></TR></TBODY></TABLE></DIV>