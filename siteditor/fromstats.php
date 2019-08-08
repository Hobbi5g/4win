<?php                     

	$datas = array();

	$sSQL = '';
	if (isset($page))
	{

		$beg=$byear . '-' . $bmonth . '-' . $bday;
		$end=$eyear . '-' . $emonth . '-' . $eday;

		//$base->BaseSelect("select rating from statistics where stat_date<='$end' AND stat_date>='$beg'");
		$sSQL = "select rating from statistics where stat_date<='$end' AND stat_date>='$beg'";
	}
	else
	{
		//$base->BaseSelect("select rating from statistics where stat_date<=CURDATE() AND stat_date>=DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
		$sSQL = "select rating from statistics where stat_date<=CURDATE() AND stat_date>=DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
		
		$today = getdate();

		$eyear  = $today["year"]; // end date
		$emonth  = $today["mon"];
		$eday  = $today["mday"];

		$byear = $eyear; // begin date
		$bmonth = $emonth;
		$bday  = 1;


    }


	$resulttable = array();
	
	$data = $conn->getAll($sSQL);
	//while ($data = $base->BaseFetch())
	for($i=0; $i<count($data); $i++)
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
<h1>"From" links <?if(isset($page)){?>(<?=$bday?>.<?=$bmonth?>.<?=$byear?> - <?=$eday?>.<?=$emonth?>.<?=$eyear?>)<?}?>:</h1>

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

<form name="form1" method="post" action="./?report=stats">
<input type="hidden" name="page" value="fromstatsupdate">
Просмотреть статистику <br>
c
<select name="bday">
<option value="1" <?if($bday==1){?>selected<?}?>>1</option>
<option value="2" <?if($bday==2){?>selected<?}?>>2</option>
<option value="3" <?if($bday==3){?>selected<?}?>>3</option>
<option value="4" <?if($bday==4){?>selected<?}?>>4</option>
<option value="5" <?if($bday==5){?>selected<?}?>>5</option>
<option value="6" <?if($bday==6){?>selected<?}?>>6</option>
<option value="7" <?if($bday==7){?>selected<?}?>>7</option>
<option value="8" <?if($bday==8){?>selected<?}?>>8</option>
<option value="9" <?if($bday==9){?>selected<?}?>>9</option>
<option value="10" <?if($bday==10){?>selected<?}?>>10</option>
<option value="11" <?if($bday==11){?>selected<?}?>>11</option>
<option value="12" <?if($bday==12){?>selected<?}?>>12</option>
<option value="13" <?if($bday==13){?>selected<?}?>>13</option>
<option value="14" <?if($bday==14){?>selected<?}?>>14</option>
<option value="15" <?if($bday==15){?>selected<?}?>>15</option>
<option value="16" <?if($bday==16){?>selected<?}?>>16</option>
<option value="17" <?if($bday==17){?>selected<?}?>>17</option>
<option value="18" <?if($bday==18){?>selected<?}?>>18</option>
<option value="19" <?if($bday==19){?>selected<?}?>>19</option>
<option value="20" <?if($bday==20){?>selected<?}?>>20</option>
<option value="21" <?if($bday==21){?>selected<?}?>>21</option>
<option value="22" <?if($bday==22){?>selected<?}?>>22</option>
<option value="23" <?if($bday==23){?>selected<?}?>>23</option>
<option value="24" <?if($bday==24){?>selected<?}?>>24</option>
<option value="25" <?if($bday==25){?>selected<?}?>>25</option>
<option value="26" <?if($bday==26){?>selected<?}?>>26</option>
<option value="27" <?if($bday==27){?>selected<?}?>>27</option>
<option value="28" <?if($bday==28){?>selected<?}?>>28</option>
<option value="29" <?if($bday==29){?>selected<?}?>>29</option>
<option value="30" <?if($bday==30){?>selected<?}?>>30</option>
<option value="31" <?if($bday==31){?>selected<?}?>>31</option>

</select>
<select name="bmonth">
<option value="1" <?if($bmonth==1){?>selected<?}?>>января (1)</option>
<option value="2" <?if($bmonth==2){?>selected<?}?>>февраля (2)</option>
<option value="3" <?if($bmonth==3){?>selected<?}?>>марта (3)</option>
<option value="4" <?if($bmonth==4){?>selected<?}?>>апреля (4)</option>
<option value="5" <?if($bmonth==5){?>selected<?}?>>мая (5)</option>
<option value="6" <?if($bmonth==6){?>selected<?}?>>июня (6)</option>
<option value="7" <?if($bmonth==7){?>selected<?}?>>июля (7)</option>
<option value="8" <?if($bmonth==8){?>selected<?}?>>августа (8)</option>
<option value="9" <?if($bmonth==9){?>selected<?}?>>сентября (9)</option>
<option value="10" <?if($bmonth==10){?>selected<?}?>>октября (10)</option>
<option value="11" <?if($bmonth==11){?>selected<?}?>>ноября (11)</option>
<option value="12" <?if($bmonth==12){?>selected<?}?>>декабря (12)</option>

