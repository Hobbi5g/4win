<?
    include ($s_addurl."login.php");

	global $conn;
    //require_once '../db_conn.php';

	// feed upload action
	if ($_POST['action'] == 'uploadfeed') {
	
	    $feed = array();
		$feed = file('./cachedfeed/trymedia_tab.txt');
	
		$conn->query("DELETE FROM trymediafeed");
	
		//$atitle = 'productid	date	title	publisher	englishshortdescription	englishlongdescription	dutchdescription	italiandescription	germandescription	frenchdescription	usdprice	europrice	gbpprice	cadprice	audprice	nzdprice	jpyprice	filesize	trialcriteria	systemrequirements	triallink	buylink	boxshot	screenshot1	screenshot2	screenshot3	esrb	webgamelink	webgamelinkjs	webgamedescriptionenglish	webgamedescriptionfrench	webgamedescriptiongerman	webgamedescriptiondutch	webgamedescriptionitalian	award	genre	bullets';
		$atitle = 'productid, date, title, publisher, englishshortdescription, englishlongdescription, dutchdescription, italiandescription, germandescription, frenchdescription, usdprice, europrice, gbpprice, cadprice, audprice, nzdprice, jpyprice, filesize, trialcriteria, systemrequirements, triallink, buylink, boxshot, screenshot1, screenshot2, screenshot3, esrb, webgamelink, webgamelinkjs, webgamedescriptionenglish, webgamedescriptionfrench, webgamedescriptiongerman, webgamedescriptiondutch, webgamedescriptionitalian, award, genre, bullets';
		
		//$title = split("\t", rtrim($feed[0]));
		
		//$title = split("\t", $atitle);
		$title = preg_split('/, /', $atitle);
		//print_r($title);
		$titlecount = count($title);
	
	
		for($line=1; $line<count($feed); $line++) {
			$res = array();
			$info = $feed[$line];
			$info = explode("\t", $info);
			for($i=0; $i<$titlecount; $i++) {
				$res[$title[$i]] = addslashes(trim(trim($info[$i]), '"'));
			}
	
			$sSQL = " INSERT INTO trymediafeed SET ".
					" productid = '{$res[productid]}',".
					" date = STR_TO_DATE('{$res[date]}', '%m/%d/%Y'),".
					" title = '{$res[title]}',".
					" publisher = '{$res[publisher]}',".
					" englishshortdescription = '{$res[englishshortdescription]}',".
					" englishlongdescription = '{$res[englishlongdescription]}',".
					(empty($res['dutchdescription']) ?  "" : " dutchdescription = '{$res[dutchdescription]}',").
					(empty($res['italiandescription']) ?  "" : " italiandescription = '{$res[italiandescription]}',").
					(empty($res['germandescription']) ?  "" : " germandescription = '{$res[germandescription]}',").
					(empty($res['frenchdescription']) ?  "" : " frenchdescription = '{$res[frenchdescription]}',").
					" usdprice = '{$res[usdprice]}',".
					(empty($res['europrice']) ?  "" : " europrice = '{$res[europrice]}',").
					(empty($res['gbpprice']) ?  "" : " gbpprice = '{$res[gbpprice]}',").
					(empty($res['cadprice']) ?  "" : " cadprice = '{$res[cadprice]}',").
					(empty($res['audprice']) ?  "" : " audprice = '{$res[audprice]}',").
					(empty($res['nzdprice']) ?  "" : " nzdprice = '{$res[nzdprice]}',").
					(empty($res['jpyprice']) ?  "" : " jpyprice = '{$res[jpyprice]}',").
					" filesize = '{$res[filesize]}',".
					" trialcriteria = '{$res[rialcriteria]}',".
					" systemrequirements = '{$res[systemrequirements]}',".
					(empty($res['triallink']) ?  "" : " triallink = '{$res[triallink]}',").
					" buylink = '{$res[buylink]}',".
					" boxshot = '{$res[boxshot]}',".
					" screenshot1 = '{$res[screenshot1]}',".
					" screenshot2 = '{$res[screenshot2]}',".
					" screenshot3 = '{$res[screenshot3]}',".
					" esrb = '{$res[esrb]}',".
					(empty($res['webgamelink']) ?  "" : " webgamelink = '{$res[webgamelink]}',").
					(empty($res['webgamelinkjs']) ?  "" : " webgamelinkjs = '{$res[webgamelinkjs]}',").
					(empty($res['webgamedescriptionenglish']) ?  "" : " webgamedescriptionenglish = '{$res[webgamedescriptionenglish]}',").
					(empty($res['webgamedescriptionfrench']) ?  "" : " webgamedescriptionfrench = '{$res[webgamedescriptionfrench]}',").
					(empty($res['webgamedescriptiongerman']) ?  "" : " webgamedescriptiongerman = '{$res[webgamedescriptiongerman]}',").
					(empty($res['webgamedescriptiondutch']) ?  "" : " webgamedescriptiondutch = '{$res[webgamedescriptiondutch]}',").
					(empty($res['webgamedescriptionitalian']) ?  "" : " webgamedescriptionitalian = '{$res[webgamedescriptionitalian]}',").
					(empty($res['award']) ?  "" : " award = '{$res[award]}',").
					" genre = '{$res[genre]}'".
					(empty($res['bullets']) ?  "" : " ,bullets = '{$res[bullets]}'");
	
	                //echo '<pre>'; print_r($res); echo '</pre>';
					//echo $res['bullets'];
	
					$conn->query($sSQL);
		}
	
	
		?><font color="red">Feed uploaded!</font><?
	
		//print_r($feed);
	} // action == uploadfeed
	
	


	if ($addgames) {
		foreach ($ids as $id) {
			$sSQL = "SELECT * FROM trymediafeed WHERE productid='$id'";
			$game = $conn->getRow($sSQL);
			$stringid = GenerateStringId($game['title']);

			$game['title'] = addslashes($game['title']);
			$longdesc = $game['title'].' is a '.$game['genre'].' game.'.
						"\n\n".
						$game['englishshortdescription'].
						"\n\n".
						$game['englishlongdescription'].
						(empty($game['bullets']) ? "" : "\n\n".$game['title']." Includes:\n * ".str_replace('<br>', "\n * ", $game['bullets']) ).
						"\n\n".
						"Note: The Download Now link will download a small installer file to your desktop. Remain online and double-click the installer to proceed with the actual download.".
						"";
			$longdesc = addslashes($longdesc);
			$game['englishshortdescription'] = addslashes($game['englishshortdescription']);

			$game['systemrequirements'] = str_replace('<br>', ', ', $game['systemrequirements']);
			$game['systemrequirements'] = addslashes($game['systemrequirements']);

			$boxshot = $game['boxshot'];

			$shortpngname = '';
			$im = imagecreatefromjpeg($boxshot);
			$sy = 100;
			if($im)
			{
				$root = '../logos';
				$shortpngname = $stringid.'_'.$game['productid'].'.png';
				$filename2 = $root.'/'.$shortpngname;
				$sy = (imagesy($im) * 100) / imagesx($im);
				$im2 = imagecreatetruecolor(100, $sy  );
				imagecopyresampled($im2, $im, 0,0, 0,0,   100,$sy,   imagesx($im),imagesy($im)  );
				imagepng($im2, $filename2);
			}


			// PRICE
			$game['usdprice'] = str_replace('.', '', $game['usdprice']);
			if (strlen($game['usdprice']) <= 2) $game['usdprice'] .= '00'; // 9 -> 900, 10 -> 1000, 1995 -> 1995

			// FILE SIZE
			$game['filesize'] = substr($game['filesize'], 0, strlen($game['filesize'])-2);

			$sSQL = <<<REQ01
INSERT INTO games
SET		
		games.hidden = 'Y',
		games.developer = '{$game['publisher']}',
		games.trymedia_product_id = '{$game['productid']}',
		games.title = '{$game['title']}',
		games.stringid = '{$stringid}',
		games.version = '1.0',
		games.`category01` = 'none',
		games.`category02` = 'none',
		games.shortdesc = '{$game['englishshortdescription']}',
		games.longdesc = '',
		games.longdesc_wiki = '{$longdesc}',
		games.page_title = '',
		games.rating = '0',
		games.`win311` = 'N',
		games.`win9x` = 'Y',
		games.winnt = 'N',
		games.`win2k` = 'Y',
		games.winxp = 'Y',
		games.other = 'N',
		games.requires = '[pc]{$game['systemrequirements']}',
		games.gameprice = '{$game['usdprice']}',
		games.fsize = '{$game['filesize']}',
		games.fsize_points = 'Mb',
		games.homepage = 'http://www.trygames.com/game/aff=t_18oi/vid={$game['productid']}',
		games.`download1` = '{$game['triallink']}',
		games.`download2` = '',
		games.screenshot = '{$game['screenshot1']}',
		games.orderpage = '{$game['buylink']}',
		games.logo = '{$shortpngname}',
		games.logo_w = '100',
		games.logo_h = '{$sy}',
		games.featscale = '0',
		games.similargames = ''
REQ01;

		$conn->query($sSQL);

		$gameid = $conn->getOne("SELECT LAST_INSERT_ID() a FROM games");

		$sSQL = <<<RATES001
INSERT INTO rates 
SET gameid = {$gameid},
	playability='0',
	graphics='0',
 	sounds='0',
  	quality='0',
  	idea='0',
  	awards='0',
  	time='0',
  	action='0',
  	age1='0',
  	age2='0',
  	age3='0',
  	age4='0',
  	age5='0',
  	age6='0',
  	cpu='1',
  	video='1',
  	netmode1='Y',
  	netmode2='N',
  	netmode3='N',
  	netmode4='N'
RATES001;

			$conn->query($sSQL);
			//echo $sSQL.'<br><br>';
		
			if (!empty($game['dutchdescription']))
			{
				$game['dutchdescription'] = addslashes($game['dutchdescription']);
				$sSQL = " INSERT INTO longreports".
						" SET".
						" gameid = {$gameid}, ".
						" userid = 0, ".
						" report = '{$game['dutchdescription']}', ".
						" lang='dutch', ".
						" reporttype='main'";
				$conn->query($sSQL);
			}

			if (!empty($game['italiandescription']))
			{
				$game['italiandescription'] = addslashes($game['italiandescription']);
				$sSQL = " INSERT INTO longreports".
						" SET".
						" gameid = {$gameid}, ".
						" userid = 0, ".
						" report = '{$game['italiandescription']}', ".
						" lang='italian', ".
						" reporttype='main'";
				$conn->query($sSQL);
			}

			if (!empty($game['germandescription']))
			{
				$game['germandescription'] = addslashes($game['germandescription']);
				$sSQL = " INSERT INTO longreports".
						" SET".
						" gameid = {$gameid}, ".
						" userid = 0, ".
						" report = '{$game['germandescription']}', ".
						" lang='german', ".
						" reporttype='main'";
				$conn->query($sSQL);
			}

			if (!empty($game['frenchdescription']))
			{
				$game['frenchdescription'] = addslashes($game['frenchdescription']);
				$sSQL = " INSERT INTO longreports".
						" SET".
						" gameid = {$gameid}, ".
						" userid = 0, ".
						" report = '{$game['frenchdescription']}', ".
						" lang='french', ".
						" reporttype='main'";
				$conn->query($sSQL);
			}


		} // foreach

	} // addgames


	$ids = $conn->getCol("SELECT trymedia_product_id FROM games WHERE trymedia_product_id IS NOT NULL");
	$idsline = implode("','", $ids);
	
	//$sSQL = "SELECT * FROM trymediafeed WHERE productid NOT IN ($idsline) ORDER BY title";
	
	//$sSQL = " SELECT trymediafeed.*, IF (trymediafeed.title = games.title, '+', '') alreadyadded, IF (trymediafeed.title = games.title, games.stringid, '') stringid, ".
	$sSQL = " SELECT t.productid, t.date, t.title, t.publisher, t.genre, t.triallink, IF (t.title = games.title, '+', '') alreadyadded, IF (t.title = games.title, games.stringid, '') stringid, ".
			" IF (t.webgamelink IS NOT NULL, 'Web', '') webgame ".
			" FROM trymediafeed t".
			" LEFT JOIN games ON t.title = games.title".
			" WHERE t.productid NOT IN ('$idsline')".
			" ORDER BY t.date DESC";

	$games = $conn->getAll($sSQL);


	//
	$outfeed = $conn->getAll('SELECT g.gameid, g.title, g.developer, g.trymedia_product_id, g.hidden FROM games g LEFT JOIN trymediafeed t ON t.productid = g.trymedia_product_id  WHERE g.trymedia_product_id IS NOT NULL AND t.productid IS NULL ORDER BY hidden, g.gameid');

	//
	$already_added = $conn->getAll("SELECT g.gameid, g.title, g.developer, g.gameprice, t.usdprice, t.filesize, g.fsize, g.fsize_points FROM games g JOIN trymediafeed t ON t.productid = g.trymedia_product_id WHERE g.hidden = 'N' AND g.trymedia_product_id IS NOT NULL ORDER BY g.gameid");
	
	// needs id correction
	$id_correction = $conn->getAll("SELECT g.gameid, g.title, g.developer, t.productid, g.trymedia_product_id, g.developer, t.publisher, g.hidden FROM games g LEFT JOIN trymediafeed t ON t.title = g.title WHERE g.trymedia_product_id IS NOT NULL AND t.productid IS NOT NULL AND g.trymedia_product_id <> t.productid ORDER BY hidden");

