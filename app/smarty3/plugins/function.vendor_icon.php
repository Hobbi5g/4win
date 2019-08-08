<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {vendor_icon} function plugin
 * Input:<br>
 *         - vendor_name = 
 *         - alt = 
 * @author   Yury Zayats <rabbitone at coderab dot com>
 */
function smarty_function_vendor_icon($params, &$smarty)
{
	$ret = "";
	$vendor_name = "";
	if (!empty($params['vendor'])) {
		$vendor_name = $params['vendor'];
	}

	if (!empty($params['vendor_name'])) {
		$vendor_name = $params['vendor_name'];
	}

	$alt = $params['alt'];

	$show_title = false;
	if ($params['show_title']) {
		$show_title = true;
	}

	switch ($vendor_name) {
		case 'genesis' :
            if (empty($alt)) { $alt = 'Sega Genesis'; }
			$ret = "<img src=\"/images/icon-1.png\" alt=\"{$alt}\" width=\"16\" height=\"12\" class=\"controller-icon\" />";
			if ($show_title) {
				$ret .= '<a href="/genesis/">Genesis</a>';
				//$ret .= 'Genesis';
			}
			break;
		case 'nes' :
            if (empty($alt)) { $alt = 'NES'; }
			$ret = "<img src=\"/images/icon-3.png\" alt=\"{$alt}\" width=\"16\" height=\"12\" class=\"controller-icon\" />";
			if ($show_title) {
				$ret .= '<a href="/nes/">NES</a>';
				//$ret .= 'NES';
			}
			break;
		case 'snes' :
            if (empty($alt)) { $alt = 'SNES'; }
	        $ret = "<img src=\"/images/icon-2.png\" alt=\"{$alt}\" width=\"16\" height=\"12\" class=\"controller-icon\" />";
			if ($show_title) {
				$ret .= '<a href="/snes/">SNES</a>';
				//$ret .= 'SNES';
			}
			break;
        case 'sms' :
            if (empty($alt)) { $alt = 'Sega Master System'; }
            $ret = "<img src=\"/images/icon-sms.png\" alt=\"{$alt}\" width=\"16\" height=\"12\" class=\"controller-icon\" />";
	        if ($show_title) {
		        $ret .= '<a href="/sega/">Sega Master System</a>';
		        //$ret .= 'Sega Master System';
	        }
	        break;
        case 'n64' :
            if (empty($alt)) { $alt = 'Nintendo 64'; }
            $ret = "<img src=\"/images/icon-n64.png\" alt=\"{$alt}\" width=\"16\" height=\"16\" class=\"controller-icon\" />";
	        if ($show_title) {
		        $ret .= '<a href="/n64/">Nintendo 64</a>';
		        //$ret .= 'Nintendo 64';
	        }
	        break;
	}
	
	return $ret;

}

/* vim: set expandtab: */

