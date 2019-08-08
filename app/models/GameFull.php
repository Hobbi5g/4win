<?
/**
 * Created by JetBrains PhpStorm.
 * User: rabbitone
 * Date: 07.01.12
 * Time: 3:42
 * To change this template use File | Settings | File Templates.
 */

// getAllGameIdsAsString, только без запросов к базе
function generate_ids_string($ids_array) {
	return implode(', ', $ids_array);
}

class GameFull extends Model {

	var $games;
	var $game_ids;
	var $game_ids_string;

	function __construct($game_identifier) {
		parent::__construct();

		$this->game_ids = $this->conn->getCol("SELECT gameid FROM games WHERE stringid = ? AND hidden = 'N'", 0, array($game_identifier));

		if (empty($this->game_ids)) {
			throw new NotFoundException("Game StringId not found");
		}

		$this->game_ids_string = generate_ids_string($this->game_ids);

		//$game_count = $this->conn->getOne("SELECT count(*) FROM games WHERE gameid = ? AND hidden = 'N'", array($game_id));
		//if ($game_count == 0) {
		//	throw new GameIsHiddenException("Game is hidden");
		//}

		//$this->populate_games($this->game_ids);
		$this->populate_games2();

	}



	function getGameId() {
		return $this->game['gameid'];
	}

	function GetGamesBySameDeveloper() {

		$game_id = $this->getGameId();
		$devname = $this->conn->getOne("SELECT developer FROM games WHERE gameid = ?", array($game_id));

		$resp = $this->conn->getAll("SELECT gameid, stringid, title, shortdesc FROM games WHERE (developer = ? AND gameid != ?) ORDER BY title", array($devname, $game_id));

		return $resp;
	}



	function getNews() {
		$tt = $this->conn->getAll("SELECT UNIX_TIMESTAMP(newsdate) newsdate, SUBSTR(newsdate,1,10) nd, gameid,comment FROM news WHERE (news.gameid = ?) ORDER BY gameid DESC", array($this->getGameId()));
		for ($i = 0; $i<count($tt); $i++) {
			$tt[$i]['comment'] = strip_tags($tt[$i]['comment']);
		}
		return $tt;
	}


/*
	function populate_games($game_ids) {

		$sSQL = "SELECT gameid, stringid, platform, developer, title, version, orderpage, download1, category01, category02, shortdesc, shortdesc_ru, gameprice, fsize, fsize_points, logo, featscale, rating FROM games WHERE (games.gameid IN (?)) ORDER BY game_order";
		$all_game_data = $this->conn->getAll($sSQL, array($game_ids[0]));

		$data = $all_game_data[0];
		//print_r($data);

		$comments = $this->conn->getOne("SELECT count(*) FROM shortreports WHERE gameid = ? AND show_in_digest = 'n' ", array($game_id));

		//list($dl1, $dl2) = $this->GetDownloadLinks($data['stringid']);

		$game = array(
			'gameid' => $data['gameid'],
			'stringid' => $data['stringid'],
			'platform' => $data['platform'],
			'developer' => $data['developer'],
			'title' => stripslashes($data['title']),
			'version' => $data['version'],
			'vendorid' => $data['vendorid'],
			'category01' => ConvertCategory($data['category01']),
			'category02' => ConvertCategory($data['category02']),
			'shortdesc' => strtr($data['shortdesc'], array("\r\n"=>' ')),
			'shortdesc_ru' => strtr($data['shortdesc_ru'], array("\r\n"=>' ')),
			'gameprice' => $data['gameprice'],
			'gameprice_formatted' => ConvertPrice($data['gameprice']),
			'fsize' => $data['fsize'],
			'fsize_formatted' => ConvertFSize($data['fsize'], $data['fsize_points']),
			'logo' => $data['logo'],
			//'logo_w' => $data['logo_w'],
			//'logo_h' => $data['logo_h'],
			//'searchdata' => unserialize($data['searchdataser']),
			'featscale' => $data['featscale'],
			'orderpage' => $data['orderpage'],
			'download1' => $data['download1'],
			//'download1link' => $dl1,
			'download1base' => basename($data['download1']),
			'comments' => $comments,
			'rating' => $data['rating']
		);

		// digest reviews
		//$digest_review = $this->conn->getOne(" SELECT report FROM shortreports WHERE gameid = ? AND show_in_digest = 'y' LIMIT 1", array($gameid));
		//if (!empty($digest_review)) {
		//	$game['digest_review'] = $digest_review;
		//}

		//if ($game['platform'] == "genesis") {
		//	$game['logo_w'] = 220;
		//	$game['logo_h'] = 112;
		//}

		if (file_exists("./images/big/{$game['stringid']}.jpg")) {
			$game['have_teaser'] = true;
		}

		//
		$game['logo'] = str_replace('.gif', '.png', $game['logo']);
		$game['logo'] = str_replace('.jpg', '.png', $game['logo']);

		// общие в getgamedata, getshortgamedata
		$game['title_nodashes'] = $game['title'];
		$game['title_nodashes'] = str_replace('-', ' ', $game['title_nodashes']);
		$game['title_nodashes'] = str_replace("'", '', $game['title_nodashes']);
		$game['title_nodashes'] = str_replace(':', ' ', $game['title_nodashes']);
		$game['title_nodashes'] = str_replace('   ', ' ', $game['title_nodashes']);

		//print_r($game);
		$this->game = $game;

	}
*/

