<?php
/**
 * Clib_Form_Element_Abstract
 * @author extacy @ godosoft development team.
 */
abstract class Clib_Form_Element_Abstract
{
	/**
	 * element's attributes
	 * @var array
	 */
	private $_attributes;

	/**
	 * element's validateOptions
	 * @var array
	 */
	private $_validations;

	/**
	 * Construct
	 * @return void
	 */
	public function __construct()
	{
		$this->_attributes = array();
	}

	/**
	 * ���� ����
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->getAttribute('_value');
	}

	/**
	 * �Ӽ��� ����
	 * @return string
	 */
	protected function getAttributeTag()
	{
		$pair = array();

		$arguments = func_get_args();
		$exclude = array_merge(array(
			'_value',
			'default_value',
			'checked_value',
		), $arguments);

		foreach ($this->getAttributes() as $k => $v) {
			if (in_array($k, $exclude))
				continue;
			if ($k == 'checked' && ! $v)
				continue;
			$pair[] = sprintf('%s="%s"', $k, str_replace('"', '\"', $v));
		}

		return implode(' ', $pair);
	}

	/**
	 * �Ӽ� �߰�
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Form_Element_Abstract
	 */
	public function addAttribute($name, $value)
	{
		$this->_attributes[$name] = $value;
		return $this;
	}

	/**
	 * �Ӽ� �߰�
	 * @param array $attributes
	 * @return Clib_Form_Element_Abstract
	 */
	public function addAttributes($attributes)
	{
		foreach ($attributes as $k => $v) {
			$this->addAttribute($k, $v);
		}

		return $this;
	}

	/**
	 * �Ӽ� ���� ���� ����
	 * @param string $name
	 * @return boolean
	 */
	public function hasAttribute($name)
	{
		return array_key_exists($name, $this->_attributes);
	}

	/**
	 * �����ϴ� �Ӽ��� ���� ����
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Form_Element_Abstract
	 */
	public function setAttribute($name, $value)
	{
		if ($this->hasAttribute($name)) {
			$this->addAttribute($name, $value);
		}

		return $this;
	}

	/**
	 * �Ӽ� ���� ����
	 * @param string $name
	 * @return mixed
	 */
	public function getAttribute($name)
	{
		return $this->_attributes[$name];
	}

	/**
	 * ��� �Ӽ��� ����
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}

	/**
	 * html �±׷� ��ȯ�� ���ڿ� ����
	 * @return string
	 */
	public function getTag()
	{
		if ( ! $this->hasAttribute('value')) {
			$this->addAttribute('value', $this->getValue());
		}
		else {

			$value = $this->getAttribute('value');

			if ($value instanceof Clib_Collection_Abstract || $value instanceof Clib_Model_Abstract) {
				$this->setAttribute('value', $value->toArray(array(
					$value,
					'getIdNamePair'
				)));
			}
		}

		return $this->getTagHtml();
	}

	/**
	 * html �±׷� ��ȯ�� ���ڿ� ����
	 * @return
	 */
	abstract public function getTagHtml();

	/**
	 * validation �߰�
	 * @return
	 */
	public function setValidateOption($validation)
	{
		foreach ($validation as $k => $v) {
			$this->addValidation($k, $v);
		}

		return $this;
	}

	/**
	 * validation �߰�
	 * @param string $name
	 * @param mixed $value
	 * @return Clib_Form_Element_Abstract
	 */
	public function addValidation($name, $value)
	{
		$this->_validations[$name] = $value;
		return $this;
	}

	/**
	 * validation ���� ����
	 * @param string $name
	 * @return mixed
	 */
	public function getValidations($name)
	{
		return $this->_validations;
	}

}
