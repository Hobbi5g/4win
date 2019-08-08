<?

global $conn;

function GetGameCategories($which=1)
{
	global $conn;

		if ($which!=1 && $which!=2) $which = 1;
		if ($which == 1)
			$cat_name = 'category01';
		else 
			$cat_name = 'category02';

		$cats = array(); // categories here

	//print_r($conn);

	$datas = $conn->getAll("SHOW FIELDS FROM games");

	    foreach($datas as $data)
		{
			if ($data['Field'] == $cat_name)
			{
				$catdata = $data['Type'];
		        $catdata = substr($catdata, 5);
		        $catdata = substr($catdata, 0,-1);
				$parts=explode(",",$catdata);

				foreach($parts as $p)
				{
					$p = substr($p, 1,-1);
					array_push($cats,$p);
				}

			}
		}

	    //$base->BaseFinish();
	    //$base->BaseDisconnect();

		return $cats;
}



function GetDownloadType()
{
	global $conn;


	$cats = array(); // categories here

	$datas = $conn->getAll("SHOW FIELDS FROM games");

	foreach($datas as $data)
	{
		if ($data['Field'] == 'download_type')
		{
			$catdata = $data['Type'];
	        $catdata = substr($catdata, 5);
	        $catdata = substr($catdata, 0,-1);
			$parts=explode(",",$catdata);
    
			foreach($parts as $p)
			{
				$p = substr($p, 1,-1);
				array_push($cats,$p);
			}

		}
	}

	return $cats;
}


// ����������� ���� ��� - win, dos, genesis ���
function GetPlatformType()
{
	global $conn;

	$cats = array(); // categories here

	$datas = $conn->getAll("SHOW FIELDS FROM games");

	foreach($datas as $data)
	{
		if ($data['Field'] == 'platform')
		{
			$catdata = $data['Type'];
	        $catdata = substr($catdata, 5);
	        $catdata = substr($catdata, 0,-1);
			$parts=explode(",",$catdata);
    
			foreach($parts as $p)
			{
				$p = substr($p, 1,-1);
				array_push($cats,$p);
			}

		}
	}
	          
	return $cats;
}