	function getInfo() {
		return $this->game;
	}


	function get_any_digest_image($game_id) {

		$ids_string = "";

		if (is_numeric($game_id)) {
			$identifier = $this->conn->getOne("SELECT identifier FROM gf_games WHERE id = ? AND is_hidden = 0", array($game_id));
			$ids = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ? AND is_hidden = 0", 0, array($identifier));
		} else {
			$ids = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ? AND is_hidden = 0", 0, array($game_id));
		}

		if (!empty($ids)) {
			$ids_string = implode(', ', $ids);
			$row = $this->conn->getRow("SELECT vendor, digest_image FROM gf_games WHERE id IN (?) AND (digest_image IS NOT NULL AND digest_image <> '') AND (vendor <> 'trymedia') ORDER BY FIELD(vendor, 'genesis', 'snes', 'nes', 'trymedia', '') ", array($ids_string) );
			if (!empty($row)) {
				return '/i/' . $row['vendor'] . '/' . $row['digest_image'];
			}
		}

		return '/images/no_logo.png';
	}



	// string $string_ids = "id1,id2,id3"
	function get_similar_games($string_ids, $slice = 0) {
		if (empty($string_ids))
			return null;

		$games = array();
		$similar_ids = $this->conn->getCol("SELECT DISTINCT game_02_id FROM similar_games WHERE game_01_id IN (!)", 0, $string_ids);
		
		foreach ($similar_ids as $id) {
			//echo ($id);
			$game = $this->conn->getRow("SELECT identifier, title, short_description FROM gf_games WHERE id = ?", array($id));
			//print_r($game);
			if (!empty($game)) {
				$game['digest_image'] = $this->get_any_digest_image($game['identifier']);
				array_push($games, $game);
			}
		}
		
		// slicer
		if ($slice > 0 && count($games) > $slice) {
			$games = array_slice($games, 0, $slice);
		}
		
		return $games;
	}


	// $game_ids_list = array(id1, id2, id3)
	/*
	function get_similar_games_by_ids($game_ids_list) {
		if (!is_array($ids)) {
			echo "err";
			exit();
		}
		
		$game_ids_string = implode(', ', $game_ids_list);
		return $this->get_similar_games($game_ids_string);
	}*/


