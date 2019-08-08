<?


class DigestController extends ApplicationController {


	var $paginator_page = 1;

	var $portals;
	var $portals_list;
	var $vendor;
	var $sql_filter;
	var $rel_prev = '', $rel_next = '';
	var $show_full_page; // показывать ли полную страницу или только данные для аякс-запроса

	var $page = null;

	var $games_per_page = 100;
	var $pageoptions;

	var $search_query;


	function __construct() {
		parent::__construct();
		$this->pageoptions = array();
		$this->paginator_page = 1;
	}


	function set_page($page) {
		$this->paginator_page = intval($page);
	}

	function set_year($year) {
		if ($year >= 1991 && $year <= 2019) {
			$this->year = intval($year);
		}
	}


	function redirect_from_all() {
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		if (empty($this->paginator_page)) {
			header('Location: /games/', TRUE, 301);
		} else {
			header('Location: /games/'.$this->paginator_page.'/', TRUE, 301);
		}
		exit();
	}



	function portal() {

        $this->initCounters();

		$this->page_title = "Game Portal";

		// старая версия (две таблицы: portals + portals_games
		$sSQL = 'SELECT p.id, p.title, p.identifier, p.description, p.keywords, g.vendor, g.digest_image FROM portals p INNER JOIN gf_games g WHERE p.game_id = g.id ORDER BY position';
		$this->portals_list = $this->conn->getAll($sSQL);

		for($i = 0; $i < min(count($this->portals_list), 10); $i++){
			$portal_id = $this->portals_list[$i]['id'];
			$this->portals_list[$i]['portal_games'] = $this->conn->getAll("SELECT g.id, g.title, g.identifier, g.vendor, g.digest_image, (SELECT name FROM gf_screenshots s WHERE s.game_id=g.id AND auto_added = 1 ORDER BY position, id LIMIT 1) nes_screenshot FROM gf_games g INNER JOIN portals_games pg ON g.id=pg.game_id WHERE pg.portal_id = ? LIMIT 4", array($portal_id));
			
			
			$this->portals_list[$i]['link_list'] = $this->conn->getAll("SELECT g.id, g.title, g.identifier, g.vendor, g.digest_image FROM gf_games g INNER JOIN portals_games pg ON g.id=pg.game_id WHERE pg.portal_id = ? LIMIT 4, 3", array($portal_id));
		}


		// новая версия: все игры в portals.template
		$this->portals = $this->conn->getAll("SELECT title, identifier FROM portals");

		$this->controller = 'portals';
		$this->yield_me('portals.tpl');
	}




	function game_redirect() {
		
		// -> http://gf/?from=ultimate_mortal_kombat_3
		//if (strtolower(substr($_SERVER['REQUEST_URI'], 0, 7)) == '/?from=') {
		//	$request = strtolower($_SERVER['REQUEST_URI']);
		//	$data = explode('=', $request);
		//	$game_identifier = str_replace('_', ' ', $data[1]);
		//}	
		
		// -> http://gf/aladdin.html
		//if (strtolower(substr($_SERVER['REQUEST_URI'], -5, 5)) == '.html' ) {
		//	$request = ltrim(strtolower($_SERVER['REQUEST_URI']), '/');
		//	$data = explode('.', $request);
		//	$game_identifier = str_replace('_', ' ', $data[0]);
		//}
		
		// -> http://gf/genesis/ultimate_mortal_kombat_3.exe
		//if (strtolower(substr($_SERVER['REQUEST_URI'], 0, 9)) == '/genesis/') {
		//	$request = ltrim(strtolower($_SERVER['REQUEST_URI']), '/');
		//	$data = explode('/', $request); // -> genesis   ultimate_mortal_kombat_3.exe
		//	$data = explode('.', $data[1]); // -> ultimate_mortal_kombat_3   exe
		//	$game_identifier = str_replace('_', ' ', $data[0]);
		//}
		
   		
	}



