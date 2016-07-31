<?php
/**
 * Clib_Model_Abstract
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Model_Abstract extends Clib_Object
{

	/**
	 * @var string
	 * ID �÷���
	 */
	protected $idColumnName = '';

	/**
	 * ORM ����
	 *
	 * ### �⺻ ����
	 *
	 * <code>
	 * protected $objectRelationMapping = array('goods_discount', 'goods_option');
	 * </code>
	 *
	 * ### ���� ����
	 *
	 * <code>
	 * protected $objectRelationMapping = array(
	 *     'discount' => array(
	 *         'modelName' => 'goods_discount',
	 *     ),
	 *     'options' => array(
	 *         'modelName' => 'goods_option',
	 *         'isCollection' => true,
	 *         'foreignColumn'=>'goodsno',
	 *     )
	 * );
	 * </code>
	 *
	 * ### ��� ������ ������
	 *
	 * - `modelName`: ���� �� �̸�
	 * - `isCollection`: �ݷ������� �������� ���� (many)
	 * - `foreignColumn`: ���� �� �÷�
	 * - `primaryColumn`: ���� �� �÷� �� ��Ī�� �� �� �÷�
	 *
	 * @var array
	 * @see Clib_Model_Abstract::getRelationShipConfig()
	 */
	protected $objectRelationMapping = array();

	/**
	 * ID �÷����� ����
	 * @return string
	 */
	final public function getIdColumnName()
	{
		if (empty($this->idColumnName)) {
			debug_print_backtrace();
			trigger_error("idColumnName �� ���� �� ����.", E_USER_ERROR);
		}
		return $this->idColumnName;
	}

	/**
	 * ID �÷����� ����
	 * @return string
	 */
	final public function setIdColumnName($column)
	{
		if ($column) {
			$this->idColumnName = $column;
		}

	}

	/**
	 *
	 * @return
	 */
	final public function setId($id)
	{
		$this->setData($this->getIdColumnName(), $id);
		return $this;
	}

	/**
	 *
	 * @return integer|string
	 */
	final public function getId()
	{
		return $this->getData($this->getIdColumnName());
	}

	/**
	 * {@inheritdoc}
	 */
	public function __get($name)
	{

		$ret = parent::__get($name);

		if ($config = $this->getRelationShip($name)) {

			// lazy load;
			if ( ! is_object($ret)) {
				$ret = Clib_Application::getModelClass($config['modelName']);
			}

			if ($this->hasLoaded() && ! $ret->hasLoaded()) {

				if ($config['isCollection']) {
					$ret = $ret->getCollection();
					$ret->setSingularize();
					$ret->addFilter($config['foreignColumn'], $this->getData($config['primaryColumn']));
					$ret->load();
				}
				else {

					$ret->setIdColumnName($config['foreignColumn']);
					$ret->load($this->getData($config['primaryColumn']));
				}
			}

			$this->$name = $ret;

		}

		return $ret;

	}

	/**
	 * Construct
	 * @return void
	 */
	final public function __construct()
	{
		$this->construct();
	}

	/**
	 * Construct
	 * @return void
	 */
	protected function construct()
	{
	}

	/**
	 * ������ ���̺���� ����
	 * @return string
	 */
	public function getTableName($prefix = 'gd')
	{
		$tableName = array_unique(explode('_', strtolower(Clib_Application::getClassLastName($this))));

		if ($prefix) {
			array_unshift($tableName, $prefix);
		}

		return implode('_', $tableName);

	}

	/**
	 * ������ ���̺��� �÷����� ����
	 * @return array
	 */
	public function getTableColumns()
	{
		if (property_exists($this, 'propertyMap')) {
			return $this->propertyMap;

		}
		else {

			$hash = spl_object_hash($this);

			static $cache = array();

			if ( ! isset($cache[$hash])) {
				$cache[$hash] = $this->getResource()->getColumns($this->getTableName());

			}

			return $cache[$hash];
		}
	}

	/**
	 *
	 * @return
	 */
	final protected function getResource()
	{
		return Clib_Application::getResourceClass(Clib_Application::getClassLastName($this));
	}

	/**
	 *
	 * @return
	 */
	public function createNew($id = null)
	{
		$this->resetData();
		return $this->create($id);
	}

	public function create($id = null)
	{
		if ( ! is_null($id)) {

			if (is_array($id)) {
				foreach ($id as $k => $v) {
					$this->setData($k, $v);
				}
			}
			else {
				$this->setId($id);
			}

		}

		return $this->getResource()->create($this);
	}

	function setRelatedObject()
	{
		if ($this->hasRelationShip()) {

			foreach ($this->getRelationShip() as $property => $config) {

				// for basic usage;
				if (is_int($property) && ! is_array($config)) {
					$property = $config;
					$config = array();
				}

				// create model;
				$config['modelName'] = $config['modelName'] ? $config['modelName'] : $property;
				$relatedObject = Clib_Application::getModelClass($config['modelName']);

				// reset config;
				$config = $this->getRelationShipConfig($relatedObject, $config);
				$this->setRelationShip($property, $config);

				// register related object;
				if ( ! isset($this->$property)) {
					$this->$property = $relatedObject;
				}
				else {
					// already registered;
				}
			}
		}
	}

	/**
	 *
	 * @return
	 */
	public function load($id, $columns = null)
	{
		if ($this->hasLoaded()) {
			return $this;
		}

		$this->setRelatedObject();

		$resource = $this->getResource();
		$resource->loadById($this, $id, $columns);

		//$this->setLoaded(true);
		//$this->setChanged(false);

		return $this;
	}

	public function loadBySql($sql)
	{
		if ($this->hasLoaded()) {
			return $this;
		}

		$resource = $this->getResource();
		$resource->loadBySql($this, $sql);

		//$this->setLoaded(true);
		//$this->setChanged(false);

		return $this;
	}

	/**
	 *
	 * @return $this
	 */
	public function save()
	{
		if ( ! $this->hasLoaded()) {
			return $this->create();
		}

		// save related objects;
		foreach ($this->getRelationShip() as $property => $config) {
			if ($config['updateCascade'] === true) {
				$this->$property->save();
			}
		}

		// save this;
		$resource = $this->getResource();
		$resource->save($this);
		$this->setOriginalData();
		return $this;

	}

	/**
	 *
	 * @return
	 */
	public function delete()
	{
		if ( ! $this->hasLoaded()) {
			return $this;
		}

		// delete related objects;
		foreach ($this->getRelationShip() as $property => $config) {
			if ($config['deleteCascade'] === true) {
				$this->$property->delete();
			}
		}

		// delete this;
		$resource = $this->getResource();
		$resource->delete($this);
	}

	/**
	 *
	 * @param object $form
	 * @return
	 */
	public function setForm($form)
	{
		return $this->setFormData($form);
	}

	/**
	 * ��obj�� �޾� ���� �����͸� ����
	 * @param object $form
	 * @return Clib_Object
	 */
	public function setFormData($form)
	{
		if ($form instanceof Clib_Form_Abstract) {
			return $this->setData($form->getValue());
		}
		else {
			return false;
		}
	}

	/**
	 * ���� Collection �� ����
	 * @return Clib_Collection
	 */
	public function getCollection()
	{
		$name = Clib_Application::getClassLastName($this);

		$collection = Clib_Application::getCollectionClass($name);
		$collection->setValueModel($this);
		return $collection;

	}

	/**
	 *
	 * @return boolean
	 */
	public function hasRelationShip()
	{
		return sizeof($this->objectRelationMapping) > 0 ? true : false;
	}

	/**
	 *
	 * @param string $name [optional]
	 * @return array()
	 */
	public function getRelationShip($name = null)
	{
		return ! is_null($name) ? $this->objectRelationMapping[$name] : $this->objectRelationMapping;

	}

	/**
	 *
	 * @param string $type
	 * @param array $config
	 * @return Clib_Model_Abstract
	 */
	public function setRelationShip($propertyName, $config)
	{
		$this->objectRelationMapping[$propertyName] = $config;
		return $this;

	}

	/**
	 *
	 * @param Clib_Model_Abstract|Clib_Collection_Abstract $relatedObject
	 * @param array $config
	 * @return
	 */
	public function getRelationShipConfig($relatedObject, $config)
	{
		return $config + array(
			'isCollection' => false, // collection ȭ ����
			'primaryColumn' => $this->getIdColumnName(), // ������ ����� ��� �𵨰� ��Ī�� �� ���� Ű �÷� (�������� �⺻ ID �÷����� ����)
			'foreignColumn' => $relatedObject->getIdColumnName(), // ������ ����� �� �𵨰� ��Ī�� ��� ���� Ű �÷� (�������� �⺻ ID �÷����� ����)
			'deleteCascade' => false, // �� �� ������, ��� �� ���� ������ ������ ����
			'updateCascade' => false, // �� �� ���Ž� ��� �� ���� ���� �Ұ����� ����
		);

	}

	/**
	 * {@inheritdoc}
	 */
	public function resetData()
	{
		// unset orm properties;
		if ($this->hasRelationShip()) {
			foreach (array_keys($this->getRelationShip()) as $property) {
				unset($this->$property);
			}
		}

		return parent::resetData();
	}

}
