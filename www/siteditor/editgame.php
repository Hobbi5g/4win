<?

	include ("login.php");

	$gameid = intval($_GET['gameid']);

	//print_r($_POST);




?><html>
<head>
<title><?=$GameTitle?> - <?=$softname?> v.<?=$version;?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/styles.css" />

</head>



<script language=javascript>
	function operatag(id,tag1,tag2) {
		i=document.getElementById(id);
		i.value=i.value+tag1+tag2;
		return false;
	}

	function editdescription(gid) {
		window.open('./editdescription.php?id='+gid, 'newWin', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=750, Height=600');
		return false;
	}

	function showkeywords(gid) {
		window.open('./showkeywords.php?id='+gid, 'newWin', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=750, Height=600');
		return false;
	}

	function addreview() {
		document.getElementById('page').value = 'newreview';
		document.frm.submit();
		return false;
	}


	function addforumtopic() {
		document.getElementById('page').value = 'newforumtopic';
		document.frm.submit();
		return false;
	}


	function deletereview(gid) {
		if (confirm("DELETE THIS REVIEW?")) {
			document.getElementById('page').value = 'deletereview';
			document.getElementById('descrid').value = gid; 
			document.frm.submit();
		}
		return false;
	}


	function deletescreenshot(gid) {
		if (confirm("DELETE SCREENSHOT "+gid+"?")) {
			document.getElementById('page').value = 'deletescreenshot';
			document.getElementById('descrid').value = gid; 
			document.frm.submit();
		}
		return false;
	}


</script>



<body text="#000000" bgcolor="#f0f0f0" link="#ff0000" vlink="#500000" alink="#ff8080">

	   <h1>!</h1>

<?

	print"1"; flush();

	global $conn;

	require_once 'settings.php';

	require_once 'gameinfo.php';
	require_once 'specialtxt.php';
	require_once 'tracker.php';

	print"2"; flush();

	include_once ('searchlib.php');
	require_once 'class.stemmer.inc.php';
	require_once 'class.html2text.inc.php';
	include_once('padlib.php');
	include_once('routines.php');
	require_once 'tcustomimage.php';

	print"3"; flush();

//  $what = $LongDesc;
//  $what = $typo->correct( $what );
//  $what = $para->correct( $what );

    $page = $_POST['page'];


    //$_GET[insert] => Update!




	if (isset($page) && $page=='newforumtopic')
	{
		echo "!!!";

		$message = "Feel free to comment and discuss $GameTitle here.\nAlso, if you have any useful tips or tricks don't hesitate to share them with the others!\nThanks!";
		$topicid = CreateTopic('admin', $GameTitle, $message);

		$sSQL = "UPDATE games SET forumtopic='$topicid' WHERE gameid='$GameID' ";
		$conn->query($sSQL);
    }


	$root = '../up'; // for screenshot uploading


	if (isset($page) && $page=='newreview') {
		$conn->query("INSERT INTO longreports SET gameid = ?", array($gameid));
	}


	if (isset($page) && $page=='deletereview') {
    	$conn->query("DELETE FROM longreports WHERE longreportid = ? LIMIT 1", array($descrid));

	}

	// delete screenshot
	if (isset($page) && $page=='deletescreenshot') {
		$fn = $conn->getOne("SELECT scr0{$descrid} FROM games WHERE gameid='{$GameID}'");

		$sSQL = "UPDATE games SET scr0{$descrid}src='',scr0{$descrid}='' WHERE gameid='{$GameID}'";
		$conn->query($sSQL);

		unlink($root.'/'.$fn."_{$descrid}.jpg");
		unlink($root.'/'.$fn."_{$descrid}s.jpg");
		unlink($root.'/'.$fn."_{$descrid}t.jpg");
	}



	if (isset($page) && $page=='updategame')
	{

		//upload screenshots
		for($i=1;$i<=3;$i++)
		{

			if(!empty($_POST["scr0$i"]))
			{
				//echo '-'.$_POST["scr0$i"].'-';
				// load g4w transparent logo
				$logo = imagecreatefrompng('../images/g4wtrans.png');
				$logosx = imagesx($logo);
				$logosy = imagesy($logo);

				$watermark = imagecreatefrompng('../images/watermark.png');

				// get screenshot 1
				//$filename = $root.'/'.$StringID."_{$i}.jpg";
				$filename = $root.'/'.$StringID."_{$i}.png";
				$s1 = file_get_contents($_POST["scr0$i"]);
				$res1 = fopen($filename, 'wb');
				fwrite($res1, $s1);
				fclose($res1);

				//$im = imagecreatefromjpeg($filename);
				$im = imagecreatefrompng($filename);
				if($im)
				{
					$sx = (int)imagesx($im);
					$sy = (int)imagesy($im);
					$sx2 = (int)imagesx($im)/2;
					$sy2 = (int)imagesy($im)/2;

					//640x480
					$filename2 = $root.'/'.$StringID."_{$i}s.jpg";
					$im2 = imagecreatetruecolor(640,480);
					imagecopyresampled($im2, $im, 0,0, 0,0, 640,480, $sx,$sy);
					imagecopy($im2, $logo, 640-$logosx,480-$logosy, 0,0, $logosx,$logosy); // add logotype to image
					imagejpeg($im2, $filename2, 90);

					//640x480 to thumb
					$destW = 180;
					$destH = (int)$destW/1.333333;
					$filename3 = $root.'/'.$StringID."_{$i}t.jpg";
					$im3 = imagecreatetruecolor($destW,$destH);
					//imagecopyresampled($im3, $im2, 0,0, 100,100, $destW,$destH, 440,280 );
					imagecopyresampled($im3, $im2, 0,0, 0,0, $destW,$destH, 640,480 );
					imagecopy($im3, $watermark, 0,0, 0,0, $destW,$destH); // add watermark
					imagejpeg($im3, $filename3, 90);

					$fn = addslashes($_POST["scr0$i"]);
					$conn->query("UPDATE games SET scr0{$i}src='{$fn}' WHERE gameid='{$GameID}'");
					$conn->query("UPDATE games SET scr0{$i}='{$StringID}' WHERE gameid='{$GameID}'");
				}
            }

		} // for (upload screenshots)

        echo ("Updating wiki descriptions<br>");
        flush();
 
		$cont = Markdown($LongDesc);

		$descrid = $_POST['descrid'];
		$GameID = $_POST['GameID'];
		$oldstringid = $_POST['oldstringid'];
		$platform = $_POST['platform'];
		$StringID = $_POST['StringID'];
		$GameTitle = $_POST['GameTitle'];
		$also_known_as = $_POST['also_known_as'];
		$release_year = $_POST['release_year'];
		$Developer = $_POST['Developer'];
		$ShortDesc = $_POST['ShortDesc'];
		$shortdesc_ru = $_POST['shortdesc_ru'];
		$PageTitle = $_POST['PageTitle'];
		$LongDesc = $_POST['LongDesc'];
		$catlist01 = $_POST['catlist01'];
		$catlist02 = $_POST['catlist02'];
		$Win9x = $_POST['Win9x'];
		$Win2K = $_POST['Win2K'];
		$WinXP = $_POST['WinXP'];
		$Requires = $_POST['Requires'];
		$Price = $_POST['Price'];
		$Filesize = $_POST['Filesize'];
		$fsize_points = $_POST['fsize_points'];
		$Homepage = $_POST['Homepage'];
		$Order = $_POST['Order'];
		$Screenshot = $_POST['Screenshot'];
		$scr01 = $_POST['scr01'];
		$scr02 = $_POST['scr02'];
		$scr03 = $_POST['scr03'];
		$Logo = $_POST['Logo'];
		$Download1 = $_POST['Download1'];
		$Download2 = $_POST['Download2'];
		$download_type = $_POST['download_type'];
		$regnow_id = $_POST['regnow_id'];
		$FeatScale = $_POST['FeatScale'];
		$SimilarGames = $_POST['SimilarGames'];
		$Playability = $_POST['Playability'];
		$Graphics = $_POST['Graphics'];
		$Sounds = $_POST['Sounds'];
		$Quality = $_POST['Quality'];
		$Orig = $_POST['Orig'];
		$Time = $_POST['Time'];
		$Action = $_POST['Action'];
		$Age1 = $_POST['Age1'];
		$Age2 = $_POST['Age2'];
		$Age3 = $_POST['Age3'];
		$Age4 = $_POST['Age4'];
		$Age5 = $_POST['Age5'];
		$Age6 = $_POST['Age6'];
		$CPU = $_POST['CPU'];
		$Video = $_POST['Video'];
		$F1 = $_POST['F1'];


        //$LongDesc = addslashes($_POST['LongDesc']);
        //$StringID = addslashes($_POST['StringID']);
		//$GameTitle = addslashes($_POST['$GameTitle']);
		//$also_known_as = addslashes($_POST['GameTitle']);
		//$ShortDesc = addslashes($_POST['ShortDesc']);
		//$shortdesc_ru = addslashes($_POST['shortdesc_ru']);
		//$Developer = addslashes($_POST['Developer']);
		//$Requires = addslashes($_POST['Requires']);
		//$SimilarGames = addslashes($_POST['SimilarGames']);
		$cont = addslashes($cont);
			 //echo $LongDesc;
		if(!($ShortDesc{strlen($ShortDesc)-1} == '.' ||
			 $ShortDesc{strlen($ShortDesc)-1} == '!' ||
			 $ShortDesc{strlen($ShortDesc)-1} == '?'))  { $ShortDesc .= '.'; }

		if( !empty($shortdesc_ru) &&
			!($shortdesc_ru{strlen($shortdesc_ru)-1} == '.' ||
			 $shortdesc_ru{strlen($shortdesc_ru)-1} == '!' ||
			 $shortdesc_ru{strlen($shortdesc_ru)-1} == '?'))  { $shortdesc_ru .= '.'; }


		// calculate rating
		$rating = $Playability*3+$Graphics*2+$Sounds+$Quality*3+$Orig*2 - 8;
		if (isset($F1)) $rating+=1;
		if (isset($F2)) $rating+=3;
		if (isset($F3)) $rating+=2;
		if (isset($F4)) $rating+=2;
		$rating = intval($rating / 5);

		if ($rating > 10) $rating = 10;
			$award = 0;
		// Wow! award
		if ($Orig*2+$Playability+$Quality >= 18)
			$award = 2;
		// GREAT! award
		if (($rating >= 9) && ($Playability>=5) && ($Graphics>=4) && ($Sounds >=3) && ($Quality>=4) && ($Orig>=3))
			$award = 1;


	   if (isset($_POST['Win3x'])) $Win3x = 'Y'; else $Win3x = 'N';
	   if (isset($_POST['Win9x'])) $Win9x = 'Y'; else $Win9x = 'N';
	   if (isset($_POST['WinNT'])) $WinNT = 'Y'; else $WinNT = 'N';
	   if (isset($_POST['Win2K'])) $Win2K = 'Y'; else $Win2K = 'N';
	   if (isset($_POST['WinXP'])) $WinXP = 'Y'; else $WinXP = 'N';
	   if (isset($_POST['Other'])) $Other = 'Y'; else $Other = 'N';

	   if (isset($_POST['F1'])) $F1 = 'Y'; else $F1 = 'N'; // netmode: Single game
	   if (isset($_POST['F2'])) $F2 = 'Y'; else $F2 = 'N'; // Multiplaying on single comp
	   if (isset($_POST['F3'])) $F3 = 'Y'; else $F3 = 'N'; // Multiplaying via LAN
	   if (isset($_POST['F4'])) $F4 = 'Y'; else $F4 = 'N'; // Multiplaying via Inet

	   if ($_POST['FeatScale']=='') $FeatScale = 0;
	   // games.featured_weight,
	   // games.counter = '{}',
	   // games.regdate = '{}',
	   // games.hidden = '{}',


        $conn->query("UPDATE games SET developer = ?, vendorid = ?, platform = ?, title = ?, also_known_as = ?, release_year = ?, stringid = ?, category01 = ?, category02 = ?, download_type  = ?, shortdesc = ?, shortdesc_ru = ?,
                    longdesc = ?, longdesc_wiki = ?, page_title = ?, rating = ?, win311 = ?, win9x = ?, winnt = ?, win2k = ?, winxp = ?, other = ?,
                    requires = ?, gameprice = ?, fsize = ?, fsize_points = ?, homepage = ?, download1 = ?, download2 = ?, screenshot = ?, orderpage = ?, logo = ?,
	                featscale = ?, similargames = ?
	                WHERE  gameid = ?", array(
                $Developer, $regnow_id, $platform, $GameTitle, $also_known_as, $release_year, $StringID, $catlist01, $catlist02, $download_type, $ShortDesc, $shortdesc_ru,
                $cont, $LongDesc, $PageTitle, $rating, $Win3x, $Win9x, $WinNT, $Win2K, $WinXP, $Other,
                $Requires, $Price, $Filesize, $fsize_points, $Homepage, $Download1, $Download2, $Screenshot, $Order, $Logo,
                $FeatScale, $SimilarGames,
                $GameID ));

        $conn->query("UPDATE rates SET playability=?, graphics=?, sounds=?, quality=?, idea=?, awards=?, time=?, action=?, age1=?, age2=?, age3=?, age4=?, age5=?, age6=?, cpu=?, video=?, netmode1=?, netmode2=?, netmode3=?, netmode4=?
                WHERE gameid=?", array(
                $Playability, $Graphics, $Sounds, $Quality, $Orig, $award, $Time, $Action, $Age1, $Age2, $Age3, $Age4, $Age5, $Age6, $CPU, $Video, $F1, $F2, $F3, $F4, $GameID
            ));

        echo('Updating database<br>');
        flush();

        //update search info
        //AddSearchInfo($GameID);
        //echo('Updating searchinfo<br>');
	    //flush();

		// ????????? ??????? ? ??????? ????
		if ($oldstringid != $StringID)
		{
			if (is_file("../img/{$oldstringid}.jpg")) {
				unlink("../img/{$oldstringid}.jpg");
			}

			if (is_file("../img/{$oldstringid}-rating.png")) {
				unlink("../img/{$oldstringid}-rating.png");
			}
		}

        echo('Updating logos<br>');
	    flush();

		//if (!empty($Logo))
		//{
			$tci = new TCustomImage;
		//	$tci->SaveLogoImage($StringID);
			$tci->SaveRatingImage($StringID);
		//}


		echo('<b>UPDATED</b>');
    	flush();

		tracker_add("$GameTitle($GameID) updated", $slogin);

		//if (isset($oldsubmit) || isset($proceedpad))
		//	die("NOW PROCEED FROM !!GAMEBASE!!");

	}

	$cats01 = GetGameCategories();
	$cats02 = GetGameCategories();
	$cats03 = GetDownloadType();
	$platforms = GetPlatformType();

	if (!isset($oldsubmit) && !isset($proceedpad))
	{
		$game = GetGameData($gameid);

	}

	//if (isset($oldsubmit))
	//{
	//	$game = GetGameDataOldSubmit($oldsubmit);
	//}

	//if (isset($proceedpad))
	//{
	//	$game = GetPADFullInfo($proceedpad);
	//}
	///echo '<pre>'; print_r($game); echo '</pre>';

	// game news
	$gamenews = $conn->getAll("SELECT * FROM news WHERE gameid = ? ORDER BY newsdate", array($gameid));

