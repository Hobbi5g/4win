<?php

	$tagarray = array();
	$tagstack = '';
	$lasttag = '';

	function GetData($parser,$data)
	{
		global $tagstack, $tagarray, $lasttag;
		if ($lasttag == $tagstack) $tagarray["$tagstack"] .= $data; else $tagarray["$tagstack"] = $data;
		$lasttag = $tagstack;
	}


	function GetEndTag($parser,$element_name)
	{
		global $tagstack;
		$x = "<" . $element_name . ">";
 		if (strrchr($tagstack,$x) == $x) $tagstack = substr($tagstack,0,strrpos($tagstack,$x));
    	else
		{ 
      		echo "Error: no corresponding start tag for end tag",htmlspecialchars($x),"<br><br>";
			exit;
    	}
	}


  	function GetStartTag($parser,$element_name,$element_attribs)
  	{
    	global $tagstack;
	    $tagstack .= "<" . $element_name . ">";
	}


  //$padurl = $HTTP_GET_VARS["padurl"];
  //$padcat = $HTTP_GET_VARS["padcat"];


function ProceedPAD($fname)
{

	global $tagstack, $tagarray, $lasttag;

	$padurl = $fname;
	$padcat = '';
	$paderr = false;

	if (substr(strtolower($padurl),0,7) <> "http://") 
        $paderr = true;
		//die ("<b>Error:</b> PAD URL must begin with http://<br>Press the Back button.<br>");

	if (strtolower($padurl) == "http://") 
        $paderr = true;
    	//die ("<b>Error:</b> No PAD URL submitted. Press the Back button.<br>");

	$tagstack = "";
  	$lasttag = "?";

  	if (!$data = @file($padurl)) 
        $paderr = true;
		//die("Error opening $padurl . Press back button and check URL"); //print_r($data);

  	$data = implode(" ",$data); $content = $data;
  	$data = substr($data, strpos($data,"<?xml"));  // remove any http headers

  	if (!($xml_parser = xml_parser_create())) die ("Error creating XML parser");

  	xml_set_element_handler($xml_parser,"GetStartTag","GetEndTag");
  	xml_set_character_data_handler($xml_parser,"GetData");
  	xml_parse($xml_parser,$data,true);
  	xml_parser_free($xml_parser);

  	$reqtags = array("<XML_DIZ_INFO><COMPANY_INFO><COMPANY_NAME>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_NAME>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_VERSION>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_RELEASE_MONTH>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_RELEASE_DAY>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_RELEASE_YEAR>",
                   	"<XML_DIZ_INFO><COMPANY_INFO><COMPANY_WEBSITE_URL>",
                   	"<XML_DIZ_INFO><COMPANY_INFO><CONTACT_INFO><CONTACT_EMAIL>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_COST_DOLLARS>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_TYPE>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_OS_SUPPORT>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><FILE_INFO><FILE_SIZE_K>",
                   	"<XML_DIZ_INFO><PROGRAM_DESCRIPTIONS><ENGLISH><KEYWORDS>",
                   	"<XML_DIZ_INFO><PROGRAM_DESCRIPTIONS><ENGLISH><CHAR_DESC_2000>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_INFO_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_SCREENSHOT_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_XML_FILE_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><DOWNLOAD_URLS><PRIMARY_DOWNLOAD_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_ORDER_URL>",
					"<XML_DIZ_INFO><ASP><ASP_MEMBER_NUMBER>");

	// set defaults for cost, screenshot, and ASP number if blank
	$tag = "<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_COST_DOLLARS>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "0.00";
	$tag = "<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_SCREENSHOT_URL>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "none";

	$tag = "<XML_DIZ_INFO><ASP><ASP_MEMBER_NUMBER>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "none";

	$tag = "<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_ORDER_URL>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "";


  // check for missing/empty tags
  $paderr = false; 

  foreach ($reqtags as $tag)
  {
    if (empty($tagarray["$tag"]))
    {
      echo "Required tag not found in PAD file: ",htmlspecialchars($tag),"<br>";
      $paderr = true;
    }
    else
    {
      $tagarray["$tag"] = trim($tagarray["$tag"]);
      if  ($tagarray["$tag"] == "")
      {
        echo "Required data not found in tag: ",htmlspecialchars($tag),"<br>";
        $paderr = true;
      }
    }
  }


  $tag = $reqtags[0];  $company = substr($tagarray["$tag"],0,50);
  $tag = $reqtags[1];  $title = substr($tagarray["$tag"],0,50);
  $tag = $reqtags[2];  $version = $tagarray["$tag"];
  $tag = $reqtags[3];  $pmonth = $tagarray["$tag"];
  $tag = $reqtags[4];  $pday = $tagarray["$tag"];
  $tag = $reqtags[5];  $pyear = $tagarray["$tag"];
  $tag = $reqtags[6];  $website = $tagarray["$tag"];
  $tag = $reqtags[7];  $email = $tagarray["$tag"];
  $tag = $reqtags[8];  $cost = $tagarray["$tag"];
  $tag = $reqtags[9];  $ptype = $tagarray["$tag"];
  $tag = $reqtags[10]; $os = $tagarray["$tag"];
  $tag = $reqtags[11]; $ksize = $tagarray["$tag"];
  $tag = $reqtags[12]; $keywords = $tagarray["$tag"];
  $tag = $reqtags[13]; $description = $tagarray["$tag"];
  $tag = $reqtags[14]; $homepage = $tagarray["$tag"];
  $tag = $reqtags[15]; $screenshot = $tagarray["$tag"];
  $tag = $reqtags[16]; $padfile = $tagarray["$tag"];
  $tag = $reqtags[17]; $download = $tagarray["$tag"];
  $tag = $reqtags[18]; $order = $tagarray["$tag"];
  $tag = $reqtags[19]; $aspnumber = $tagarray["$tag"];

  if (strlen($pmonth) == 1) $pmonth = "0" . $pmonth;
  if (strlen($pday) == 1) $pday = "0" . $pday;
  $pdate = $pyear . "-" . $pmonth . "-" . $pday;
  $ksize = str_replace(",","",$ksize);  // remove any commas

  // count chars in description minus html encodings

  $desc2 = str_replace("&lt;","x",strtolower($description));
  $desc2 = str_replace("&gt;","x",$desc2);
  $desc2 = str_replace("&amp;","x",$desc2);
  $desc2 = str_replace("&quot;","x",$desc2);
  if (strlen($desc2) > 450)
	$paderr = true;
    //die("Error: CHAR_DESC_450 field exceeds 450 character limit.<br>");


	if ($paderr) {
		//die("<br><b>PAD file ($fname) rejected</b><br>");
		//echo "<br><b>PAD file ($fname) rejected</b><br>";
		$ret = array(
					'error' => "PAD file ($fname) rejected"
					);
		return $ret;

	}


//  if (strcasecmp($padurl,$padfile) <> 0) // removed in v2.00.01 (DV)
//    die("Error: submitted url ($padurl) does not match url inside PAD file ($padfile). Click back button and correct the URL"); // removed in v2.00.01 (DV)

  //$tomorrow = date("Y-m-d",time()+86400);
  //if ($pdate > $tomorrow) 
  //  die("Error: Forward dating not allowed. Release date ($pdate) is greater than tomorrow's date ($tomorrow)<br>");



  // connect to database
  //$link_id = mysql_connect("localhost",$userid,$userpassword) or die ("Error connecting to the database server.");
  //mysql_select_db ($dbname);

  //$company = addslashes($company);
  //$keywords = addslashes($keywords);
  //$title = addslashes($title);
  //$description = addslashes($description);
  //$download = addslashes($download); // added in v1.50.03 (DV)
  //$poll = 0;
    
  // see if company/title already in paddata table

  //$result = mysql_query("select * from paddata where company = '$company' and title = '$title'", $link_id);
  //$replace = (mysql_num_rows($result) <> 0);

  // insert (replace) fields into paddata table

  //$flist =  "company, title, version, pdate, website, email, cost, ptype, os, ksize, ";
  //$flist .= "category, keywords, description, homepage, screenshot, padfile, download, aspnumber, poll";
  //$vlist =  "'$company', '$title', '$version', '$pdate', '$website', '$email', '$cost', '$ptype', '$os', '$ksize', ";
  //$vlist .= "'$padcat', '$keywords', '$description', '$homepage', '$screenshot', '$padfile', '$download', '$aspnumber', '$poll'";
  //if (!mysql_query("replace into paddata ($flist) values($vlist)", $link_id)) die("Error updating paddata table");
  //echo $vlist.'<br><br>';
  // update activity log

  //$ipaddr = getenv("REMOTE_ADDR");
  //mysql_query("insert into editlog values(now(), '$aspnumber', '$ipaddr', 'Add PAD: $company - $title')", $link_id);

  // disconnect from database

  //mysql_close($link_id);

  // display messages

  //if ($replace) echo "Existing program record successfully updated<br>";
  //else echo "New program successfully added<br>";

/*

		$game = array(
		    'gameid' => $data['gameid'],
    		'stringid' => $data['stringid'],
		    'userid' => $data['userid'],
	    	'developer' => $data['developer'],
	    	'vendorid' => $data['vendorid'],
		    'title' => $data['title'],
		    'version' => $data['version'],
		    'category01' => $data['category01'],
		    'category02' => $data['category02'],
		    'category01_formatted' => ConvertCategory($data['category01']),
		    'category02_formatted' => ConvertCategory($data['category02']),
		    'shortdesc' => strtr($data['shortdesc'], array("\r\n"=>' ')),
		    'page_title' => $data['page_title'],
		    'rating' => $data['rating'],
		    'win311' => $data['win311'],
		    'win9x' => $data['win9x'],
		    'winnt' => $data['winnt'],
		    'win2k' => $data['win2k'],
		    'winxp' => $data['winxp'],
		    'other' => $data['other'],
		    'requires' => $data['requires'],
		    'gameprice' => $data['gameprice'],
		    'gameprice_formatted' => ConvertPrice($data['gameprice']),
		    'fsize' => $data['fsize'],
		    'fsize_formatted' => ConvertFSize($data['fsize']),
		    'homepage' => $data['homepage'],
		    'download1' => $data['download1'],
		    'download2' => $data['download2'],
		    'download1base' => basename($data['download1']),
		    'download2base' => basename($data['download2']),
		    'screenshot' => $data['screenshot'],
		    'scr01src' => $data['scr01src'],
		    'scr02src' => $data['scr02src'],
		    'scr03src' => $data['scr03src'],
		    'orderpage' => $data['orderpage'],
		    'logo' => $data['logo'],
		    'counter' => $data['counter'],
		    'featscale' => $data['featscale'],
		    'regdate' => $data['regdate'],
		    'hidden' => $data['hidden'],
		    'searchdataser' => $data['searchdataser'],
		    'playability' => $data['playability'],
		    'graphics' => $data['graphics'],
		    'sounds' => $data['sounds'],
		    'quality' => $data['quality'],
		    'idea' => $data['idea'],
		    'awards' => $data['awards'],
		    'time' => $data['time'],
		    'action' => $data['action'],
		    'age1' => $data['age1'],
		    'age2' => $data['age2'],
		    'age3' => $data['age3'],
		    'age4' => $data['age4'],
		    'age5' => $data['age5'],
		    'age6' => $data['age6'],
		    'cpu' => $data['cpu'],
		    'video' => $data['video'],
		    'netmode1' => $data['netmode1'],
		    'netmode2' => $data['netmode2'],
		    'netmode3' => $data['netmode3'],
		    'netmode4' => $data['netmode4']
 		);


*/

	$ret = array(
				'developer' => $company,
				'title' => $title,
				'version' => $version,
				'pmonth' => $pmonth,
				'pday' => $pday,
				'pyear' => $pyear,
				//'website' => $website, // corporate website
				'email' => $email,
				'gameprice' => $cost,
				'ptype' => $ptype,
				'os' => $os,
				'fsize' => $ksize,
				'keywords' => $keywords,
				'description' => $description,
				'homepage' => $homepage,
				'screenshot' => $screenshot,
				'padfile' => $padfile,
				'download1' => $download,
				'orderpage' => $order,
				'aspnumber' => $aspnumber,
				
				'padfilecontent' => $content
				);
	return $ret;

}// function ProceedPAD






