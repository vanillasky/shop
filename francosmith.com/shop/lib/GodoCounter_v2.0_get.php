<?
//==============================================================================//
//		PROGRAM Title : Godo-Shoppingmall Counter
//		Company Name  : (��) �ö������� - ����
//
//		Version       : 2.0 Version (Get)
//		Create Date   : 2006.06.01
//		Update Date   : ----,--,--
//		Programer     : �ڼ���(�ŵ���)
//		Copyright (C)2004 flyfox.co.kr , All rights reserved.
//==============================================================================//





class Counter_Get
{
	var $engine_srchname = array( 'yahoo' => 'p', 'naver' => 'query', 'msn' => 'q', 'empas' => 'q', 'daum' => 'q', 'google' => 'q', 'nate' => 'query', 'korea' => 'keyword', 'dreamwiz' => 'q', 'netian' => 'keyword' );
	var $week_text = array( '0' => '��', '1' => '��', '2' => 'ȭ', '3' => '��', '4' => '��', '5' => '��', '6' => '��' );

	function Counter_Get()
	{
	}

	function multi_array_sum( $arr, $key )
	{
		$tmp = array();
		foreach ( $arr as $row ) $tmp[] = $row[ $key ];
		return array_sum( $tmp );
	}

	function get_pageview( $DateType, $Dates )
	{
		$data = array();
		$i_start = ( $DateType != 'Month' ? substr( $Dates[0], 0, 4 ) : substr( $Dates[0], 0, 4 ) - 1 );
		while( $i_start <= substr( $Dates[1], 0, 4 ) ){ // ����Ÿ �ε�
			if ( is_file ( $path = dirname(__FILE__) . "/../log/godocount_pageview_" . $i_start++ . ".ini" ) ) $data = array_merge( $data, parse_ini_file( $path ) );
		}

		$recode = $this->get_timecount( &$data, $DateType, $Dates );

		return $recode;
	}

	function get_visit( $DateType, $Dates )
	{
		$data = array();
		$i_start = ( $DateType != 'Month' ? substr( $Dates[0], 0, 4 ) : substr( $Dates[0], 0, 4 ) - 1 );
		while( $i_start <= substr( $Dates[1], 0, 4 ) ){ // ����Ÿ �ε�
			if ( is_file ( $path = dirname(__FILE__) . "/../log/godocount_visit_" . $i_start++ . ".ini" ) ) $data = array_merge( $data, parse_ini_file( $path ) );
		}

		$recode = $this->get_timecount( &$data, $DateType, $Dates );

		return $recode;
	}

	function get_visit_main()
	{
		$Dates = array( date("Ymd"), date("Ymd") );

		$data = array();
		$i_start = substr( $Dates[0], 0, 4 ) - 1;
		while( $i_start <= substr( $Dates[1], 0, 4 ) ){ // ����Ÿ �ε�
			if ( is_file ( $path = dirname(__FILE__) . "/../log/godocount_visit_" . $i_start++ . ".ini" ) ) $data = array_merge( $data, parse_ini_file( $path ) );
		}

		$term = array();
		$term[] = time()-24*60*60*2; // 2����
		$term[] = time()-24*60*60*1; // 1����
		$term[] = time(); // ����

		foreach( $term as $day ){
			$res = $this->cal_timecount_day( $data, $day ); // ī��Ʈ ���
			$recode[] = $res;
		}

		$term = array();
		$term[] = time(); // �ݿ�
		$term[] = time()-24*60*60*30; // ����

		foreach( $term as $day ){
			$res = $this->cal_timecount_month( $data, $day ); // ī��Ʈ ���
			$recode[] = $res;
		}

		return $recode;
	}

	function cal_timecount_time( &$data, $field, $unixtime )
	{
		$field = 'time' . substr( $field, -2 );
		$tmp = array();
		$tmp[0] = date('Y-m-d', $unixtime);
		parse_str( $data[ $tmp[0] ], $proArr );
		$tmp[1] = sprintf( "%0d", $proArr["{$field}"] );
		return $tmp;
	}

	function cal_timecount_day( &$data, $unixtime )
	{
		$tmp = array();
		$tmp[0] = date('Y-m-d', $unixtime);
		parse_str( $data[ $tmp[0] ], $proArr );
		$tmp[1] = sprintf( "%0d", array_sum( $proArr ) );
		return $tmp;
	}

	function cal_timecount_month( &$data, $unixtime )
	{
		$tmp = array();
		$tmp[0] = date('Y-m', $unixtime);
		parse_str( $data[ $tmp[0] ], $proArr );
		$t = date( 't', $unixtime );
		for ( $i = 1; $i <= $t; $i++ ){
			parse_str( $data[ $tmp[0] . "-" . sprintf( "%02d", $i ) ], $proArr );
			$tmp[1] += array_sum( $proArr );
		}
		return $tmp;
	}

