<?

	global $conn;
	
	$games = $conn->getAll("SELECT g.gameid, g.title,t.productid, t.triallink,g.download1, t.buylink FROM games AS g INNER JOIN trymediafeed AS t ON t.title = g.title WHERE download1 LIKE \"%arcade.reflexive.com%\" OR download1 LIKE \"%download.gamecentersolution.com%\" OR download2 LIKE \"%arcade.reflexive.com%\" OR download2 LIKE \"%download.gamecentersolution.com%\" ");	
	
	
	foreach($games as $game)
		{
			//print_r($game);
			?>
			
			UPDATE games SET download1="<?=$game['triallink'] ?>", orderpage="<?=$game['buylink'] ?>" WHERE gameid=<?=$game['gameid'];?>;<br />
			
			<?
		}
	
?>
RAND:<?=rand(10000,99999);?>

