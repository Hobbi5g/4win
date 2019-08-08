<?
    if (isset($action))
   	{

        	if ($action=='deletesubmits')
	        {


				$root = $submits_path;
   		 		foreach($del as $a)
	            {
						if(@unlink($root.'/'.$a))
						{
							tracker_add("Delete submit $a", $slogin);

						?>

						Deleted:<?=$a?><br>

						<?
						}
						else
						{
							tracker_add("Failed to delete submit $a", $slogin);

						?>

						<font color=red>Cant delete this file:</font>&nbsp;<?=$a?><br>

						<?
						}
        	    }
    	    }


	} 
	else
	{
	     
		$root = $submits_path;
		$allgames = array();
		$counter = 0;
		if ($dir = opendir($root.'/')) 
		{
		  	while ($counter<500 && (($filename = readdir($dir)) !== false))
			{    
				if((!($filename=='.' || $filename=='..'))/* && preg_match("/.*[.]txt/i", $filename)*/)
				{
					$counter++;
				 	$lines = file($root.'/'.$filename);
					for($i=0;$i<count($lines);$i++)
						$lines{$i} = rtrim($lines{$i});

					//$allgames["$filename"] = $lines{2}.' '.$lines{3};
					$a = array($filename => $lines{2}.' '.$lines{3});
					$allgames = array_merge($allgames,$a);

		        } // if preg_match
				
			} // while
			closedir($dir);	
		}

		//asort($allgames);

		   
		?>

	<form action="./?report=o" method="POST">
	<input type="hidden" name="action" value="deletesubmits">

	<h1>Old style submits</h1>
    <table cellspacing=1 cellpadding=4 class=frm4>
        <tr>
            <td bgcolor=#eeeeee>
                <b>N</b>
            <td bgcolor=#eeeeee>
                <b>Del</b>
            <td bgcolor=#eeeeee>
                <b>Title</b>
            <td bgcolor=#eeeeee>
                <b>Download</b>
            <td bgcolor=#eeeeee>
                <b>Desc</b>
            <td bgcolor=#eeeeee>
                <b>Action</b>

		<?

		$counter = 0;
		reset($allgames);
		while(list($key, $val) = each($allgames))
		{		
			$counter++;
	 		$lines = file($root.'/'.$key);
			for($i=0;$i<count($lines);$i++)
				$lines{$i} = rtrim($lines{$i});

					?>
				        
    <tr>
		<td>
			<?if(strpos($lines[12],'regnow')==true){?><font color=blue><?}?>N:<?=$counter?><br>[<?=$key?>]<br><nobr><?=$lines{0}?></nobr><?if(strpos($lines[12],'regnow')==true){?></font><?}?>
	    <td>
			<input type="checkbox" name="del[]" style="border:0" value="<?=$key?>">
		<td>
			<nobr><?=$lines{2}?>,<nobr><br>ver. <?=$lines{3}?><br>by <?=$lines{4}?>
	    <td>
			<nobr><a href="<?=$lines{14}?>">download1</a> [<?=$lines{16}?>]</nobr>
			<?if (!(($lines{15}=='http://')||($lines{15}==''))){?><br><a href="<?=$lines{15}?>">download2</a><?}?>
			<?if (isset($lines{17})){?><br><nobr><b><?=$lines{17}?></b></nobr><?}?>
    	<td><?=$lines{9}?>
	    <td> 
        	<nobr><a href="" onClick="window.open('./editgame.php?oldsubmit=<?=$key?>', 'newWin<?=$counter?>', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=1, Resizable=1, Copyhistory=1, Width=700, Height=600');return false;">Edit (new window)</a></nobr>

			<?

		}

		?>
    </table>
	<br>
	<input type="submit" name="clear" value="Delete Selected Submits">
 	</form>
	<?=$counter?> submits total.<br>
		<?

	}


?>