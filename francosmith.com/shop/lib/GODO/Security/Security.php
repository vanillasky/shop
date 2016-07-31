<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Security
 */

/**
 * Security
 * @see http://ha.ckers.org/xss.html
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Security
 */
class Security {

	/**
	 * �ü��
	 * @var string
	 */
	private $PHP_OS;

	/**
	 * �˻� ���
	 * @var array
	 */
	private $_target;

	/**
	 * �˻� ���
	 * @var array
	 */
	private $_result;

	/**
	 * ��ū ����
	 * @var array
	 */
	private $token_cfg= array();

	/**
	 * ���� ������ ���� Ȯ���� (binary ���� ����)
	 * @var array
	 */
	private $_excutableFileExtension = array('asa', 'asax', 'asp', 'aspx', 'cgi', 'htm', 'html', 'inc', 'js', 'jsp', 'php', 'php3', 'phtm', 'pl', 'pm', 'py', 'sh');

	/**
	 * Construct
	 * �˻� ��� �� OS ����
	 * @return void
	 */
	public function __construct() {

		// ������ �ü��
		if ( strpos( strtoupper( PHP_OS ), 'WIN' ) === 0)
			$this->_PHP_OS = 'WIN';
		else
			$this->_PHP_OS = 'LINUX';

		// GET, POST, COOKIE, FILES �� ���ؼ� ����
		$this->_target['GET'] = true;
		$this->_target['POST'] = true;
		$this->_target['COOKIE'] = true;
		$this->_target['FILES'] = true;

		// ��ū ����
		$this->token_cfg = array(
			'use' => session_id() == '' ? false : true,
			'pipe' => '|',
			'name' => '_gd_security_tk_',
		);

	}

	/**
	 * �˻�
	 * @return void
	 */
	public function detect() {

        // temporary not use.
        return ;

		$this->detectThreat();

	}

