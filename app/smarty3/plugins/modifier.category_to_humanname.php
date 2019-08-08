<?
//
// SEE: modifier.platform
// ^^^^^^^^^^^^^^^^^^^^^^
// ||||||||||||||||||||||


function smarty_modifier_category_to_humanname($category) {

	//$vendor_name = strtolower($params['vendor_name']);
    //$vendor = str_replace(' ', '-', $vendor);

    $category = strtolower($category);
//'none','arcade_action','arkanoids','adventure_rpg','board','tetris','card','logic_puzzle','shooter','strategy_war','handheld','pacman','word','bowling','rpg','sport','racing','fighting'
    switch ($category) {
        case 'none' :
            $ret = "Unknown";
            break;
        case 'arcade_action' :
            $ret = 'Arcade/Action';
            break;
        case 'arkanoids' :
            $ret = 'Arkanoid';
            break;
        case 'adventure_rpg' :
            $ret = 'Adventure';
            break;
        case 'board' :
            $ret = 'Board';
            break;
        case 'tetris' :
            $ret = 'Tetris';
            break;
        case 'card' :
            $ret = 'Card';
            break;
        case 'logic_puzzle' :
            $ret = 'Logic/Puzzle';
            break;
        case 'shooter' :
            $ret = 'Shooter';
            break;
        case 'strategy_war' :
            $ret = 'Strategy/Wargame';
            break;
        case 'handheld' :
            $ret = 'Handheld';
            break;
        case 'pacman' :
            $ret = 'Pacman';
            break;
        case 'word' :
            $ret = 'Word';
            break;
        case 'bowling' :
            $ret = 'Bowling';
            break;
        case 'rpg' :
            $ret = 'RPG';
            break;
        case 'sport' :
            $ret = 'Sport';
            break;
        case 'racing' :
            $ret = 'Racing';
            break;
        case 'fighting' :
            $ret = 'Fighting';
            break;
    }

	return $ret;

}

