<?

include "../lib/library.php";
require_once("../lib/upload.lib.php");

### �ı� ���ε� �̹��� ���� ����
if($cfg['reviewFileNum']){
	$reviewFileNum = $cfg['reviewFileNum'];
} else {
	$reviewFileNum = 1;
}

function confirm_reload($str,$url='')
{
	if($url){
		echo "
		<script>
		alert('$str');
		if (opener){ opener.location.reload(); window.close(); }
		else location.href='$url';
		</script>
		";
	}else{
		echo "
		<script>
		alert('$str');
		if (opener){ opener.location.reload(); window.close(); }
		else parent.location.reload();
		</script>
		";
	}
	exit;
}

function spamFail() {
	global $_POST, $_SERVER;

	$str = "";
	$str .= "<script language='javascript'>";
	$str .= "alert('�ڵ���Ϲ������ڰ� ��ġ���� �ʽ��ϴ�. �ٽ� �Է��Ͽ� �ֽʽÿ�.');";
	$str .= "window.onload = function() { rtnForm.submit(); }";
	$str .= "</script>";
	$str .= "<div style='display:none;'>";
	$str .= "<form name='rtnForm' method='post' action='".$_SERVER['HTTP_REFERER']."'>";
	$str .= "<input type='text' name='email' value='".$_POST['email']."' />";
	$str .= "<input type='text' name='phone' value='".$_POST['phone']."' />";
	$str .= "<input type='text' name='secret' value='".$_POST['secret']."' />";
	$str .= "<input type='text' name='point' value='".$_POST['point']."' />";
	$str .= "<input type='text' name='subject' value='".$_POST['subject']."' />";
	$str .= "<textarea name='contents'>".$_POST['contents']."</textarea>";
	$str .= "</form>";
	$str .= "</div>";
	exit($str);
}

$mode = ($_POST[mode] ? $_POST[mode] : $_GET[mode] );
if(!$_POST['secret'])$_POST['secret']=0;

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
	    validation::DEFAULT_KEY => 'text',
	    'contents' => 'disable',
	    'password' => 'disable',
	    'mode' => 'disable',
	    'captcha_key' => 'disable',
		'subject'=>'disable',
	));
}

if($_POST['encode'] == 'y') {
	$_POST['subject'] = iconv('utf8','euckr',urldecode($_POST['subject']));
	$_POST['contents'] = iconv('utf8','euckr',urldecode($_POST['contents']));
	$_POST['contents'] = validation::xssClean($_POST['contents'], 'html', 'ent_quotes');
	$_POST['subject'] = validation::xssClean($_POST['subject'], 'html', 'ent_quotes');
} else if($_POST['encode'] == 'htmlencode') { //Ư�� �ѱ� ȣȯ 2013-05-08
	$_POST['subject'] = html_entity_decode($_POST['subject']);
	$_POST['contents'] = html_entity_decode($_POST['contents']);
	$_POST['contents'] = validation::xssClean($_POST['contents'], 'html', 'ent_compat');
	$_POST['subject'] = validation::xssClean($_POST['subject'], 'html', 'ent_compat');
}
else {
	$_POST['contents'] = validation::xssClean($_POST['contents'], 'html', 'ent_quotes');
	$_POST['subject'] = validation::xssClean($_POST['subject'], 'html', 'ent_quotes');
}

