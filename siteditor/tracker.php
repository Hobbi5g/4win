<?

function tracker_add($action,$actor)
{	

	global $conn;
	$conn->query("INSERT INTO tracking_manager SET action_desc='$action', actor_name='$actor'");

}

