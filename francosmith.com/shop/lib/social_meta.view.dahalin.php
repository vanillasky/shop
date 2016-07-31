<?
return;
$_meta_site_info['dahalin'] = array('name'=>'������','url'=>'http://www.dahalin.co.kr/');

// ������ ��
class social_meta_view_dahalin extends social_meta_view {

	var $data = array();

	function social_meta_view_dahalin() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<rss version=\'2.0\'>';
		$_xml .= '<channel>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			switch($row['usable_spot_type']) {
				case '����,��ǰ':
					$row['usable_spot_type'] = 'EZ';
					break;
				case '�м�,��Ƽ':
					$row['usable_spot_type'] = 'DZ';
					break;
				case '��Ȱ,�ǰ�':
					$row['usable_spot_type'] = 'FZ';
					break;
				case '���,���Ƶ�':
					$row['usable_spot_type'] = 'AZ';
					break;
				case '������,����':
					$row['usable_spot_type'] = 'CZ';
					break;
				case '������,����':
				case '����,����':
					$row['usable_spot_type'] = 'HZ';
					break;
				case '����,��ȭ,���':
				case '�ڵ���':
				case '����,ħ��':
				default:
					$row['usable_spot_type'] = 'ZZ';
					break;
			}


			$row = $this->encode($row);

			$_xml .= '
			<item>
				<guid><![CDATA['.$row['tgsno'].']]></guid>
				<link><![CDATA['.$row['url'].']]></link>
				<title><![CDATA[['.$row['goodsnm'].']]></title>
				<subtitle><![CDATA['.$row['goodsnm'].']]></subtitle>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<category><![CDATA['.$row['usable_spot_type'].']]></category>
				<minCnt><![CDATA['.$row['limit_ea'].']]></minCnt>
				<maxCnt><![CDATA['.$row['maxstock'].']]></maxCnt>
				<curCnt><![CDATA['.$row['buyercnt'].']]></curCnt>
				<pubDate><![CDATA['.$row['regdt'].']]></pubDate>
				<image><![CDATA['.$row['image'].']]></image>
				<price><![CDATA['.$row['consumer'].']]></price>
				<dcPrice><![CDATA['.$row['price'].']]></dcPrice>
				<dcRate><![CDATA['.$row['dc_rate'].']]></dcRate>
				<dcInfo></dcInfo>
				<shipFree>'.(($row['goodstype'] != 'coupon' && $row['delivery_type'] != 1) ? 'N' : 'Y').'</shipFree>
				<itemInfo></itemInfo>
				<begin><![CDATA['.$row['startdt'].']]></begin>
				<end><![CDATA['.$row['enddt'].']]></end>
				<update><![CDATA['.$row['updatedt'].']]></update>
			</item>
			';

		}	// for

		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}

}

?>