	// int $id = id
	function get_similar_games_by_id($id) {
		if (!is_numeric($id)) { 
			echo "err";
			exit;
		}
		
		$game_identifier = $this->conn->getOne("SELECT identifier FROM gf_games WHERE id = ?", array($id));
		
		$game_ids_list = $this->conn->getCol("SELECT id FROM gf_games WHERE identifier = ?", 0, array($game_identifier));//print_r($game_ids_list);
		$game_ids_string = implode(', ', $game_ids_list);
		return $this->get_similar_games($game_ids_string);
	}



	public function populate_games2() {

		$game_ids = $this->game_ids;

		$all_game_data = $this->conn->getAll(
			"SELECT DISTINCT games.*, rates.* FROM games, rates WHERE ((games.gameid = rates.gameid) AND (games.gameid IN (!))) ORDER BY game_order", array($this->game_ids_string)
		);

		$data = $all_game_data[0];

		list($dl1, $dl2) = $this->GetDownloadLinks($data['stringid']);

		$game = array(
			'gameid' => $data['gameid'],
			'stringid' => $data['stringid'],
			'release_year' => $data['release_year'],
			'userid' => $data['userid'],
			'developer' => $data['developer'],
			'vendorid' => $data['vendorid'],
			'title' => $data['title'],
			'version' => $data['version'],
			'category01' => $data['category01'],
			'category02' => $data['category02'],
			'category01_formatted' => ConvertCategory($data['category01']),
			'category02_formatted' => ConvertCategory($data['category02']),
			///'category01_link' => ConvertCategoryLink($data['category01']),
			'category01_link' => convert_category_readable($data['category01']),
			'category01_url' => convert_category_to_url_part($data['category01']),
			//'category02_link' => ConvertCategoryLink($data['category02']),
			'html_meta_desc' => $data['html_meta_desc'],
			'shortdesc' => strtr($data['shortdesc'], array("\r\n"=>' ')),
			'page_title' => $data['page_title'],
			'rating' => $data['rating'],
			'win311' => $data['win311'],
			'win9x' => $data['win9x'],
			'winnt' => $data['winnt'],
			'win2k' => $data['win2k'],
			'winxp' => $data['winxp'],
			'other' => $data['other'],
			'requires' => $data['requires'],
			'gameprice' => $data['gameprice'],
			'gameprice_formatted' => ConvertPrice($data['gameprice']),
			'fsize' => $data['fsize'],
			'fsize_formatted' => ConvertFSize($data['fsize'], $data['fsize_points']),
			'homepage' => $data['homepage'],
			'download1' => $data['download1'],
			'download2' => $data['download2'],
			'download1link' => $dl1,
			'download2link' => $dl2,
			'download1base' => basename($data['download1']),
			'download2base' => basename($data['download2']),
			'screenshot' => $data['screenshot'],
			//'scr01' => $data['scr01'],
			//'scr02' => $data['scr02'],
			//'scr03' => $data['scr03'],
			'video01' => $data['video01'],
			'orderpage' => $data['orderpage'],
			'logo' => $data['logo'],
			'logo_w' => $data['logo_w'],
			'logo_h' => $data['logo_h'],
			'counter' => $data['counter'],
			'featscale' => $data['featscale'],
			'regdate' => $data['regdate'],
			'hidden' => $data['hidden'],
			//'searchdataser' => unserialize($data['searchdataser']),
			'playability' => $data['playability'],
			'graphics' => $data['graphics'],
			'sounds' => $data['sounds'],
			'quality' => $data['quality'],
			'idea' => $data['idea'],
			'awards' => $data['awards'],
			'time' => $data['time'],
			'action' => $data['action'],
			'age1' => $data['age1'],
			'age2' => $data['age2'],
			'age3' => $data['age3'],
			'age4' => $data['age4'],
			'age5' => $data['age5'],
			'age6' => $data['age6'],
			'cpu' => $data['cpu'],
			'video' => $data['video'],
			'netmode1' => $data['netmode1'],
			'netmode2' => $data['netmode2'],
			'netmode3' => $data['netmode3'],
			'netmode4' => $data['netmode4'],
			//'searchdata' => unserialize($data['searchdataser']),
			'forumtopic' => $data['forumtopic'],
			'parseddownload' => parse_url($dl1),
			'parsedorder' => parse_url($data['orderpage']),
			'parsedhomepage' => parse_url($data['homepage'])

		);

		//
		//$game['reports'] = array();
		//$game['reports'][0] = $data['longdesc'];

		//
		//
		//$sSQL = "SELECT userid, report FROM longreports WHERE (gameid='".$game['gameid']."' AND lang='english')";
		//$sSQL = "SELECT userid, report FROM longreports WHERE (gameid='".$game['gameid']."' AND lang='russian')";


		// user reviews
		$game['main_reviews'] = $this->conn->getAll("SELECT * FROM longreports WHERE (gameid IN (!) AND lang = 'english' AND reporttype = 'main')", array( $this->game_ids_string ));

		if (!empty($game['main_reviews'])) {
			$game['userreviews'] = array();
			$game['userreviews'] = $this->conn->getAll("SELECT * FROM longreports WHERE (gameid IN (!) AND lang = 'english' AND reporttype = 'additional')", array( $this->game_ids_string ));
			$game['userreviews_total'] = count($game['userreviews']);
		} else {
			$game['main_reviews'] = $this->conn->getAll("SELECT * FROM longreports WHERE (gameid IN (!) AND lang = 'english' AND reporttype = 'additional')", array( $this->game_ids_string ));
		}
		//print_r($game['main_reviews']);

		if (!empty($game['main_reviews'])) {
			foreach ($game['main_reviews'] as &$review) {
				//$review['review_html'] = markdown($this->convert_images($review['report_wiki']));

				if (!empty($review['screenshot_tag'])) {
					$review['review_html'] = markdown(headchanger($this->convert_images($review['report_wiki'], $review['screenshot_tag'] )));
				}

			}
			unset($review);
		}
//print_r($game['main_reviews']);



		//echo('--'.$smarty);

        //$game['userreviews'] = array();
        //$game['userreviews'] = $this->conn->getAll("SELECT userid, report, report_wiki FROM longreports WHERE (gameid = ? AND lang = 'english' AND reporttype = 'additional')", array($game['gameid']));
        //$game['userreviews_total'] = count($game['userreviews']);

		if ($game['download2'] == 'http://') $game['download2'] = '';
		if ($game['screenshot'] == 'http://') $game['screenshot'] = '';
		if ($game['orderpage'] == 'http://') $game['orderpage'] = '';


		//
		//if(isset($data['forumtopic']))
		//{
		//	$game['forumposts'] = $this->conn->getOne("SELECT COUNT(*) FROM forum_posts WHERE topic_id='{$data['forumtopic']}'");
		//}


		// platforms
		$platforms = [];
		foreach ($all_game_data as $game_data) {
			array_push($platforms,
				array(
					'platform_name' => $game_data['platform'],
					'release_year' => $game_data['release_year']
				)
			);
		}
		unset($game_data);
		$game['platforms'] = array_unique($platforms, SORT_REGULAR);



		// AKA Also known as
		$also_known_as_titles = [];
		foreach ($all_game_data as $game_data) {
			if (!empty($game_data['also_known_as'])) {
				array_push($also_known_as_titles, $game_data['also_known_as']);
			}
		}
		unset($game_data);

		$game['also_known_as'] = array_unique($also_known_as_titles);




		// similar games
		// TODO: брать Similar games не только из первой игры, но из всех остальных
		$data['similargames'] = trim($data['similargames']);

		$sims = explode("\n", trim($data['similargames']));

		//$sims = array_filter($sims); // delete empty strings
		$sims2 = [];
		for($i=0; $i<count($sims); $i++) {
			$game_identifier = trim($sims[$i]);
			if (!empty($game_identifier)) {
				$sims2[$i] = "'" . $game_identifier . "'";
			}
		}

		$sims = $sims2;

		if (count($sims) > 0) {
			$res = $this->conn->getAll("SELECT stringid, title FROM games WHERE hidden='N' AND stringid IN(!) GROUP BY stringid", array(implode(',', $sims)) );
			if (count($res) > 0) {
				$game['similargames'] = $res;
			}
		}





		$game['homepage_direct'] = $game['homepage'];
		if ($game['vendorid'] != '') {
			$game['homepage_direct'] = "http://www.regnow.com/softsell/visitor.cgi?affiliate=19154&action=site&vendor=" . $game['vendorid'];
		}
		if (trim($game['homepage_direct']) == 'http://' || trim($game['homepage_direct']) == 'https://') {
			$game['homepage_direct'] = null;
		}

		$addedby = $this->conn->getOne("SELECT author,login FROM members WHERE members.userid='".$game['userid']."'");

		//$game['addedby_name']  = $addedby['author'];
		//$game['addedby_login'] = $addedby['login'];

		//
		//$samegames = $this->GetGamesBySameDeveloper();
		//$game['samedeveloper_titles'] = $samegames;
		//$game['samedeveloper_titles_total'] = count($samegames);

		//
		//$gamecomments = TGameInfo::GetCommentsForGame($gameid, $morecomments, $lang);
		//$total = $gamecomments['total'];
		//unset($gamecomments['total']);
		//$game['comments'] = $gamecomments;
		//$game['comments_total'] = $total;

		// requires
		$req = $game['requires'];
		$req = str_replace('[pc]','<img src="/images/system.gif" alt="System" width="16" height="16" align="top">', $req);
		$req = str_replace('[directx]','<img src="/images/icon_dx.gif" alt="DirectX" width="16" height="16" align="top">', $req);
		$req = str_replace('[video]','<img src="/images/video.gif" alt="DirectX" width="16" height="16" align="top">', $req);
		$game['requires_html'] = $req;

		// news
		$game['news'] = $this->getNews();
		$game['news_count'] = count($game['news']);

		//searchdata
		//$keywordstoshow = 10;
		//$keywords = $game['searchdata']['keywords'];

		//if (count($keywords) > $keywordstoshow) $keywords = array_slice($keywords, 0, $keywordstoshow);
		//$game['generatedkeywords'] = $keywords;
		//$game['keywordscount'] = count($keywords);

		//screenshots


		$have_screenshots = false;
		$counter = 0;
		foreach ($all_game_data as &$game_data) {
			$game_data['screenshots'] = $this->conn->getAll("SELECT * FROM screenshots WHERE game_id = ? ORDER BY game_id", array( $game_data['gameid'] ));
			if (!empty($game_data['screenshots'])) {
				$have_screenshots = true;
			}

			foreach ($game_data['screenshots'] as &$screenshot) {
				$thumb = $screenshot['name'];
				if (Helpers::endsWith($thumb, '.png')) {
					$thumb = basename($thumb, '.png') . '.jpg';
				}

				$screenshot['thumb'] = $thumb;

				$counter = $counter + 1;
				$screenshot['title'] = $game['title'] . ' ' . $counter;
			}
			unset($screenshot);

		}
		unset($game_data);

		//print_r($all_game_data);
		//print_r($all_game_data);

		foreach($all_game_data as $game_data) {
			if (!empty($game_data)) {
				$game['screenshots'] = $game_data['screenshots'];
				break;
			}
		}

		$game['have_screenshots'] = $have_screenshots;


		/*
		$game['screenshots'] = $this->conn->getAll("SELECT * FROM screenshots WHERE game_id IN (!) ORDER BY game_id", array($this->game_ids_string));

		$counter = 0;
		foreach ($game['screenshots'] as &$screenshot) {
			$thumb = $screenshot['name'];
			if (Helpers::endsWith($thumb, '.png')) {
				$thumb = basename($thumb, '.png') . '.jpg';
			}

			$screenshot['thumb'] = $thumb;

			$counter = $counter + 1;
			$screenshot['title'] = $game['title'] . ' ' . $counter;
		}
		*/

		/*
		if (!empty($game['screenshots'])) {
			if (!empty($game['screenshots'][0])) {
				$game['screenshots'][0]['title'] = $game['title'];
			}

			if (!empty($game['screenshots'][1])) {
				$game['screenshots'][1]['title'] = $game['title'] . ' Download';
			}

			if (!empty($game['screenshots'][2])) {
				$game['screenshots'][2]['title'] = $game['title'] . ' Game';
			}
		}
		*/


		//
		$game['logo'] = str_replace('.gif', '.png', $game['logo']);
		$game['logo'] = str_replace('.jpg', '.png', $game['logo']);

		//
		//$sSQL = "SELECT stringid, title, platform, logo, logo_w, logo_h FROM games WHERE hidden='N' AND games.gameid>'".$game['gameid']."' AND category01='".$game['category01']."'"; //"
		//$game['nextgame'] = $this->conn->getRow($sSQL); //print_r($game['nextgame']);
        //echo($sSQL);
        $game['nextgames'] = $this->conn->getAll("SELECT stringid, title, platform, logo, logo_w, logo_h FROM games WHERE hidden='N' AND games.gameid > ? AND category01 = ? LIMIT 8", array($game['gameid'], $game['category01']));



		// общие в getgamedata, getshortgamedata
		$game['title_nodashes'] = $game['title'];
		$game['title_nodashes'] = str_replace('-', ' ', $game['title_nodashes']);
		$game['title_nodashes'] = str_replace("'", '', $game['title_nodashes']);
		$game['title_nodashes'] = str_replace(':', ' ', $game['title_nodashes']);
		$game['title_nodashes'] = str_replace('   ', ' ', $game['title_nodashes']);


		$game['all_game_data'] = $all_game_data;

		$this->game = $game;

		//print_r($game);
		return $game;
	}


