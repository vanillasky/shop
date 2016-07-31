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
 * GODO_DB_driver
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
class GODO_DB_driver {

	/**
	 * ���� �߻��� ȭ�� ��� ����
	 * @var boolean
	 */
	public $silent = false;

	/**
	 * DB ������ ���� ����
	 * @var boolean
	 */
	private $fixed = false;

	/**
	 * �ִ� ���� �ð� (������ ���ο� DB ������ ����)
	 * @var integer
	 */
	private $timeout = 30;


	/**
	 * ������ DB ���� ��� �ð�
	 * @var integer
	 */
	protected $connectTime = 0;

	/**
	 * replication ��� ���� (�߰��� ������ 2�� �̻��� ��� true �� ������)
	 * @var boolean
	 */
	private $replication = false;

	/**
	 * GODO_DB_statement �ν��Ͻ�
	 * @var GODO_DB_statement
	 */
	private $statement = null;

	/**
	 * ���� ������(�����) DB ��ȣ
	 * @var integer
	 */
	private $seq = null;

	/**
	 * ����� DB ���� ��ũ
	 * @var resource
	 */
	protected $dbconn = null;

	/**
	 * ������ DB ����
	 * @var resource
	 */
	protected $dbconns = array();

	/**
     * ������
	 * @var integer
	 */
	private $scope = 0;

	/**
	 * Ʈ����� ����
	 * @var boolean
	 */
	protected $transaction = false;

	/**
	 * �ν��Ͻ� ���� ����
	 * @return
	 */
	public function __clone() {}

	/**
	 * Construct
 	 * @return void
	 */
	public function __construct() {

		$this->setScope();

		$driver = strtolower($this->getName());
		$class_name = sprintf('GODO_DB_driver_%s_statement', $driver);

		if (class_exists($class_name, true)) {	// autoload
			$this->statement = new $class_name;
		}
		else {
			Core::raiseError('Prepared Stetement �� ����� �� ����');
		}

	}

	/**
	 * Destruct
 	 * @return void
	 */
	public function __destruct() {}


    /**
     * DB ����̹� �������� ����
     * @return void
     */
	private function setScope() {

		static $scope = 0;

		$scope++;

		$this->scope = $scope;

	}

	/**
	 * ������ ���� ������ �����ϰų�, ������ ������ ������
 	 * @param string $mode [optional]
	 * @param array $data [optional]
	 * @return array database server infomation
	 */
	public function server( $mode = 'get', $data = null ) {

		static $servers = array();

		if ( $mode == 'set' && $data ) {

			if (!isset($servers[$this->scope]))
				$servers[$this->scope] = array();

			if (!in_array( $data, $servers[$this->scope] ))
				$servers[$this->scope][] = $data;

			if (sizeof($servers[$this->scope]) > 1) $this->replication = true;
		}
		else {
			return is_int($data) ? $servers[$this->scope][$data] : $servers[$this->scope];
		}

	}

	/**
	 * ��� DB ������ ����
	 * @return void
	 */
	public function disconnects() {
		$this->closeAll();
	}

	/**
	 * DB ������ �����Ͽ� �����ϵ��� ����
	 * @param boolean $bool [optional]
	 * @return void
	 */
	public function setFixedMode( $bool = true ) { // true or false

		$this->fixed = (boolean)$bool;
	}

	/**
	 * DB ������ �����ϰų�, ������ ������ ����
	 * @param object $rw [optional]
	 * @return resource database connection
	 */
	protected function getDBconn( $rw = 'w' ) {

		$now = time();

		// ���� ��� �϶�, db �� �����Ͽ�, ���� �б� ���� ���ο� ������ �������� �ʵ��� ��
		if ( $rw == 'w' || $this->fixed === true ) {
			$this->setFixedMode( true );
			$this->seq = 0;
		}
		else {
			$this->seq = mt_rand( 0, sizeof( $this->server( 'get' ) ) - 1 );
		}

		if ( !isset( $this->dbconns[$this->seq] ) || $this->timeout <= ( $now - $this->connectTime ) ) {
			$this->dbconns[$this->seq] = $this->connect( $this->server( 'get', $this->seq ) );
			$this->connectTime = $now;
		}

		$this->dbconn = $this->dbconns[$this->seq];

		return $this->dbconn;
	}

