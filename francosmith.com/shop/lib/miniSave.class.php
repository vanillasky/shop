<?
class miniSave
{
	private $data_path = '';

	public function __construct() {
		$this->data_path = dirname(__FILE__).'/../data/board';
	}

	### MySQL v5.0 �̻��϶� binary ���Ĺ����� ���� ��ġ..
	function binary_patch()
	{
		$this->orderby = "order by idx,main,hex(sub)";
	}

	function chkNotice()
	{
		if($this->mode=="modify"){
			if(($_POST[notice] && !$this->data[notice]) || (!$_POST[notice] && $this->data[notice])){
				if(!$_POST[notice]) $qr_notice = "and notice!='o'";
				$query	= "select * from `".GD_BD_.$this->id."` where idx like 'a%' $qr_notice $this->orderby limit 1";
				$data = $this->db->fetch($query);
				$this->idx  = ($data[idx]) ? substr($data[idx],1) : 999;
				$this->main = $data['main'] ? $data['main'] - 1 : 5000;
			}
		}
		if($_POST[notice]){
			if($this->mode=="reply") msg("������ �亯���� ���·� ����� �ȵ˴ϴ�",-1);
			list($chk) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."` where notice='o'");
			if(!$chk) $this->main = -10000;
			### �������� ù���϶� inf ���̺� ����Ÿ ����
			list($cnt) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."`");
			if($cnt==1){
				$this->idx--;
				### idx�� ����
				if($this -> idx < 100) msg("�ùٸ��� ���� �Խ��� �׷��ȣ�Դϴ�.",-1);
				list($chk) = $this->db->fetch("select id from ".GD_BOARD_INF." where id='{$this->id}' and idx='a{$this->idx}'");
				if(!$chk) $this->db->insert(GD_BOARD_INF)->set(array('id'=>$this->id, 'idx'=>'a'.$this->idx))->query();
			}
		}
	}

	function getIndex()
	{
		if($this->mode=="write"){
			if(!$_POST[notice]) $qr_notice = "and notice!='o'";
			$query	= "select * from `".GD_BD_.$this->id."` where idx like 'a%' $qr_notice $this->orderby limit 1";
			$gap	= -1;
		} else $query = "select * from `".GD_BD_.$this->id."` where no='".$this->no."'";

		$this->data = $this->db->fetch($query,1);

		$this->idx  = ($this->data[idx]) ? substr($this->data[idx],1) : 1000;
		$this->main = $this->data['main'] + $gap;
		$this->sub	= $this->data[sub];

		$query = "select count(*) from `".GD_BD_.$this->id."` where idx='".$this->data[idx]."' and main='".$this->data[main]."'";
		list($cnt) = $this->db->fetch($query);
		if($this->mode!="write" && $_POST[notice] && $cnt > 1) msg("�亯���� �޸� �Խù��� �������·� ����� �ȵ˴ϴ�",-1);

		if($_POST[notice] || $this->data[notice]) $this->chkNotice();
		else if($this->main<0){
			$this->idx--;
			### idx�� ����
			if($this -> idx < 100) msg("�ùٸ��� ���� �Խ��� �׷��ȣ�Դϴ�.",-1);
			$this->main = 5000;

			list($chk) = $this->db->fetch("select id from ".GD_BOARD_INF." where id='{$this->id}' and idx='a{$this->idx}'");
			if(!$chk) $this->db->insert(GD_BOARD_INF)->set(array('id'=>$this->id, 'idx'=>'a'.$this->idx))->query();

			list($chk) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."` where notice='o'");
			if($chk){
				$this->db->update(GD_BD_.$this->id)->set(array('idx' => 'a'.$this->idx))->where('notice = ?', 'o')->query();
				$this->db->update(GD_BOARD_INF)->set(array('num' => $chk))->where('id = ?', $this->id)->where('idx = ?', 'a'.$this->idx)->query();
				$this->db->update(GD_BOARD_INF)->set(array('num' => $this->db->expression('num - '.$chk)))->where('id = ?', $this->id)->where('idx = ?', 'a'.($this->idx+1))->query();
			}
		}

		if($this->mode=="reply"){
			if($this->data[notice]) msg("�������� �亯�� �� ���� �����ϴ�",-1);
			$query	= "select right(sub,1) from `".GD_BD_.$this->id."` where idx='{$this->data[idx]}' and main='{$this->data[main]}' and length(sub)=length('{$this->data[sub]}')+1 and left(sub,length('{$this->data[sub]}'))='{$this->data[sub]}' order by sub desc limit 1";
			list($sub) = $this->db->fetch($query);
			$sub = ord($sub) + 1;
			if($sub==39 || $sub==92) $sub++;
			else if($sub==256) $sub = 255;
			$this->sub .= chr($sub);
			$this->_pass   = $this->data[password];
			$this->_member = $this->data[m_no];
		}
	}

	function setFileName()
	{
		$maxStr = @floor((256-count($_FILES[file][tmp_name]))/count($_FILES[file][tmp_name]));
		for($i=0;$i<count($_FILES[file][tmp_name]);$i++){
			if($this->old_file[$i]){
				if(strlen($this->old_file[$i])>$maxStr) $div = explode(".",$this->old_file[$i]);
				$tmp_old[] = (strlen($this->old_file[$i])>$maxStr) ? substr($this->old_file[$i],0,$maxStr-6).sprintf("%02d",$i+1).".".substr($div[count($div)-1],0,3) : $this->old_file[$i];
				$tmp_new[] = $this->new_file[$i];
			}
		}
		if( count($_FILES[file][tmp_name]) ){
			$this->new_file = @implode("|",$tmp_new);
			$this->old_file = @implode("|",$tmp_old);
		}
		else {
			$this->new_file = @implode("|",$this->new_file);
			$this->old_file = @implode("|",$this->old_file);
		}
	}

	function getPreFileArr()
	{
		$this->old_file = explode("|",$this->data[old_file]);
		$this->new_file = explode("|",$this->data[new_file]);
	}

	function multiUpload()
	{
		GLOBAL $bdListImgSizeW,$bdListImgSizeH;

		if($this->mode=="modify") $this->getPreFileArr();
		$file_array = reverse_file_array($_FILES[file]);
		for($i=0;$i<count($_FILES[file][tmp_name]);$i++){
			if($_POST[del_file][$i]=="on"){
				unlink($this->data_path."/$this->id/".$this->new_file[$i]);
				@unlink($this->data_path."/$this->id/t/".$this->new_file[$i]);
				$this->old_file[$i] = "";
				$isChange = true;
			}
			if(is_uploaded_file($_FILES[file][tmp_name][$i])){
				if($this->bdMaxSize && $_FILES[file][size][$i] > $this->bdMaxSize) msg("�ִ� ���ε� ������� ".byte2str($this->bdMaxSize)."�Դϴ�",-1);
				if($this->new_file[$i]){
					unlink($this->data_path."/$this->id/".$this->new_file[$i]);
					@unlink($this->data_path."/$this->id/t/".$this->new_file[$i]);
				}
				$this->old_file[$i]	= $_FILES[file][name][$i];
				$this->new_file[$i]	= substr(md5(microtime()),0,16);
				if(preg_match("/^image/",$_FILES[file][type][$i])) thumbnail($_FILES[file][tmp_name][$i],$this->data_path."/$this->id/t/".$this->new_file[$i],$bdListImgSizeW,$bdListImgSizeH,1);
				$upload = new upload_file($file_array[$i],$this->data_path."/$this->id/".$this->new_file[$i]);
				if(!$upload -> upload()) msg('������ �ùٸ��� �ʽ��ϴ�.',-1);
				$isChange = true;
			}
		}

		### �����뷮 ���
		if($isChange === true) setDu('board');

		$this->setFileName();
	}

	function chkPrivilege()
	{
		switch($this->mode){
		case "modify":
			if($this->ici_admin) $priv_modify = 1;
			if($this->data[m_no]){
				if($this->sess[m_no]==$this->data[m_no]) $priv_modify = 1;
			} else if($_POST[password]){
				$query = "select no from `".GD_BD_.$this->id."` where no='".$this->no."' and password='".md5($_POST[password])."'";
				list($chk) = $this->db->fetch($query);
				if($chk) $priv_modify = 1;
			}
			if(!$priv_modify) msg("��й�ȣ�� ��ġ���� �ʽ��ϴ�",-1);
			break;
		case "reply":
			if($this->data[notice]) msg("�������׿��� �亯�� �Ұ����մϴ�",-1);
		}
	}

	function exec_()
	{
		$this->binary_patch();

		$this->getIndex();
		$this->chkPrivilege();
		$this->multiUpload();

		if($_POST[html])	$html = 1;
		if($_POST[br])		$html += 2;

		### �������� ���� html ��� on
		$html = 1;

		if((!eregi("^http://",$_POST[urlLink])) && $_POST[urlLink]) $_POST[urlLink] = "http://".$_POST[urlLink];

		$sp_param = array();

		switch($this->mode)
		{
		case "reply":
			$sp_param['_pass'] = $this->_pass;
			$sp_param['_member'] = $this->_member;
		case "write":
			$sp_param['password'] = md5($_POST[password]);
			$sp_param['m_no'] = $this->sess[m_no];
			$sp_param['ip'] = $_SERVER[REMOTE_ADDR];
			$sp_param['regdt'] = Core::helper('Date')->format(G_CONST_NOW);
			$this->db->update(GD_BOARD_INF)->set(array('num' => $this->db->expression('num + 1')))->where('id = ?', $this->id)->where('idx = ?', 'a'.$this->idx)->query();

			//����ϰԽ��ǿ��� �� �Է½� ���ε� �̹����� ������ �ڵ����� �̹������� �������� ����
			if ($this->isMobileBoard === true){
				$imgContents = "";
				$checkSaveUrl = $_SERVER['DOCUMENT_ROOT']."/shop/data/board/".$this->id."/t/";
				$mobileNewFile = explode("|",$this->new_file);
				if(count($mobileNewFile)>0){
					foreach($mobileNewFile as $v=>$val){
						if(getImageSize($checkSaveUrl.$val)) $imgContents .= "[:�̹���".($v+1).":]<br>";
					}
				}
				$_POST[contents] = $imgContents.$_POST[contents];
			}
		case "modify":
			$sp_param['idx'] = 'a'.$this->idx;
			$sp_param['main'] = $this->main;
			$sp_param['sub'] = $this->sub;
			$sp_param['name'] = $_POST[name];
			$sp_param['email'] = $_POST[email];
			$sp_param['homepage'] = $_POST[homepage];
			$sp_param['titleStyle'] = $this->style;
			$sp_param['subject'] = $_POST[subject];
			$sp_param['contents'] = $_POST[contents];
			$sp_param['urlLink'] = $_POST[urlLink];
			$sp_param['old_file'] = $this->old_file;
			$sp_param['new_file'] = $this->new_file;
			$sp_param['notice'] = $_POST[notice];
			$sp_param['secret'] = $_POST[secret];
			$sp_param['html'] = $html;
			$sp_param['category'] = $_POST[subSpeech];

		}

		if (G_CONST_MAGIC_QUOTES) {
			Core::helper('String')->stripslashes($sp_param);
		}

		if ($this->mode == 'modify') {
			$this->db->update(GD_BD_.$this->id)->set($sp_param)->where('no = ?', $this->no)->query();
		}
		else {
			$this->db->insert(GD_BD_.$this->id)->set($sp_param)->query();
		}

		/*
		// @�ӽ� : ���ó �Һи�
		if($this->mode == "reply") {
			$insertId = $this->db->_last_insert_id();
			$this->db->query("UPDATE ".GD_BD_.$this->id." SET reply = CONCAT(reply, '".$insertId.";'), replyCount = replyCount + 1 WHERE no = '".$this->no."'");
		}
		*/

	}

}
?>