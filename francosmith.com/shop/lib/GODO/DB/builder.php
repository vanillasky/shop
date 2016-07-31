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
 * GODO_DB_builder
 *
 * SQL���� ����
 *
 * ��� ����:
 *
 * <code>
 *  // SQL�� ����
 *  $builder = $db->builder();
 *  echo $builder->select()->from(GD_MEMBER)->where("m_id = ?", 'blue')->toString();
 *
 *  // ���ڵ� ��ȸ
 *  $builder = $db->builder()->select();
 *  $builder->from(GD_MEMBER);
 *  $builder->order('regdt');
 *  $builder->limit(5);
 *  $res = $builder->query();
 * </code>
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
abstract class GODO_DB_builder {

	/**
	 * �÷��� ���δ� ����ǥ `
	 * @var string
	 */
	protected $backtic = '`';

	/**
	 * ������ SQL���� ���� ������
	 * @var array
	 */
	protected $scheme = array();

	/**
	 * ������ SQL���� ����
	 * @var string
	 */
	protected $operate = null;

	/**
	 * GODO_DB
	 * @var object
	 */
	protected $db;

	/**
	 * Construct
	 * @param GODO_DB
 	 * @return void
	 */
	public function __construct(&$db) {
		$this->db = $db;
	}

	/**
	 * �ν��Ͻ� ����
	 * @return GODO_DB_builder
	 */
	public function __clone() {

		return $this;

	}

	/**
	 * string ĳ���ý� ������ �������� �����Ͽ� ����
	 * @return string sql query string
	 */
	public function __toString() {
		return (string)$this->generate();
	}

	/**
	 * join, *join, select, update, insert, delete, replace, GODO_DB �޼��� �ݹ�
	 * @param string $name
	 * @param mixed $arguments
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {

		// join
		if ( preg_match( '/^([a-zA-Z]{0,})(join)$/', $name, $matches ) ) {

			array_unshift(
				$arguments,
				! empty( $matches[1] ) ? $matches[1] : 'inner'
			);

			return
				call_user_func_array( array($this, '_join'), $arguments );
		}
		// operate
		else if (in_array(strtolower($name), array('select','update','insert','delete','replace'))) {

			$builder = $this->_getThis(strtolower($name));

			if (sizeof($arguments) > 0) {
				foreach($arguments as $option) {
					$builder->option($option);
				}
			}

			return $builder;

		}
		// db method
		else {

			if (!in_array(strtolower($name), array('wildcard','func','expression','quote'))) {
				array_unshift(
					$arguments,
					$this->toString()
				);
			}

			return call_user_func_array( array( $this->db, $name), $arguments );

		}

	}

	/**
	 * ������ SQL�� ������ ����
	 * @param string $operate
	 * @return void
	 */
	private function _setOperate( $operate ) {
		$this->operate = strtoupper($operate);
	}

	/**
	 * Builder�� ���� or �����Ͽ� ����
	 * @param string $operate
	 * @return GODO_DB_builder
	 */
	private function _getThis($operate) {

		static $_this = null;

		if ($_this === null) {
			$builder = $this;
		}
		else {
			$builder = clone $this;
		}

		$builder->_setOperate( $operate );

		return $builder;

	}

	/**
	 * JOIN ������ ����� ������ ����
	 * @param string $join_type
	 * @param string $table
	 * @param string $condition
	 * @param mixed $columns [optional]
	 * @return GODO_DB_builder
	 */
	protected function _join( $join_type, $table, $condition, $columns = '*' ) {

		$table_alias = $this->_setTable( $table, $join_type, $condition );
		$this->_setColumn( $columns, $table_alias );

		return $this;

	}

