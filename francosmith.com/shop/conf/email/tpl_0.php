<DIV 
style="BORDER-RIGHT: #cfcfcf 2px solid; PADDING-RIGHT: 5px; BORDER-TOP: #cfcfcf 2px solid; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; BORDER-LEFT: #cfcfcf 2px solid; WIDTH: 100px; PADDING-TOP: 5px; BORDER-BOTTOM: #cfcfcf 2px solid">
<TABLE style="FONT: 9pt 굴림">
<TBODY>
<TR>
<TD><IMG src="/shop/admin/img/mail/mail_bar_order.gif"></TD></TR>
<TR>
<TD 
style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" 
vAlign=top height=400>
<DIV 
style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; LINE-HEIGHT: 150%; PADDING-TOP: 10px">고객님, 
저희 쇼핑몰을 이용해 주셔서 감사합니다.<BR>{nameOrder}님께서 주문하신 제품이 주문 접수 되었습니다.<BR>주문내역 및 배송정보는 
MY Shopping에서 주문/배송조회에서 확인하실 수 있습니다.<BR>고객님께 빠르고 정확하게 제품이 전달될 수 있도록 최선을 
다하겠습니다.</DIV>
<DIV 
style="BORDER-RIGHT: #efefef 5px solid; PADDING-RIGHT: 5px; BORDER-TOP: #efefef 5px solid; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; BORDER-LEFT: #efefef 5px solid; PADDING-TOP: 5px; BORDER-BOTTOM: #efefef 5px solid">
<DIV 
style="PADDING-RIGHT: 0px; PADDING-LEFT: 10px; BACKGROUND: #f7f7f7; PADDING-BOTTOM: 0px; PADDING-TOP: 7px; HEIGHT: 25px"><B>- 
주문자 정보</B></DIV>
<TABLE style="FONT: 9pt 굴림" cellPadding=2>
<COLGROUP>
<COL width=100>
<TBODY>
<TR>
<TD height=5></TD></TR>
<TR>
<TD>주문번호</TD>
<TD><B>{ordno}</B></TD></TR>
<TR>
<TD>주문하시는 분</TD>
<TD>{nameOrder}</TD></TR>
<TR>
<TD>전화번호</TD>
<TD>{phoneOrder}</TD></TR>
<TR>
<TD>핸드폰</TD>
<TD>{mobileOrder}</TD></TR>
<TR>
<TD>결제방법</TD>
<TD>{str_settlekind}</TD></TR>
<TR>
<TD>결제금액</TD>
<TD><STRONG>{=number_format(settleprice)}원</STRONG></TD></TR>
<TR>
<TD height=10><STRONG></STRONG></TD></TR></TBODY></TABLE>
<DIV 
style="PADDING-RIGHT: 0px; PADDING-LEFT: 10px; BACKGROUND: #f7f7f7; PADDING-BOTTOM: 0px; PADDING-TOP: 7px; HEIGHT: 25px"><B>- 
배송 정보</B></DIV>
<TABLE style="FONT: 9pt 굴림" cellPadding=2>
<COLGROUP>
<COL width=100>
<TBODY>
<TR>
<TD height=5></TD></TR>
<TR>
<TD>받으시는 분</TD>
<TD>{nameReceiver}</TD></TR>
<TR>
<TD>주소</TD>
<TD>[{zipcode}] {address}</TD></TR>
<TR>
<TD>전화번호</TD>
<TD>{phoneReceiver}</TD></TR>
<TR>
<TD>핸드폰</TD>
<TD>{mobileReceiver}</TD></TR>
<TR>
<TD>배송메세지</TD>
<TD>{memo}</TD></TR>
<TR>
<TD height=10></TD></TR></TBODY></TABLE>
<DIV 
style="PADDING-RIGHT: 0px; PADDING-LEFT: 10px; BACKGROUND: #f7f7f7; PADDING-BOTTOM: 0px; PADDING-TOP: 7px; HEIGHT: 25px"><B>- 
구매상품 정보</B></DIV>
<TABLE style="FONT: 9pt 굴림" cellSpacing=0 cellPadding=0 width="100%">
<TBODY>
<TR>
<TD bgColor=#303030 colSpan=10 height=2></TD></TR>
<TR bgColor=#f0f0f0 height=23>
<TH class=input_txt colSpan=2>상품정보</TH>
<TH class=input_txt>적립금</TH>
<TH class=input_txt>판매가</TH>
<TH class=input_txt>수량</TH>
<TH class=input_txt>합계</TH></TR>
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
<DIV>(무료배송)</DIV><!--{ / }--></TD>
<TD align=middle>{=number_format(.reserve)}원</TD>
<TD style="PADDING-RIGHT: 10px" align=right>{=number_format(.price + 
.addprice)}원</TD>
<TD align=middle>{.ea}개</TD>
<TD style="PADDING-RIGHT: 10px" align=right>{=number_format((.price + 
.addprice)*.ea)}원</TD></TR>
<TR>
<TD bgColor=#dedede colSpan=10 height=1></TD></TR><!--{ / }-->
<TR>
<TD align=right bgColor=#f7f7f7 colSpan=10 height=60>상품합계금액 &nbsp;<B 
id=cart_goodsprice>{=number_format(cart->goodsprice)}</B>원 &nbsp; + &nbsp; 
배송비&nbsp;<!--{ ? deli_msg }-->{deli_msg}<!--{ : }--><B 
id=cart_delivery>{=number_format(cart->delivery)}</B>원<!--{ / }-->&nbsp; = 
&nbsp; 총주문금액 &nbsp;<B class=red 
id=cart_totalprice>{=number_format(cart->totalprice)}</B>원 &nbsp; </TD></TR>
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