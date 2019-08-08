<?

	include_once('padlib.php');
	global $conn;

	if($action == 'updateconstraints')
	{
		$sSQL = "SELECT padfileid, padfiletitle FROM padfiles";
		$games = $conn->getAll($sSQL); echo"<pre>"; //print_r($games);echo"</pre>";
		for($i=0;$i<count($games);$i++)
		{
			$row = $games[$i];//print_r($row);
			$id = $row['padfileid'];
			$title = addslashes($row['padfiletitle']); //echo '!'.$title;
			$found = $conn->getOne("SELECT COUNT(*) c FROM games WHERE title='$title'");
			if ($found > 0)
			{
				$conn->query("UPDATE padfiles SET isgameadded='Y' WHERE padfileid='$id'");
			}
		}
		
	}

	if ($action == 'delpadfiles')
	{
		//echo "Del";
		//print_r($delpad);
		for($i=0;$i<count($delpad);$i++)
		{
			$padid = $delpad[$i];
			$conn->query("UPDATE padfiles SET padfilestate='disabled' WHERE padfileid='$padid'");
		}
		
	}

	// ��������� ����� ����� � ������� 'padfiles'
	if ($action == 'addpadfiles')
	{
		//forea
		$fnames = explode("\n", $padurls);
		foreach($fnames as $fname)
		{
			// ��������� ���������� ����
			$fname = trim($fname);
			if (strlen($fname) <= 7 || substr(strtolower($fname),0,7) <> 'http://' ) continue;

			// ��������� ������������� ������ ������
			$sSQL = "SELECT count(*) FROM padfiles WHERE padfileurl='$fname'";
			$exists = $conn->getOne($sSQL);
			if ($exists != 0) continue;

			// ��������� ���� ����
			$data = ProceedPAD($fname);
			if (isset($data['error'])) {
				echo $data['error'].'<br>';
				$conn->query("INSERT INTO padfiles SET padfileurl='$fname', padfilestate='notxml'");
			} else {

				$company = addslashes($data['developer']);
				$title = addslashes($data['title']);
				$version = addslashes($data['version']);
				$screenshot = addslashes($data['screenshot']);
				$padfilecontent = addslashes($data['padfilecontent']);
				$order = addslashes($data['order']);

				$sSQL = "INSERT INTO padfiles SET padfileurl='$fname', padfilecontent='$padfilecontent', padfilemd5=md5('$padfilecontent'),  padfiledeveloper='$company', padfiletitle='$title', padfileversion='$version', padfilescreenshot='$screenshot', padfileorder='$order'";
				$conn->query($sSQL);
			}

		}
		//print_r($fnames);
	}

	$states = array('enabled','disabled','ignored','tempremoved','alreadyadded','notxml'); //print_r($states);
	$result = array();
	foreach ($states as $state)
	{
		$sSQL = "SELECT * FROM padfiles WHERE padfilestate='$state' ORDER BY padfiledeveloper";
		$result["$state"] = $conn->getAll($sSQL);
	}
?>


<h1>UPDATE CONSTRAINTS</h1>
<form action="./?report=padfile" method="POST">
<input type="hidden" name="action" value="updateconstraints">
<input type="submit" name="update" value="Update!">
</form>
<br><br>



<h1>PAD files URLs - one in row</h1>
<form action="./?report=padfile" method="POST">
<input type="hidden" name="action" value="addpadfiles">
<textarea name="padurls" id="pstinp" class="inp" cols="40" rows="30" style="wrap:virtual;width:100%;height:200px"></textarea>
<input type="submit" name="clear" value="Add PADs">
</form>



<form action="./?report=padfile" method="POST">
<input type="hidden" name="action" value="delpadfiles">
	<h1>Enabled PADs</h1>
    <table cellspacing=1 cellpadding=4 class=frm4>
        <tr>
            <td>
                <b>N</b>
            <td>
                <b>Sel</b>
            <td>
                <b>PAD URL</b>
            <td>
                <b>MD5</b>
            <td>
                <b>Developer</b>
            <td>
                <b>Game Title</b>
            <td>
                <b>Screenshot</b>
            <td>
                <b>Order Page</b>
			<td>

