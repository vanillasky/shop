<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Development
 */

/**
 * GODO_Development_syntax
 *
 * command line ���� ����
 *
 * ��� ���� :
 *
 * shell>php ./syntax.php ���丮
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Development
 */
class GODO_Development_syntax {

    /**
     * syntax �� üũ�� ���丮
     * @var string
     */
	private $root_dir = './';

    /**
     * syntax üũ ���
     * @var array
     */
	private $log = array();

    /**
     * syntax üũ�� ������ Ȯ����
     * @var array
     */
	private $exclude = array('.','..','.svn');


    /**
     * syntax �� üũ�� ���丮�� ����
     * @param string $path
     * @return void
     */
	public function setRootDir($path) {

		$this->root_dir = $path;
	}


    /**
     * _scan �޼��� ����
     * @param string $dir
     * @return void
     */
	public function scan($dir='') {
		if ($dir == '') $dir = $this->root_dir;
		$this->_scan($dir);
	}


    /**
     * ���ڷ� �Ѿ�� ���丮 �Ǵ�, ������ ���丮���� php ������ syntax üũ
     * @param string $path
     * @return void
     */
	private function _scan($path='') {

		if (is_dir($path)) {

			if (!preg_match('/\/$/',$path)) $path .= '/';

			$fl = scandir($path);
		}
		else if (is_file($path)) {
			$fl = array($path);
		}
		else {
			$fl = array();
		}

		foreach ($fl as $file) {

			if (in_array($file, $this->exclude)) continue;

			$_file = $path . $file;

			if (is_dir($_file)) {
				$this->_scan($_file);
			}
			else {
				$this->check($_file);
			}
		}
	}

    /**
     * PHP �������� üũ
     * @param string $path
     * @return boolean
     */
	private function isPHP($path) {
		$tmp = explode('.',$path);
		$ext = array_pop($tmp);

		return in_array($ext, array(
			'php','html','inc','php3'
		));
	}

    /**
     * php ������ syntax �� üũ�Ͽ� ����� ȭ�鿡 ���
     * @param $path
     * @return void
     */
	private function check($path) {

		if ($this->isPHP($path)) {

			$script = sprintf('php -l %s', $path);

			exec($script, $error, $code);

			if ($code !== 0) {
				echo $path.PHP_EOL;
			}

		}

	}

}



if (isset($argv[1]) && $argv[1] !== '')
	$root = $argv[1];
else
	exit('missing argument.');

set_time_limit(0);

$sc = new GODO_Development_syntax;
$sc->setRootDir($root);
$sc->scan();

?>