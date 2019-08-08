<?


define('GAMEFABRIQUE_TITLE_TAG', ' | GameFabrique');
define('TEMPLATE_404_NOT_FOUND', -2);
define('TEMPLATE_NOT_DETECTED', -1);
define('TEMPLATE_MAIN', 1);
define('TEMPLATE_DIGEST', 2);
define('TEMPLATE_GAMEPAGE', 3);
define('TEMPLATE_MEMBER', 4);
define('TEMPLATE_MEMBERS_DIRECTORY', 5);

//define('MEMBERS_SORT_ORDER_ACTIVE', 1);
//define('MEMBERS_SORT_ORDER_NEWEST', 2);
//define('MEMBERS_SORT_ORDER_ALPHABETICAL', 3);

//define('SUBTEMPLATE_MEMBERS_MAIN', 1);
define('SUBTEMPLATE_MEMBERS_ADMINISTRATORS', 2);

class TemplateProcessor {
	
	
	const HTML_CONTENT_TYPE = 'text/html';
	const XML_CONTENT_TYPE = 'xml/html';
	const REDIRECT_301_MOVED_PERMANENTLY = 301;
	

	var $site_url;
	var $request_url;
	var $page_template;
	var $page_subtemplate;
	var $page_sort_order;
	var $page_title = 'GameFabrique';
	var $member_login;
	var $paginator_page;
	var $game_slug;
	
	
	function __construct() {
		$this->init_variables();
		$this->slashed_redirect();
	}	
	
	
	private function init_variables() {
		$this->site_url = 'http://'.$_SERVER['SERVER_NAME'].'/';
		$this->request_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	}
	
	
	private function change_header($header_type, $location = '') {
		
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		
		switch ($header_type) {
			
			case self::HTML_CONTENT_TYPE:
			case self::XML_CONTENT_TYPE :
								header('Content-Type: '.$header_type.'; charset=UTF-8');
								break;
										
			case self::REDIRECT_301_MOVED_PERMANENTLY:
								header('Location: '.$location, TRUE, 301);	
								exit();
			
			
		}
		
	}
	
	
	private function set_game_slug($slug) {
		
	}


	private function get_game_slug() {
		return $this->game_slug;
	}
	
	
	public function out() {
		$this->init_db();
		$this->init_smarty();
		$this->change_header(self::HTML_CONTENT_TYPE);
		
		$this->page_detector();
		
		$this->smarty->assign('page_template', $this->page_template);
		$this->smarty->assign('page_subtemplate', $this->page_subtemplate);

		echo $this->page_template;
		
		switch ($this->page_template) {
			case TEMPLATE_MAIN:
							$this->show_main_page();
							break;
			case TEMPLATE_DIGEST:
							$this->show_digest_page();
							break;
			case TEMPLATE_GAMEPAGE:
							$this->show_game_page();
							break;
			case TEMPLATE_MEMBER:
							$this->show_member_page();
							break;
			case TEMPLATE_MEMBERS_DIRECTORY:
							$this->show_members_directory_page();
							break;
		}
		
	}
	
	
	public function show_main_page() {
		
		$game = $this->conn->getRow("SELECT * FROM gf_games WHERE id = 14 LIMIT 1");
		$game['long_description'] = $this->conn->getOne("SELECT body FROM gf_game_descriptions WHERE game_id = 14 LIMIT 1");

		$this->smarty->assign('game', $game);
		$this->smarty->display('index.tpl'); 
	}


	public function show_member_page() {
	
	}
	

//	public function show_game_page() {
	
//	}




	//public function show_digest_page() {
	 
	//}
	
	


//	public function show_members_directory_page() {

//	}
	

/*
	public function show_sitemap() {

		$this->init_db();
		
		$pages = $this->conn->getCol("SELECT identifier FROM gf_games WHERE identifier <> ''");
		$smarty->assign('SITEURL', SITEURL);
		$smarty->assign('pages', $pages);
		
		
		$smarty->display('sitemap.tpl');
	}
*/
	
	private function slashed_redirect() {
		if ($this->request_url[strlen($this->request_url)-1] != '/') {
			$this->change_header(self::REDIRECT_301_MOVED_PERMANENTLY, $this->request_url.'/');
		}
	}
	
	/*
	public function page_detector() {
		
		if ($_SERVER['REDIRECT_URL'] == '') {
			$this->page_template = TEMPLATE_MAIN;
			return;
		}
		
		
		$request = trim($_SERVER['REQUEST_URI'], '/');
		$request_array = split('/', $request);
		
		//var_dump($request_array);
		
		if (count($request_array) == 1) {
			$this->paginator_page = 1;
			$this->smarty->assign('paginator_page', $this->paginator_page);
			switch ($request_array[0]) {
				case 'games':
					$this->page_template = TEMPLATE_DIGEST;
					return;
				case 'administrators':
					$this->page_subtemplate = SUBTEMPLATE_MEMBERS_ADMINISTRATORS;
				case 'members':
					$this->page_template = TEMPLATE_MEMBERS_DIRECTORY;
					return;
			}
		} else if (count($request_array) == 2) {
			
			if (is_numeric($request_array[1])) {
				$this->paginator_page = intval($request_array[1]);
				$this->smarty->assign('paginator_page', $this->paginator_page);
				switch ($request_array[0]) {
					case 'games':
						$this->page_template = TEMPLATE_DIGEST;
						return;
					case 'administrators':
						$this->page_subtemplate = SUBTEMPLATE_MEMBERS_ADMINISTRATORS;
					case 'members':
						$this->page_template = TEMPLATE_MEMBERS_DIRECTORY;
						return;
				}
			} else {
						
				switch ($request_array[0]) {
					case 'games':
						$this->page_template = TEMPLATE_GAMEPAGE;
						$this->set_game_slug($request_array[1]);
						return;
					case 'members':
						$this->page_template = TEMPLATE_MEMBER;
						return;
						
				}				
					
				
			}
			
				//$this->page_template = TEMPLATE_DIGEST;
				return;
			
		} 
			
		$this->page_template = TEMPLATE_NOT_DETECTED;
	}
	
	*/
	
} // </class>

?>