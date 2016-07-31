<?php
/**
 * ��Ƽ�˾� class
 * @author cjb3333 , artherot @ godosoft development team.
 */
class MultiPopup
{
	private $db;
	private $now;
	private $imgField = array(
		'mouseOnImg'=>'N',
		'mouseOutImg'=>'T',
		'mainBannerImg'=>'B',
	);
	private $data_path;
	private $tmp_data_path;
	private $env_code;

	/**
	 * ������
	 */
	public function __construct()
	{
		$this->db				= Core::loader('db');
		$this->data_path		= dirname(__FILE__).'/../data/multipopup/';
		$this->tmp_data_path	= dirname(__FILE__).'/../data/tmp_skinCopy/';
		$this->now				= time();
		$this->env_code			= 'multiPopup';
	}

	/**
	 * ��Ƽ �˾� �ڵ� ����
	 * @return string ��Ƽ �˾� �ڵ� (UNIXTIME)
	 */
	public function getNewCode()
	{
		$newCode	= $this->now;

		return $newCode;
	}

	/**
	 * ��Ƽ �˾� ���� Ȯ��
	 * @param integer $code ��Ƽ �˾� �ڵ�
	 * @return array ��Ƽ �˾� ����
	 */
	public function getPopupData($code)
	{
		$query		= "SELECT * FROM ".GD_ENV." WHERE category = '".$this->env_code."' AND name = '".$code."'";
		$popupData	= $this->db->fetch($query);

		return $popupData;
	}

	/**
	 * ��Ƽ �˾� ���� ����Ʈ
	 * @return array ��Ƽ �˾� ���� ����Ʈ
	 */
	public function getPopupList()
	{
		$query	= "SELECT * FROM ".GD_ENV." WHERE category = '".$this->env_code."' ORDER BY name";
		$result	= $this->db->query($query);

		// ������ json ó���� ��
		while($data = $this->db->fetch($result)) {
			$value[] = gd_json_decode(stripslashes($data[value]));
		}

		return $value;
	}

