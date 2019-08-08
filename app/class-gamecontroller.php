<?



class GameController extends ApplicationController {

	var $game_model;
	var $paginator_page = 1;
	var $game_slug = null;
	var $games = null;
	var $is_search = false;
	var $is_debug = false;
	var $is_local = false; // то же что и is_debug
	var $installer_vendor = false;
	var $search_query;

	var $game;

	function __construct() {
		parent::__construct();
		//$this->game_model = new Game();
		$this->export_defines();
		$this->init_main_menu();

	}



	function export_defines() {
		if (DEBUG) {
			$this->is_debug = true;
			$this->is_local = true;
		}

		$this->installer_vendor = INSTALLER_VENDOR;

	}

	// если есть запись в таблице portals, то портал открывается по адресам portal и portal-game
	// сделано это для того, что если есть игра с идентификатором portal, то через альтернативное имя portal-game можно
	// было бы просмотреть портал


	function index() {


		//$games = array();
		/*

		$game_slug = $this->game_slug;
		$game_count = $this->conn->getCol("SELECT count(*) FROM games WHERE stringid = ? AND hidden = 'N'", 0, array($game_slug));

		if ($game_count == 0) {

			//echo $game_slug;
			//if (substr($game_slug, -5, 5) == '-game') {
				//echo 'xxx';
			//	$game_slug = substr($game_slug, 0, strlen($game_slug) - 5); // terminator-game  =>  terminator
			//}

			//$this->page_title = ucwords(str_replace('-', ' ', $game_slug)) . ' game';

			// такой портал существует
			$portal_identifier = $this->is_portal_exists($game_slug);
			if (!empty($portal_identifier)) {
				// портал есть
				$this->portal($portal_identifier);
			}


			$this->smart_redirects($game_slug); //echo $portal_identifier;

		}
		*/

		// это обычная игра, есть записи в базе
		$this->existing_game($this->game_slug);

	}



	// $filtered_identifier уже очищен от окончания -game при проверке на порталы
	private function smart_redirects($filtered_identifier) {

		// сюда мы попадаем уже с валидного роута игры т.е. gf/games/terminator/
		$game_slug = $this->game_slug;

		// проверим все остальное
		$game_slug = $filtered_identifier;

		// TODO: хорошо бы это делать через sphinxsearch
		$sSQL = "(SELECT identifier FROM gf_games WHERE (title LIKE '%{$game_slug}%' OR identifier LIKE '%{$game_slug}%') AND is_hidden = 0 AND (vendor <> '' AND vendor <> 'trymedia') LIMIT 100)".
				"UNION ".
				"(SELECT identifier FROM gf_games WHERE (download01 LIKE '%{$game_slug}%' OR short_description LIKE '%{$game_slug}%' OR subtype LIKE '%{$game_slug}%' OR digest_image LIKE '%{$game_slug}%') AND is_hidden = 0 AND (vendor <> '' AND vendor <> 'trymedia') LIMIT 100) LIMIT 50";

		$ids = $this->conn->getCol( $sSQL );
		
		$ids = array_unique($ids);
		//print_r($ids);
		//exit();

		$this->vendor = 'games/' . $game_slug;
		$this->digest($ids);
		exit;


	}






	function set_search_query($search_query) {

		$search_query = urldecode($search_query);
		$this->search_query = $search_query;
		$this->search_query_for_form = htmlspecialchars($search_query);

	}

	function search() {

		$cl = new SphinxClient();
		$cl->SetServer("localhost", 9312);
		$cl->SetMatchMode(SPH_MATCH_ANY);
		$cl->SetConnectTimeout(4);
		$cl->SetMaxQueryTime(3000);
		$cl->SetSortMode(SPH_SORT_RELEVANCE);
		$cl->_maxmatches = 100;
		$cl->_limit = 50;

		// запишем в лог
		$logger = Logger::singleton();
		$logger->log_event('search', $this->search_query);

		// поиск
		$result = $cl->Query($this->search_query, 'gamefabrique_descriptions_index');

		if (!empty($result) && !empty($result['matches'])) {
			$ids_found = array_keys($result['matches']);
			$this->page_title = 'Search: ' . $this->search_query;
			$this->is_search = true;

			$this->vendor = 'search/' . $this->search_query;
			$this->digest($ids_found, false);
		} else {
			$this->error_404("No results found for \"". $this->search_query ."\"");
			exit;
		}

		//print_r($this->result);

		/*
		if (count($this->result['matches']) > 0) {
			foreach ($this->result['matches'] as $key => &$value) {
				$value['game']= $this->conn->getRow("SELECT id, identifier, title, vendor, short_description, digest_image FROM gf_games WHERE id = ?", array($key));
			}
		}
		*/

		//$this->digest(array('batman-forever','batman-forever', 5938, 5961));

	}


	function set_page($page) {
		$this->paginator_page = intval($page);
	}

	function set_developer_identifier($developer_identifier){
		$this->developer_identifier = $developer_identifier;
	}

	// спикок разработчиков или игры конкретного разработчика
	function developer() {

		$this->initCounters();


		// просто список разработчиков по адресу /developers/
		if (empty($this->developer_identifier)) {

			$this->page_title = "Game Developers";

			$this->developers_list = $this->conn->getAll("SELECT company_name, company_identifier FROM gf_developers ORDER BY company_name");
			$this->yield_me("developers_list.tpl");
			exit();
		}


		$developer = $this->conn->getRow("SELECT id, company_name FROM gf_developers WHERE company_identifier = ?", array($this->developer_identifier));
		$developer_id = $developer['id'];
		$company_name = $developer['company_name'];

		$this->page_title = $company_name . " Games";

		if (empty($developer_id)) {
			$this->error_404();
			exit;
		}

		$game_identifiers = $this->conn->getCol("SELECT DISTINCT identifier FROM gf_games g INNER JOIN gf_developers_games dg ON dg.game_id = g.id WHERE dg.developer_id = ? AND g.is_hidden = 0 AND g.vendor <> 'trymedia'", 0, array($developer_id));

		//$game_identifiers = array_slice($game_identifiers, 0, 20);

		$this->vendor = 'developers/' . $this->developer_identifier;
		$this->digest($game_identifiers);
		//$this->digest(array(2211,225));

	}




