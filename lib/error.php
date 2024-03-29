<?php
/**
* File: error.php
*
* Error class. Helps to find errors.
*
* @author Klaus Horsten <horsten@gmx.at>
* @package TGD   
*/
//include_once('policeman.php'); 
/**
 * Error:  
 * 
 * @subpackage 
 */  
class Error 
{

	/**
	 * back_trace_errors:
	 * 
	 * @return 
	 * @access 
	 **/
	function back_trace_errors()
	{
		if (!function_exists('debug_backtrace'))
		{
			echo 'function debug_backtrace does not exists'."\r\n";
			return;
		}
		echo '<pre>';
		echo 'Aufrufverlauf der Funktionen:'."\r\n";

		$collection_of_function_calls = debug_backtrace();
		$collection_of_function_calls = array_reverse($collection_of_function_calls);

		$count = count($collection_of_function_calls);
		$i = 1;
		for ($i = 0; $i < $count; $i++)
		{
			if ($i != $count -1)
			{
				Error::_trace_output($collection_of_function_calls, $i, false);
			}
			else
			{
				Error::_trace_output($collection_of_function_calls, $i, true);
			}
		}
		echo '</pre>';
	}


	/**
	 * _trace_output:
	 * @param $collection_of_function_calls: 
	 * @param $i: 
	 * @param $false: 
	 * 
	 * @return 
	 * @access 
	 **/
	function _trace_output($collection_of_function_calls, $i, $brackets_at_last_list_item = false)
	{
		$collection_of_function_calls = $collection_of_function_calls[$i];

		$list_item_counter = $i + 1;
		if ($brackets_at_last_list_item)
		{
			echo "[$list_item_counter. ";
		}
		else
		{
			echo "$list_item_counter. ";
		}

		if (isset($collection_of_function_calls['file']))
		{
			echo $collection_of_function_calls['file'] . ', Zeile: ' . $collection_of_function_calls['line'];
		}
		else
		{
			// if file was not set, I assumed the functioncall
			// was from PHP compiled source (ie XML-callbacks).
			echo '<PHP inner-code>';
		}
		echo ', ';

		if (isset($collection_of_function_calls['class']))
		{
			echo $collection_of_function_calls['class'] . $collection_of_function_calls['type'];
		}

		echo $collection_of_function_calls['function'];


		if (isset($collection_of_function_calls['args']) && sizeof($collection_of_function_calls['args']) > 0)
		{
			echo "(...)";
			echo "<p/>";
			echo "Parameterwerte der Funktion:<p/>";
			echo "<pre>";
			var_dump($collection_of_function_calls['args']);
			echo "</pre>";
		}
		else
		{
			echo '()';
		}

		if ($brackets_at_last_list_item)
		{
			echo "]";
		}
		echo "\r\n";
	}


	/**
	 * reportEmptyParameter:
	 * @param $file: 
	 * @param $line: 
	 * @param $function: 
	 * 
	 * @return 
	 * @access 
	 **/
	function reportEmptyParameter($file, $line, $function)
	{
		echo "<p/>\n"; 
		echo "Fehler. Parameter leer. Bitte Parameter-Wert angeben in File "
		.$file. ", Zeile ".$line .", Funktion "
		.$function."(...)";
		echo "<p/>\n"; 
	}


	/**
	 * reportWrongParameter:
	 * @param $file: 
	 * @param $line: 
	 * @param $function: 
	 * 
	 * @return 
	 * @access 
	 **/
	function reportWrongParameter($file, $line, $function)
	{
		echo "<p/>\n"; 
		echo "Fehler. Falscher Parameter-Wert in File "
		.$file. ", Zeile ".$line .", Funktion "
		.$function."(...)";
		echo "<p/>\n"; 
	   	echo "&Uuml;berpr&uuml;fen Sie den Typ des &uuml;bergebenen Parameters.
		Z.B. kann ein integer-Wert erwartet werden, wo
		tats&auml;chlich aber ein string &uuml;bergeben worden ist."; 	
		echo "<p/>\n"; 
	}



}
?>
