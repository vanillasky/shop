<?
$_meta_site_info['coupondeal'] = array('name'=>'������','url'=>'http://www.coupondeal.co.kr');

// ������ ��
class social_meta_view_coupondeal extends social_meta_view {

	var $data = array();

	function social_meta_view_coupondeal() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<rss version=\'2.0\' xmlns:dc=\'http://purl.org/dc/elements/1.1/\'>';
		$_xml .= '<channel>';
		$_xml .= '<title><![CDATA['.$this->cfg['shopName'].']]></title>';
		$_xml .= '<link><![CDATA[http://'.$this->cfg['shopUrl'].']]></link>';
		$_xml .= '<description></description>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			switch($row['usable_spot_type']) {
				case '����,��ǰ':
					$row['usable_spot_type'] = '����/��ǰ';
					break;
				case '�м�,��Ƽ':
					$row['usable_spot_type'] = '�м�/��Ƽ';
					break;
				case '��Ȱ,�ǰ�':
					$row['usable_spot_type'] = '�ǰ�/��Ȱ';
					break;
				case '���,���Ƶ�':
					$row['usable_spot_type'] = '���/����';
					break;
				case '������,����':
					$row['usable_spot_type'] = '����/������';
					break;
				case '������,����':
					$row['usable_spot_type'] = '������/����';
					break;
				case '����,����':
					$row['usable_spot_type'] = '����/����';
					break;
				case '����,��ȭ,���':
					$row['usable_spot_type'] = '����/��ȭ/���';
					break;
				case '�ڵ���':
					$row['usable_spot_type'] = '����/��ǰ';
					break;
				case '����,ħ��':
					$row['usable_spot_type'] = '����/ħ��';
					break;
				default:
					$row['usable_spot_type'] = '��Ÿ';
					break;
			}

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<categoryname><![CDATA['.$row['usable_spot_type'].']]></categoryname>
				<areaname><![CDATA['.$row['area'].']]></areaname>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<image><![CDATA['.$row['image'].']]></image>
				<originprice>'.$row['consumer'].'</originprice>
				<dcprice>'.$row['price'].'</dcprice>
				<dcpercent>'.$row['dc_rate'].'</dcpercent>
				<mincount>'.$row['limit_ea'].'</mincount>
				<maxcount>'.$row['maxstock'].'</maxcount>
				<curcount>'.$row['buyercnt'].'</curcount>
				<close>'.$row['buyercnt'].'</close>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>