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
 * GODO_DB_indexer
 * @author extacy @ godosoft development team.
 * @package GODO
 * @subpackage DB
 */
final class GODO_DB_indexer {

	/**
	 * DB �ν��Ͻ�
	 * @var GODO_DB
	 */
	private $db;

	/**
	 * Construct
	 * @param GODO_DB
	 * @return void
	 */
	public function __construct(&$db) {
		$this->db = $db;
	}


	/**
	 * ���� ���� & ����� ���� ���� ���� ����
	 * @var boolean
	 */
	public $skipclear = false;


	/**
	 * �ν��Ͻ� ����
	 * @return GODO_DB_builder
	 */
	public function __clone() {

		return $this;

	}

	/**
	 * Ư��, ������ ������ �ܾ ����
	 * @toto �����ڵ� ��ȯ��, �� range �� ���� ���� ó��
	 * @param string $words
	 * @return array
	 */
	private function getRawWords($words) {

		// �����ڵ�� ��ȯ�� �ѱ��� ���� ������� ����
		$words = Core::helper('String')->toHan($words);

		// �±�, &xxxx ���� �� ġȯ
		$words = strip_tags($words);
		$words = strtr( $words, array_flip( get_html_translation_table( HTML_SPECIALCHARS ) ) );

		// �˻��� �ʿ� ���� Ư�� ġȯ
		$_replaceFr = array(
			'\n','\r\n','-','+','_','~','!','@','#','$','%','^','&','*','(',')','{','}','[',']',';','\'',':','"',',','.','<','>','/','?','`',
		);

		$words = str_replace($_replaceFr,' ',$words);

		// �������� ����
		$words = explode(' ',$words);

		// �ߺ� ����
		$words = array_unique($words);

		return $words;

	}

	/**
	 * �� ����� ������ ����
	 * @param string $table ������ ���̺��
	 * @param mixed $pk ������ primary key
	 * @return void
	 */
	private function clear($table, $pk) {

		$this->db->delete( $this->getTable($table) )->where( 'pk = ?' , $pk )->query();

	}

	/**
	 * ����� ������ ����
	 * @param string $keywords
	 * @param string $table
	 * @param string $column
	 * @param string $pk
	 * @return void
	 */
	private function save($keywords,$table,$column, $pk) {

		$query = array();

		$kw_insert_stmt = $this->db->prepare("insert into gd_indexer_keywords set `word` = ? on duplicate key update `hits` = `hits` + 1");
		$kw_select_stmt = $this->db->prepare("select seq from gd_indexer_keywords where `word` = ?");

		foreach ($keywords as $keyword) {

			$r = $kw_insert_stmt->execute($keyword);

			if (($seq = $kw_insert_stmt->lastID()) < 1) {
				$kw_select_stmt->execute($keyword);
				$tmp = $kw_select_stmt->current();
				$seq = $tmp['seq'];
			}

			//
			$query[] = "('$seq', '$column', '$pk')";
		}

		if (sizeof($query) > 0) {
			$queries = array_chunk($query, 5);

			foreach ($queries as $query) {
				$query = "INSERT INTO ".$this->getTable($table)." (`seq`, `column`, `pk`) VALUES " . implode(',',$query);
				$this->db->query($query);
			}

		}

	}

	/**
	 * ���� ���̺���� ����
	 * @param object $table
	 * @return
	 */
	private function getTable($table) {
		return 'gd_indexer_table_'.$table;

	}

	/**
	 * ���� ���̺��� ����
	 * @param object $table
	 * @return
	 */
	private function _createTable($table) {

		return;

		$_table = $this->getTable($table);

		$query = "
		CREATE TABLE IF NOT EXISTS `$_table` (
		  `seq` int(10) unsigned NOT NULL,
		  `column` varchar(32) NOT NULL,
		  `pk` int(10) unsigned NOT NULL,
		  KEY `ix_key` (`seq`,`column`)
		) ENGINE=MyISAM DEFAULT CHARSET=euckr;
		";

	}