switch ($mode){

	case "add_review":
		# Anti-Spam ����
		$switch = ($cfg['reviewSpamBoard']&1 ? '123' : '000') . ($cfg['reviewSpamBoard']&2 ? '4' : '0');
		$rst = antiSpam($switch, "goods/goods_review_register.php", "post");
		if (substr($rst[code],0,1) == '4') spamFail();
		if ($rst[code] <> '0000') msg("���� ��ũ�� �����մϴ�.",-1);

		$query = "
		insert into ".GD_GOODS_REVIEW." set
			goodsno		= '$_POST[goodsno]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			point		= '$_POST[point]',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		$sno=$db->lastID();

		// �̹��� ���ε�
		$attach = 0;
		if ($_FILES['attach']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			if (is_uploaded_file($_FILES['attach'][tmp_name])){
				$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
				$filename = 'RV'.sprintf("%010s", $sno);
				$filename_tmp = $filename.'_tmp';
				$upload = new upload_file($_FILES['attach'],$data_path.$filename_tmp,'image');
				if (!$upload -> upload()){
					msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
					exit;
				} else {
					$img_size = getimagesize( $data_path.$filename_tmp);
					if ($img_size[0] > 700) {
						thumbnail($data_path.$filename_tmp,$data_path.$filename,700);
					} else {
						copy($data_path.$filename_tmp,$data_path.$filename);
					}
					@unlink($data_path.$filename_tmp);
					$attach = 1;
				}
			}
		} else {
			$file_array = reverse_file_array($_FILES[file]);
			$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
			for ($i=0;$i<$reviewFileNum;$i++){
				if ($i == 0){
					$filename = 'RV'.sprintf("%010s", $sno);
				} else {
					$filename = 'RV'.sprintf("%010s", $sno).'_'.$i;
				}
				$filename_tmp = $filename.'_tmp';
				if ($_POST[del_file][$i]=="on"){
					@unlink($data_path.$filename);
				}
				if (is_uploaded_file($file_array[$i][tmp_name])){
					if ($filename){
						@unlink($data_path.$filename);
					}
					$upload = new upload_file($file_array[$i],$data_path.$filename_tmp,'image');
					if (!$upload -> upload()){
						msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
						exit;
					} else {
						if ($cfg['reviewFileSize'] > 0){
							$reviewFileSize_byte = $cfg['reviewFileSize'] * 1024;
							if (filesize($data_path.$filename_tmp) > $reviewFileSize_byte){
								$db->query("delete from ".GD_GOODS_REVIEW." where sno='$sno'");
								for ($rm=0;$rm<=$i;$rm++){
									if ($rm == 0){
										$del_name = 'RV'.sprintf("%010s", $sno);
									} else {
										$del_name = 'RV'.sprintf("%010s", $sno).'_'.$rm;
									}
									@unlink($data_path.$del_name);
								}
								msg("�ִ� ���ε� ������� ".$cfg['reviewFileSize']."kb �Դϴ�",-1);
								exit;
							}
						}
						$img_size = getimagesize($data_path.$filename_tmp);
						if ($img_size[0] > $cfg['reviewLimitPixel']) {
							thumbnail($data_path.$filename_tmp,$data_path.$filename,$cfg['reviewLimitPixel'],1000,1);
						} else {
							copy($data_path.$filename_tmp,$data_path.$filename);
						}
						@unlink($data_path.$filename_tmp);
						$attach = 1;
					}
				}
			}
		}

		$db->query("update ".GD_GOODS_REVIEW." set parent=sno, attach='$attach' where sno='$sno'");

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_review');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_review');

		if($_POST['referer'] == 'orderview'){
			echo "<script>".
			"alert('���������� ��ϵǾ����ϴ�');".
			"opener.location.href='../goods/goods_review.php';".
			"window.close();".
			"</script>";
		}else{
			if ($_POST['goodsno']) confirm_reload("���������� ��ϵǾ����ϴ�","goods_review_list.php?goodsno=".$_POST[goodsno]);
			else confirm_reload("���������� ��ϵǾ����ϴ�");
		}
		break;

	case "mod_review":

		# Anti-Spam ����
		$switch = ($cfg['reviewSpamBoard']&1 ? '123' : '000') . ($cfg['reviewSpamBoard']&2 ? '4' : '0');
		$rst = antiSpam($switch, "goods/goods_review_register.php", "post");
		if (substr($rst[code],0,1) == '4') spamFail();
		if ($rst[code] <> '0000') msg("���� ��ũ�� �����մϴ�.",-1);

		### ����üũ
		list( $m_no, $password ) = $db->fetch("select m_no, password from ".GD_GOODS_REVIEW." where sno = '$_POST[sno]'");
		if ( !isset($sess) && $password != md5($_POST[password]) ) msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����
		if ( isset($sess) && $sess['level'] < 80 && $sess['m_no'] != $m_no ) msg('������ �ۼ��� ��ǰ�ı⸸ �����Ͻ� �� �ֽ��ϴ�.',$code=-1); // @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ��

		// �̹��� ���ε�
		if ($_FILES['attach']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			if (is_uploaded_file($_FILES['attach'][tmp_name])){
				$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
				$filename = 'RV'.sprintf("%010s", $_POST['sno']);
				$filename_tmp = $filename.'_tmp';
				$upload = new upload_file($_FILES['attach'],$data_path.$filename_tmp,'image');
				if (!$upload -> upload()){
					msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
					exit;
				} else {
					$img_size = getimagesize( $data_path.$filename_tmp);
					if ($img_size[0] > 700) {
						thumbnail($data_path.$filename_tmp,$data_path.$filename,700);
					} else {
						copy($data_path.$filename_tmp,$data_path.$filename);
					}
					@unlink($data_path.$filename_tmp);
					$attach_query = ", attach = '1'";
				}
			}
		} else {
			$file_array = reverse_file_array($_FILES[file]);
			$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
			for ($i=0;$i<10;$i++){
				if ($i == 0){
					$filename = 'RV'.sprintf("%010s", $_POST['sno']);
				} else {
					$filename = 'RV'.sprintf("%010s", $_POST['sno']).'_'.$i;
				}
				$filename_tmp = $filename.'_tmp';
				if ($_POST[del_file][$i]=="on"){
					@unlink($data_path.$filename);
				}
				if (is_uploaded_file($file_array[$i][tmp_name])){ // �̹��� �ű� ���
					if ($filename){
						@unlink($data_path.$filename);
					}
					$upload = new upload_file($file_array[$i],$data_path.$filename_tmp,'image');
					if (!$upload -> upload()){
						msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
						exit;
					} else {
						if ($cfg['reviewFileSize'] > 0){
							$reviewFileSize_byte = $cfg['reviewFileSize'] * 1024;
							if (filesize($data_path.$filename_tmp) > $reviewFileSize_byte){
								@unlink($data_path.$filename_tmp);
								msg("�ִ� ���ε� ������� ".$cfg['reviewFileSize']."kb �Դϴ�",-1);
								exit;
							}
						}
						$img_size = getimagesize($data_path.$filename_tmp);
						if ($img_size[0] > $cfg['reviewLimitPixel']) {
							thumbnail($data_path.$filename_tmp,$data_path.$filename,$cfg['reviewLimitPixel'],1000,1);
						} else {
							copy($data_path.$filename_tmp,$data_path.$filename);
						}
						@unlink($data_path.$filename_tmp);
					}
				} else if ($_POST[file_ori][$i]){ // �̹��� ��ġ ����
					if ($i != $_POST[file_ori][$i]){
						$new_name = 'RV'.sprintf("%010s", $_POST[sno]).'_'.$i;
						@unlink($data_path.$new_name);
						$re_name = 'RV'.sprintf("%010s", $_POST[sno]).'_'.$_POST[file_ori][$i];
						@rename($data_path.$re_name,$data_path.$new_name);
					}
				}
			}
			// ��ϵ� �̹��� Ȯ��
			$file_chk = 0;
			for ($i=0;$i<10;$i++){
				if ($i == 0){
					$upload_file = 'RV'.sprintf("%010s", $_POST[sno]);
				} else {
					$upload_file = 'RV'.sprintf("%010s", $_POST[sno]).'_'.$i;
				}
				if (file_exists($data_path.$upload_file)){
					$file_chk++;
				}
			}
			if ($file_chk > 0){
				$attach_query = ", attach = '1'";
			} else {
				$attach_query = ", attach = '0'";
			}
		}

		$query = "
		update ".GD_GOODS_REVIEW." set
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			point		= '$_POST[point]',
			name		= '$_POST[name]'
			$attach_query
		where sno = '$_POST[sno]'
		";
		$db->query($query);

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_review');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_review');

		confirm_reload("���������� �����Ǿ����ϴ�");
		break;

	case "reply_review":

		$query = "
		insert into ".GD_GOODS_REVIEW." set
			goodsno		= '$_POST[goodsno]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			parent		= '$_POST[sno]',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		$sno=$db->lastID();

		// �̹��� ���ε�
		$attach = 0;
		if ($_FILES['attach']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			if (is_uploaded_file($_FILES['attach'][tmp_name])){
				$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
				$filename = 'RV'.sprintf("%010s", $sno);
				$filename_tmp = $filename.'_tmp';
				$upload = new upload_file($_FILES['attach'],$data_path.$filename_tmp,'image');
				if (!$upload -> upload()){
					msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
					exit;
				} else {
					$img_size = getimagesize( $data_path.$filename_tmp);
					if ($img_size[0] > 700) {
						thumbnail($data_path.$filename_tmp,$data_path.$filename,700);
					} else {
						copy($data_path.$filename_tmp,$data_path.$filename);
					}
					@unlink($data_path.$filename_tmp);
					$attach = 1;
				}
			}
		} else {
			$file_array = reverse_file_array($_FILES[file]);
			$data_path = $_SERVER['DOCUMENT_ROOT'].'/shop/data/review/';
			for ($i=0;$i<$reviewFileNum;$i++){
				if ($i == 0){
					$filename = 'RV'.sprintf("%010s", $sno);
				} else {
					$filename = 'RV'.sprintf("%010s", $sno).'_'.$i;
				}
				$filename_tmp = $filename.'_tmp';
				if ($_POST[del_file][$i]=="on"){
					@unlink($data_path.$filename);
				}
				if (is_uploaded_file($file_array[$i][tmp_name])){
					if ($filename){
						@unlink($data_path.$filename);
					}
					$upload = new upload_file($file_array[$i],$data_path.$filename_tmp,'image');
					if (!$upload -> upload()){
						msg("�̹��� ���ϸ� ���ε尡 �����մϴ�",-1);
						exit;
					} else {
						if ($cfg['reviewFileSize'] > 0){
							$reviewFileSize_byte = $cfg['reviewFileSize'] * 1024;
							if (filesize($data_path.$filename_tmp) > $reviewFileSize_byte){
								$db->query("delete from ".GD_GOODS_REVIEW." where sno='$sno'");
								for ($rm=0;$rm<=$i;$rm++){
									if ($rm == 0){
										$del_name = 'RV'.sprintf("%010s", $sno);
									} else {
										$del_name = 'RV'.sprintf("%010s", $sno).'_'.$rm;
									}
									@unlink($data_path.$del_name);
								}
								msg("�ִ� ���ε� ������� ".$cfg['reviewFileSize']."kb �Դϴ�",-1);
								exit;
							}
						}
						$img_size = getimagesize($data_path.$filename_tmp);
						if ($img_size[0] > $cfg['reviewLimitPixel']) {
							thumbnail($data_path.$filename_tmp,$data_path.$filename,$cfg['reviewLimitPixel'],1000,1);
						} else {
							copy($data_path.$filename_tmp,$data_path.$filename);
						}
						@unlink($data_path.$filename_tmp);
						$attach = 1;
					}
				}
			}
		}

		$db->query("update ".GD_GOODS_REVIEW." set attach='$attach' where sno='$sno'");

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_review');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_review');

		confirm_reload("���������� ��ϵǾ����ϴ�");
		break;

	case "del_review":

		### ����üũ
		list( $m_no, $password ) = $db->fetch("select m_no, password from ".GD_GOODS_REVIEW." where sno = '$_POST[sno]'");
		if ( !isset($sess) && $password != md5($_POST[password]) ) msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����
		if ( isset($sess) && $sess['level'] < 80 && $sess['m_no'] != $m_no ) msg('������ �ۼ��� ��ǰ�ı⸸ �����Ͻ� �� �ֽ��ϴ�.','close'); // @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ��
		daum_goods_review($_POST['sno']);	// ���� ��ǰ�� DB ����
		$query = "delete from ".GD_GOODS_REVIEW." where sno = '$_POST[sno]'";
		$db->query($query);

		// �̹��� ���ε�
		$data_path = "../data/review";
		for($i=0;$i<10;$i++){
			if($i == 0){
				$name = 'RV'.sprintf("%010s", $_POST[sno]);
			} else {
				$name = 'RV'.sprintf("%010s", $_POST[sno]).'_'.$i;
			}
			if (file_exists($data_path."/".$name)){
				@unlink($data_path."/".$name);
			}
		}

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_review');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_review');

		confirm_reload("���������� �����Ǿ����ϴ�");
		break;

	case "add_qna":
		include "../conf/config.php";
		# Anti-Spam ����
		$switch = ($cfg['qnaSpamBoard']&1 ? '123' : '000') . ($cfg['qnaSpamBoard']&2 ? '4' : '0');
		$rst = antiSpam($switch, "goods/goods_qna_register.php", "post");
		if (substr($rst[code],0,1) == '4') spamFail();
		if ($rst[code] <> '0000') msg("���� ��ũ�� �����մϴ�.",-1);

		$query = "
		insert into ".GD_GOODS_QNA." set
			goodsno		= '$_POST[goodsno]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			secret		= '".$_POST['secret']."',
			ip			= '$_SERVER[REMOTE_ADDR]',
			email		= '$_POST[email]',
			phone		= '$_POST[phone]',
			rcv_sms		= '$_POST[rcv_sms]',
			rcv_email	= '$_POST[rcv_email]'
		";
		$db->query($query);

		$db->query("update ".GD_GOODS_QNA." set parent=sno where sno='" . $db->lastID() . "'");

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_qna');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_qna');

		//���Ẹ�ȼ���
		$write_end_url = $sitelink->link("goods/indb.php?mode=add_qna_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
		break;

	case "add_qna_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���������� ��ϵǾ����ϴ�');opener.location.reload();opener.focus();window.close();</script>";
		exit;
		break;

	case "mod_qna":
		include "../conf/config.php";
		# Anti-Spam ����
		$switch = ($cfg['qnaSpamBoard']&1 ? '123' : '000') . ($cfg['qnaSpamBoard']&2 ? '4' : '0');
		$rst = antiSpam($switch, "goods/goods_qna_register.php", "post");
		if (substr($rst[code],0,1) == '4') spamFail();
		if ($rst[code] <> '0000') msg("���� ��ũ�� �����մϴ�.",-1);

		### ����üũ
		list( $parent, $m_no, $password ) = $db->fetch("select parent, m_no, password from ".GD_GOODS_QNA." where sno = '$_POST[sno]' limit 1");
		if ( !isset($sess) && $password != md5($_POST[password]) ) msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����
		if ( isset($sess) && $sess[level] < 80 && $sess['m_no'] != $m_no ) msg('������ �ۼ��� ��ǰ���Ǹ� �����Ͻ� �� �ֽ��ϴ�.',$code=-1); // @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ��

		$query = "
			update ".GD_GOODS_QNA." set
				subject		= '$_POST[subject]',
				contents	= '$_POST[contents]',
				name		= '$_POST[name]',
				secret		= '".$_POST['secret']."',
				email		= '".$_POST['email']."',
				phone		= '".$_POST['phone']."',
				rcv_sms		= '".$_POST['rcv_sms']."',
				rcv_email	= '".$_POST['rcv_email']."'
			where sno = '$_POST[sno]'
			";
		$db->query($query);

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_qna');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_qna');

		//���Ẹ�ȼ���
		$write_end_url = $sitelink->link("goods/indb.php?mode=mod_qna_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
		break;

	case "mod_qna_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���������� �����Ǿ����ϴ�');opener.location.reload();opener.focus();window.close();</script>";
		exit;
		break;

	case "del_qna":

		### ����üũ
		list( $password,$m_no,$parent ) = $db->fetch("select password,m_no,parent from ".GD_GOODS_QNA." where sno = '$_POST[sno]'");
		if ( !isset($sess) && $password != md5($_POST[password]) ) msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����
		if ( isset($sess) && $sess[level] < 80 && $sess['m_no'] != $m_no ) msg('������ �ۼ��� ��ǰ���Ǹ� �����Ͻ� �� �ֽ��ϴ�.','close'); // @qnibus 2015-06 ȸ�����̵�� �Խñ� �ۼ��� ��ġ���� Ȯ��

		if(isset($sess)){
			if($parent!=$_POST[sno]){
				list($om_no) = $db->fetch("select m_no from ".GD_GOODS_QNA." where sno = '$parent'");
				if($sess[m_no]!=$om_no && $sess[m_no]!=$m_no){
					confirm_reload("������ �����ϴ�.");
					exit;
				}
			}
		}

		$query = "delete from ".GD_GOODS_QNA." where sno = '$_POST[sno]'";
		if($parent == $_POST[sno]){
			list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_QNA." where parent='$parent'");
			if($cnt > 1) $query = "update ".GD_GOODS_QNA." set subject='������ �Խù� �Դϴ�.',contents='������ �Խù� �Դϴ�.' where sno='".$_POST['sno']."'";
		}
		$db->query($query);

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_qna');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_qna');

		confirm_reload("���������� �����Ǿ����ϴ�");
		break;

	case "reply_qna":

		list($parent) = $db->fetch("select parent from ".GD_GOODS_QNA." where sno='$_POST[sno]'");
		$query = "
		insert into ".GD_GOODS_QNA." set
			goodsno		= '$_POST[goodsno]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			parent		= '$parent',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			secret		= '".$_POST['secret']."',
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);

		//DB Cache �ʱ�ȭ 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('goodsview_qna');

		// ������ĳ�� �ʱ�ȭ
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods_qna');

		//���Ẹ�ȼ���
		$write_end_url = $sitelink->link("goods/indb.php?mode=reply_qna_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
		break;

	case "reply_qna_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���������� ��ϵǾ����ϴ�');opener.location.reload();opener.focus();window.close();</script>";
		exit;
		break;

	case "auth_qna":
		include "../conf/config.php";
		### ����üũ
		list( $password ) = $db->fetch("select password,contents from ".GD_GOODS_QNA." where sno = '$_POST[sno]'");
		if ( $password != md5($_POST[password]) ) msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����
		if($_SESSION['qna_auth']) $r_qna_auth = unserialize($_SESSION['qna_auth']);
		$r_qna_auth[] = $_POST[sno];
		$_SESSION['qna_auth'] = serialize(array_unique($r_qna_auth));
		?>
		<script type="text/javascript">
		opener.view_content('<?php echo $_POST['sno'];?>');
		self.close();
		</script>
		<?php
		break;

	case "request_stocked_noti" :
		$goodsno = isset($_POST['goodsno']) ? trim($_POST['goodsno']) : '';
		$m_id = isset($_SESSION['sess']['m_id']) ? trim($_SESSION['sess']['m_id']) : '';
		$ip = isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '';

		$opt = isset($_POST['opt']) ? $_POST['opt'] : array();
		$name = isset($_POST['name']) ? trim($_POST['name']) : '';
		$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
		$phone = str_replace("-", "", $phone);

		if (empty($goodsno) || empty($m_id)){
			msg("�߸��� �����Դϴ�.", "popup_request_stocked_noti.php?goodsno=".$goodsno);
			exit;
		}

		if (empty($name)){
			msg("�̸��� �Է����ּ���", "popup_request_stocked_noti.php?goodsno=".$goodsno);
			exit;
		}

		if (!is_numeric($phone) || strlen($phone) < 10 || strlen($phone) > 11){
			msg("�ùٸ� ��ȭ��ȣ�� �Է����ּ���", "popup_request_stocked_noti.php?goodsno=".$goodsno);
			exit;
		}

		list($cnt, $optno) = $db->fetch("SELECT count(*) FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and go_is_deleted <> '1' and go_is_display = '1'");

		if (count($opt)<=0 && $cnt > 1) {
			msg("���԰� ��û�� �ɼ��� �����ϼ���.", "popup_request_stocked_noti.php?goodsno=".$goodsno);
			exit;
		}else if(count($opt)<=0 && $cnt == 1){
			list($opt[]) = $db->fetch("SELECT optno FROM ".GD_GOODS_OPTION." WHERE goodsno='".$goodsno."' and go_is_deleted <> '1' and go_is_display = '1'");
		}

		$res = $db->query("
		SELECT
			*
		FROM
			".GD_GOODS_STOCKED_NOTI." gsn,
			".GD_GOODS." g,
			".GD_GOODS_OPTION." go
		WHERE
				gsn.goodsno = '".$goodsno."'
			AND gsn.m_id = '".$m_id."'
			AND gsn.goodsno = g.goodsno
			AND go.goodsno = g.goodsno
			AND go.optno = gsn.optno and go_is_deleted <> '1' and go_is_display = '1'
			AND gsn.optno in ('".implode("','", $opt)."')");

		$exists = false;
		while ($data = $db->fetch($res)) {
			$exists = true;
			$optExists[] = $data['optno'];
		}

		foreach($opt as $v){
			if(in_array($v, $optExists)){
				continue;
			}
			$res = "SELECT opt1, opt2 FROM ".GD_GOODS_OPTION." WHERE optno='".$v."' AND goodsno='".$goodsno."' and go_is_deleted <> '1' and go_is_display = '1' LIMIT 1";
			$data = $db->fetch($res);

			$query = "
			INSERT INTO ".GD_GOODS_STOCKED_NOTI." SET
				m_id = '".$m_id."' ,
				goodsno = '".$goodsno."' ,
				optno = '".$v."',
				opt1 = '".$data['opt1']."',
				opt2 = '".$data['opt2']."',
				name = '".$name."' ,
				phone = '".$phone."' ,
				regdt = NOW(),
				ip = '".$ip."',
				sended = '0'
			";
			$db->query($query);
		}
		?>
		<script type="text/javascript">
			alert('SMS �˸� ��û�� �Ϸ�Ǿ����ϴ�.');
			self.close();
		</script>
		<?
		exit;
		break;

		// 2013-01-16 dn ��ǰ QA �Խ��� ��ȸ�� �� ��й�ȣ �Է��� ���� �� �������� ���� ���� ��ȸ�� ��й�ȣ ���� ���� �ҽ� �߰�
		case "auth_nomember":
			include "../conf/config.php";
			### ����üũ
			list( $password, $contents, $goodsno ) = $db->fetch("select password,contents,goodsno from ".GD_GOODS_QNA." where sno = '$_POST[sno]'");
			if ( $password != md5($_POST[password]) ) {
				msg($msg='��й�ȣ�� �߸� �Է� �ϼ̽��ϴ�.',$code=-1); // ȸ������ & �α�����
			}
			else {
				if($_SESSION['qna_auth']) $r_qna_auth = unserialize($_SESSION['qna_auth']);
				$r_qna_auth[] = $_POST[sno];
				$_SESSION['qna_auth'] = serialize(array_unique($r_qna_auth));
				go('goods_qna_register.php?mode=mod_qna&goodsno='.$goodsno.'&sno='.$_POST[sno]);
			}

		break;
}

?>
