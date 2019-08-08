<?


class TCustomImage
{
	var $conn;
	
	function TCustomImage()
	{
		global $conn; 
		if (isset($conn))
		{
			$this->conn = $conn;
			$this->conn->setFetchMode(DB_FETCHMODE_ASSOC);
		}

	}


	//
	function GetGameEditorRate($game,$is_name=false)
	{
	    $gameid = $game;
		$sSQL = " SELECT DISTINCT games.gameid, games.stringid, games.rating, rates.gameid, rates.playability, rates.graphics, rates.sounds, rates.quality, rates.idea, rates.awards".
				" FROM games,rates".
				" WHERE".
				" ((games.gameid = rates.gameid)".
				" AND (games.stringid = ?))".
				"";

		$game = $this->conn->getRow($sSQL, array($gameid));
		return $game;
	}



	function SaveLogoImage($stringid)
	{          
			//@setlocale(LC_ALL, 'ru_RU.WIN_CP_1251');
			//Header ("Content-type: image/gif");
			//Header ("Content-type: image/jpeg");
			
			$sSQL = "SELECT logo FROM games WHERE stringid='$stringid'";
			$name = $this->conn->getOne($sSQL);

			$name = '../logos/' . $name;

			//$im = @imagecreate (100, 100) ;
			$im = imagecreatefrompng($name);
			if (!empty($im))
			{
				$sx = imagesx($im);
				$sy = imagesy($im);

				$white = imagecolorallocate($im, 255, 255, 255);
				$black = imagecolorallocate($im, 0, 0, 0);
				$red = imagecolorallocate($im, 255, 0, 0);
				imagerectangle($im, 0, 0, $sx-1, $sy-1, $black);
			
				//imagefilledrectangle($im,38,75,99,89,$red);
				//imagestring($im, 2, 42, 75, 'games4win', $white);
				if (empty($stringid))
					imagestring($im, 2, 2, 75, 'StringID is empty', $red);
				//imagestring($im, 2, 2, 85, $name, $red);

				$corner = imagecreatefrompng("../images/logo_corner.png");
				imagecopy($im, $corner, 92, 0, 0, 0, 8, 8);

				imagejpeg($im, '../img/'.$stringid.'.jpg', 80);
				//imagegif($im);
				imagedestroy($im);
				//exit();
			}
	}


	// ������� ���������� �� ����
	function SaveRatingImage($game)
	{
			//@setlocale(LC_ALL, 'ru_RU.WIN_CP_1251');

			//$gamerates = TGameInfo::GetGameEditorRate($game);
			$gamerates = $this->GetGameEditorRate($game);

			//Header ("Content-type: image/png");
			$im = @imagecreate(350, 70) or die("Cannot Initialize new GD image stream");

			$white = imagecolorallocate($im, 255, 255, 255);
			$black = imagecolorallocate($im, 0, 0, 0);
			$gray = imagecolorallocate($im, 200, 200, 200);
			$red = imagecolorallocate($im, 255, 0, 0);
			$orange = imagecolorallocate($im, 255, 160, 50);

			$dy = 5;
			$value = 'Playability';
			imagestring($im, 2, 75 - strlen($value) * imagefontwidth(2), $dy, $value, $black);
			$dy += 12;
			$value = 'Graphics';
			imagestring($im, 2, 75 - strlen($value) * imagefontwidth(2), $dy, $value, $black);
			$dy += 12;
			$value = 'Sound';
			imagestring($im, 2, 75 - strlen($value) * imagefontwidth(2), $dy, $value, $black);
			$dy += 12;
			$value = 'Quality';
			imagestring($im, 2, 75 - strlen($value) * imagefontwidth(2), $dy, $value, $black);
			$dy += 12;
			$value = 'Idea';
			imagestring($im, 2, 75 - strlen($value) * imagefontwidth(2), $dy, $value, $black);
			$dy += 12;

			$dy = 10;

			for ( $i = 0; $i < 5; $i++ )
			{
					imagerectangle($im, 80, $dy, 147, $dy + 5, $gray);
					imagerectangle($im, 80 + 2, $dy + 2, 145, $dy + 3, $gray);

					switch ( $i )
						{
						case 0:
							$n = $gamerates['playability'];
							break;

						case 1:
							$n = $gamerates['graphics'];
							break;

						case 2:
							$n = $gamerates['sounds'];
							break;

						case 3:
							$n = $gamerates['quality'];
							break;

						case 4:
							$n = $gamerates['idea'];
							break;
						}

					switch ( $n )
						{
						case 1:
							imagerectangle($im, 80 + 2, $dy + 2, 83, $dy + 3, $red);
							break;

						case 2:
							imagerectangle($im, 80 + 2, $dy + 2, 95, $dy + 3, $red);
							break;

						case 3:
							imagerectangle($im, 80 + 2, $dy + 2, 108, $dy + 3, $red);
							break;

						case 4:
							imagerectangle($im, 80 + 2, $dy + 2, 120, $dy + 3, $red);
							break;

						case 5:
							imagerectangle($im, 80 + 2, $dy + 2, 133, $dy + 3, $red);
							break;

						case 6:
							imagerectangle($im, 80 + 2, $dy + 2, 145, $dy + 3, $red);
							break;
						}

					$dy += 12;
			}

			$rated = $gamerates['rating']; //$rated = 0; ///////////////////////////////////

			if ( $rated >= 6 && $rated <= 10 )
			{
					$value = 'Overall';
					imagestring($im, 2, 160, 10, $value, $black);
					$value = 'rating';
					imagestring($im, 2, 164, 20, $value, $black);

					switch ( $rated )
						{
						case 6:
							$ratedsign = imagecreatefrompng("../images/awards/label06.png");

							break;

						case 7:
							$ratedsign = imagecreatefrompng("../images/awards/label07.png");

							break;

						case 8:
							$ratedsign = imagecreatefrompng("../images/awards/label08.png");

							break;

						case 9:
							$ratedsign = imagecreatefrompng("../images/awards/label09.png");

							break;

						case 10:
							$ratedsign = imagecreatefrompng("../images/awards/label10.png");

							break;
						}

					imagecopy($im, $ratedsign, 169, 35, 0, 0, 24, 24);
			} //////////////////////////////////////////////////////////////////////////////

			/*
				   $value = 'Games4Win';
				   imagestring($im, 2, 210, 10, $value, $black);
				   $value = 'award';
				   imagestring($im, 2, 223, 20, $value, $black);
			
				$award = 8;
				switch($award)
				{
					case 6:	$ratedsign = imagecreatefrompng("images/awards/label06.png");
							break;
					case 7:	$ratedsign = imagecreatefrompng("images/awards/label07.png");
							break;
					case 8:	$ratedsign = imagecreatefrompng("images/awards/label08.png");
							break;
					case 9:	$ratedsign = imagecreatefrompng("images/awards/label09.png");
							break;
					case 10:$ratedsign = imagecreatefrompng("images/awards/label10.png");
							break;
			
				}
				imagecopy($im, $ratedsign, 226,35, 0, 0, 24, 24);
			*/
			imagepng ($im, '../img/'.$gamerates['stringid'].'-rating.png');
			imagedestroy ($im);
			//exit();
	}
}
