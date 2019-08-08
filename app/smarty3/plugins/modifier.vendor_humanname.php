<?
//
// SEE: modifier.platform
// ^^^^^^^^^^^^^^^^^^^^^^
// ||||||||||||||||||||||


function smarty_modifier_vendor_humanname($vendor) {

	//$vendor_name = strtolower($params['vendor_name']);
    //$vendor = str_replace(' ', '-', $vendor);

    $vendor_name = strtolower($vendor);

    switch ($vendor_name) {
		case 'win':
			$ret = 'Windows';
			break;
		case 'dos':
			$ret = 'DOS';
			break;
		case 'segacd':
			$ret = 'Sega CD';
			break;

        case 'genesis':
            $ret = 'Sega Genesis';
            break;
        case 'nes':
            $ret = 'NES';
            break;
        case 'snes':
            $ret = 'SNES';
            break;
        case 'n64':
            $ret = 'Nintendo 64/N64';
            break;
        case 'sms':
            $ret = 'Sega Master System';
            break;
        case 'gg':
            $ret = 'GameGear';
            break;
		case 'gamegear':
			$ret = 'GameGear';
			break;

        case 'gba':
            $ret = 'GBA';
            break;
        case 'lynx':
            $ret = 'Lynx';
            break;
        case 'ngpc':
            $ret = 'NeoGeo Pocket Color';
            break;
        case 'arcade':
            $ret = 'Arcade';
            break;
        case 'saturn':
            $ret = 'Saturn';
            break;
        case 'neogeo':
            $ret = 'NeoGeo';
            break;
        case 'ps':
            $ret = 'Playstation';
            break;
        case 'ps2':
            $ret = 'Playstation 2';
            break;
        case 'ps3':
            $ret = 'Playstation 3';
            break;
        case 'psx':
            $ret = 'PSX';
            break;
        case 'psp':
            $ret = 'PSP';
            break;
        case '3do':
            $ret = '3DO';
            break;
        case 'wii':
            $ret = 'Wii';
            break;
        case 'ds':
            $ret = 'DS';
            break;
        case 'dreamcast':
            $ret = 'Dreamcast';
            break;
        case 'sega cd':
            $ret = 'Sega CD';
            break;
        case 'gameboy':
            $ret = 'GameBoy';
            break;
        case 'sg-1000':
            $ret = 'SG-1000';
            break;
        case 'gbc':
            $ret = 'GameBoy Color';
            break;
        case 'pc':
            $ret = 'PC';
            break;
        case 'pc engine':
            $ret = 'PC Engine';
            break;
        case 'atari jaguar':
            $ret = 'Atari Jaguar';
            break;
        case 'ngc':
            $ret = 'GameCube';
            break;
        case 'xbox':
            $ret = 'XBox';
            break;
        case '360':
            $ret = 'XBox 360';
            break;
        case 'turboduo':
            $ret = 'TurboDuo';
    }

	return $ret;

}

