<?
include "../lib/library.php";

function chkBoardBot($id,$idx,$main,$sub,$no){
	global $db;
	$db_table = "`".GD_BD_.$id."`";
	list($chkErr) = $db->fetch("select count(*) from $db_table where idx='$idx' and main=$main and sub = '$sub'");

	if( $chkErr > 1 ){
		$resErr = $db->query("select * from $db_table where idx='$idx' and main='$main' and sub = '$sub' and no != '$no'");
		while($dataErr = $db->fetch($resErr)){
			$db->query("delete from $db_table where no=$dataErr[no]");
			$db->query("update ".GD_BOARD_INF." set num=num-1 where id='$id' and idx='$dataErr[idx]'");

			// ���ε� ���� ����
			$div = explode("|",$dataErr[new_file]);
			for ($j=0;$j<count($div);$j++){
				@unlink("../data/board/$id/".$div[$j]);
				@unlink("../data/board/$id/thumbnail/".$div[$j]);
			}

			// ������ �̹��� ����
			delEditorImg($dataErr[contents]);
		}
	}
}

extract($_GET); extract($_POST);

$db_table = ($mode!="comment") ? "`".GD_BD_.$id."`" : GD_BOARD_MEMO;
$f_no = ($mode!="comment") ? "no" : "sno";
rsort($sel);

$md5_password = md5($_POST[password]);
if ($mode=="comment") $md5_password = substr($md5_password,0,16);

for ($i=0;$i<count($sel);$i++){

	$data	= $db->fetch("select * from $db_table where $f_no='".$sel[$i]."'");
	list($chk) = $db->fetch("select * from $db_table where $f_no='".$sel[$i]."' and password='$md5_password'");

	if (($chk && $password) || $ici_admin || ($sess[m_no] && $sess[m_no]==$data[m_no])){

		// �亯�� ���� ���� �ľ�

		if ($mode!="comment"){
			if($ici_admin) chkBoardBot($id,$data[idx],$data[main],$data[sub],$sel[$i]);
			list($chk) = $db->fetch("select no from $db_table where idx='$data[idx]' and main=$data[main] and sub like '$data[sub]%' limit 1,1");
			if ($chk) msg("�亯���� �����մϴ�",-1);
		}

		// �Խù��� �ڸ�Ʈ�� ��� ó��
		$db->query("delete from $db_table where $f_no='".$sel[$i]."'");
		if ($mode!="comment"){
			$db->query("update ".GD_BOARD_INF." set num=num-1 where id='$id' and idx='$data[idx]'");

			// ���ε� ���� ����
			$div = explode("|",$data[new_file]);
			for ($j=0;$j<count($div);$j++){
				@unlink("../data/board/$id/".$div[$j]);
				@unlink("../data/board/$id/thumbnail/".$div[$j]);
			}

			// ������ �̹��� ����
			delEditorImg($data[contents]);
		} else {
			$db->query("update `".GD_BD_.$id."` set comment=comment-1 where no='$data[no]'");

			// ������ ��бۿ���
			$query = "select * from `".GD_BD_.$id."` where no='$data[no]'";
			$data = $db->fetch($query);
			if ($data[secret] == 'o' && $secret != 'o') $secret = 'o';
		}

	} else msg("��й�ȣ�� ��ġ���� �ʽ��ϴ�",-1);

}

// ������ĳ�� �ʱ�ȭ
$templateCache = Core::loader('TemplateCache');
$templateCache->clearCacheByClass('board');

### �����뷮 ���
setDu('board');
setDu('editor');

if (!$returnUrl) $returnUrl = $_SERVER[HTTP_REFERER];

if($secret != 'o'){
	go($returnUrl);
}else{
	go("list.php?id=$_POST[id]&".getReUrlQuery('no,id,mode', $returnUrl));
}

?>