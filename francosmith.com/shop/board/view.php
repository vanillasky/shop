<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;

## �����Ҵ�
$id = $_GET['id'];
$no = $_GET['no'];

$pageView = true;
@include_once "../conf/bd_".$_GET['id'].".php";

if ($bdTypeView==2) include "list.php";
else {

	include "../_header.php";
	include "../lib/page.class.php";
	include "../lib/board.class.php";

	if(class_exists('validation') && method_exists('validation','xssCleanArray')){
		$_GET = validation::xssCleanArray($_GET, array(
			validation::DEFAULT_KEY => 'text',
			'search'=>'disable',
		));

		$_GET['search'] = validation::xssCleanArray($_GET['search'], array(
			validation::DEFAULT_KEY => 'text',
		));
	}

	### bd class

	$bd = new Board();

	$bd->db  = &$db;
	$bd->tpl = &$tpl;
	$bd->cfg = &$cfg;
	if ( file_exists( dirname(__FILE__) . '/../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $bd->adminicon = 'admin.gif';

	$bd->id			= $id;
	$bd->sess		= $sess;
	$bd->ici_admin	= $ici_admin;
	$bd->subSpeech	= $_GET['subSpeech'];
	$bd->search		= $_GET['search'];

	$bd->assign(array(
		bdLvlW			=> $bdLvlW,
		));
}

$bd->bdSkin	= $bdSkin;

//if ($bdLvlR && $bdLvlR>$sess[level]) msg("�� ���� ������ �����ϴ�",-1);
if ($bdLvlR && $bdLvlR>$sess[level]) msg("�� ���� ������ �����ϴ�","list.php?id=".$id);
if ($bdLvlC && $bdLvlC>$sess[level]) $bdDenyComment = true;

if ($no) $_GET[sel][] = $no;

### ���ñ�
if ($bdTypeView==1 && count($_GET[sel])==1) $bd->relation = true;

for ($i=0;$i<count($_GET[sel]);$i++){
	$bd->no = $_GET[sel][$i];
	$bd->_view();
	$loop[] = $bd->data;
	$checked[chk][$_GET[sel][$i]] = "checked";
}

if ($bd->mini_idno) setCookie("mini_idno","{$bd->mini_idno}$_COOKIE[mini_idno]",0);
if ($sess){
	$tpl->assign(readonly,array(name => "readonly style='border:0;font-weight:bold'"));
	$tpl->assign(name_comment,$sess[nick]);
}

$bdHeader = stripslashes($bdHeader);
$bdFooter = stripslashes($bdFooter);

### tpl class
$tpl->assign(array(
			'id'	=> $id,
			'loop'	=> $loop,
			));
$tpl->define('view',"board/$bdSkin/view.htm");
$tpl->print_('view');

if ($bdTypeView==2) echo "<script src='../lib/js/board.js'></script>";
?>
<script>
<? for ($i=0;$i<count($_GET[sel]);$i++){ ?>
if (document.getElementById('examC_<?=$_GET[sel][$i]?>')) document.getElementById('contents_<?=$_GET[sel][$i]?>').innerHTML = document.getElementById('examC_<?=$_GET[sel][$i]?>').value;
<? } ?>
</script>