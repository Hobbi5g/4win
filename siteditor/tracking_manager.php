<?
	global $conn;

	$data = array();
	$sSQL = "SELECT UNIX_TIMESTAMP(action_time) at, action_desc, actor_name FROM tracking_manager ORDER BY action_time DESC";
	$data = $conn->getAll($sSQL);
	//print_r($data);
	echo count($data);
	                              

?>
<h1>Manager tracking</h1><br>

    <table cellspacing="1" cellpadding="4" class="frm4">
        <tr>
            <td bgcolor=#eeeeee>
                <b>Time</b>
            <td bgcolor=#eeeeee>
                <b>Actor</b>
            <td bgcolor=#eeeeee>
                <b>Action</b>

<?

//	for($i=0; $i<count($data); $i++)
	for($i=0; $i<1500; $i++)
	{
	?>

        <tr>
            <td>
				<?=date("d M Y h:i:s A", $data[$i]['at']); ?>
            <td>
				<b><?=$data[$i]['actor_name']?></b>
            <td>
				<?=$data[$i]['action_desc']?>


	<?

	}

?>
	</table>