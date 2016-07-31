<?php

/**
 * GODO_DB_cache �����Ͽ� ����
 *
 * SQL ���� ����� ĳ���Ͽ�, ���� ���ǽ� ĳ�õ� ������ ������
 * 2014�� 10�� 30�� ��ġ�� �������� ��밳��
 */
class dbcache
{
	/**
	 * DBĳ�� ȯ������
	 */
	private $_cacheConfig = array(
		'cacheUseType' => 'none',
	);

	/**
	 * DBĳ�� ��뿩��
	 */
	private $isAvailable = false;

	/**
	 * cache ����
	 * @var string
	 */
	private $cachePath = null;

	/**
	 * DBĳ�ð� ����Ǵ� default ���
	 * @var string
	 */
	private $defaultPath = null;

	/**
	 * DBĳ�ð� ����Ǵ� location ���
	 * @var string
	 */
	private $locationPath = null;

	/**
	 * ĳ�� ���� �ð� (��)
	 * 30�ʷ� ����
	 * @var integer
	 */
	private $cache_expire = 30;

	/**
	 *
	 */
	private $cacheNamePrefix = "__dbcache__";

	/**
	 * ������ (set cache save directory)
  	 * @return void
	 */
	public function __construct()
	{
		// DBĳ�� ȯ������ ����
		if (file_exists(dirname(__FILE__).'/../conf/cache.db.cfg.php')) {
			include dirname(__FILE__).'/../conf/cache.db.cfg.php';
			$this->_cacheConfig = $cacheConfig['db'];
		}

		// DBĳ�� ��뿩�� ����
		if ($this->_cacheConfig['cacheUseType'] == 'default') {
			$this->isAvailable = true;
		}
		else {
			return;
		}

		// cache ���� ����
		$this->cachePath = G_CONST_DOCROOT . Core::DS . 'cache' . Core::DS;
		// cache ���� ������ false
		if (!is_dir($this->cachePath)) {
			return;
		}

		// DBĳ�� default ���丮 ����
		$this->defaultPath = $this->cachePath . 'DB' . Core::DS;

		// DBĳ�� ���丮 ����
		// step1. DB���� ����
		if ($this->defaultPath != '' && !is_dir($this->defaultPath)) {
			@mkdir($this->defaultPath, 0707);
			chmod($this->defaultPath, 0707);
		}

	}

	public function setLocation($loc) {

		$loc = trim($loc, '\\/');

		$this->locationPath = $this->defaultPath . $loc . Core::DS;

		// step2. �������� ����
		if (!is_dir($this->locationPath)) {
			@mkdir($this->locationPath, 0707);
			chmod($this->locationPath, 0707);
		}

		return $this;
	}

	/*
	 * DBĳ�� ȯ������ ���� (ȯ������ ������ ���)
	 * @return array
	 */
	public function loadConfig()
	{
		return $this->_cacheConfig;
	}

	/**
	 * SQL ���� �ؽø� ����
 	 * @param string $sql
	 * @return string
	 */
	private function getCacheName( $sql )
	{
		return $this->cacheNamePrefix.md5( trim( $sql ) );
	}

