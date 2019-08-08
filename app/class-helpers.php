<?


class Helpers {


	/**
	 * bp_core_time_since()
	 *
	 * Based on function created by Dunstan Orchard - http://1976design.com
	 *
	 * This function will return an English representation of the time elapsed
	 * since a given date.
	 * eg: 2 hours and 50 minutes
	 * eg: 4 days
	 * eg: 4 weeks and 6 days
	 *
	 * @package BuddyPress Core
	 * @param $older_date int Unix timestamp of date you want to calculate the time since for
	 * @param $newer_date int Unix timestamp of date to compare older date to. Default false (current time).
	 * @return str The time since.
	 */
	 
	function time_since( $older_date, $newer_date = false ) {
		// array of time period chunks
	
		$chunks = array(
		array( 60 * 60 * 24 * 365 , 'year', 'years' ),
		array( 60 * 60 * 24 * 30 , 'month', 'months' ),
		array( 60 * 60 * 24 * 7, 'week', 'weeks' ),
		array( 60 * 60 * 24 , 'day', 'days' ),
		array( 60 * 60 , 'hour', 'hours' ),
		array( 60 , 'minute', 'minutes' ),
		array( 1, 'second', 'seconds' )
		);
	
		if ( !is_numeric( $older_date ) ) {
			$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
			$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
	
			$older_date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
		}
	
		/* $newer_date will equal false if we want to know the time elapsed between a date and the current time */
		/* $newer_date will have a value if we want to work out time elapsed between two known dates */
		
//		$newer_date = ( !$newer_date ) ? gmmktime( gmdate( 'H' ), gmdate( 'i' ), gmdate( 's' ), gmdate( 'n' ), gmdate( 'j' ), gmdate( 'Y' ) ) : $newer_date;
		$newer_date = ( !$newer_date ) ? gmmktime(  ) : $newer_date;
		//echo $older_date.'+'.$newer_date."\n\n";
	
		/* Difference in seconds */
		$since = $newer_date - $older_date;
	
		/* Something went wrong with date calculation and we ended up with a negative date. */
		//if ( 0 > $since )
			//return 'sometime'.$since;
	
		/**
		 * We only want to output two chunks of time here, eg:
		 * x years, xx months
		 * x days, xx hours
		 * so there's only two bits of calculation below:
		 */
	
		/* Step one: the first chunk */
		for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
			$seconds = $chunks[$i][0];
	
			/* Finding the biggest chunk (if the chunk fits, break) */
			if ( ( $count = floor($since / $seconds) ) != 0 )
				break;
		}
	
		/* Set output var */
		$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];
	
		/* Step two: the second chunk */
		if ( $i + 2 < $j ) {
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];
	
			if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
				/* Add to output var */
				$output .= ( 1 == $count2 ) ? ',' . ' 1 '. $chunks[$i + 1][1] : ',' . ' ' . $count2 . ' ' . $chunks[$i + 1][2];
			}
		}
	
		if ( !(int)trim($output) )
			$output = '0 ' . 'seconds';
	
		return $output;
	}
 	


	//Utility Function to Trim Array
    function trim_array(array $array, $int){
            $newArray = array();
            for($i = 0; $i < min($int,count($array)); $i++){
                array_push($newArray,$array[$i]);
            }
            return (array)$newArray;
    }


	//function markdown($text) {
	//	$parser = new Markdown_Parser();
	//	return $parser->transform($text);
	//}




	/**
	 * Converts MySQL DATETIME field to user specified date format.
	 *
	 * If $dateformatstring has 'G' value, then gmmktime() function will be used to
	 * make the time. If $dateformatstring is set to 'U', then mktime() function
	 * will be used to make the time.
	 *
	 * The $translate will only be used, if it is set to true and it is by default
	 * and if the $wp_locale object has the month and weekday set.
	 *
	 * @since 0.71
	 *
	 * @param string $dateformatstring Either 'G', 'U', or php date format.
	 * @param string $mysqlstring Time from mysql DATETIME field.
	 * @param bool $translate Optional. Default is true. Will switch format to locale.
	 * @return string Date formated by $dateformatstring or locale (if available).
	 */

	function mysql2date( $dateformatstring, $mysqlstring /*, $translate = false */ ) {
		//global $wp_locale;
		$m = $mysqlstring;
		if ( empty( $m ) )
			return false;
	
		if( 'G' == $dateformatstring ) {
			return strtotime( $m . ' +0000' );
		}
	
		$i = strtotime( $m );
	
		if( 'U' == $dateformatstring )
			return $i;
	
		//if ( $translate)
		//    return date_i18n( $dateformatstring, $i );
		//else
			return date( $dateformatstring, $i );
	} 




	static function get_game_link_and_size($url, $vendor, $identifier = '') {
		//
		if (empty($vendor) || (empty($url) && empty($identifier))) {
			return null;
		}

		if (empty($url)) {
			// попытаемся угадать
			//$basename_variant = "/download/{$vendor}/" . str_replace('-', '_', $identifier) . '.exe';
			$basename_variant = "/dl/{$vendor}/" . str_replace('-', '_', $identifier) . '.exe';

			if (file_exists(".{$basename_variant}")) {
				return array('url' => 'http://gamefabrique.com' . $basename_variant, 'size' => filesize(".{$basename_variant}") );
			}
		}


		if ($vendor != 'trymedia') {
		//if ($vendor == 'genesis' || $vendor == 'nes' || $vendor == 'snes' || $vendor == 'n64' || $vendor == 'sms') {

			$base = basename($url);
			//$path = "/download/{$vendor}/{$base}";
			$path = "/dl/{$vendor}/{$base}";

			if (file_exists(".{$path}")) {
				$size = filesize(".{$path}");

				return array('url' => 'http://gamefabrique.com'.$path, 'size' => $size);
				//return array('url' => $path, 'size' => $size);
			}

			return null; // not exists, у нас только внутренние ссылки

		} else {
			return array('url' => $url);
		}
	}



    static function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    static function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }



}


