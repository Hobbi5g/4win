<?           
	global $conn;
	global $tpl;


	include_once('routines.php'); //AddPostToTopic($poster, $topicid, $message)



	if ($deletecomments) {
		if (isset($move)) {
			foreach($move as $id) {
				$comment = $conn->getRow("SELECT * FROM shortreports WHERE reportid='$id'");
				$topicid = $conn->getOne("SELECT forumtopic FROM games WHERE gameid='{$comment['gameid']}'");
				AddPostToTopic($comment['username'], $topicid, $comment['report'] );
				$conn->query("DELETE FROM shortreports WHERE reportid='$id'");
			}
		}

    	if(isset($del)) {
			foreach($del as $id) {
			$conn->query("DELETE FROM shortreports WHERE reportid='$id' ");
			}
		} 
	}

	$sSQL = "SELECT shortreports.*,  games.title, games.forumtopic ".
			"FROM shortreports ".
			"LEFT JOIN games ON games.gameid = shortreports.gameid ";

	if ($order == 'title' || $order == '') {
		$sSQL .= "ORDER BY games.title, shortreports.reportid ";
		$tpl->assign('order', 'title');
	}
	else {
		$sSQL .= "ORDER BY games.gameid, shortreports.reportid ";
		$tpl->assign('order', 'gameid');
	}


	$comments = $conn->getAll($sSQL);
	$tpl->assign('comments', $comments);


	$tpl->display("comments.tpl");
	//echo $tpl->fetch("comments.tpl");