	/**
	 * ���� ����� ���
	 * @return void
	 */
	public function result() {

		if ( empty( $this->_result ))
			return;

		ob_start();

		echo '
		<style>
		.gd-security-result-table {border-collapse:collapse;}
		.gd-security-result-table th,
		.gd-security-result-table td {font-family:Bitstream Vera Sans Mono,Dotum;font-size:11px;padding:5px;}
		.gd-security-result-table tr.threat {background:#FABCB0}
		.gd-security-result-table tr.safe {background:#C6F6C4}
		</style>
		<table width="100%" border="1" bordercolor="#E6E6E6" class="gd-security-result-table">
		<tr>
			<th>type</th>
			<th>input (html special chars)</th>
			<th>threat</th>
		</tr>';

		foreach ( $this->_result as $log ) {
			echo '
			<tr class="' . ( $log['type'] != 'none' ? 'threat' : 'safe' ) . '">
				<td>' . $log['type'] . '</td>
				<td>' . htmlspecialchars( $log['value'] ) . '</td>
				<td>' . htmlspecialchars( $log['founded'] ) . '</td>
			</tr>';
		}

		echo '</table>';

		$_html = ob_get_contents();
		ob_end_clean();

		return $_html;

	}

	/**
	 * ���� �������� ���μ��� ������ ���� üũ
	 * �ַ�ǳ� ���μ��� �������� indb �� �����ϴ� ������ ����
	 * @return boolean
	 */
	private function isWritePage() {

		$_php = basename( $_SERVER['PHP_SELF'] );
		$_php = explode('.', $_php);
		array_pop($_php);
		$_php = implode('.',$_php);

		return
		  (strpos($_php,'indb') !== false)
		? true
		: false;
	}

	/**
	 * �˻簡 �ʿ���� ������ ���� üũ
	 * @param string $path [optional]
	 * @return boolean
	 */
	private function isWhiteList( $path = '' ) {

		if ( $path == '' )
			$path = $_SERVER['PHP_SELF'];

		$whitelist = array(
			/*/shop*/ '/admin/proc/indb.php',
			/*/shop*/ '/_godoConn/.+',
			/*/shop*/ '/_spt_service/.+',
			/*/shop*/ '/proc/.+',
			/*/shop*/ '/partner/.+',
			/*/shop*/ '/engine/.+',
			/*/shop*/ '/order/ipay_order_indb.php',
			/*/shop*/ '/order/card/.+',					// pg ���� ó��
			/*/shop*/ '/todayshop/card/.+',				// pg ���� ó��
		);

		foreach ( $whitelist as $item ) {
			$pattern = '/^([a-zA-Z0-9_\/]+)?' . str_replace( array(	'\\', '/' ), '\/', $item ) . '$/';
			if ( preg_match( $pattern, $path ))
				return true;
		}

		return false;
	}

	/**
	 * CSRF (cross site request forgery) �� ������ ��ū ����
	 * @return void
	 */
	private function setToken() {

		$_SESSION[ $this->token_cfg['name'] ] = $this->getClientHash();

	}

    /**
     * Ŭ���̾�Ʈ�� ������(?) ���� �����Ͽ� ����
     * @return string
     */
	private function getClientHash() {

		$tmp = array();
		$tmp[] = session_id();
		$tmp[] = $_SERVER['HTTP_USER_AGENT'];

		return md5( implode( $this->token_cfg['pipe'], $tmp ) );

	}

	/**
	 * ���μ��� �������� ���� ȣ������ üũ
	 * @return void
	 */
	private function checkToken() {

		if (!$_SESSION[ $this->token_cfg['name'] ] || ($_SESSION[ $this->token_cfg['name'] ] != $this->getClientHash())) {
			$msg = sprintf( '%s request is not valid.', $_SERVER['PHP_SELF'] );
			Core::raiseError($msg);
		}

	}

	/**
	 * �˻� ��� ���� ��Ұ� �ִ��� üũ
	 * @return void
	 */
	private function detectThreat() {

		if ($this->token_cfg['use']) {

			if ( $this->isWritePage() ) {

				if (!$this->isWhiteList())
					 $this->checkToken();
			}
			else {
				$this->setToken();
			}

		}

		foreach ( $this->_target as $_target=>$status ) {

			if ( $status === false)
				continue;

			@eval( '$V = &$_' . strtoupper( $_target ) . ';' );

			if ( $_target === 'FILES')
				$this->scanFile( $V );
			else
				$this->scanString( $V );

		}

	}

	/**
	 * ������ üũ
	 * @param string $value
	 * @param string $patterns
	 * @return mixed ���� ��Ұ� ������ �ش� array, ������ false
	 */
	private function testThreat( $value, $patterns ) {

		$value = $this->getTestString( $value );

		foreach ( $patterns as $regexp)
			if ( preg_match( $regexp, $value, $matches ))
				return $matches;

		return false;

	}

	/**
	 * ������ ������ üũ
	 * @param string $var
	 * @return void
	 */
	private function scanFile( $var ) {

		if ( is_array( $var ) ) {

			foreach ( $var as $val ) {

				$is_threat = false;

				if ( $val['error'] === 0) {
					if ( $matches = $this->isExcutableFile( $val['name'] )) $is_threat = true;
				}

				$this->throwThreat( 'file', $val['name'], $matches, $is_threat );

			}

		}

	}

	/**
	 * ���ڿ��� ������ üũ
	 * @param string $var
	 * @return void
	 */
	private function scanString( $var ) {

		if ( is_array( $var ) ) {

			foreach ( $var as $val ) {

				if ( empty( $val ))
					continue;
				elseif ( is_array( $val ) ) {
					$this->scanString( $val );
					continue;
				}

				$is_threat = false;

				foreach ( $this->getThreatPatterns() as $type=>$patterns ) {

					if ( ( $matches = $this->testThreat( $val, $patterns ) ) !== false ) {

						switch ( $type ) {
							case 'img' :
								if ( $this->isExcutableFile( $matches[1] ) ) {
									$is_threat = true;
									break ( 2 );
								}
								break;

							case 'path' :
								if ( $this->isSystemPath( $matches[0] ) ) {
									$is_threat = true;
									break ( 2 );
								}
								break;

							default :
								$is_threat = true;
								break ( 2 );
						}

					}

				}

				$this->throwThreat( $type, $val, $matches, $is_threat );

			}

		}

	}

	/**
	 * ��ΰ� �ý��� ������� üũ
	 * @param string $path
	 * @return boolean
	 */
	private function isSystemPath( $path ) {

		if ( $this->_PHP_OS == 'WIN')
			$endslash_pattern = '/\\$/';
		else
			$endslash_pattern = '/\/$/';

		$doc_root = isset( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['HOME'];
		$doc_root .= preg_match( $endslash_pattern, $doc_root ) ? DIRECTORY_SEPARATOR : '';

		$_path = $doc_root . dirname( $_SERVER['PHP_SELF'] ) . DIRECTORY_SEPARATOR . $path;

		$_path = realpath( $_path );

		return ( strlen( $_path ) < strlen( $doc_root ) ) ? true : false;

	}


	/**
	 * ���� Ȯ���ڸ� �̿��Ͽ� ������ �������� üũ
	 * @param string $filepath
	 * @return mixed
	 */
	private function isExcutableFile( $filepath ) {

		$_path = parse_url( $filepath );

		if ( preg_match( '/\.(' . implode( '|', $this->_excutableFileExtension ) . ')$/i', $_path['path'], $matches ) ) {
			return $matches;
		}
		/*else if (preg_match('/\.('.implode('|', $this->_excutableFileExtension).')/i', $_path['path'], $matches)) {
		 return $matches;
		 }*/
		else {
			return false;
		}

	}

	/**
	 * �����Ҹ� üũ�� ���ڿ��� ����
	 * @param string $str
	 * @return string
	 */
	private function getTestString( $str ) {

		$str = G_CONST_MAGIC_QUOTES ? stripslashes( $str ) : $str;

		// url decode
		$str = urldecode( $str );
		$str = rawurldecode( $str );

		// &xxxx ġȯ
		$str = strtr( $str, array_flip( get_html_translation_table( HTML_SPECIALCHARS /* HTML_SPECIALCHARS or HTML_ENTITIES*/ ) ) );

		// CR, LF, &#xnn, &#nn, /* */ ġȯ
		$_source_pattern = array(
			'#\/\*.*\*\/#Ui',
			'/\x00/',
			'/\x0a/',
			'~&#x([0-9a-f]+);~ei',
			'~&#x([0-9a-f]+)~ei',
			'~&#([0-9]+);~e',
			'~&#([0-9]+)~e'
		);
		$_target_pattern = array(
			'',
			'',
			'',
			'chr(hexdec("\\1"))',
			'chr(hexdec("\\1"))',
			'chr("\\1")',
			'chr("\\1")'
		);
		$str = preg_replace( $_source_pattern, $_target_pattern, $str );

		return $str;

	}

	/**
	 * �����Ҹ� ������ ���� ǥ������ ����
	 * @return array �����Ҹ� ������ ���� ǥ����
	 */
	private function getThreatPatterns() {

		static $patterns = array();

		if ( empty( $patterns ) ) {

			$patterns['tag'] = array(
				'/<iframe[[:space:]]*/i',
				'/<meta[[:space:]]*/i'
			);

			$patterns['xss'] = array(
				'/expression[[:space:]]*/',
				'/window\.open[[:space:]]*\(/',
				'/location.href[[:space:]]*=/',
				'/xss\:*\(.+\)/',
				'/document\.cookie/',
				'/document\.location/',
				'/document\.write/',
				'/<script/i',
				'/%3script/',
				'/\x3script/i',
				'/javascript:/i',
				'/(on)([a-z]+)[[:space:]]*=/i'
			);

			$patterns['img'] = array(
				'@<img.+src="(.+)".*>@Ui'
			);

			$patterns['path'] = array(
				'/(\.\.\/)+/',
				'/(\.\.\\\)+/'
			);

		}

		return $patterns;

	}

	/**
	 * ���� ��� �߽߰� ó��
	 * @param string $type
	 * @param string $value
	 * @param array $matches
	 * @param boolean $is_threat
	 * @return void
	 */
	private function throwThreat( $type, $value, $matches, $is_threat ) {

		if ( !$is_threat)
			return;

		// logging.
		$tmp = array(
			'type'=>$is_threat ? $type : 'none',
			'value'=>$value,
			'founded'=>$is_threat ? $matches[0] : '',
			'threat'=>$is_threat ? 'true' : 'false'
		);

		$this->_result[] = $tmp;

		// action.
		// what to do ?
		/*$msg = "����� �� ���� ���ڿ��� ���ԵǾ� �ֽ��ϴ�.\\n���� ���ڿ� : ".addslashes($matches[0]);
		 msg($msg, -1);*/

	}

}
?>