	/**
	 * ��� DB ������ ����
	 * @return void
	 */
	public function closeAll() {

		for ( $i = 0, $m = sizeof( $this->dbconns ); $i < $m; $i++ ) {
			$dbconn = $this->dbconns[$i];
			if ( $this->disconnect( $dbconn ))
				unset( $this->dbconns[$i] );
		}
	}

	/**
	 * ������ �б� / ���� �Ӽ����� üũ
 	 * @param string $sql
	 * @return string r : �б�, w : ����
	 */
	private function rwCheck( $sql ) {

		$sql = trim( $sql );
		switch ( strtoupper( substr( $sql, 0, 4 ) ) ) {
			case 'SELE' :	// select
			case 'EXPL' :	// explain
			case 'SHOW' :	// show
			case 'DESC' :	// describe
				return 'r';
				break;
			default :
				return 'w';
				break;
		}
	}

	/**
	 * ������ SQL, DB ���� �� GODO_DB_statement �ν��Ͻ� ����
 	 * @param string $sql
	 * @return GODO_DB_statement
	 */
	public function prepare( $sql ) {

		$rw = $this->rwCheck($sql);

		$stmt = clone $this->statement;
		$stmt->readOnly = $rw == 'r' ? true : false;
		$stmt->PREPARED_STATEMENT = $sql;
		$stmt->values = array();
		$stmt->setDbconn( $this->getDBconn($rw) );

		return $stmt;
	}

	/**
	 * query �޼����� ��Ī
	 * @return mixed
	 */
	public function execute() {
		$arguments = func_get_args();
		$result = call_user_func_array(array($this, 'query'), $arguments);
		return $result;
	}

	/**
	 * SQL ���� �����Ͽ� ����� ����
	 * �߰� �Ķ���� �Է½�, �� ���� bind �Ͽ� ����
	 * @param string $sql
	 * @return mixed
	 * @todo ĳ�� ���� ���� ����� ���� or ���� �ϴ� �ڵ� �߰�
	 */
	public function query( $sql ) {

		// prevent execute empty SQL
		if (empty($sql)) return false;

		// check arguments
		if (func_num_args() > 1) {
			$args = func_get_args(); unset($args[0]);
		}
		else {
			$args = null;
		}

		$_cache = false;

		// cache trigger ( /*sql_file_cache*/ )
		if (strpos($sql,'/*sql_file_cache*/') === 0) {
			$_cache = true;
		}

		if ($_cache) {
			if ($this->cache()->hasCache($sql) && ($rs = $this->cache()->getCache($sql)) !== false) {
				return $rs;
			}
		}

		$stmt = $this->prepare($sql);
		$stmt->execute($args);
		$rs   = $stmt->getResultResource();

		if ($rs === false && $this->silent !== true && G_CONST_DEVELOPER_MODE) {

			$err   = array();
			if ($who = Core::whoisCallMe()) {
				$err[] = vsprintf('%s @ %d line', $who);	// file, line
			}

			$err[] = sprintf( '<strong>%d : %s</strong>', $this->errorCode(), $this->errorInfo() );
			$err[] = $this->sqlBeautifier( $sql , false);

			Core::raiseError($err);

		}
		else if ($_cache) {
			$this->cache()->setCache($sql, $stmt);
		}

		return $rs;
	}

	protected function cache() {
		static $_cache = null;

		if (is_null($_cache)) {
			$_cache = new GODO_DB_cache();
		}

		return $_cache;
	}

	/**
	 * �����õ� sql ���� ����
	 * @todo sql ������ �ڵ� �ۼ�
	 * @param string  $sql sql query
	 * @param boolean $html [optional] true : ���ڰ� ���� ���� html, false : �ؽ�Ʈ
	 * @return string
	 */
	protected function sqlBeautifier( $sql, $html = true ) {

		return trim($sql);
	}

	/**
	 * Ʈ����� ���θ� ����
	 * @return boolean
	 */
	public function hasTransaction()
	{
		return $this->transaction;
	}

}

?>
