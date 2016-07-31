<?

/**
 * Bank class
 * �������� �ڵ��Ա�Ȯ�� �ۼ��� Ŭ����
 */

class Bank
{
	var $act, $ordno, $MID;

	function Bank($act, $ordno)
	{
		$this->act = $act;
		$this->ordno = $ordno;
		$this->getMid();

		### �۽��� �������̵� üũ(�α����� ���� ����)
		if ( $this->act == 'send' )
		{
			ob_start();
			$result = readurl("http://bankmatch.godo.co.kr/sock_ismid.php?MID={$this->MID}&hashdata=" . md5($this->MID));
			if ( $result != 'true' ) return;
			ob_end_clean();
		}

		$this->log( "START" );
		$this->log( "DEBUG [__datetime__] <__ordno__> USER_IP:{$_SERVER['REMOTE_ADDR']}" );

		$this->$act();

		$this->log( "END" );
	}

	### �۽�
	function send()
	{
		$this->log( "DEBUG [__datetime__] <__ordno__> MID:{$this->MID}" );
		if ( $this->MID == '' ) return;

		### �ֹ��� üũ
		ob_start();
		$data = $GLOBALS[db]->fetch("SELECT o.ordno, b.bank, b.account, o.bankSender, o.settleprice, o.settlelog FROM ".GD_ORDER." as o LEFT JOIN ".GD_LIST_BANK." as b ON o.bankAccount=b.sno WHERE ordno='{$this->ordno}'", "ASSOC");
		if ( $this->err( ob_get_clean() ) ) return;

		$data[account] = str_replace(array("-", " "), "", $data[account]);	# ����

		$this->log( "DEBUG [__datetime__] <__ordno__> ORDER_DATA:[\nordno:{$data[ordno]}\nbank:{$data[bank]}\naccount:{$data[account]}\nbankSender:{$data[bankSender]}\nsettleprice:{$data[settleprice]}\n]" );

		if ( $data[ordno] == '' || $data[bank] == '' || $data[account] == '' ) return;

		/***************************************************************************************************
		*  hashdata ����
		*    - ������ ���Ἲ�� �����ϴ� �����ͷ� ��û�� �ʼ� �׸�.
		*    - MID+ordno+bank+account+settleprice+bankSender �� �������� md5 ������� ������ �ؽ���.
		***************************************************************************************************/

		$MID			= $this->MID;				# �������̵�
		$ordno			= trim($data[ordno]);		# �ֹ���ȣ
		$bank			= trim($data[bank]);		# ����
		$account			= trim($data[account]);		# ����
		$bankSender		= trim($data[bankSender]);	# �Ա���
		$settleprice		= trim($data[settleprice]);	# �ݾ�
		$hashdata		= md5($MID . $ordno . $bank . $account . $bankSender . $settleprice);	# hashdata ����

		$urlQuery			= "MID={$MID}&ordno={$ordno}&bank={$bank}&account={$account}&bankSender={$bankSender}&settleprice={$settleprice}&hashdata={$hashdata}";
		$this->log( "DEBUG [__datetime__] <__ordno__> URL_QUERY:{$urlQuery}" );

		$result = readurl("http://bankmatch.godo.co.kr/sock_insert.php?{$urlQuery}");

		if ( preg_match("/\n|\r/", $result) > 0 )
			$this->log( "DEBUG [__datetime__] <__ordno__> RESULT:[\n{$result}\n]" );
		else
			$this->log( "DEBUG [__datetime__] <__ordno__> RESULT:{$result}" );

		if ( preg_match("/^true\|/i",$result) ) // ����
		{
			ob_start();
			$data[settlelog] .= "�������� : �������ֹ����� ���� (" . date('Y-m-d_H:i:s') . ")\n";
			$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET settlelog='{$data[settlelog]}' WHERE ordno='{$this->ordno}'");
			if ( $this->err( ob_get_clean() ) ) return;
		}
	}

