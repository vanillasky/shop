<?

// Ŭ����

class Page{

	var $page	= array();
	/*
	$page[now]		���� ������
	$page[num]		�� �������� ��µǴ� ���ڵ� ����
	$page[total]	��ü ������ ��
	$page[url]		������ ��ũ URL
	$page[navi]		������ �׺���̼�
	$page[prev]		���������� ������
	$page[next]		���������� ������
	*/
	var $recode	= array();
	/*
	$recode[start]	���� ���ڵ� ��ȣ
	$recode[total]	��ü ���ڵ� �� (��ü �ۼ�)
	*/

	var $vars		= array();
	var $field		= "*";			// ������ �ʵ�
	var $cntQuery	= "";			// ��ü ���ڵ� ���� ������ ������ (���ν� ���� ����� ����)
	var $nolimit	= false;		// ���� ��� ��ü ����Ÿ ����
	var $idx		= 0;			// �ش������� ù���� ���ڵ� ��ȣ��

	var $foo = null;

	function Page($page=1,$page_num=20)
	{
		$this->vars[page]= getVars('no,chk,page,password,x,y');
		$this->page[now] = ($page<1) ? 1 : $page;
		$this->page[num] = ($page_num<1) ? 20 : $page_num;
		$this->page[url] = $_SERVER['PHP_SELF'];
		$this->recode[start] = ($this->page[now]-1) * $this->page[num];
		$this->page[prev] = "��";
		$this->page[next] = "��";
	}

	function getTotal()
	{
		if (!$this->cntQuery){
			//$cnt = (!preg_match("/distinct/i",$this->field)) ? "count(*)" : "count($this->field)";

			if(!preg_match("/distinct/i",$this->field)) $cnt = "count(*)";
			else {
				$temp = explode( ",", $this->field );
				$cnt = "count($temp[0])";
			}
			$this->cntQuery = "select $cnt from $this->db_table $this->where";
		}
		list($this->recode[total]) = $GLOBALS[db]->fetch($this->cntQuery);
	}

	function setTotal()
	{
		$limited = ($this->recode[start]+$this->page[num]<$this->recode[total]) ? $this->page[num] : $this->recode[total] - $this->recode[start];
		if (!$this->nolimit) $this->limit = "limit {$this->recode[start]},$limited";
		$this->query = "select $this->field from $this->db_table $this->where $this->tmpQry $this->orderby $this->limit";
		$this->idx = $this->recode[total] - $this->recode[start];
	}

	function setQuery($db_table,$where='',$orderby='',$tmp='')
	{
		$this->db_table = $db_table;
		$this->tmpQry = $tmp;
		if ($where) $this->where = "where ".implode(" and ",$where);
		if (trim($orderby)) $this->orderby = "order by ".$orderby;
		if (!isset($this->recode[total])) $this->getTotal();
	}

	function exec()
	{
		if ($this->foo === null) $this->setTotal();

		$this->setNavi();

	}

	function getNavi($total) {

		$this->recode[total] = $total;

		$this->foo = true;
		$this->exec();

		return $this->page['navi'];
	}

	function setNavi($tpl='') {

		$this->page[total]	= @ceil($this->recode[total]/$this->page[num]);
		if ($this->page[total] && $this->page[now]>$this->page[total]) $this->page[now] = $this->page[total];
		$page[start]		= (ceil($this->page[now]/10)-1)*10;

		if($this->page[now]>10){
			$navi .= "
			<a href=\"{$this->page[url]}?{$this->vars[page]}&page=1{$this->flag}\" class=navi>[1]</a>
			<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[start]{$this->flag}\" class=navi>{$this->page[prev]}</a>
			";
		}

		while($i+$page[start]<$this->page[total]&&$i<10){
			$i++;
			$page[move] = $i+$page[start];
			$navi .= ($this->page[now]==$page[move]) ? " <b>$page[move]</b> " : " <a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\" class=navi>[$page[move]]</a> ";
		}

		if($this->page[total]>$page[move]){
			$page[next] = $page[move]+1;
			$navi .= "
			<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[next]{$this->flag}\" class=navi>{$this->page[next]}</a>
			<a href=\"{$this->page[url]}?{$this->vars[page]}&page={$this->page[total]}{$this->flag}\" class=navi>[{$this->page[total]}]</a>
			";
		}

		if ($this->recode[total] && !$this->nolimit) $this->page[navi] = &$navi;

	}

}

?>
