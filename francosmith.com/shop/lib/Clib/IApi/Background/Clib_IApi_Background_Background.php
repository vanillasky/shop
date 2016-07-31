<?php
/*
 * @desc 	���޵� ��׶��� ���μ����� �����ϰų�, ���డ�ɿ��� üũ �� ������¸� ��ȸ�ϴ� ��� ���� Ŭ����
 * @author 	khs
 * @date 	2013-01-08
 * @todo   �ϴ��� �ѹ��� �ϳ��� ���μ����� �����ϰ� �ϰ�,  Queueing ó���� ������ ����Ѵ�.
 */

class Clib_IApi_Background_Background
{
	const BACKGROUND_SRC_PATH = '/../../../../admin/background/';
	const BACKGROUND_LOG_PATH = '/../../../../log/background/';

	private $_is_window = false;
	private $_pid = '';
	private $_exec_file = '';
	private $_exec_sh = '';
	private $_exec_start = '';
	private $_exec_id = '';
	private $_log_key = '';
	private $_exec_log = '';
	private $_limit_time = 300;

	public function __construct()
	{
	}

	public function doExec($command_name, $params)
	{

		$this->_exec_file = dirname(__FILE__) . self::BACKGROUND_SRC_PATH . 'adm_back_' . $command_name . '.php';
		$this->_exec_sh = dirname(__FILE__) . self::BACKGROUND_SRC_PATH . 'get_path.sh';
		$this->_exec_log = dirname(__FILE__) . self::BACKGROUND_LOG_PATH . 'ing_process.log';

		if ( ! $this->_checkExec()) {
			return false;
		}

		if ( ! is_array($params)) {
			return false;
		}
		/*
		 *
		 * * @todo �α� ���� üũ �� �α� ó��
		 */

		if ( ! $this->_checkIngProcess()) {
			return false;
		}

		$this->_log_key = $this->_getLogKey();
		$sess = Clib_Application::session()->get('sess');
		$this->_exec_id = $sess['m_id'];
		$this->_exec_start = date(YmdHis);

		if ( ! $this->_logWrite()) {
			return false;
		}

		// WINDOW üũ
		if (substr(php_uname(), 0, 7) == "Windows") {
			$this->_is_windows = true;
		}
		else {
			$this->_is_windows = false;
		}

		$str_args = base64_encode(serialize($params));

		if ($this->_is_windows) {

			$cmd = $this->path_executable . $this->exec_name;
			$WshShell = new COM("WScript.Shell");

			$oExec = $WshShell->Run('C:\\APM_Setup\\Server\\PHP5\\php.exe ' . $cmd . ' ' . $str_args . ' ' . $this->_log_key, 0, false);

			$this->_pid = $oExec->ProcessId;

			if ( ! $this->_pid) {
				$this->_pid = 'test01001';
			}
		}
		else {
			// �⺻ e���� ���� ���� ��η� �ϵ��ڵ�
			$php_path = '/usr/local/php/bin/php';

			$cmd = 'nohup ' . $php_path . ' -q ' . $this->_exec_file . ' ' . $str_args . ' ' . $this->_log_key . '  > /dev/null 2>&1 & echo $!';
			$this->_pid = shell_exec($cmd);
		}

		if ($this->_pid && $this->_log_key) {
			$this->_logUpdate();
		}

		return true;
	}

	private function _checkIngProcess()
	{

		$cnt_chk = 0;

		if ( ! is_file($this->_exec_log)) {
			// ������ ���� ��� ���� ���μ����� ���� ���̹Ƿ� true�� ���� �Ѵ�
			return true;
		}
		else {

			$fp = fopen($this->_exec_log, 'r');

			while ( ! feof($fp)) {
				$cnt_chk++;
				$line = fgets($fp);
			}

			if ($cnt_chk > 10) {
				return false;
			}
			else {
				return true;
			}
		}
	}

	private function _checkExec()
	{
		if (is_file($this->_exec_file)) {
			return true;
		}
		else {
			return false;
		}
	}

