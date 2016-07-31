<?php

/*

Requirement
1. install mhash0.9.9.9(http://kr.php.net/manual/en/book.mhash.php)
2. install libmcrypt2.5.8+mcrypt2.6.8(http://kr.php.net/manual/en/book.mcrypt.php)
3. php 4.4 or higher with additional "--with-mhash --with-mcrypt --with-dom --with-zlib-dir" options
4. pear packages(PEAR1.9, Message0.6, HTTP_Request1.4.4, Net_URL1.0.15, Net_Socket1.0.9)

*/

require_once 'Message/Message.php';
//require_once 'Message/Hash/SHA256.php'; // pear�� �뷮�� ���̱� ���� ���� �ʴ� �Լ��� ���� ��Ŭ��� ����. 2011.09.28 Heavening@GODO Soft

//AES128 ��ȣ�˰����� ��ϻ�����
define('NHNAPISCL_BLOCK_SIZE', 16);

//AES128 ��ȣ�˰��� ����� ���� Initial Vector
define('NHNAPISCL_IV', "c437b4792a856233c183994afe10a6b2");

class NHNAPISCL
{

	/**
	 * HMAC-SHA256 ���� ����
	 * @param data ������ ������(UTF-8)
	 * @param key ���� ����� ����Ű
	 * @return base64���ڵ��� ���� �Ǵ� PEAR_Error
	 */
	function generateSign($data, $key){

	   if(strlen($data)==0){
		 return new PEAR_Error('invalid data');
	   }

	   if(strlen($key)==0){
		 return new PEAR_Error('invalid key');
	   }

	   $crypt = Message::createHMAC('SHA256', $key);

	   if(PEAR::isError($crypt)){
		  return $crypt;
	   }

	   return $crypt->calc($data,'none','base64');
	}

	/**
	 * AES128 ��ȣ�˰��� ����� ��ȣŰ ����
	 * @param timestamp ��ȣŰ������ ����� ������
	 * @param key ��ȣŰ������ ����� secret
	 * @return hex���ڵ��� ��ȣŰ �Ǵ� PEAR_Error
	 */
	function generateKey($timestamp, $key){

	   if(strlen($timestamp)==0){
		 return new PEAR_Error('invalid timestamp');
	   }

	   if(strlen($key)==0){
		 return new PEAR_Error('invalid key');
	   }

	   $crypt = Message::createHMAC('SHA256', $key);

	   if(PEAR::isError($crypt)){
		  return $crypt;
	   }

	   $hmac = $crypt->calc($timestamp,'none','raw');
	  
	   if(PEAR::isError($hmac)){
		  return $hmac;
	   }

	   for($i = 0; $i < 16; $i++){
			$secretkey .= substr($hmac,$i,1) ^ substr($hmac,$i+16,1);
	   }
	  
	   return bin2hex($secretkey);

	}

	/**
	 * NHN API�� ���Ǵ� Ÿ�ӽ����� ����
	 * @return ���˿� ���� Ÿ�ӽ�����
	 */
	function getTimestamp(){

	   $timestamp=date("Y-m-d\TH:i:s",strtotime("-9hour"));

	   $microtime=substr(microtime(),2,3);

	   return $timestamp.".".$microtime."Z".rand(1000,9999);

	}

	/**
	 * PKCS7 �е�����
	 * @param data �е��� ������
	 * @param block ��ȣ�������� ��ϻ�����
	 * @return pkcs7�е��� �߰��� ������
	 */
	function p7padding($data, $block){

		$len = strlen($data);

		$padding = $block - ($len % $block);

		return $data . str_repeat(chr($padding),$padding);

	}

	/**
	 * PKCS7 �е�����
	 * @param text �е������� ������
	 * @return pkcs7�е��� ������ ������ �Ǵ� PEAR_Error
	 */
	function p7unpadding($text) {

		$pad = ord($text{strlen($text)-1});

		if ($pad > strlen($text)){
			return new PEAR_Error('invalid padding');
		}

		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
			return new PEAR_Error('invalid padding');
		}

		return substr($text, 0, -1 * $pad);

	}

	/**
	 * AES128 ��ȣȭ
	 * @param secret hex���ڵ��� ��ȣŰ
	 * @param text ��ȣȭ�� ��(UTF-8)
	 * @return base64���ڵ��� ��ȣ�� �Ǵ� PEAR_Error
	 */
	function encrypt($secret, $text){

	   if(strlen($secret)==0){
		 return new PEAR_Error('invalid secret');
	   }
	  
	   if(strlen($text)==0){
		 return new PEAR_Error('invalid text');
	   }

	   $padded = $this->p7padding($text, NHNAPISCL_BLOCK_SIZE);

	   $iv=pack('H*',NHNAPISCL_IV);

	   $key=pack('H*',$secret);

	   $ctext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $padded, MCRYPT_MODE_CBC, $iv);

	   return base64_encode($ctext);
	}

	/**
	 * AES128 ��ȣȭ
	 * @param secret hex���ڵ��� ��ȣŰ
	 * @param text base64���ڵ��� ��ȣ��
	 * @return ��ȣȭ�� ��(UTF-8) �Ǵ� PEAR_Error
	 */
	function decrypt($secret, $text){

	   if(strlen($secret)==0){
		 return new PEAR_Error('invalid secret');
	   }

	   if(strlen($text)==0){
		 return new PEAR_Error('invalid text');
	   }

	   $ctext = base64_decode($text);

	   $iv=pack('H*',NHNAPISCL_IV);

	   $key=pack('H*',$secret);

	   $dtext = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ctext, MCRYPT_MODE_CBC, $iv);

	   return $this->p7unpadding($dtext);
	
	}

	/**
	 * SHA256 �ؽ� ����
	 * @param data �ؽ��� ������(UTF-8)
	 * @return hex���ڵ��� �ؽ��� �Ǵ� PEAR_Error
	 */
	function sha256($data){
	   
	   $crypt = Message::createHash('SHA256');

	   if(PEAR::isError($crypt)){
		  return $crypt;
	   }

	   return $crypt->calc($data,'none','hex');

	}
}
