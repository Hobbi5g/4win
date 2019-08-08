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
function smarty_function_digest_image($params, &$smarty)
{
	$ret = "";

	$vendor = $params['vendor'];
	$filename = $params['filename'];
	$alt = $params['alt'];

	if (empty($params['width'])) {
		$width = 220;
	} else {
		$width = $params['width'];
	}

	if (empty($params['height'])) {
		$height = 112;
	} else {
		$height = $params['height'];
	}

	if (empty($filename)) {
		$ret .= sprintf('<img src="/images/no_logo.png" alt="%s" width="%s" height="%s" class="game-logo" />', $alt, $width, $height);
	} else {
		$ret .= sprintf('<img src="/i/%s/%s" alt="%s" width="%s" height="%s" class="game-logo" />', $vendor, $filename, $alt,  $width, $height);
	}

	return $ret;

}

