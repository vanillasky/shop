<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */

/**
 * GODO_DB_cache
 *
 * SQL ���� ����� ĳ���Ͽ�, ���� ���ǽ� ĳ�õ� ������ ������
 * ����, ������� ����
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_cache {

	/**
	 * ĳ�� ��� ����
	 * @var boolean
	 */
	private $use = true;

	/**
	 * ĳ�ø� ������ ���
	 * @var string
	 */
	private $path;

	/**
	 * ĳ�� ���� �ð� (��)
	 * @var integer
	 */
	private $cache_expire = 300;

	/**
	 * ������ (set cache save directory)
 	 * @return void
	 */
	public function __construct() {
		$this->path = G_CONST_DOCROOT . Core::DS . 'cache' . Core::DS . 'DB' . Core::DS;
		if (!is_dir($this->path)) {
			@mkdir($this->path, 0707);
		}
	}

	/**
	 * ĳ�� ��� ���θ� ����
 	 * @return boolean
	 */
	public function isAvailable() {

		return $this->use;
	}

	/**
	 * SQL ���� �ؽø� ����
 	 * @param string $sql
	 * @return string
	 */
	private function getCacheName( $sql ) {

		return md5( trim( $sql ) );
	}

	private function getCacheFilePath($sql)
	{
		$cache_name = $this->getCacheName( $sql );
		return $this->path . $cache_name;
	}


	/**
	 * ĳ�õǾ� �ִ� ���� ����� ����
 	 * @param string $sql
	 * @return array
	 */
	public function getCache( $sql ) {

		if ( $this->hasCache($sql) ) {

			$result = '';

			if ( $fh = @fopen( $this->getCacheFilePath($sql), 'r' ) ) {

				flock( $fh, LOCK_SH );
				if ( filesize( $this->getCacheFilePath($sql) ) > 0)
					$result = fread( $fh, filesize( $this->getCacheFilePath($sql) ) );
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
	 * @param mixed GODO_DB_statement or string [optional]
	 * @return void
	 */
	public function setCache( $sql, $result = '' ) {

		$cache_name = $this->getCacheName( $sql );

		if ($result instanceof GODO_DB_statement) {
			$result = $result->fetchAll();
		}
		else {
			return false;
		}

			$contents = serialize( $result );

		if ( $fh = @fopen( $this->getCacheFilePath($sql), 'w' ) ) {

			flock( $fh, LOCK_EX );
			fwrite( $fh, $contents );
			flock( $fh, LOCK_UN );
			fclose( $fh );
			@chmod( $this->getCacheFilePath($sql), 0777 );
		}

	}

	public function hasCache($sql)
	{
		$cache_name = $this->getCacheName( $sql );

		if ( is_file( $this->getCacheFilePath($sql) ) ) {
			if ((filemtime($this->getCacheFilePath($sql)) + $this->cache_expire) >= G_CONST_NOW) {
				return true;
			}
			else {
				@unlink( $this->getCacheFilePath($sql) );
			}
		}

		return false;
	}

	public function setCacheExpire($expire = 300)
	{
		$this->cache_expire = $expire;
	}

}
?>