?>

 <form action="" method="POST" name="frm">
	<input type="hidden" id="page" name="page" value="updategame">
	<input type="hidden" id="descrid" name="descrid" value="">
	<input type="hidden" name="GameID" value="<?=$gameid?>">
	<input type="hidden" name="oldstringid" value="<?=$game['stringid']?>">
	<?if(isset($oldsubmit) || isset($proceedpad)){?><input type="hidden" name="add_oldsubmit" value=1><?}?>


 <table align="center">

 <tr>
	<td>
	<td>
		<h2 style="font-family:courier;font-size:24px">Modify game <a href="/games/<?=$game['stringid']?>/" target="_blank"><?=$game['title']?></a></h2>
 <tr>
	 <td align="right">MemberID&nbsp;(login&nbsp;name): </td>
	 <td><!--input type="text" name="MemberID" value="$MemID">--> <?=$game['addedby_name']?> [<?=$game['addedby_login']?>]

 <tr>
 <td align="right">Originally on:
 <td>
	<select name="platform">
	<?
		$counter=0;
		foreach ($platforms as $c)
		{
			$counter++;
			?>
			<option value="<?=$c?>"<?if($c==$game['platform']){?> selected<?}?>><?=$counter?>.<?=$c?></option>
			<?
		}
	?>
	</select> Now selected: <?=$game['platform']?>
 </tr>

	 