	/**
	 * POST ������ ���� ó�� (���� �� ������ �ϱ� ���� ����)
	 * @return array ������ ����
	 */
	private function postDataProcessing()
	{
		// Ư���Ⱓ���� �˾�â ����
		if($_POST['popup_dt2tm'] == 'Y'){
			$_POST['popup_sdt']		= $_POST['popup_sdt_tg'];
			$_POST['popup_edt']		= $_POST['popup_edt_tg'];
			$_POST['popup_stime']	= $_POST['popup_stime_tg_h'].$_POST['popup_stime_tg_m'];
			$_POST['popup_etime']	= $_POST['popup_etime_tg_h'].$_POST['popup_etime_tg_m'];
		}
		// Ư���Ⱓ���� Ư���ð����� �˾�â ����
		else if($_POST['popup_dt2tm'] == 'T'){
			$_POST['popup_stime']	= $_POST['popup_stime_h'].$_POST['popup_stime_m'];
			$_POST['popup_etime']	= $_POST['popup_etime_h'].$_POST['popup_etime_m'];
		}
		// �׻� �˾�â ����
		else {
			$_POST['popup_dt2tm']	= 'N';
			unset($_POST['popup_sdt'], $_POST['popup_edt'], $_POST['popup_stime'], $_POST['popup_etime']);
		}

		// �Ⱓ�� ���� ������ ���ڰ� ������ �׻� �˾�â �������� ó��
		if (empty($_POST['popup_sdt']) || empty($_POST['popup_edt'])) {
			$_POST['popup_dt2tm']	= 'N';
			unset($_POST['popup_sdt'], $_POST['popup_edt'], $_POST['popup_stime'], $_POST['popup_etime']);
		}

		// �ʿ� ���� ����Ÿ ����
		unset($_POST['popup_sdt_tg'], $_POST['popup_edt_tg']);
		unset($_POST['popup_stime_tg_h'], $_POST['popup_stime_tg_m'], $_POST['popup_etime_tg_h'], $_POST['popup_etime_tg_m']);
		unset($_POST['popup_stime_h'], $_POST['popup_stime_m'], $_POST['popup_etime_h'], $_POST['popup_etime_m']);

		// ������ ����Ÿ
		$popupConf = array();
		foreach ( $_POST as $k => $v ){
			$popupConf[$k] = $v;
		}

		$_mainBannerImg[0]	= 0;
		$_mainBannerImg[1]	= 0;
		$_mouseOutImg[0]	= 0;
		$_mouseOutImg[1]	= 0;

		//temp ������ �ִ� �̹��� ����
		foreach($popupConf as $key => $value)
		{
			if(array_key_exists($key, $this->imgField))
			{
				// �̹��� ����
				$tmp		= explode('_', $popupConf['displaySet']);
				$row		= $tmp[0];
				$col		= $tmp[1];
				$imgCount	= $row * $col;

				for($k = 1; $k <= $imgCount; $k++)
				{
					$image_name	= $popupConf[$key][$k];

					// �ش� �̹����� �ִ��� üũ
					if (!preg_match('/^http(s)?:\/\/.+$/', $image_name))
					{
						// temp������ �̹����� �ִٸ� ���� ����� �̹���
						if (is_file($this->tmp_data_path.$image_name))
						{
							// ��Ƽ�̹��� �˾��� ���� üũ
							if(!is_dir($this->data_path))
							{
								mkdir($this->data_path);
								chmod($this->data_path,0777);
							}

							// �̹��� �̸�����
							$newImgNm	= str_replace('tmp_','ori_',$image_name);

							// ��Ͻ� �̹��� ������¡ �κ�
							if($key == 'mainBannerImg'){
								thumbnail($this->tmp_data_path.$image_name,$this->data_path.$newImgNm,$popupConf['mainImgSizew'], $popupConf['mainImgSizeh'],1);
							}else{
								thumbnail($this->tmp_data_path.$image_name,$this->data_path.$newImgNm,$popupConf['mouseImgSizew'], $popupConf['mouseImgSizeh'],1);
							}

							$_key	= preg_replace('/^[a-z]_/','',$key);
							@unlink( $this->tmp_data_path.$image_name );					// temp���� ���� ����
							@unlink( $this->data_path.$popupConf['prev_'.$_key][$k] );	// ������ ����� �̹��� ����

							// �̹��� �̸��� ���ο� �̹��� ������ ��ü
							$image_name				= $newImgNm;
							$popupConf[$key][$k]	= $newImgNm;

						}
						// ������ �ö� �̹����� �ִ� ���
						else if(is_file($this->data_path.$image_name)){

							// ������ �̹��� ������¡ �κ�
							if($key == 'mainBannerImg'){
								thumbnail($this->data_path.$image_name,$this->data_path.$image_name,$popupConf['mainImgSizew'], $popupConf['mainImgSizeh'],1);
							}else{
								thumbnail($this->data_path.$image_name,$this->data_path.$image_name,$popupConf['mouseImgSizew'], $popupConf['mouseImgSizeh'],1);
							}
						}
					}

					// �̹����߿� ���� ū �̹����� âũ��� ���
					$imgSize	= @getimagesize($this->data_path.$image_name);

					if($key == 'mainBannerImg') {
						if($imgSize[0] > $_mainBannerImg[0] ) $_mainBannerImg[0] = $imgSize[0];
						if($imgSize[1] > $_mainBannerImg[1] ) $_mainBannerImg[1] = $imgSize[1];

					}
					else {
						if($imgSize[0] > $_mouseOutImg[0] ) $_mouseOutImg[0] = $imgSize[0];
						if($imgSize[1] > $_mouseOutImg[1] ) $_mouseOutImg[1] = $imgSize[1];
					}
				}
			}
		}

		// ���� �Ϸ� �������� ����
		if($popupConf['popup_invisible'] == 'Y') {
			$popupInvisibleHeight	= 20;
		}

		if($_mainBannerImg[0] < ($_mouseOutImg[0] * $row) ){
			$popup_sizew	= floor(($_mouseOutImg[0] * $row)+($popupConf['outlinePadding']*2));	//��ư���� + ��������
			$popup_sizeh	= floor(($_mainBannerImg[1]+($_mouseOutImg[1]*$col))+$popupInvisibleHeight+($popupConf['outlinePadding']*2)+$popupConf['outlinePadding']);	//���ι�ʼ��� + �����̹������� + �����Ϸ纸�ӳ��� + ��������
		}else{
			$popup_sizew	= floor($_mainBannerImg[0]+($popupConf['outlinePadding']*2));			//���ι�ʰ��� + ��������
			$popup_sizeh	= floor(($_mainBannerImg[1]+($_mouseOutImg[1]*$col))+$popupInvisibleHeight+($popupConf['outlinePadding']*2)+$popupConf['outlinePadding']);	//���ι�ʼ��� + �����̹������� + �����Ϸ纸�ӳ��� + ��������
		}

		// â�������
		$popupConf['popup_sizew']	= $popup_sizew;
		$popupConf['popup_sizeh']	= $popup_sizeh;

		// �̹��� ȣ���� �̹����� �ִ��� Ȯ���Ͽ� �ִٸ� ������ ũ��� âũ�� �缳��
		foreach($popupConf['image_attach_method'] as $image_attach_method){
			if($image_attach_method == 'url'){
				$popupConf['popup_sizew'] = $_POST['popup_sizew'];
				$popupConf['popup_sizeh'] = $_POST['popup_sizeh'];
				break;
			}
		}

		return $popupConf;
	}