	// smart digest
    // digest() - для поиска
	private function digest($game_ids_list=array(), $need_sort_teasers = true) {
/*
		//echo "digest here";
		//print_r($game_ids_list);

		$this->show_paginator = true;
		$this->show_sorting = true;

		$this->sort_mode = 'popular';
		$order_mode = '';

        $this->initCounters();
		$this->loadTeasers();


		// преобразуем цифровые game_id в строковые identifier, если это необходимо
		foreach ($game_ids_list as &$game_id) {
			if (is_integer($game_id)) {
				$game_id = $this->conn->getOne("SELECT identifier FROM gf_games WHERE id = ? LIMIT 1", array($game_id));
			}
		}
		unset($game_id);

		// удалим дубли из строковых идентификаторов
		$game_ids_list = array_unique($game_ids_list);

		// удалим те, у которых is_hidden = 1
		foreach ($game_ids_list as $key => $game_id) {
			$is_hidden = $this->conn->getOne("SELECT is_hidden FROM gf_games WHERE identifier = ? LIMIT 1", array($game_id));
			if ($is_hidden == 1 || is_null($is_hidden)) { // или 1 или вообще нет такой записи в базе
				unset($game_ids_list[$key]);
			}
		}
		unset($game_id);


		//$this->number_of_games = $this->conn->getOne('SELECT count(*) from gf_games WHERE is_hidden = 0 ' . $this->sql_filter); // in selected digest section
		//echo $this->sql_filter;

		$this->number_of_games = count($game_ids_list);

		$this->from = PAGINATOR_GAMES_PER_PAGE * ($this->paginator_page-1);
		$this->to = 0;

		if ($this->paginator_page < ceil(count($game_ids_list) / PAGINATOR_GAMES_PER_PAGE)) {
			$this->is_last_page = false;
		} else {
			$this->is_last_page = true;
		}


		$game_ids_list = array_slice($game_ids_list, ($this->paginator_page-1) * PAGINATOR_GAMES_PER_PAGE, PAGINATOR_GAMES_PER_PAGE);


		if (empty($game_ids_list)) {
			// если наступает, то добавить 404
            // сюда попадаем, например, если неявный поиск не вернул результатов (http://gf/games/wil33 )
            $this->error_404();

		} else {
			
			///$game_ids_string = implode(', ', $game_ids_list);

			$this->games = array();

			if ($need_sort_teasers) {
				// разделяем на два списка
				// в дайджесте у нас тизеры идут сверху
				foreach ($game_ids_list as $identifier) {
					if (in_array($identifier, $this->teasers)) { // если есть тизер
						array_unshift($this->games, array('identifier' => $identifier)); // в начало
					} else {
						array_push($this->games, array('identifier' => $identifier)); // в конец
					}
				}
			} else {
				foreach ($game_ids_list as $identifier) {
					array_push($this->games, array('identifier' => $identifier)); // в конец
				}
			}

			//print_r($this->games);

			$game_model = new Game();
			foreach ($this->games as &$game) {
				//$sSQL = " SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g WHERE identifier ({$game_ids_string}) ";
				$game['games_list'] = $this->conn->getAll("SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g WHERE identifier = ? AND is_hidden = 0", array($game['identifier']));
				$game['game_ids_list'] = array();
				foreach($game['games_list'] as &$g) {

					array_push($game['game_ids_list'], $g['id']);
					
					// 01
					if (empty($game['short_description']) && !empty($g['short_description']))
						$game['short_description'] = $g['short_description'];

					// 02
					if (!empty($g['title']))
						$game['title'] = $g['title'];

					// 03
					if ($g['vendor'] == 'genesis') { // TODO: иногда может быть два варианта игры для genesis (32x), выбираем первый, хотя у него может не быть скриншотов
						if (empty($game['genesis_id'])) {
							$game['genesis_id'] = $g['id'];
						}
					}
					if ($g['vendor'] == 'snes') {
						$game['snes_id'] = $g['id'];
					}
					if ($g['vendor'] == 'nes') {
						$game['nes_id'] = $g['id'];
					}
                    if ($g['vendor'] == 'n64') {
                        $game['n64_id'] = $g['id'];
                    }

                    // находим картинку к игре
                    if (empty($game['main_digest_image']) && !empty($g['digest_image']))	{
                        $game['main_digest_image'] = $g['digest_image'];
                        $game['main_vendor'] = $g['vendor'];
                    }

				} unset($g); // $g end
				
				if (in_array($game['identifier'], $this->teasers)) {
					$game['have_teaser'] = true;
				}				
				
				$game['game_ids_string'] = implode(', ', $game['game_ids_list']);	
				
				$game['similar_games'] = $game_model->get_similar_games($game['game_ids_string'], 2);
				
			} unset($game);


			//echo $sSQL;
			
			//if (!empty($this->games_list)) {
			//	$this->page_title = $this->games_list[0]['title'];
			
			//}

			//if ($this->is_search) {
			//	$this->show_paginator = false;
			//	$this->show_sorting = false;
			//}

			//$this->vendor = "ccc";
			$this->to = 1000;
			
		}


		for($i = 0; $i < count($this->games); $i++){
			
			// all_vendors
			$this->games[$i]['all_vendors'] = $this->conn->getCol("SELECT vendor FROM gf_games WHERE identifier = ? AND is_hidden = 0 AND (vendor <> 'trymedia') ORDER BY vendor LIMIT 3 ", 0, array($this->games[$i]['identifier']));
			
			// have_teasers			
			//if (in_array($this->games_list[$i]['identifier'], $this->teasers) ) {
			//	$this->games_list[$i]['have_teaser'] = true;
			//	$this->games_list[$i]['screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 3,2", array($this->games_list[$i]['id']) );
			//	if (count($this->games_list[$i]['screenshots']) != 2) {
			//		$this->games_list[$i]['screenshots'] = null;
			//	}
			//}
			
		}

        //print_r($this->games);

		// подсчет числа комментариев
		for($i = 0; $i < min(count($this->games[$i]), 5); $i++){
			$this->games[$i]['num_of_comments'] = $this->getNumOfComments($this->games[$i]['game_ids_list'][0]);
		}
		

		//for($i = 0; $i < min(count($this->games), 3); $i++){
		for($i = 0; $i < count($this->games); $i++){
			if ($i < 3 || $this->games['have_teaser'])
			if (!empty($this->games[$i]['genesis_id'])) {
				$this->games[$i]['genesis_screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 3,3", array($this->games[$i]['genesis_id']) );
			}
			if (!empty($this->games[$i]['nes_id'])) {
				$this->games[$i]['nes_screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 3,3", array($this->games[$i]['nes_id']) );
			}
			if (!empty($this->games[$i]['snes_id'])) {
				$this->games[$i]['snes_screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 3,3", array($this->games[$i]['snes_id']) );
			}
			

			//  screenshot theme
			
			//  1
			//  XX YY    nes-snes-genesis
			//  XX ZZ
			//
			
			//  2
			//  ZZ ZZ    genesis-snes
			//  YY YY
			//

			//  3
			//  XX XX    nes-snes
			//  XX XX
			//  YY YY

			//  4
			//  XX XX    nes-genesis
			//  XX XX
			//  ZZ ZZ
			
			//  5
			//  XX XX  genesis

			//  6
			//  XX XX  snes

			//  7
			//  XX XX  nes
			//  XX XX


			if (count($this->games[$i]['nes_screenshots']) >= 2) {
				$this->games[$i]['screenshot_theme'] = 7;
			}

			if (count($this->games[$i]['snes_screenshots']) >= 2) {
				$this->games[$i]['screenshot_theme'] = 6;
			}

			if (count($this->games[$i]['genesis_screenshots']) >= 2) {
				$this->games[$i]['screenshot_theme'] = 5;
			}

			if (count($this->games[$i]['nes_screenshots']) >= 2 && count($this->games[$i]['genesis_screenshots']) >= 2) {
				$this->games[$i]['screenshot_theme'] = 4;
			}

			if (count($this->games[$i]['nes_screenshots']) >= 2 && count($this->games[$i]['snes_screenshots']) >= 2) {
				$this->games[$i]['screenshot_theme'] = 3;
			}

			if (count($this->games[$i]['snes_screenshots']) >= 2 && count($this->games[$i]['genesis_screenshots']) >= 2) {
				$this->games[$i]['screenshot_theme'] = 2;
			}
			
			if (count($this->games[$i]['genesis_screenshots']) > 0 && count($this->games[$i]['nes_screenshots']) > 0 && count($this->games[$i]['snes_screenshots']) > 0) {
				$this->games[$i]['screenshot_theme'] = 1;
			}

		}

		for($i = 0; $i < count($this->games); $i++) {
			if (!$this->games[$i]['have_teaser'] && empty($this->games[$i]['screenshot_theme']) ) {
				if (!empty($this->games[$i]['genesis_id']))
					$this->games[$i]['screenshots'] = $this->conn->getAll("SELECT name, 'genesis' vendor FROM gf_screenshots WHERE game_id = ? AND user_id = 4 AND auto_added = 1 ORDER BY position, id LIMIT 1,3", array($this->games[$i]['genesis_id']) );
				if (empty($this->games[$i]['screenshots']) && !empty($this->games[$i]['snes_id']))
					$this->games[$i]['screenshots'] = $this->conn->getAll("SELECT name, 'snes' vendor FROM gf_screenshots WHERE game_id = ? AND user_id = 4 AND auto_added = 1 ORDER BY position, id LIMIT 1,3", array($this->games[$i]['snes_id']) );
				if (empty($this->games[$i]['screenshots']) && !empty($this->games[$i]['nes_id']))
					$this->games[$i]['screenshots'] = $this->conn->getAll("SELECT name, 'nes' vendor FROM gf_screenshots WHERE game_id = ? AND user_id = 4 AND auto_added = 1 ORDER BY position, id LIMIT 1,3", array($this->games[$i]['snes_id']) );
			}
		}
		

        // ссылки на скачивание (основная ссылка в смарт-дайджесте)
        foreach($this->games as &$digest_element) {

            $download_link = false;
            $vendor = false;
            foreach($digest_element['games_list'] as &$game) {

                if ($download_link == false && !empty($game['download01']) && Helpers::endsWith(basename($game['download01']), '.exe') ) {
                    $download_link = $game['download01'];
                    $vendor = $game['vendor'];
                }

            }

            if (!empty($download_link)) {
                $digest_element['download'] = Helpers::get_game_link_and_size($download_link, $vendor, $game['identifier']);
            }

        } unset($digest_element);

        // title_nodashes
        foreach($this->games as &$game) {
            $game['title_nodashes'] = $this->get_no_dashes($game['title']);
        } unset($game);


		$this->from += 1;
		$this->to = $this->from + count($this->games) - 1;


		//$this->from += 15;
		//$this->to = $this->from + count($this->games_list) - 1;

		//print_r($this->games);
		
		$this->yield_me('smart_digest.tpl');
*/
	}	



	

