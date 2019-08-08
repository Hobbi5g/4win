<?


/*
 * ������� ����� ���� �� ������
 *
 */
function CreateTopic($poster, $subject, $message)
{
	global $conn;


	if ($message == '') die("Empty forum post");

	$now = time();

	$posterid = 1;
	if ($poster == 'admin')
		$posterid = 2;

	if ($poster == '')
		$poster = 'Guest';

	$postid = $conn->getOne("SELECT MAX(id)+1 FROM forum_posts");
	$topicid = $conn->getOne("SELECT MAX(id)+1 FROM forum_topics");
	$numposts = $conn->getOne("SELECT num_posts+1 FROM forum_users WHERE id='$posterid'"); // 2-admin 1-guest
	$postsonforum = $conn->getOne("SELECT num_posts+1 FROM forum_forums WHERE id='1' "); // 1-Games Discussion
	$topicsonforum = $conn->getOne("SELECT num_topics+1 FROM forum_forums WHERE id='1' "); // 1-Games Discussion

	$message = addslashes($message);		
	$poster = addslashes($poster);		
	$subject = addslashes($subject);		

	// ��������� ����
	$sSQL = "INSERT INTO forum_posts (id, poster, poster_id, poster_ip, poster_email, message, hide_smilies, posted, topic_id) VALUES ('$postid', '$poster', '$posterid', '127.0.0.1', 'forum@games4win.com', '$message', 0, '$now', '$topicid'  ) ";
	$conn->query($sSQL);
	//echo $sSQL."<br>";

	// ��������� ����
	$sSQL = "INSERT INTO forum_topics (id, poster, subject, posted, last_post, last_post_id, last_poster, forum_id) VALUES ('$topicid', '$poster', '$subject', '$now', '$now', '$postid', '$poster', '1' )";
	$conn->query($sSQL);
	//echo $sSQL."<br>";

	// ����������� ������� ���������� ������ ���������
	$sSQL = "UPDATE forum_users SET num_posts='$numposts', last_post='$now' WHERE id='$posterid' ";
	$conn->query($sSQL);
	//echo $sSQL."<br>";
		         
	// ����������� ������� ������ � ����� ������
	$sSQL = "UPDATE forum_forums SET num_posts='$postsonforum', num_topics='$topicsonforum', last_post='$now', last_post_id='$postid', last_poster='$poster' WHERE id='1' ";
	$conn->query($sSQL);
	//echo $sSQL."<br>";

	return $topicid; // ���������� id ��������� ����
}



/*
 * ��������� ��������� � ���� �� ������
 *
 */
function AddPostToTopic($poster, $topicid, $message)
{
	global $conn;


	if ($message == '') die("Empty forum post");

	$now = time();

	$posterid = 1;
	if ($poster == 'admin')
		$posterid = 2;
	
	if ($poster == '')
		$poster = 'Guest';

	$postid = $conn->getOne("SELECT MAX(id)+1 FROM forum_posts");
	//$topicid = $conn->getOne("SELECT MAX(id)+1 FROM forum_topics");
	$numposts = $conn->getOne("SELECT num_posts+1 FROM forum_users WHERE id='$posterid'"); // 2-admin 1-guest
	$postsonforum = $conn->getOne("SELECT num_posts+1 FROM forum_forums WHERE id='1' "); // 1-Games Discussion
	$topicsonforum = $conn->getOne("SELECT num_topics+1 FROM forum_forums WHERE id='1' "); // 1-Games Discussion

	$message = addslashes($message);
	$poster = addslashes($poster);		

	// ��������� ����
	$sSQL = "INSERT INTO forum_posts (id, poster, poster_id, poster_ip, poster_email, message, hide_smilies, posted, topic_id) VALUES ('$postid', '$poster', '$posterid', '127.0.0.1', 'forum@games4win.com', '$message', 0, '$now', '$topicid'  ) ";
	$conn->query($sSQL);

	// ����������� ������� ���������� �����
	$views = $conn->getOne("SELECT num_views+1 FROM forum_topics WHERE id='$topicid'");
	$replies = $conn->getOne("SELECT num_replies+1 FROM forum_topics WHERE id='$topicid'");
	$sSQL = "UPDATE forum_topics SET last_post='$now', last_post_id='$postid', last_poster='$poster', num_views='$views', num_replies='$replies' WHERE id='$topicid'";
	$conn->query($sSQL);

	// ����������� ������� ���������� ������ ���������
	$sSQL = "UPDATE forum_users SET num_posts='$numposts', last_post='$now' WHERE id='$posterid' ";
	$conn->query($sSQL);

	//$sSQL = "UPDATE forum_topics SET last_post='$now', last_post_id='$postid', last_poster='$poster'"		       
  
	// ����������� ������� ������� � �����
	$sSQL = "UPDATE forum_forums SET num_posts='$postsonforum', last_post='$now', last_post_id='$postid', last_poster='$poster' WHERE id='1' ";
	$conn->query($sSQL);

	return $topicid; // ���������� id ��������� ����
}


