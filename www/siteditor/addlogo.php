<?

global $conn;


$action = $_POST['action'];
$f = $_FILES['f'];

//echo "<pre>";
//print_r($GLOBALS);
//print_r($f);
//echo "</pre>";

function to_chmod($mode)
{
	$str = '';
	$val = substr(strval(decoct($mode)), 3,3);

	if (intval($val[0]) & 4) $str .= 'r'; else $str .= '-';
	if (intval($val[0]) & 2) $str .= 'w'; else $str .= '-';
	if (intval($val[0]) & 1) $str .= 'x'; else $str .= '-';

	if (intval($val[1]) & 4) $str .= 'r'; else $str .= '-';
	if (intval($val[1]) & 2) $str .= 'w'; else $str .= '-';
	if (intval($val[1]) & 1) $str .= 'x'; else $str .= '-';

	if (intval($val[2]) & 4) $str .= 'r'; else $str .= '-';
	if (intval($val[2]) & 2) $str .= 'w'; else $str .= '-';
	if (intval($val[2]) & 1) $str .= 'x'; else $str .= '-';

	return $str;
}



	$root = '../logos';

    if (isset($action))
   	{

        	if ($action=='deletelogos')
	        {
				if(count($del)>0)
				{
	   		 		foreach($del as $a)
		            {
						if(/*@unlink($root.'/'.$a) && */strpos($a,'.png') && !strpos($a,'/') && !strpos($a,'\\') )
						{
							tracker_add("Delete file $a", $slogin);

						?>

						Deleted:<?=$a?><br>

						<?
						}
						else
						{
							tracker_add("Failed to delete file $a", $slogin);

						?>

						<font color=red>Cant delete this file:</font>&nbsp;<?=$a?><br>

						<?
						}


        		    }
				} else 
				{
						?>

						<font color=red>No files selected</font><br>

						<?
				}
    	    }
	}


	if((!isset($f)) || (!is_uploaded_file($f)))
	//if((!isset($f)) || (!is_uploaded_file($_FILES['f'])))
	{
		print_r($f);
?>

<br>

<h1>Add logo</h1>

<form  enctype="multipart/form-data" action="./?report=addlogo" method=POST>
	<table cellpadding=5 cellspacing=2 class=frm4>
		<tr>
			<td align=right>Файл:
			<td width=60%><input type=file name=f size=30 style="width:100%">
		<tr>
			<td align=right>Новое имя (пусто, если не менять):
			<td width=60%><input type=text name=n size=10 style="width:100%">
		<tr>
			<td align=right>Перезаписать файл:
			<td width=60%><input type=checkbox name=re style="border:0" checked=on>
		<tr>
			<td align=right>Утвердить:
			<td width=60%><input class=butt type=submit value="Угу" size=20 style="width:100%">
	</table>
</form>



<?

	} 
	else
	{
		echo '-1-';
		if(!$n)
			$n = $f_name;

		if(!isset($re) || !$re)
		{
			if(!is_file($root.'/'.$n))
			{
				copy($f,$root.'/'.$n);
				/*unlink($f);*/
				tracker_add("Added new logo file $f_name", $login);
			}
			else
			{
				?>
				<font color=red>Файл с таким именем уже есть</font><br>
				<?
			}
		}
		else
		{
			/*@unlink($root.'/'.$n); */
			copy($f,$root.'/'.$n);
			tracker_add("Rewrited or added new logo file $f_name", $slogin);
		}

	} // else

?>



<br>
<h1>Already uploaded</h1>

<?
/*
		$alllogos = array();
		if ($dir = @opendir($root.'/')) 
		{
		  	while (($filename = readdir($dir)) !== false)
			{
				if(!($filename == '..' || $filename == '.'))
				{

					array_push($alllogos,$filename);
				//$filename
				//filesize($root.'/'.$filename);

				}


			} // while
			closedir($dir);	
		}

	    //$base = new MySQLconnector;
	    
					?>

<form action="./?report=addlogo" method="POST">
<input type="hidden" name="action" value="deletelogos">

    <table cellspacing=1 cellpadding=4 class=frm4>
        <tr>
            <td bgcolor=#eeeeee>
                <b>N</b>
            <td bgcolor=#eeeeee>
                <b>Del</b>
            <td bgcolor=#eeeeee>
                <b>Filename</b>
            <td bgcolor=#eeeeee>
                <b>Size</b>
            <td bgcolor=#eeeeee>
                <b>Permission</b>
            <td bgcolor=#eeeeee>
                <b>Comment</b>

		<?
		$counter = 0;
		reset($alllogos);
		for($i=0;$i<count($alllogos);$i++)
		{		
			$counter++;
	 		//$lines = file($root.'/'.$key);
			//for($i=0;$i<count($lines);$i++)
			//	$lines{$i} = rtrim($lines{$i});
		?>

        <tr>
            <td>
			<?=$counter?>
            <td>
				<input type="checkbox" name="del[]" class="check1" value="<?=$alllogos{$i}?>">
            <td>
			<?
				$flag = true;
				if(preg_match("/.*[.]jpg/i", $alllogos{$i}))
				{
					$flag = false;
			?>
					<img src="../images/admin/icon_jpg.gif" width=16 height=16 align=center>
			<?
				}
			?>

			<?
				if(preg_match("/.*[.]gif/i", $alllogos{$i}))
				{
					$flag = false;
			?>
					<img src="../images/admin/icon_gif.gif" width=16 height=16 align=center>
			<?
				}
			?>

			<?
				if($flag)
				{
			?>
					<img src="../images/admin/icon_unk.gif" width=16 height=16 align=center>
			<?
				}
			?>



			<a href="" onClick="window.open('previewlogo.php?logo=<?=$alllogos{$i}?>', 'newWin<?=$counter?>', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=0, Resizable=0, Copyhistory=1, Width=111, Height=111');return false;"><?=$alllogos{$i}?></a>
            <td>
			<?=number_format(filesize($root.'/'.$alllogos{$i}),0,'.',' ')?> bytes
            <td>
  			<?=to_chmod(fileperms($root.'/'.$alllogos{$i}))?> 
  			(<?=substr(strval(decoct((fileperms($root.'/'.$alllogos{$i})))),3,3)?>)

            <td>
				<?
				    $rows = $conn->getOne("SELECT count(*) FROM games WHERE logo='".$alllogos{$i}."'");
					//$rows=$base->BaseRows(); print $rows.$alllogos{$i};
					//$rows=$base->BaseFetch();
					if ($rows == 0) 
					{
						?>
							Not used in database
						<?
					}		
				    //$base->BaseFinish();


				?>

		<?

		}

		?>

	</table>
	<br>	
 <input type="submit" name="clear" value="Delete Selected Logos">
 </form>

<?      */
		//$base->BaseDisconnect();

?>