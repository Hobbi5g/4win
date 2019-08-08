<?

	global $conn;

	$map = array('en', 'ru');

	if (!empty($setitems))
	{   
		$sSQL = "SELECT COUNT(*) FROM settings WHERE name='featured'";
		if ($conn->getOne($sSQL) == 0)
		{
			// ��������� �� �������
			?><h1><font color="red">'FEATURED' setting not found.</font></h1><?
		} else
		{
			
			$lines = explode("\r\n", $featuredlist); //print_r($lines);
			for ($i=0; $i<count($lines); $i++)
			{
				$sSQL = "SELECT COUNT(*) FROM games WHERE stringid='{$lines[$i]}'";
				if ($conn->getOne($sSQL) == 0)
				{
					?>Gameid '<?=$lines[$i]?>' not found<br><?
				}
			}

			$featuredlist = addslashes($featuredlist);
			$sSQL = "UPDATE settings SET value='{$featuredlist}' WHERE name='featured'";
			$data = $conn->getAll($sSQL);
		}

	}
	
	$sSQL = "SELECT value FROM settings WHERE name='featured'";
	$feat = $conn->getOne($sSQL);
?>

<h1>SET <font color="blue">FEATURED</font> LIST</h1>
	<form action="./?report=mainmenu" method="POST">
		<input type="hidden" name="setitems" value="true">
		<textarea name="featuredlist" id="pstinp" class="inp" cols="40" rows="30" style="wrap:virtual;width:400px;height:200px"><?=$feat?></textarea>
		<br>
		<input type="submit" value="Set menu items">
	</form>

<hr>

RAND:<?=rand(10000,99999);?>