<?
//==============================================================================//
//		PROGRAM Title : Godo-Shoppingmall Counter
//		Company Name  : (��) �ö������� - ����
//
//		Version       : 2.0 Version
//		Create Date   : 2006.06.01
//		Update Date   : ----,--,--
//		Programer     : �ڼ���(�ŵ���)
//		Copyright (C)2004 flyfox.co.kr , All rights reserved.
//==============================================================================//





class Counter
{
	var $engine_srchname = array( 'yahoo' => 'p', 'naver' => 'query', 'msn' => 'q', 'empas' => 'q', 'daum' => 'q', 'google' => 'q', 'nate' => 'query', 'korea' => 'keyword', 'dreamwiz' => 'q', 'netian' => 'keyword' );

	function Counter()
	{
		$this->ConnectDate	= date("Y/m/d/D/H");											// ��/��/��/����/�ð�
		$this->Get_Date();																	// ���� ��¥ ���ϱ�
		$this->Date			= "{$this->DateYear}-{$this->DateMonth}-{$this->DateDay}";		// ��¥(YYYY:MM:DD)
		$this->Time			= "{$this->DateHour}:{$this->DateMinut}:{$this->DateSecond}";	// �ð�(HH:II:SS}
		$this->LogIP		= getenv("REMOTE_ADDR");										// ���� IP
		$this->ConnectLink	= getenv("HTTP_REFERER");										// ���� ���
		if ( $this->ConnectLink == '' ) $this->ConnectLink = "�����Է� �Ǵ� ���ã������";	// ���� ��� ���� ������
		$this->Check_CountCookie();															// ��Ű
	}

	//==============================================================================//
	//	��¥ ���ϴ� �Լ�
	//==============================================================================//
	function Get_Date()
	{
		$DateStr = explode("/", $this->ConnectDate);

		$this->DateYear		= $DateStr[0];
		$this->DateMonth	= $DateStr[1];
		$this->DateDay		= $DateStr[2];
		$this->DateWeek		= $DateStr[3];
		$this->DateHour		= $DateStr[4];
		$this->DateMinut	= date(i);
		$this->DateSecond	= date(s);
	}

	//==============================================================================//
	//	��Ű�� ������ ����
	//==============================================================================//
	function Check_CountCookie()
	{
		if ( !IsSet( $_COOKIE['CountCookie'] ) ){ // Cookie ���� ����
			$RandCookie = md5(uniqid(rand()));
			SetCookie("CountCookie",$RandCookie,0,"/");
		}
		else $RandCookie = $_COOKIE['CountCookie'];

		$this->RandCookie = $RandCookie;
	}

	//==============================================================================//
	//	�迭�� ����ȭ
	//==============================================================================//
	function Concat_str( $str )
	{
		$tmp = array();
		foreach ( $str as $key => $value ) $tmp[] = $key  ."=" . $value;
		$str = implode( "&", $tmp );
		return $str;
	}

	//==============================================================================//
	//	���� �Ľ�
	//==============================================================================//
	function Parsing_str( $str )
	{
		$parse = array();
		$tmp = explode( "&", $str );
		foreach ( $tmp as $pro ){
			if ( $pro == '' ) continue;
			$tmp2 = explode( "=", $pro );
			$parse[ $tmp2[0] ] = $tmp2[1];
		}
		return $parse;
	}

