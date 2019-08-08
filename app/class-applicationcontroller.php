<?

class ApplicationController {

	var $conn;
	var $smarty;
	var $mc;
	var $page_title;
	var $page_metatag;
	//var $page_slogan; //
	var $page_h1;
	var $salt = "123456XXXX123456abcdezasdcx";
	var $teasers = null;


	function __construct() {
		$this->init_db();
		$this->init_smarty();
		$this->init_memcache();
		$this->check_user_login();


		$today = getdate(); //print_r($today);
		$this->eyear = $today["year"];

		$spider_catcher = SpiderCatcher::singleton();
		$spider_catcher->log_all_bots();

	}
	
	
	private function init_db() {
		$connection = DatabaseConnection::singleton();
		$this->conn = $connection->conn;
	}

	
	private function init_memcache() {
		//$this->mc = new Memcache;
		//$this->mc->connect('localhost', 11211) or die ("Could not connect");
	}
	

	private function init_smarty() {
		$this->smarty = new Smarty;
		$this->smarty->template_dir = array('../templates');
		$this->smarty->compile_dir = '../templates_c';

		if (ENVIRONMENT != 'production') {
			//$this->smarty->caching = Smarty::CACHING_OFF;
			$this->smarty->caching = false;
		}

		global $smarty;
		$smarty = $this->smarty;
	}


	function export_smarty_vars() {
		$class_vars = get_object_vars($this);
		foreach ($class_vars as $var_name => $value) {
			if (gettype($var_name) != 'object') {
				$this->smarty->assign((string)$var_name, $value);
			}
		}
	}


	// panel
	private function check_authentication() {

		if ($this->auth) {
			
			$u = "";
			$p = "";
			
			if (isset($_SERVER['PHP_AUTH_USER']) ) {
				$u = md5($_SERVER['PHP_AUTH_USER']);
				$p = md5($this->salt . $_SERVER['PHP_AUTH_PW'] . $this->salt);
				setcookie('u', $u, time()+60*60, '/');
				setcookie('p', $p, time()+60*60, '/');
			} else {
				$u = $_COOKIE['u'];
				$p = $_COOKIE['p'];
			}

			if ( !(md5('rabbitone') == $u && md5($this->salt . '123123' . $this->salt) == $p) ) {
				header('WWW-Authenticate: Basic realm="Black Mesa Research Facility"');
				header('HTTP/1.0 401 Unauthorized');
				die ("Not authorized");
			}
		}

	}


	function check_user_login() {
		if (isset($_COOKIE['l']) && isset($_COOKIE['p'])) {
			$login = mysql_real_escape_string($_COOKIE['l']);
			$this->user_data = $this->conn->getRow("SELECT id, login, name, user_hash FROM gf_users WHERE login = ? LIMIT 1", array($login));

			if(($this->user_data['user_hash'] !== $_COOKIE['p']) or ($this->user_data['login'] !== $_COOKIE['l'])) {
				// unset login
				setcookie('l', '', time() - 3600*24*30*12, '/');
				setcookie('p', '', time() - 3600*24*30*12, '/');
			}
			else {
				$this->is_logged = true;
				$this->conn->query("UPDATE gf_users SET last_visited = NOW() WHERE id = ? LIMIT 1", array($this->user_data['id']));
			}
		}
	}


	/*
	 * '10' => ['10', '12', '78']
	*/
	public function getAllGameIds($game_id) {

		$identifier = null;
		$ids = null;
		if (is_numeric($game_id)) {
			$identifier = $this->conn->getOne("SELECT identifier FROM gf_games WHERE id = ?", array($game_id));

			if (empty($identifier)) {
				return null;
			}

			$ids = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ? AND is_hidden = 0", 0, array($identifier));

		} else {
			$ids = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ? AND is_hidden = 0", 0, array($game_id));
		}

		return $ids;
	}


	/*
	 * '10' => "10, 12, 78"
	*/
	public function getAllGameIdsAsString($game_id) {

		$ids = $this->getAllGameIds($game_id);
		if (empty($ids)) {
			return null;
		}
		$ids_as_string = implode(', ', $ids);

		return $ids_as_string;
	}


	public function getNumOfComments($game_id) {
		$comments_count = 0;
		$ids = $this->getAllGameIds($game_id);
		foreach ($ids as $id) {
			$counter = $this->conn->getOne("SELECT count(*) num_of_comments FROM gf_comments c WHERE c.game_id = ?", array($id));
			$comments_count += $counter;
		}

		return $comments_count;
	}


