<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/* solimba */
// Function for generate url
function solimba_url($dmr_code, $name, $file_download, $file_icon, $execution_params = '')
{
	if (!function_exists('curl_init')){
		die('cURL is not installed!');
	}

	$server = "api.socdn.com";
	$fields = array(
		'dmr' => $dmr_code,
		'name' => $name,
		'src' => $file_download,
		'icon' => $file_icon,
		'execution_params' => $execution_params
	);

	// urlify the data for the POST
	$fields_string = '';
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, count($fields));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_URL, $server.'/installer');
	curl_setopt($ch, CURLOPT_USERAGENT, 'SolimbaPublisher/1.0');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type" => "text/plain"));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$response = curl_exec($ch);
	curl_close($ch);

	$success = (preg_match('#^https?://#', trim($response)));
	if (!$success) {
		$response = $file_download;
	}
	return $response;
}



/**
 * Smarty {vendor} function plugin
 * Input:<br>
 *         - vendor_name = 
 * @author   Yury Zayats <rabbitone at coderab dot com>
 */
function smarty_function_solimba($params, &$smarty)
{
//	$ret = "";
	$game_title = $params['game_title'];
	$download_url = $params['download_url'];

	if (!empty($game_title) && !empty($download_url)) {

		$ret = solimba_url('5576f94f-e320-4069-8ddc-6d820a000013', $game_title, $download_url, 'http://gamefabrique.com/images/download_game.png');
		return $ret;
	}

	return "";

}

/* vim: set expandtab: */


