<?php

/***
 * �ڵ����ϼ����� ���� ���Ϲ߼� Ŭ����
*/

class automail {

	var $mail;
	var $tpl;
	var $mode=false;
	var $to_mail=false;
	
	// mail�� tplŬ������ ����
	function automail() {
		include_once dirname(__FILE__)."/../Template_/Template_.class.php";
		include_once dirname(__FILE__)."/../lib/mail.class.php";
		$this->db = $GLOBALS['db'];
		$this->mail = new Mail($params);
		$this->tpl = new Template_;
	}

	// $mode=�����,$to_mail=�޴¸����ּ�,$cfg=���θ�ȯ�漳������
	function _set($mode,$to_mail,$cfg) {
		$this->mode=$mode;
		$this->to_mail=$to_mail;
		$this->cfg=$cfg;

		$this->tpl->template_dir = dirname(__FILE__)."/../conf/email";
		$this->tpl->compile_dir = dirname(__FILE__)."/../Template_/_compiles/$cfg[tplSkin]/conf/email";
		$this->tpl->define('tpl',"tpl_$mode.php");
		$this->tpl->assign("cfg",$cfg);
	}

	// �������
	function _assign($arg,$arg2=null) {
		if (is_array($arg)) $this->tpl->assign($arg);
		else $this->tpl->assign($arg,$arg2);
	}

	// �߼�
	function _send() {
		if($this->mode===false) return false;
		if($this->to_mail===false) return false;

		include dirname(__FILE__)."/../conf/email/subject_{$this->mode}.php";
		$headers['Name']    = $this->cfg[shopName];
		$headers['From']    = $this->cfg[adminEmail];
		$headers['To']		= $this->to_mail;

		return $this->mail->send($headers, $this->tpl->fetch('tpl'));
	}

	function _assign_tpl($ordno) {
		GLOBAL $db;
		GLOBAL $r_settlekind;

		$query="SELECT a.prn_settleprice, a.nameOrder,a.email,a.goodsprice,a.settleprice,a.settlekind,a.zipcode,a.zonecode,a.address,a.nameReceiver,a.phoneReceiver,a.deliverycode,a.delivery,b.deliveryno,b.deliveryurl
		FROM ".GD_ORDER." a LEFT OUTER JOIN ".GD_LIST_DELIVERY." b on a.deliveryno=b.deliveryno WHERE a.ordno='".$ordno."' ";
		$data = $db->fetch($query,1);

		$row[goodslink] = 'http://'.$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir].'/goods/goods_view.php?goodsno=';	//��ǰ�󼼺��⸵ũ

		$query = "
		SELECT a.goodsno,a.goodsnm,a.opt1,a.opt2,a.addopt,a.reserve,a.price,a.ea,b.img_s
		FROM ".GD_ORDER_ITEM." AS a
		INNER join ".GD_GOODS." AS b on a.goodsno=b.goodsno
		where
		a.ordno = '".$ordno."' and istep <= 4 ";
		$res = $db->query($query);
		$orderInfo_body='';
		while ($sub=$db->fetch($res)){
			$goods[goodsno]=$sub[goodsno];
			$goods[goodsimg]=goodsimg($sub[img_s],50,'',4);
			$goods[reserve] =number_format($sub[reserve]*$sub[ea]);	//������
			$goods[price]=number_format($sub[price]);	//���ϻ�ǰ��
			$goods[ea]=number_format($sub[ea]);	//����
			$goods[sumprice]=number_format($sub[price]*$sub[ea]);	//��ǰ�հ�
			$goods[goodsinfo]=$sub[goodsnm];	//��ǰ����
			if($sub[opt1])$goods[goodsinfo].='['.$sub[opt1].']';
			if($sub[opt2])$goods[goodsinfo].='['.$sub[opt2].']';
			if($sub[addopt])$goods[goodsinfo].='<div>['.str_replace("^","] [",$sub[addopt]).']</div>';

			//ġȯ�ڵ�
			$orderInfo_body.='<tr><td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000" align="left">
			<table><tr><td style="font:12px ����;padding-left:10px"><a href="'.$row[goodslink].$goods[goodsno].'" target="_blank">'.$goods[goodsimg].'</a></td>
					<td style="font:12px ����;padding-left:20px">'.$goods[goodsinfo];
			$orderInfo_body.='</td>
				</tr></table></td><td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[reserve].' ��</td>
			<td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[price].' </td>
			<td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[ea].' ��</td>
			<td style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">'.$goods[sumprice].' ��</td></tr>';
			$totalGoodsPrice+=$sub[price]*$sub[ea];
			$item[] = $goods;
		}
		$row[goodsprice]=number_format($totalGoodsPrice);//��ǰ�հ�ݾ�
		$row[delivery]=number_format($data[delivery]);//��ۺ�
		$row[totalprice]=number_format($totalGoodsPrice+$data[delivery]);	//�� �ֹ��ݾ�
		$row[settleprice]=number_format($data[prn_settleprice]);//�����ݾ�

		$row[zipcode]= ($data['zonecode']) ? $data['zonecode'] : $data['zipcode']; //�����ȣ
		$row[address]=$data[address];	//���ּ�
		$row[nameReceiver]=$data[nameReceiver];	//�޴»��
		$row[phoneReceiver]=$data[phoneReceiver];	//�޴���ȭ��ȣ
		$row[deliverycode]=$data[deliverycode];	//����ڵ�
		$row[deliveryurl]=$data[deliveryurl];	//���url
		$row[nameOrder]=$data[nameOrder];	//�ֹ���

		##ġȯ�ڵ�##
		//���� �� ��������
		$orderInfo_header='<table style="width:100%;border-bottom:2px solid #dcdcdc;font-family:����,dotum;font-size:12px;text-align:center;border-collapse:collapse;width:640px" border="0" cellspacing="0"  >
		<colgroup><col ><col width="10%"><col width="10%"><col width="10%"><col width="10%"></colgroup><thead>
			<tr><th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:����,dotum;font-size:12px;font-weight:bold">��ǰ����</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:����,dotum;font-size:12px;font-weight:bold">������</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:����,dotum;font-size:12px;font-weight:bold">�ǸŰ�</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:����,dotum;font-size:12px;font-weight:bold">����</th>
				<th scope="col" style="height: 34px;padding:7px 0 4px;border-top:2px solid #e6e6e6;border-right:1px solid #e6e6e6;border-left:1px solid #e6e6e6;background-color:#F6F6F6;color:#000;font-family:����,dotum;font-size:12px;font-weight:bold">�հ�</th>
		</tr></thead><tbody>';

		$orderInfo_footer='</tbody><tfoot style="background-color:#f5f7f9"><tr><td align="left" colspan="5" style="padding:6px 0 4px;border:1px solid #e6e6e6;color:#000">
						&nbsp;&nbsp;��ǰ�հ�ݾ� &nbsp;<B>'.($row[goodsprice]).'</B>�� &nbsp; + &nbsp;
						��ۺ�&nbsp;<B>'.($row[delivery]).'</B>��&nbsp; =&nbsp;���ֹ��ݾ� &nbsp;<B>'.($row[settleprice]).'</B>��</td></tr></tfoot></table>';

		$orderInfo=$orderInfo_header.$orderInfo_body.$orderInfo_footer;
		$row['str_settlekind'] = $r_settlekind[$data['settlekind']];

		//��������
		$settleInfo='<div style="border:1px solid #e6e6e6;width:640px;height:70px;font:12px ����;color:#000;line-height:20px;width:640px;padding-top:5px">
			&nbsp;&nbsp;������� : '.$row['str_settlekind'].' <br/>
			&nbsp;&nbsp;�ֹ��ݾ� : '.$row['goodsprice'].' �� <br/>
			&nbsp;&nbsp;�����ݾ� : '.$row['settleprice'].' ��
		</div>';
		
		//�������
		$deliveryInfo='<div style="border:1px solid #e6e6e6;width:640px;height:70px;font:12px ����;color:#000;line-height:20px;padding-top:5px;">
			&nbsp;&nbsp;���ó : ['.$row['zipcode'].'] '.$row['address'].' <br/>
			&nbsp;&nbsp;�޴º� : '.$row['nameReceiver'].' <br/>
			&nbsp;&nbsp;����ó : '.$row['phoneReceiver'].'
		</div>';
		
		$deliveryLink='<a href="'.$row[deliveryurl].$row[deliverycode].'" target=_blank>'.$row[deliverycode].'</A>';

		$this->_assign($row);
		$this->_assign('item',$item);
		$this->_assign('deliveryLink',$deliveryLink);
		$this->_assign('orderInfo',$orderInfo);
		$this->_assign('settleInfo',$settleInfo);
		$this->_assign('deliveryInfo',$deliveryInfo);
	}
}
?>