// ���������� stringid �� �������� ����
function GenerateStringId($title)
{
	//$title = $g['GameTitle'];
	$stringid = str_replace(" ","-",strtolower($title));
	$stringid = str_replace("'",'', $stringid);
	$stringid = str_replace(":",'', $stringid);
	$stringid = str_replace(".",'', $stringid);
	$stringid = str_replace("!",'', $stringid);
	$stringid = str_replace("(",'', $stringid);
	$stringid = str_replace(")",'', $stringid);
	$stringid = str_replace("*",'', $stringid);
	$stringid = str_replace("&",'and', $stringid);

	return $stringid;
}



	// ��� �����������
	class Dummy {}



	include ("typografica/classes/typografica.php");
	include ("typografica/classes/paragrafica.php");
	include_once ("../../app/class-markdown.php");
	$dummy = new Dummy();
	$typo = new typografica( $dummy );
	$para = new paragrafica( $dummy );



	function processwiki($data)
	{
		
		$data = explode("\n", $data);
		$pstart = false;
		$ulstart = false;
		$res = '';
		        
		//print_r($data);

		for($i = 0, $countdata = count($data); $i < $countdata; $i++)
		{
			$data[$i] = rtrim($data[$i]);

			if (substr($data[$i], 0, 3) == ' * ')
			{
	 			if (!$pstart) // ���� ����� �� �����
				{
					//$res .= '<font color="red">'.'N1-'.$ulstart.'-'.$pstart.'</font>';
					$res .= '<p>';
					$pstart = true;
				}

				if (!$ulstart) // ���� ������ �� �����
				{
					$res .= '<ul>';
					//$res .= '<font color="red">'.'N2-'.$ulstart.'-'.$pstart.'</font>';
					$ulstart = true;
				}

				$res .=  '<li>'.substr($data[$i], 3, strlen($data[$i])).'</li>';
			} else
			{
				if ($ulstart) // ���� ��� �� ������� ������, �� ������� ������ ���� ���������
				{
					$res .= '</ul>'; 
					//$res .= '<font color="red">'.'N3-'.$ulstart.'-'.$pstart.'</font>';
					$ulstart = false;
				}

				if (empty($data[$i]) && $pstart)
				{
					$res .= '</p>';
					//$res .= '<font color="red">'.'N4-'.$ulstart.'-'.$pstart.'</font>';
					$pstart = false;
				}

				if (!empty($data[$i]))
				{
					if (!$pstart)
					{
						$res .= '<p>';
						//$res .= '<font color="red">'.'N5-'.$ulstart.'-'.$pstart.'</font>';
						$pstart = true;
					} 

					// ���� ������ �� ����� � ��� �� �����
					// � ���������� ���� ����� ��, �� ��� - ������
					if ($i >=1 && !empty($data[$i-1]) && substr($data[$i-1], 0, 3) != ' * ')
						$res .= '<br>';
				}


				$res .= $data[$i];
			}

			if ($i == $countdata-1)
			{                   
				//echo "-----".$countdata;
				if ($ulstart)
				{
					$res .= '</ul>';
					$ulstart = false;
				}
				if ($pstart)
				{
					$res .= '</p>';
					$pstart = false;
				}
			}
		}

		return $res;
	}