    private function get_no_dashes($title) {
        $title_nodashes = $title;
        $title_nodashes = str_replace('-', ' ', $title_nodashes);
        $title_nodashes = str_replace("'", '', $title_nodashes);
        $title_nodashes = str_replace(':', ' ', $title_nodashes);
        $title_nodashes = str_replace('   ', ' ', $title_nodashes);
        return $title_nodashes;
    }


	function add_comment_points($user_id, $comment_id, $mark) {
		if ($mark != '+' && $mark != '-')
			return;

		$user_exists = $this->conn->getOne("SELECT id FROM gf_users WHERE id = ?", array($user_id));
		$comment_exists = $this->conn->getOne("SELECT id FROM gf_comments WHERE id = ?", array($comment_id));
		
		if (empty($user_exists) || empty($comment_exists))
			return;
			
		$old_point = $this->conn->getRow("SELECT * FROM gf_comment_points WHERE user_id = ? AND comment_id = ?", array($user_id, $comment_id));
		if (!empty($old_point)) {
			if ($old_point['mark'] == $mark)
				return $this->conn->getOne("SELECT points FROM gf_comments WHERE id = ?", array($comment_id));;
			
			$this->conn->query("DELETE FROM gf_comment_points WHERE user_id = ? AND comment_id = ?", array($user_id, $comment_id));
			
			if ($old_point['mark'] == '+') {
				$this->conn->query("UPDATE gf_comments SET points = points - 1 WHERE id = ?", array($comment_id));
			} else {
				$this->conn->query("UPDATE gf_comments SET points = points + 1 WHERE id = ?", array($comment_id));
			}
		}
		
		$this->conn->query("INSERT INTO gf_comment_points(mark, comment_id, user_id, created_at) VALUES (?, ?, ?, NOW()) ", array($mark, $comment_id, $user_id ) );
		//$this->conn->getOne("SELECT LAST_INSERT_ID()");
		
		if ($mark == '+') {
			$this->conn->query("UPDATE gf_comments SET points = points + 1 WHERE id = ?", array($comment_id));
		} else {
			$this->conn->query("UPDATE gf_comments SET points = points - 1 WHERE id = ?", array($comment_id));
		}
		
		return $this->conn->getOne("SELECT points FROM gf_comments WHERE id = ?", array($comment_id));
	}


