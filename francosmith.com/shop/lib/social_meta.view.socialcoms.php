<?
$_meta_site_info['socialcoms'] = array('name'=>'�Ҽ�Ŀ�ӽ�����','url'=>'http://www.socialcoms.com/');

// �Ҽ�Ŀ�ӽ����� ��
class social_meta_view_socialcoms extends social_meta_view {

	var $data = array();

	function social_meta_view_socialcoms() {
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

			switch($row['usable_spot_type']) {
				case '����,��ǰ':
					$row['usable_spot_type'] = '����';
					break;
				case '�м�,��Ƽ':
				case '�ڵ���':
				case '������,����':
				case '����,ħ��':
				case '��Ȱ,�ǰ�':
				case '���,���Ƶ�':
				case '������,����':
				case '����,��ȭ,���':
					$row['usable_spot_type'] = '��Ƽ/��Ȱ';
					break;
				case '����,����':
					$row['usable_spot_type'] = '����/����';
					break;
				default:
					$row['usable_spot_type'] = '��Ÿ';
					break;
			}

			if ($row['area'] == '����') {

				// ���� or ����
				$_gu = array_pop(explode(" ",$row['address1']));


			}
			else if (preg_match('/(���|��õ|����|�뱸|�λ�|����)/',$row['area'])) {

			}
			else {
				$row['area'] = '��Ÿ';

			}

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<url>'.$row['url'].'</url>
				<division>'.$row['usable_spot_type'].'</division>
				<region>'.$row['area'].'</region>
				<name>'.$row['goodsnm'].'</name>
				<image1>'.$row['image'].'</image1>
				<image2>'.$row['image'].'</image2>
				<image3>'.$row['image'].'</image3>
				<descript>'.$row['shortdesc'].'</descript>
				<address>'.$row['address1'].' '.$row['address2'].' '.$row['address3'].'</address>
				<price>'.$row['consumer'].'</price>
				<saleprice>'.$row['price'].'</saleprice>
				<salerate>'.$row['dc_rate'].'</salerate>
				<fullcount>'.$row['maxstock'].'</fullcount>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<maxcnt>'.$row['stock'].'</maxcnt>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<limitdate>'.$row['enddt'].'</limitdate>
				<lng></lng>
				<lat></lat>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}

}

?>