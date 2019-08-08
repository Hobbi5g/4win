<?
	/*
	*
	* Search routines
	*
	*/

	global $conn;

	// const
	$testrecords = 15;



	function AddSearchInfo($gameid)
	{
		global $conn;

		echo "0<br>";

		$stopwords = file('stopwords.dta');
		for ($i=0;$i<count($stopwords);$i++)
			$stopwords[$i] = rtrim($stopwords[$i]);

		if (!is_numeric($gameid)) die('Error. gameid is not numeric.');
		$sSQL = "SELECT gameid, longdesc, title, stringid, hidden FROM games WHERE gameid='$gameid'";
		$row = $conn->getRow($sSQL);

		$h2t = new html2text($row['title'].'. '.$row['longdesc']);
		$stemmer = new Stemmer();

		$desc = $h2t->get_text(); 
		$desc = str_replace("\n", ' ', $desc);

		echo "1<br>";


		// удаляем повторяющиеся пробелы
		$desc2 = $desc; $desc = $desc[0];
		for($i=1;$i<strlen($desc2);$i++)
			if(!($desc2[$i]==' ' && $desc2[$i-1]==' ')) $desc .= $desc2[$i];

		echo "2<br>";


		//echo $desc;
  		$pieces = explode(" ", $desc); $unstemmedtext = $pieces;
		$pieces = $stemmer->stem_list($pieces); $stemmedtext = $pieces;
		$temparr = $pieces; //print_r($pieces);
		$pieces = array();

		foreach ($temparr as $val)
		{
			if (!in_array($val, $stopwords))
				array_push($pieces, $val);
		}
		$temparr = $pieces;
		$pieces = array();
			
		foreach ($temparr as $val)
				$pieces["$val"]++;
//		ksort($pieces);
		arsort($pieces);


		echo "3<br>";


		$save = array();
		// составляем сниппеты для слов описания
		foreach($pieces as $word => $wordweight) // бежим по всем словам кортежа
		{
			//$find = array_search($word, $stemmedtext);
			$found = array();
			for($i=0;$i<count($stemmedtext);$i++)
				if($stemmedtext[$i] == $word)
					array_push($found, $i);

			if (count($found) > 0) // составляем сниппеты
			{
				$counter = 0;
				foreach($found as $i)
				{
					// определяем границы сниппета
					$startword = $i - 5;
					if ($startword < 0) $startword = 0;
					$endword = $i + 5;
					if ($endword > count($stemmedtext)-1) $endword = count($stemmedtext)-1;

					$snippet = '';
					for ($x=$startword;$x<=$endword;$x++)
						if ($x != $i)
							$snippet .= $unstemmedtext[$x].' ';
						else
							$snippet .= '<b>'.$unstemmedtext[$x].'</b> ';

					$save['snippets']["$word"]["$counter"] = $snippet;

					$counter++;
	
				}
			}
			//print_r($found);

		}

		echo "4<br>";

		// подбираем начальные слова к словам, обработанным стеммером
		$keywords = array();
		foreach($pieces as $word => $wordweight)
		{
			$originalwords = $conn->getCol("SELECT DISTINCT original FROM stemmedwords WHERE stemmed='$word'");
			if (count($originalwords) > 0) 
				$keywords = array_merge($keywords, $originalwords);
		}

		echo "5<br>";


		// сериализируем и пишем в базу
		$save['words'] = $pieces;
		$save['keywords'] = $keywords;

		//echo "<pre>";
		//print_r($save);
		//print_r($unstemmedtext);
		//print_r($stemmedtext);
		//echo "</pre>";

		// write collected data for each game			
		$ser = serialize($save);
		$ser = addslashes($ser);
		$conn->query("UPDATE games SET searchdataser='$ser' WHERE gameid=".$row['gameid']);

		echo "6<br>";


		foreach ($pieces as $key => $val)
		{
			$numof = $conn->getOne("SELECT COUNT(*) FROM searchdata WHERE stemmed_word='".$key."'");
			if ($numof > 0)
			{   
				$row3 = $conn->getRow("SELECT * FROM searchdata WHERE stemmed_word='".$key."'");
   		        $stored = unserialize($row3['relevant_docs']);
				$stored[$row['stringid']] = $val;
				$ser = addslashes(serialize($stored));
				$conn->query("UPDATE searchdata SET stemmed_word='$key', relevant_docs='".$ser."' WHERE searchdata_id=".$row3['searchdata_id']);
			} 
			else
			{
				$stored = array();
				$stored[$row['stringid']] = $val;
				$ser = addslashes(serialize($stored));
   				$conn->query("INSERT INTO searchdata SET stemmed_word='$key', relevant_docs='".$ser."'");
			}
		}


		echo "7<br>";


	}



?>