	/**
	 * SQL ���� ����� ���̺� ����
	 * @param string $table
	 * @param string $join_type [optional]
	 * @param string $join_condition [optional]
	 * @return string ���̺� ��Ī (����� ��쿡��)
	 */
	protected function _setTable( $table, $join_type = false, $join_condition = false ) {

		if ( !is_array( $table ))
			$table = array(	$table 	);

		if ( sizeof( $table ) > 1)
			Core::raiseError( '�Ѱ��� ��' );

		$_tmp = array_keys( $table );
		$alias = array_shift( $_tmp );

		$_tmp = array_values( $table );
		$table = array_shift( $_tmp );

		unset($_tmp);

		$alias = !is_numeric( $alias ) ? $alias : '';

		if ( $join_type ) {

			switch ( strtoupper( $join_type ) ) {
				case 'INNER' :
					$format = 'INNER JOIN %s ON %s';
					break;
				case 'LEFT' :
					$format = 'LEFT JOIN %s ON %s';
					break;
				case 'STRAIGHT' :
					$format = 'STRAIGHT_JOIN %s ON %s';	// straight_join Ű���带 �̿��� join �� ����. ���ƻ� ���� ���� �ϱ� ���� ����.
					break;
				case 'NATURAL' :
					$format = 'NATURAL JOIN %s ON %s';
					break;
				case 'RIGHT' :
					$format = 'RIGHT JOIN %s ON %s';
					break;
				case 'CROSS' :
					$format = 'CROSS JOIN %s ON %s';
					break;
				default :
					Core::raiseError( $join_type . ' join type does not support.' );
					break;
			}

			if ($alias)
				$this->scheme['join'][$alias] = array(
					'type'=>$join_type,
					'condition'=>$join_condition,
					'format'=>$format
				);
			else
				$this->scheme['join'][] = array(
					'type'=>$join_type,
					'condition'=>$join_condition,
					'format'=>$format
				);

		}

		if ($alias)
			$this->scheme['table'][$alias] = $table;
		else
			$this->scheme['table'][] = $table;

		return $alias;

	}

	/**
	 * ORDER ������ ����� ������ ����
	 * @param mixed $orders
	 * @return void
	 */
	protected function _setOrder( $orders ) {

		if ( !is_array( $orders ))
			$orders = array($orders);

		foreach($orders as $order) {
			$this->scheme['order'][] = is_array( $order ) ? implode(',',$order) : $order;
		}

	}

	/**
	 * GROUP ������ ����� ������ ����
	 * @param string $groups
	 * @return void
	 */
	protected function _setGroup( $groups ) {

		if ( !is_array( $groups ))
			$groups = array($groups);

		foreach($groups as $group) {
			$_group = is_array( $group ) ? implode(',', $group) : $group;

			if (!in_array($_group, $this->scheme['group'])) {
				$this->scheme['group'][] = $_group;
			}
		}

	}

	/**
	 * LIMIT (limit, top �� driver ���� �ٸ�) ������ ����� ������ ����
	 * @param string $offset [optional]
	 * @param string $row_count
	 * @return void
	 */
	protected function _setLimit( $offset = 0, $row_count ) {

		$this->scheme['limit']['offset'] = $offset;
		$this->scheme['limit']['row_count'] = $row_count;

	}

	/**
	 * HAVING ������ ����� ������ ����
	 * @param string $condition
	 * @param mixed $value
	 * @param string $chain [optional]
	 * @return void
	 */
	protected function _setHaving( $condition, $value, $chain = 'and' ) {

		$this->scheme['having'][] = array(
			'condition'=>$condition,
			'value'=>$value,
			'chain'=>$chain
		);

	}

	/**
	 * WHERE ������ ����� ������ ����
	 * @param string $condition
	 * @param mixed $value
	 * @param string $chain [optional]
	 * @return void
	 */
	protected function _setWhere( $condition, $value, $chain = 'and' ) {

		$this->scheme['where'][] = array(
			'condition'=>$condition,
			'value'=>$value,
			'chain'=>$chain
		);

	}

	/**
	 * columns ���� ������ �����͸� ����
	 * @param string $values
	 * @return void
	 */
	protected function _setValues($values) {

		if ( !is_array( $values ))
			$values = array( $values );

		foreach($values as $column => $value) {

			if ( !is_int($column) ) {
				$column = $this->backticQuote($column);
				$this->scheme['value'][$column] = $value;
			}
			else {
				$this->scheme['value'][] = $value;
			}
		}

	}

