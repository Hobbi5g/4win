<?              
    include_once ('searchlib.php');

	global $conn;

    if (isset($action))
    {

		$counter = 1;
		$conn->query("DELETE FROM searchdata"); // clear searchdata

		$sSQL = 'SELECT * FROM games ORDER BY gameid';
		if ($submittest) $sSQL .= " LIMIT ".$testrecords;

		$ids = $conn->getCol($sSQL);
		foreach($ids as $id)
		{
			AddSearchInfo($id);
		}

						

		?>
			<?if($row['hidden']=='Y'){?><font color="#c0c0c0"><?}?>
			<?=$counter++?>&nbsp;[id:<?=$row['gameid']?>]&nbsp;<?=$row['title']?>, <?=count($pieces)?> words indexed<br>
			<?if($row['hidden']=='Y'){?></font><?}?>
			<?//print_r($pieces)?>
		<?
			flush();
	}

		
//    } 



?>
<p><h1>Search Words</h1><p>

<script language="JavaScript">
function a(id)
{
	window.open('./vs.php?wid='+id, 'newWin'+id, 'Scrollbars=1, Resizable=1, Width=300, Height=500');
}
</script>

 <form action="./?report=search" method="POST">
 <input type="hidden" name="action" value="generatesearchwords">
    <table>
    <tr>
        <td>Generate<td><input type="submit" name="submit" value="           Go!          "> <input type="submit" name="submittest" value=" Test (<?=$testrecords?> records only)">

    </table>

 </form>

<b>Words:</b>

    <table cellspacing=1 cellpadding=4 class=frm4 bgcolor=#eeeeee>
        <tr>
            <td>
                <b>N</b>
            <td>
                <b>Word</b>
            <td>
                <b>Pages</b>
<?
	$counter = 0;
    $rows = $conn->getAll("SELECT searchdata_id, stemmed_word, relevant_docs FROM searchdata ORDER BY stemmed_word, searchdata_id");
	foreach($rows as $row)
	{
		$counter++;
		$c = count(unserialize($row['relevant_docs']));
	?><tr><td><?=$counter?><td><a href="" onClick="a(<?=$row['searchdata_id']?>);return false;"><?=$row['stemmed_word']?></a><td><?=$c?><?
	}
?>
	</table>
<?


?>