<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_POST['id'])) exit;
include "../conf/bd_$_POST[id].php";
include "../lib/library.php";

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY => 'text',
		'memo' => 'disable',
		'password' =>'disable',
		'captcha_key'=>'disable',
		'id'=>'disable',
		'm_no'=>'disable',
		'mode'=>'disable',
	));
 }

if ($bdLvlC && $bdLvlC>$sess[level]) msg("�α����ϼž� �� ���񽺸� �̿��Ͻ� �� �ֽ��ϴ�","../member/login.php");

# Anti-Spam ����
$switch = ($bdSpamComment&1 ? '123' : '000') . ($bdSpamComment&2 ? '4' : '0');
$rst = antiSpam($switch, "board/(view|list).php", "post");
if (substr($rst[code],0,1) == '4') msg("�ڵ���Ϲ������ڰ� ��ġ���� �ʽ��ϴ�. �ٽ� �Է��Ͽ� �ֽʽÿ�.",-1);
if ($rst[code] <> '0000') msg("���� ��ũ�� �����մϴ�.",-1);

switch ($_POST[mode]){

	case "write":

		$query = "insert into ".GD_BOARD_MEMO." set
				id			= '$_POST[id]',
				no			= '$_POST[no]',
				name		= '$_POST[name]',
				memo		= '$_POST[memo]',
				password	= '".md5($_POST[password])."',
				m_no		= '$sess[m_no]',
				regdt		= now()
				";

		$db->query($query);
		$db->query("update `".GD_BD_.$_POST['id']."` set comment=comment+1 where no='".$_POST[no]."'");
		break;

	case "modify":

		$data	= $db->fetch("select * from ".GD_BOARD_MEMO." where id='$_POST[id]' and no='".$_POST[no]."'");
		list ($chk)	= $db->fetch("select no from ".GD_BOARD_MEMO." where id='$_POST[id]' and sno='".$_POST[no]."' and password=password('$_POST[password]')");

		if (!(($chk && $_POST[password]) || $ici_admin || $sess[m_no]==$data[m_no])) msg("��й�ȣ�� ��ġ���� �ʽ��ϴ�",-1);

		$query = "update ".GD_BOARD_MEMO." set
				name		= '$_POST[name]',
				memo		= '$_POST[memo]',
				etc			= '$_POST[etc]'
				where id='$_POST[id]' and sno='".$_POST[no]."'
				";

		$db->query($query);
		break;

}

$query = "select * from ".GD_BD_.$_POST['id']." where no='$_POST[no]'";
$data = $db->fetch($query);
if($data[secret] != 'o'){
	go($_POST[returnUrl]);
}else{
	go("list.php?id=$_POST[id]&".getReUrlQuery('no,id,mode', $_POST[returnUrl]));
}

?>