	/**
	 * ��Ƽ �˾� ���� ����
	 * @param integer $code ��Ƽ �˾� �ڵ�
	 */
	public function popupRegister($code)
	{
		// ������ ���� ����
		$postData		= $this->postDataProcessing();

		// json ó��
		if (empty($postData) === false) {
			$popupJsonData	= gd_json_encode($postData);
		} else {
			msg("��Ƽ �˾� ������ �̻��� �־ ���忡 ������ �߻��� �Ǿ����ϴ�.");
			return false;
		}

		// ��Ƽ �˾� ���� ����
		$query	= "INSERT INTO ".GD_ENV." SET category = '".$this->env_code."', name = '".$code."' , value = '".addslashes($popupJsonData)."'";
		$res	= $this->db->query($query);
		if($res) {
			msg("[".$postData['text']."] ��Ƽ �˾� â�� ���� �Ǿ����ϴ�.");
			return true;
		} else {
			msg("[".$postData['text']."] ��Ƽ �˾� â ���忡 ������ �߻��� �Ǿ����ϴ�.");
			return false;
		}
	}

	/**
	 * ��Ƽ �˾� ���� ����
	 * @param integer $code ��Ƽ �˾� �ڵ�
	 */
	public function popupModifiy($code)
	{
		// ������ ���� ����
		$postData	= $this->postDataProcessing();

		// json ó��
		if (empty($postData) === false) {
			$popupJsonData	= gd_json_encode($postData);
		} else {
			msg("��Ƽ �˾� ������ �̻��� �־ ������ ������ �߻� �Ǿ����ϴ�.");
			return false;
		}

		// ��Ƽ �˾� ���� ����
		$query	= "UPDATE ".GD_ENV." SET value = '".addslashes($popupJsonData)."' WHERE category = '".$this->env_code."' AND name = '".$code."'";
		$res	= $this->db->query($query);
		if($res) {
			msg("[".$postData['text']."] ��Ƽ �˾� â�� ���� �Ǿ����ϴ�.");
		} else {
			msg("[".$postData['text']."] ��Ƽ �˾� â ������ ������ �߻��� �Ǿ����ϴ�.");
		}
	}

	/**
	 * ��Ƽ �˾� ���� ����
	 * @param integer $code ��Ƽ �˾� �ڵ�
	 */
	public function popupDelete($code)
	{
		// �̹��� ���� ������ ���� ���� ȣ��
		$data		= $this->getPopupData($code);
		$popData	= gd_json_decode(stripslashes($data['value']));

		// �˾� �̹��� ����
		$tmp		= explode('_', $popData['displaySet']);
		$imgCount	= $tmp[0] * $tmp[1];

		// �̹��� ȭ���� ����
		foreach($this->imgField as $key => $val)
		{
			for($k = 1; $k <= $imgCount; $k++)
			{
				if(is_file($this->data_path.$popData[$key][$k])){
					@unlink( $this->data_path.$popData[$key][$k] );
				}
			}
		}

		// ��� ���� ����
		$query	= "DELETE FROM ".GD_ENV." where category = '".$this->env_code."' and name = '".$code."'";
		$res	= $this->db->query($query);

		if($res) {
			msg("[".$popData['text']."] ��Ƽ �˾� â�� ���� �Ǿ����ϴ�.");
		} else {
			msg("[".$popData['text']."] ��Ƽ �˾� â ������ ������ �߻��� �Ǿ����ϴ�.");
		}
	}