//
function GetGameData($game, $is_name=false)
{
	global $conn;

	if (!$is_name) {

	    $gameid = $game;

    	$data = $conn->getRow("SELECT DISTINCT games.*, rates.* FROM games,rates WHERE ( (games.gameid = rates.gameid) AND (games.gameid = ?))", array($gameid));
		$game = array(
		    'gameid' => $data['gameid'],
		    'platform' => $data['platform'],
		    'trymedia_product_id' => $data['trymedia_product_id'],
    		'stringid' => $data['stringid'],
		    'userid' => $data['userid'],
	    	'developer' => $data['developer'],
	    	'vendorid' => $data['vendorid'],
		    'title' => $data['title'],
			'also_known_as' => $data['also_known_as'],
		    'release_year' => $data['release_year'],
		    'category01' => $data['category01'],
		    'category02' => $data['category02'],
		    'download_type' => $data['download_type'],
		    'category01_formatted' => ConvertCategory($data['category01']),
		    'category02_formatted' => ConvertCategory($data['category02']),
		    'shortdesc' => strtr($data['shortdesc'], array("\r\n"=>' ')),
		    'shortdesc_ru' => strtr($data['shortdesc_ru'], array("\r\n"=>' ')),
		    'longdesc_wiki' => $data['longdesc_wiki'],
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
		    'fsize_points' => $data['fsize_points'],
		    'fsize_formatted' => ConvertFSize($data['fsize']),
		    'homepage' => $data['homepage'],
		    'download1' => $data['download1'],
		    'download2' => $data['download2'],
		    'download1base' => basename($data['download1']),
		    'download2base' => basename($data['download2']),
		    'screenshot' => $data['screenshot'],
		    'scr01src' => $data['scr01src'],
		    'scr02src' => $data['scr02src'],
		    'scr03src' => $data['scr03src'],
		    'orderpage' => $data['orderpage'],
		    'logo' => $data['logo'],
		    'counter' => $data['counter'],
		    'featscale' => $data['featscale'],
		    'regdate' => $data['regdate'],
		    'hidden' => $data['hidden'],
		    'searchdataser' => $data['searchdataser'],
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
			'forumtopic' => $data['forumtopic'],
			'similargames' => $data['similargames']
 		);

		$game['reports'] = array();
		$game['reports'][0] = $data['longdesc']; //echo 'XXXXX:'.$game['longdesc_wiki'].'XXXXX';
		if (empty($data['longdesc_wiki']))
			$game['longdesc_wiki'] = $data['longdesc'];

		if ($game['download2'] == 'http://') $game['download2'] = '';
		if ($game['screenshot'] == 'http://') $game['screenshot'] = '';
		if ($game['orderpage'] == 'http://') $game['orderpage'] = '';

		$game['homepage_direct'] = $game['homepage'];
		if ($game['vendorid'] != '')
			$game['homepage_direct'] = "http://www.regnow.com/softsell/visitor.cgi?affiliate=19154&action=site&vendor=".$game['vendorid'];

	    $addedby = $conn->getRow("SELECT author,login FROM members WHERE members.userid=?", array($game['userid']));

		$game['addedby_name']  = $addedby['author'];
		$game['addedby_login'] = $addedby['login'];

		$counter=1;
		if ($game['reports'][0] == '') $counter=0; // no description found for this game

		$reps = $conn->getAll("SELECT userid,report FROM longreports WHERE (gameid=?)", array($game['gameid']));
	    foreach($reps as $rep) {
			$game['reports']["$counter"] = stripslashes($rep['report']);
			$counter++;
		}
		$game['reports']['number'] = $counter;

		if (strlen($game['searchdataser']) > 0)
			$game['searchdataser'] = unserialize($data['searchdataser']);


		//
		$game['logo'] = str_replace('.gif','.png',$game['logo']);
		$game['logo'] = str_replace('.jpg','.png',$game['logo']);


		return $game;
	}

}



// ������ ����, ���������� ��� �� �������������
function GetGamesBySameDeveloper($gameid)
{
	    $base = new MySQLconnector;
	    $base->BaseSelect("SELECT developer FROM games WHERE gameid=$gameid");
		$dev = $base->BaseFetch();
	    $base->BaseFinish();
		$devname = $dev['developer'];
		$devname = str_replace("'","\'",$devname);
//		print_r($devname);
	    $base->BaseSelect("SELECT gameid, stringid, title, version, shortdesc FROM games WHERE (developer='".$devname."' AND gameid!=".$gameid.") ORDER BY title");
	    // $addedby = $base->BaseFetch();
		$counter = 0;
		while($rep = $base->BaseFetch())
		{
			//print_r($rep);
			//print($counter);
			$resp[$counter] = $rep;
			$counter++;
		}
	    $base->BaseFinish();

//		print "<pre>";
//		print_r($resp);
//		print "</pre>";

		return $resp;

}


// ������ ����������� ������������� � ����
function GetCommentsForGame($gameid, $morecomments=false)
{
	    $base = new MySQLconnector;
	    $base->BaseSelect("SELECT COUNT(*) FROM shortreports WHERE gameid=".$gameid);
		$numof1 = $base->BaseFetch();
		$numof = $numof1[0];
	    $base->BaseFinish();

		if (!$morecomments)
		    $base->BaseSelect("SELECT userid,icon,report,username FROM shortreports WHERE gameid=".$gameid." ORDER BY reportid DESC LIMIT 0,5");
		else
		    $base->BaseSelect("SELECT userid,icon,report,username FROM shortreports WHERE gameid=".$gameid." ORDER BY reportid DESC");
		$counter = 0;
		while($rep = $base->BaseFetch())
		{
			$resp[$counter] = $rep;
			$counter++;
		}
	    $base->BaseFinish();
		$resp['numof'] = $numof;

		return $resp;
}