	//==============================================================================//
	//	IP,Cookie üũ
	//==============================================================================//
	function Count_init()
	{
		GLOBAL $db;

		$query = "
		create temporary table gd_godocount_ip(
			sno	int unsigned not null auto_increment primary key,
			thatDay datetime default NULL,
			IPAddress varchar(15) default NULL,
			CookieCHK varchar(32) default NULL
		)
		";
		$db->query($query); // �ӽ� IP TABLE ����

		$path = dirname(__FILE__) . "/../log/godocount_ip.ini";

		if ( is_file ( $path ) ){ // TABLE�� IP DATA ����
			foreach ( parse_ini_file( $path, true) as $arr ) $db->query( "INSERT INTO gd_godocount_ip VALUES ('', '{$arr['thatDay']}', '{$arr['IPAddress']}', '{$arr['CookieCHK']}')" );
		}

		$db->query( "DELETE FROM gd_godocount_ip WHERE thatDay < '{$this->Date} 00:00:00'" ); // ������ �ڷ� ����

		list( $cnt ) = $db->fetch( "SELECT count(sno) FROM gd_godocount_ip WHERE thatDay LIKE '{$this->Date}%' AND IPAddress = '{$this->LogIP}' AND CookieCHK = '{$this->RandCookie}'" );

		if ( $cnt < 1 ){ // ���� �ð��ȿ� ���� IP �� ������ IP ���� �� ī���� ����
			$db->query( "INSERT INTO gd_godocount_ip (thatDay,IPAddress,CookieCHK) VALUES ('{$this->Date} {$this->Time}','{$this->LogIP}','{$this->RandCookie}')" );
			$this->Count_Increase(); // ī���� ����
		}
		else $db->query( "UPDATE gd_godocount_ip SET thatDay = '{$this->Date} {$this->Time}' WHERE IPAddress = '{$this->LogIP}' AND CookieCHK='{$this->RandCookie}'" );
		//if ( $cnt >= 1 ) $this->Count_Increase(); // ī���� ����(���½û���)

		{ // ����
			$fp = @fopen( $path, "w" );
			$res = $db->query( "SELECT * FROM gd_godocount_ip ORDER BY sno" );
			while ( $row = $db->fetch( $res ) ){
				@fwrite( $fp, "[" . $row['sno'] . "]" . "\n" );
				@fwrite( $fp, "thatDay = \"" . $row['thatDay'] . "\"" . "\n" );
				@fwrite( $fp, "IPAddress = \"" . $row['IPAddress'] . "\"" . "\n" );
				@fwrite( $fp, "CookieCHK = \"" . $row['CookieCHK'] . "\"" . "\n" );
			}
			@fclose( $fp );
			@chMod( $path, 0757 );
		}

		$db->query( "drop temporary table gd_godocount_ip" );

		$this->Count_Pageview(); // �������� ����
	}