	/**
	 * �̹��� ���� �˻�
	 * @param string $type �̹��� ����
	 */
	private function chkPopupimg($type)
	{
		require_once(dirname(__FILE__)."/upload.lib.php");
		$upload = new upload_file;
		$upload->upload_file($_FILES[$type],'','image');
		if(!$upload->file_extension_check())return false;
		if(!$upload->file_type_check())return false;
		return true;
	}

	/**
	 * �̹������ε�
	 * @return text ��� ���� �� json_encode ó���� �̹�����
	 */
	public function imgUploadTemp()
	{
		foreach($this->imgField as $key => $val)
		{
			if(!$this->chkPopupimg($key))
			{
				echo 'fileError';
				exit;
			}
		}

		// ���ε�� �̹��� �迭
		$uploadImgArray = array();

		foreach($this->imgField as $key => $val)
		{
			// �̹����� ���ε� �Ȱ����
			if (is_uploaded_file($_FILES[$key]['tmp_name']))
			{
				// �ӽ� ������ ���ε� ���� �� ���� üũ
				if(!is_dir($this->tmp_data_path))
				{
					mkdir($this->tmp_data_path);
					chmod($this->tmp_data_path,0707);
				}

				$_ext	= array_pop(explode(".",$_FILES[$key]['name']));
				$_key	= $val;
				$_rnd	= mt_rand(0,999);

				while (is_file($this->tmp_data_path.$this->now.$_rnd.$_key.".".$_ext))
				{
					$this->now++;
					$_rnd	= mt_rand(0,999);
				}

				// ���ε� �̹��� ����
				$file[$key]['name']	= "tmp_".$this->now.$_rnd.$_key.".".$_ext;

				move_uploaded_file($_FILES[$key]['tmp_name'],$this->tmp_data_path.$file[$key][name]);
				chmod($this->tmp_data_path.$file[$key][name],0707); // ���ε�� ���� ���� ����
			}
			// �̹��� ���ε� ���� ������ ��� ������ �� ����
			else if($_POST[$key]){
				$file[$key]['name']	= $_POST[$key];

			}

			// ���ε�� �̹��� ȭ�ϸ�
			$uploadImgArray[$key]	= $file[$key]['name'];
		}

		// �̹��� ���� ���
		if (empty($uploadImgArray) === false && is_array($uploadImgArray)) {
			echo gd_json_encode($uploadImgArray);	// json_encode ó��
		} else {
			echo 'fileError';
		}

		return true;
	}

	/**
	 * ��Ƽ �˾� ���� ����
	 * @param integer $code ��Ƽ �˾� �ڵ�
	 */
	public function popupCopy($code)
	{
		// ��Ƽ �˾� ����
		$data		= $this->getPopupData($code);
		$popData	= gd_json_decode(stripslashes($data['value']));

		// ����� ��Ƽ �˾��� �ڵ�
		$newcode	= $this->getNewCode();

		// ����� ����
		$copyPopdata	= array();

		foreach ( $popData as $key => $value)
		{
			if(array_key_exists($key, $this->imgField)){

				// �˾� �̹��� ����
				$tmp		= explode('_', $popData['displaySet']);
				$imgCount	= $tmp[0] * $tmp[1];

				if ($now === null) $now = time();

				for($k=1;$k<=$imgCount;$k++){

					$image_name = $popData[$key][$k];

					if (! preg_match('/^http(s)?:\/\/.+$/', $image_name)) {

						if(is_file($this->data_path.$image_name)){
							$_ext = array_pop(explode(".",$image_name));
							$_key = $this->imgField[$key];
							$_rnd = mt_rand(0,999);

							while (is_file($this->data_path.$now.$_rnd.$_key.".".$_ext)) {
								$now++;
								$_rnd = mt_rand(0,999);
							}

							$_image_name = "ori_".$now.$_rnd.$_key.".".$_ext;

							@copy($this->data_path.$image_name,$this->data_path.$_image_name);

							$image_name = $_image_name;
						} else {
							$image_name = '';
						}
					}

					$copyPopdata[$key][$k] = $image_name;
				}

				continue;
			}

			if($key == "code"){
				$copyPopdata[$key] = $newcode;
			}else if($key == "popup_use"){
				$copyPopdata[$key] = 'N';
			}else{
				$copyPopdata[$key] = $value; //����Ű�� ����
			}
		}

		$copyJsonPopdata = $copyPopdata ? gd_json_encode($copyPopdata) : 'false';

		$query	= "INSERT INTO ".GD_ENV." set category = '".$this->env_code."', name ='".$newcode."', value = '".addslashes($copyJsonPopdata)."'";
		$res	= $this->db->query($query);

		if($res) {
			msg("��Ƽ �˾� â�� ���� �Ǿ����ϴ�.");
		} else {
			msg("��Ƽ �˾� â ���翡 ������ �߻��� �Ǿ����ϴ�.");
		}
	}

