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
 * GODO_helper_csv
 *
 * ��� ���� :
 *
 * <code>
 *  $csv = Core::helper('CSV', $���ϰ��);
 *  $header = $csv->getHeader();	// �ʵ�� => �ʵ弳�� �� �迭�� ����
 *
 *  foreach($csv as $row) {
 *  	// debug($row);
 *  }
 * </code>
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage Helper
 */
final class GODO_helper_csv extends GODO_helper Implements Iterator {

    /**
     * ���� ������
     * @var resource
     */
	private $fp = null;

    /**
     * ���� �����ͳ� ���� ��ġ
     * @var integer
     */
	private $sp = 0;

    /**
     * fgetcsv �� �̿��Ͽ� �ѹ��� �о� ���� ����
     * @var integer
     */
	private $length = 135000;

    /**
     * CSV ������ ��� (�̳��� ���� ��� 2�ٿ� �ش��)
     * @var array
     */
	private $header = array();

    /**
     * ���� ó������ ���� ��ġ
     * @var integer
     */
	private $position = 0;

    /**
     * CSV ������ �о� ���̰�, iterate �� �غ� ��
     * @param string $path
     * @return void
     */
	public function __construct( $path ) {

		if ( is_readable( $path ) ) {

			$this->fp = @fopen( $path, 'r' );

			$_vals = fgetcsv( $this->fp, $this->length );
			$_keys = fgetcsv( $this->fp, $this->length );

			$this->sp = ftell( $this->fp );

			$this->header = array_combine( array_map('trim', $_keys), array_map('trim', $_vals) );
			unset( $_vals, $_keys );

		}
		else {
			Core::raiseError( '�ҷ��� CSV ������ �����ϴ�.' );
		}

	}

    /**
     * ���� �����͸� ����
     * @return void
     */
	public function __destruct() {

		if ( $this->fp !== null)
			fclose( $this->fp );
	}

    /**
     * CSV ���� ����� ����
     * @return array
     */
	public function getHeader() {

		return $this->header;
	}

    // �Ʒ� ������ �޼���� �ļ� �̹Ƿ�, ���� iterator �ʹ� ���� �ٸ��ϴ�
	public function rewind() {

		$this->position = 0;
		fseek( $this->fp, $this->sp );
	}

	public function current() {

		$this->position++;
		return fgetcsv( $this->fp, $this->length );
	}

	public function key() {

		return $this->position;
	}

	public function valid() {

		return feof( $this->fp ) ? false : true;
	}

	public function next() {

		//++$this->position;
	}

}
?>
