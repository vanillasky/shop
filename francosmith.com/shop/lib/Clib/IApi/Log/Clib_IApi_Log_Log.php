<?php
/**
 * Logging class - file
 *
 * @author Clib_Logging.php, dn, leeys@godo.co.kr
 * @version 1.0
 * @date 2013-01-23
 *
 */
class Clib_IApi_Log_Log
{
	const LOG_DIR = '/log';

	/**
	 *
	 * @param string $log_type, $contents, $file_name = '', $delete_term = -1
	 * @return bool
	 */
	public function loggingFile($log_type, $contents, $file_name = '', $delete_term = -1)
	{
		// �α� ���丮 ����
		$tmp_path = explode('/', $log_type);
		$parent_path = _SHOP_ROOT_ . self::LOG_DIR;
		for ($i = 0; $i < count($tmp_path); $i++) {
			if ( ! is_dir($parent_path . '/' . $tmp_path[$i])) {
				mkdir($parent_path . '/' . $tmp_path[$i]);
			}
			$parent_path .= '/' . $tmp_path[$i];
		}

		$file_path = _SHOP_ROOT_ . self::LOG_DIR . '/' . implode('/', $tmp_path);

		// path ���� ���ϸ� �����
		if ($file_name == '') {
			$file_name = date('Ymd');
		}

		$tmp_file_name = explode('.', $file_name);

		// �α����� Ȯ���� ���̱� .log
		if ($tmp_file_name[count($tmp_file_name) - 1] != 'log') {
			$tmp_file_name[] = 'log';
		}

		$file_name = implode('.', $tmp_file_name);

		// ������ �Ⱓ ���� �α� ���� ����

		if ($delete_term > - 1) {
			$this->_deleteLogFile($file_path, $delete_term);
		}

		// ������ ������� �α� ������ �����
		$log_contents = $this->_makeLogContents($contents);

		$this->_fileWrite($file_path . '/' . $file_name, $log_contents);
	}

	/**
	 *
	 * @param string $contents
	 * @return string
	 */
	private function _makeLogContents($contents)
	{
		$log_date = date('Y-m-d H:i:s');
		$log_ip = $_SERVER['REMOTE_ADDR'];

		$log_contents = '-- ' . $log_date . ' -- ' . str_pad($log_ip, 15, ' ', STR_PAD_RIGHT) . ' --' . "\r\n";
		$log_contents .= $contents . "\r\n";
		$log_contents .= '--------------------------------------------' . "\r\n";

		return $log_contents;
	}

	/**
	 *
	 * @param int $term
	 * @return void
	 */
	private function _deleteLogFile($file_path, $term)
	{
		$dh = opendir($file_path);

		// ���� ��¥�� �α׸� ������ �ʱ����� �ð�,��,�� �� 0���� ����
		$now_date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

		while (($file = readdir($dh))) {

			// . �� .. ���丮�� ����
			if ($file == "." || $file == "..") {
				continue;
			}

			if (is_file($file_path . '/' . $file)) {
				// ������ ������ timestamp�� ���Ѵ�
				$file_date = filemtime($file_path . '/' . $file);

				// ���� �ð�����
				$diff = ceil(($now_date - $file_date) / 86400);

				if ($diff >= $term) {
					unlink($file_path . '/' . $file);
				}
			}
		}
	}

	/**
	 *
	 * @param string $file_name, $log_contents
	 * @return void
	 */
	private function _fileWrite($file_name, $log_contents)
	{
		if ( ! is_file($file_name)) {
			$fp = fopen($file_name, 'w');

			fclose($fp);
			chmod($file_name, 0707);
		}

		$fp = fopen($file_name, 'a');
		fwrite($fp, $log_contents);
		fclose($fp);
	}

}
?>