	function plus_comment_vote() {
		$comment_id = (integer)substr($_POST['comment_id'], strlen('voter-for-comment:'));
		echo $this->add_comment_points(4, $comment_id, '+');
	}


	function minus_comment_vote() {
		$comment_id = (integer)substr($_POST['comment_id'], strlen('voter-for-comment:'));
		echo $this->add_comment_points(4, $comment_id, '-');
	}


	function get_categories($game_ids) {
		return $this->conn->getAll("SELECT DISTINCT c.name, c.url FROM gf_categories c INNER JOIN gf_categories_games cg ON c.id=cg.category_id WHERE cg.game_id IN ({$game_ids})" );
	}


	function main_page() {
		//$this->page_title = 'Gamefabrique Games';
		$this->setPageTitle("Download Games - Game Downloads on Games For Win");
		//$this->page_slogan = 'Over 9000 free and abandonware games';
		
        $this->initCounters();
/*
		$this->games_list = $this->conn->getAll("SELECT id, title, identifier, download01, short_description, digest_image, vendor FROM gf_games g WHERE is_hidden = 0 AND identifier IN('road-rash', 'road-rash-3', 'ultimate-mortal-kombat-3', 'snow-bros', 'super-mario-bros', 'incredible-hulk', 'worms', 'sonic-the-hedgehog', 'theme-park', 'cannon-fodder', 'streets-of-rage', 'castlevania-the-new-generation', 'michael-jacksons-moonwalker', 'super-bomberman', 'aladdin', 'lion-king' ) GROUP BY(identifier) ");

		// lastest descriptions
		$games = array();

		$sSQL = 'SELECT DISTINCT game_id FROM gf_game_descriptions d '.
				'JOIN gf_games g ON g.id = d.game_id '.
				'WHERE d.game_id <> 0 AND g.is_hidden = 0 ORDER BY d.id DESC LIMIT 10';
		$game_ids = $this->conn->getCol($sSQL);

		foreach ($game_ids as $id) {

			$game = $this->conn->getRow("SELECT identifier, title, short_description FROM gf_games WHERE id = ?", array($id));

			if (!empty($game)) {
			}
		}

		$this->latest_reviews = $games;
*/

		$index_games = array();
/*		$games_list = array('road-rash',
							'aladdin',
							'sonic-and-knuckles',
							'battletech',
							'adventures-of-batman-and-robin',
							'urban-strike',
							'contra-hard-corps',
							'taz-mania',
							'nhl-95',
							'maximum-carnage',
							'jurassic-park-rampage-edition',
							'wwf-raw');
*/
        $games_list = array(
            'mortal-kombat-4',
            'road-rash',
            'lion-king',
            'beach-head-2002',
            'virtua-cop-2',
            'kasparov-chessmate',
            'virtua-cop-2-arcade',
            'rollercoaster-tycoon-2',
            'beach-head-2000',
            'virtua-cop',
            'sonic-r',
            'risk-2',
            'commandos-2-men-of-courage',
            'house-of-the-dead',
            'street-fighter-3',
            'aladdin',
            'commandos-behind-enemy-lines',
            'mortal-kombat-2',
            'worms-armageddon',
            'doraemon',
            'sonic-fighters',
            'jungle-book',
            'superman',
            'worms-3d',
            'snail-mail',
            'lemonade-tycoon',
            'double-dragon',
            'tomb-raider-3-adventures-of-lara-croft',
            'time-crisis',
            'time-crisis-2',
            'chicken-hunter',
            'war-chess',
            'sonic-the-hedgehog',
            'tekken-tag-tournament',
            'tiny-toon-adventures-busters-hidden-treasure',
            'family-feud-holiday-edition',
            'marvel-vs-capcom-2',
            'rival-schools-united-by-fate',
            'battle-for-troy',
            'carnivores-2'
        );


		foreach ($games_list as $game_identifier) {
			$gg = new Game($game_identifier);
			array_push($index_games, $gg->getInfo());
		}

		$this->index_games = $index_games;




		// новости
		// TODO: рефакторинг
		$sSQL = "".
			" SELECT newsid, n.gameid, comment".
			", DATE_FORMAT(newsdate,\"%d\") tday, SUBSTRING(MONTHNAME(newsdate),1,3) tmonth, YEAR(newsdate) tyear FROM news n LEFT JOIN games g ON g.gameid = n.gameid WHERE g.hidden='N' OR g.gameid is null ".
			" ORDER BY newsdate DESC LIMIT 0,20";
		$allnews = $this->conn->getAll($sSQL);

		for($i=0; $i<count($allnews); $i++) {
			$p = strpos($allnews[$i]['comment'], '#');
			if(false!==$p && !empty($allnews[$i]['gameid']))
			{
				$stringid = $this->conn->getOne("SELECT stringid FROM games WHERE gameid = ?", array($allnews[$i]['gameid']));
				$replacestring = '/games/';
				$allnews[$i]['comment'] = str_replace('#', $replacestring.$stringid.'/', $allnews[$i]['comment']);
			}
		}

		//print_r($allnews);
		$this->allnews = $allnews;



		$this->yield_me('main_content.tpl');
	}