// �� ������� � ���������� ���������� ������ ��� ����������� �� ��������
function ConvertFSize($fs)
{
	if ($fs < 1024)
	{
		$val = $fs.'K';
		return $val;
	} else
	{
		$fs /= 1024;
		$fs = sprintf("%01.2f",$fs);
		$fs = str_replace('.', ',',$fs);
		$val = $fs.'M';
		return $val;
	}
}


function ConvertPrice($p)
{
	if ($p == 0 || $p == -1) return $p;
	$pr = $p;

	$p /= 100;
	$p = sprintf("%01.2f",$p);
	$p = str_replace('.', ',',$p);
	if ($pr % 100 == 0) // for prices like 10.00
		$p = substr($p,0,strlen($p)-3);
	$val = '$'.$p;
	return $val;
}


function ConvertCategory($cat)
{
//	$cat = str_replace('_', '/',$cat);
	$data = array(	'none'=>'none',
					'arcade_action'=> 'arcade/action',
					'arkanoids' => 'arkanoid',
					'adventure_rpg' => 'adventure/rpg',
					'board' => 'board game',
					'tetris' => 'tetris',
					'card' => 'card',
					'logic_puzzle' => 'logic/puzzle',
					'shooter' => 'shooter',
					'strategy_war' => 'strategy or wargame',
					'handheld' => 'handheld');
	foreach($data as $k=>$v)
		if ($cat == $k) return $v;
	return "none";
}



//
function GetShortGameData($game)
{
	    $gameid=$game;
	    $base = new MySQLconnector;

		if (is_numeric($gameid))
			$q = "SELECT gameid, stringid, developer, title, version, orderpage, download1, category01, category02, shortdesc, gameprice, fsize, logo, featscale FROM games WHERE (games.gameid = $gameid)";
		else
			$q = "SELECT gameid, stringid, developer, title, version, orderpage, download1, category01, category02, shortdesc, gameprice, fsize, logo, featscale FROM games WHERE (games.stringid = '$gameid')";

	    $base->BaseSelect($q);
    	$data = $base->BaseFetch();
	    $base->BaseFinish();
		$game = array(
		    'gameid' => $data['gameid'],
    		'stringid' => $data['stringid'],
	    	'developer' => $data['developer'],
		    'title' => stripslashes($data['title']),
		    'version' => $data['version'],
	    	'vendorid' => $data['vendorid'],
		    'category01' => ConvertCategory($data['category01']),
		    'category02' => ConvertCategory($data['category02']),
		    'shortdesc' => strtr($data['shortdesc'], array("\r\n"=>' ')),
		    'gameprice' => $data['gameprice'],
		    'gameprice_formatted' => ConvertPrice($data['gameprice']),
		    'fsize' => $data['fsize'],
		    'fsize_formatted' => ConvertFSize($data['fsize']),
		    'logo' => $data['logo'],
		    'featscale' => $data['featscale'],
		    'orderpage' => $data['orderpage'],
		    'download1base' => basename($data['download1'])

 		);

		//��������� ����
		$game['logo'] = str_replace('.gif','.png',$game['logo']);
		$game['logo'] = str_replace('.jpg','.png',$game['logo']);

		return $game;
}