function GetPADFullInfo($padid)
{
	global $tagstack, $tagarray, $lasttag;
	global $conn;

	//$padurl = $fname;
	$padcat = '';
	$paderr = false;

	$tagstack = "";
  	$lasttag = "?";

	$data = $conn->getOne("SELECT padfilecontent FROM padfiles WHERE padfileid='$padid'"); echo '!!'.$data.'!!';

  	//$data = implode(" ",$data);
	$content = $data;
  	$data = substr($data, strpos($data,"<?xml"));  // remove any http headers

  	if (!($xml_parser = xml_parser_create())) die ("Error creating XML parser");

  	xml_set_element_handler($xml_parser,"GetStartTag","GetEndTag");
  	xml_set_character_data_handler($xml_parser,"GetData");
  	xml_parse($xml_parser,$data,true);
  	xml_parser_free($xml_parser);

  	$reqtags = array("<XML_DIZ_INFO><COMPANY_INFO><COMPANY_NAME>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_NAME>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_VERSION>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_RELEASE_MONTH>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_RELEASE_DAY>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_RELEASE_YEAR>",
                   	"<XML_DIZ_INFO><COMPANY_INFO><COMPANY_WEBSITE_URL>",
                   	"<XML_DIZ_INFO><COMPANY_INFO><CONTACT_INFO><CONTACT_EMAIL>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_COST_DOLLARS>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_TYPE>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_OS_SUPPORT>",
                   	"<XML_DIZ_INFO><PROGRAM_INFO><FILE_INFO><FILE_SIZE_K>",
                   	"<XML_DIZ_INFO><PROGRAM_DESCRIPTIONS><ENGLISH><KEYWORDS>",
                   	"<XML_DIZ_INFO><PROGRAM_DESCRIPTIONS><ENGLISH><CHAR_DESC_450>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_INFO_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_SCREENSHOT_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_XML_FILE_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><DOWNLOAD_URLS><PRIMARY_DOWNLOAD_URL>",
                   	"<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_ORDER_URL>",
					"<XML_DIZ_INFO><ASP><ASP_MEMBER_NUMBER>",

					"<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_SYSTEM_REQUIREMENTS>", 
                   	"<XML_DIZ_INFO><PROGRAM_DESCRIPTIONS><ENGLISH><CHAR_DESC_450>"
                   	//"<XML_DIZ_INFO><PROGRAM_DESCRIPTIONS><ENGLISH><KEYWORDS>"  // keywords


					);

	// set defaults for cost, screenshot, and ASP number if blank
	$tag = "<XML_DIZ_INFO><PROGRAM_INFO><PROGRAM_COST_DOLLARS>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "0.00";
	$tag = "<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_SCREENSHOT_URL>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "none";

	$tag = "<XML_DIZ_INFO><ASP><ASP_MEMBER_NUMBER>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "none";

	$tag = "<XML_DIZ_INFO><WEB_INFO><APPLICATION_URLS><APPLICATION_ORDER_URL>";
	if (empty($tagarray["$tag"]) or ($tagarray["$tag"] == "")) $tagarray["$tag"] = "";


  // check for missing/empty tags
  $paderr = false; 

  foreach ($reqtags as $tag)
  {
    if (empty($tagarray["$tag"]))
    {
      echo "Required tag not found in PAD file: ",htmlspecialchars($tag),"<br>";
      $paderr = true;
    }
    else
    {
      $tagarray["$tag"] = trim($tagarray["$tag"]);
      if  ($tagarray["$tag"] == "")
      {
        echo "Required data not found in tag: ",htmlspecialchars($tag),"<br>";
        $paderr = true;
      }
    }
  }


  $tag = $reqtags[0];  $company = substr($tagarray["$tag"],0,50);
  $tag = $reqtags[1];  $title = substr($tagarray["$tag"],0,50);
  $tag = $reqtags[2];  $version = $tagarray["$tag"];
  $tag = $reqtags[3];  $pmonth = $tagarray["$tag"];
  $tag = $reqtags[4];  $pday = $tagarray["$tag"];
  $tag = $reqtags[5];  $pyear = $tagarray["$tag"];
  $tag = $reqtags[6];  $website = $tagarray["$tag"];
  $tag = $reqtags[7];  $email = $tagarray["$tag"];
  $tag = $reqtags[8];  $cost = $tagarray["$tag"];
  $tag = $reqtags[9];  $ptype = $tagarray["$tag"];
  $tag = $reqtags[10]; $os = $tagarray["$tag"];
  $tag = $reqtags[11]; $ksize = $tagarray["$tag"];
  $tag = $reqtags[12]; $keywords = $tagarray["$tag"];
  $tag = $reqtags[13]; $description = $tagarray["$tag"];
  $tag = $reqtags[14]; $homepage = $tagarray["$tag"];
  $tag = $reqtags[15]; $screenshot = $tagarray["$tag"];
  $tag = $reqtags[16]; $padfile = $tagarray["$tag"];
  $tag = $reqtags[17]; $download = $tagarray["$tag"];
  $tag = $reqtags[18]; $order = $tagarray["$tag"];
  $tag = $reqtags[19]; $aspnumber = $tagarray["$tag"];

  $tag = $reqtags[20]; $sysreq = $tagarray["$tag"];
  $tag = $reqtags[21]; $shortdesc = $tagarray["$tag"];
//  $tag = $reqtags[22]; $keywords = $tagarray["$tag"];

  if (strlen($pmonth) == 1) $pmonth = "0" . $pmonth;
  if (strlen($pday) == 1) $pday = "0" . $pday;
  $pdate = $pyear . "-" . $pmonth . "-" . $pday;
  $ksize = str_replace(",","",$ksize);  // remove any commas

  // count chars in description minus html encodings

  $desc2 = str_replace("&lt;","x",strtolower($description));
  $desc2 = str_replace("&gt;","x",$desc2);
  $desc2 = str_replace("&amp;","x",$desc2);
  $desc2 = str_replace("&quot;","x",$desc2);
  if (strlen($desc2) > 450)
	$paderr = true;
    //die("Error: CHAR_DESC_450 field exceeds 450 character limit.<br>");


	if ($paderr) {
		$ret = array(
					'error' => "PAD file ($fname) rejected"
					);
		return $ret;

	}


/*

		$game = array(
		    'gameid' => $data['gameid'],
    		'stringid' => $data['stringid'],
		    'userid' => $data['userid'],
	    	'developer' => $data['developer'],
	    	'vendorid' => $data['vendorid'],
		    'title' => $data['title'],
		    'version' => $data['version'],
		    'category01' => $data['category01'],
		    'category02' => $data['category02'],
		    'category01_formatted' => ConvertCategory($data['category01']),
		    'category02_formatted' => ConvertCategory($data['category02']),
		    'shortdesc' => strtr($data['shortdesc'], array("\r\n"=>' ')),
		    'page_title' => $data['page_title'],
		    'rating' => $data['rating'],
		    'win311' => $data['win311'],
		    'win9x' => $data['win9x'],
		    'winnt' => $data['winnt'],
		    'win2k' => $data['win2k'],
		    'winxp' => $data['winxp'],
		    'other' => $data['other'],
		    'requires' => $data['requires'],
		    'gameprice' => $data['gameprice'],
		    'gameprice_formatted' => ConvertPrice($data['gameprice']),
		    'fsize' => $data['fsize'],
		    'fsize_formatted' => ConvertFSize($data['fsize']),
		    'homepage' => $data['homepage'],
		    'download1' => $data['download1'],
		    'download2' => $data['download2'],
		    'download1base' => basename($data['download1']),
		    'download2base' => basename($data['download2']),
		    'screenshot' => $data['screenshot'],
		    'scr01src' => $data['scr01src'],
		    'scr02src' => $data['scr02src'],
		    'scr03src' => $data['scr03src'],
		    'orderpage' => $data['orderpage'],
		    'logo' => $data['logo'],
		    'counter' => $data['counter'],
		    'featscale' => $data['featscale'],
		    'regdate' => $data['regdate'],
		    'hidden' => $data['hidden'],
		    'searchdataser' => $data['searchdataser'],
		    'playability' => $data['playability'],
		    'graphics' => $data['graphics'],
		    'sounds' => $data['sounds'],
		    'quality' => $data['quality'],
		    'idea' => $data['idea'],
		    'awards' => $data['awards'],
		    'time' => $data['time'],
		    'action' => $data['action'],
		    'age1' => $data['age1'],
		    'age2' => $data['age2'],
		    'age3' => $data['age3'],
		    'age4' => $data['age4'],
		    'age5' => $data['age5'],
		    'age6' => $data['age6'],
		    'cpu' => $data['cpu'],
		    'video' => $data['video'],
		    'netmode1' => $data['netmode1'],
		    'netmode2' => $data['netmode2'],
		    'netmode3' => $data['netmode3'],
		    'netmode4' => $data['netmode4']
 		);


*/

	$ret = array(
				'stringid' => str_replace(' ','-', strtolower($title)),
				'developer' => $company,
				'title' => $title,
				'version' => $version,
				'pmonth' => $pmonth,
				'pday' => $pday,
				'pyear' => $pyear,
				//'website' => $website, // corporate website
				'email' => $email,
				'gameprice' => $cost,
				'ptype' => $ptype,
				'os' => $os,
				'fsize' => $ksize,
				'keywords' => $keywords,
				'description' => $description,
				'homepage' => $homepage,
				'screenshot' => $screenshot,
				'padfile' => $padfile,
				'download1' => $download,
				'orderpage' => $order,
				'aspnumber' => $aspnumber,

				'shortdesc' => $shortdesc,
				'requires' => $sysreq,
				
				'padfilecontent' => $content
				);
	return $ret;

}// function



?>