	/**
	 * ĳ�õǾ� �ִ� ���� ����� ����
	 * @param string $sql
 	 * @param string $lifeTime : ĳ�ø���ð� ($this->cache_expire �� �⺻��)
	 * @return array or bool
	 */
	public function getCache( $sql , $lifeTime = null)
	{
		// 1.DBĳ�� ��뿩�� ����
		if ($this->isAvailable !== true) {
			return false;
		}

		// 2. DBĳ�� ���丮 ����
		if ($this->locationPath == '') return false;
		else if (!is_dir($this->locationPath)) return false;
		else if (strpos($this->locationPath, $this->defaultPath) === false) return false;

		// 3. ĳ���̸�����
		$cache_name = $this->getCacheName( $sql );

		// 4. ĳ�ø���ð� ����
		if($lifeTime === null) {
			$cache_expire = G_CONST_NOW - $this->cache_expire;
		} else {
			$cache_expire = G_CONST_NOW - $lifeTime;
		}

		// 5. ĳ�������� �ְ�, ĳ�ø���ð��� ��������ʾ����� ĳ�����ϸ���
		if ( is_file( $this->locationPath . $cache_name ) && filemtime( $this->locationPath . $cache_name ) > $cache_expire ) {

			$result = '';

			if ( $fh = @fopen( $this->locationPath . $cache_name, 'r' ) ) {

				flock( $fh, LOCK_SH );
				if ( filesize( $this->locationPath . $cache_name ) > 0)
					$result = fread( $fh, filesize( $this->locationPath . $cache_name ) );
				flock( $fh, LOCK_UN );
				fclose( $fh );
			}

			return ( $result != '' ) ? unserialize( $result ) : false;
		}

		return false;
	}

	/**
	 * ���� ����� ����
 	 * @param string $sql
	 * @param array $result
	 * @return void
	 */
	public function setCache( $sql, $result = array() )
	{
		// 1.DBĳ�� ��뿩�� ����
		if ($this->isAvailable !== true) {
			return;
		}

		// 2. DBĳ�� ���丮 ����
		if ($this->locationPath == '') return false;
		else if (!is_dir($this->locationPath)) return false;
		else if (strpos($this->locationPath, $this->defaultPath) === false) return false;

		// 3. ĳ���̸�����
		$cache_name = $this->getCacheName( $sql );

		// 4. ĳ������
		if (is_array($result) && count($result) > 0 ) {

			$contents = serialize( $result );

			if ( $fh = @fopen( $this->locationPath . $cache_name, 'w' ) ) {

				flock( $fh, LOCK_EX );
				fwrite( $fh, $contents );
				flock( $fh, LOCK_UN );
				fclose( $fh );
				@chmod( $this->locationPath . $cache_name, 0707 );
			}
		}
		else {
			@unlink( $this->locationPath . $cache_name );
		}
	}

	/**
	 * DBĳ�ð��� (ĳ������ �����ð��� �� ���� 30�������� ����)
	 * @param string $loc : ������ ĳ�� ����
	 * return void
	 */
	public function clearCache($loc = null)
	{
		// 1.DBĳ�� ��뿩�� ����
		if ($this->isAvailable !== true) {
			return;
		}

		// 2. DBĳ�� ���丮 ����
		if ($this->defaultPath == '') return;
		else if (!is_dir($this->defaultPath)) return;

		// ��������ġ�� ĳ�ø� ����
		if ($loc !== null && $loc != '') {
			$this->locationPath = $this->defaultPath . $loc;
			// 3. ��ġ����������, DBĳ�� ��ġ���� ���丮 ����
			if ($this->locationPath == '') return;
			else if (!is_dir($this->locationPath)) return;

			foreach(glob($this->locationPath.'/*') as $cacheFile) {
				// ĳ�ð�ο� "$this->cachePath"�� ���ԵǾ��ְ�, ĳ���̸��� prefix�� ���ԵǾ��ִ��� üũ ��, ���ϻ���
				if ((strpos($cacheFile, $this->cachePath) !== false) && (strpos($cacheFile, $this->cacheNamePrefix) !== false)) {
					@unlink( $cacheFile );
				}
			}
		// ��üĳ�ð���
		} else {
			foreach(glob($this->defaultPath.'*') as $cacheFolder) {

				foreach(glob($cacheFolder.'/*') as $cacheFile) {
					// ĳ�ð�ο� "$this->cachePath"�� ���ԵǾ��ְ�, ĳ���̸��� prefix�� ���ԵǾ��ִ��� üũ ��, ���ϻ���
					if ((strpos($cacheFile, $this->cachePath) !== false) && (strpos($cacheFile, $this->cacheNamePrefix) !== false)) {
						@unlink( $cacheFile );
					}
				}
			}
		}
	}
}
?>
