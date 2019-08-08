<?
	global $conn;
	
	$sSQL = "SELECT gameid, stringid, title, similargames FROM games ";
	//$sSQL .= ' WHERE download1 LIKE "%games4win.com%" OR download2 like "%games4win.com%" ';
	$sSQL .= ' WHERE platform="genesis"';
	$data = $conn->getAll($sSQL." ORDER BY gameid");
	
	$games = array();
	foreach ($data as $d) {
		if ($d['similargames'] == '') {
			$d['found_games'] = array();
			array_push($games, $d);
		} else {
			
		// similar games
		//$d['similargames'] = trim($d['similargames']);
		
		$sims = explode("\n", trim($d['similargames']));
		
		
		//print "------<br>";
		//print '--'.$d['title'].'<br>';
		
		$found_games = array();
		foreach($sims as $sim) {
			$sim = trim($sim);
			$is_found = $conn->getOne("SELECT COUNT(*) FROM games WHERE stringid = '{$sim}' AND hidden = 'N' ");
			if ($is_found == 0) {
				//print $sim.'<br>';
				array_push($found_games, $sim);
			}
//			if (count($found_games)>0){ print_r($found_games); }
		}
//		if (count($found_games)>0){ print_r($found_games); }
		
		$d['found_games'] = $found_games;
		array_push($games, $d);
		
		//for($i=0; $i < count($sims); $i++) {
		//	$sims[$i] = "'".trim($sims[$i])."'";
		//}
		//$sSQL = "SELECT stringid, title FROM games WHERE hidden='N' AND ";
		//$sSQL .= "stringid IN(".implode(',', $sims).")";
		//$res = $this->conn->getAll($sSQL);
		//if (count($res) > 0)
		//	$game['similargames'] = $res; 			
			
			
		}
	}

?>

<script>
	function eg(gid) {
		window.open('./editgame.php?gameid='+gid, 'newWin'+gid, 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=750, Height=600');
		return false;
	}
</script>

<h1>Similar(<?=count($games) ?> total)</h1>
<table>
	<tr>
		<th>id</th>
		<th>Game title</th>
		<th>Errors found</th>
	</tr>
		
	<? foreach ($games as $game) { ?>
		<tr>
			<td><?=$game['gameid']?></td>
			<td><a href="" onClick="return eg(<?=$game['gameid']?>);"><?=$game['title']?></a></td>
			<td><? if (count($game['found_games']) > 0) { ?>
					<?=implode('<br>', $game['found_games'])?>
				<? } else { ?>
					N/A
				<? } ?>
			</td>
		</tr>
	<?}?>

</table>