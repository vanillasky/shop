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
 * GODO ��Ű�� ���
 */

require_once(dirname(__FILE__) .'/config.inc.php');
require_once(dirname(__FILE__) .'/core.php');


// ����δ� ����
if (!class_exists('GODO_Autoload', false)) {
	require_once(SHOPROOT . '/lib/GODO/Autoload.php');
}

GODO_Autoload::register();	// spl_autoload_register
?>