	// получение информации о игре, количестве скриншотов, комментариях итд.
    // все, что в селекторе итд.
	// вызывается перед index и остальными страницами
	private function get_stats() {

		$game_slug = $this->game_slug;

		//$this->ids = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ? AND is_hidden = 0", 0, array($game_slug));
		$this->ids = $this->getAllGameIds($game_slug);

		if (count($this->ids) == 0) {
			$this->error_404();
			exit;
		}

		$this->ids_string = $this->getAllGameIdsAsString($game_slug);


		//$this->games = $this->conn->getAll("SELECT * FROM gf_games WHERE id IN ({$this->ids_string})");
        $this->games = array();

        $this->games_list = $this->conn->getAll("SELECT * FROM gf_games g WHERE is_hidden = 0 AND id IN ({$this->ids_string}) ORDER BY vendor, id");

		//$this->game_title = $this->games[0]['title'];
		//print_r($this->games_list);

        $this->game_title = "";
        foreach ($this->games_list as &$game) {
            if (empty($this->game_title) && !empty($game['title']) ) {
                $this->game_title = $game['title'];
            }
        } unset($game);

        $this->short_description = "";
        foreach ($this->games_list as &$game) {
            if (empty($this->short_description) && !empty($game['short_description']) ) {
                $this->short_description = $game['short_description'];
            }
        } unset($game);

		$this->number_of_comments = $this->conn->getOne("SELECT count(*) FROM gf_comments WHERE game_id in ({$this->ids_string})");
		$this->number_of_screenshots = $this->conn->getOne("SELECT count(*) FROM gf_screenshots WHERE user_id = 4 AND game_id IN ({$this->ids_string})");
        $this->number_of_cheats = $this->conn->getOne("SELECT count(*) FROM cheats WHERE game_id in ({$this->ids_string})");

        $this->initCounters();

		// digest image
		foreach ($this->games_list as &$game) {
			if (!empty($game['digest_image'])) {
				if (empty($this->games['vendor']) || $this->games['vendor'] == 'nes') { // we can rewrrite empty slot or nes slot only (выбираем любую digest_image лучше nes
					$this->games['vendor'] = $game['vendor'];
					$this->games['digest_image'] = $game['digest_image'];
				}
			}
		}
        unset($game);

		if (!empty($this->games['vendor']) && !empty($this->games['digest_image'])) {
			$this->games['digest_image_url'] = sprintf("/i/%s/%s", $this->games['vendor'], $this->games['digest_image']);
		} else {
			$this->games['digest_image_url'] = "/images/no_logo.png";
		}


	}




