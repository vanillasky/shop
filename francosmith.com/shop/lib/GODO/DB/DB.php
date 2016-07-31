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
 * GODO_DB
 *
 * ��� ���� :
 *
 * <code>
 *  $db = new GODO_DB;
 *  $db->driver('mysql');	// ���� ���� �Ұ���
 *  $db->addServer($db_host1,$db_user1,$db_pass1,$db_name1);	// master
 *  $db->addServer($db_host2,$db_user2,$db_pass2,$db_name2);	// slave (select operate only)
 * </code>
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
class GODO_DB {

	/**
	 * GODO DB ����̹� instance
	 * @var GODO_DB_driver
	 */
	private $driver = null;

	/**
	 * GODO_DB_statement �ν��Ͻ�
	 * @var GODO_DB_statement
	 */
	private $stmt = null;

	/**
	 * Construct
	 * Ŭ���� ������, �μ��� ����̹� ���� �����ϸ� �ش� �ν��Ͻ��� ������
 	 * @param string $driver [optional]
	 * @return void
	 */
	public function __construct($driver='') {

		if ($driver)
			$this->driver($driver);

	}

	/**
	 * Destruct
	 * ��� DB ������ ����
 	 * @return void
	 */
	public function __destruct() {

		if ($this->driver instanceof GODO_DB_driver)
			$this->driver->disconnects();

	}

	/**
	 * �ν��Ͻ� ����
	 * @return GODO_DB
	 */
	public function __clone() {

		return $this;

	}

	/**
	 * DB ����̹��� ����
	 * @param string $driver [optional]
	 * @return GODO_DB
	 */
	public function driver($driver = 'godomysql') {

		$driver = strtolower(trim($driver));
		$class_name = sprintf('GODO_DB_driver_%s_driver', $driver);

		if (class_exists($class_name, true)) {	// autoload
			$this->driver = new $class_name;
		}
		else {
			$msg = sprintf('"%s" ����̹��� ���� �������� �ʽ��ϴ�.', $driver);
			Core::raiseError($msg);
		}

		return $this;

	}


	/**
	 * DB Ȯ�� �ν��Ͻ��� ����
	 * @param string $name
	 * @return object
	 */
	private function getDriverExtenstion($name) {

		$driver = $this->getDriver()->getName();

		$class_name = sprintf('GODO_DB_driver_%s_%s', strtolower($driver), strtolower($name));

		if (class_exists($class_name, true)) {	// autoload
			$extensions = new $class_name ($this);
		}
		else {
			$msg = sprintf('sql %s is not support.', $name);
			Core::raiseError($msg);
		}

		return $extensions;

	}

	/**
	 * DB ����̹��� ����
	 * @return GODO_DB_driver
	 */
	private function getDriver() {

		if ($this->driver instanceof GODO_DB_driver === false) {
			Core::raiseError('DB ����̹� �ν��Ͻ��� �������� �ʾҽ��ϴ�.');
		}
		else
			return $this->driver;
	}


	/**
	 * DB ������ �߰� (���� �߰� ������ master)
 	 * @param string $hostname	hostname
	 * @param string $username username
	 * @param string $password password
	 * @param string $database database name
	 * @param string $charset [optional] database charset
	 * @return void
	 */
	public function addServer( $hostname, $username, $password, $database, $charset = 'euc-kr' ) {

		$server = array(
			'hostname'=>$hostname,
			'username'=>$username,
			'password'=>$password,
			'database'=>$database,
			'charset'=>$charset
		);

		$this->getDriver()->server( 'set', $server );

	}

	/**
	 * DB ����̹��� Utility �ν��Ͻ� ����
 	 * @return GODO_DB_utility
	 */
	public function utility() {

		return $this->getDriverExtenstion('utility');

	}

	/**
	 * �ε��� �ν��Ͻ� ����
 	 * @return GODO_DB_indexer
	 */
	public function indexer() {

		static $instance = null;

		if ($instance === null) {

			$instance = new GODO_DB_indexer($this);
		}

		return clone $instance;

	}

	/**
	 * DB ����̹��� GODO_DB_builder ����
 	 * @return GODO_DB_builder
	 */
	public function builder() {

		return $this->getDriverExtenstion('builder');

	}