	//==============================================================================//
	//	ī���� �ø���
	//==============================================================================//
	function Count_Increase()
	{
		{ // ī���� �ø��� - ��ü(godocount_counter)
			$counter = array();
			$path = dirname(__FILE__) . "/../log/godocount_counter.ini";
			if ( is_file ( $path ) ) $counter = parse_ini_file( $path );

			if ( $counter['thatDay'] != "{$this->Date}" ){ // ��¥ ������ ��� today, yesday �ʱ�ȭ �� ����
				$fp = @fopen( $path, "w" );
				@fwrite( $fp, "thatDay = \"" . "{$this->Date}" . "\"" . "\n" );
				@fwrite( $fp, "today = \"" . 1 . "\"" . "\n" );
				@fwrite( $fp, "yesday = \"" . $counter['today'] . "\"" . "\n" );
				@fwrite( $fp, "count = \"" . ( $counter['count'] + 1 ) . "\"" . "\n" );
				@fclose( $fp );
				@chMod( $path, 0757 );
			}
			else { // ��¥ ������ ��� ���� �� ����
				$fp = @fopen( $path, "w" );
				@fwrite( $fp, "thatDay = \"" . "{$this->Date}" . "\"" . "\n" );
				@fwrite( $fp, "today = \"" . ( $counter['today'] + 1 ) . "\"" . "\n" );
				@fwrite( $fp, "yesday = \"" . $counter['yesday'] . "\"" . "\n" );
				@fwrite( $fp, "count = \"" . ( $counter['count'] + 1 ) . "\"" . "\n" );
				@fclose( $fp );
				@chMod( $path, 0757 );
			}
		}

		{ // ī���� �ø��� - �湮��(godocount_visit)
			$counter = array();
			$path = dirname(__FILE__) . "/../log/godocount_visit_" . date('Y') . ".ini";
			if ( is_file ( $path ) ) $counter = parse_ini_file( $path );

			parse_str( $counter[ "{$this->Date}" ], $counter[ "{$this->Date}" ] ); // ���ϰ� ����Ÿ �Ľ�
			$counter[ "{$this->Date}" ][  "time" . sprintf( "%02d", $this->DateHour ) ] += 1; // ī���� ����
			$counter[ "{$this->Date}" ] = $this->Concat_str( $counter[ "{$this->Date}" ] ); // ���ϰ� ����Ÿ ����ȭ

			{ // ����
				$fp = @fopen( $path, "w" );
				foreach ( $counter as $day => $value ) @fwrite( $fp, $day  . " = \"" . $value . "\"" . "\n" );
				@fclose( $fp );
				@chMod( $path, 0757 );
			}
		}

		{ // ī���� �ø��� - �˻�����(godocount_engine)
			$link = parse_url( $this->ConnectLink );	// �ּ� ����Ÿ �Ľ�
			parse_str( $link[query], $link[query] );	// ���� ���ڿ� �Ľ�
			if ( $_SERVER['HTTP_HOST'] == 'dev2.godo.co.kr' ) $link[host] = $link[query][site]; // �ӽ�

			if ( preg_match("/yahoo\.com|naver\.com|msn\.co\.kr|empas\.com|daum\.net|google\.co\.kr|nate\.com|korea\.com|dreamwiz\.com|netian\.com$/is", $link[host], $matches) ){

				$engine = substr( $matches[0], 0, strpos($matches[0], '.') );	// �˻�����
				$search = $link[query][  $this->engine_srchname[ $engine ] ];			// �˻���
				$search = str_replace( array( '"', '&', '=', '[', ']' ), array( '%22', '%26', '%3d', '%5b', '%5d' ), stripslashes( $search ) );

				if ( $search != '' ){
					$counter = array();
					$path = dirname(__FILE__) . "/../log/godocount_engine_" . date('Y') . ".ini";
					if ( is_file ( $path ) ) $counter = parse_ini_file( $path, true );

					$engine_searchs = $this->Parsing_str( $counter[ "{$this->Date}" ][ $engine ] ); // �˻����� ����Ÿ �Ľ�
					$engine_searchs[ "$search" ] += 1; // ī���� ����
					$counter[ "{$this->Date}" ][ $engine ] = $this->Concat_str( $engine_searchs ); // �˻����� ����Ÿ ����ȭ

					{ // ����
						$fp = @fopen( $path, "w" );
						foreach ( $counter as $day => $arr ){
							@fwrite( $fp, "[$day]" . "\n" );
							foreach ( $arr as $engine => $search ) @fwrite( $fp, $engine  . " = \"" . $search . "\"" . "\n" );
						}
						@fclose( $fp );
						@chMod( $path, 0757 );
					}
				}
			}
		}
	}

	//==============================================================================//
	//	�������� �ø���
	//==============================================================================//
	function Count_Pageview()
	{
		// ī���� �ø��� - ��������(godocount_pageview)
		$counter = array();
		$path = dirname(__FILE__) . "/../log/godocount_pageview_" . date('Y') . ".ini";
		if ( is_file ( $path ) ) $counter = parse_ini_file( $path );

		parse_str( $counter[ "{$this->Date}" ], $counter[ "{$this->Date}" ] ); // ���ϰ� ����Ÿ �Ľ�
		$counter[ "{$this->Date}" ][  "time" . sprintf( "%02d", $this->DateHour ) ] += 1; // ī���� ����
		$counter[ "{$this->Date}" ] = $this->Concat_str( $counter[ "{$this->Date}" ] ); // ���ϰ� ����Ÿ ����ȭ

		{ // ����
			$fp = @fopen( $path, "w" );
			foreach ( $counter as $day => $value ) @fwrite( $fp, $day  . " = \"" . $value . "\"" . "\n" );
			@fclose( $fp );
			@chMod( $path, 0757 );
		}
	}

}

$counter = new Counter();
$counter->Count_init();

/*echo <<<ENDH
<pre>
{$counter->ConnectDate}
{$counter->LogIP}
{$counter->ConnectLink}
{$counter->DateYear}
{$counter->DateMonth}
{$counter->DateDay}
{$counter->DateWeek}
{$counter->DateHour}
{$counter->DateMinut}
{$counter->DateSecond}
{$counter->RandCookie}

<br>
ENDH;*/
?>