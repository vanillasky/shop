<?

include "../library.php";
require_once("../upload.lib.php");

switch ($_POST[mode]){

	case "InsertImage":

		$_POST[idx] += 0;
		$_POST['mini_url'] = trim($_POST['mini_url']);

		if ($_POST['mini_url'] != '' && $_POST['mini_url'] != 'http://'){
			$src = $_POST['mini_url'];
		}
		else {
			//$dir = "data/";
			$dir = "../../data/editor/";
			if ($_FILES[mini_file][error] === UPLOAD_ERR_INI_SIZE) {
				echo '<script>alert("�̹��� ���� ����� �ʹ� Ů�ϴ�. (�ִ� '.byte2str(str_to_byte(ini_get('upload_max_filesize'))).')");</script>';
				exit;
			}
			if (!preg_match("/^image/",$_FILES[mini_file][type])){
				echo "<script>alert('�̹��� ���ϸ� ���ε尡 �����մϴ�');</script>";
				exit;
			}

			if (is_uploaded_file($_FILES[mini_file][tmp_name])){
				$div = explode(".",$_FILES[mini_file][name]);
				$filename = substr(md5(microtime()),0,16).".".$div[count($div)-1];
				$upload = new upload_file($_FILES['mini_file'],$dir.$filename,'image');
				if(!$upload -> upload()){
					echo "<script>alert('�̹��� ���ϸ� ���ε尡 �����մϴ�');</script>";
					exit;
				}
				setDu('editor'); # �����뷮 ���
			}
			$src = dirname($_SERVER[PHP_SELF])."/".$dir.$filename;
		}

		if ($_POST[imgWidth] && $_POST[imgHeight]) $size = " width='$_POST[imgWidth]' height='$_POST[imgHeight]'";

		if ($src) echo "<script>parent.opener.mini_set_html($_POST[idx],\"<img src='$src' $size>\");</script>";
		echo "<script>parent.window.close();</script>";
		break;

}

?>