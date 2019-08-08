<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * @author   Yury Zayats <rabbitone at coderab dot com>
 */
function smarty_function_member_link($params, &$smarty)
{
	$ret = "";

	$login = $params['login'];
	$action = $params['action'];

	$login = urlencode($login);

	if (empty($action)) {
		$ret = sprintf('/members/%s/', $login);
	} else {
		$ret = sprintf('/members/%s/%s/', $login, $action);
	}

	return $ret;

}

