<?

//    include ($s_addurl."login.php");

	global $conn;
//	include_once('routines.php');

	if (!empty($adddeveloper) && !empty($stringid) && !empty($companyname) )
	{

		$stringid = addslashes($stringid);
		$companyname = addslashes($companyname);
		$companyurl = addslashes($companyurl);
		$description_wiki = addslashes($description);
		$description = $typo->correct(processwiki($description));
		$description = addslashes($description);

		$sSQL = "";
		if ($editmode)
		{
			$sSQL = "UPDATE companies ".
					"SET ".
					"stringid='{$stringid}',".
					"companyname='{$companyname}',".
					(!empty($companyurl)?"companyurl='{$companyurl}',":"companyurl IS NULL,").
					"description='{$description}',".
					"description_wiki='{$description_wiki}'".
					"WHERE companyid='{$companyid}'".
					"";         
		}
		else
		{
			$sSQL = "INSERT INTO companies ".
					"SET ".
					"stringid='{$stringid}',".
					"companyname='{$companyname}',".
					(!empty($companyurl)?"companyurl='{$companyurl}',":"companyurl IS NULL,").
					"description='{$description}',".
					"description_wiki='{$description_wiki}'".
					"";         
		}

		$conn->query($sSQL);
	}

	if (!empty($mode))
	{
		switch ($mode)
		{
			case 'add': 
				break;
			case 'edit':
				$sSQL = "SELECT * FROM companies WHERE companyid='{$id}'";
				$company = $conn->getRow($sSQL); 
				break;
		}

	}
	else
	{
		$abc = $conn->getAll("SELECT DISTINCT substring(companyname,1,1) abc FROM companies ORDER BY abc");

		$companies = $conn->getAll("SELECT * FROM companies".(empty($filter)?"":" WHERE substring(companyname,1,1)='$filter'")  );
	}

?>


<?	if (!empty($mode) && ($mode == 'add' || $mode == 'edit') ) { ?>


<form action="./?report=developers<?if(!empty($filter)){?>&filter=<?=$filter?><?}?>" method="POST" name="frm">
<?if(!empty($company)){?>
<b>Edit developer:</b>
<input type="hidden" name="editmode" value="true">
<input type="hidden" name="companyid" value="<?=$company['companyid']?>">
<?}else{?>
<b>Add record:</b>
<?}?>

<table cellspacing="1" cellpadding="4" class="frm4" width="700">
<tr><td>String&nbsp;ID:<td><input type="textbox" size="40" class="inp" name="stringid" <?if(!empty($company)){?>value="<?=$company['stringid']?>"<?}?> >
<tr><td>Name:<td><input type="textbox" size="40" class="inp" name="companyname" <?if(!empty($company)){?>value="<?=$company['companyname']?>"<?}?> >
<tr><td>URL:<td><input type="textbox" size="40" class="inp" name="companyurl" <?if(!empty($company)){?>value="<?=$company['companyurl']?>"<?}?>>
<tr><td>Description:<td><textarea name="description" id="pstinp" class="inp" cols="40" rows="30" style="wrap:virtual;width:100%;height:200px"><?=htmlspecialchars($company['description_wiki']);?></textarea>
<tr><td>Description:<td><??>
<tr><td><td><input type="submit" name="adddeveloper" value="<?if(!empty($company)){?>Edit developer<?}else{?>Add developer<?}?>">&nbsp;<input type="submit" name="cancel" value="Cancel">
</table>

</form>



<? } else { ?>
<b>Add:</b>
<table cellspacing="1" cellpadding="4" class="frm4" width="100%">
<tr><td>
<a href="./?report=developers&mode=add<?if(!empty($filter)){?>&filter=<?=$filter?><?}?>">Add developer</a>
</table>


<br><br>


<b>Sort:</b>
<table cellspacing="1" cellpadding="4" class="frm4" width="100%">
<tr>
<td>
<?
$c = count($abc)-1;
for($i=0; $i<=$c; $i++){?>
<a href="./?report=developers&filter=<?=$abc[$i]['abc']?>"><?if(!empty($filter) && $filter==$abc[$i]['abc']){?><b><?=$abc[$i]['abc']?></b><?}else{?><?=$abc[$i]['abc']?><?}?></a><?if($i!=$c){?> | <?}?>
<?}?>
</table>

<br><br>

<b>Developers:</b>


<table cellspacing="1" cellpadding="4" class="frm4" width="100%">
<tr>
<td>
	<b>N</b>
<td>
	<b>String ID</b>
<td>
	<b>Name</b>
<td>
	<b>URL</b>
<?
	for($i=0; $i<count($companies); $i++)
	{
?>
<tr><td><?=$companies[$i]['companyid']?>
	<td><?=$companies[$i]['stringid']?>
	<td><?if(empty($companies[$i]['companyurl'])){?><?=$companies[$i]['companyname']?><?}else{?><a href="<?=$companies[$i]['companyurl']?>" target=_blank><?=$companies[$i]['companyname']?></a><?}?>
	<td><a href="./?report=developers&mode=edit&id=<?=$companies[$i]['companyid']?><?if(!empty($filter)){?>&filter=<?=$filter?><?}?>">Edit</a>

<?
	}
?>
</table>
<? } // mode ?>