	function existing_game() {


		//$this->get_stats();

		/*
		//print_r($this->games);

		// этот тег устанавливается только для индекса
		$this->detailed_game_page = true;

		//$this->game_id = $this->game['id'];

		// screenshots preview
		$this->screenshots = $this->conn->getAll("SELECT ss.*, g.vendor FROM gf_screenshots ss INNER JOIN gf_games g ON g.id = ss.game_id WHERE game_id IN ({$this->ids_string}) AND user_id = 4 ORDER BY FIELD(vendor, 'genesis', 'snes', 'n64', 'sms', 'nes', 'trymedia', '')");

		// developers
		$this->developers = $this->conn->getAll("SELECT DISTINCT company_name, company_identifier FROM gf_developers d INNER JOIN gf_developers_games dg ON d.id = dg.developer_id WHERE dg.game_id IN ({$this->ids_string})");

		// download link
		$this->first_url = "";
		foreach ($this->games_list as &$game) {
			$game['download_url'] = Helpers::get_game_link_and_size($game['download01'], $game['vendor'], $game['identifier']);

			if (empty($this->first_url) && $game['vendor'] != 'trymedia' && !empty($game['download_url'])) {
				$this->first_url = $game['download_url']['url'] ;
			}
		}
		unset($game);

		// have teaser?
		if (file_exists("./bigimages/{$this->game_slug}.jpg")) {
			$this->have_teaser = true;
		}

		// have poster?
		if (file_exists("./images/posters/small/{$this->game_slug}.jpg")) {
			$this->have_poster = true;
			$this->poster_filename_small = "/images/posters/small/{$this->game_slug}.jpg";
			$this->poster_filename_medium = "/images/posters/medium/{$this->game_slug}.jpg";
		}

		// cut screenshots
		if (count($this->screenshots) >= 4) {
			//if ($this->games['vendor'] == 'nes') {
			//	$this->screenshots = array_slice($this->screenshots, 1, 5); // 5
			//} else {
			if ($this->have_poster) {
				$this->screenshots = array_slice($this->screenshots, 1, 6); // 4
			} else {
				$this->screenshots = array_slice($this->screenshots, 1, 4); // 4
			}
			//}
		} else $this->screenshots = array();


		// game descriptions
		$this->game_descriptions = $this->conn->getAll("SELECT gd.body, gd.platform, gd.game_id, u.login, gd.user_id, u.name from gf_game_descriptions gd INNER JOIN gf_users u ON u.id=gd.user_id WHERE game_id IN ({$this->ids_string}) AND gd.is_hidden = 0 ORDER BY gd.is_selected DESC, gd.created_at DESC");
		//print_r($this->game_descriptions);
		if(!empty($this->game_descriptions) && !empty($this->game_descriptions[0]) && $this->game_descriptions[0]['user_id'] == '4' ) {

			$this->gamefabrique_description = $this->game_descriptions[0];

			// выделение первого абзаца в описаниях (этот код заработает если его раскоментировать)
			//$paragraphs = explode("\r\n\r\n", $this->gamefabrique_description['body']);
			//if (count($paragraphs) >= 4) {
			//	$this->gamefabrique_description['synopsys'] = $paragraphs[0];
			//	$this->gamefabrique_description['body'] = implode("\r\n\r\n", array_slice( $paragraphs, 1 ) );
			//}

			//print_r( explode("\r\n\r\n", $this->gamefabrique_description['body']) );
			//$this->gamefabrique_description['body_markdown'] = Helpers::markdown($this->gamefabrique_description['body']);

			$this->game_descriptions = array_slice($this->game_descriptions, 1);
		}

		foreach ($this->game_descriptions as &$description) {
			if (!empty($description['game_id'])) {
				$game = array();
				if (empty($description['platform'])) {
					$game = $this->conn->getRow("SELECT vendor, title, download01 FROM gf_games WHERE id = ? AND is_hidden = 0 LIMIT 1", array($description['game_id']));

				} else {
					$game = $this->conn->getRow("SELECT vendor, title, download01 FROM gf_games WHERE identifier = ? AND is_hidden = 0 AND vendor = ? LIMIT 1", array($this->game_slug, $description['platform']));
				}
				if (!empty($game)) {
					$description['title'] = $game['title'];
					$description['platform'] = $game['vendor'];
					$description['download_url'] = Helpers::get_game_link_and_size($game['download01'], $description['platform'], $this->game_slug);
				}

			}
		}
		unset($description);

		//print_r($this->game_descriptions);

//		$comments = $this->conn->getAll("SELECT c.*, u.login, u.name FROM gf_comments c INNER JOIN gf_users u ON u.id = c.user_id WHERE c.game_id = '{$game_id}' AND {$parent} ORDER BY created_at ");
		$this->selected_comments = $this->conn->getAll("SELECT c.*, u.login, u.name FROM gf_comments c INNER JOIN gf_users u ON u.id = c.user_id WHERE is_selected = 1 AND game_id IN ({$this->ids_string})");

		$this->categories = $this->get_categories($this->ids_string);
		//print_r($this->categories);

		// page title
		$this->page_title = $this->game_title . ' Game Download for PC';

		//$this->games['title'] = $this->games[0]['title'];
		$this->title_nodashes = $this->get_no_dashes($this->game_title);

		//$this->games_list = $this->conn->getAll("SELECT id, title, identifier, download01, short_description, digest_image, vendor FROM gf_games g WHERE is_hidden = 0 AND id IN({$this->ids_string}) ORDER BY vendor, id"); // игры на этой странице
		//$this->games_list = $this->conn->getAll("SELECT * FROM gf_games g WHERE is_hidden = 0 AND id IN({$this->ids_string}) ORDER BY vendor, id"); // игры на этой странице
		//$this->games[0]['download01'] = $this->get_game_link_and_size($this->games[0]['download01'], $this->games[0]['vendor']);

		foreach($this->games_list as $g) {
			if (empty($this->main_short_description) && !empty($g['short_description']))	{
				$this->main_short_description = $g['short_description'];
			}
			if (empty($this->main_digest_image) && !empty($g['digest_image']))	{
				$this->main_digest_image = $g['digest_image'];
				$this->main_vendor = $g['vendor'];
			}
		}
		unset($g);

		//$this->developers = $this->conn->getRow("SELECT d.company FROM gf_developers d INNER JOIN gf_developers_games dg ON d.id = dg.developer_id WHERE dg.game_id = ?", array($this->game_id));

		//print_r($this->game->get_any_digest_image('alien-3ww'));

		// similar games
		$this->similar_games = $this->game_model->get_similar_games($this->ids_string);
		//print_r($this->similar_games);
		//176, 252, 2647
		//print_r($this->game_model->get_similar_games("176, 252, 2647"));


		// если нет similar, покажем пару игр от разработчика
		if (count($this->similar_games) == 0 && count($this->developers) > 0) {

			// сгруппируем всех разработчиков по количеству их игр
			$sSQL =
				"SELECT count(*) c, dg.developer_id, d.company_name FROM gf_games g ".
				"INNER JOIN gf_developers_games dg ON dg.game_id = g.id ".
				"INNER JOIN gf_developers d ON d.id = dg.developer_id  ".
				"WHERE dg.developer_id IN (SELECT d.id FROM gf_developers d JOIN gf_developers_games dg ON dg.developer_id = d.id WHERE dg.game_id IN (?)) ".
				"GROUP BY dg.developer_id ".
				"ORDER BY c DESC ";

			$developer_ids = $this->conn->getAll($sSQL, array($this->ids_string));
			$this->games_by_same_developer_devname = $developer_ids[0]['company_name'];

			// найдем все игры первого в списке разработчика
			// Other games by XXX
			$sSQL =
				//"SELECT DISTINCT identifier, title, short_description, vendor, digest_image FROM gf_games g ".
				"SELECT DISTINCT identifier FROM gf_games g ".
				"INNER JOIN gf_developers_games dg ON g.id = dg.game_id ".
				"WHERE g.short_description IS NOT NULL AND g.short_description <> '' AND identifier <> ? AND digest_image IS NOT NULL AND digest_image <> '' AND is_hidden = 0 AND dg.developer_id = ? LIMIT 0,6";

			$game_identifiers_by_same_developer = $this->conn->getCol($sSQL, 0, array($this->game_slug, $developer_ids[0]['developer_id']));

			// если в списке 4 или 5 игр, обрежем до 3
			if (count($game_identifiers_by_same_developer) == 4 || count($game_identifiers_by_same_developer) == 5) {
				$game_identifiers_by_same_developer = array_slice($game_identifiers_by_same_developer, 0, 3);
			}

			$this->games_by_same_developer = array();
			foreach($game_identifiers_by_same_developer as $identifier) {

				array_push( $this->games_by_same_developer,
					$this->conn->getRow("SELECT identifier, title, short_description, vendor, digest_image FROM gf_games WHERE identifier = ? LIMIT 1", array($identifier))
				);
			}
			unset($identifier);

			//$this->conn->getAll($sSQL, array($this->game_slug, $developer_ids[0]['developer_id']));

			//print_r($game);
			//if (!empty($game)) {
			//    $game['digest_image'] = $this->get_any_digest_image($game['identifier']);
			//    array_push($games, $game);
			//}


		}
*/

		try {
			$game = new GameFull($this->game_slug);

			//$game->getFullInfo();
			$this->game = $game->getInfo();

			//$this->setPageTitle($game->getGameTitle() . ". Download and Play " . $game->getGameTitle() . " Game");
            //$this->setPageTitle($game->getGameTitle() . " Download on Games4Win",  false);
			$this->setPageTitle($game->getGameTitle() . " Free Download full game for PC, review and system requirements",  true);

			$this->user_rating = $this->getUserGameRating($this->game_slug);

			$this->template = 'existing_game';

			//$this->setPageMetatag($this->game['shortdesc']);
			$this->setPageMetaDescription("Free Download " . $game->getGameTitle() . " full game for windows, review and system requirements on " . $game->getGameTitle() . " for PC. Play it now!");

		} catch (NotFoundException $e) {
			$main_controller = new MainController();
			$main_controller->error_404();
		}  catch (GameIsHiddenException $e) {
			$main_controller = new MainController();
			$main_controller->error_410_gone();
		}



		$this->topline = 'gamedesc';

		// slidebox manipulations
		/*
		$this->show_game_slidebox = true;
		$slidebox_ids = array(74, 320, 751, 32, 46, 12, 501, 101, 137, 45, 78, 23, 1074 );
		$selected_game_id = $slidebox_ids[rand(0, count($slidebox_ids) - 1)];
		$this->slidebox_game = $this->conn->getRow("SELECT id, vendor, title, identifier, digest_image FROM gf_games WHERE id = ?", array($selected_game_id));
		*/

		//print_r($this->games_list);
		$this->yield_me('longdesc.tpl');

	}



