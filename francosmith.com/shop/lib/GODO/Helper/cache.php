<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */

/**
 * GODO_helper_cache
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_cache extends GODO_helper {

	/**
	 * ĳ�� ���
	 * @var string
	 */
	private $cache_path;

	/**
	 * ĳ�� ��θ� ���� (���� ��� ����)
     * @param mixed $sub_path ���� ���丮
	 * @return void
	 */
	public function __construct($sub_path = false) {

		$this->cache_path = G_CONST_DOCROOT . Core::DS . 'cache';

		if ($sub_path) {
			$this->cache_path .= Core::DS . $sub_path;
		}

		if (!is_dir($this->cache_path)) {
			@mkdir($this->cache_path, 0707);
            @chmod($this->cache_path, 0707);
		}

	}

	/**
	 * ĳ�� �̸��� ����
	 * @param string $name
	 * @return string
	 */
	private function getCacheName($name) {
		return md5($name);
	}

	/**
	 * ĳ�ø� �ҷ���
	 * @param string $name	ĳ�� �̸�
	 * @param integer $expire [optional]	����ð�
	 * @return string	����
	 */
	public function get($name, $expire=300) {	// �⺻�� 5��

		$_name = $this->getCacheName($name);
		$_file = $this->cache_path . Core::DS . $_name;

		$fh = Core::helper('File');

		return
			  $fh->mtime($_file) + $expire > G_CONST_NOW
			? $fh->get($_file)
			: false;

	}

	/**
	 * ĳ�ø� ����
	 * @param string $name	ĳ���̸�
	 * @param string $contents [optional]	����
	 * @return boolean
	 */
	public function set($name, $contents = '') {

		$_name = $this->getCacheName($name);
		$_file = $this->cache_path . Core::DS . $_name;

		$fh = Core::helper('File');

		return
			  $contents
			? $fh->set($_file, $contents, 0707)
			: false;

	}

}
?>