	function get_bottompage_screenshots() {

		$game['screenshots'] = $this->conn->getAll("SELECT * FROM screenshots WHERE game_id IN (!) ORDER BY game_id", array($this->game_ids_string));

		$counter = 0;
		foreach ($game['screenshots'] as &$screenshot) {
			$thumb = $screenshot['name'];
			if (Helpers::endsWith($thumb, '.png')) {
				$thumb = basename($thumb, '.png') . '.jpg';
			}

			$screenshot['thumb'] = $thumb;

			$counter = $counter + 1;
			$screenshot['title'] = $game['title'] . ' ' . $counter;
		}

	}



	/**
	 * Links for game download
	 *
	 * @package games4win
	 * @author Reggie
	 * @since 1.0
	 */
	function GetDownloadLinks($strid)
	{

		$data = $this->conn->getRow("SELECT vendorid, title, download1, download2, shortdesc FROM games WHERE (games.stringid = ?)", array($strid));
		$dl01 = $data['download1'];
		$dl02 = $data['download2'];
		$game_description = $data['shortdesc'];
		$game_title = $data['title'];

        if ($dl01 == 'http://' || $dl01 == 'https://') {
            $dl01 = 'http://';
        }

        if ($dl02 == 'http://' || $dl02 == 'https://') {
            $dl02 = '';
        }

        if (empty($dl01)) {
            $dl01 = 'https://gamefabrique.com/dl/nes/super_mario_bros.exe';
        }



		//echo "2".'-'.$data['vendorid'].'-'.$dl01.'-';
		if ($data['vendorid'] != '')
		{
			if (false!==strpos($dl01,'regnow'))
			{
				//echo "2-1";

			} else
			{

				if (false!==strpos($dl01,'games4win'))
				{

					//echo "2-2";

				}
				else// if (false===strpos($dl01,'games4win'))
				{
					//echo "2-3";
					$dl01 = "http://www.regnow.com/softsell/visitor.cgi?affiliate=19154&action=site&vendor={$data['vendorid']}&ref=".$dl01;
				}
			}
		}

		//echo "3";
		if ($data['vendorid'] != '')
		{
			if (false!==strpos($dl02,'regnow'))
			{
			} else
			{

				if (false!==strpos($dl02,'games4win'))
				{
				}
				else// if (false===strpos($dl02,'games4win'))
				{
					$dl02 = "http://www.regnow.com/softsell/visitor.cgi?affiliate=19154&action=site&vendor={$data['vendorid']}&ref=".$dl02;
				}
			}
		}

		$show_installer = $this->conn->getOne("SELECT value FROM settings WHERE name = 'show_installer'");
		$is_installcore = false;
		if ($show_installer == 'true' && ENVIRONMENT == 'production') {
			$dl01 = $this->convertDownloadLinkInstallcore($game_title, $game_description, $dl01);
			$is_installcore = true;
		}

		return array($dl01, $dl02);
	}