	function get_timecount( &$data, $DateType, $Dates )
	{
		$recode = array();
		$basearr = array( 'cnt'=>'0', 'aftdate1'=>'', 'aftcnt1'=>'0', 'aftdate2'=>'', 'aftcnt2'=>'0', 'etctext'=>'' );

		$DataUnix[0] = mktime( 0, 0, 0, substr( $Dates[0], 4, 2 ), substr( $Dates[0], 6, 2 ), substr( $Dates[0], 0, 4 ) );
		$DataUnix[1] = mktime( 0, 0, 0, substr( $Dates[1], 4, 2 ), substr( $Dates[1], 6, 2 ), substr( $Dates[1], 0, 4 ) );

		switch ( $DateType ){

		case 'Time':
			for ( $i = 0; $i < 24; $i++ ) $recode[ date( 'Y-m-d ', $DataUnix[0] ) . sprintf( "%02d", $i ) ] = $basearr; // ���ڵ� ����

			$ago_time	= $DataUnix[0] - 86400; // ����
			$ago_week	= $DataUnix[0] - ( 86400 * 7 ); // ���ֵ�����

			foreach ( $recode as $k => $v ){
				list( $bog, $recode["{$k}"]['cnt'] ) = $this->cal_timecount_time( $data, $k, $DataUnix[0] );
				list( $recode["{$k}"]['aftdate1'], $recode["{$k}"]['aftcnt1'] ) = $this->cal_timecount_time( $data, $k, $ago_time );
				list( $recode["{$k}"]['aftdate2'], $recode["{$k}"]['aftcnt2'] ) = $this->cal_timecount_time( $data, $k, $ago_week );
				$recode["{$k}"]['etctext'] = '��';
			}
			break;

		case 'Day':
			for ( $i = $DataUnix[0]; $i <= $DataUnix[1]; $i+=86400 ){
				if ( $i <= time() ) $recode[ date('Y-m-d', $i) ] = $basearr; // ���ڵ� ����
			}

			foreach ( $recode as $k => $v ){
				$unix = mktime( 0, 0, 0, substr( $k, 5, 2 ), substr( $k, 8, 2 ), substr( $k, 0, 4 ) );
				$ago_time	= $unix - 86400; // ����
				$ago_week	= $unix - ( 86400 * 7 ); // ���ֵ�����

				list( $bog, $recode["{$k}"]['cnt'] ) = $this->cal_timecount_day( $data, $unix );
				list( $recode["{$k}"]['aftdate1'], $recode["{$k}"]['aftcnt1'] ) = $this->cal_timecount_day( $data, $ago_time );
				list( $recode["{$k}"]['aftdate2'], $recode["{$k}"]['aftcnt2'] ) = $this->cal_timecount_day( $data, $ago_week );
				$recode["{$k}"]['etctext'] = ' (' . $this->week_text[ date( 'w', $unix ) ] . ')';
			}
			break;

		case 'Month':
			for ( $i = $DataUnix[0]; $i <= $DataUnix[1]; $i+=86400 ){ // ���ڵ� ����
				$recode[ date('Y-m', $i) ] = $basearr;
				$i = mktime( 0, 0, 0, date( 'm', $i ), date( 't', $i ), date( 'Y', $i ) );
			}

			foreach ( $recode as $k => $v ){
				$unix = mktime( 0, 0, 0, substr( $k, 5, 2 ), 1, substr( $k, 0, 4 ) );
				$ago_month	= $unix - 86400; // ����
				$ago_year	= mktime( 0, 0, 0, substr( $k, 5, 2 ), 1, ( substr( $k, 0, 4 ) - 1 ) ); // ���⵿��

				list( $bog, $recode["{$k}"]['cnt'] ) = $this->cal_timecount_month( $data, $unix );
				list( $recode["{$k}"]['aftdate1'], $recode["{$k}"]['aftcnt1'] ) = $this->cal_timecount_month( $data, $ago_month );
				list( $recode["{$k}"]['aftdate2'], $recode["{$k}"]['aftcnt2'] ) = $this->cal_timecount_month( $data, $ago_year );
				$recode["{$k}"]['etctext'] = '��';
			}
			break;
		}

		return $recode;
	}

