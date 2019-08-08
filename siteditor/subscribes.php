<?

	global $conn;

	require('./phpmailer/class.phpmailer.php');


	// quick and dirty sql-injection protection
	if (!empty($id) && !is_numeric($id))
		die("fuck off, script kiddie");



	if (!empty($action))
	{
		switch($action)
		{
			case 'on':
				$sSQL = "UPDATE emails SET isactive='Y' WHERE emailid='{$id}'";
				$conn->query($sSQL);
				break;

			case 'off':
				$sSQL = "UPDATE emails SET isactive='N' WHERE emailid='{$id}'";
				$conn->query($sSQL);
				break;

			// переносим адреса для рассылки
			case 'addrecs':
				$sSQL = "SELECT COUNT(*) c FROM mailrecipients WHERE emailid='{$id}'";
				if($conn->getOne($sSQL) == 0)
				{
					$sSQL = "".
							"INSERT INTO mailrecipients (recname, recemail, emailid) ".
							"SELECT author, email, {$id} a FROM members ".
							"WHERE wantads='Y'".
							"";
					$conn->query($sSQL);

					$sSQL = "SELECT COUNT(*) c FROM mailrecipients WHERE emailid='{$id}'";
					$c = $conn->getOne($sSQL);

					$conn->query("UPDATE emails SET usersadded='{$c}' WHERE emailid='{$id}' ");

				}

				break;

			// удаляем адреса для рассылки
			case 'delrecs':
				$sSQL = "DELETE FROM mailrecipients WHERE emailid='{$id}'";
				$conn->query($sSQL);

				$conn->query("UPDATE emails SET usersadded='0' WHERE emailid='{$id}' ");
				break;

		   	// отправим немного почты
			case 'sendsome':
				$mails = array();
				$sSQL = "".
						"SELECT recid, recname, recemail, emailsubject, emailtext, mailrecipients.emailid FROM mailrecipients ".
						"LEFT JOIN emails ON mailrecipients.emailid = emails.emailid ".
						"WHERE emails.isactive='Y' ".
						"LIMIT 0, 100".
						"";
				$data = $conn->getAll($sSQL);

				$mailer = new phpmailer;

				for($i=0; $i<count($data); $i++)
				{
					if (empty($data['emailid']['emailsubject']))
					{
						// приготовим письмо
						$data['emailid'] = array();
						$data['emailid']['emailsubject'] = $data[$i]['emailsubject'];
						$data['emailid']['emailtext'] = 
							$data[$i]['emailtext'].
							//"\n\n".
							//"You are receiving this e-mail because you subscribed to Games4Win mailing list. If you wish to unsubscribe please click on the following link:\n".
							//" http://www.games4win.com/unsubscribe.php?code=1891136726&email=reggie12@gmail.com".
							"";
					}


					$mailer->IsSMTP();                    // send via SMTP
					$mailer->Host     = '198.63.211.205'; // SMTP servers 'smtp1.site.com;smtp2.site.com'
					$mailer->SMTPAuth = true;     // turn on SMTP authentication
					$mailer->Username = 'games4win@games4win';  // SMTP username
					$mailer->Password = 'games4winpwd'; // SMTP password


					$mailer->From     = 'support@games4win.com';
					$mailer->FromName = 'Games4Win';
					$mailer->WordWrap = 75;

					$mailer->AddAddress($data[$i]['recemail'], $data[$i]['recname']);
					$mailer->Subject = $data['emailid']['emailsubject'];
					$mailer->Body    = $data['emailid']['emailtext'];
					//$mailer->AddAttachment("c:/temp/11-10-00.zip", "new_name.zip");  // optional name

					if(!$mailer->Send())
					{
						echo "There was an error sending the message - ".$data[$i]['recname'].' &lt;'.$data[$i]['recemail'].'&gt; - '.$mailer->ErrorInfo.'<br>';
						//exit;
					}

					// удаляем id из списка, на него отправили
					// нельзя удалять по емейлу, он может быть и в другой рассылке
					$id = $data[$i]['recid']; 
					$conn->query("DELETE FROM mailrecipients WHERE recid='$id'");

					$mailer->ClearAddresses();

					//print_r($data);
				}

				break;
		}
	}

	if (!empty($addemail))
	{

		$emailsubject = addslashes($emailsubject);
		$emailtext = addslashes($emailtext);

		$sSQL = "";
		if ($editmode)
		{
			$sSQL = "UPDATE emails ".
					"SET ".
					"emailsubject='{$emailsubject}',".
					"emailtext='{$emailtext}'".
					"WHERE emailid='{$emailid}'".
					"";         
		}
		else
		{
			$sSQL = "INSERT INTO emails ".
					"SET ".
					"emailsubject='{$emailsubject}',".
					"emailtext='{$emailtext}'".
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
				$sSQL = "SELECT * FROM emails WHERE emailid='{$id}'";
				$email = $conn->getRow($sSQL); 
				break;
		}

	}
	else
	{
		$sSQL = "SELECT * FROM emails";
		$emails = $conn->getAll($sSQL);

		for($i=0; $i<count($emails); $i++)
		{
			$id = $emails[$i]['emailid'];
			$emails[$i]['numof'] = $conn->getOne("SELECT COUNT(*) FROM mailrecipients WHERE emailid='{$id}'");
		}

	}