	public function initCounters() {
		// счетчики игр
		//$this->counters = $this->conn->getRow('SELECT (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor <> "trymedia") all_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor = "trymedia") demos_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="genesis") genesis_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="snes") snes_count, (SELECT count(*) FROM gf_games WHERE is_hidden = 0 AND vendor="nes") nes_count');
	}


	public function loadTeasers() {
		// загрузим teasers
		$this->teasers = array();
		foreach (glob("./bigimages/*.jpg") as $filename) {
			array_push($this->teasers, basename($filename, '.jpg'));
		}
	}

	function error_404($title = "Page not found") {
		$this->setPageTitle($title);
		header("HTTP/1.0 404 Not Found");
		$this->yield_me("error_404.tpl");
		exit();
	}


	function error_410_gone($title = "Page not found") {
		$this->setPageTitle($title);
		header("HTTP/1.0 410 Gone");
		$this->yield_me("error_404.tpl");
		exit();
	}




	function yield_me($template_name, $content_type = 'html') {

		$this->check_authentication();

		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		$this->export_smarty_vars();

		$header_type = 'text/html';
		if ($content_type == 'xml') {

			// content-type: xml
			$header_type = 'text/xml';
			header('Content-Type: ' . $header_type . '; charset=UTF-8');
			$this->smarty->display($template_name);

		} else {
			// content-type: html
			header('Content-Type: ' . $header_type . '; charset=UTF-8');
			$yield = $this->smarty->fetch($template_name);

			$this->smarty->assign('yield', $yield);

			if ($this->layout) {

				$this->smarty->display($this->layout . '.tpl');
			} else {

				$this->smarty->display('layout_index.tpl');
			}

			
		}

	}




	function display($template_name, $content_type = 'html') {

		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		$this->export_smarty_vars();

		$header_type = 'text/html';

		// content-type: html
		header('Content-Type: ' . $header_type . '; charset=UTF-8');

		//$yield = $this->smarty->fetch($template_name);
		//$this->smarty->assign('yield', $yield);
		//if ($this->layout) {
		$this->smarty->display($template_name);
		//} else {
		//	$this->smarty->display('layout_index.tpl');
		//}

	}


	function set_game_slug($game_slug) {
		$game_slug = str_replace("%20", "+", $game_slug);
		$game_slug = str_replace(" ", "+", $game_slug);

		$game_slug = html_entity_decode($game_slug);

		// http://stackoverflow.com/questions/7172378/replace-repeating-characters-with-one-with-a-regex
		$game_slug = preg_replace('/([\s.\'-,])\1+/', '$1', $game_slug);

		$this->game_slug = $game_slug;

	}


	function setPageTitle($title, $add_sitename = true) {
	    if ($add_sitename) {
            $this->page_title = $title . " - Games4Win";
            $this->title = $title . " - Games4Win";
        } else {
            $this->page_title = $title;
            $this->title = $title;
        }
	}

	function setPageH1($h1_tag) {
		$this->page_h1 = $h1_tag;
		$this->h1_tag = $h1_tag;
	}

	function setPageMetaDescription($metadescription) {
		$this->page_metadescription = $metadescription;
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


	public function getUserGameRating($string_id) {
		$average_rating = $this->conn->getOne("SELECT format(avg(user_rating),1) FROM user_votes v WHERE v.game_identifier = ?", array($string_id));
		$rating_count = $this->conn->getOne("SELECT count(*) FROM user_votes v WHERE v.game_identifier = ?", array($string_id));

		$previous_vote = $this->get_user_vote($string_id);
		if ($previous_vote != null) {
			return array('average_rating' => $average_rating, 'rating_count' => $rating_count, 'this_user_rating' => $previous_vote);
		}

		return array('average_rating' => $average_rating, 'rating_count' => $rating_count);

	}





	function is_portal_exists($portal_identifier) {

		// проверим, существует ли такой портал
		$portals_count = $this->conn->getOne("SELECT COUNT(*) FROM portals WHERE identifier = ?", array($portal_identifier));

		if ($portals_count != 0) {
			//return $portal_identifier;
			return true;
		}

		return false;
	}


	// портал точно должен существовать
	function portal($portal_identifier) {

		$this->portal = $this->conn->getRow("SELECT * FROM portals WHERE identifier = ? LIMIT 1", array($portal_identifier));

		if (!empty($this->portal['template'])) {

			if (!empty($this->portal['title'])) {
				$this->page_title =  $this->portal['title'];
			}

			$ids = explode("\r\n", $this->portal['template']);
			$ids = array_unique($ids);
			//print_r($ids);
			$this->vendor = 'games/' . $portal_identifier; // '/games/movie/'
			$this->digest($ids);
			exit;
		}

	}


}

