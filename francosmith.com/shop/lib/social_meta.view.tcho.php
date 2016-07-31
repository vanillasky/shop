<?
$_meta_site_info['tcho'] = array('name'=>'Ƽ�����̽�','url'=>'http://www.tcho.co.kr');

// Ƽ�����̽� ��
class social_meta_view_tcho extends social_meta_view {

	var $data = array();

	function social_meta_view_tcho() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<products>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$tmp = explode(' ',$row['usable_spot_address']);
			$row['area'] = array_shift($tmp);
			$row['area2'] = array_shift($tmp);
			if ($row['area'] == '�泲' || $row['area'] == '���') $row['area'] = '��û';

			switch($row['usable_spot_type']) {
				case '����,��ǰ':
					$row['usable_spot_type'] = '����/�ܽ�';
					break;
				case '�м�,��Ƽ':
					$row['usable_spot_type'] = '��Ƽ/�̿�';
					break;
				case '������,����':
					$row['usable_spot_type'] = '����/������';
					break;
				case '�ڵ���':
				case '���,���Ƶ�':
				case '������,����':
				case '����,ħ��':
				case '��Ȱ,�ǰ�':
					$row['usable_spot_type'] = '��Ȱ/�ǰ�';
					break;
				case '����,��ȭ,���':
					$row['usable_spot_type'] = '����/��ȭ';
					break;
				case '����,����':
					$row['usable_spot_type'] = '����/����';
					break;
				default:
					$row['usable_spot_type'] = '��Ÿ';
					break;
			}

			// ��� �Ǹ� �϶� (=startdt, enddt �� null �϶�)
			if ($row['startdt'] == null && $row['enddt'] == null) {
				$row['startdt'] = $row['updatedt'];
				$row['enddt'] = date('Y-m-d H:i:s', time() + 2592000);
			}

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<prod_id>'.$row['tgsno'].'</prod_id>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<url><![CDATA['.$row['url'].']]></url>
				<descript><![CDATA['.$row['shortdesc'].']]></descript>
				<image><![CDATA['.$row['image'].']]></image>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
				<ticketstartdate><![CDATA['.$row['usestartdt'].']]></ticketstartdate>
				<ticketenddate><![CDATA['.$row['useenddt'].']]></ticketenddate>
				<price><![CDATA['.$row['consumer'].']]></price>
				<saleprice><![CDATA['.$row['price'].']]></saleprice>
				<salerate><![CDATA['.$row['dc_rate'].']]></salerate>
				<mincnt><![CDATA['.$row['limit_ea'].']]></mincnt>
				<maxcnt><![CDATA['.$row['maxstock'].']]></maxcnt>
				<salecnt><![CDATA['.$row['buyercnt'].']]></salecnt>
				<shop_name><![CDATA['.$row['usable_spot_name'].']]></shop_name>
				<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
				<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
				<region1><![CDATA['.$row['area'].']]></region1>
				<region2><![CDATA['.$row['area2'].']]></region2>
				<category><![CDATA['.$row['usable_spot_type'].']]></category>
				<mobilesupportpayment>N</mobilesupportpayment>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}
}
?>