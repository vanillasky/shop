<?php
/**
 * Clib_Resource_Goods_Option_Stock_History
 * @author extacy @ godosoft development team.
 */
class Clib_Resource_Member_Group extends Clib_Resource_Abstract
{
	/**
	 * ��޺� ȸ�� ���� ����
	 * @param integer $level
	 * @return integer
	 */
	public function getMemberCountByLevel($level)
	{
		$query = "select count(*) from gd_member where level = '$level'";
		list($count) = $this->db->fetch($query);
		return $count;
	}

	public function loadByLevel($object, $level, $columns = null)
	{
		$alias = Clib_Application::getAlias($object->getClassName());
		$format = sprintf('`%s`.level = ?', $alias);

		$select = $this->getSqlBuilder();
		$select->from(array($alias => $object->getTableName()));
		$select->where($format, $level);

		// ���� �÷�
		if ( ! is_null($columns)) {
			if ( ! is_array($columns)) {
				$columns = array($columns);
			}

			if ( ! in_array($object->getIdColumnName(), $columns)) {
				array_push($columns, $object->getIdColumnName());
			}

			$select->reset('column')->columns($columns);
		}

		foreach ($this->getFilter() as $filter) {
			$select->where($filter['column'], $filter['value'], $filter['chain']);
		}

		if ($data = $select->fetch(1)) {
			$object->setData($data);
			$object->setOriginalData();

			$object->setLoaded(true);
			$object->setChanged(false);
		}
		else {
			$object->resetData();
		}

		return $object;

	}

}