	//private function initCounters() {
        // счетчики игр
    //    $this->counters = $this->conn->getRow('SELECT (SELECT count(*) FROM gf_games WHERE is_hidden = 0) all_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="trymedia") demos_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="genesis") genesis_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="snes") snes_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="nes") nes_count');
    //}


/*
	private function index($game_ids_list=array()) {

        $this->initCounters();
		$this->loadTeasers();

		//print_r($game_ids_list);
		$this->show_paginator = true;
		$this->show_sorting = false;
		
		$this->sort_mode = 'popular';
		$order_mode = '';
		switch ($_COOKIE['sort_mode']) {
			case 'popular':
				$this->sort_mode = 'popular';
				$order_mode = '';
				break;
			case 'discussed':
				$this->sort_mode = 'discussed';
				$order_mode = ' ORDER BY num_of_comments DESC';
				break;
			case 'alphabet':
				$this->sort_mode = 'alphabet';
				$order_mode = ' ORDER BY title ';
				break;
		}

		$this->number_of_games = $this->conn->getOne('SELECT count(*) from gf_games WHERE is_hidden = 0 ' . $this->sql_filter); // in selected digest section

		$this->from = PAGINATOR_GAMES_PER_PAGE * ($this->paginator_page-1);
		$this->to = 0;
		
		if (empty($game_ids_list)) {
			if ($this->is_nes) {
				// для nes спецзапрос с сортировкой
				$sSQL = ' SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g LEFT JOIN nes_sort s ON s.game_id = g.id WHERE is_hidden = 0 ' . $this->sql_filter . "ORDER BY COALESCE(s.sort_order, 9999999)";
			} else {
				if ($this->is_all) {
					$sSQL = ' SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g LEFT JOIN all_sort s ON s.game_id = g.id WHERE is_hidden = 0 ' . $this->sql_filter . "ORDER BY COALESCE(s.sort_order, 9999999)";
				} else {
					$sSQL = ' SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g WHERE is_hidden = 0 ' . $this->sql_filter . $order_mode;
				}
			}
			$sSQL .= " LIMIT {$this->from}, " . (PAGINATOR_GAMES_PER_PAGE + 10); // +10 for slidebox
			$this->games_list = $this->conn->getAll($sSQL); // игры на этой странице
			//print_r($this->games_list );

			if (count($this->games_list) == 0) {
				$this->error_404();
			    exit;
            }

			if (count($this->games_list) > 3 && $this->paginator_page > 1) {
				$this->page_title = $this->games_list[0]['title'] . ', ' . $this->games_list[1]['title'] . ', ' . $this->games_list[2]['title'] . ' and '. (count($this->games_list)-13) .' other games';
			}
			
		} else {
			// непонятно, попадаем ли мы сюда вообще
			$game_ids_string = implode(', ', $game_ids_list);
			$this->games_list = $this->conn->getAll(" SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g WHERE id IN ({$game_ids_string}) ");
			if (!empty($this->games_list)) {
				$this->page_title = $this->games_list[0]['title'];
			}
			$this->show_paginator = false;
			$this->show_sorting = false;

		}
		
		// slidebox
		if (count($this->games_list) > PAGINATOR_GAMES_PER_PAGE) {

			$this->slidebox_games = array_slice($this->games_list, PAGINATOR_GAMES_PER_PAGE);
			$this->games_list = array_slice($this->games_list, 0, PAGINATOR_GAMES_PER_PAGE);

		} 
		//print_r($this->games_list);
		//print_r($this->slidebox_games);
        
		if (!empty($this->slidebox_games)) {
            $this->sliderbox_other_games_counter = max(0, count($this->slidebox_games) - 3);
            $this->slidebox_games = array_slice($this->slidebox_games, 0, 3);

            $this->show_slidebox = true;
        }


		foreach($this->games_list as &$game) {
			$game['num_of_comments'] = $this->getNumOfComments($game['id']);
		}
		unset($game);

			// определим ids, game_id всех игры с одинаковым identifier
        foreach($this->games_list as &$game) {
            $ids = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ? AND is_hidden = 0", 0, array($game['identifier']));
            if (count($ids) == 0) {
			    exit;
            }
            $game['ids_string'] = implode(', ', $ids);
        }


        // если у игры пустой shortdescr, то попробуем найти его у игр других вендоров
        foreach($this->games_list as &$game) {
            if (empty($game['short_description'])) {
                $game['short_description'] = $this->conn->getOne("SELECT short_description FROM gf_games WHERE id IN ({$game['ids_string']}) AND is_hidden=0 AND (vendor != 'trymedia' AND vendor != '') AND (short_description IS NOT NULL OR short_description <> '') ORDER BY vendor");
            }
        }



        // ссылки на скачивание
        foreach($this->games_list as &$game) {
            $game['download'] = Helpers::get_game_link_and_size($game['download01'], $game['vendor'], $game['identifier'] );
        }

        foreach($this->games_list as &$game) {

            $title_nodashes = $game['title'];
            $title_nodashes = str_replace('-', ' ', $title_nodashes);
            $title_nodashes = str_replace("'", '', $title_nodashes);
            $title_nodashes = str_replace(':', ' ', $title_nodashes);
            $title_nodashes = str_replace('   ', ' ', $title_nodashes);
            $game['title_nodashes'] = $title_nodashes;

        }



		
		for($i = 0; $i < count($this->games_list); $i++) {
			
			// all_vendors

			$this->games_list[$i]['all_vendors'] = $this->conn->getCol("SELECT vendor FROM gf_games WHERE identifier = ? AND is_hidden=0 AND (vendor <> 'trymedia' AND vendor <> '') ORDER BY vendor", 0, array($this->games_list[$i]['identifier']));

			// have_teasers			
			if (in_array($this->games_list[$i]['identifier'], $this->teasers) ) {
				$this->games_list[$i]['have_teaser'] = true;

				$this->games_list[$i]['screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 3,2", array($this->games_list[$i]['id']) );
                //if ($this->games_list[$i]['identifier'] == 'streets-of-rage-3'){print_r($this->games_list[$i]['screenshots']);echo $this->games_list[$i]['id'];}
                // фикс для игр, у которых всего 4 скриншота (cyber-cop)
                if (count($this->games_list[$i]['screenshots']) <= 2) {
                    $this->games_list[$i]['screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 2,2", array($this->games_list[$i]['id']) );
                }

				if (count($this->games_list[$i]['screenshots']) != 2) {
					$this->games_list[$i]['screenshots'] = null;
				}
				
				$game_model = new Game();
				$this->games_list[$i]['similar_games'] = $game_model->get_similar_games_by_id($this->games_list[$i]['id']);
				
				if (count($this->games_list[$i]['similar_games']) > 2) {
					$this->games_list[$i]['similar_games'] = array_slice($this->games_list[$i]['similar_games'], 0, 2);
				}
				
			}
		}


        
		for($i = 0; $i < min(count($this->games_list), 5); $i++){
			//$comments_count = 0;
			$game_id = $this->games_list[$i]['id'];
			$comments = $this->conn->getAll("SELECT c.*, u.login, u.name FROM gf_comments c INNER JOIN gf_users u ON u.id=c.user_id WHERE game_id = '$game_id' ORDER BY points DESC, created_at LIMIT 2");
			//if (count($comments) > 0) {
			//	$comments_count += count($comments);
			//}
			foreach ($comments as &$comment) {
				$comment['time_since'] = Helpers::time_since($comment['created_at']);
			}
			//$this->games_list[$i]['comments_count'] = $comments_count;
			$this->games_list[$i]['comments'] = $comments;

		}



		//if (count($this->games_list) > 5) {
			for($i = 0; $i < count($this->games_list); $i += 2){

				//if (count($this->games_list[$i]['comments']) == 0 && $this->games_list[$i]['have_teaser'] == false) {
                if ($this->games_list[$i]['have_teaser'] == false) {
					$this->games_list[$i]['screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 1,3", array($this->games_list[$i]['id']) );
                    //AND auto_added = 1
				}
				
			}
		//}

		//$this->counters = $this->conn->getRow('SELECT (SELECT count(*) FROM gf_games WHERE is_hidden = 0) all_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="trymedia") demos_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="genesis") genesis_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="snes") snes_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="nes") nes_count');
		//print_r($this->games_list);
		
		$this->from += 1;
		$this->to = $this->from + count($this->games_list) - 1;
		
		//print_r($this);
		//print_r($this->games_list);


		$this->pages_total = ceil($this->number_of_games / PAGINATOR_GAMES_PER_PAGE);
		if ($this->paginator_page >= 1 && $this->paginator_page < $this->pages_total) {
			$this->rel_next = '<link rel="next" href="/' . $this->vendor . '/' . ($this->paginator_page + 1) . '/">';
		}

		if ($this->paginator_page > 1 && $this->paginator_page <= $this->pages_total) {
			if ($this->paginator_page - 1 != 1) {
				$this->rel_prev = '<link rel="prev" href="/' . $this->vendor . '/' . ($this->paginator_page - 1) . '/">';
			} else
			{
				$this->rel_prev = '<link rel="prev" href="/' . $this->vendor . '/">';
			}
		}


		if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
			$this->show_full_page = true;
			$this->yield_me('digest_simple.tpl');
			exit();
		}

		$this->show_full_page = false;

		$this->display('digest_simple_ajax.tpl'); // в этом вызове используем display, а не yield
	}
*/



