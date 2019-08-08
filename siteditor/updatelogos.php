<?

    require_once 'tcustomimage.php';

	global $conn;


	function update_screenshot_thumbs() {

		global $conn;
		$screenshots = $conn->getAll("SELECT s.* FROM screenshots s JOIN games g ON g.gameid = s.game_id WHERE g.hidden = 'N'");

		foreach ($screenshots as $screenshot) {

			// load g4w transparent logo
			$logo = imagecreatefrompng('../images/g4wtrans.png');
			$logosx = imagesx($logo);
			$logosy = imagesy($logo);

			// get screenshot 1
			$filename = '../up/original/' . $screenshot['name'];


			//if (!file_exists($filename)) {
				// если нет jpg, то попытаемся прочитать png
				//$name = basename($screenshot['name'], '.jpg') . '.png';
				//$screenshot['name'] = $name;
				//$filename = '../up/original/' . $name;
				//if (file_exists($filename)) {
				//	echo $name.'<br>';
					//$conn->query("UPDATE screenshots SET name = ? WHERE id = ?", array($name, $screenshot['id']));
				//}
			//}


			if (file_exists('../up/original/' . $screenshot['name']) && !file_exists('../up/' . $screenshot['name'])) {
				echo $screenshot['name'] . '<br>';


				if (Helpers::endsWith($filename, '.jpg')) {
					$im = imagecreatefromjpeg($filename);
					$extention = '.jpg';
				} else {
					$im = imagecreatefrompng($filename);
					$extention = '.png';
				}

				if ($im) {
					$sx = (int)imagesx($im);
					$sy = (int)imagesy($im);
					$sx2 = (int)imagesx($im) / 2;
					$sy2 = (int)imagesy($im) / 2;

					//640x480

					$im2 = imagecreatetruecolor(640, 480);
					imagecopyresampled($im2, $im, 0, 0, 0, 0, 640, 480, $sx, $sy);
					imagecopy($im2, $logo, 640 - $logosx, 480 - $logosy, 0, 0, $logosx, $logosy); // add logotype to image
					if ($extention == '.jpg') {
						$filename2 = '../up/' . basename($screenshot['name'], $extention) . '.jpg';
						imagejpeg($im2, $filename2, 90);
					} else {
						$filename2 = '../up/' . basename($screenshot['name'], $extention) . '.png';
						imagepng($im2, $filename2);
					}

					//640x480 to thumb
					$destW = 180;
					$destH = (int)$destW / 1.333333;
					$filename3 = '../up/thumbs/' . basename($screenshot['name'], $extention) . '.jpg';
					$im3 = imagecreatetruecolor($destW, $destH);

					imagecopyresampled($im3, $im2, 0, 0, 0, 0, $destW, $destH, 640, 480);
					imagejpeg($im3, $filename3, 90);

					echo 'done: ' . $filename . '<br/><br/>';
					flush();

				} else {
					echo 'FAIL: ' . $filename . '<br/><br/>';
					flush();
					//print_r($screenshot);
				}

			}

		}

	}

    // resize and save different size screenshots
    function update_onpage_screenshots() {
		$image_files = glob('../screenshots/original/*.png');
		foreach ($image_files as $image_filename) {
		    if (!file_exists($image_filename)) { continue; }

		    $image_pathinfo = pathinfo($image_filename);
		    $new_filename_small = '../screenshots/' . $image_pathinfo['filename'] . '.small.jpg';
			$new_filename_medium = '../screenshots/' . $image_pathinfo['filename'] . '.medium.jpg';
			$new_filename_big = '../screenshots/' . $image_pathinfo['filename'] . '.jpg';
		    //echo $new_filename . PHP_EOL.'<br>';

            if (!file_exists($new_filename_small)) {
                $center = new \stojg\crop\CropEntropy($image_filename);
                $croppedImage = $center->resizeAndCrop(300, 225);
                $croppedImage->writeimage($new_filename_small);
            }

			if (!file_exists($new_filename_medium)) {
				$center = new \stojg\crop\CropEntropy($image_filename);
				$croppedImage = $center->resizeAndCrop(460, 345);
				$croppedImage->writeimage($new_filename_medium);
			}

			if (!file_exists($new_filename_big)) {
				$image = new Imagick($image_filename);
				$image->writeImage($new_filename_big);
			}
		}


		//print_r($matches);
    }


    $action = $_POST['action'];
	if ($action == 'update') {
		/*
		$sSQL = "SELECT stringid FROM games WHERE logo <> '' AND hidden = 'N'";
		$games = $conn->getCol($sSQL);

		$tci = new TCustomImage;
		foreach($games as $game)
		{
			//echo $game.'<br/>';
			//$tci->SaveLogoImage($game);
			//$tci->SaveRatingImage($game);
		}
		*/

        update_onpage_screenshots();
		update_screenshot_thumbs();
	}

/*
	$scrs = $conn->getAll("select gameid, stringid, screenshot, scr01, scr02, scr03, scr01src, scr02src, scr03src from games where hidden='N'");
	foreach ($scrs as $s) {
		if (!empty($s['scr01'])) {
			$conn->query("INSERT INTO screenshots SET game_id = ?, name = ?", array($s['gameid'], $s['scr01'] . '_1s.jpg'));
		}
		if (!empty($s['scr02'])) {
			$conn->query("INSERT INTO screenshots SET game_id = ?, name = ?", array($s['gameid'], $s['scr02'] . '_2s.jpg'));
		}
		if (!empty($s['scr03'])) {
			$conn->query("INSERT INTO screenshots SET game_id = ?, name = ?", array($s['gameid'], $s['scr03'] . '_3s.jpg'));
		}
	}
*/
	echo "Rand:" . rand(0, 100000);
?>


 <form action="./?report=updatelogos" method="POST">
 <input type="hidden" name="action" value="update">
    <table>
    <tr>
        <td>Update logos and ratings:<td><input type="submit" name="submit" value="           Go!          ">
    </table>
 </form>
