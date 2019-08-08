<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {vendor} function plugin
 * Input:<br>
 *         - vendor_name = 
 * @author   Yury Zayats <rabbitone at coderab dot com>
 */
function smarty_function_vendor($params, &$smarty)
{
	$ret = "";
	
	$vendor_name = strtolower($params['vendor_name']);

	switch ($vendor_name) {
		case 'genesis' :
				$ret = 'Genesis';
				break;
		case 'nes' :
				$ret = 'NES';
				break;
		case 'snes' :
				$ret = 'SNES';
				break;
        case 'n64' :
				$ret = 'Nintendo 64';
				break;
        case 'sms' :
				$ret = 'Sega Master System';
				break;
	}

	return $ret;

}

/* vim: set expandtab: */