	private function _logWrite()
	{
		if ($this->_limit_time) {//���� �ð��� ���� ��� ������ ���μ����� ���� �Ѵ�.
			$this->_stopOldProcess();
		}

		if ( ! is_dir(dirname(__FILE__) . self::BACKGROUND_LOG_PATH)) {
			mkdir(dirname(__FILE__) . self::BACKGROUND_LOG_PATH, 0707);
			chmod(dirname(__FILE__) . self::BACKGROUND_LOG_PATH, 0707);
		}

		if ( ! is_file($this->_exec_log)) {
			$fp = fopen($this->_exec_log, 'w');
			fclose($fp);
			chmod($this->_exec_log, 0707);
		}

		$arr_process = array(
			$this->_log_key,
			$this->_exec_file,
			$this->_exec_id,
			$this->_exec_start
		);

		$log_buffer = implode('|', $arr_process) . "\n";

		if ($fp = fopen($this->_exec_log, 'a')) {
			fwrite($fp, $log_buffer);
			fclose($fp);

			return true;
		}
		else {
			return false;
		}
	}

	private function _logUpdate()
	{
		$ret_bool = false;

		if (is_file($this->_exec_log)) {
			$fp = fopen($this->_exec_log, 'r');

			$lines = array();
			while ( ! feof($fp)) {
				$lines[] = fgets($fp);
			}

			$write_lines = array();
			foreach ($lines as $line) {
				$tmp_line = array();
				$tmp_line = explode('|', $line);

				if ($tmp_line[0] == $this->_log_key) {
					$tmp_line[count($tmp_line) - 1] = str_replace("\n", "", $tmp_line[count($tmp_line) - 1]);
					$tmp_line[] = $this->_pid . "\n";
					$write_lines[] = implode('|', $tmp_line);
					$ret_bool = true;
				}
				else {
					$write_lines[] = $line;
				}
			}
			fclose($fp);

			$fp = fopen($this->_exec_log, 'w');
			fwrite($fp, implode('', $write_lines));
			fclose($fp);
		}

		return $ret_bool;
	}

	public function logDelete($log_key)
	{
		$ret_bool = false;

		if ( ! $this->_exec_log) {
			$this->_exec_log = dirname(__FILE__) . self::BACKGROUND_LOG_PATH . 'ing_process.log';
		}
		if (is_file($this->_exec_log)) {
			$fp = fopen($this->_exec_log, 'r');

			$lines = array();
			while ( ! feof($fp)) {
				$lines[] = fgets($fp);
			}

			$write_lines = array();
			foreach ($lines as $line) {
				$tmp_line = array();
				$tmp_line = explode('|', $line);

				if ($tmp_line[0] == $log_key) {
					$ret_bool = true;
				}
				else {
					$write_lines[] = $line;
				}
			}
			fclose($fp);

			$fp = fopen($this->_exec_log, 'w');
			fwrite($fp, implode('', $write_lines));
			fclose($fp);
		}
		return $ret_bool;
	}

	private function _stopOldProcess()
	{
		if (is_file($this->_exec_log)) {
			$fp = fopen($this->_exec_log, 'r');

			$lines = array();
			while ( ! feof($fp)) {
				$lines[] = fgets($fp);
			}

			foreach ($lines as $line) {

				if ($line) {
					$tmp_line = array();
					$tmp_line = explode('|', $line);
					// ���μ��� �α� ���� ��Ģ�� ���� 4��° �׸� ���� �ð��� �־�� ��.
					$l_time = $tmp_line[3];

					$diff_time = $this->_diffTime($l_time);

					if ($diff_time > $this->_limit_time) {
						$pid = $tmp_line[4];

						if ($pid) {
							if ($this->killProcess($pid)) {
								$delete_log_keys[] = $tmp_line[0];
							}
						}
					}
				}
			}

			foreach ($delete_log_keys as $delete_log_key) {
				$this->logDelete($delete_log_key);
			}

			fclose($fp);
		}
		else {
			// ������ ������ �������� ���μ����� ���ٴ� ���̹Ƿ� true�� ����
			return true;
		}
	}

	public function killProcess($pid)
	{
		return exec("kill -KILL " . $pid);
	}

	private function _getLogKey()
	{
		$return_key = '';

		$time = time();
		$rand = rand(100, 999);
		$return_key = $time . $rand;

		return $return_key;
	}

	private function _diffTime($l_time)
	{
		$s_time = mktime(substr($this->_exec_start, 8, 2), substr($this->_exec_start, 10, 2), substr($this->_exec_start, 12, 2), substr($this->_exec_start, 4, 2), substr($this->_exec_start, 6, 2), substr($this->_exec_start, 0, 4));
		$c_time = mktime(substr($l_time, 8, 2), substr($l_time, 10, 2), substr($l_time, 12, 2), substr($l_time, 4, 2), substr($l_time, 6, 2), substr($l_time, 0, 4));

		return $s_time - $c_time;
	}

}
