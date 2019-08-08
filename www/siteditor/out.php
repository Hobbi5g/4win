<?php

	$beg=$byear . '-' . $bmonth . '-' . $bday;
	$end=$eyear . '-' . $emonth . '-' . $eday;

	$base = newMySQlConnector;
	$base->BaseSelect("select rating from statistics where date<='$end' AND date>='$beg'");
	$data = $base->BaseFetch();
	$spisok = unserialize($data["rating"]);
	
	while ($data<>'')
	{
		$data = $base->BaseFetch();
		$spisok1 = unserialize($data["rating"]);
		$spisok = $spisok+$spisok1;
	}
	asort($spisok);
	reset($spisok); 
	
	while (list($key, $val) = each($spisok)) 
	{ 
	   echo "$key = $val\n"; 
	} 

?>