	/**
	 * DB ����̹� �޼��� �ݹ�
 	 * @param string $name
	 * @param mixed $arguments
	 * @return mixed �� db Ŭ���� ��� or GODO_DB_statement
	 */
	public function __call($name, $arguments) {

		$driver_methods = get_class_methods($this->getDriver());

		if (in_array($name, $driver_methods )) {
			$result = call_user_func_array(array($this->getDriver(), $name), $arguments);
			return $result;

		}
		else {
			Core::raiseError($name.' �޼��带 �����Ͻÿ�.');
		}

	}

	/**
	 * GODO_DB_statement �ν��Ͻ��� �����Ͽ� ����
	 * @param string $statement [optional]
	 * @return GODO_DB_statement
	 */
	public function prepare($statement='') {

		if (!empty($statement)) {
			$this->stmt = & $this->getDriver()->prepare($statement);
			return $this->stmt;
		}
		else {
			Core::raiseError('SQL ���� �Է��ϼ���.');
		}

	}

	/**
	 * ������ ���ν����� �����ϰ� �� ����� ����
	 * @param string $name procedure name
	 * @return mixed
	 */
	public function procedure($name /* $1 ~ $n optional parameters */) {

		$args = func_get_args();

		unset($args[0]);	// $name ����

		return call_user_func_array(array($this->getVirtualProcedure($name), 'procedure'), $args);

	}

	/**
	 * ������ ���ν��� �ν��Ͻ��� �����Ͽ� ����
 	 * @param string $name procedure name
	 * @return GODO_DB_procedure
	 */
	private function getVirtualProcedure($name) {

		static $procedure = array();

		if (!isset($procedure[$name])) {

			if (!class_exists($name, false)) {	// autoload ������� ����
				$class_file = dirname(__FILE__).Core::DS.'procedure'.Core::DS.$name.'.php';

				if (is_file($class_file))
					include_once($class_file);
				else
					Core::raiseError('create '.$name);

			}

			$procedure[$name] = new $name($this);
		}

		return $procedure[$name];

	}


	/**
	 * placeholder �� ä���� ���ڿ��� ����
	 * @param mixed $statement GDO_expression or string
	 * @param mixed $values string or array
	 * @return string
	 */
	public function fillReplaceHolder( $statement, $values = null) {

		if ($statement instanceof GODO_DB_expression) {
			return $statement->getExpression();
		}

		if ( !is_array( $values ))
			$values = array( $values );

		$_keys = array_keys( $values );

		$_questionmark_types = 0;
		$_namebased_type = 0;

		foreach ( $_keys as $_key ) {
			if ( is_numeric( $_key ))
				$_questionmark_types++;	//	?
			else
				$_namebased_type++;		// :name
		}

		// ? type placeholders.
		preg_match_all( '/\?/', $statement, $matches, PREG_SET_ORDER );
		if ( ( $_holdersize = sizeof( $matches ) ) > 0)
			if ( $_holdersize != $_questionmark_types ) {
				$msg = array(
					'prepared statement\'s placeholder coundn\'t empty.',
					'',
					$statement
				);
			}

		// name based placeholders.
		preg_match_all( '/:[a-zA-Z_]{1}[a-zA-Z0-9_]+/', $statement, $matches, PREG_SET_ORDER );
		if ( ( $_holdersize = sizeof( $matches ) ) > 0)
			if ( $_holdersize != $_namebased_type ) {
				$msg = array(
					'prepared statement\'s placeholder coundn\'t empty.',
					'',
					$statement
				);
			}

		$_replaceFr = array();
		$_replaceTo = array();

		if ( ! empty( $values ) ) {

			ksort( $values );

			foreach ( $values as $_holder=>$_value ) {

				if ( is_array( $_value ) ) {

					$_tmp_value = array();

					foreach ( $_value as $_v ) {

						$_v = ($_v instanceof GODO_DB_expression) ? $_v->getExpression() : $this->quote( $_v );
						$_tmp_value[$_v] = true;
					}

					$_value = sprintf( '%s', implode( ', ', array_keys( $_tmp_value ) ) );

				}
				else if (is_null($_value)) {
					$_value = 'null';
				}
				else {
					$_value = ($_value instanceof GODO_DB_expression) ? $_value->getExpression() : $this->quote( $_value );
				}

				// ����! preg_replace �� \ �� \\ �� ġȯ�ؾ� ��
				$_value = str_replace('\\','\\\\',$_value);

				if ( is_numeric( $_holder ) ) {
					$_replaceFr[$_holder - 1] = '/\?/';
					$_replaceTo[$_holder - 1] = $_value;
				}
				else {
					$_replaceFr[$_holder] = '/:' . $_holder . '/';
					$_replaceTo[$_holder] = $_value;
				}
			}
		}

		$replaced_statement = preg_replace( $_replaceFr, $_replaceTo, $statement, 1 );

		return $replaced_statement;
	}