</select>
<select name="byear">
<option value="2003" <?if($byear==2003){?>selected<?}?>>2003</option>
<option value="2004" <?if($byear==2004){?>selected<?}?>>2004</option>
<option value="2005" <?if($byear==2005){?>selected<?}?>>2005</option>
<option value="2006" <?if($byear==2006){?>selected<?}?>>2006</option>
<option value="2007" <?if($byear==2007){?>selected<?}?>>2007</option>
</select>
по
<select name="eday">
<option value="1" <?if($eday==1){?>selected<?}?>>1</option>
<option value="2" <?if($eday==2){?>selected<?}?>>2</option>
<option value="3" <?if($eday==3){?>selected<?}?>>3</option>
<option value="4" <?if($eday==4){?>selected<?}?>>4</option>
<option value="5" <?if($eday==5){?>selected<?}?>>5</option>
<option value="6" <?if($eday==6){?>selected<?}?>>6</option>
<option value="7" <?if($eday==7){?>selected<?}?>>7</option>
<option value="8" <?if($eday==8){?>selected<?}?>>8</option>
<option value="9" <?if($eday==9){?>selected<?}?>>9</option>
<option value="10" <?if($eday==10){?>selected<?}?>>10</option>
<option value="11" <?if($eday==11){?>selected<?}?>>11</option>
<option value="12" <?if($eday==12){?>selected<?}?>>12</option>
<option value="13" <?if($eday==13){?>selected<?}?>>13</option>
<option value="14" <?if($eday==14){?>selected<?}?>>14</option>
<option value="15" <?if($eday==15){?>selected<?}?>>15</option>
<option value="16" <?if($eday==16){?>selected<?}?>>16</option>
<option value="17" <?if($eday==17){?>selected<?}?>>17</option>
<option value="18" <?if($eday==18){?>selected<?}?>>18</option>
<option value="19" <?if($eday==19){?>selected<?}?>>19</option>
<option value="20" <?if($eday==20){?>selected<?}?>>20</option>
<option value="21" <?if($eday==21){?>selected<?}?>>21</option>
<option value="22" <?if($eday==22){?>selected<?}?>>22</option>
<option value="23" <?if($eday==23){?>selected<?}?>>23</option>
<option value="24" <?if($eday==24){?>selected<?}?>>24</option>
<option value="25" <?if($eday==25){?>selected<?}?>>25</option>
<option value="26" <?if($eday==26){?>selected<?}?>>26</option>
<option value="27" <?if($eday==27){?>selected<?}?>>27</option>
<option value="28" <?if($eday==28){?>selected<?}?>>28</option>
<option value="29" <?if($eday==29){?>selected<?}?>>29</option>
<option value="30" <?if($eday==30){?>selected<?}?>>30</option>
<option value="31" <?if($eday==31){?>selected<?}?>>31</option>

</select>
<select name="emonth">
<option value="1" <?if($emonth==1){?>selected<?}?>>января (1)</option>
<option value="2" <?if($emonth==2){?>selected<?}?>>февраля (2)</option>
<option value="3" <?if($emonth==3){?>selected<?}?>>марта (3)</option>
<option value="4" <?if($emonth==4){?>selected<?}?>>апреля (4)</option>
<option value="5" <?if($emonth==5){?>selected<?}?>>мая (5)</option>
<option value="6" <?if($emonth==6){?>selected<?}?>>июня (6)</option>
<option value="7" <?if($emonth==7){?>selected<?}?>>июля (7)</option>
<option value="8" <?if($emonth==8){?>selected<?}?>>августа (8)</option>
<option value="9" <?if($emonth==9){?>selected<?}?>>сентября (9)</option>
<option value="10" <?if($emonth==10){?>selected<?}?>>октября (10)</option>
<option value="11" <?if($emonth==11){?>selected<?}?>>ноября (11)</option>
<option value="12" <?if($emonth==12){?>selected<?}?>>декабря (12)</option>


</select>
<select name="eyear">
<option value="2003" <?if($eyear==2003){?>selected<?}?>>2003</option>
<option value="2004" <?if($eyear==2004){?>selected<?}?>>2004</option>
<option value="2005" <?if($eyear==2005){?>selected<?}?>>2005</option>
<option value="2006" <?if($eyear==2006){?>selected<?}?>>2006</option>
<option value="2007" <?if($eyear==2007){?>selected<?}?>>2007</option>
</select>
<input name="start" type="submit" value="Просмотр">
</form>