	/**
	 * COLUMNS ������ ����� ������ ����
	 * @param string $columns
	 * @param string [optional] $table_alias
	 * @return void
	 */
	protected function _setColumn( $columns, $table_alias = '') {

		if (empty($columns)) return;

		if ( !is_array( $columns ))
			$columns = array( $columns );

		foreach ( $columns as $alias => $column ) {

			if ( $column instanceof GODO_DB_expression ) {
				$column = $column->getExpression();
			}
			else if ( strpos( $column, '.' ) !== false || empty( $table_alias ) ) {
				// nothing to do
			}
			else {
				$column = sprintf( '%s.%s', $table_alias, $column );
			}

			$column = $this->backticQuote($column);

			if ( is_integer( $alias ) ) {
				$this->scheme['column'][] = $column;
			}
			else {
				$this->scheme['column'][$alias] = $column;
			}

		}

	}

	/**
	 * �÷��� quote �Ͽ� ����
	 * �÷� ���� ����ؾ� ��
	 * @param string $var
	 * @return string
	 */
	public function backticQuote($var) {

		if (
			   empty($var)
			|| $this->backtic == ''
			|| strpos($var, '(') !== false
			|| strpos($var, ' ') !== false
			|| strpos($var, '*') !== false
		) return $var;

		if (strpos($var, '.') !== false) {
			$var = str_replace('.', '.'.$this->backtic, $var) . $this->backtic;
		}
		else {
			$var = $this->backtic . $var . $this->backtic;
		}

		return $var;

	}

	/**
	 * __toString �޼��� ����� ����
 	 * @return string
	 */
	public function toString() {
		return (string)$this->generate();
	}

	/**
	 * from �� ����
 	 * @param mixed $table table name
	 * @param mixed $columns [optional] column name
	 * @return GODO_DB_builder
	 */
	public function from( $table, $columns = '*' ) {

		$table_alias = $this->_setTable( $table );
		$this->_setColumn( $columns, $table_alias );

		return $this;

	}

	/**
	 * into �� ����
	 * @param string $table
	 * @param string $columns [optional]
	 * @return GODO_DB_builder
	 */
	public function into($table, $columns = null) {

		$table_alias = $this->_setTable( $table );
		$this->_setColumn( $columns, $table_alias );

		return $this;
	}

	/**
	 * set column clause
 	 * @param string $columns
	 * @param string $table_alias [optional]
	 * @return GODO_DB_builder
	 */
	public function columns( $columns, $table_alias = '' ) {

		$this->_setColumn( $columns, $table_alias );

		return $this;
	}

	/**
	 * set where clause
 	 * @param string $condition	 condition
	 * @param string $value [optional]
	 * @param string $chain [optional] and �Ǵ� or
	 * @return GODO_DB_builder
	 */
	public function where( $condition, $value = false, $chain = 'AND' ) {
		$this->_setWhere( $condition, $value, $chain );
		return $this;
	}

	/**
	 * $condition ���ڿ� ���� placeholder �� $value �� ä�� ����
	 * @param string $condition
	 * @param mixed $value
	 * @return string
	 */
	public function parse($condition, $value) {
		return $this->db->fillReplaceHolder($condition, $value);
	}

	/**
	 * group �� ����
 	 * @param string $columns group ����
	 * @return GODO_DB_builder
	 */
	public function group( $columns ) {
		$this->_setGroup( $columns );
		return $this;
	}

	/**
	 * order �� ����
 	 * @param mixed $columns order ����
	 * @return GODO_DB_builder
	 */
	public function order( $columns ) {
		$this->_setOrder( $columns );
		return $this;
	}

	/**
	 * limit �� ����
 	 * @param integer $offset ������ ���� ���� ��ġ
	 * @param integer [optional] $row_count ������ ���� ��
	 * @return GODO_DB_builder
	 */
	public function limit( $offset, $row_count = null) {
		$this->_setLimit( $offset, $row_count );
		return $this;
	}

	/**
	 * having �� ����
 	 * @param string $condition ����
	 * @param string $value [optional]
	 * @param string $chain [optional]
	 * @return GODO_DB_builder
	 */
	public function having( $condition, $value = false, $chain = 'AND' ) {

		$this->_setHaving( $condition, $value, $chain );

		return $this;

	}