	### ����
	function receive()
	{
		$this->log( "DEBUG [__datetime__] <__ordno__> TRANS_ARGUMENT:[\n" . str_replace( array("=", "&"), array(":", "\n"), getVars($except='', $_GET) ) . "\n]" );

		$this->log( "DEBUG [__datetime__] <__ordno__> MID:{$this->MID}" );
		if ( $this->MID == '' )
		{
			echo "false - ���θ� ȯ�������� �������̵� ��� ����";
			return;
		}

		### �ؽ��� ����
		$hashdata	= $_GET[hashdata];	# �ؽ���
		$hashdata2	= md5($this->MID . $_GET[ordno] . $_GET[bkcode] . $_GET[GOcode]);

		if ( $hashdata2 != $hashdata ) // �ؽ��� ���� ����
		{
			$this->log( "DEBUG [__datetime__] <__ordno__> HASHDATA:������ ���Ἲ ����" );
			echo "false - ������ ���Ἲ ����";
			return;
		}

		### �ֹ��� üũ
		ob_start();
		$data = $GLOBALS[db]->fetch("select ordno, step, step2, settlelog from ".GD_ORDER." where ordno='$_GET[ordno]'");
		if ( $this->err( ob_get_clean() ) ) return;

		$status = $GLOBALS[r_stepi][$data[step]][$data[step2]];
		$this->log( "DEBUG [__datetime__] <__ordno__> ORDER_DATA:[\nordno:{$data[ordno]}\nstep:{$data[step]}\nstep2:{$data[step2]}\nstatus:{$status}\n]" );

		if ( $data[ordno] == '' )
		{
			echo "false - ���θ��� �ֹ������� ����";
			return;
		}

		if ( $data['step'] == '0' && $data['step2'] == '44' ){
			echo "false - ��ҵ� �ֹ���";
			return;
		}

		if ( $data[step] != '0' ){
			echo "before";
			return;
		}

		### �����Ȳ�� ó��
		ob_start();
		@ctlStep($_GET[ordno],1,'stock');
		if ( $this->err( ob_get_clean() ) ) return;

		### �����޸� �����
		ob_start();
		$data[settlelog] .= "�������� : �������ֹ� �Ա�Ȯ�� ó�� (" . date('Y-m-d_H:i:s') . ")\n";
		$GLOBALS[db]->query("UPDATE ".GD_ORDER." SET settlelog='{$data[settlelog]}' WHERE ordno='{$_GET[ordno]}'");
		if ( $this->err( ob_get_clean() ) ) return;

		### �����޽��� ���
		$this->log( "INFO [__datetime__] <__ordno__> RESULT:����" );
		echo 'true';
	}

	### �ֹ����±������� ����
	function filterOrderStep()
	{
		$this->log( "DEBUG [__datetime__] <__ordno__> TRANS_ARGUMENT:[\n" . str_replace( array("=", "&"), array(":", "\n"), getVars($except='', $_GET) ) . "\n]" );

		$this->log( "DEBUG [__datetime__] <__ordno__> MID:{$this->MID}" );
		if ( $this->MID == '' )
		{
			echo "false - ���θ� ȯ�������� �������̵� ��� ����";
			return;
		}

		### �ؽ��� ����
		$hashdata	= $_GET['hashdata'];	# �ؽ���
		$hashdata2	= md5($this->MID);

		if ( $hashdata2 != $hashdata ) // �ؽ��� ���� ����
		{
			$this->log( "DEBUG [__datetime__] <__ordno__> HASHDATA:������ ���Ἲ ����" );
			echo "false - ������ ���Ἲ ����";
			return;
		}

		### ���͸�
		ob_start();
		$resOrdno = array();
		$tmp = explode('|', $_GET['ordnos']);
		foreach($tmp as $ordno)
		{
			$data = $GLOBALS['db']->fetch("select ordno, step, step2 from ".GD_ORDER." where ordno='$ordno'");
			if ( $data['ordno'] != '' && $data['step'] == '0' && $data['step2'] == '0' ) $resOrdno[] =  $data['ordno'];
		}
		if ( $this->err( ob_get_clean() ) ) return;

		### �����޽��� ���
		$this->log( "INFO [__datetime__] <__ordno__> RESULT:".implode('|', $resOrdno) );
		echo implode('|', $resOrdno);
	}

	### ERROR
	function err( $obOut='' )
	{
		if ( mysql_errno() )
		{
			$this->log( "DEBUG [__datetime__] <__ordno__> DB_ERROR:[" . mysql_errno() . "] " . mysql_error() );
			if ( $this->act == 'receive' ) echo "false - ����Ÿ���̽� ���� ����"; # ���Ű�츸
			return true;
		}
		else if ( $obOut )
		{
			if ( preg_match("/<span style='font:8pt tahoma'><b>\[ERROR\]<\/b>/i", $obOut) ) return false; # ���ϰ��� �޽����� ���
			$obOut = strip_tags( str_replace( "<br>", "\n", $obOut ) );
			$tmp_o = explode( "\n", $obOut );
			$tmp_n = array();
			foreach ( $tmp_o as $str )
				if ( trim($str) != '' ) $tmp_n[] = trim($str);
			$obOut = implode( "\n", $tmp_n );
			if ( count($tmp_n) > 1 ) $obOut = "[\n$obOut\n]";

			$this->log( "DEBUG [__datetime__] <__ordno__> CODE_ERROR:" . $obOut );
			if ( $this->act == 'receive' ) echo "false - �ڵ� ���� ����"; # ���Ű�츸
			return true;
		}
	}

	### �������̵�
	function getMid()
	{
		$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
		if (is_file($file)){
			$file = file($file);
			$godo = decode($file[1],1);
		}
		if ( $godo[sno] > 0 ) $this->MID= sprintf("GODO%05d",$godo[sno]);
	}

	### �α� �����
	function log( $msg )
	{
		if ( $msg == 'START' ) $msg = "INFO  [__datetime__] <__ordno__> START";
		else if ( $msg == 'END' ) $msg = "INFO  [__datetime__] <__ordno__> END";

		$msg = str_replace( array('__datetime__', '__ordno__'), array(date('Y-m-d_H:i:s:B'), $this->ordno), $msg ) . "\n";
		error_log($msg, 3, $tmp = dirname(__FILE__) . "/../log/bank" . ucfirst($this->act) . "_" . date('Ymd') . ".log");
		@chmod( $tmp, 0707 );
	}


}

?>