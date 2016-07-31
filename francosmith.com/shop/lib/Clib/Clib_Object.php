<?php
/**
 * Clib_Object
 * Clib ���Ͽ� ��������� ��� Ŭ������ �ֻ��� Ŭ����
 * Ŭ�������̺귯�� ������Ʈ�� �⺻������ ������ �ϴ� �Լ��� �����Ѵ�.
 * @author khs @ godosoft development team.
 * @author extacy @ godosoft development team.
 */
class Clib_Object implements ArrayAccess, IteratorAggregate, Countable
{
	/**
	 * ��Ƶ� ������
	 * @var array
	 */
	private $_data = array();

	/**
	 * �� ���ǵ� property
	 * @var array
	 */
	private $_store = array();

	/**
	 * ��Ƶ� �������� ����.
	 * ������Ʈ Ȥ�� ó�� ���� �߰��� �����Ͱ� ����Ǿ����� Ȯ���ϱ� ���� ����Ѵ�
	 * @var array
	 */
	private $_originalData = array();

	/**
	 * DB ���� Resource �� ����ȭ(load or save) ����
	 * @var boolean
	 */
	private $_hasLoaded = false;

	/**
	 * DB ���� Resource �� ����ȭ ����, �������� ������ �־����� ����
	 * @var boolean
	 */
	private $_hasChanged = false;

	/**
	 * Ŭ������ �ø�������� ��Ų��.
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this);
	}

	/**
	 * Ŭ�����̸��� �����Ѵ�.
	 * @return string Ŭ���� �̸�
	 */
	public function getClassName()
	{
		return get_class($this);
	}

	/**
	 * ȯ�� ���� ������ �ҷ����δ�
	 * @param string $filename
	 * @return void
	 */
	public function includeConfig($filename)
	{
		include "../../conf/" . $filename;
	}

	/**
	 * ����(clone) ���� �޼���
	 * @return object
	 */
	public function __clone()
	{
		return $this;
	}

	/**
	 * ���� �����Ϳ� ������ ���� �޼���
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	public function __call($name, $value)
	{
		/*
		 * �Ʒ��� ���� Ȱ�� ����
		 * �÷����� db �÷����̸� ���ַ̹꿡 ���� ��ҹ��ڸ� �����Ͽ� ����Ѵ�
		 * ��, �÷��� ���� �߰� ó���� �ʿ��� ���, �ش� �𵨿��� �޼��� override �Ͽ� ����Ѵ�
		 *
		 * $model->get�÷���()   : �÷��� �ش��ϴ� �� or �������̵�� ����� ���� ����
		 * $model->set�÷���(��) : �÷��� ���� ����
		 * $model->has�÷���()   : �÷��� �ش� �Ǵ� ���� �����Ǿ� �ִ��� üũ
		 * $model->del�÷���()   : �÷����� ����
		 */
		if (preg_match('/^(get|set|has|del)([a-zA-Z_0-9]+)$/', $name, $matches)) {

			$operate = $matches[1];
			$columnName = $this->_camelCaseToLowerCaseWithUnderscore($matches[2]);

			switch ($operate) {
				case 'get' :
					return $this[$columnName];
					break;
				case 'set' :
					$value = $value[0];
					$this[$columnName] = $value;
					break;
				case 'has' :
					return isset($this[$columnName]);
					break;
				case 'del' :
					unset($this[$columnName]);
					break;
			}

			return $this;
		}
		else {
			// �� ��.
			printf('Undefined Method "%s" in %s', $name, get_class($this));
		}