	/**
	 * GODO_DB_builder ���� (insert)
	 * @param mixed $table string or array
	 * @return GODO_DB_builder
	 */
	public function insert($table) {
		$builder = $this->builder()->insert();
		$builder->into($table);
		return $builder;
	}

	/**
	 * GODO_DB_builder ���� (update)
	 * @param mixed $table string or array
	 * @return GODO_DB_builder
	 */
	public function update($table) {
		$builder = $this->builder()->update();
		$builder->from($table);
		return $builder;
	}

	/**
	 * GODO_DB_builder ���� (delete)
	 * @param mixed $table string or array
	 * @return GODO_DB_builder
	 */
	public function delete($table) {
		$builder = $this->builder()->delete();
		$builder->from($table);
		return $builder;
	}

	/**
	 * GODO_DB_builder ���� (select)
	 * @param mixed $table string or array
	 * @param mixed $columns [optional] string or array
	 * @return GODO_DB_builder
	 */
	public function select($table, $columns='') {
		$builder = $this->builder()->select();
		$builder->from($table);
		if ($columns) {
			if (!is_array($columns)) $columns = array($columns);
			$builder->reset('column')->columns($columns);
		}
		return $builder;
	}

	/**
	 * ����ǥ�� ����, escape �� ���ڿ��� ����
	 * @param string $str
	 * @return string
	 */
	public function quote($str) {

		if (is_null($str)) return 'null';

		$str = $this->getDriver()->escape($str);

		return "'".$str."'";

	}

	/**
	 * quote ��Ű�� ���� ���� �Է�
 	 * @param string $str
	 * @param mixed $value [optional]
	 * @return GODO_DB_expression
	 */
	public function expression( $str , $value=null) {

		$str = $value != null ? $this->fillReplaceHolder($str, $value) : $str;

		$expr = new GODO_DB_expression;
		$expr->setExpression($str);

		return $expr;

	}

	/**
	 * ���ǵ� sql �Լ��� ���
 	 * @param string $func
	 * @param mixed $value [optional]
	 * @return GODO_DB_expression
	 */
	public function func($func) {

		$instance = $this->getDriverExtenstion('function');

		$argv = func_get_args();
		$func = strtoupper(array_shift($argv));
		$str  = call_user_func_array( array($instance, $func), $argv );

		$expr = new GODO_DB_expression;
		$expr->setExpression($str);

		return $expr;

	}

	/**
	 * �Է� ���ڿ� ���ϵ�ī��(%) ���ڸ� �ٿ� ����
	 * @param string $str
	 * @param integer $pad [optional] 0 : ����, 1 : ������, 2 : ����
	 * @return string
	 */
	public function wildcard( $str , $pad = 0) {

		switch ($pad) {
			case 1:	// right
				$format = '%s%%';
				break;
			case 2:	// left
				$format = '%%%s';
				break;
			default://both
				$format = '%%%s%%';
				break;
		}

		$str = $this->getDriver()->escape($str);
		return sprintf($format,$str);

	}


    /**
     * ���� ��� ������ ���� ����, ������ ������� �ʴ� ���·� ����
     * @param mixed $bool [optional] null : ���� ���� ������ ����, true : ����, false : ����
     * @return void
     */
	public function silent($bool = null) {

		static $silent = null;

		if ($this->driver instanceof GODO_DB_driver) {

			if (is_bool($bool)) {

				if ($silent === null) {
					$silent = $this->driver->silent;
				}

				$this->driver->silent = $bool;

			}
			else if ($silent !== null && $bool === null) {
				$this->driver->silent = $silent;
			}

		}



	}


}
?>
