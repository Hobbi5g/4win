<?     
	
	$add = true;        
	$counter=0;

    $hidnews = $_POST['hidnews'];
    $nlid = $_POST['nlid'];
    $relatedgame = $_POST['relatedgame'];

    $action = $_POST['action'];


    if (isset($action)) {

        if ($action=='addnews') {
			$text = $_POST['text'];

			if ($relatedgame == 'null') {
                $conn->query("INSERT INTO news (comment, newsdate) values (?, NOW())", array($text));
			}
			else {
                $conn->query("INSERT INTO news (comment, gameid, newsdate) values (?, ?, NOW() )", array($text, $relatedgame));
			}
        }

        if ($action=='deletenews') {
            foreach($del as $a) {
                $conn->query("DELETE FROM news WHERE newsid = ? LIMIT 1", array($a));
            }
        }

		// save edited line
        if ($action=='editnews') {
		    $text = $_POST['text'];
			//$text = addslashes($text);
			//$text_ru = addslashes($text_ru);

			$conn->query("UPDATE news SET comment = ?, gameid = ? WHERE newsid = ? LIMIT 1", array($text, $relatedgame, $nlid));

			//if ($relatedgame != "null")
			//{
				//if (empty($text_ru))
			//		$conn->query("UPDATE news SET comment='$text', gameid=$relatedgame WHERE newsid=$nlid");
				//else
				//	$conn->query("UPDATE news SET comment='$text', comment_ru='{$text_ru}', gameid=$relatedgame WHERE newsid=$nlid");
			//}
			//else
			//{
				//if (empty($text_ru))
			//		$conn->query("UPDATE news SET comment='$text', gameid = NULL  WHERE newsid=$nlid");
				//else
				//	$conn->query("UPDATE news SET comment='$text', comment_ru='{$text_ru}', gameid=NULL WHERE newsid=$nlid");
			//}

		}

		// user want to edit line
		if ($action == 'editnewsline') {
			// edit newsline

			$hnews = $_POST['hnews'];
			$add = false;

			$data = $conn->getRow("SELECT * FROM news WHERE newsid = ?", array($hnews));

			$gid = $data['gameid'];
			$line = htmlentities(stripslashes($data['comment']));
            $line_ru = htmlentities(stripslashes($data['comment_ru']), ENT_QUOTES);
		}
		else {
			$add = true;
		}

    }

    // force news addition from editgame section
	$gametitle = '';
    $force_gameid = $_POST['force_gameid'];
	if($force_gameid) {
		$gametitle = $conn->getOne($sSQL = "SELECT title FROM games WHERE gameid = ?", array($force_gameid) );
	}

?>

<script>
	function editnews(newslineid)
	{
		document.getElementById('hidnews').value = newslineid;
		subform.submit();
	}

	function Check()
	{
		if (document.getElementById('mytext').value == '') {
			alert("Really need a newsline!");
			return false;
		} else return true;


	}
</script>

 <p align="center"><h1>News Editor - <?if($add){?>Add newsline<?}else{?><font color=blue>EDIT NEWSLINE <?=$hgame?></font><?}?></h1> </p>

 <form action="./?report=h" method="POST">
<?if ($add){?>
 <input type="hidden" name="action" value="addnews">
<?}else{?>
 <input type="hidden" name="action" value="editnews">
 <input type="hidden" name="nlid" value="<?=$hnews?>">
<?}?>
    <table>
    <tr>
     <td>Text (en): <td><input ID="mytext" type="text" name="text" size="150" maxlength="200" class="input1" value="<?if(!$add){?><?=$line?><?}?><?if($force_gameid){?><a href=&quot;#&quot;><?=$gametitle?></a><?}?>"><br>
    <tr>
     <td>Text (ru): <td><input ID="mytext" type="text" name="text_ru" size="150" maxlength="200" class="input1" value="<?if(!$add){?><?=$line_ru?><?}?><?if($force_gameid){?><a href=&quot;#&quot;><?=$gametitle?></a><?}?>"><br>
    <tr>
     <td>Related game: <td><select name="relatedgame" size="1">
                        <option value="null">NULL</option>

