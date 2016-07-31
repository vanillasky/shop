<?php
/**
 * GODO
 *
 * PHP version 5
 *
 * @author extacy @ godosoft development team.
 * @package GODO
 */

/**
 * magic quote gpc ���� ����
 */
define('G_CONST_MAGIC_QUOTES',	get_magic_quotes_gpc() ? true : false);

/**
 * ���� �ð� (Ÿ�ӽ�����)
 */
define('G_CONST_NOW',			time());

/**
 * ��ũ��Ʈ ���� �ð� (����ũ�� Ÿ��)
 */
define('G_CONST_SCRIPT_START',	microtime());

/**
 * ���ڿ� ������
 */
define('G_STR_DIVISION',		'|!|');

/**
 * document root
 */
if (defined('SHOPROOT'))
	define('G_CONST_DOCROOT',	SHOPROOT);
else
	define('G_CONST_DOCROOT',	realpath(dirname(__FILE__).'/../../'));


/**
 * ������ ���
 */
define('G_CONST_DEVELOPER_MODE',	false);

// include path.
$_inc_path = explode(PATH_SEPARATOR, ini_get('include_path'));
$_inc_path[] = SHOPROOT.'/lib';
$_inc_path[] = SHOPROOT.'/lib/pear';
$_inc_path[] = SHOPROOT.'/lib/GODO';

ini_set("register_globals",0);
ini_set('include_path', implode(PATH_SEPARATOR, $_inc_path));

// config.
$_CFG = array();

$_CFG['global']['charset'] = 'euc-kr';	// Ÿ ĳ���ͼ� ȣȯ ���� ����
$_CFG['global']['use_security_filter'] = true;
$_CFG['global']['use_custom_error_handler'] = false;
$_CFG['global']['strip_requests_slashes'] = false;	// ���� ����
$_CFG['global']['report_error_level'] = version_compare(PHP_VERSION, '5.3.0' ,'>=') ? E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED : E_ALL ^ E_NOTICE ^ E_WARNING;

$_CFG['global']['use_output_compression'] = false;
$_CFG['global']['output_compression_level'] = 3;  // 0 (no compression) to 9 (most compression)

$_CFG['session']['use_db'] = false;
$_CFG['session']['savepath'] = 'session_tmp';
$_CFG['session']['lifetime'] = 18000;	// 5�ð�(��)

$_CFG['db']['persistent_connect'] = false;
$_CFG['db']['use_cache'] = false;

//
unset($_inc_path);

/**
 * ��ǰ�з� ������ ��ȯ ����
 */
define('_CATEGORY_NEW_METHOD_',	is_file(SHOPROOT.'/conf/category_new_method') ? true : false);
?>