	/**
	 * �̹��� ���
	 * @param string $imgUrl �̹��� ���
	 * @param string $imgSize �̹��� ����
	 * @param string $parm �Ķ����
	 * @return text img �ױ�
	 */
	public function popupimg($imgUrl, $imgSize = '', $parm = '')
	{
		global $cfg;

		if(!$imgUrl) return;

		if(!preg_match('/http:\/\//',$imgUrl)){
			$imgUrl = $cfg['rootDir'].'/data/multipopup/'.$imgUrl;
		}

		if ($imgSize){
			$size	= explode(',', $imgSize);
			$vsize	= ' width="'.$size[0].'" ';
			if ($size[1]) {
				$vsize .= ' height="'.$size[1].'" ';
			}
		}

		$imgTag		= '<img src="'.$imgUrl.'" '.$vsize.' '.$parm.' />';

		return $imgTag;
	}

	/**
	 * �̹��� ��ũ �ּ�
	 * @param string $linkTarget Ÿ�� ����
	 * @param string $linkUrl ��ũ �ּ�
	 * @return text a �ױ׳� elemnt
	 */
	public function getLink($linkTarget, $linkUrl)
	{
		if ($linkTarget == 'self' && empty($linkUrl) === false) {
			$link	= 'href=javascript:goLink(\''.$linkUrl.'\');';
		}
		else if(!empty($linkUrl)) {
			$link = 'href="'.$linkUrl.'" target="_blank"';
		}
		else {
			$link	= '';
		}

		return $link;

	}

	/**
	 * �̹��� �ּ�
	 * @param string $img �̹�����
	 * @param string $_dir http �ּ�
	 * @return text ���� �̹��� �ּ�
	 */
	public function getImgSrc($img,$_dir)
	{
		if(!preg_match('/http:\/\//',$img)){
			$img	= $_dir.$img;
		}else{
			$img	= $img;
		}

		return $img;

	}

	/**
	 * ��Ƽ �˾� ��뿩�� üũ �� ����Ÿ�� json ó��
	 */
	public function ajaxDataPopup()
	{
		// ��Ƽ �˾� ���� ����Ʈ
		$popupList	= $this->getPopupList();

		// ���� ����� ��Ƽ �˾� ����
		$popupData	= array();

		foreach ( $popupList as $getData )
		{
			// �˾�â �̻���� ���
			if ( $getData['popup_use'] != 'Y' ) continue;

			// Ư���Ⱓ���� �˾�â ������ ���
			if ($getData['popup_dt2tm'] == 'Y')
			{
				// �Ⱓ�� �ȵǾ��ٸ� �н�
				if ( ($getData['popup_sdt'].$getData['popup_stime']) > date('YmdHi') || ($getData['popup_edt'].$getData['popup_etime']) < date('YmdHi') )
				{
					continue;
				}
			}

			// Ư���Ⱓ���� Ư���ð����� �˾�â ����
			if ($getData['popup_dt2tm'] == 'T')
			{
				// ��¥�� �ȵǾ��ٸ� �н�
				if ($getData['popup_sdt'] > date('Ymd') || $getData['popup_edt'] < date('Ymd') )
				{
					continue;
				}
				else {
					// ��¥�� �Ǿ��ٰ� �ص� �ð��� ���� ������ �н�
					if ($getData['popup_stime'] > date('Hi') || $getData['popup_etime'] < date('Hi') ) {
						continue;
					}
				}
			}

			// ����� ��Ƽ �˾�â ����Ÿ ����
			$popupData[]	= $getData;
		}

		return gd_json_encode($popupData);
	}
}
?>