	function digest2() {

		if (!isset($this->pageoptions['showonly'])) {
			$this->error_404();
		}

		$games_per_page = $this->games_per_page;

		$from = ($this->paginator_page - 1) * $games_per_page;

		//$gamelink = '/'.$this->pageoptions['scriptname'].'/';
		$gamelink = '/'.$this->pageoptions['showonly'].'/';

		$sSQL = "";
		$sSQL2 = "";
		$params = array();
		switch ($this->pageoptions['showonly']) {
			case 'freeware-games':
				//$sSQL= "SELECT gameid FROM games WHERE (hidden='N' AND gameprice=0) ORDER BY gameid DESC LIMIT $from, 10";
				//$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND gameprice=0)";
				$sSQL = "SELECT gameid FROM games LEFT JOIN games_sort gs ON gs.game_id = gameid WHERE (hidden='N' AND gameprice=0) ORDER BY COALESCE(gs.position, 99999999) ASC, id, gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games LEFT JOIN games_sort gs ON gs.game_id = gameid WHERE (hidden='N' AND gameprice=0)";
				// we need this to solve problem with first page (means ./../ will point to the root)
				break;
			case 'sega-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND platform='genesis') ORDER BY title, gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND platform='genesis')";
				//$this->tpl->assign('page_h1', 'All Sega Games');
				break;
			case 'arcade-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='arcade_action' OR category02='arcade_action')) ORDER BY CASE platform WHEN 'genesis' THEN 1 ELSE 2 END, gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='arcade_action' OR category02='arcade_action'))";
				break;
			case 'arkanoid-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='arkanoids' OR category02='arkanoids')) ORDER BY CASE platform WHEN 'genesis' THEN 1 ELSE 2 END, gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='arkanoids' OR category02='arkanoids'))";
				break;
			case 'adventure-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='adventure_rpg' OR category02='adventure_rpg')) ORDER BY CASE platform WHEN 'genesis' THEN 1 ELSE 2 END, gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='adventure_rpg' OR category02='adventure_rpg'))";
				break;
			case 'chess-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='board' OR category02='board')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='board' OR category02='board'))";
				break;
			case 'tetris-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='tetris' OR category02='tetris')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='tetris' OR category02='tetris'))";
				break;
			case 'card-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='card' OR category02='card')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='card' OR category02='card'))";
				break;
			case 'puzzle-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='logic_puzzle' OR category02='logic_puzzle')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='logic_puzzle' OR category02='logic_puzzle'))";
				break;
			case 'shooter-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='shooter' OR category02='shooter')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='shooter' OR category02='shooter'))";
				break;
			case 'strategy-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='strategy_war' OR category02='strategy_war')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='strategy_war' OR category02='strategy_war'))";
				break;
			case 'featured-games':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND featscale>1) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND featscale>1)";
				break;
			case 'pacman':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='pacman' OR category02='pacman')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='pacman' OR category02='pacman'))";
				break;
			case 'rpg':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='rpg' OR category02='rpg' OR category01='adventure_rpg' OR category02='adventure_rpg')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='rpg' OR category02='rpg' OR category01='adventure_rpg' OR category02='adventure_rpg'))";
				break;
			case 'board':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='board' OR category02='board')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='board' OR category02='board'))";
				break;
			case 'sport':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='sport' OR category02='sport')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='sport' OR category02='sport'))";
				break;
			case 'racing':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='racing' OR category02='racing')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='racing' OR category02='racing'))";
				break;
			case 'fighting':
				$sSQL = "SELECT gameid FROM games WHERE (hidden='N' AND (category01='fighting' OR category02='fighting')) ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE (hidden='N' AND (category01='fighting' OR category02='fighting'))";
				break;
			case 'games':
				$sSQL = "SELECT gameid FROM games WHERE hidden='N' ORDER BY gameid DESC LIMIT $from, $games_per_page";
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE hidden='N'";
				break;
			case 'year':
				$this->hide_paginator = true;
				$sSQL = "SELECT gameid FROM games WHERE release_year=? AND hidden='N' ORDER BY gameid DESC";
				$params = array($this->year);
				$sSQL2 = "SELECT COUNT(*) FROM games WHERE release_year=? AND hidden='N'";
				break;
			default:
				$this->error_404();
		}

		$gamesinbase = $this->conn->getOne($sSQL2, $params);

		$numpages = ceil($gamesinbase / $games_per_page);

		$this->numpages = $numpages;
		$this->gamelink = $gamelink;

		$result = $this->conn->getCol($sSQL, 0, $params);

		$this->num_of_results = count($result);

		if ($this->num_of_results == 0) {
			$this->error_404();
		}

		$games_on_page = array();

		// assign games to template
		$indexgame = array();
		for($i = 0; $i < count($result); $i++) {
			$game = new Game($result[$i]);
			$gg = $game->getInfo();

			$gg['iposition'] = $i;
			$games_on_page[$i] = $gg;

			array_push($indexgame, $gg);
		}

		$this->indexgame = $indexgame;//print_r($indexgame);


		// перепишем title страницы
		if ($this->paginator_page != 1) {
			if (count($result) >= 3) {
				$title = $games_on_page[0]['title'] . ", ". $games_on_page[1]['title'] . ", " . $games_on_page[2]['title'];
				if (count($result) > 5) {
					$title .= " and " . (count($result)-3) . " other games";
				}
				$this->setPageTitle($title);

				//print_r($games_on_page[0]);
			}
		}


		$this->yield_me('games_digest.tpl');
	}




	function set_search_query($search_query) {
		$search_query = urldecode($search_query);
		$this->search_query = $search_query;
		$this->search_query_for_form = htmlspecialchars($search_query);

		$this->set_game_slug($search_query);

	}


	function game_search() {
		$this->tag_search();
	}



	// function year()
	function by_year() {

		if (empty($this->year)) {
			$this->setPageTitle('List of years in video gaming');
			$this->yield_me('games_by_year.tpl');
			exit();
		}


		$this->setPageTitle($this->year . ' in Video Gaming');
		$this->setPageH1($this->year . ' Games');

		$this->pageoptions['showonly'] = 'year';

		$this->digest2();
	}



	// портал точно должен существовать
	function portal2($portal_identifier) {

		$this->portal = $this->conn->getRow("SELECT * FROM portals WHERE identifier = ? LIMIT 1", array($portal_identifier));

		if (!empty($this->portal['template'])) {

			if (!empty($this->portal['title'])) {
				$this->setPageH1($this->portal['h1_header']);
				$this->setPageTitle($this->portal['title']);
				$this->setPageMetaDescription($this->portal['meta_description']);
			}

			$identifiers = array_filter(array_unique(explode("\r\n", $this->portal['template'])));
			$this->vendor = 'game/' . $portal_identifier; // '/games/movie/'

			//$this->digest($ids);

			$indexgame = array();
			foreach ($identifiers as $identifier) {
				try {
					$game = new Game($identifier);
					$sdescr = $game->getInfo();
					array_push($indexgame, $sdescr);
				} catch (GameIsHiddenException $e) {
					// TODO: Log something
				}
				catch (NotFoundException $e) {}

			}

			$this->hide_next_page_button = true;
			$this->num_of_results = count($indexgame);


			$this->indexgame = $indexgame;
			$this->yield_me('games_digest.tpl');

			exit;
		}

	}






	function tag_search() {

		// проверим, существует ли портал
		if ($this->is_portal_exists($this->game_slug)) {

			/*
			for($i = 0; $i < count($result); $i++) {

				try {
					$game = new Game($result[$i]);
					$sdescr = $game->getInfo();

					$sdescr['iposition'] = $i;

					array_push($indexgame, $sdescr);
				} catch (GameIsHiddenException $e) {
				}
			}
			*/
			$this->portal2($this->game_slug);
			exit();
		}

		//$this->conn->getOne("SELECT * FROM portals")






		$games_per_page = $this->games_per_page;

		$from = ($this->paginator_page - 1) * $games_per_page;

		//$found = $this->SearchString($srequest);
		$cl = new SphinxClient();
		$cl->SetServer( "localhost", 9312 );
		$cl->SetMatchMode( SPH_MATCH_ANY );
		$cl->SetConnectTimeout(2);
		$cl->SetMaxQueryTime(3000);
		$cl->SetLimits($from, $games_per_page);
		$cl->_maxmatches = 200;

		$found = $cl->Query( $this->game_slug, 'games4win_index' );

		$result = array();
		if (is_array($found['matches'])) {
			$result = array_keys($found['matches']);
		} else {
			$this->error_404();
		}

		$gamesinbase = count($found['matches']);

		$numpages = ceil($found['total'] / $games_per_page);
		if ($gamesinbase > 0 && $numpages == 0) $numpages = 1;
		$gamelink = '/game/'.htmlentities($this->game_slug).'/';


		$this->num_of_results = $gamesinbase;

		$this->numpages = $numpages;
		$this->gamelink = $gamelink;

		$indexgame = array();
		for($i = 0; $i < count($result); $i++) {

			try {
				$game = new Game($result[$i]);
				$sdescr = $game->getInfo();

				$sdescr['iposition'] = $i;

				array_push($indexgame, $sdescr);
			} catch (GameIsHiddenException $e) {
			}
		}
		$this->indexgame = $indexgame;

		$game_title_normalized = $this->game_slug;
		$game_title_normalized = str_replace("+", " ", $game_title_normalized);
		$game_title_normalized = str_replace("-", " ", $game_title_normalized);
		$game_title_normalized = ucwords($game_title_normalized);

		$title = $game_title_normalized . ' Game Download';
		$h1_tag = $game_title_normalized . ' Games';

		$this->setPageTitle($title);
		$this->setPageH1($h1_tag);


		$this->yield_me('games_digest.tpl');

	}




