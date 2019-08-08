<?
	    // pgt
		$ttt=microtime();
		$ttt=((double)strstr($ttt, ' ')+(double)substr($ttt,0,strpos($ttt,' ')));

		define('ABSPATH', dirname(__FILE__) . '/' );

		include_once('../app/const.php');

		include_once('DB.php');
		if (DEBUG) {
			include_once('../app/class-error.php');
			PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handle_pear_error');
		}

		class NotFoundException extends Exception {}
		class GameIsHiddenException extends Exception {}

		include_once('../app/class-database.php');

		require_once('../app/smarty3/Smarty.class.php');

		//include_once('../app/class-templateprocessor.php');
		include_once('../app/class-applicationcontroller.php');
		include_once('../app/class-maincontroller.php');
		include_once('../app/class-digestcontroller.php');
		include_once('../app/class-gamecontroller.php');
		include_once('../app/class-routing.php');
		include_once('../app/class-markdown.php');
		//include_once('../app/class-textile.php');
		include_once('../app/class-helpers.php');
		//include_once('../app/class-panelcontroller.php');
		//include_once('../app/class-image.php');
		include_once('../app/class-logger.php');
		include_once('../app/class-spidercatcher.php');
		include_once('../app/global.php');
		include_once('../app/sphinxapi.php');

		include '../app/client.php'; // installcore




		// models
		include_once('../app/models/Model.php');
		include_once('../app/models/Game.php');
		include_once('../app/models/GameFull.php');

		//$template_processor = new TemplateProcessor();
		//$template_processor->out();



		//strtolower(substr($_SERVER['REQUEST_URI'], 0, 7)) == '/?from=' ||
		//strtolower(substr($_SERVER['REQUEST_URI'], -5, 5)) == '.html' 
		
		//$l = Logger::singleton();
		//$l->bark();

		$connection = DatabaseConnection::singleton();
		$conn = $connection->conn;
	
		
		// facebook referral links deletion ( ?ref=nf  )
		// from $_SERVER['REQUEST_URI']
		if (strpos($_SERVER['REQUEST_URI'], '?ref=nf') !== false) {
			$_SERVER['REQUEST_URI'] = str_replace('?ref=nf', '', $_SERVER['REQUEST_URI']);
		}
		

		if (!empty($_GET['q'])) {
			$search_query = urlencode($_GET['q']);
			header("Location: /search/" . $search_query . "/");
			exit();
		}


		$logger = Logger::singleton();