//
function GetGameDataOldSubmit($fname)
{
//		$root = $submits_path;
		$root = 'submits';
	 	$lines = file($root.'/'.$fname);
		for($i=0;$i<count($lines);$i++)
			$lines{$i} = trim($lines{$i});

		// -------------------------------------------------------------------
		$q = parse_url($lines[12]);
        parse_str($q['query']);
		if (isset($item))
		{
        	$vndr = substr($item,0,4);
		} else
			$vndr = '';
		
		$win=explode("/",$lines[7]);
		for($i=0;$i<count($win);$i++)
			if ($win[$i] == 'on')
				$win[$i] = 'Y'; else
				$win[$i] = 'N'; 
		
 		list($size,$unit) = explode(' ',$lines[16]);
 		strtr($size,',','.');
 		 if ($unit == 'Mb') $size = $size*1024;

		// -------------------------------------------------------------------

		$game = array(
		    'gameid' => '',
    		'stringid' => '',
		    'userid' => '',
	    	'developer' => $lines[4],
	    	'vendorid' => $vndr,
		    'title' => stripslashes($lines[2]),
		    'version' => $lines[3],
		    'category01' => 'none',
		    'category02' => 'none',
		    'shortdesc' => strtr($lines[9], array('\n'=>' ')),
		    'page_title' => '',
		    'rating' => $data['rating'],
		    'win311' => $win[0],
		    'win9x' => $win[1],
		    'winnt' => $win[2],
		    'win2k' => $win[3],
		    'winxp' => $win[4],
		    'other' => $win[5],
		    'requires' => $lines[8],
		    'gameprice' => $lines[6],
		    'fsize' => $size,
		    'homepage' => $lines[11],
		    'download1' => $lines[14],
		    'download2' => $lines[15],
		    'screenshot' => $lines[13],
		    'orderpage' => $lines[12],
		    'logo' => $data['logo'],
		    'counter' => $data['counter'],
		    'featscale' => $data['featscale'],
		    'regdate' => $data['regdate'],
		    'hidden' => $data['hidden'],
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
		    'netmode4' => $data['netmode4']
 		);
		$game['reports'] = array();		
		$game['reports'][0] = strtr($lines[10], array('\n'=>'<br>'));

//	    $base->BaseSelect("SELECT author,login FROM members WHERE members.userid='".$game['userid']."'");
//	    $addedby = $base->BaseFetch();
//	    $base->BaseFinish();

		$game['addedby_name']  = $addedby['author'];
		$game['addedby_login'] = $addedby['login'];

		$counter=1;
		if ($game['reports'][0] == '') $counter=0; // no description found for this game
//	    $base->BaseSelect("SELECT userid,report FROM longreports WHERE (gameid='".$game['gameid']."')");
//	    while(($rep = $base->BaseFetch()))
//		{
//			$game['reports']["$counter"] = $rep['report'];
//			$counter++;
//		}
//	    $base->BaseFinish();

		return $game;
}


function GetNewsByGame($id)
{
//	echo '!!!'.$id;
	$tt = array();
	if ($id !='')
	{
	    $base = new MySQLconnector;
//    $base->BaseSelect("SELECT UNIX_TIMESTAMP(newsdate) newsdate, gameid,comment FROM news WHERE (news.gameid = $id) ORDER BY news.newsdate DESC");
	    $base->BaseSelect("SELECT UNIX_TIMESTAMP(newsdate) newsdate, gameid,comment FROM news WHERE (news.gameid = $id) ORDER BY gameid DESC");
   			while(($data = $base->BaseFetch()))
			$tt[] = $data;
	    $base->BaseFinish();
    	$base->BaseDisconnect();
	}
	return $tt;
}


function isGame($strid)
{
    $base = new MySQLconnector;
    $base->BaseSelect("SELECT COUNT(*) FROM games WHERE (games.stringid = '$strid')");
   	$data = $base->BaseFetch();
    $base->BaseFinish();
    $base->BaseDisconnect();
	if ($data['COUNT(*)'] == 0)
		return false; 
	else 
		return true;

}

function GetDownloadLinks($strid)
{
    $base = new MySQLconnector;
    $base->BaseSelect("SELECT vendorid,download1,download2 FROM games WHERE (games.stringid = '$strid')");
   	$data = $base->BaseFetch();
    $base->BaseFinish();
    $base->BaseDisconnect();
	$dl01 = $data['download1'];
	$dl02 = $data['download2'];
	if ($dl02 == 'http://')
		$dl02 = '';

	if ($data['vendorid'] != '')
		$dl01 = "http://www.regnow.com/softsell/visitor.cgi?affiliate=19154&action=site&vendor={$data['vendorid']}&ref=".$dl01;
	if ($data['vendorid'] != '' && $dl02 != '')
		$dl02 = "http://www.regnow.com/softsell/visitor.cgi?affiliate=19154&action=site&vendor={$data['vendorid']}&ref=".$dl02;


	return array($dl01,$dl02);
}

