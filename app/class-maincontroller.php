<?
class MainController extends ApplicationController {


	function sitemap() {
		
		$this->identifiers = $this->conn->getCol("SELECT DISTINCT identifier FROM gf_games WHERE is_hidden='0'");
		$this->yield_me('sitemap.tpl', 'xml');
	}


	function rss() {
		$this->descrs = $this->conn->getAll("SELECT d.*, g.title, g.identifier, g.digest_image, g.vendor, rss.date_override FROM gf_game_descriptions d JOIN gf_games g ON g.id = d.game_id JOIN rss_titles rss ON rss.game_description_id = d.id ORDER BY rss.date_override, d.created_at LIMIT 10");
		
		$this->last_build_date = date('D, d M Y H:i:s +0000');
		$this->pub_date = $this->last_build_date;
		
		foreach ($this->descrs as &$descr) {
			//$image_url = "http://www.gamefabrique.com/i/{$descr.vendor}/{$descr.digest_image}";
			
			$post_date = '';
			if (empty($descr['date_override'])) {
				$post_date = $descr['created_at'];
			}
			else {
				$post_date = $descr['date_override'];
			}
						
			$descr['body'] = htmlentities("<img src=\"http://www.gamefabrique.com/i/{$descr['vendor']}/{$descr['digest_image']}\" border=\"0\" alt=\"{$descr['title']}\" width=\"220\" height=\"112\" />" . markdown($descr['body']));
			$descr['date'] = Helpers::mysql2date('D, d M Y H:i:s +0000', $post_date);
		}
		
		$this->yield_me('rss.tpl', 'xml');
	}

}
