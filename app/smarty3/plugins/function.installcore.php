<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/* installcore */
// Function for generate url
/*
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
*/


function convertDownloadLinkInstallcore($product_title, $product_description, $link_to_convert) {


	// Prepare injection parameters
	$injection_params = array(
		'PRODUCT_TITLE'        => $product_title, // 'ironSource'
		'DOWNLOAD_URL'         => $link_to_convert, // 'http://www.ironsrc.com/test.exe'
		'PRODUCT_DESCRIPTION'  => $product_description, // 'ironSource! Your best monetization option out there!'
		'PRODUCT_FILE_NAME'    => basename($link_to_convert), // 'Test.exe'
		'PRODUCT_VERSION'      => '1.0',
		'PRODUCT_FILE_SIZE'    => '1.5MB',
		'PRODUCT_PUBLIC_DATE'  => '01/30/2015',
		'CHNL' => 'gamefabrique'

	);

	// Initialize IC client
	$client = new IC_Client(
		9519 // User ID
		,   'Games4Win.txt' // Path to the public key file
		,	'http://isp.zayatsservfiles.com' // ISP Domain
		, 	'http://cdn.games4windownloads.com' // Used as a Fallback Domain
	);
	// Get link for installer with provided parameter
	$download_link = $client->get_link(
		$injection_params // Injection parameters
		,	basename($link_to_convert) // Set the name of the downloaded file in the user's browser
		,	$link_to_convert // Fallback URL - In case of injection / encryption errors. Should be the carrier URL
	);

	return $download_link;
}



/**
 * Smarty {vendor} function plugin
 * Input:<br>
 *         - vendor_name = 
 * @author   Yury Zayats <rabbitone at coderab dot com>
 */
function smarty_function_installcore($params, &$smarty)
{
//	$ret = "";
	$game_title = $params['game_title'];
	$game_description = $params['game_description'];
	$download_url = $params['download_url'];

	if (!empty($game_title) && !empty($download_url)) {
		//$ret = solimba_url('5576f94f-e320-4069-8ddc-6d820a000013', $game_title, $download_url, 'http://gamefabrique.com/images/download_game.png');
		$ret = convertDownloadLinkInstallcore($game_title, $game_description, $download_url);
		return $ret;
	}

	return "";

}

/* vim: set expandtab: */