/*
	function all_games_ajax() {


		if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
			header('Location: /games/', TRUE, 301);
			exit();
		}

		$this->from = PAGINATOR_GAMES_PER_PAGE * ($this->paginator_page-1);
		$this->to = 0;

		if (empty($game_ids_list)) {
			if ($this->is_nes) {
				// для nes спецзапрос с сортировкой
				$sSQL = ' SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g LEFT JOIN nes_sort s ON s.game_id = g.id WHERE is_hidden = 0 ' . $this->sql_filter . "ORDER BY COALESCE(s.sort_order, 9999999)";
			} else {
				if ($this->is_all) {
					$sSQL = ' SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g LEFT JOIN all_sort s ON s.game_id = g.id WHERE is_hidden = 0 ' . $this->sql_filter . "ORDER BY COALESCE(s.sort_order, 9999999)";
				} else {
					$sSQL = ' SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g WHERE is_hidden = 0 ' . $this->sql_filter . $order_mode;
				}
			}
			$sSQL .= " LIMIT {$this->from}, " . (PAGINATOR_GAMES_PER_PAGE + 10); // +10 for slidebox
			$this->games_list = $this->conn->getAll($sSQL); // игры на этой странице
			//print_r($this->games_list );

			if (count($this->games_list) == 0) {
				$this->error_404();
				exit;
			}

			if (count($this->games_list) > 3 && $this->paginator_page > 1) {
				$this->page_title = $this->games_list[0]['title'] . ', ' . $this->games_list[1]['title'] . ', ' . $this->games_list[2]['title'] . ' and '. (count($this->games_list)-13) .' other games';
			}

		} else {
			// непонятно, попадаем ли мы сюда вообще
			$game_ids_string = implode(', ', $game_ids_list);
			$this->games_list = $this->conn->getAll(" SELECT id, title, identifier, download01, short_description, digest_image, vendor, (SELECT count(*) FROM gf_comments c WHERE c.game_id=g.id) num_of_comments FROM gf_games g WHERE id IN ({$game_ids_string}) ");
			if (!empty($this->games_list)) {
				$this->page_title = $this->games_list[0]['title'];
			}
			$this->show_paginator = false;
			$this->show_sorting = false;

		}

		// slidebox
		if (count($this->games_list) > PAGINATOR_GAMES_PER_PAGE) {

			$this->slidebox_games = array_slice($this->games_list, PAGINATOR_GAMES_PER_PAGE);
			$this->games_list = array_slice($this->games_list, 0, PAGINATOR_GAMES_PER_PAGE);

		}
		//print_r($this->games_list);
		//print_r($this->slidebox_games);

		if (!empty($this->slidebox_games)) {
			$this->sliderbox_other_games_counter = max(0, count($this->slidebox_games) - 3);
			$this->slidebox_games = array_slice($this->slidebox_games, 0, 3);

			$this->show_slidebox = true;
		}


		foreach($this->games_list as &$game) {
			$game['num_of_comments'] = $this->getNumOfComments($game['id']);
		}
		unset($game);

		// определим ids, game_id всех игры с одинаковым identifier
		foreach($this->games_list as &$game) {
			$ids = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ? AND is_hidden = 0", 0, array($game['identifier']));
			if (count($ids) == 0) {
				exit;
			}
			$game['ids_string'] = implode(', ', $ids);
		}


		// если у игры пустой shortdescr, то попробуем найти его у игр других вендоров
		foreach($this->games_list as &$game) {
			if (empty($game['short_description'])) {
				$game['short_description'] = $this->conn->getOne("SELECT short_description FROM gf_games WHERE id IN ({$game['ids_string']}) AND is_hidden=0 AND (vendor != 'trymedia' AND vendor != '') AND (short_description IS NOT NULL OR short_description <> '') ORDER BY vendor");
			}
		}


		for($i = 0; $i < count($this->games_list); $i++) {

			// all_vendors

			$this->games_list[$i]['all_vendors'] = $this->conn->getCol("SELECT vendor FROM gf_games WHERE identifier = ? AND is_hidden=0 AND (vendor <> 'trymedia' AND vendor <> '') ORDER BY vendor", 0, array($this->games_list[$i]['identifier']));

			// have_teasers
			if (in_array($this->games_list[$i]['identifier'], $this->teasers) ) {
				$this->games_list[$i]['have_teaser'] = true;

				$this->games_list[$i]['screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 3,2", array($this->games_list[$i]['id']) );
				//if ($this->games_list[$i]['identifier'] == 'streets-of-rage-3'){print_r($this->games_list[$i]['screenshots']);echo $this->games_list[$i]['id'];}
				// фикс для игр, у которых всего 4 скриншота (cyber-cop)
				if (count($this->games_list[$i]['screenshots']) <= 2) {
					$this->games_list[$i]['screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 2,2", array($this->games_list[$i]['id']) );
				}

				if (count($this->games_list[$i]['screenshots']) != 2) {
					$this->games_list[$i]['screenshots'] = null;
				}

				$game_model = new Game();
				$this->games_list[$i]['similar_games'] = $game_model->get_similar_games_by_id($this->games_list[$i]['id']);

				if (count($this->games_list[$i]['similar_games']) > 2) {
					$this->games_list[$i]['similar_games'] = array_slice($this->games_list[$i]['similar_games'], 0, 2);
				}

			}
		}



		for($i = 0; $i < min(count($this->games_list), 5); $i++){
			//$comments_count = 0;
			$game_id = $this->games_list[$i]['id'];
			$comments = $this->conn->getAll("SELECT c.*, u.login, u.name FROM gf_comments c INNER JOIN gf_users u ON u.id=c.user_id WHERE game_id = '$game_id' ORDER BY points DESC, created_at LIMIT 2");
			//if (count($comments) > 0) {
			//	$comments_count += count($comments);
			//}
			foreach ($comments as &$comment) {
				$comment['time_since'] = Helpers::time_since($comment['created_at']);
			}
			//$this->games_list[$i]['comments_count'] = $comments_count;
			$this->games_list[$i]['comments'] = $comments;

		}




		//if (count($this->games_list) > 5) {
		for($i = 0; $i < count($this->games_list); $i += 2){

			//if (count($this->games_list[$i]['comments']) == 0 && $this->games_list[$i]['have_teaser'] == false) {
			if ($this->games_list[$i]['have_teaser'] == false) {
				$this->games_list[$i]['screenshots'] = $this->conn->getAll("SELECT name FROM gf_screenshots WHERE game_id = ? AND user_id = 4 ORDER BY position, id LIMIT 1,3", array($this->games_list[$i]['id']) );
				//AND auto_added = 1
			}

		}
		//}

		//$this->counters = $this->conn->getRow('SELECT (SELECT count(*) FROM gf_games WHERE is_hidden = 0) all_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="trymedia") demos_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="genesis") genesis_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="snes") snes_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="nes") nes_count');
		//print_r($this->games_list);

		$this->from += 1;
		$this->to = $this->from + count($this->games_list) - 1;

		//print_r($this);
		//print_r($this->games_list);

		//$this->yield_me('digest_simple.tpl');
		$this->display('digest_simple_ajax.tpl');
	}
*/

	function redirect_to_games() {
		// 301 Moved Permanently
		header("Location: /games/", TRUE, 301);
		exit();
	}

	//function sms_ajax() {
	//	$this->page_title = 'Sega Games';
	//	$this->page_h1 = "Sega Games";
	//	$this->sql_filter = ' AND vendor = "sms"';
	//	$this->vendor = 'sega';
	//	$this->all_games_ajax();
	//}


	function index() {
		$this->setPageTitle('Games Download');
		$this->setPageH1('All Games');
		$this->pageoptions['showonly'] = 'games';
		$this->digest2();
	}


	function featured_games() {
		$this->setPageTitle('Download Featured Games');
		$this->setPageH1('Games4Win Featured Games');
		$this->pageoptions['showonly'] = 'featured-games';
		$this->digest2();
	}


	function sega_games() {
		$this->setPageTitle('Sega Games Download');
		$this->setPageH1('All Sega Games');
		$this->pageoptions['showonly'] = 'sega-games';
		$this->digest2();
	}


	function freeware_games() {
		$this->setPageTitle('Free Games Download');
		$this->setPageH1('Freeware Games');
		$this->pageoptions['showonly'] = 'freeware-games';
		$this->digest2();
	}


	function arcade_games() {
		//$this->setPageTitle('Download Arcade Games');
		//$this->setPageH1('Arcade Games');

		$this->setPageTitle('FREE download Arcade Games for PC – new and old Arcade games review, system requirements');
		$this->setPageH1('Download Arcade Games');
		$this->setPageMetaDescription('FREE download Arcade Games for PC. Play new and old Arcade games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'arcade-games';
		$this->digest2();
	}


	function arkanoid_games() {
		//$this->setPageTitle('Arkanoid Download');
		//$this->setPageH1('Arkanoid Games');

		$this->setPageTitle('FREE download Arkanoid Games for PC – new and old Arkanoid Games review, system requirements');
		$this->setPageH1('Download Arkanoid Games');
		$this->setPageMetaDescription('FREE download Arkanoid Games for PC. Play new and old Arkanoid Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'arkanoid-games';
		$this->digest2();
	}


	function adventure_games() {
		//$this->setPageTitle('Download Adventure Games');

		$this->setPageTitle('FREE download Adventure Games for PC – new and old Adventure Games review, system requirements');
		$this->setPageH1('Download Adventure Games');
		$this->setPageMetaDescription('FREE download Adventure Games for PC. Play new and old Adventure Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'adventure-games';
		$this->digest2();
	}


	function chess_games() {
		//$this->setPageTitle('Chess Games Download');
		//$this->setPageH1('Chess Download');

		$this->setPageTitle('FREE download Chess Games for PC – new and old Chess Games review, system requirements');
		$this->setPageH1('Download Chess Download');
		$this->setPageMetaDescription('FREE download Chess Games for PC. Play new and old Chess Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'chess-games';
		$this->digest2();
	}


	function tetris_games() {
		//$this->setPageTitle('Tetris Download');
		//$this->setPageH1('Tetris Games');

		$this->setPageTitle('FREE download Tetris Games for PC – new and old Tetris Games review, system requirements');
		$this->setPageH1('Download Tetris Games');
		$this->setPageMetaDescription('FREE download Tetris Games for PC. Play new and old Tetris Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'tetris-games';
		$this->digest2();
	}


	function card_games() {
		//$this->setPageTitle('Card Download');
		//$this->setPageH1('Card Games');

		$this->setPageTitle('FREE download Card Games for PC – new and old Card Games review, system requirements');
		$this->setPageH1('Download Card Games');
		$this->setPageMetaDescription('FREE download {name} games for PC. Play new and old Card Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'card-games';
		$this->digest2();
	}


	function puzzle_games() {
		//$this->setPageTitle('Puzzle Games Download');
		//$this->setPageH1('Puzzle Download');

		$this->setPageTitle('FREE download Puzzle Games for PC – new and old Puzzle Games review, system requirements');
		$this->setPageH1('Download Puzzle Games');
		$this->setPageMetaDescription('FREE download Puzzle Games for PC. Play new and old Puzzle Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'puzzle-games';
		$this->digest2();
	}


	function shooter_games() {
		//$this->setPageTitle('Download Shooters');
		//$this->setPageH1('Download Shooter Games');

		$this->setPageTitle('FREE download Shooter Games for PC – new and old Shooter Games review, system requirements');
		$this->setPageH1('Download Shooter Games');
		$this->setPageMetaDescription('FREE download Shooter Games for PC. Play new and old Shooter Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'shooter-games';
		$this->digest2();
	}


	function strategy_games() {
		//$this->setPageTitle('Strategy Games Download');
		//$this->setPageH1('Download Strategy Games');

		$this->setPageTitle('FREE download Strategy Games for PC – new and old Strategy Games review, system requirements');
		$this->setPageH1('Download Strategy Games');
		$this->setPageMetaDescription('FREE download Strategy Games for PC. Play new and old Strategy Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'strategy-games';
		$this->digest2();
	}


	function pacman() {
		//$this->setPageTitle('Pacman Download');
		//$this->setPageH1('Pacman Download');

		$this->setPageTitle('FREE download Pacman Games for PC – new and old Pacman Games review, system requirements');
		$this->setPageH1('Download Pacman');
		$this->setPageMetaDescription('FREE download Pacman Games for PC. Play new and old Pacman Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'pacman';
		$this->digest2();
	}


	function rpg() {
		//$this->setPageTitle('Download Role Playing Games (RPG)');
		//$this->setPageH1('RPG Download');

		$this->setPageTitle('FREE download RPG Games for PC – new and old Role Playing Games review, system requirements');
		$this->setPageH1('Download RPG');
		$this->setPageMetaDescription('FREE download RPG Games for PC. Play new and old Role Playing Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'rpg';
		$this->digest2();
	}


	function board() {
		//$this->setPageTitle('Board Games ');
		//$this->setPageH1('Board Games Download');

		$this->setPageTitle('FREE download Board Games for PC – new and old Board Games review, system requirements');
		$this->setPageH1('Download Board Games');
		$this->setPageMetaDescription('FREE download Board Games for PC. Play new and old Board Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'board';
		$this->digest2();
	}


	function racing() {
		//$this->setPageTitle('Racing Games');
		//$this->setPageH1('Download Racing Games');

		$this->setPageTitle('FREE download Racing Games for PC – new and old Racing Games review, system requirements');
		$this->setPageH1('Download Racing Games');
		$this->setPageMetaDescription('FREE download Racing games for PC. Play new and old Racing games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'racing';
		$this->digest2();
	}


	function fighting() {
		//$this->setPageTitle('Fighting Games');
		//$this->setPageH1('Download Fighting Games');

		$this->setPageTitle('FREE download Fighting Games for PC – new and old Fighting games review, system requirements');
		$this->setPageH1('Download Fighting Games');
		$this->setPageMetaDescription('FREE download Fighting Games for PC. Play new and old Fighting Games. Review, system requirements, rating and top 10.');
		$this->pageoptions['showonly'] = 'fighting';
		$this->digest2();
	}


}