<?if (!empty($game['trymedia_product_id'])){?>
<script>
	function showinfo(gid)
	{
		window.open('./trymediainfo.php?id='+gid, 'newWin'+gid, 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=800, Height=600');
		return false;
	}
</script>


 <tr>
	 <td align="right">Trymedia Product ID: </td>
	 <td><a href="" onClick="return showinfo('<?=$game['trymedia_product_id']?>');"><?=$game['trymedia_product_id']?></a>

<?}?>


    <tr>
        <td align="right">StringID:</td>
        <td><input type="text" name="StringID" value="<?=$game['stringid']?>" size="90" class="inp"></td>
     </tr>

    <tr>
        <td align="right">Game Title:</td>
        <td><input type="text" name="GameTitle" value="<?=$game['title']?>" size="90" class="inp"></td>
    </tr>

     <tr>
         <td align="right">Also known as:</td>
         <td><input type="text" name="also_known_as" value="<?=$game['also_known_as']?>" size="90" class="inp"></td>
     </tr>

     <tr>
         <td align="right">Release Year:</td>
         <td><input type="text" name="release_year" value="<?=$game['release_year']?>" size="10" class="inp"></td>
     </tr>

     <tr>
        <td align="right">Developer:</td>
        <td><input type="text" name="Developer" value="<?=$game['developer']?>" size="90" class="inp"></td>
    </tr>

    <tr>
        <td align="right">Short description (en):</td>
	    <td>
            <nobr><input type="text" name="ShortDesc" value="<?=$game['shortdesc']?>" size="90" class="inp">&nbsp;<b>.</b></nobr>&nbsp;
	        <!--td><textarea name="ShortDesc" cols="50" rows="2" class=input1><?=$game['shortdesc']?></textarea-->
        </td>
    </tr>

    <tr>
        <td align="right">Short description (ru):</td>
	    <td>
            <nobr><input type="text" name="shortdesc_ru" value="<?=$game['shortdesc_ru']?>" size="90" class="inp">&nbsp;<b>.</b></nobr>
        </td>
    </tr>


 <tr valign=top>
	<td align="right">Page Title:
	<td><input type="text" id="title_id" name="PageTitle" value="<?=$game['page_title']?>" size="90" class="inp"><br>

<?
	$tags = array();
	$tags['title'] = array('[title]','');
	$tags['version'] = array('[version]','');
	$tags['developer'] = array('[developer]','');

	foreach($tags as $n=>$ts) { ?>
	<input type=button class=butt value='<?=$n?>' onclick="operatag('title_id','<?=$ts[0]?>','<?=$ts[1]?>');">&nbsp;
	<? } ?>


 <tr>
	 <td align="right" valign=top><b>Full description:</b>
<!--	 <td><textarea name="LongDesc" cols="50" rows="10" class=input1><?=$game['longdesc']?></textarea>

 <tr>
	<td colspan=2>
	-->
	<td>
	<textarea name="LongDesc" id="pstinp" class="inp" cols="40" rows="30" style="wrap:virtual;width:100%;height:400px"><?if (isset($proceedpad)){?><?=$game['description']?><?}?><?=htmlspecialchars($game['longdesc_wiki']);?><?//=trim($game['reports'][0]);?></textarea>


<?
$tags=array();
$tags['A (a href)']=array('<a href=>','</a>');
$tags['B (bold)']=array('<b>','</b>');
$tags['I (italic)']=array('<i>','</i>');
$tags['U (underline)']=array('<u>','</u>');
$tags['img']=array("<img src= alt=>",'');
$tags['OL (list)']=array("<OL>\\n<li>\\n<li>\\n<li>\\n</OL>",'');
$tags['UL (list)']=array("<UL>\\n<li>\\n<li>\\n<li>\\n</UL>",'');

	foreach($tags as $n=>$ts)
	{
	//<input type=button class=butt value='{$n}' onclick="operatag('pstinp','{$ts[0]}','{$ts[1]}');">&nbsp;
	?>
	<input type="button" class="butt" value="<?=$n?>" onclick="operatag('pstinp','<?=$ts[0]?>','<?=$ts[1]?>');">&nbsp;
	<?
	}
	?>

<tr><td><td>
<b>Formatted:</b>

<?
//<br>
//<input type=checkbox class=check1 name=format1 value=1 id=format1 checked><label for=format1 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;????? (???????? ?????, ???????, ?????? (?) ? (r),...)&nbsp;</label><br>
//<input type=checkbox class=check1 name=format2 value=1 id=format2><label for=format2 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;???????? &laquo;&lt;&raquo; ?? &laquo;&amp;lt;&raquo;&nbsp;</label><br>
//<input type=checkbox class=check1 name=format3 value=1 id=format3><label for=format3 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;???????????? ??????????? (???????? &laquo;&amp;&raquo; ?? &laquo;&amp;amp;&raquo;)&nbsp;</label><br>
//<input type=checkbox class=check1 name=format4 value=1 id=format4 checked><label for=format4 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;???????? http://&hellip; ?? ?????? (???? &lt;a&gt; ??? ????, ?? ?? ????????)&nbsp;</label><br>
?>

<?=htmlspecialchars($game['reports'][0]);?>
<!--tr><td colspan=2 valign=top>

<br><br>
<input name=posted type=hidden value=ok>
<input type=submit class=butt value=" ????????? " style="width:200px">
	-->
	<hr>
<?

	//echo "!!".count($game['reports']);
	if($conn->getOne("SELECT COUNT(*) c FROM longreports WHERE (gameid='{$gameid}')") > 0)
	{
		$sSQL = "SELECT longreportid, report, lang, reporttype FROM longreports WHERE (gameid='{$gameid}')";
		$reps = $conn->getAll($sSQL);
		foreach($reps as $rep)
		{
			//$game['reports']["$counter"] = $rep['report'];
			//$counter++;
			?>Review #<?=$rep['longreportid']?> <a href="" onClick="return editdescription(<?=$rep['longreportid']?>);">EDIT</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="" onClick="return deletereview(<?=$rep['longreportid']?>);">DELETE</a><?if(strlen($rep['report'])==0){?> <font color=red>EMPTY REVIEW</font><?}?> (<?=$rep['lang']?>, <?=$rep['reporttype']?>)<br><?
		}
	} 
	else
	{     
		?><b>NO REVIEWS FOUND<b><?
	}
?>
	<a href="" onClick="return addreview();">Add new review</a>
	<hr>
 


<?if(!isset($game['forumtopic'])) { ?>No forum topic found. <a href="" onClick="return addforumtopic();">Create new topic</a><?} else { ?>
Forum topic: <a href="/forum/viewtopic.php?id=<?=$game['forumtopic']?>" target=_blank><?=$game['forumtopic']?></a> 
<?}?>

<hr>

 <tr>
 <td align="right">Category01:
 <td>
	<select name="catlist01">
	<?
		$counter=0;
		foreach ($cats01 as $c)
		{
			$counter++;
			?>
			<option value="<?=$c?>"<?if($c==$game['category01']){?> selected<?}?>><?=$counter?>-<?=$c?></option>
			<?
		}
	?>
	</select><?=$game['category01']?>
 </tr>

 <tr>
 <td align="right">Category02:
 <td>
	<select name="catlist02">
	<?
		$counter=0;
		foreach ($cats02 as $c)
		{
			$counter++;
			?>
			<option value="<?=$c?>"<?if($c==$game['category02']){?> selected<?}?>><?=$counter?>-<?=$c?></option>
			<?
		}
	?>
	</select><?=$game['category02']?>
 </tr>
 
 <!--tr>
 <td align="right">Similar games: </td>
 <td><select name='simgames' size=12 multiple>@simgamelist </select></td>
 </tr-->
 <tr>
	 <td align="right">Platforms:
	 <td><input type=checkbox class=check1 id=os1 name="Win3x"<?if($game['win311']=="Y"){?> checked<?}?>><label for=os1 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;Win3.x&nbsp;</label>&nbsp;&nbsp;
		 <input type=checkbox class=check1 id=os2 name="Win9x"<?if($game['win9x']=="Y"){?> checked<?}?>><label for=os2 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;Win9x&nbsp;</label>&nbsp;&nbsp;
		 <input type=checkbox class=check1 id=os3 name="WinNT"<?if($game['winnt']=="Y"){?> checked<?}?>><label for=os3 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;WinNT&nbsp;</label>&nbsp;&nbsp;
		 <input type=checkbox class=check1 id=os4 name="Win2K"<?if($game['win2k']=="Y"){?> checked<?}?>><label for=os4 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;Win2K&nbsp;</label>&nbsp;&nbsp;
		 <input type=checkbox class=check1 id=os5 name="WinXP"<?if($game['winxp']=="Y"){?> checked<?}?>><label for=os5 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;WinXP&nbsp;</label>&nbsp;&nbsp;
		 <input type=checkbox class=check1 id=os6 name="Other"<?if($game['other']=="Y"){?> checked<?}?>><label for=os6 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">&nbsp;Other&nbsp;</label>

 <tr>
	 <td align="right">Requirements:
	 <td><input type="text" name="Requires" id=req_id value="<?=$game['requires']?>" size="50" style="width:100%;" class="inp">
	 <br>
<?
$tags=array();
$tags['PC']=array('[pc]','');
$tags['Video']=array('[video]','');
$tags['DirectX']=array('[directx]','');

	foreach($tags as $n=>$ts)
	{
	?>
	<input type=button class=butt value='<?=$n?>' onclick="operatag('req_id','<?=$ts[0]?>','<?=$ts[1]?>');">&nbsp;
	<?
	}
	?>


 <tr>
 <td align="right">Price:
 <td><input type="text" name="Price" value="<?=$game['gameprice']?>" size="10" class="inp"> in cents!

 <tr>
 <td align="right">Filesize:
 <td><input type="text" name="Filesize" value="<?=$game['fsize']?>" size="10" class="inp">

	<select name="fsize_points" size="1">
		<option value="Kb" <?if ($game['fsize_points']=='Kb'){?>selected<?}?>>Kb</option>
		<option value="Mb" <?if ($game['fsize_points']=='Mb'){?>selected<?}?>>Mb</option>
	</select>

 <?=$game['fsize_points']?>.

 <tr>
 <td align="right">Homepage:
 <td><input type="text" name="Homepage" value="<?=$game['homepage']?>" size="100" class="inp">
 <a href="<?=$game['homepage']?>" target="_blank">Check</a>

 <tr>
 <td align="right" valign=top><b>Order page:</b>
 <td><input type="text" name="Order" value="<?=$game['orderpage']?>" size="100" id=order_id class="inp">
 <a href="<?=$game['orderpage']?>" target="_blank">Check</a><br>

<?
$tags=array();
$tags['&affiliate=19154']=array('&affiliate=19154','');

	foreach($tags as $n=>$ts)
	{
	?>
	<input type=button class=butt value='<?=$n?>' onclick="operatag('order_id','<?=$ts[0]?>','<?=$ts[1]?>');">&nbsp;
	<?
	}
	?><br><a href="http://www.regnow.com/vendorpriv" target=rn_blank>Regnow Vendors</a>

	<tr>
	<td align="right">Screenshot:</td>
	<td><input type="text" name="Screenshot" value="<?=$game['screenshot']?>" size="100" class="inp"> <a href="<?=$game['screenshot']?>" target="_blank">Check</a></td>

	<tr>
	<td align="right">Scr01:</td>
	<td><?if(strlen($game['scr01src'])==0){?>
		<input type="text" name="scr01" value="" size="100" class="inp"></td>
		<?}else{?>
		<?=$game['scr01src']?> &nbsp;&nbsp;&nbsp;&nbsp; <a href="<?=$game['scr01src']?>" target="_blank">Check original</a> | <a href="" onClick="return deletescreenshot('1');">Delete</a>
		<?}?>

	<tr>
	<td align="right">Scr02:</td>
	<td><?if(strlen($game['scr02src'])==0){?>
		<input type="text" name="scr02" value="" size="100" class="inp"></td>
		<?}else{?>
		<?=$game['scr02src']?> &nbsp;&nbsp;&nbsp;&nbsp; <a href="<?=$game['scr02src']?>" target="_blank">Check original</a> | <a href="" onClick="return deletescreenshot('2');">Delete</a>
		<?}?>

	<tr>
	<td align="right">Scr03:</td>
	<td><?if(strlen($game['scr03src'])==0){?>
		<input type="text" name="scr03" value="" size="100" class="inp"></td>
		<?}else{?>
		<?=$game['scr03src']?> &nbsp;&nbsp;&nbsp;&nbsp; <a href="<?=$game['scr03src']?>" target="_blank">Check original</a> | <a href="" onClick="return deletescreenshot('3');">Delete</a>
		<?}?>


 <tr>
 <td align="right">Logo (100x100):
 <td><input type="text" name="Logo" value="<?=$game['logo']?>" size="75" class="inp">
	 <a href="" onClick="window.open('previewlogo.php?logo=<?=$game['logo']?>', 'newWin_logo', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=0, Resizable=0, Copyhistory=1, Width=111, Height=111');return false;">View</a>
 <tr>
 <td align="right">Download #1: </td>
 <td><input type="text" name="Download1" value="<?=$game['download1']?>" size="100" class="inp">
 <a href="<?=$game['download1']?>" target="_blank">Check</a> </td>
 </tr>
 <tr>
 <td align="right">Download #2: </td>
 <td><input type="text" name="Download2" value="<?=$game['download2']?>" size="100" class="inp">
 <a href="<?=$game['download2']?>" target="_blank">Check</a> </td>
 </tr>

 <tr>
 <td align="right">Download Type:
 <td>
	<select name="download_type">
	<?
		$counter=0;
		foreach ($cats03 as $c)
		{
			$counter++;
			?>
			<option value="<?=$c?>"<?if($c==$game['download_type']){?> selected<?}?>><?=$counter?>-<?=$c?></option>
			<?
		}
	?>
	</select><?=$game['download_type']?>
 </tr>

 
 <tr>
 <td align="right">RegNow ID (XXXX or XXXXX): </td>
 <td><input type="text" name="regnow_id" value="<?=$game['vendorid']?>" size="10" MAXLENGTH="5" class="inp">
 </tr>
 
 
 <tr>
 <td align="right">Featured weight: </td>
 <td><input type="text" name="FeatScale" value="<?=$game['featscale']?>" size="6" class="inp"></td>
 </tr>

	<tr>
	<td align="right" valign=top><b>Similar:</b>
	<td>
	<textarea name="SimilarGames" id="pstinp" class="inp" cols="40" rows="30" style="wrap:virtual;width:400px;height:150px"><?=$game['similargames']?></textarea>



 <tr><td colspan="2">

<tr><td><td><hr>

 <tr>
 <td align="right" valign=top><b>Search Data (Keywords): </b>
 <td>
	<a href="" onClick="return showkeywords(<?=$gameid?>);">Show keywords</a>
	<pre><?//print_r($game['searchdataser'])?></pre>

<tr><td><td><hr>


 <tr><td align="right">Playability: </td>
 <td>
	<select name="Playability" size="1">
		<option value="1" <?if ($game['playability']==1){?>selected<?}?>>0-Poor</option>
		<option value="2" <?if ($game['playability']==2){?>selected<?}?>>1-Below normal</option>
		<option value="3" <?if ($game['playability']==3){?>selected<?}?>>2-Normal</option>
		<option value="4" <?if ($game['playability']==4){?>selected<?}?>>3-Good</option>
		<option value="5" <?if ($game['playability']==5){?>selected<?}?>>4-Very good</option>
		<option value="6" <?if ($game['playability']==6){?>selected<?}?>>5-Excellent!</option>
	</select></td>
 </tr>
 <tr><td align="right">Graphics: </td>
 <td>
	<select name="Graphics" size="1">
		<option value="1" <?if ($game['graphics']==1){?>selected<?}?>>0-Poor</option>
		<option value="2" <?if ($game['graphics']==2){?>selected<?}?>>1-Below normal</option>
		<option value="3" <?if ($game['graphics']==3){?>selected<?}?>>2-Normal</option>
		<option value="4" <?if ($game['graphics']==4){?>selected<?}?>>3-Good</option>
		<option value="5" <?if ($game['graphics']==5){?>selected<?}?>>4-Very good</option>
		<option value="6" <?if ($game['graphics']==6){?>selected<?}?>>5-Excellent!</option>
	</select></td>
 </tr>
 <tr><td align="right">Sounds: </td>
 <td>
	<select name="Sounds" size="1">
		<option value="1" <?if ($game['sounds']==1){?>selected<?}?>>0-Poor</option>
		<option value="2" <?if ($game['sounds']==2){?>selected<?}?>>1-Below normal</option>
		<option value="3" <?if ($game['sounds']==3){?>selected<?}?>>2-Normal</option>
		<option value="4" <?if ($game['sounds']==4){?>selected<?}?>>3-Good</option>
		<option value="5" <?if ($game['sounds']==5){?>selected<?}?>>4-Very good</option>
		<option value="6" <?if ($game['sounds']==6){?>selected<?}?>>5-Excellent!</option>
	</select></td>
 </tr>
 <tr><td align="right">Quality: </td>
 <td>
	<select name="Quality" size="1">
		<option value="1" <?if ($game['quality']==1){?>selected<?}?>>0-Poor</option>
		<option value="2" <?if ($game['quality']==2){?>selected<?}?>>1-Below normal</option>
		<option value="3" <?if ($game['quality']==3){?>selected<?}?>>2-Normal</option>
		<option value="4" <?if ($game['quality']==4){?>selected<?}?>>3-Good</option>
		<option value="5" <?if ($game['quality']==5){?>selected<?}?>>4-Very good</option>
		<option value="6" <?if ($game['quality']==6){?>selected<?}?>>5-Excellent!</option>
	</select></td>
 </tr>
 <tr><td align="right">Originality: </td>
 <td>
	<select name="Orig" size="1">
		<option value="1" <?if ($game['idea']==1){?>selected<?}?>>0-Poor</option>
		<option value="2" <?if ($game['idea']==2){?>selected<?}?>>1-Below normal</option>
		<option value="3" <?if ($game['idea']==3){?>selected<?}?>>2-Normal</option>
		<option value="4" <?if ($game['idea']==4){?>selected<?}?>>3-Good</option>
		<option value="5" <?if ($game['idea']==5){?>selected<?}?>>4-Very good</option>
		<option value="6" <?if ($game['idea']==6){?>selected<?}?>>5-Excellent!</option>
	</select>

 <tr class="gr"><td align="right">Turnbased or Realtime:
 <td><!--input type="text" name="Time" value="$lines[33]" size="3">-->
	<select name="Time" size="1">
		<option value="-10" <?if ($game['time']==-10){?>selected<?}?>>-10 turnbased</option>
		<option value="-9"  <?if ($game['time']==-9 ){?>selected<?}?>>-9 </option>
		<option value="-8"  <?if ($game['time']==-8 ){?>selected<?}?>>-8 </option>
		<option value="-7"  <?if ($game['time']==-7 ){?>selected<?}?>>-7 </option>
		<option value="-6"  <?if ($game['time']==-6 ){?>selected<?}?>>-6 </option>
		<option value="-5"  <?if ($game['time']==-5 ){?>selected<?}?>>-5 </option>
		<option value="-4"  <?if ($game['time']==-4 ){?>selected<?}?>>-4 </option>
		<option value="-3"  <?if ($game['time']==-3 ){?>selected<?}?>>-3 </option>
		<option value="-2"  <?if ($game['time']==-2 ){?>selected<?}?>>-2 </option>
		<option value="-1"  <?if ($game['time']==-1 ){?>selected<?}?>>-1 </option>
		<option value="0"   <?if ($game['time']==-0 ){?>selected<?}?>> 0 neutral</option>
		<option value="1"   <?if ($game['time']== 1 ){?>selected<?}?>> 1 </option>
		<option value="2"   <?if ($game['time']== 2 ){?>selected<?}?>> 2 </option>
		<option value="3"   <?if ($game['time']== 3 ){?>selected<?}?>> 3 </option>
		<option value="4"   <?if ($game['time']== 4 ){?>selected<?}?>> 4 </option>
		<option value="5"   <?if ($game['time']== 5 ){?>selected<?}?>> 5 </option>
		<option value="6"   <?if ($game['time']== 6 ){?>selected<?}?>> 6 </option>
		<option value="7"   <?if ($game['time']== 7 ){?>selected<?}?>> 7 </option>
		<option value="8"   <?if ($game['time']== 8 ){?>selected<?}?>> 8 </option>
		<option value="9"   <?if ($game['time']== 9 ){?>selected<?}?>> 9 </option>
		<option value="10"  <?if ($game['time']== 10){?>selected<?}?>> 10 realtime</option>
	</select>
	 -10 - turnbased, 10 - realtime 


 <tr class="gr"><td align="right">Action or Logical:
 <td><!--input type="text" name="Action" value="$lines[34]" size="3"--> 
	<select name="Action" size="1">
		<option value="-10" <?if ($game['action']==-10){?>selected<?}?>>-10 action</option>
		<option value="-9"  <?if ($game['action']==-9 ){?>selected<?}?>>-9 </option>
		<option value="-8"  <?if ($game['action']==-8 ){?>selected<?}?>>-8 </option>
		<option value="-7"  <?if ($game['action']==-7 ){?>selected<?}?>>-7 </option>
		<option value="-6"  <?if ($game['action']==-6 ){?>selected<?}?>>-6 </option>
		<option value="-5"  <?if ($game['action']==-5 ){?>selected<?}?>>-5 </option>
		<option value="-4"  <?if ($game['action']==-4 ){?>selected<?}?>>-4 </option>
		<option value="-3"  <?if ($game['action']==-3 ){?>selected<?}?>>-3 </option>
		<option value="-2"  <?if ($game['action']==-2 ){?>selected<?}?>>-2 </option>
		<option value="-1"  <?if ($game['action']==-1 ){?>selected<?}?>>-1 </option>
		<option value="0"   <?if ($game['action']==-0 ){?>selected<?}?>> 0 neutral</option>
		<option value="1"   <?if ($game['action']== 1 ){?>selected<?}?>> 1 </option>
		<option value="2"   <?if ($game['action']== 2 ){?>selected<?}?>> 2 </option>
		<option value="3"   <?if ($game['action']== 3 ){?>selected<?}?>> 3 </option>
		<option value="4"   <?if ($game['action']== 4 ){?>selected<?}?>> 4 </option>
		<option value="5"   <?if ($game['action']== 5 ){?>selected<?}?>> 5 </option>
		<option value="6"   <?if ($game['action']== 6 ){?>selected<?}?>> 6 </option>
		<option value="7"   <?if ($game['action']== 7 ){?>selected<?}?>> 7 </option>
		<option value="8"   <?if ($game['action']== 8 ){?>selected<?}?>> 8 </option>
		<option value="9"   <?if ($game['action']== 9 ){?>selected<?}?>> 9 </option>
		<option value="10"  <?if ($game['action']== 10){?>selected<?}?>> 10 logical</option>
	</select>
	-10 - action, 10 - logical


 <tr class="gr">
	<td align="right">Age group - <b>Kids</b>:
	<td><select name="Age1" size="1">
			<option value="-1"<?if ($game['age1']== -1){?> selected<?}?>>-1  Not recommended</option>
			<option value="0"<?if ($game['age1']==  0){?> selected<?}?>>0  Indifferent</option>
			<option value="1"<?if ($game['age1']== 1){?> selected<?}?>>1  Recommended</option>
			<option value="2"<?if ($game['age1']== 2){?> selected<?}?>>2  Highly recommended</option>
		</select>

 <tr class="gr">
	<td align="right"><nobr>Age group - <b>Boys (7-12)</b>:</nobr>
	<td><select name="Age2" size="1">
			<option value="-1"<?if ($game['age2']== -1){?> selected<?}?>>-1  Not recommended</option>
			<option value="0"<?if ($game['age2']==  0){?> selected<?}?>>0  Indifferent</option>
			<option value="1"<?if ($game['age2']== 1){?> selected<?}?>>1  Recommended</option>
			<option value="2"<?if ($game['age2']== 2){?> selected<?}?>>2  Highly recommended</option>
		</select>

 <tr class="gr">
	<td align="right"><nobr>Age group - <b>Girls(7-14)</b>:</nobr>
	<td><select name="Age3" size="1">
			<option value="-1"<?if ($game['age3']== -1){?> selected<?}?>>-1  Not recommended</option>
			<option value="0"<?if ($game['age3']==  0){?> selected<?}?>>0  Indifferent</option>
			<option value="1"<?if ($game['age3']== 1){?> selected<?}?>>1  Recommended</option>
			<option value="2"<?if ($game['age3']== 2){?> selected<?}?>>2  Highly recommended</option>
		</select>

 <tr class="gr">
	<td align="right">Age group - <b>Young men</b>:
	<td><select name="Age4" size="1">
			<option value="-1"<?if ($game['age4']== -1){?> selected<?}?>>-1  Not recommended</option>
			<option value="0"<?if ($game['age4']== 0){?> selected<?}?>>0  Indifferent</option>
			<option value="1"<?if ($game['age4']== 1){?> selected<?}?>>1  Recommended</option>
			<option value="2"<?if ($game['age4']== 2){?> selected<?}?>>2  Highly recommended</option>
		</select>

 <tr class="gr">
	<td align="right">Age group - <b>Old men</b>:
	<td><select name="Age5" size="1">
			<option value="-1"<?if ($game['age5']== -1){?> selected<?}?>>-1  Not recommended</option>
			<option value="0"<?if ($game['age5']== 0){?> selected<?}?>>0  Indifferent</option>
			<option value="1"<?if ($game['age5']== 1){?> selected<?}?>>1  Recommended</option>
			<option value="2"<?if ($game['age5']== 2){?> selected<?}?>>2  Highly recommended</option>
		</select>

 <tr class="gr">
	<td align="right">Age group - <b>Women</b>:
	<td><select name="Age6" size="1">
			<option value="-1"<?if ($game['age6']== -1){?> selected<?}?>>-1  Not recommended</option>
			<option value="0"<?if ($game['age6']== 0){?> selected<?}?>>0  Indifferent</option>
			<option value="1"<?if ($game['age6']== 1){?> selected<?}?>>1  Recommended</option>
			<option value="2"<?if ($game['age6']== 2){?> selected<?}?>>2  Highly recommended</option>
		</select>

 <tr class="gr"><td align="right">CPU speed:
 <td><select name="CPU" size="1">
  <option value="1"<?if ($game['cpu']== 1){?> selected<?}?>>1- 486</option>
  <option value="2"<?if ($game['cpu']== 2){?> selected<?}?>>2- Pentium</option>
  <option value="3"<?if ($game['cpu']== 3){?> selected<?}?>>3- Pentium-II</option>
  <option value="4"<?if ($game['cpu']== 4){?> selected<?}?>>4- Pentium-III</option>
  <option value="5"<?if ($game['cpu']== 5){?> selected<?}?>>5- Pentium-IV</option>
 </select>


 <tr class="gr"><td align="right">Video:
 <td><select name="Video" size="1">
  <option value="1"<?if ($game['video']== 1){?> selected<?}?>>1- No acceleration needed</option>
  <option value="2"<?if ($game['video']== 2){?> selected<?}?>>2- 2D-accelerator</option>
  <option value="3"<?if ($game['video']== 3){?> selected<?}?>>3- Common 3D-accels</option>
  <option value="4"<?if ($game['video']== 4){?> selected<?}?>>4- Fast 3D-accels</option>
  <option value="5"<?if ($game['video']== 5){?> selected<?}?>>5- Extra fast 3D-accels</option>
 </select>


 <tr>
 <td align="right">Features:
 <td><input type="checkbox" name="F1" id=f1<?if ($game['netmode1']=="Y"){?> checked<?}?>><label for=f1 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">Single game</label><br>
	 <input type="checkbox" name="F2" id=f2<?if ($game['netmode2']=="Y"){?> checked<?}?>><label for=f2 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">Multiplaying on single comp</label><br>
	 <input type="checkbox" name="F3" id=f3<?if ($game['netmode3']=="Y"){?> checked<?}?>><label for=f3 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">Multiplaying via LAN</label><br>
	 <input type="checkbox" name="F4" id=f4<?if ($game['netmode4']=="Y"){?> checked<?}?>><label for=f4 style="border: 1px solid #f0f0f0;" onMouseOver="this.style.border='1px dashed #303030'" onMouseOut="this.style.border='1px solid #f0f0f0'">Multiplaying via Inet</label>

 <tr>
	<td align="right" valign="top">
		<br><b>News:</b>
    </td>
	<td>
		<hr>

<?
	for($i=0; $i<count($gamenews); $i++)
	{
		?><?=$gamenews[$i]['newsdate']?> - <?=$gamenews[$i]['comment']?><br><?
	}
?>
        <br><b><a href="./?report=h&force_gameid=<?=$gameid?>">ADD NEW NEWSLINE</a></b>
    <hr>
         </td>
         </tr>

     <tr>
         <td></td>
            <td>
                <br>
                <input type="submit" name="insert" value="      Update!      ">
            </td>
     </tr>
 </table>
 <br>
 </form>

</body>
</html>