<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */

/**
 * GODO_DB_statement
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
class GODO_DB_statement {

	/**
	 * SQL ��
	 * @var string
	 */
	public $PREPARED_STATEMENT;

	/**
	 * placeholder �� bind �� ��
	 * @var mixed
	 */
	public $values;

	/**
	 * ���� ����� fetch ��Ÿ�� (0 : array, 1 : assoc)
	 * @var integer
	 */
	public $fetchstyle = 0;


	/**
	 * ����� �α��� ���� ����
	 * @var boolean
	 */
	public $debugging = G_CONST_DEVELOPER_MODE;

	/**
	 * DB ���� ��ũ
	 * @var resource
	 */
	protected $dbconn;

	/**
	 * ���� ���
	 * @var mixed
	 */
	protected $rs;

	/**
	 * �߰�������
	 * @var
	 */
	protected $extra_data = array();

	private $_cache = 0;

	/**
	 * �������� ������Ƽ�� �߰������� ������� ������
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return $this->extra_data[$name];
	}

	/**
	 * �������� ������Ƽ�� �߰������� ����� ����
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value) {
		$this->extra_data[$name] = $value;
	}

	/**
	 * ���� ��� ��ü�� �迭�� ����
	 * @return
	 */
	public function toArray() {

		$result = array();
		foreach($this as $row) {
			$result[] = $row;
		}

		return $result;

	}

	/**
	 * placeholder �� ä���� ������ SQL ���� ����
 	 * @return string sql
	 */
	public function getSQL() {
		return Core::loader('GODO_DB')->fillReplaceHolder( $this->PREPARED_STATEMENT, $this->values );
	}

	/**
	 * Ư�� placeholder �� ä�� ���� bind
 	 * @param string $parameter
	 * @param string $value
	 * @return true
	 */
	public function bind( $parameter, $value ) {

		if ( !is_numeric( $parameter ) ) {
			$parameter = preg_replace( '/^:(.*)$/', '\\1', $parameter );
		}

		$this->values[$parameter] = $value;

		return true;
	}

	/**
	 * bind �޼����� ��Ī
 	 * @param string $parameter
	 * @param string $value
	 * @return
	 */
	public function bindValue( $parameter, $value ) {
		return $this->bind( $parameter, $value );
	}

	/**
	 * placeholder �� ä�� ���� �迭���·� �ϰ� bind
 	 * @param array $values
	 * @return
	 */
	public function bindArray( $values ) {
		$parameters = array_keys( $values );
		foreach ( $parameters as $key=>$parameter ) {
			$_parameter = is_numeric( $parameter ) ? $parameter + 1 : $parameter;
			$this->bind( $_parameter, $values[$parameter] );
		}
	}

	/**
	 * �Է� �Ķ���͸� bind �� ������ SQL ���� ����
	 * @param mixed $input [optional]
	 * @return string
	 */
	private function getParsedSQL($input = null) {

		if ( ! is_null( $input )) {
			if (!is_array($input)) $input = array($input);
			$this->bindArray( $input );
		}

		return $this->getSQL();

	}

	/**
	 * ����
 	 * @param string $input [optional]
	 * @return mixed ���� ���
	 */
	public function execute( $input = null ) {

		if (($sql = $this->getParsedSQL($input)) === false) return false;

		if ( $this->debugging )
			$start = microtime();

		if ($expire = $this->getCache()) {

			$cache = new GODO_DB_cache();
			$cache->setCacheExpire($expire);

			if (($this->rs = $cache->getCache($sql))) {
				return $this->rs;
			}

		}

		$this->rs = $this->query( $sql );

		if ( $this->rs !== false && $this->debugging ) {

			$log = array(
				'sql'=>trim( $sql ),
				'lap'=> array($start, microtime()),
				'row'=> $this->rowCount(),
			);

			$trace = debug_backtrace();

			foreach ( $trace as $info ) {

				if ( isset( $info['object'] ) && preg_match( '/^GODO_DB/', get_class( $info['object'] ) ) ) {
					continue;
				}
				else if ( isset( $info['function'] ) && $info['function'] == 'call_user_func_array' ) {
					continue;
				}
				else if ( isset( $info['file'] ) ) {

					$log['func'] = $info['function'];
					$log['file'] = $info['file'];
					$log['line'] = $info['line'];

					break;
				}

			}

			if ($expire = $this->getCache()) {
				$cache = new GODO_DB_cache();
				$cache->setCacheExpire($expire);
				$cache->setCache($sql, $this);
			}

			Core::log($log, 'db');

		}

		return $this->rs;

	}

	/**
	 * ���� �׽�Ʈ (sql �� ���)
 	 * @param string $input [optional]
	 * @return void
	 */
	public function test( $input = null ) {

		$sql = $this->getParsedSQL($input);

		if (function_exists('debug')) {
			debug($sql);
		}
		else {
			echo '<xmp>';
			print_r($sql);
			echo '</xmp>';
		}

	}

	/**
	 * prepared statement �� ������ ����
	 * placeholder ���� ��
	 * @return string
	 */
	public function debugDumpParams() {

		$dump = array(	);
		$params = array();

		// ? type placeholders.
		preg_match_all( '/\?/', $this->PREPARED_STATEMENT, $matches, PREG_SET_ORDER );
		foreach ( $matches as $param ) {
			$params[] = $param[0];
		}

		// name based placeholders.
		preg_match_all( '/:[a-zA-Z_]{1}[a-zA-Z0-9_]+/', $this->PREPARED_STATEMENT, $matches, PREG_SET_ORDER );
		foreach ( $matches as $param ) {
			$params[] = $param[0];
		}

		$dump[] = sprintf( 'SQL: (%s)%s', strlen( $this->PREPARED_STATEMENT ), $this->PREPARED_STATEMENT );
		$dump[] = sprintf( 'Paramsize : %s', sizeof( $params ) );
		$index = 0;
		foreach ( $params as $param ) {
			$index++;

			$dump[] = sprintf( '	Name: (%s)%s', strlen( $param ), $param );
			$dump[] = sprintf( '	Key: %s', $index );

		}

		return implode( PHP_EOL, $dump );
	}

	public function getCache()
	{
		return $this->_cache;

	}

	public function setCache($expire = 30)
	{
		$this->_cache = $expire;
		return $this;
	}


}

?>