	/**
	 * ������ ���� �Ͽ� ����
	 * @param string $table
	 * @param integer $pk
	 * @param array $data	[optional]
	 * @param array $allow_column	[optional]
	 * @return
	 */
	public function generate($table, $pk, $data=array(), $allow_column=array()) {

		if (!$this->skipclear)
			 $this->clear($table, $pk);

		if (!empty($data) && !empty($allow_column)) {

			if (!is_array($allow_column))
				$allow_column = array($allow_column);

			foreach ($allow_column as $column) {
				$keywords = $this->getKeywords($data[$column], 4);	// 4����Ʈ ����
				$this->save($keywords, $table, $column, $pk);
			}
		}

	}

	/**
	 * ���� ����
	 * @param string $word
	 * @param integer $byte [optional]
	 * @return array
	 */
	public function getKeywords($word, $byte = 4) {	// �ѱ� 2 ��, ���� 4��

		$words = !empty($word) ? $this->getRawWords($word) : array();

		$keywords = array();

		foreach ($words as $word) {

			$chars = $this->getChars($word);

			if (($m = sizeof($chars)) >= $byte) {

				for ($i=0;$i<$m;$i++) {

					$_keyword = '';
					$_mb = false;

					for ($j=0,$_m=$byte;$j<$_m;$j++) {

						if (!isset($chars[$i + $j])) continue;

						$_keyword .= $chars[$i + $j];

						if (ord($chars[$i + $j]) > 127) {
							$_mb = true;
							$_m--;
						}
					}

					if ($_mb) $_byte = $byte - 1;
					else $_byte = $byte;

					if ($_byte > strlen($_keyword)) continue;

					$keywords[] = $this->convert($_keyword);
				}
			}
			else {
				$keywords[] = $this->convert($word);
			}

		}

		return $this->cleanup($keywords);
	}


	/**
	 * �빮�ڸ� �ҹ��ڷ� ����, �ѱ��� �ڼ� ������ �и� �Ͽ� ����
	 * @param string $str
	 * @return string
	 */
	function convert($str) {

		$str = Core::helper('String')->strtolower($str);
		$str = Core::helper('String')->splitKorean($str);

		return $str;

	}


	/**
	 * �ܾ �ѱ��ھ� �ڸ�
	 * @param string $word
	 * @return array
	 */
	private function getChars($word) {

		$words = array();

		for ($i=0,$m=strlen($word);$i<$m;$i++) {

			$char = $word[$i];

			if (ord($char) > 127) {
				$i++;
				$char = $char . $word[$i];
			}

			$words[] = $char;
		}

		return $words;

	}

	/**
	 * ����� ������ �ߺ� ����
	 * @param array $keywords
	 * @return array �ߺ� ���ŵ� ���� �迭
	 */
	private function cleanup($keywords) {

		for ($i=0,$m=sizeof($keywords);$i<$m;$i++) {

			$_keyword = trim($keywords[$i]);

			if (empty($_keyword) || sizeof(array_unique($this->getChars($_keyword))) < 2) unset($keywords[$i]);

		}

		$keywords = array_unique($keywords);

		return $keywords;

	}

	/**
	 * �˻��� ���� join ������ ������ SQL ������ ����
	 * @param string $table
	 * @param string $columns
	 * @param string $keyword
	 * @return mixed GODO_DB_builder or false
	 */
	public function search($table, $columns, $keyword) {

		if (!is_array($columns)) $columns = array($columns);
		$keyword = $this->getKeywords($keyword);

		$_columns = array();
		$_keyword = array();

		foreach ($columns as $k) if ($k) $_columns[] = $k;
		foreach ($keyword as $k) if ($k) $_keyword[] = $k;

		if (sizeof($_columns) && sizeof($_keyword)) {

			$builder = $this->db->builder()->select();

			$builder->from(array('map'=> $this->getTable($table)),null);
			$builder->join(array('kw'=> 'gd_indexer_keywords'),'kw.seq = map.seq',null);

			$builder->where('map.`column` IN (?)', array( $_columns ));
			$builder->where('kw.`word` IN (?)', array( $_keyword ));

			$builder->group('map.pk');
			$builder->having('_cnt >= ?', sizeof($_keyword));
			$builder->order('null');

			$builder->columns(array(
				'map.pk',
				'_cnt' => $this->db->expression('COUNT(map.pk)')
			));

			return $builder;

		}

		return false;

	}

}
?>