	function titleToExeFilename($gameTitle) {
		$filename = $gameTitle;
		$filename = str_replace("'", "", $filename);
		$filename = str_replace(":", "", $filename);

		$filename = strtolower($filename);

		$filename = str_replace("  ", " ", $filename);
		$filename = str_replace("  ", " ", $filename);
		$filename = str_replace("  ", " ", $filename);

		$filename = str_replace(" ", "_", $filename);

		return $filename;
	}


	function convertDownloadLinkInstallcore($product_title, $product_description, $link_to_convert) {
        //error_log('1.' . $product_title . ' 2.' . $link_to_convert . ' 3.' . $product_description);

		// Prepare injection parameters
		$injection_params = array(
			'PRODUCT_TITLE'        => $product_title, // 'ironSource'
			'DOWNLOAD_URL'         => $link_to_convert, // 'http://www.ironsrc.com/test.exe'
			'PRODUCT_DESCRIPTION'  => $product_description, // 'ironSource! Your best monetization option out there!'
			'PRODUCT_FILE_NAME'    => basename($link_to_convert), // 'Test.exe'
			'PRODUCT_VERSION'      => '1.0',
			'PRODUCT_FILE_SIZE'    => '1.5MB',
			'PRODUCT_PUBLIC_DATE'  => '01/30/2015',
			'CHNL' => 'games4win'

		);

		// Initialize IC client
		$client = new IC_Client(
			9519 // User ID
			,   'Games4Win.txt' // Path to the public key file
			,	'http://isp.zayatsservfiles.com' // ISP Domain
			, 	'http://cdn.games4windownloads.com' // Used as a Fallback Domain
		);
		// Get link for installer with provided parameter
		$download_link = $client->get_link(
			$injection_params // Injection parameters
			//,	basename($link_to_convert) // Set the name of the downloaded file in the user's browser
			,	$this->titleToExeFilename($product_title) // Set the name of the downloaded file in the user's browser
			,	$link_to_convert // Fallback URL - In case of injection / encryption errors. Should be the carrier URL
		);


		return $download_link;
	}



	function getGameTitle() {
		return $this->game['title'];
	}




	// $image_prefix = game_identifier ('spider-man', etc)
	function convert_images($text, $game_screenshot_tag = 'ss') {

		$pattern = '/<!--\s*images\s*=\s*(.*?)\s*-->/';
		if (preg_match($pattern, $text)!=false) {

			$text = preg_replace_callback(
				$pattern,
				function ($matches) use ($game_screenshot_tag) {
					/*
					 * matches
					 * Array
					 *(
					 * [0] => <!--images=4,5-->
					 * [1] => 4,5
					)*/

					$images_list = trim($matches[1]);
					$images = array_map('trim', explode(',', $images_list));

					foreach($images as &$image) {
						//$image = '01';
						$image = sprintf("%02d", $image); // zerofill
					}
					unset($image);

					global $smarty;
					$smarty->assign('game_screenshot_tag', $game_screenshot_tag);
					$smarty->assign('image_list', $images);
					$output = $smarty->fetch('image_list.tpl');
					$smarty->clearAssign('game_screenshot_tag');
					$smarty->clearAssign('image_list');

					return $output;
				}
				, $text
			);

		}

		return $text;

	}


}
