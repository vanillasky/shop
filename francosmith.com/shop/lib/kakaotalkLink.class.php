<?php
/**
* Copyright (c) 2014 GODO Co. Ltd
* All right reserved.
*
* This software is the confidential and proprietary information of GODO Co., Ltd.
* You shall not disclose such Confidential Information and shall use it only in accordance
* with the terms of the license agreement  you entered into with GODO Co., Ltd
*
* Revision History
* Author            Date              Description
* ---------------   --------------    ------------------
* ������            2014.07.31        2014.07.31
*/

/**
* īī���� ��ũ API
*
* @author kakaotalkLink.class.php,  bumyul2000, bumyul2000@godo.co.kr
* @version 1.0.0
* @date 2014-07-31
*
*/
class KakaotalkLink
{
	var $serverLinkUrl = 'http://socialmember.godo.co.kr/kakaoSDK/kakaotalkLink/kakaotalkLink.php'; // �߾Ӽ��� ��ũ��Ʈ ���
	var $kakaoServerType;
	var $kakaoLinkApiType;
	var $checkImageUrl;
	var $imageData = array();
	
	var $fileMaxSize = 512000;	//maximum image size 500KB

	/**
	 * īī���� ��ũ API construct
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return
	 * @date 2013-07-31
	 */
	function KakaotalkLink()
	{
		//�⺻����, �������� ���뿩��
		$this->kakaoServerType = $this->getKakaoServerType();
	}

	/**
	 * īī���� ���� Ÿ��
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return string n (�⺻����), string y (��������)
	 * @date 2013-07-31
	 */
	function getKakaoServerType()
	{
		return 'n';
	}

	/**
	 * īī���� ��ũ API Ÿ��
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * string webButton (�̹���API), string webLink (�ؽ�ƮAPI)
	 * @date 2013-07-31
	 */
	function getKakaoLinkApiType()
	{
		if( $this->imageData['fileExists'] === true && $this->imageData['fileSize'] < $this->fileMaxSize){
			$this->kakaoLinkApiType = 'webButton';
		} else {
			$this->kakaoLinkApiType = 'webLink';
		}
	}

	/**
	 * �̹��� ����
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return 
	 * @date 2013-07-31
	 */
	function setImageData()
	{
		//���� ���翩��
		$imgInfo = @getimagesize($this->checkImageUrl);
		if($imgInfo[2] && is_array($imgInfo)) $this->imageData['fileExists'] = true;
		if( $this->imageData['fileExists'] === true ){
			$this->imageData['fileWidth']  = ( $imgInfo[0] < 80 ) ? 80 : $imgInfo[0] ;
			$this->imageData['fileHeight'] = ( $imgInfo[1] < 80 ) ? 80 : $imgInfo[1] ;
		}

		if (preg_match('/^http(s)?:\/\//', $this->checkImageUrl)){
			//���ϻ�����
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $this->checkImageUrl);
			curl_setopt($ch, CURLOPT_HEADER, true); 
			curl_setopt($ch, CURLOPT_NOBODY, 1); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			$result = curl_exec($ch); 
			$imageHeader = @explode("\r\n", $result);
			$imageContentLength = @explode(":", $imageHeader[6]);
			$this->imageData['fileSize'] = @trim($imageContentLength[1]);
		} else {
			//���ϻ�����
			$this->imageData['fileSize'] = filesize($this->checkImageUrl);
		}
	}

	/**
	 * īī���� ��ũ API ��ũ��Ʈ ��ȯ
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param 
	 * @return string 
	 * @date 2013-07-31
	 */
	function getKakaoScript($param)
	{
		$this->checkImageUrl = ($param['msg_kakao_AbsoluteImgUrl']) ? $param['msg_kakao_AbsoluteImgUrl'] : $param['msg_kakao_imgUrl'];
		$this->setImageData();

		//īī����ũ API Ÿ��üũ
		$this->getKakaoLinkApiType();

		switch($this->kakaoServerType){
			case 'y' :
			
			break;

			case 'n': default:
				$data = $this->setServerData($param);

				return $this->getServerScript($data);
			break;
		}
	}

	/**
	 * �߾Ӽ��� īī���� ��ũ data ����
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param  array $param
	 * @return string $postData 
	 * @date 2013-07-31
	 */
	function setServerData($param)
	{
		$data			= array();		
		$postDataArray	= array();

		$data['kakaoLinkImgUrl']		= $param['msg_kakao_imgUrl'];												//��ǰ�̹��� URL
		$data['kakaoLinkWidth']			= $this->imageData['fileWidth'];											//��ǰ�̹��� width (80px �̻�)
		$data['kakaoLinkHeight']		= $this->imageData['fileHeight'];											//��ǰ�̹��� height (80px �̻�)
		$data['kakaoLinkShopName']		= ($param['msg_kakao1']) ? $param['msg_kakao1'] : $_SERVER['HTTP_HOST'];	//���θ��� (��ư����)
		$data['kakaoLinkGoodsName']		= iconv("UTF-8", "EUC-KR", urldecode($param['msg_kakao2']));				//��ǰ���� (1000�� ����)
		$data['kakaoLinkRedirectUrl']	= $param['msg_kakao3'];														//�����̷�Ʈ URL (developer ������ ��ϵǾ��ִ� url)

		$data = @array_map('urlencode', (array)$data);
		
		$postDataArray[] = 'kakaoLinkGoodsName=' . $data['kakaoLinkGoodsName'];
		$postDataArray[] = 'kakaoLinkShopName=' . $data['kakaoLinkShopName'];
		$postDataArray[] = 'kakaoLinkRedirectUrl=' . $data['kakaoLinkRedirectUrl'];
		$postDataArray[] = 'kakaoLinkApiType=' . $this->kakaoLinkApiType;
		if($data['kakaoLinkImgUrl']){
			$postDataArray[] = 'kakaoLinkImgUrl=' . $data['kakaoLinkImgUrl'];
		}
		if($data['kakaoLinkWidth']){
			$postDataArray[] = 'kakaoLinkWidth=' . $data['kakaoLinkWidth'];
		}
		if($data['kakaoLinkHeight']){
			$postDataArray[] = 'kakaoLinkHeight=' . $data['kakaoLinkHeight'];
		}
		$postData = @implode('&', $postDataArray);

		return $postData;
	}

	/**
	 * ������ �߾Ӽ��� ���
	 * @author bumyul2000, bumyul2000@godo.co.kr
	 * @param string $postData ������ ������
	 * @return string $result
	 * @date 2013-07-31
	 */
	function getServerScript($postData)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->serverLinkUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		$result = curl_exec($ch);

		return base64_decode($result);
	}
}
?>