	function forum() {
		$this->get_stats();

		$this->comment_tree = array();
		foreach ($this->ids as $game_id) {
			array_push($this->comment_tree, $this->get_comments_tree($game_id));
		}
		//print_r($this->comment_tree);

		//TODO:  в шаблоне неверно отображается, если нет комментов и расскомментировать код сообщения о том, что нет комментов
		
		$this->page_title = $this->game_title . ' Forum';
		$this->yield_me('game_forum.tpl');
	}



	function screenshots() {

		$this->get_stats();

		$this->game = $this->conn->getRow("SELECT * FROM gf_games WHERE identifier = ?", array($this->game_slug));
		//print_r($this->game);
		
		$this->screenshots = array();
		//print_r($this->ids);
		foreach ($this->ids as $game_id) {
			$ss = $this->conn->getAll("SELECT * FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id", array($game_id) );
			
			$game = $this->conn->getRow("SELECT * FROM gf_games WHERE id = ?", array($game_id));
			$divider = 0;
			
			if ($game['vendor'] != 'nes') {
				for($i = 1; $i < count($ss); $i++) {
					if (empty($ss[$i]['title']) ) {
						$divider = $i;
						break;
					}
					$divider = $i;
				}
				
				if ($divider >= 3) 
					$divider = 3;
					
			} else $divider = 1;
			
			$ss1 = array_slice($ss, 0, $divider);
			$ss2 = array_slice($ss, $divider);

			array_push($this->screenshots,  array('ss1' => $ss1, 'ss2' => $ss2, 'game' => $game, 'count' => count($ss)) );
		}
		//print_r($this->screenshots);

		$this->page_title = $this->game['title'] . ' Screenshots';
		$this->yield_me('game_screenshots.tpl');
	}
	

	function downloads() {
		$this->get_stats();
		//echo 'xxx';
		
		//$this->games = $this->conn->getAll("SELECT * FROM gf_games WHERE identifier = '{$this->game_slug}' AND is_hidden = 0 ORDER BY vendor");

        $this->title_nodashes = $this->get_no_dashes($this->game_title);

		$this->first_url = "";

        $this->link_counter = 0;
		foreach ($this->games_list as &$game) {

			$game['screenshots'] = $this->conn->getAll("SELECT * FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 1,3", array($game['id']) );

            if (!empty($game['download01'])) {
                $this->link_counter++;
            }

			$game['download01'] = Helpers::get_game_link_and_size($game['download01'], $game['vendor'], $game['identifier']); //echo($game['download02']['url']);
            //if (!empty($game['download02'])) {
            //    $game['download_link'] = $game['download02']['url'];
            //}
			if (empty($this->first_url) && $game['vendor'] != 'trymedia') {
				$this->first_url = 'http://www.gamefabrique.com'.$game['download01']['url'] ;
			}

		}
        unset($game);

		$this->page_title = $this->games_list[0]['title'] . ' Download';

		$this->yield_me('game_downloads.tpl');
	}



    function snes_download() {
		$this->get_stats();

		$this->games = $this->conn->getAll("SELECT * FROM gf_games WHERE identifier = '{$this->game_slug}' AND vendor = 'snes' AND is_hidden = 0 ORDER BY vendor");

        if (count($this->games) == 0) {
            $this->error_404();
			exit;
        }

		$this->first_url = "";

		foreach ($this->games as &$game) {
			$game['screenshots'] = $this->conn->getAll("SELECT * FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 1,3", array($game['id']) );
			$game['download01'] = Helpers::get_game_link_and_size($game['download01'], $game['vendor'], $game['identifier']);
			if (empty($this->first_url) && $game['vendor'] != 'trymedia') {
				$this->first_url = 'http://www.gamefabrique.com'.$game['download01']['url'] ;
			}
		}

		$this->page_title = $this->games[0]['title'] . ' SNES Download';

		$this->yield_me('game_downloads_direct.tpl');
    }


    function nes_download() {
		$this->get_stats();

		$this->games = $this->conn->getAll("SELECT * FROM gf_games WHERE identifier = '{$this->game_slug}' AND vendor = 'nes' AND is_hidden = 0 ORDER BY vendor");

        if (count($this->games) == 0) {
            $this->error_404();
			exit;
        }

		$this->first_url = "";

		foreach ($this->games as &$game) {
			$game['screenshots'] = $this->conn->getAll("SELECT * FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 1,3", array($game['id']) );
			$game['download01'] = Helpers::get_game_link_and_size($game['download01'], $game['vendor'], $game['identifier']);
			if (empty($this->first_url) && $game['vendor'] != 'trymedia') {
				$this->first_url = 'http://www.gamefabrique.com'.$game['download01']['url'] ;
			}
		}

		$this->page_title = $this->games[0]['title'] . ' NES Download';

		$this->yield_me('game_downloads_direct.tpl');
    }