	/**
	 * set �� ����
 	 * @param array $values
	 * @return GODO_DB_builder
	 */
	public function set($values) {

		$this->reset('column');
		$this->reset('values');

		$columns = array_keys($values);
		$values = array_values($values);

		$this->_setColumn($columns);
		$this->_setValues($values);

		return $this;

	}

	/**
	 * �� �÷��� ��Ī�� ���� ����
	 * @param mixed $values
	 * @return GODO_DB_builder
	 */
	public function values($values) {

		$this->_setValues($values);

		return $this;
	}

	/**
	 * Ư�� ������ �Ǵ� ��ü �����͸� ����
	 * @param string $specify [optional]
	 * @return GODO_DB_builder
	 */
	public function reset($specify='') {

		if ($specify) {
			if (!empty($this->scheme[$specify])) {

				if ($specify == 'join') {
					$_keys = array_keys($this->scheme['table']);
					for ($i=0,$m=sizeof($_keys);$i<$m;$i++) {
						$_key = $_keys[$i];

						if (isset($this->scheme[$specify][$_key])) {
							unset( $this->scheme[$specify][$_key], $this->scheme['table'][$_key] );
						}
					}
				}
				else {
					$this->scheme[$specify] = array();
				}

			}
		}
		else {
			$this->scheme = array();
		}

		return $this;
	}

	/**
	 * option �� ����
	 * @param string $option
	 * @return GODO_DB_builder
	 */
	public function option($option) {
		$this->scheme['option'][] = strtoupper($option);

		return $this;
	}

	/**
	 * union �� ����
	 * @return GODO_DB_builder
	 */
	public function union() {

		if (func_num_args() < 2) Core::raiseError('union must be 2 sql queries.');

		$this->_setOperate( 'union' );

		$this->reset();
		$this->scheme['query'] = func_get_args();

		return $this;

	}

	/**
	 * SQL ������ ������ ����
	 * @return string
	 */
	public function getOperate () {
		return strtoupper($this->operate);
	}

	/**
	 * $key �� �ش� ���� �����Ǿ� �ִ��� üũ
     * @param String $key
	 * @return boolean
	 */
	public function has($key) {

		return
			  sizeof($this->scheme[$key]) > 0
			? true
			: false;
	}

	/**
	 * �˻� �� ����� join ���� ����
	 * @param string $table
	 * @param string $pk
	 * @param array $columns
	 * @param string $keyword
	 * @return void
	 */
	public function search($table, $pk, $columns, $keyword) {

		static $scope = 0;

		$sc = $this->db->indexer();

		if ($q = $sc->search($table, $columns, $keyword)) {

			$scope++;

			$this->join(
				array('__search_'.$scope => sprintf('( %s )', $q))
				,sprintf('__search_%d.pk = %s', $scope, $pk)
			);

		}

	}

	/**
	 * on duplicate key update ���� ��� ���� ����
	 * @return GODO_DB_builder
	 */
	public function duplicateupdate() {

		$this->scheme['onduplicatekeyupdate'] = true;

		return $this;
	}

	/**
	 * SQL���� �����Ͽ� ����
	 * @return string SQL
	 */
	abstract protected function generate();

	/**
	 * limit (skip, top ��)�� ���� �Ͽ� ����
     * @param boolean $row_count_only
	 * @return string
	 */
	abstract protected function getSyntax_limit($row_count_only = false);

	/**
	 * having �� ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_having();

	/**
	 * order �� ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_order();

	/**
	 * return�� ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_group();

	/**
	 * column �� ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_column();

	/**
     * values �� �����Ͽ� ����
	 * @return string
	 */
	abstract protected function getValueString();

	/**
	 * table �� (, �� ����) ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_tableList();

	/**
	 * table �� ���� �Ͽ� ����
     * @param boolean $single_table
	 * @return string
	 */
	abstract protected function getSyntax_table($single_table = false);

	/**
	 * SET �� ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_where();

	/**
	 * on duplicate key update �� ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_onDuplicateKeyUpdate();

	/**
	 * SET �� ���� �Ͽ� ����
	 * @return string
	 */
	abstract protected function getSyntax_set();

	/**
	 * �ɼ� �� ���� �Ͽ� ����
	 * @param string $operate
	 * @return string
	 */
	abstract protected function getSyntax_option($operate);

}
?>