	function get_engine( $Dates )
	{
		$recode = array();

		$DataUnix[0] = mktime( 0, 0, 0, substr( $Dates[0], 4, 2 ), substr( $Dates[0], 6, 2 ), substr( $Dates[0], 0, 4 ) );
		$DataUnix[1] = mktime( 0, 0, 0, substr( $Dates[1], 4, 2 ), substr( $Dates[1], 6, 2 ), substr( $Dates[1], 0, 4 ) );

		$data = array();
		$i_start = substr( $Dates[0], 0, 4 );
		while( $i_start <= substr( $Dates[1], 0, 4 ) ){ // ����Ÿ �ε�
			if ( is_file ( $path = dirname(__FILE__) . "/../log/godocount_engine_" . $i_start++ . ".ini" ) ) $data = array_merge( $data, parse_ini_file( $path, true ) );
		}

		for ( $i = $DataUnix[0]; $i <= $DataUnix[1]; $i+=86400 ){
			if ( is_array( $data[ date('Y-m-d', $i) ] ) === false ) continue;

			foreach ( $data[ date('Y-m-d', $i) ] as $engine => $word ){
				if ( isset( $recode[ $engine ] ) == false ) $recode[ $engine ] = array( 'cnt'=>'0', 'rate'=>'', 'sub'=>array() );

				$proArr = $this->Parsing_str( $word );
				$recode[ $engine ]['cnt'] += array_sum( $proArr );

				foreach ( $proArr as $w => $cnt ){
					if ( isset( $recode[ $engine ]['sub']["{$w}"] ) == false ) $recode[ $engine ]['sub']["{$w}"] = array( 'cnt'=>'0', 'rate'=>'' );
					$recode[ $engine ]['sub']["{$w}"][cnt] += $cnt;
				}
			}
		}

		$recode = $this->cal_rate( $recode ); // ������ ���
		$recode = $this->arr_sort( $recode, 'cnt', 'desc' ); // 1���з� ����
		foreach ( $recode as $key => $arr ) $recode[ "$key" ][sub] = $this->arr_sort( $recode[ "$key" ][sub], 'cnt', 'desc' ); // 2���з� ����

		return $recode;
	}

	function get_search( $Dates )
	{
		$recode = array();

		$DataUnix[0] = mktime( 0, 0, 0, substr( $Dates[0], 4, 2 ), substr( $Dates[0], 6, 2 ), substr( $Dates[0], 0, 4 ) );
		$DataUnix[1] = mktime( 0, 0, 0, substr( $Dates[1], 4, 2 ), substr( $Dates[1], 6, 2 ), substr( $Dates[1], 0, 4 ) );

		$data = array();
		$i_start = substr( $Dates[0], 0, 4 );
		while( $i_start <= substr( $Dates[1], 0, 4 ) ){ // ����Ÿ �ε�
			if ( is_file ( $path = dirname(__FILE__) . "/../log/godocount_engine_" . $i_start++ . ".ini" ) ) $data = array_merge( $data, parse_ini_file( $path, true ) );
		}

		for ( $i = $DataUnix[0]; $i <= $DataUnix[1]; $i+=86400 ){
			if ( is_array( $data[ date('Y-m-d', $i) ] ) === false ) continue;

			foreach ( $data[ date('Y-m-d', $i) ] as $engine => $word ){

				$proArr = $this->Parsing_str( $word );

				foreach ( $proArr as $w => $cnt ){
					if ( isset( $recode["{$w}"] ) == false ) $recode["{$w}"] = array( 'cnt'=>'0', 'rate'=>'', 'sub'=>array() );
					$recode["{$w}"][cnt] += $cnt;

					if ( isset( $recode["{$w}"]['sub'][ $engine ] ) == false ) $recode["{$w}"]['sub'][ $engine ] = array( 'cnt'=>'0', 'rate'=>'' );
					$recode["{$w}"]['sub'][ $engine ]['cnt'] += $cnt;
				}
			}
		}

		$recode = $this->cal_rate( $recode ); // ������ ���
		$recode = $this->arr_sort( $recode, 'cnt', 'desc' ); // 1���з� ����
		foreach ( $recode as $key => $arr ) $recode[ "$key" ][sub] = $this->arr_sort( $recode[ "$key" ][sub], 'cnt', 'desc' ); // 2���з� ����

		return $recode;
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

	function cal_rate( $arr )
	{
		$tot = $this->multi_array_sum( $arr, 'cnt' );

		foreach ( $arr as $k => $v ){
			$arr[ $k ]['rate'] = round( ( $v['cnt'] / $tot * 100 ) , 2 );

			foreach ( $v['sub'] as $ks => $vs ) $arr[ $k ]['sub'][ $ks ]['rate'] = round( ( $vs['cnt'] / $tot * 100 ) , 2 );
		}

		return $arr;
	}

	function arr_sort( $arr, $key, $orderby )
	{
		$result = array();

		if ( count( $arr ) > 1 ){

			$tmp = array();
			foreach ( $arr as $s_key => $s_arr ) $tmp[ $s_key ] = strtolower( $s_arr[ $key ] ); // �ӽ� ������ ����

			if ( $orderby == 'desc' ) arsort( $tmp ); else asort( $tmp ); // �ӽ� ���� ����
			reset( $tmp );

			foreach ( $tmp as $k => $v ) $result[ $k ] = $arr[ $k ];  // ���� �������� ����Ÿ ����
		}
		else $result = $arr;

		return $result;
	}
}

$counter_get = new Counter_Get();