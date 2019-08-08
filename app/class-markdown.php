<?php

require_once 'Michelf/Markdown.inc.php';

//spl_autoload_register(function($class){
//	require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
//});



use \Michelf\Markdown;

//include "/Michelf/Markdown.inc.php";


function headchanger($text) {

	if (strpos($text, '\r\n## ') !== 0) {
		$text = str_replace("\r\n## ", "\r\n### ", $text);
	}

	// костыль: если текст начинается с заголовка второго уровня
	if (Helpers::startsWith($text, "## ")) {
		$text = '#' . $text;
	}

	return $text;
}




function smarty_modifier_markdown_headchanger($text) {
	return headchanger($text);
}


function markdown($text) {
	# Transform text using parser.
	return Markdown::defaultTransform($text);
}

### Smarty Modifier Interface ###

// author: rabbitone
function replace_marks($text) {

    $matches = array();
    $mark_regexp = "/\[mark.*\]*/i";

    $text = preg_replace_callback($mark_regexp,

        function($matches) {

            //print_r($matches);
            //foreach($matches[0] as $mark) {
            $mark = $matches[0];

            $mark_value = array();
            $mark_comment = array();

            preg_match("/value=\"(.*?)\"/i", $mark, $mark_value);
            //preg_match("/comment=\"(.*?)\"/i", $mark, $mark_comment);
	        //"(?:[^"\\]|\\.)*"     http://www.regextester.com/3269
	        preg_match("/comment=\"(.*)\"/i", $mark, $mark_comment);
	        //preg_match("/comment=\"(.*?)\"/i", $mark, $mark_comment);


            $final_value = "";
            $final_comment = "";

            if (!empty($mark_value) && !empty($mark_comment) && !empty($mark_value[1]) && !empty($mark_comment[1])) {
                $final_value = $mark_value[1];
                $final_comment = markdown(stripslashes($mark_comment[1]));
            }

            //echo $final_value;
            //echo $final_comment;

	        if (strlen($final_value) > 1) {
		        return '<div class="numblocks"><div class="span-mark-long">' . $final_value . '</div><div class="mark-text">' . trim($final_comment) . '</div></div>';
	        }

            return '<div class="numblocks"><div class="span-mark">' . $final_value . '</div><div class="mark-text">' . trim($final_comment) . '</div></div>';
        },

        $text);



/*
 *
 *
 *

        foreach($matches[0] as $mark) {

            $mark_value = array();
            if (preg_match("/value=\"(.*?)\"/i", $mark, $mark_value)) {
                //print_r($mark_value);
            }

            $mark_comment = array();
            if (preg_match("/comment=\"(.*?)\"/i", $mark, $mark_comment)) {
                //print_r($mark_comment);
            }

            $final_value = "";
            $final_comment = "";

            if (!empty($mark_value) && !empty($mark_comment) && !empty($mark_value[1]) && !empty($mark_comment[1])) {
                $final_value = $mark_value[1];
                $final_comment = Markdown($mark_comment[1]);
            }

            //echo $final_value;
            //echo $final_comment;


        }



 *
 */



    return $text;
}


// modified by rabbitone
function smarty_modifier_markdown($text) {

    $text = replace_marks($text);
	return markdown($text);
}


### Textile Compatibility Mode ###

# Rename this file to "classTextile.php" and it can replace Textile everywhere.

if (strcasecmp(substr(__FILE__, -16), "classTextile.php") == 0) {
	# Try to include PHP SmartyPants. Should be in the same directory.
	@include_once 'smartypants.php';
	# Fake Textile class. It calls Markdown instead.
	class Textile {
		function TextileThis($text, $lite='', $encode='') {
			if ($lite == '' && $encode == '')    $text = Markdown($text);
			if (function_exists('SmartyPants'))  $text = SmartyPants($text);
			return $text;
		}
		# Fake restricted version: restrictions are not supported for now.
		function TextileRestricted($text, $lite='', $noimage='') {
			return $this->TextileThis($text, $lite);
		}
		# Workaround to ensure compatibility with TextPattern 4.0.3.
		function blockLite($text) { return $text; }
	}
}


