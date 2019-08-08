<?
//SELECT g.title FROM games AS g INNER JOIN trymediafeed AS t ON t.title = g.title
//WHERE download1 LIKE "%arcade.reflexive.com%" OR download1 LIKE "%download.gamecentersolution.com%" OR download2 LIKE "%arcade.reflexive.com%" OR download2 LIKE "%download.gamecentersolution.com%" 

//SELECT g.gameid, g.title, t.productid FROM games AS g INNER JOIN trymediafeed AS t ON t.title = g.title
//WHERE download1 LIKE "%arcade.reflexive.com%" OR download1 LIKE "%download.gamecentersolution.com%" OR download2 LIKE "%arcade.reflexive.com%" OR download2 LIKE "%download.gamecentersolution.com%" 
//ORDER BY g.gameid

	global $conn;
    $counter=0;

	//include_once('routines.php');


    //$base = new MySQLconnector;
	//if (isset($addmultiple))
	if (isset($action) && $action == 'addmult')
	{

		?><br>Added:<br><?
		//echo($SimilarGames);
		$lines = explode("\r\n", $SimilarGames); //print_r($lines);

		foreach($lines as $t)
		{
			$sid = GenerateStringId($t);

			?><?=$t?> - <?=$sid?><br><?


			$gameid = $conn->getOne("SELECT gameid FROM games ORDER BY gameid DESC LIMIT 0,1");
			$gameid++;

			$req = "".
			"INSERT INTO games ".
			"SET games.gameid = '{$gameid}', ".
			"games.developer = '', ".
			"games.relatedmanager = '{$logged_games}', ".
			"games.vendorid = '', ".
			"games.title = '{$t}', ".
			"games.stringid = '{$sid}', ".
			"games.version = '1.0', ".
			"games.`category01` = 'none', ".
			"games.`category02` = 'none', ".
			"games.platform = '{$platform}', ".
			"games.shortdesc = '', ".
			"games.longdesc = '', ".
			"games.page_title = '', ".
			"games.rating = 0, ".
			"games.`win311` = 'N', ".
			"games.`win9x` = 'Y', ".
			"games.winnt = 'N', ".
			"games.`win2k` = 'Y', ".
			"games.winxp = 'Y', ".
			"games.other = 'N', ".
			"games.requires = '[pc]P-100', ".
			"games.gameprice = '0', ".
			"games.fsize = '0', ".
			"games.homepage = 'http://', ".
			"games.`download1` = 'http://', ".
			"games.`download2` = 'http://', ".
			"games.screenshot = '', ".
			"games.orderpage = 'http://', ".
			"games.logo = '', ".
			"games.featscale = '0', ".
			"games.hidden = 'Y' ".
			"";

        //print($req);
		$rate = "".
			"INSERT INTO rates ". 
			"SET gameid = {$gameid}, ".
			"playability='0', ".
			"graphics='0', ".
			"sounds='0', ".
			"quality='0', ".
			"idea='0', ".
			"awards='0', ".
			"time='0', ".
			"action='0', ".
			"age1='0', ".
			"age2='0', ".
			"age3='0', ".
			"age4='0', ".
			"age5='0', ".
			"age6='0', ".
			"cpu='1', ".
			"video='1', ".
			"netmode1='Y', ".
			"netmode2='N', ".
			"netmode3='N', ".
			"netmode4='N' ".
			"";

			$conn->query($req);
			$conn->query($rate);
		}


	}


    if (isset($action))
    {
        if ($action=='off')
            $conn->query('UPDATE games SET hidden="Y" WHERE gameid = ? LIMIT 1', array($gameid));
        elseif ($action=='on')
            $conn->query('UPDATE games SET hidden="N" WHERE gameid = ? LIMIT 1', array($gameid));

        elseif ($action=='addnewgame')
		{

			// get unused gameid
		    //$b = new MySQLconnector;
	    	$gameid = $conn->getOne("SELECT gameid FROM games ORDER BY gameid DESC LIMIT 0,1");
			//$dt = $b->BaseFetch();
		    //$b->BaseFinish();
	    	//$b->BaseDisconnect();
			//$gameid = $dt['gameid'] + 1;
			$gameid++;
    
			$hpg = "http://";
			$sst = "http://";
			$dlo = "http://";
			$developer = '';
			$title = 'New title';
			$stringid = 'new-game';
			$longdesc = '';
			$shortdesc = '';
			$fsize = '0';
			$requires = '';
			$gameprice = '0';
			$shortpngname = '';

			if (!empty($reflexiveid) && is_numeric($reflexiveid))
			{

			    require_once("./ra_rasreader.php");
			    $MyCID = 3990;
    			$g = RA_GetFeed("http://arcade.reflexive.com/feed/GameData.ras", $MyCID, "./cachedfeed", $reflexiveid);
				$g['DownloadSizeKB'] = round($g['DownloadSizeMB']*1024);
				//echo '<pre>'; print_r($g); echo '</pre>'; die;
				/*
				Array
				(
				    [GameTitle] => Global Defense Network
				    [Developer] => Evertt.com
				    [Price] => 14.95
				    [ShortDescription] => A fast paced shooter combined with a rhythm action game.
				    [MediumDescription] => A combination of a fast paced shooter and rhythm action game superbly synchronized in a Sci-Fi plot.
				    [LongDescription] => The GDN is a combination of a fast paced shooter and a rhythm action game. Charged with defending against aggressive extra terrestrial activity, the GDN has begun to seek out gifted individuals to assist in its efforts. Test your skills, and see if you are good enough to be a GDN Agent.
				    [DownloadSizeMB] => 7.65
				    [ScreenShotCount] => 4
				    [AIDForImages] => 213
				    [SystemRequirements] => OS: Windows 98, Windows 2000, Windows XP Memory: 128 MB DirectX: 8.0 CPU: P300
				    [GamePrimaryCategory] => Shooter
				    [GameCategories] => Action/Shooter
				    [DownloadSizeKB] => 7834
				)
				*/
				$developer = $g['Developer'];
				$title = $g['GameTitle'];
				//$stringid = str_replace(" ","-",strtolower($title));
				//$stringid = str_replace("'",'', $stringid);
				//$stringid = str_replace(":",'', $stringid);
				//$stringid = str_replace("&",'and', $stringid);
				$stringid = GenerateStringId($title);

				$shortdesc = addslashes($g['ShortDescription']);
				$longdesc = addslashes('<p>'.$g['ShortDescription'].'</p><p>'.$g['MediumDescription'].'</p><p>'.$g['LongDescription'].'</p><br>'.$g['GamePrimaryCategory'].'<br>'.$g['GameCategories']);
				$fsize = $g['DownloadSizeKB'];
				$requires = addslashes($g['SystemRequirements']);
				$gameprice = '-1';
				$img_aid = $g['AIDForImages'];

				if (empty($img_aid)) die("Empty AID, try again!!!");

				$hpg = "http://arcade.reflexive.com/gameinfo.aspx?CID=3990&AID=$reflexiveid";
				$sst = "http://www.reflexive.net/arcadeimages/ss/{$img_aid}Thumb1.jpg";
				$dlo = "http://arcade.reflexive.com/downloadgame.aspx?CID=3990&AID=$reflexiveid";
				$box200 = "http://www.reflexive.net/arcadeimages/box/{$img_aid}_200x200.jpg";

				//die($stringid);
				//die;

				$im = imagecreatefromjpeg($box200);
				if($im)
				{
					$root = '../logos';
					$shortpngname = $stringid.'_'.$img_aid.'.png';
					$filename2 = $root.'/'.$shortpngname;
					$im2 = imagecreatetruecolor(100,100);
					imagecopyresampled($im2, $im, 0,0, 0,0, 100,100, 200,200);
					imagepng($im2, $filename2);
				}
				//die;
				$stringid = addslashes($stringid);
				$shortpngname = addslashes($shortpngname);
				$title = addslashes($g['GameTitle']);

			}


		$req = <<<REQ01
INSERT INTO games
SET    games.gameid = '{$gameid}',
	   games.developer = '{$developer}',
       games.relatedmanager = '{$logged_games}',
       games.vendorid = '',
       games.title = '{$title}',
       games.stringid = '{$stringid}',
       games.version = '1.0',
       games.`category01` = 'none',
       games.`category02` = 'none',
       games.shortdesc = '{$shortdesc}',
       games.longdesc = '{$longdesc}',
       games.page_title = '',
       games.rating = 0,
       games.`win311` = 'N',
       games.`win9x` = 'Y',
       games.winnt = 'N',
       games.`win2k` = 'Y',
       games.winxp = 'Y',
       games.other = 'N',
       games.requires = '{$requires}',
       games.gameprice = '{$gameprice}',
       games.fsize = '{$fsize}',
       games.homepage = '{$hpg}',
       games.`download1` = '{$dlo}',
       games.`download2` = 'http://',
       games.screenshot = '{$sst}',
       games.orderpage = 'http://',
       games.logo = '{$shortpngname}',
       games.featscale = '0',
       games.hidden = 'Y'
REQ01;
        //print($req);
		$rate = <<<RATES001
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

			$conn->query($req);
			$conn->query($rate);

		}
    }


	if (!empty($filter))
	{

		$SQLfilter = '';
		$order_by_filter = 'ORDER BY hidden, gameid';
        $limit_filter = '';

		switch ($filter)
		{
			case 'all':
                $SQLfilter = '';
                break;
			case 'last_100':
				$SQLfilter = '';
				$order_by_filter = 'ORDER BY gameid DESC';
				$limit_filter = 'LIMIT 100';
				break;
			case 'hidden':
				$SQLfilter = 'WHERE hidden="Y"';
				break;
			case 'win':
				$SQLfilter = 'WHERE platform="win"';
				break;
			case 'dos':
				$SQLfilter = 'WHERE platform="dos"';
				break;
			case 'genesis':
				$SQLfilter = 'WHERE platform="genesis"';
				break;
            case 'gamegear':
                $SQLfilter = 'WHERE platform="gamegear"';
                break;
            case 'segacd':
                $SQLfilter = 'WHERE platform="segacd"';
                break;
			case 'arcade':
                $SQLfilter = 'WHERE platform="arcade"';
                break;
			case 'saturn':
				$SQLfilter = 'WHERE platform="saturn"';
				break;
			case 'games4win':
				$SQLfilter = 'WHERE download1 LIKE "%games4win.com%" OR download2 like "%games4win.com%" ';
				break;
			case 'regnow':
				$SQLfilter = 'WHERE vendorid <> "" ';
				break;
			case 'trymedia':
				$SQLfilter = 'WHERE trymedia_product_id <> "" ';
				break;
			case 'shareit':
				$SQLfilter = 'WHERE orderpage LIKE "%secure.element5.com%" ';
				break;
			case 'plimus':
				$SQLfilter = 'WHERE orderpage LIKE "%plimus.com%" ';
				break;
			case 'alawar':
				$SQLfilter = 'WHERE orderpage LIKE "%item=1660%" ';
				break;
			case 'reflexive':
				$SQLfilter = 'WHERE download1 LIKE "%arcade.reflexive.com%" OR download1 LIKE "%download.gamecentersolution.com%" OR download2 LIKE "%arcade.reflexive.com%" OR download2 LIKE "%download.gamecentersolution.com%" ';
				break;

            //
			case 'price0':
                $SQLfilter = 'WHERE gameprice <= 0';
				break;
			case 'price10':
				$SQLfilter = 'WHERE gameprice > 0 AND gameprice < 999';
				break;
			case 'price20':
				$SQLfilter = 'WHERE gameprice > 999 AND gameprice < 1999';
				break;
			case 'price30':
				$SQLfilter = 'WHERE gameprice > 1999';
				break;
						
		}
		$SQLfilter .= ' ';


		if(substr($filter,0,6)=='letter')
		{
			$letter = substr($filter,7,1);
			$SQLfilter = "WHERE SUBSTRING(title,1,1) = \"{$letter}\"";
			
		}

		if ($logged_role == 'admin')
		{
			$sSQL = "SELECT gameid,forumtopic, stringid, category01, category02, shortdesc_ru, relatedmanager, title, version, orderpage, vendorid, hidden, longdesc, gameprice, IF(scr01='' AND scr02='' AND scr03='' AND scr01src='' AND scr02src='' AND scr03src='','0','1') scr FROM games" . " " . $SQLfilter . $order_by_filter . " " .  $limit_filter;

	    	$games = $conn->getAll($sSQL);
			//print_r($games);
		}

		if ($logged_role == 'manager' && $logged_games > 0)
		{
	    	$games = $conn->getAll("SELECT gameid,forumtopic,stringid, category01, category02, shortdesc_ru, relatedmanager,title,version,orderpage,vendorid,hidden,longdesc, gameprice FROM games WHERE relatedmanager='$logged_games' ORDER BY gameid");
		}


		for($i=0;$i<count($games);$i++) {

			$gid = $games[$i]['gameid'];

			// russian longreports counter
			$russian_longreports_count = $conn->getOne("SELECT COUNT(*) FROM longreports WHERE gameid = ? AND lang='russian'", array($gid) );
			if(empty($russian_longreports_count)) {
				$games[$i]['no_russian_long'] = true;
			}


			/*
			$title = addslashes($games[$i]['title']); //echo '!!'.$title.'!!';

			$sSQL = "SELECT count(*) c FROM padfiles WHERE padfiletitle='$title'";
			$isname = $conn->getOne($sSQL);
			if ($isname > 0) {
				$sSQL = "SELECT padfileversion,padfileid FROM padfiles WHERE padfiletitle='$title' ORDER BY 1";
				$games[$i]['padallversions'] = $conn->getAll($sSQL); //print_r($games[$i]['padallversions']);
			

				$sSQL = "SELECT max(padfileversion) FROM padfiles WHERE padfiletitle='$title'";
				$ver = $conn->getOne($sSQL);
				if ($ver > $games[$i]['version']) 
					$games[$i]['padversion'] = $ver;
			}
			*/


		}
	}


	$platforms = GetPlatformType(); 

