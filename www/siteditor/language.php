<?

	global $conn;

	$map = array('en', 'ru');

	if (!empty($updatelanguagefiles))
	{   
		$sSQL = "SELECT * FROM lang ORDER BY phraseid";
		$data = $conn->getAll($sSQL);
		//echo '<pre>'; print_r($data); echo '</pre>';

		foreach ($map as $m)
		{
			$f = fopen ('./../lang/'.$m.'.php', "w");

			fwrite($f, "<?\n\n");
			fwrite($f, "\t //This file was generated automatically. Please don't change it.\n\n");
			fwrite($f, "\t\$lang = array();\n");
			fwrite($f, "\t\$links = array();\n");
			fwrite($f, "\t\$lang['lang'] = '{$m}';\n");
			fwrite($f, "\t\$lang['{$m}'] = true;\n\n");

		                      
			for($i=0; $i<count($data); $i++)
			{
				$key = $data[$i]['stringid'];
				$val = $data[$i][$m];
				$vallink = $data[$i]["{$m}_link"];
				fwrite($f, "\t\$lang['{$key}'] = '{$val}';\n");
				if (!empty($vallink))
				{
					fwrite($f, "\t\$links['{$key}'] = '{$vallink}';\n\n");
				}


			}

			fwrite($f, "\n\n\t\$this->tpl->assign('lang', \$lang);");
			fwrite($f, "\n\n\t\$this->tpl->assign('links', \$links);");
			fwrite($f, "\n\n?>");
			fclose($f);
		}

	}
	
?>

	<form action="./?report=language" method="POST">
		<input type="hidden" name="updatelanguagefiles" value="true">
		<input type="submit" value="Update language files">
	</form>

RAND:<?=rand(10000,99999);?>