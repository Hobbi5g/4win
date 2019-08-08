<?

// дублирует vendor
function smarty_modifier_platform($platform) {

	$platform = strtolower($platform);

    if ($platform == 'genesis') { return 'Genesis'; }
    if ($platform == 'nes') { return 'NES'; }
    if ($platform == 'snes') { return 'SNES'; }
    if ($platform == 'n64') { return 'Nintendo 64'; }
    if ($platform == 'sms') { return 'Sega Master System'; }

}