?>

<script>
	function eg(gid)
	{
		window.open('./editgame.php?gameid='+gid, 'newWin'+gid, 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=750, Height=600');
		return false;
	}
</script>

<script>
	function egdiff(gid,diffn)
	{
		window.open('./editgame.php?gameid='+gid+'&diff='+diffn, 'newWin'+gid, 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=750, Height=600');
		return false;
	}
</script>


<!--<table width=100% cellspacing=7 cellpadding=10 class=frm4 bgcolor=white>
<tr><td valign=top class=frm5>
-->

<h1>Actions:</h1>

<form action="./?report=g<?if(!empty($filter)){?>&filter=<?=$filter?><?}?>" method="POST" name="frm">
<input type="hidden" name="action" value="addmult">
    <table cellspacing="1" cellpadding="4" class="frm4" width="100%">
        <tr>
            <td>
				Add new titles:
				<br>
				<textarea name="SimilarGames" id="pstinp" class="inp" cols="40" rows="30" style="wrap:virtual;width:400px;height:150px"><?//=$game['similargames']?></textarea>
				<br>
				<?
				//	<input type="radio" name="a[]" id="g01" checked><label for="g01" style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;PC-Windows&nbsp;</label>
				//	<input type="radio" name="a[]" id="g02"><label for="g02" style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;PC-DOS&nbsp;</label>
				//	<input type="radio" name="a[]" id="g03"><label for="g03" style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;Genesis&nbsp;</label>
				//	<input type="radio" name="a[]" id="g04"><label for="g04" style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;XBox&nbsp;</label>
				?>
	For platform:
	<select name="platform">
	<?
		$counter=0;
		foreach ($platforms as $c)
		{
			$counter++;
			?>
            <option value="<?=$c?>"><?=$counter?>-<?=$c?></option>
			<?
		}
	?>
	</select><?=$game['platform']?>
	            <input type="hidden" name="addmultiple" value="true">
				<br><br>                    
				<input name="submit" type="submit" class="button" value="Add">
</form><!-- platform -->

        <tr>
            <td>
				<a href="./?report=g&action=addnewgame<?if(!empty($filter)){?>&filter=<?=$filter?><?}?>">ADD NEW TITLE</a> (see at the end of the list)
            </td>
        </tr>
        <tr>
            <td>
                <form action="./?report=g&action=addnewgame<?if(!empty($filter)){?>&filter=<?=$filter?><?}?>" method="POST" name="frm">
                ADD NEW <b>REFLEXIVE</b> GAME, &nbsp;&nbsp;&nbsp; ID:<input type="text" name="reflexiveid" size="5">&nbsp;&nbsp;<input name="submit" type="submit" class="button" value="Add">
                </form>
            </td>
        </tr>
    </table>


<br>


<h1>Categories:</h1>
<table cellspacing="1" cellpadding="4" class="frm4" width="100%">
	<tr>
		<td>
			<a href="./?report=g&filter=all"><?if($filter=='all'){?><b>All games</b><?}else{?>All games<?}?></a>&nbsp;|&nbsp;
            <a href="./?report=g&filter=last_100"><?if($filter=='last_100'){?><b>Last 100</b><?}else{?>Last 100<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=hidden"><?if($filter=='hidden'){?><b>Hidden</b><?}else{?>Hidden<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=win"><?if($filter=='win'){?><b>PC-Windows</b><?}else{?>PC-Windows<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=dos"><?if($filter=='dos'){?><b>PC-DOS</b><?}else{?>PC-DOS<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=genesis"><?if($filter=='genesis'){?><b>Genesis</b><?}else{?>Genesis<?}?></a>&nbsp;|&nbsp;
            <a href="./?report=g&filter=gamegear"><?if($filter=='gamegear'){?><b>GameGear</b><?}else{?>GameGear<?}?></a>&nbsp;|&nbsp;
            <a href="./?report=g&filter=segacd"><?if($filter=='segacd'){?><b>SegaCD</b><?}else{?>SegaCD<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=arcade"><?if($filter=='arcade'){?><b>Arcade</b><?}else{?>Arcade<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=saturn"><?if($filter=='saturn'){?><b>Saturn</b><?}else{?>Saturn<?}?></a>&nbsp;|&nbsp;
			<br />By game vendor:
			<a href="./?report=g&filter=games4win"><?if($filter=='games4win'){?><b>Games4Win</b><?}else{?>Games4Win<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=regnow"><?if($filter=='regnow'){?><b>RegNow</b><?}else{?>RegNow<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=trymedia"><?if($filter=='trymedia'){?><b>Trymedia</b><?}else{?>Trymedia<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=shareit"><?if($filter=='shareit'){?><b>ShareIt</b><?}else{?>ShareIt<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=plimus"><?if($filter=='plimus'){?><b>Plimus</b><?}else{?>Plimus<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=alawar"><?if($filter=='Alawar'){?><b>Alawar</b><?}else{?>Alawar<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=reflexive"><?if($filter=='Reflexive'){?><b>Reflexive</b><?}else{?>Reflexive<?}?></a>
			<br />By price:
			<a href="./?report=g&filter=price0"><?if($filter=='price0'){?><b>Free</b><?}else{?>Free<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=price10"><?if($filter=='price10'){?><b>$0-$9.99</b><?}else{?>$0-$9.99<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=price20"><?if($filter=='price20'){?><b>$10-$19.99</b><?}else{?>$10-$19.99<?}?></a>&nbsp;|&nbsp;
			<a href="./?report=g&filter=price30"><?if($filter=='price30'){?><b>&gt;$20</b><?}else{?>&gt;$20<?}?></a>
		</td>
		
		<td>
			<?  $abc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				for($i=0; $i<strlen($abc); $i++) { ?>
			<a href="./?report=g&filter=letter_<?=$abc[$i]?>"><?if(substr($filter,0,6)=='letter' && substr($filter,7,1)==$abc[$i]){?><b><?=$abc[$i]?></b><?}else{?><?=$abc[$i]?><?}?></a><?if($abc[$i] != 'Z'){?> | <?}?>
			<? } ?>
		</td>
	</tr>
</table>

<br>

<br><br>


<? if (!empty($filter) || count($games) ) { ?>

<h1>Games (<?=count($games)?> titles selected):</h1>


<table cellspacing=1 cellpadding=4 class=frm4>
	<tr>
		<td><b>ID</b></td>
		<td><b>Forum</b></td>
		<td><b>State</b></td>
		<td><b>PAD</b></td>
		<td><b>Name</b></td>
		<td><b>Price</b></td>
		<td><b>Page</b></td>
		<td><b>Edit</b></td>
		<td><b>Diff</b></td>
		<td><b>Comment</b></td>
	</tr>
	
<? foreach ($games as $game){  $counter++;  ?>
<tr>
	<td><b>ID:<?=$game['gameid']?></b> <br> [<?=$counter?>-<?=$game['relatedmanager']?>]</td>
	<td><?if(isset($game['forumtopic'])){?>+<?}?></td>
	<td><?if($game['hidden']=='N'){?> Enabled [<a href="./?report=g&action=off&gameid=<?=$game['gameid']?><?if(!empty($filter)){?>&filter=<?=$filter?><?}?>">switch off</a>]  <?}else{?>  <b>Hidden</b> [<a href="./?report=g&action=on&gameid=<?=$game['gameid']?><?if(!empty($filter)){?>&filter=<?=$filter?><?}?>">switch on</a>]  <?}?></td>
	<td><?if($game['padversion']){?><?=$game['padversion']?><?}?></td>

	<td><?if($game['hidden']!='N'){?><font color="#b0b0b0"><?}?><b><?=$game['title']?></b> <?=$game['version']?> [<?=$game['stringid']?>] <?if($game['vendorid']!=''){?> [vendor:<?=$game['vendorid']?>] <?}?><?if($game['scr']=='0'){?><b>[*SCR*]</b><?}?>
		<br><?=substr($game['orderpage'],0,120)?>
		<br><?=$game['category01']?><?if (!empty($game['category02']) && $game['category02']!='none'){?>,<?=$game['category02']?><?}?>

		<?if(empty($game['shortdesc_ru'])){?><font color="red">NoRusShort </font><?}?>
		<?if($game['shortdesc_ru']=='.'){?><font color="red">RusEmpty </font><?}?>
		<?if($game['no_russian_long']){?><font color="red">NoRusLong </font><?}?>


		<?if($game['hidden']!='N'){?></font><?}?>
	</td>

	<td><?=$game['gameprice']?></td>
	
	<td>
		<a href="/games/<?=$game['stringid']?>/" target=_blank>Jump</a>
	</td>
	
	<td>
		<a href="" onClick="return eg(<?=$game['gameid']?>);">Edit</a>
	</td>
	<td>
		<?//print_r($game['padallversions'])?><?for($i=0;$i<count($game['padallversions']);$i++){?><a href="" onClick="return egdiff(<?=$game['gameid']?>,<?=$game['padallversions'][$i]['padfileid']?>);"><?=$game['padallversions'][$i]['padfileversion']?></a> <?}?>
	</td>
	<td>
		<?if($game['longdesc']==''){?>No description<?}?>
	</td>
</tr>
<? }?>
</table>
<? } ?>