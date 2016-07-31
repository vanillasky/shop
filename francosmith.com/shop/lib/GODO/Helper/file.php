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
 * GODO_helper_file
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_file extends GODO_helper {

	/**
	 * ������ ���� ���� �ð��� ����
	 * @param string $path
	 * @return int
	 */
	public function mtime($path) {

		clearstatcache();

		return (is_file($path)) ? filemtime($path) : 0;

	}

	/**
	 * ������ ������ ����
	 * @param string $path �ҷ��� ���� ���
	 * @return mixed
	 */
	public function get($path) {

		$contents = '';

		if ($fh = @fopen($path, 'r')) {
			flock($fh, LOCK_SH);
			if (filesize($path) > 0) $contents = fread($fh, filesize($path));
			flock($fh, LOCK_UN);
			fclose($fh);
		}
		else {
			return false;
		}

		return $contents;

	}

	/**
	 * ���Ͽ� ������ ���
	 * @param string $path ������ ���� ���
	 * @param string $contents ���� ����
	 * @param integer $permission [optional] ������ �۹̼�
	 * @return
	 */
	public function set($path, $contents, $permission=0707) {

		if ($fh = @fopen($path, 'w')) {

			flock($fh, LOCK_EX);
			fwrite($fh, $contents);
			flock($fh, LOCK_UN);
			fclose($fh);
			@chmod($path, $permission);

			return true;
		}
		else {
			return false;
		}

	}

	/**
	 * ���� ����
	 * @param string $path ������ ���� ���
	 * @return boolean
	 */
	public function del($path) {

		if (is_file($path)) {
			return @unlink($path);
		}
		else if (is_dir($path)) {
			return @rmdir($path);
		}
		else {
			return false;
		}

	}

	/**
	 * ������ mime Ÿ���� ����
	 * @param string $path
	 * @return string mime Ÿ��
	 */
	public function mime($path) {

		$mime = '';

		if (is_file($path)) {

			if (function_exists('finfo_file')) {
				// http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
				$fo = finfo_open(FILEINFO_MIME);
				list($mime) = explode('; ', finfo_file($fo, $path));
				finfo_close($fo);

			} else if (function_exists('mime_content_type')) {
				$mime = mime_content_type($path);
			}
			else {
				$mime = 'unknown';
			}

		}

		return $mime;

	}

}
?>
