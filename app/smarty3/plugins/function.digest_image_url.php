<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {digest_image} function plugin
 * Input:<br>
 *         - vendor =
 *         - filename =
 *         - alt =
 * @author   Yury Zayats <rabbitone at coderab dot com>
 */
function smarty_function_digest_image_url($params, &$smarty)
{
	$ret = "";

	$vendor = $params['vendor'];
	$filename = $params['filename'];

	if (empty($filename)) {
		$ret = '/images/no_logo.png';
	} else {
		$ret = sprintf('/i/%s/%s', $vendor, $filename);
	}

	return $ret;

}

