<?
$_meta_site_info['banprice'] = array('name'=>'�ݰ��ݴ���','url'=>'http://www.banprice.co.kr');

// �ݰ��ݴ��� ��
class social_meta_view_banprice extends social_meta_view {

	var $data = array();

	function social_meta_view_banprice() {
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
		$_xml .= '<noticeTitle></noticeTitle>';
		$_xml .= '<noticeHtml></noticeHtml>';
		$_xml .= '<eventTitle></eventTitle>';
		$_xml .= '<eventHtml></eventHtml>';
		$_xml .= '<eventLink></eventLink>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			//ī�װ� �ڵ尪 �Է� 01(����),(��ǰ),(�м�),04(��Ƽ),(������) ,06(����),(���Ƶ�),08(���),(��Ȱ),10(�ǰ�),(��ȭ),12(����),(����),(����)
			switch($row['usable_spot_type']) {
				case '����,��ǰ':
					$row['usable_spot_type'] = '01';
					break;
				case '�м�,��Ƽ':
					$row['usable_spot_type'] = '03';
					break;
				case '��Ȱ,�ǰ�':
					$row['usable_spot_type'] = '09';
					break;
				case '���,���Ƶ�':
					$row['usable_spot_type'] = '07';
					break;
				case '������,����':
					$row['usable_spot_type'] = '05';
					break;
				case '������,����':
					$row['usable_spot_type'] = '14';
					break;
				case '����,����':
					$row['usable_spot_type'] = '13';
					break;
				case '����,��ȭ,���':
					$row['usable_spot_type'] = '11';
					break;
				case '�ڵ���':
					$row['usable_spot_type'] = '09';
					break;
				case '����,ħ��':
					$row['usable_spot_type'] = '09';
					break;
				default:
					$row['usable_spot_type'] = '';
					break;
			}


			$row = $this->encode($row);

			$_xml .= '
			<item>
			  <title><![CDATA['.$row['goodsnm'].']]></title>
			  <pricePub>'.$row['consumer'].'</pricePub>
			  <priceSale>'.$row['price'].'</priceSale>
			  <discount>'.$row['dc_rate'].'</discount>
			  <link><![CDATA['.$row['url'].']]></link>
			  <startDt><![CDATA['.$row['startdt'].']]></startDt>
			  <endDt><![CDATA['.$row['enddt'].']]></endDt>
			  <nowCount>'.$row['buyercnt'].'</nowCount>
			  <nowStock>'.$row['maxstock'].'</nowStock>
			  <category><![CDATA['.$row['usable_spot_type'].']]></category>
			  <areaName><![CDATA['.$row['area'].']]></areaName>
			  <description><![CDATA['.$row['shortdesc'].']]></description>
			  <descriptionHtml><![CDATA[<P>'.$row['shortdesc'].'</P>]]></descriptionHtml>
			  <movieLink></movieLink>
			  <keyword></keyword>
			  <talkLink></talkLink>
			  <status>1</status>
			  <sendStartDt></sendStartDt>
			  <sendEndDt></sendEndDt>
			  <pubDate></pubDate>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>