    function genesis_download() {
		$this->get_stats();

		$this->games = $this->conn->getAll("SELECT * FROM gf_games WHERE identifier = '{$this->game_slug}' AND vendor = 'genesis' AND is_hidden = 0 ORDER BY vendor");

        if (count($this->games) == 0) {
            $this->error_404();
			exit;
        }

		$this->first_url = "";

		foreach ($this->games as &$game) {
			$game['screenshots'] = $this->conn->getAll("SELECT * FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 1,3", array($game['id']) );
			$game['download01'] = Helpers::get_game_link_and_size($game['download01'], $game['vendor'], $game['identifier']);
			if (empty($this->first_url) && $game['vendor'] != 'trymedia') {
				$this->first_url = 'http://www.gamefabrique.com'.$game['download01']['url'] ;
			}
		}

		$this->page_title = $this->games[0]['title'] . ' Genesis Download';

		$this->yield_me('game_downloads_direct.tpl');
    }

    
	function cheats() {
		$this->get_stats();
		
		//$this->game = $this->conn->getRow("SELECT * FROM gf_games WHERE identifier = '{$this->game_slug}'");
		//$this->number_of_comments = $this->conn->getOne("SELECT count(*) FROM gf_comments WHERE game_id='{$this->game['id']}' ");
		
		$this->games_list = $this->conn->getAll("SELECT id, title, identifier, download01, short_description, digest_image, vendor FROM gf_games g WHERE is_hidden = 0 AND id IN({$this->ids_string}) ORDER BY vendor, id"); // игры на этой странице

        //$this->game = $this->conn->getRow("SELECT * FROM gf_games WHERE identifier = '{$this->game_slug}'");
        foreach ($this->games_list as &$game) {
            $game['cheats'] = $this->conn->getAll("SELECT * FROM cheats WHERE game_id = ?", array($game['id']) );
        }


		$this->have_cheats = false;
		foreach($this->games_list as $g) {
			if (!empty($g['cheats'])) {
				$this->have_cheats = true;
			}
		}

		
		$this->page_title = $this->game_title . ' Cheats';
		$this->yield_me('game_cheats.tpl');
	}
	

/*
	private function get_game_link_and_size($url, $vendor) {

		if (empty($url) || empty($vendor))
			return null;

		if ($vendor == 'genesis' || $vendor == 'nes' || $vendor == 'snes' || $vendor == 'n64' || $vendor == 'sms') {
			$base = basename($url);
			$path = "/download/{$vendor}/{$base}";

			if (file_exists(".{$path}")) {
				$size = filesize(".{$path}");

                return array('url' => $path, 'size' => $size);
			}

			return null; // not exists, у нас только внутренние ссылки

		} else {
			return array('url' => $url);
		}
	}
*/


	private function get_comments_tree($game_id, $deep = 0, $parent_id = null) {
		$parent = '';
		if ($parent_id == null) {
			$parent = " c.parent_id IS NULL ";
		} else {
			$parent = " c.parent_id = '{$parent_id}' ";
		}
		
		$comments = $this->conn->getAll("SELECT c.*, u.login, u.name FROM gf_comments c INNER JOIN gf_users u ON u.id = c.user_id WHERE c.game_id = '{$game_id}' AND {$parent} ORDER BY created_at ");
		foreach ($comments as &$c) {
			$c['time_since'] = Helpers::time_since($c['created_at']);
			$c['deep'] = $deep;
			//$c['comment_markdown'] = Helpers::markdown($c['comment_text']);

			$result[$c['id']] = $c;
			
			
			$p_id = $c['id'];
			$children_count = $this->conn->getOne("SELECT count(*) FROM gf_comments WHERE parent_id = '{$p_id}'");
			
			if ($children_count > 0) {
				$sub_results = $this->get_comments_tree($game_id, $deep + 1, $p_id);
				foreach($sub_results as &$sr) {
					$result[$sr['id']] = $sr;
				}
			}
			
		}
		
		return $result;
	}

	private function init_main_menu() {

		//$lang = $params['lang'];

		$sids = $this->conn->getOne("SELECT value FROM settings WHERE name = 'featured'");

		$featured = array();
		$lines = explode("\r\n", $sids);

		for($i=0; $i < count($lines); $i++) {
			$data = $this->conn->getRow("SELECT title AS content, stringid AS link FROM games WHERE stringid = ?", array($lines[$i]));
			if (!empty($data)) {
				array_push($featured, $data);
			}
		}


		$this->gameblock = $featured;
		//print_r($smarty);

		//$smarty->assign('gameblocktitle','Featured games [<a href="/featured-games/" title="Download featured games">all</a>]');
		//$smarty->assign('showfeatured', true);

		//
		//$result = $smarty->fetch('gameblock.tpl');

		//return $result;
	}




	function game_vote() {
		//$comment_id = (integer)substr($_POST['comment_id'], strlen('voter-for-comment:'));
		if (empty($_POST)) { return; }
		if (empty($_POST['stars']) || empty($_POST['game'])) {
			echo "1";
			return;
		}

		if (!in_array($_POST['stars'], array('star_1', 'star_2', 'star_3', 'star_4', 'star_5')) ) {
			echo "2";
			return;
		}

		if ($this->conn->getOne("SELECT count(*) FROM gf_games WHERE identifier = ?", array($_POST['game'])) == 0) {
			echo "3";
			return;
		}

		$game_identifier = $_POST['game'];
		switch ($_POST['stars']) {
			case 'star_1' : $user_rating = 2; $star_rating = 1; break;
			case 'star_2' : $user_rating = 4; $star_rating = 2; break;
			case 'star_3' : $user_rating = 6; $star_rating = 3; break;
			case 'star_4' : $user_rating = 8; $star_rating = 4; break;
			case 'star_5' : $user_rating = 10; $star_rating = 5; break;
		}

		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$client_ip = getRemoteIPAddress();
		$numeric_ip = ip2long($client_ip);

		$previous_vote = $this->get_user_vote($game_identifier);
		if ($previous_vote != null) {
			echo "Already voted " . $previous_vote. " stars";
			return;
		}

		$this->conn->query("INSERT INTO user_votes(game_identifier, user_rating, client_ip, numeric_ip, created_at, user_agent) values (?,?,?,?, NOW(), ?)",
			array($game_identifier, $user_rating, $client_ip, $numeric_ip, $user_agent));

		if ($star_rating == 1) {
			echo "1 star";
		} else {
			echo $star_rating . " stars";
		}
		//echo(print_r($_POST,true));
		//echo($_POST);
		//echo("xxxx");
	}





	function get_user_vote($string_id) {
		$client_ip = getRemoteIPAddress();
		$numeric_ip = ip2long($client_ip);

		$requests_count = $this->conn->getOne("SELECT count(*) FROM user_votes v WHERE v.created_at > NOW() - INTERVAL 1 DAY AND v.numeric_ip = ? AND game_identifier = ?",
			array($numeric_ip, $string_id));

		if ($requests_count > 0) {
			$previous_vote = $this->conn->getOne("SELECT user_rating FROM user_votes v WHERE v.created_at > NOW() - INTERVAL 1 DAY AND v.numeric_ip = ? AND game_identifier = ? LIMIT 1",
				array($numeric_ip, $string_id));
			return($previous_vote/2);
		} else {
			return null;
		}
	}




}