/*
		// <magic redirects>		
		// -> http://gf/?from=ultimate_mortal_kombat_3
		$game_identifier = null;
		
		if (strtolower(substr($_SERVER['REQUEST_URI'], 0, 7)) == '/?from=') {
			$request = strtolower($_SERVER['REQUEST_URI']);
			$data = explode('=', $request);
			$game_identifier = str_replace('_', '-', $data[1]);
		}
		
		// -> http://gf/aladdin.html
		if (strtolower(substr($_SERVER['REQUEST_URI'], -5, 5)) == '.html' ) {
			$request = ltrim(strtolower($_SERVER['REQUEST_URI']), '/');
			$data = explode('.', $request);
			$game_identifier = str_replace('_', '-', $data[0]);
		}
		
		// -> http://gf/genesis/ultimate_mortal_kombat_3.exe
		if (strtolower(substr($_SERVER['REQUEST_URI'], 0, 9)) == '/genesis/' &&
			substr($_SERVER['REQUEST_URI'], strlen($_SERVER['REQUEST_URI'])-4, 4) == '.exe' ) {

			$request = ltrim(strtolower($_SERVER['REQUEST_URI']), '/');
			$data = explode('/', $request); // -> genesis   ultimate_mortal_kombat_3.exe
			$data = explode('.', $data[1]); // -> ultimate_mortal_kombat_3   exe
			$game_identifier = str_replace('_', '-', $data[0]);
		}
		
		if (!empty($game_identifier)) {

			$logger->log_event('redirect', $_SERVER['REQUEST_URI']);
			
			// проверим таблицу редиректов
			$destination_identifier = $conn->getOne("SELECT destination_identifier FROM magic_redirects WHERE source_identifier = ? LIMIT 1", array($game_identifier) );
			if (!empty($destination_identifier)) {
				$game_identifier = $destination_identifier;
			}
			
			// 301 Moved Permanently
			header("Location: /games/{$game_identifier}/", TRUE, 301);
			exit();
		}
		// </magic redirects>
*/

	if(!DEBUG && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")) {
	// $_SERVER['HTTP_HOST'] == gamefabrique.com
	// $_SERVER['REQUEST_URI'] == /games/back-to-the-future
	if (Helpers::endsWith($_SERVER['REQUEST_URI'], '/')) {
		header("Location: https://games4win.com" . $_SERVER['REQUEST_URI'], TRUE, 301);
		exit();
	} else {
		header("Location: https://games4win.com" . $_SERVER['REQUEST_URI'] . '/', TRUE, 301);
		exit();
	}
	//error_log($_SERVER['HTTP_HOST'] . '  ' . $_SERVER['REQUEST_URI']. '    '. $_SERVER['HTTP_REFERER']);

}



		// <plain_redirects>
		//echo $_SERVER['REQUEST_URI'];
		$plain_redirect_destination = $conn->getOne("SELECT destination FROM plain_redirects WHERE source = ? LIMIT 1", array($_SERVER['REQUEST_URI']));
		if (!empty($plain_redirect_destination)) {

			$logger->log_event('redirect_plain', $_SERVER['REQUEST_URI']);

			// 301 Moved Permanently
			header("Location: {$plain_redirect_destination}", TRUE, 301);
			exit();
		}
		// </plain_redirects>

