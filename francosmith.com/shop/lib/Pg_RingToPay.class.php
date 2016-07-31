<?

include "../lib/json.class.php";


class Pg_RingToPay
{
	var $rpay_yn;
	var $godo;

	function Pg_RingToPay() 
	{
		/**************************************
		rpay_yn = Y (�������� ���)
		rpay_yn = N (�������� ������� ����)
		***************************************/
		$this->rpay_yn = 'N';
	
		//godo�ַ������
		$config = Core::loader('config');
		$this->godo = $config->load('godo');
	}
	
	//�������� �������� �о�´�.
	function ringtopayConfig()
	{
		@include "../conf/pg.settlebank.ringtopay.php";
		
		// ���� ���ٸ� �⺻���� �ش�.
		if(!is_array($rpay)) {
			$rpay['state'] = 'N';
			$rpay['date'] =  date( 'YmdHis', strtotime($rpay['date'])+1800);
		}

		return $rpay;
	}
	
	function RtoPConfigRead(){

		//$json class �ε��Ѵ�.
		$json = new Services_JSON();

		//���� �������� ���� �� �о�´�.
		$rpay = $this->ringtopayConfig();

		//���� �ð��� 30���� ���� �������� �簣�� �˾ƿ´�.
		$old_date = date( 'YmdHis', strtotime($rpay['date'])+1800);
		
		//����ð��� 30���� �����ٸ� �����о�´�.
		if(date("YmdHis") >= $old_date ) {

			//godo �����κ��� �������� �о�´�.
			$new_rpay =  get_object_vars($json->decode(stripslashes($this->url_Reader())));

			//���θ� ������ȣ�� ������ �������� ������ȣ�� ������ Ȯ���Ѵ�.
			if($new_rpay['basic_sno'] == $this->godo['sno']) {

				//���ο� ������ �����Ѵ�.
				$ret['state'] = $new_rpay['rpay_yn'];
				$ret['date'] = date('YmdHis');
				$this->save("../conf/pg.settlebank.ringtopay.php", "rpay", $ret);

				//���ο� �������� �о� �´�.
				$rpay = $this->ringtopayConfig();

			}
		}

		$this->rpay_yn = $rpay['state'];
		
	}
	
	function getRpay_yn()
	{	
		return $this->rpay_yn;
	}

	//godo����Ʈ�κ��� ringtopay ��뿩�� ������ �о�´�.
	//�ش�URl���� html���� �о�´�.
	function url_Reader()
	{
		$url = "http://sapi.godo.co.kr/pg/policy/get_ring2pay_policy.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//���� ��ȸ
		$ret = curl_exec($ch);
		
		//���� ó��
		if( curl_error($ch) || $ret == false){
			$ret = false;
		}
		
		//curl ���Ǵݱ�
		curl_close($ch);
		
		return $ret;
	} 
	
	//�������� 
	function save($configFile, $configName, $cfgContent)
	{	
		$qfile = new qfile();
		$qfile->open($configFile);
		$qfile->write("<? \n");
		
		foreach ($cfgContent as $k=>$v){
			//1���迭
			if (!is_array($v)) {
				$qfile->write("\$".$configName."['".$k."'] = '".$v."'; \n");

			//2���迭
			} else {
				foreach ($v as $k2=>$v2){
					$qfile->write("\$".$configName."['".$k."']['".$k2."'] = '".$v2."'; \n");
				}
			}
		}
		
		$qfile->write("?>");
		$qfile->close();

		//���� ���Ѽ���
		chmod($configFile,0707);
	}

}
?>