<? 
	$counter = 0;
	$line = array();
	$pline = array();
	for($i=0;$i<count($result['enabled']);$i++)
	{
		$pline = $line;
		$line = $result['enabled'][$i];
		if (strpos($line['padfileorder'], 'regnow') !== false) $line['regnow'] = true;
		if (strpos($line['padfileorder'], 'shareit') !== false) $line['shareit'] = true;
		$mdfive = md5($line['padfilecontent']);
		$counter++;
?>
<tr>
<td><?=$counter?>
<td><input type="checkbox" class="chk" name="delpad[]" value="<?=$line['padfileid']?>">
<td><a href="<?=$line['padfileurl']?>" target=_blank><?=$line['padfileurl']?></a>
<td><?if($mdfive != $line['padfilemd5']){?>Diff<?}else{?>Correct<?}?>
<td><?if($pline['padfiledeveloper']!=$line['padfiledeveloper']){?><?=$line['padfiledeveloper']?><?}?>
<td><?=$line['padfiletitle']?> <b><?=$line['padfileversion']?></b>
<td><a href="<?=$line['padfilescreenshot']?>" target=_blank>Screenshot</a>
<td><a href="<?=$line['padfileorder']?>" target=_blank>Order</a><?if($line['regnow']){?> RegNow<?}?><?if($line['shareit']){?> ShareIt<?}?>
<td>
<?if($line['isgameadded']=='N'){?>
<a href="" onClick="window.open('./editgame.php?proceedpad=<?=$line['padfileid']?>', 'n<?=$counter?>', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=700, Height=600');return false;">Add</a>
<?}else{?>
<b>Added<b>
<?}?>

<?	} ?>
    </table><br>
	<input type="submit" name="clear" value="GO!">
</form>












	<h1>Disabled PADs</h1>
    <table cellspacing=1 cellpadding=4 class=frm4>
        <tr>
            <td>
                <b>N</b>
            <td>
                <b>Sel</b>
            <td>
                <b>PAD URL</b>
            <td>
                <b>MD5</b>
            <td>
                <b>Developer</b>
            <td>
                <b>Game Title</b>
            <td>
                <b>Screenshot</b>
            <td>
                <b>Order Page</b>
<? 
	$counter = 0;
	$line = array();
	$pline = array();
	for($i=0;$i<count($result['disabled']);$i++)
	{
		$pline = $line;
		$line = $result['disabled'][$i];
		if (strpos($line['padfileorder'], 'regnow') !== false) $line['regnow'] = true;
		if (strpos($line['padfileorder'], 'shareit') !== false) $line['shareit'] = true;
		$mdfive = md5($line['padfilecontent']);
		$counter++;
?>
<tr>
<td><?=$counter?>
<td><input type="checkbox" class="chk" name="delpad[]" value="<?=$line['padfileid']?>">
<td><a href="<?=$line['padfileurl']?>" target=_blank><?=$line['padfileurl']?></a>
<td><?if($mdfive != $line['padfilemd5']){?>Diff<?}else{?>Correct<?}?>
<td><?if($pline['padfiledeveloper']!=$line['padfiledeveloper']){?><?=$line['padfiledeveloper']?><?}?>
<td><?=$line['padfiletitle']?> <b><?=$line['padfileversion']?></b>
<td><a href="<?=$line['padfilescreenshot']?>" target=_blank>Screenshot</a>
<td><a href="<?=$line['padfileorder']?>" target=_blank>Order</a><?if($line['regnow']){?> RegNow<?}?><?if($line['shareit']){?> ShareIt<?}?>
<?	} ?>
    </table>








	<h1>NotXML PADs</h1>
    <table cellspacing="1" cellpadding="4" class="frm4">
        <tr>
            <td>
                <b>N</b>
            <td>
                <b>Sel</b>
            <td>
                <b>PAD URL</b>
            <td>
                <b>MD5</b>
            <td>
                <b>Developer</b>
            <td>
                <b>Game Title</b>
            <td>
                <b>Screenshot</b>
            <td>
                <b>Order Page</b>
<? 
	$counter = 0;
	$line = array();
	$pline = array();
	for($i=0;$i<count($result['notxml']);$i++)
	{
		$pline = $line;
		$line = $result['notxml'][$i];
		if (strpos($line['padfileorder'], 'regnow') !== false) $line['regnow'] = true;
		if (strpos($line['padfileorder'], 'shareit') !== false) $line['shareit'] = true;
		$mdfive = md5($line['padfilecontent']);
		$counter++;
?>
<tr>
<td><?=$counter?>
<td><input type="checkbox" class="chk" name="delpad[]" value="<?=$line['padfileid']?>">
<td><a href="<?=$line['padfileurl']?>" target=_blank><?=$line['padfileurl']?></a>
<td><?if($mdfive != $line['padfilemd5']){?>Diff<?}else{?>Correct<?}?>
<td><?if($pline['padfiledeveloper']!=$line['padfiledeveloper']){?><?=$line['padfiledeveloper']?><?}?>
<td><?=$line['padfiletitle']?> <b><?=$line['padfileversion']?></b>
<td><a href="<?=$line['padfilescreenshot']?>" target=_blank>Screenshot</a>
<td><a href="<?=$line['padfileorder']?>" target=_blank>Order</a><?if($line['regnow']){?> RegNow<?}?><?if($line['shareit']){?> ShareIt<?}?>
<?	} ?>
    </table>