		return false;
	}

	public function __isset($name)
	{
		return isset($this->_store[$name]);
	}

	/**
	 * ���ǵ��� ���� Property �� ������ ���� ����
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->$name)) {
			return $this->_store[$name];
		}
		else {
			// trigger_error or return null
			return null;
		}
	}

	/**
	 * ���ǵ��� ���� Property �� ���� ����
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Object
	 */
	public function __set($name, $value)
	{
		$this->_store[$name] = $value;
		return $this;
	}

	/**
	 * ���ǵ��� ���� Property �� ����
	 * @param string $name
	 * @return void
	 */
	public function __unset($name)
	{
		unset($this->_store[$name]);
	}

	/**
	 * CamelCase �� camel_case ���·� ��ȯ�Ͽ� ����
	 * @param string $name
	 * @return string
	 */
	private function _camelCaseToLowerCaseWithUnderscore($name)
	{
		$rename = '';

		for ($i = 0, $m = strlen($name); $i < $m; $i++) {

			$char = strtolower($name[$i]);
			$rename .= ($char != '_' && $i > 0 && $char != $name[$i]) ? '_' . $char : $char;
		}

		return $rename;

	}

	/**
	 * ������ �ε� ���θ� ����
	 * @return boolean
	 */
	public function hasLoaded()
	{
		return $this->_hasLoaded;
	}

	/**
	 * ������ ���� ���θ� ����
	 * @return boolean
	 */
	public function hasChanged()
	{
		return $this->_hasChanged;
	}

	/**
	 * ������ ���� ���θ� ����
	 * @param boolean $bool
	 * @return Clib_Object
	 */
	public function setChanged($bool)
	{
		$this->_hasChanged = (bool)$bool;
		return $this;
	}

	/**
	 * ������ �ε� ���θ� ����
	 * @param boolean $bool
	 * @return Clib_Object
	 */
	public function setLoaded($bool)
	{
		$this->_hasLoaded = (bool)$bool;
		return $this;
	}

	/**
	 * ���� �����͸� ����
	 * @param string $key
	 * @param mixed $data
	 * @return Clib_Object
	 */
	public function setOriginalData($key = null, $value = null)
	{
		if (is_null($key)) {
			$this->_originalData = $this->_data;
		}
		else {
			$this->_originalData[$key] = $value;
		}

		return $this;
	}

	/**
	 * ������ �ʱ�ȭ
	 * @return Clib_Object
	 */
	public function resetData()
	{
		$this->setData(array());
		$this->setOriginalData();

		$this->setChanged(false);
		$this->setLoaded(false);

		return $this;
	}

	/**
	 * ���� �����͸� ����
	 * @param string|array|object $key
	 * @param mixed $value
	 * @return Clib_Object
	 */
	public function setData($key, $value = null)
	{
		if (is_array($key)) {
			$this->_data = $key;
		}
		else if ($key instanceof self) {
			$this->_data = $key->toArray();
		}
		else if ( ! is_null($key)) {
			$this->_data[$key] = $value;
			// or $this[$key] = $value;
		}
		else {
			// nothing to do.
		}

		$this->setChanged(true);

		return $this;
	}

	/**
	 * ���� �����͸� �߰�
	 * @param string|array|object $key
	 * @param mixed $value
	 * @return Clib_Object
	 */
	public function addData($key, $value = null)
	{

		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->setData($k, $v);
			}
		}
		else if ($key instanceof self) {
			foreach ($key->toArray() as $k => $v) {
				$this->setData($k, $v);
			}
		}
		else if ( ! is_null($key)) {
			$this->setData($key, $value);
		}
		else {
			// nothing to do.
		}

		return $this;
	}

	/**
	 * ���� �����͸� ����
	 * @return mixed ���� ������
	 */
	public function getData($key = null)
	{
		if (is_null($key)) {

			$array = array();

			if ( ! empty($this->_data)) {
				foreach ($this->_data as $k => $v) {
					if ($v instanceof Clib_Model_Abstract) {
						$array[$k] = $v->getData();
					}
					else {
						$array[$k] = $v;
					}
				}
			}

			return $array;

		}
		else {
			return $this[$key];
		}

	}

	/**
	 * �Է� Ű�� ���� �ϴ��� ����
	 * @param string $key
	 * @return boolean
	 */
	public function hasData($key)
	{
		return isset($this[$key]);
	}

	/**
	 * ����� �����͸� ����
	 * @return array
	 */
	public function getChangedData()
	{
		$data = array();

		if ($this->hasLoaded()) {
			foreach ($this->_data as $k => $v) {
				if ((string)$v !== (string)$this->_originalData[$k]) {
					$data[$k] = $v;
				}
			}
		}
		else {
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * ���� ��ü�� hash �� ��´�
	 * @return string
	 */
	public function getHash()
	{
		return spl_object_hash($this);
	}

	/**
	 * Whether a offset exists
	 * @param string $offset An offset to check for
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Offset to retrieve
	 * @param string $offset The offset to retrieve.
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}

	/**
	 * Offset to set
	 * @param string $offset The offset to assign the value to
	 * @param mixed $value The value to set
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}

	/**
	 * Offset to unset
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}

	/**
	 * data Property convert to json
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this->_data);
	}

	/**
	 *
	 * @return
	 */
	public function toArray()
	{
		return (array)$this->_data;
	}

	/**
	 * Retrieve an external iterator
	 * @return
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->_data);
	}

	/**
	 * Count elements of an object
	 * @return
	 */
	public function count()
	{
		return count($this->_data);
	}

}
