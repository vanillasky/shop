<?
$design_skin = array();

$design_skin['default'] = array(
'outline_header'			=> 'outline/header/standard.htm',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'outline/footer/standard.htm',
'outline_sidefloat'			=> 'left',
);

$design_skin['outline/_header.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '��ܷ��̾ƿ�',
);

$design_skin['outline/_footer.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_header'			=> 'outline/header/main.htm',
'outline_side'			=> 'outline/side/cs.htm',
'outline_footer'			=> 'outline/footer/main_footer.htm',
'text'			=> '�ϴܷ��̾ƿ�',
'_et'			=> 'codemirror',
);

$design_skin['outline/footer/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '�ϴܱ⺻Ÿ��',
'_et'			=> 'codemirror',
);

$design_skin['outline/side/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '����⺻Ÿ��',
);

$design_skin['main/index.htm'] = array(
'linkurl'			=> 'index.php',
'outline_header'			=> 'outline/header/main.htm',
'outline_footer'			=> 'outline/footer/main_footer.htm',
'text'			=> '���θ� ���κ���',
'_et'			=> 'codemirror',
);

$design_skin['goods/list/tpl_01.htm'] = array(
'text'			=> '��������',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/list/tpl_02.htm'] = array(
'text'			=> '����Ʈ��',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/list/tpl_03.htm'] = array(
'text'			=> '����Ʈ �׷���',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/goods_list.htm'] = array(
'text'			=> '�з�ȭ��',
'linkurl'			=> 'goods/goods_list.php',
);

$design_skin['goods/goods_view.htm'] = array(
'text'			=> '��ǰ��ȭ��',
'linkurl'			=> 'goods/goods_view.php',
);

$design_skin['goods/goods_search.htm'] = array(
'text'			=> '�󼼰˻�ȭ��',
'linkurl'			=> 'goods/goods_search.php',
);

