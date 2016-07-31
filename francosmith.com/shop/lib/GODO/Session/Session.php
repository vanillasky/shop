<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Session
 */

/**
 * Sessions
 * @see scheme.sql
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Session
 */
final class Sessions {

	/**
	 * DB �ν��Ͻ� (DB ������ ����� ��� ������)
	 * @var GODO_DB
	 */
	private $DB = null;

	/**
	 * ���� ȯ�� ����
	 * @var array
	 */
	private $config = null;

	/**
	 * ���� ���� ���θ� �Ǵ�Ű ���� crc32
	 * @var integer
	 */
	private $checksum = null;

	/**
	 * ���� ��� ȯ���� ����
	 * @return
	 */
	public function __construct() {

		$this->config = Core::config( 'session' );

		// db ����
		if ( $this->config['use_db'] ) {

			$this->DB = Core::loader( 'GODO_DB' );

			session_set_save_handler(
				array( $this, "open" ),
				array( $this, "close" ),
				array( $this, "read" ),
				array( $this, "write" ),
				array( $this, "destroy" ),
				array( $this, "gc" )
			);

		}
		// file ����
		else {
			ini_set( "session.save_path", $_SERVER['DOCUMENT_ROOT'] . Core::DS . $this->config['savepath'] );
		}

		// session id ����
		if ( isset( $_GET['sess_id'] ) && preg_match('/^[a-zA-Z0-9]+$/', $_GET['sess_id'] ))
			$this->reset_sessionID( $_GET['sess_id'] );

		ini_set( "session.gc_maxlifetime", $this->config['lifetime'] );

		session_start();

	}

	/**
	 * ������ ����ϰ� ����
	 * @return void
	 */
	public function __destruct() {

		session_write_close();

	}

	/**
	 * ���� id �� ����
	 	 * @param string $ssid ������ ���� id
	 * @return void
	 */
	private function reset_sessionID( $ssid ) {

		session_id( $ssid );

	}

	/**
	 * ���� ���� �������� crc32(md5 ���� ����) üũ���� ����
	 * @param string $data
	 * @return integer
	 */
	private function checksum( $data ) {

		$checksum = sprintf( '%u', crc32( $data ) );
		return $checksum;

	}

	/**
	 * ���� �����͸� ���ڵ���
	 * @param string $data ���� ������
	 * @return string ���ڵ��� ���� ������
	 */
	private function encode( $data ) {

		return $data;

	}

	/**
	 * ���� �����͸� ���ڵ���
	 * @param string $data ���� ������
	 * @return string ���ڵ��� ���� ������
	 */
	private function decode( $data ) {

		return $data;

	}

	/**
	 * ���� id �� ����
	 * @param string $id
	 * @return string ���� id
	 */
	private function getID( $id ) {

		return md5( $id );
	}

	/**
	 * ������ ��
	 * @param string $savepath
	 * @param string $sessionname
	 * @return true
	 */
	public function open( $savepath, $sessionname ) {

		return true;

	}

	/**
	 * ������ ����
	 * @return true
	 */
	public function close() {

		return true;

	}

	/**
	 * ���� �����͸� ����
	 * @param string $id ���� id
	 * @return string ���� ������
	 */
	public function read( $id ) {

		static $stmt = null;	// prepared statement

		if ($stmt === null) {
			$query = "SELECT data FROM gd_session WHERE id = ? AND expire >= ?";
			$stmt  = $this->DB->prepare( $query );
		}

		$stmt->execute( array(
			$this->getID( $id ), G_CONST_NOW
		) );

		if ( $result = $stmt->fetchArray() ) {
			$this->checksum = $this->checksum( $result[0] );
			return $this->decode( $result[0] );
		}

	}

	/**
	 * ���� ������ ����
	 * @param string $id ���� id
	 * @param string $data ���� ������
	 * @return boolean
	 */
	public function write( $id, $data ) {

		static $u_stmt = null;	// prepared statement
		static $i_stmt = null;	// prepared statement

		$data = $this->encode( $data );

		if ( $this->checksum === $this->checksum( $data ) ) {

			if ($u_stmt === null) {

				$query = "
				UPDATE gd_session SET expire = ? WHERE id = ? AND expire > ?
				";
				$u_stmt  = $this->DB->prepare( $query );
			}

			$param = array(
				G_CONST_NOW + $this->config['lifetime'], $this->getID( $id ), G_CONST_NOW
			);

			$stmt = $u_stmt;

		}
		else {

			// @toto : mysql ���� ���� �̹Ƿ� ������ �ʿ䰡 ����
			if ($i_stmt === null) {

				$query = "
				INSERT INTO gd_session
					SET id = ?, data = ?, expire = ?

				ON DUPLICATE KEY UPDATE
					data = VALUES(data), expire = VALUES(expire)
				";
				$i_stmt  = $this->DB->prepare( $query );
			}

			$param = array(
				$this->getID( $id ),
				$data,
				G_CONST_NOW + $this->config['lifetime']
			);

			$stmt = $i_stmt;

		}

		return $stmt->execute( $param ) ? true : false;

	}

	/**
	 * ������ �ı���
	 * @param string $id ���� id
	 * @return boolean
	 */
	public function destroy( $id ) {

		static $stmt = null;

		if ($stmt === null) {
			$query = "DELETE FROM gd_session WHERE id = ?";
			$stmt = $this->DB->prepare( $query );
		}

		return $stmt->execute( array( $this->getID( $id ) ) ) ? true : false;

	}

	/**
	 * ������ �ݷ��� (����� ������ ��� ������)
	 * @param string $maxlifetime
	 * @return boolean
	 */
	public function gc( $maxlifetime ) {

		static $stmt = null;

		if ($stmt === null) {
			$query = "DELETE FROM gd_session WHERE expire  < ?";
			$stmt = $this->DB->prepare($query);
		}

		return $stmt->execute( G_CONST_NOW ) ? true : false;

	}
}
?>