<?
    $sSQL = 'SELECT gameid, title, SUBSTRING(title,1,1) tletter FROM games';
	if($force_gameid)
		$sSQL .= " WHERE gameid = \"{$force_gameid}\" ";
	$sSQL .= ' ORDER BY 1';

	$data = $conn->getAll($sSQL);
	foreach ($data as $row)
	{
		?>
                        <option value="<?=$row['gameid']?>"<?if((!$add && $row['gameid']==$gid) || (!empty($force_gameid) && $force_gameid==$row['gameid'] ) ){?> selected<?}?>><?=$row['title']?> - <?=$row['tletter']?>-<?=$row['gameid']?> - </option>
		<?
	}
?>
                    </select>

    <tr>
        <td><td>

<?if ($add){?>
			<input type="submit" name="submit" value="Post new newsline" onClick="return Check();">
<?} else {?>
			<input type="submit" name="submit" value="Edit the newsline" onClick="return Check();">
<?}?>

    </table>

 </form>

	<form action="./?report=h" method="POST" ID="subform">
		<input type="hidden" name="action" value="editnewsline">
		<input type="hidden" ID="hidnews" name="hnews" VALUE="">
	</form>




<p><h1>News in database</h1><p>

<form action="./?report=h" method="POST">
<input type="hidden" name="action" value="deletenews">

<b>News:</b>

    <table cellspacing=1 cellpadding=4 class=frm4 bgcolor=#eeeeee>
        <tr>
            <td>
                <b>N</b>
            <td>
                <b>Edit</b>
            <td>
                <b>Date</b>
            <td>
                <b>Del</b>
            <td>
                <b>Newsline</b>
        <? 
		    $sSQL = 'SELECT newsid, gameid, comment, comment_ru, DATE_FORMAT(newsdate,"%d") tday, MONTHNAME(newsdate) tmonth, YEAR(newsdate) tyear FROM news ORDER BY newsid DESC LIMIT 200';
		    $news = $conn->getAll($sSQL);

			foreach ($news as $game ){  $counter++; 
			unset($related);
			if (isset($game['gameid']))
			{

			    $sSQL = 'SELECT title,gameid FROM games WHERE gameid='.$game['gameid'];
				$row = $conn->getRow($sSQL);

				$related = $row['title'];
				$related_id = $row['gameid'];
			}
		?>
        <tr <?if($counter % 2){?>style="background-color: #e0e0e0;"<?}?>>
            <td>
                <?=$game['newsid']?>&nbsp;[<?=$counter?>]
            <td>
				<a href="javascript:editnews(<?=$game['newsid']?>);">Edit</a>
            <td>
                <nobr><?=$game['tday']?> <?=$game['tmonth']?> <?=$game['tyear']?></nobr>
				<!--b><nobr><?=(substr($game['newsdate'],4,2))?>-<?=(substr($game['newsdate'],2,2))?>-<?=(substr($game['newsdate'],0,2)+2000)?></nobr></b-->
            <td>
                <input type="checkbox" name="del[]" style="border:0" value="<?=$game['newsid']?>">
            <td>
                <b><?=$game['comment']?></b><br><?=htmlspecialchars($game['comment'])?>
				<?if(!empty($game['comment_ru'])){?><br><br><b>RU:<?=$game['comment_ru']?></b><br><?=htmlspecialchars($game['comment_ru'])?><br><?}?>
				<?if($related){?><br><font color=blue>Related game:&nbsp;<?=$related?> (<?=$related_id?>)</font><?}?>

        <?}?>
    </table>

 <br>
 <input type="submit" name="clear" value="Delete Selected News">
 </form>


 <?//=$base->BaseRows();?><!--records total.-->
<?
   // $base->BaseFinish();
?>