$design_skin['goods/goods_today.htm'] = array(
'text'			=> '�ֱٺ���ǰ',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['goods/goods_cart.htm'] = array(
'text'			=> '��ٱ���',
'linkurl'			=> 'goods/goods_cart.php',
);

$design_skin['goods/goods_review.htm'] = array(
'text'			=> '�̿��ı� ����',
'linkurl'			=> 'goods/goods_review.php',
);

$design_skin['goods/goods_review_list.htm'] = array(
'text'			=> '�̿��ı� ���',
'linkurl'			=> 'goods/goods_view.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_review_register.htm'] = array(
'text'			=> '�̿��ı� �ۼ�',
'linkurl'			=> 'goods/goods_review_register.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_review_del.htm'] = array(
'text'			=> '�̿��ı� ����',
'linkurl'			=> 'goods/goods_review_del.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_qna.htm'] = array(
'text'			=> '��ǰ���� ����',
'linkurl'			=> 'goods/goods_qna.php',
);

$design_skin['goods/goods_qna_view.htm'] = array(
'text'			=> '��ǰ���� ��',
'linkurl'			=> 'goods/goods_qna_view.php',
);

$design_skin['goods/goods_qna_list.htm'] = array(
'text'			=> '��ǰ���� ���',
'linkurl'			=> 'goods/goods_view.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_qna_register.htm'] = array(
'text'			=> '��ǰ���� �ۼ�',
'linkurl'			=> 'goods/goods_qna_register.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['goods/goods_qna_del.htm'] = array(
'text'			=> '��ǰ���� ����',
'linkurl'			=> 'goods/goods_qna_del.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['service/company.htm'] = array(
'text'			=> 'ȸ��Ұ�',
'linkurl'			=> 'service/company.php',
);

$design_skin['service/agreement.htm'] = array(
'text'			=> '�̿���',
);

$design_skin['service/guide.htm'] = array(
'linkurl'			=> 'service/guide.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '�̿�ȳ�',
);

$design_skin['service/private.htm'] = array(
'text'			=> '����������޹�ħ',
'linkurl'			=> 'service/private.php',
);

$design_skin['service/sitemap.htm'] = array(
'text'			=> '����Ʈ��',
'linkurl'			=> 'service/sitemap.php',
);

$design_skin['service/cooperation.htm'] = array(
'text'			=> '�������޹���',
);

$design_skin['service/customer.htm'] = array(
'linkurl'			=> 'service/customer.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '������',
);

$design_skin['html/standard.htm'] = array(
'linkurl'			=> 'html/standard.php',
'text'			=> '�߰�����������',
);

$design_skin['html/addtest.htm'] = array(
'text'			=> 'addtest',
);

$design_skin['html/hoho.htm'] = array(
'text'			=> '����',
);

$design_skin['popup/standard.htm'] = array(
'text'			=> '�⺻�˾�',
'popup_use'			=> 'N',
'popup_spotw'			=> '0',
'popup_spoth'			=> '0',
'popup_sizew'			=> '300',
'popup_sizeh'			=> '400',
'linkurl'			=> 'main/html.php?htmid=popup/standard.htm',
);

$design_skin['outline/side/cs.htm'] = array(
'text'			=> '������_left',
'linkurl'			=> 'main/index.php',
);

$design_skin['outline/side/mypage.htm'] = array(
'linkurl'			=> 'member/myinfo.php',
'text'			=> '����������_left',
);

$design_skin['service/faq.htm'] = array(
'linkurl'			=> 'service/faq.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> 'FAQ',
);

$design_skin['member/myinfo.htm'] = array(
'linkurl'			=> 'member/myinfo.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> 'ȸ����������',
);

$design_skin['member/login.htm'] = array(
'text'			=> '�α���',
'linkurl'			=> 'member/login.php',
'outline_side'			=> 'outline/side/cs.htm',
);

$design_skin['member/join.htm'] = array(
'text'			=> 'ȸ������',
'linkurl'			=> 'member/join.php',
'outline_side'			=> 'outline/side/cs.htm',
);

$design_skin['member/find_id.htm'] = array(
'text'			=> '���̵� ã��',
'linkurl'			=> 'member/find_id.php',
'outline_side'			=> 'outline/side/cs.htm',
);

$design_skin['member/find_pwd.htm'] = array(
'linkurl'			=> 'member/find_pwd.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '��й�ȣ ã��',
);

$design_skin['member/hack.htm'] = array(
'linkurl'			=> 'member/hack.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> 'ȸ��Ż��',
);

$design_skin['member/myemoney.htm'] = array(
'text'			=> '�����ݳ���',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/goods_review.htm'] = array(
'text'			=> '���� ��ǰ�ı�',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/goods_qna.htm'] = array(
'text'			=> '���� ��ǰ����',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/member_qna.htm'] = array(
'text'			=> '1:1 ���ǰԽ���',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/orderlist.htm'] = array(
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['member/mywishlist.htm'] = array(
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['main/intro.htm'] = array(
'text'			=> '��Ʈ��',
'linkurl'			=> 'intro.php',
);

$design_skin['proc/_agreement.txt'] = array(
'linkurl'			=> 'service/agreement.php',
'text'			=> '�̿��� ����',
'_et'			=> 'textarea',
);

$design_skin['goods/goods_event.htm'] = array(
'text'			=> '�̺�Ʈ',
'linkurl'			=> 'goods/goods_event.php',
'outline_side'			=> 'noprint',
);

$design_skin['mypage/mypage_coupon.htm'] = array(
'linkurl'			=> 'mypage/mypage_coupon.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '������������',
);

$design_skin['mypage/mypage_emoney.htm'] = array(
'linkurl'			=> 'mypage/mypage_emoney.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '�����ݳ���',
);

$design_skin['mypage/mypage_orderlist.htm'] = array(
'linkurl'			=> 'mypage/mypage_orderlist.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '�ֹ�����/�����ȸ',
);

$design_skin['mypage/mypage_qna.htm'] = array(
'linkurl'			=> 'mypage/mypage_qna.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '1:1����',
);

$design_skin['mypage/mypage_qna_del.htm'] = array(
'text'			=> '1:1���� ����',
'linkurl'			=> 'mypage/mypage_qna_del.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_qna_goods.htm'] = array(
'linkurl'			=> 'mypage/mypage_qna_goods.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '���� ��ǰ����',
);

$design_skin['mypage/mypage_qna_order.htm'] = array(
'text'			=> '1:1���� �ֹ���ȣ�˻�â',
'linkurl'			=> 'mypage/mypage_qna_order.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_qna_register.htm'] = array(
'text'			=> '1:1���� ���',
'linkurl'			=> 'mypage/mypage_qna_register.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_review.htm'] = array(
'linkurl'			=> 'mypage/mypage_review.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '���� ��ǰ�ı�',
);

$design_skin['mypage/mypage_wishlist.htm'] = array(
'text'			=> '��ǰ������',
'linkurl'			=> 'mypage/mypage_wishlist.php',
'outline_side'			=> 'outline/side/mypage.htm',
);

$design_skin['mypage/mypage_today.htm'] = array(
'linkurl'			=> 'mypage/mypage_today.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '�ֱٺ���ǰ���',
);

$design_skin['proc/popup_zipcode.htm'] = array(
'text'			=> '�����ȣ�˻�',
'linkurl'			=> 'proc/popup_zipcode.php',
);

$design_skin['proc/popup_coupon.htm'] = array(
'text'			=> '�������������ϱ�',
'linkurl'			=> 'proc/popup_coupon.php',
);

$design_skin['proc/scroll.js'] = array(
'text'			=> '��ũ�ѹ�� ��ũ��Ʈ',
'linkurl'			=> 'proc/scroll.js',
);

$design_skin['proc/orderitem.htm'] = array(
'text'			=> '��ٱ��� ��ǰ���',
'linkurl'			=> 'goods/goods_cart.php',
);

$design_skin['member/agreement.htm'] = array(
'text'			=> 'ȸ������ �̿���',
'linkurl'			=> 'member/join.php',
);

$design_skin['member/join_ok.htm'] = array(
'text'			=> 'ȸ������ �Ϸ�',
'linkurl'			=> 'member/join_ok.php',
);

$design_skin['member/_form.htm'] = array(
'text'			=> 'ȸ������/���� ��',
'linkurl'			=> 'member/join.php',
);

$design_skin['goods/goods_popup_large.htm'] = array(
'text'			=> '��ǰȮ�뺸��',
'linkurl'			=> 'goods/goods_popup_large.php',
);

$design_skin['goods/list/tpl_04.htm'] = array(
'linkurl'			=> 'goods/goods_list.php',
'text'			=> '��ǰ�̵���',
);

$design_skin['order/order.htm'] = array(
'text'			=> '�ֹ��ϱ�(�ֹ����ۼ�)',
'linkurl'			=> 'order/order.php',
);

$design_skin['order/settle.htm'] = array(
'text'			=> '�����ϱ�(ī��/������)',
'linkurl'			=> 'order/settle.php',
);

$design_skin['order/order_end.htm'] = array(
'text'			=> '�ֹ��Ϸ�',
'linkurl'			=> 'order/order_end.php',
);

$design_skin['order/card.allat.htm'] = array(
'text'			=> '�����ϱ�(�þ� PG)',
'linkurl'			=> 'order/card.allat.php',
);

$design_skin['proc/popup_email.htm'] = array(
'text'			=> '�����ڿ��� ���Ϻ�����',
'linkurl'			=> 'proc/popup_email.php',
'outline_header'			=> 'noprint',
'outline_side'			=> 'noprint',
'outline_footer'			=> 'noprint',
);

$design_skin['mypage/mypage_orderview.htm'] = array(
'linkurl'			=> 'mypage/mypage_orderview.php',
'outline_side'			=> 'outline/side/mypage.htm',
'text'			=> '�ֹ�������������',
);

$design_skin['goods/goods_grp_05.htm'] = array(
'linkurl'			=> 'goods/goods_grp_05.php',
);

$design_skin['board/default/list.htm'] = array(
'text'			=> '���',
'linkurl'			=> 'board/list.php',
);

$design_skin['board/test/list.htm'] = array(
'text'			=> '���',
'linkurl'			=> 'board/test/list.php',
);

$design_skin['board/gallery/list.htm'] = array(
'text'			=> '���',
'linkurl'			=> 'board/list.php',
);

$design_skin['proc/menuCategory.htm'] = array(
'linkurl'			=> 'proc/menuCategory.php',
'text'			=> 'ī�װ��޴�',
);

$design_skin['proc/ccsms.htm'] = array(
'linkurl'			=> 'proc/ccsms.php',
'text'			=> '�����ڿ��� SMS��㹮���ϱ�',
);

$design_skin['popup/layer.htm'] = array(
'text'			=> '���̾��˾�',
'popup_use'			=> 'N',
'popup_spotw'			=> '100',
'popup_spoth'			=> '100',
'popup_sizew'			=> '325',
'popup_sizeh'			=> '445',
'popup_type'			=> 'layer',
'linkurl'			=> 'main/html.php?htmid=popup/layer.htm',
);

$design_skin['service/_private.txt'] = array(
'linkurl'			=> 'service/private.php',
'text'			=> '����������޹�ħ ����',
'_et'			=> 'textarea',
);

$design_skin['proc/test_include.htm'] = array(
'text'			=> '�׽�Ʈ_����Ʈ',
'linkurl'			=> 'main/html.php?htmid=proc/test_include.htm',
);

$design_skin['service/_private.htm'] = array(
'text'			=> '����������޹�ħ (ȸ�����Գ� ������)',
'linkurl'			=> 'service/_private.php',
);

$design_skin['board/1.htm'] = array(
'text'			=> '1',
'linkurl'			=> 'board/1.php',
);

$design_skin['popup/move.htm'] = array(
'text'			=> '�̵����̾�',
'popup_use'			=> 'Y',
'popup_spotw'			=> '130',
'popup_spoth'			=> '130',
'popup_sizew'			=> '325',
'popup_sizeh'			=> '445',
'popup_type'			=> 'layerMove',
'linkurl'			=> 'main/html.php?htmid=popup/move.htm',
);

$design_skin['mypage/settle.htm'] = array(
'linkurl'			=> 'order/settle.php',
'text'			=> '������ϱ�(ī��/������)',
);

$design_skin['outline/footer/main_footer.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_side'			=> 'outline/side/cs.htm',
'outline_footer'			=> 'outline/footer/main_footer.htm',
'text'			=> '���ο� �ϴ�����',
'_et'			=> 'codemirror',
);

$design_skin['outline/header/standard.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '��ܱ⺻����',
'inbg_img'			=> 'outline.header.standard_inbg.gif',
'_et'			=> 'codemirror',
);

$design_skin['outline/header/main.htm'] = array(
'linkurl'			=> 'main/index.php',
'outline_header'			=> 'outline/header/main.htm',
'outline_side'			=> 'outline/side/cs.htm',
'text'			=> '���ο� �������',
'inbg_img'			=> 'outline.header.main_inbg.gif',
'_et'			=> 'codemirror',
);

$design_skin['outline/side/mainleft.htm'] = array(
'linkurl'			=> 'main/index.php',
'text'			=> '���ο� ��������',
);

$design_skin['setGoods/index.htm'] = array(
'linkurl'			=> 'setGoods/index.php',
'outline_side'			=> 'noprint',
'text'			=> '�ڵ�����������',
);

$design_skin['setGoods/content.htm'] = array(
'linkurl'			=> 'setGoods/content.php',
'outline_side'			=> 'noprint',
'text'			=> '�ڵ��������',
);
?>