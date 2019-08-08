<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {developer_link} function plugin
 * Input:<br>
 *         - company_name =
 *         - company_identifier =
 * @author   Yury Zayats <rabbitone at coderab dot com>
 */
function smarty_function_developer_link($params, &$smarty)
{
	$ret = "";

	$company_name = $params['company']['company_name'];
	$company_identifier = $params['company']['company_identifier'];
	//print_r($params);

	//$ret .= sprintf('<img src="/i/%s/%s" alt="%s" width="%s" height="%s" class="game-logo" />', $vendor, $filename, $alt,  $width, $height);
	if (empty($company_identifier)) {
		$ret = $company_name;
	} else {
		$ret = sprintf('<a href="/developers/%s/">%s</a>', $company_identifier, $company_name);
	}

	return $ret;

}

/* vim: set expandtab: */

