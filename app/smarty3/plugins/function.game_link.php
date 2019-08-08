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
function smarty_function_game_link($params, &$smarty)
{
	$ret = "";
	
	$game_title = $params['title'];
	$game_identifier = $params['identifier'];
	$game_id_hidden = $params['is_hidden'];
	
	//if (empty($filename)) {
	//	$ret .= sprintf('<img src="/images/no_logo.png" alt="%s" width="%s" height="%s" class="game-logo" />', $alt, $width, $height);
	//} else {
	//	$ret .= sprintf('<img src="/i/%s/%s" alt="%s" width="%s" height="%s" class="game-logo" />', $vendor, $filename, $alt,  $width, $height);
	//}
	if (empty($game_id_hidden)) {
		$ret = sprintf('<a href="/games/%s/">%s</a>', $game_identifier, $game_title);
	} else {
		$ret = sprintf('<s>%s</s>', $game_title);
	}
	
	return $ret;

}