?>

<script>
	function showinfo(gid) {
		window.open('./trymediainfo.php?id='+gid, 'newWin'+gid, 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=800, Height=600');
		return false;
	}
</script>


<h1>Update Trymedia feed</h1>
<table width=100% cellspacing=1 cellpadding=4 class=frm4>
<tr><td>
	Update Trymedia database from feed file: <a href="javascript:postwith('./',{report:'trymedia',action:'uploadfeed'});">Update</a>
</table>


<h1>Nonsynchronized ID's (sync by game title)</h1>
<table>
	<tr><th>ID</th><th>Trymedia ID from games table</th><th>Trymedia ID</th><th>Title</th><th>Developer (games)</th><th>Developer (trymedia)</th></tr>
	<? foreach ($id_correction as $g) { ?>
		<tr><td><?=$g['gameid']?></td><td><?=$g['trymedia_product_id']?></td><td><?=$g['productid']?></td><td><?=$g['title']?></td><td><?=$g['developer']?></td><td><?=$g['publisher']?></td></tr>
	<? } ?>
</table>


<h1>Out of Trymedia feed (<?=count($outfeed)?> titles)</h1>
<table>
	<tr><th>ID</th><th>Trymedia ID from games table</th><th>Title</th><th>Publisher</th></tr>
	<? foreach ($outfeed as $g) {
		if ($g['hidden'] == 'N' ) { ?>
		<tr><td><?=$g['gameid']?></td><td><?=$g['trymedia_product_id']?></td><td><?=$g['title']?></td><td><?=$g['developer']?></td></tr>
		<? } else { ?>
		<tr class="inactive"><td><?=$g['gameid']?></td><td><?=$g['trymedia_product_id']?></td><td><?=$g['title']?></td><td><?=$g['developer']?></td></tr>
		<? } ?>
	<? } ?>
</table>

<form action="./?report=trymedia" method="POST">
<input type="hidden" name="addgames" value="true">

	<h1>Games from feed:</h1>
	<table cellspacing="1" cellpadding="4" class="frm4">
		<tr>
			<th>N</th>
			<th>Add</th>
			<th>ID</th>
			<th>Not Trymedia</th>
			<th>Web</th>
			<th>Show</th>
			<th>DL</th>
			<th>Publisher</th>
			<th>Title</th>
			<th>Genre</th>
			<th>Date</th>
		</tr>
		
		<? $counter=0; foreach ($games as $game) { $counter++?>
		<tr>
		<td><?=$counter?></td>
		<td><input type="checkbox" name="ids[]" value="<?=$game['productid']?>"></td>
		<td><?=$game['productid']?></td>
		<td><? if ($game['alreadyadded'] == '') { ?>-<? } else { ?><a href="/games/<?=$game['stringid']?>">Yes</a><? } ?></td>
		<td><?=$game['webgame']?></td>
		<td><a href="" onClick="return showinfo('<?=$game['productid']?>');">Show</a></td>
		<td><?if(empty($game['triallink'])){?>N/A<?}?></td>
		<td><?=$game['publisher']?></td>
		<td><a href="http://www.trygames.com/game/vid=<?=$game['productid']?>/aff=t_18oi" target="_blank"><?=$game['title']?></a></td>
		<td><?=$game['genre']?></td>
		<td><?=$game['date']?></td>
		<?}?>
	
	</table>
	<input type="submit" name="addtrymediagames" value="Add Selected Games">
</form>  


<h1>Already added (<?=count($already_added)?> titles)</h1>
<table>
	<tr><th>ID</th><th>Title</th><th>Publisher</th><th>Price(G4W)</th><th>Price(Trymedia)</th><th>Size (games)</th><th>Size (trymedia)</th></tr>
	<? foreach ($already_added as $g) { ?>
		<tr><td><?=$g['gameid']?></td><td><?=$g['title']?></td><td><?=$g['developer']?></td><td><?=$g['gameprice']?></td><td><?=$g['usdprice']?></td><td><?=$g['fsize']?> <?=$g['fsize_points']?></td><td><?=$g['filesize']?></td></tr>
	<? } ?>
</table>