?>

<table cellspacing="1" cellpadding="4" class="frm4" width="100%">
<tr>
<td>
	<b>Actions:</b> <a href="./?report=subscribes&mode=add">Add new email</a>
</table>

<br><br>

<? if (!($mode=='add' || $mode=='edit')) {?>
<form action="./?report=subscribes&action=sendsome" method="POST" name="frm">
<input type="submit" value="Send some mails">
</form>
<br>
<?}?>

<?	if (!empty($mode) && ($mode == 'add' || $mode == 'edit') ) { ?>

<form action="./?report=subscribes&mode=edit&id=<?=$email['emailid']?>" method="POST" name="frm">

<?if(!empty($email)){?>
<b>Edit Email:</b>
<input type="hidden" name="editmode" value="true">
<input type="hidden" name="emailid" value="<?=$email['emailid']?>">
<?}else{?>
<b>Add Email:</b>
<?}?>


<table cellspacing="1" cellpadding="4" class="frm4" width="100%">
<tr><td>Subject:<td><input type="textbox" size="40" class="inp" name="emailsubject" <?if(!empty($email)){?>value="<?=$email['emailsubject']?>"<?}?> >
<tr><td>Description:<td><textarea name="emailtext" id="pstinp" class="inp" cols="40" rows="30" style="wrap:virtual;width:100%;height:400px"><?=htmlspecialchars($email['emailtext']);?><?//=$email['emailtext']?></textarea>
<tr><td><td><input type="submit" name="addemail" value="<?if(!empty($email)){?>Save email<?}else{?>Add email<?}?>">
</table>

</form>
<? } else {?>

<b>E-mails:</b>
<table cellspacing="1" cellpadding="4" class="frm4" width="100%">
<tr>
<td>
	<b>N</b>
<td>
	<b>Time</b>
<td>
	<b>Subject</b>
<td>
	<b>Recipients</b>
<td>
	<b>State</b>
<td>
	<b>Edit</b>
<?
	for($i=0; $i<count($emails); $i++)
	{
?>
<tr><td><?=$emails[$i]['emailid']?>
	<td><?=$emails[$i]['emailtime']?>
	<td><?if($emails[$i]['isactive']=='N'){?><font color="#b0b0b0"><?=$emails[$i]['emailsubject']?></font><?}else{?><?=$emails[$i]['emailsubject']?><?}?>
	<td><?=$emails[$i]['numof']?> not send, <?=$emails[$i]['usersadded']?> added <?if($emails[$i]['numof']==0){?>[<a href="./?report=subscribes&action=addrecs&id=<?=$emails[$i]['emailid']?>">Add recipients</a>]<?}else{?>[<a href="./?report=subscribes&action=delrecs&id=<?=$emails[$i]['emailid']?>">Delete recipients</a>]<?}?>
	<td><?if($emails[$i]['isactive']=='N'){?>Hidden [<a href="./?report=subscribes&action=on&id=<?=$emails[$i]['emailid']?>">switch on</a>]<?}else{?><b>Active</b> [<a href="./?report=subscribes&action=off&id=<?=$emails[$i]['emailid']?>">switch off</a>]<?}?>
	<td><a href="./?report=subscribes&mode=edit&id=<?=$emails[$i]['emailid']?>">Edit</a>

<?	} ?>

<? } // mode?>
</table>
