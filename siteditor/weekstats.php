<?php                     

	//$base = new MySQLConnector;

	//$base->BaseSelect("select rating from statistics where stat_date<=CURDATE() AND stat_date>=DATE_SUB(CURDATE(), INTERVAL 7 DAY)");		
	$sSQL = "select rating from statistics where stat_date<=CURDATE() AND stat_date>=DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
	$data = $conn->getAll($sSQL);

	$resulttable = array();
	
	//while ($data = $base->BaseFetch())
	for($i=0; $i<count($data);$i++)
	{
		$spisok1 = unserialize($data[$i]["rating"]);
		while (list($key, $val) = each($spisok1)) 
		{ 
			$resulttable[$key]+=$val;
        }
    }

	arsort($resulttable);
	reset($resulttable); 
    //$base->BaseDisconnect();
?>
<h1>"From" links :</h1>

    <table cellspacing=1 cellpadding=4 class=frm4>
        <tr>
            <td bgcolor=#eeeeee>
                <b>Link name</b>
            <td bgcolor=#eeeeee>
                <b>counter</b>
<?	
	while (list($key, $val) = each($resulttable)) 
	{ 
	?>
        <tr>
            <td bgcolor=#eeeeee>
				<?=$key?>
            <td bgcolor=#eeeeee>
				<?=$val?>
		<?
	} 
?></table><br><?



?>