/*
		if (!empty($_SERVER['HTTP_REFERER'])) {
			if ((strpos($_SERVER['HTTP_REFERER'], 'http://www.gamefabrique.com') === false) &&
				(strpos($_SERVER['HTTP_REFERER'], 'http://gamefabrique.com') === false) &&
				(strpos($_SERVER['HTTP_REFERER'], 'https://www.google') === false) &&
				(strpos($_SERVER['HTTP_REFERER'], 'https://google') === false) &&
				(strpos($_SERVER['HTTP_REFERER'], 'http://www.google') === false) &&
				(strpos($_SERVER['HTTP_REFERER'], 'http://google') === false)) {

					$logger->log_event('referrer', $_SERVER['HTTP_REFERER']);

			}
		}

*/
		
		
		$routes = new Routing();
		
		$routes->add_route( array('route' => '/', 'controller' => 'game', 'action' => 'main_page' ) );
		//$routes->add_route( array('route' => '/games/:page/', 'controller' => 'digest', 'action' => 'index', 'page' => 'integer' ) );
		//$routes->add_route( array('route' => '/games/:game_slug/', 'controller' => 'game', 'action' => 'index', 'game_slug' => 'game_tag' ) );
		
		//games

		$routes->add_route( array('route' => '/games/:game_slug/', 'controller' => 'game', 'action' => 'index', 'game_slug' => 'game_tag' ) );

		$routes->add_route( array('route' => '/game/', 'controller' => 'digest', 'action' => 'tag_search' ) );
		$routes->add_route( array('route' => '/game/:game_slug/', 'controller' => 'digest', 'action' => 'tag_search', 'game_slug' => 'game_tag' ) );
		$routes->add_route( array('route' => '/game/:game_slug/:page/', 'controller' => 'digest', 'action' => 'tag_search', 'game_slug' => 'game_tag', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/search/', 'controller' => 'digest', 'action' => 'game_search' ) );
		$routes->add_route( array('route' => '/search/:search_query/', 'controller' => 'digest', 'action' => 'game_search', 'search_query' => 'anything' ) );
		$routes->add_route( array('route' => '/search/:search_query/:page/', 'controller' => 'digest', 'action' => 'game_search', 'search_query' => 'anything', 'page' => 'integer' ) );


		// DIGEST
		$routes->add_route( array('route' => '/games/', 'controller' => 'digest', 'action' => 'index' ) );
		$routes->add_route( array('route' => '/games/:page/', 'controller' => 'digest', 'action' => 'index', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/featured-games/', 'controller' => 'digest', 'action' => 'featured_games' ) );
		$routes->add_route( array('route' => '/featured-games/:page/', 'controller' => 'digest', 'action' => 'featured_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/sega-games/', 'controller' => 'digest', 'action' => 'sega_games' ) );
		$routes->add_route( array('route' => '/sega-games/:page/', 'controller' => 'digest', 'action' => 'sega_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/freeware-games/', 'controller' => 'digest', 'action' => 'freeware_games' ) );
		$routes->add_route( array('route' => '/freeware-games/:page/', 'controller' => 'digest', 'action' => 'freeware_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/arcade-games/', 'controller' => 'digest', 'action' => 'arcade_games' ) );
		$routes->add_route( array('route' => '/arcade-games/:page/', 'controller' => 'digest', 'action' => 'arcade_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/arkanoid-games/', 'controller' => 'digest', 'action' => 'arkanoid_games' ) );
		$routes->add_route( array('route' => '/arkanoid-games/:page/', 'controller' => 'digest', 'action' => 'arkanoid_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/adventure-games/', 'controller' => 'digest', 'action' => 'adventure_games' ) );
		$routes->add_route( array('route' => '/adventure-games/:page/', 'controller' => 'digest', 'action' => 'adventure_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/chess-games/', 'controller' => 'digest', 'action' => 'chess_games' ) );
		$routes->add_route( array('route' => '/chess-games/:page/', 'controller' => 'digest', 'action' => 'chess_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/tetris-games/', 'controller' => 'digest', 'action' => 'tetris_games' ) );
		$routes->add_route( array('route' => '/tetris-games/:page/', 'controller' => 'digest', 'action' => 'tetris_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/card-games/', 'controller' => 'digest', 'action' => 'card_games' ) );
		$routes->add_route( array('route' => '/card-games/:page/', 'controller' => 'digest', 'action' => 'card_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/puzzle-games/', 'controller' => 'digest', 'action' => 'puzzle_games' ) );
		$routes->add_route( array('route' => '/puzzle-games/:page/', 'controller' => 'digest', 'action' => 'puzzle_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/shooter-games/', 'controller' => 'digest', 'action' => 'shooter_games' ) );
		$routes->add_route( array('route' => '/shooter-games/:page/', 'controller' => 'digest', 'action' => 'shooter_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/strategy-games/', 'controller' => 'digest', 'action' => 'strategy_games' ) );
		$routes->add_route( array('route' => '/strategy-games/:page/', 'controller' => 'digest', 'action' => 'strategy_games', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/pacman/', 'controller' => 'digest', 'action' => 'pacman' ) );
		$routes->add_route( array('route' => '/pacman/:page/', 'controller' => 'digest', 'action' => 'pacman', 'page' => 'integer' ) );


		$routes->add_route( array('route' => '/rpg/', 'controller' => 'digest', 'action' => 'rpg' ) );
		$routes->add_route( array('route' => '/rpg/:page/', 'controller' => 'digest', 'action' => 'rpg', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/board/', 'controller' => 'digest', 'action' => 'board' ) );
		$routes->add_route( array('route' => '/board/:page/', 'controller' => 'digest', 'action' => 'board', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/racing/', 'controller' => 'digest', 'action' => 'racing' ) );
		$routes->add_route( array('route' => '/racing/:page/', 'controller' => 'digest', 'action' => 'racing', 'page' => 'integer' ) );

		$routes->add_route( array('route' => '/fighting/', 'controller' => 'digest', 'action' => 'fighting' ) );
		$routes->add_route( array('route' => '/fighting/:page/', 'controller' => 'digest', 'action' => 'fighting', 'page' => 'integer' ) );


		$routes->add_route( array('route' => '/year/', 'controller' => 'digest', 'action' => 'by_year' ) );
		$routes->add_route( array('route' => '/year/:year/', 'controller' => 'digest', 'action' => 'by_year', 'year' => 'integer' ) );
		//$routes->add_route( array('route' => '/year/19xx/', 'controller' => 'digest', 'action' => 'by_year' ) );


//		$routes->add_route( array('route' => '/sega-games/', 'controller' => 'game', 'action' => 'digest2' ) );
		//$routes->add_route( array('route' => '/sega-games/:page/', 'controller' => 'game', 'action' => 'processCategory', 'page' => 'integer' ) );


		$routes->add_route( array('route' => '/ajax/vote/', 'controller' => 'game', 'action' => 'game_vote' ) );

		/*
		$routes->add_route( array('route' => '/games/:game_slug/:page/', 'controller' => 'game', 'action' => 'index', 'game_slug' => 'game_tag', 'page' => 'integer' ) );


		$routes->add_route( array('route' => '/download/:game_slug/', 'controller' => 'game', 'action' => 'downloads', 'game_slug' => 'game_tag' ) );
		$routes->add_route( array('route' => '/forum/:game_slug/', 'controller' => 'game', 'action' => 'forum', 'game_slug' => 'game_tag' ) );
		$routes->add_route( array('route' => '/screenshots/:game_slug/', 'controller' => 'game', 'action' => 'screenshots', 'game_slug' => 'game_tag' ) );
		$routes->add_route( array('route' => '/cheats/:game_slug/', 'controller' => 'game', 'action' => 'cheats', 'game_slug' => 'game_tag' ) );


        $routes->add_route( array('route' => '/snes/:game_slug/', 'controller' => 'game', 'action' => 'snes_download', 'game_slug' => 'game_tag' ) );
        $routes->add_route( array('route' => '/nes/:game_slug/', 'controller' => 'game', 'action' => 'nes_download', 'game_slug' => 'game_tag' ) );
        $routes->add_route( array('route' => '/genesis/:game_slug/', 'controller' => 'game', 'action' => 'genesis_download', 'game_slug' => 'game_tag' ) );

		$routes->add_route( array('route' => '/developers/', 'controller' => 'game', 'action' => 'developer' ) );
		$routes->add_route( array('route' => '/developers/:developer_identifier/', 'controller' => 'game', 'action' => 'developer', 'developer_identifier' => 'string' ) );
		$routes->add_route( array('route' => '/developers/:developer_identifier/:page/', 'controller' => 'game', 'action' => 'developer', 'developer_identifier' => 'string', 'page' => 'integer' ) );

		//digest
		$routes->add_route( array('route' => '/games/', 'controller' => 'digest', 'action' => 'all_games' ) );


		$routes->add_route( array('route' => '/sitemap.xml', 'controller' => 'main', 'action' => 'sitemap' ) );
		$routes->add_route( array('route' => '/rss/', 'controller' => 'main', 'action' => 'rss' ) );
		*/

		
	
		//$routes->parse_route('/games/comix-zone/');
		//print_r($GLOBALS);
		
		
		//if (
		//	strtolower(substr($_SERVER['REQUEST_URI'], 0, 7)) == '/?from=' ||
		//	strtolower(substr($_SERVER['REQUEST_URI'], -5, 5)) == '.html' 
			//|| strtolower(substr($_SERVER['REQUEST_URI'], 0, 9)) == '/genesis/'
		//	) {
			//print_r($_SERVER['REQUEST_URI']);
		//	$main_controller = new DigestController();
		//	$main_controller->game_redirect();
		//}


		$routes->parse_route($_SERVER['REQUEST_URI']);
		
		$main_controller = new MainController();
		$main_controller->error_404();

		//$routes->parse_route('/sitemap.xml');
		
		//print_r($routes->route_storage);

