<?
    ob_start('ob_gzhandler'); // gzipped content
    echo str_repeat(" ", 256);



	global $s_addurl;

	if (!isset ($s_addurl))
	{
		$z=dirname(__FILE__);
		$z2=$_SERVER['DOCUMENT_ROOT'].dirname ($_SERVER['PHP_SELF']);
		$s_addurl=substr ($z, strpos ($z, $z2)+strlen($z2));
		if ($s_addurl!="") $s_addurl.="/";
	}

    require_once 'settings.php';

	//include_once('../../app/const.php');

    //require_once 'mysql.php';
    //require_once '../../app/db_conn.php';
	//require_once '../../app/class-database.php';

	//include_once('../smarty/smarty.class.php');
	require_once('../../app/smarty3/Smarty.class.php');

    require_once 'tracker.php';
	require_once 'class.stemmer.inc.php';
	require_once 'class.html2text.inc.php';

    include ($s_addurl . 'login.php');

	include_once('routines.php');

    // start count time
    $ttt=microtime();
    $ttt=((double)strstr($ttt, ' ')+(double)substr($ttt,0,strpos($ttt,' ')));

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");               // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate");   // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");                                     // HTTP/1.0 

    if (!isset($report)) $report="m";


	global $tpl;
	// Smarty init
	$tpl = new Smarty();
	$tpl->template_dir = './tpl';
	$tpl->compile_dir  = './tpl_c';

    require_once 'register_me.php';

    require_once './../../app/crop/src/stojg/crop/Crop.php';
    require_once './../../app/crop/src/stojg/crop/CropCenter.php';
    require_once './../../app/crop/src/stojg/crop/CropEntropy.php';


    $report = $_GET['report'];
    $filter = $_GET['filter'];
    $action = $_GET['action'];
    $gameid = $_GET['gameid'];


?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?=$softname?> v.<?=$version;?></title>
</head>

<style type=text/css>
.check1 { border: 0px solid #ffffff;}
input { border: 1px solid #303030;}
.input1 { border: 1px solid #303030;}
body {font-size: 85%;}
.frm {font-family: Courier, Tahoma, Arial; font-size: 12px; background-color: #e0e0e0; border: 1px solid #000000;}
body {scrollbar-base-color: #000066; scrollbar-arrow-color: #ffffff; scrollbar-highlight-color: #FFFFFF; scrollbar-shadow-color: #FFFFFF; scrollbar-face-color: #909090; scrollbar-track-color: #f0f0f0; }
h1 {font-family: Verdana; font-size: 140%; font-weight: bold; margin:2em 0 0.2em 0;}
.inp{font-family:courier;font-size:12px;background:#f5f5f5;border:1px solid #999999}
a:active {background-color: #ff0000;color:#ffffff;}
.chk{margin:0 0 0 0;padding:0 0 0 0}

/*
Tema: Blue - Minimalist design in blue
Author: Newton de Gуes Horta
Site: http://www.nghorta.com
Country Origin: Brazil
*/

table {
 font-size: 95%;
 font-family: 'Lucida Grande', Helvetica, verdana, sans-serif;
 background-color:#fff;
 border-collapse: collapse;
 width: 100%;
 line-height: 1.2em;
}
caption {
 font-size: 30px;
 font-weight: bold;
 color: #002084;
 text-align: left;
 padding: 10px 0px;
 margin-bottom: 2px;
 text-transform: capitalize;
}
thead th {
 border-right: 2px solid #fff;
 color:#fff;
 text-align:center;
 padding:2px;
 height:25px;
 background-color: #004080;
}
tfoot {
 color:#002084;
 padding:2px;
 text-transform:uppercase;
 font-size:1.2em; 
 font-weigth: bold;
 margin-top:6px;
 border-top: 6px solid #004080;
 border-bottom: 6px solid #004080;
}
tbody tr {
 background-color:#fff;
 border-bottom: 2px solid #c0c0c0;
}
tbody td {
 color:#002000;
 padding:5px;
 text-align:left;
}
tbody tr.inactive td {
 color: gray;
 padding: 5px;
 text-align: left;
}
tbody th {
 text-align:left;
 padding: 2px;
}
tbody td a, tbody th a {
 color:#002084;
 text-decoration:underline;
 font-weight:normal; 
}
tbody td a:hover, tbody th a:hover {
 text-decoration:none;
}

</style>


<body text="#000000" bgcolor="#f0f0f0" link="#ff0000" vlink="#500000" alink="#ff8080">


<script>

function postwith (to,p) {
  var myForm = document.createElement("form");
  myForm.method="post" ;
  myForm.action = to ;
  for (var k in p) {
    var myInput = document.createElement("input") ;
    myInput.setAttribute("name", k) ;
    myInput.setAttribute("value", p[k]);
    myForm.appendChild(myInput) ;
  }
  document.body.appendChild(myForm) ;
  myForm.submit() ;
  document.body.removeChild(myForm) ;
}

</script>

<form action="<? $PHP_SELF; ?>" method=post>

<table width=100% cellspacing=1 cellpadding=2><tr><td>
<a href="https://games4win.com/" target=blank>Games4Win</a> | <a href=./login.php?l=logout&p=logout>Logout</a>
</td><td align=right>

</table>
<br>
<table width=100% class=frm4 cellspacing=5 cellpadding=1><tr><td width=5><img src="../siteditor/images/logo01.gif" width="32" height="32" border=0 alt="">
</td><td valign=center>
    &nbsp;&nbsp;Site maintenance:
    <?if($logged_role == 'admin'){?><a href="./?report=w">MAIN</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=stats">Statistics</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=weekstats">Last Week Statistics</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=search">Search manager</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=subscribes">Subscribes</a><?}?>

    <br>
    &nbsp;&nbsp;Games:
    <?if($logged_role == 'admin'){?><a href="./?report=h">SiteNews</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=developers">Developers</a> | <?}?>
    <a href=./?report=g>GameBase</a> |
    <a href=./?report=addlogo>Add logo</a> |
    <?if($logged_role == 'admin'){?><a href="./?report=o">Submissions (old)</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=padfile">PAD</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=trymedia">Trymedia</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=comments">Comments</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=mainmenu">Main Menu</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=similar">Similar games</a><?}?>

    <br>
    &nbsp;&nbsp;Tracking:
    <?if($logged_role == 'admin'){?><a href="./?report=tracking_manager">Manager</a><?}?>

    <br>
    &nbsp;&nbsp;One-time actions:
    <?if($logged_role == 'admin'){?><a href="./?report=updatelogos">Update game logos</a> | <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=sitemap">Sitemap generator</a>&nbsp;| <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=language">Language</a>&nbsp;| <?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=convert_reflexive">Convert Reflexive</a><?}?>
    <?if($logged_role == 'admin'){?><a href="./?report=keywords">Keywords</a><?}?>

</td>

</table>
</form>


<?

if ($report=="w" && $logged_role == 'admin') {
    include('welcome.php');
}

if ($report=="g") {
    include('editgames.php');
}


if ($report=="h" && $logged_role == 'admin') {
    include('editnews.php');
}

if ($report=='o' && $logged_role == 'admin') {
    include('editold.php');
}

if ($report=="addlogo") {
	if ($logged_role=='guest')
	    include('notauthorised.php');
	else
	    include('addlogo.php');
}


if ($report=="similar" && $logged_role == 'admin') {
    include('similar.php');
}


if ($report=="tracking_manager" && $logged_role == 'admin') {
    include('tracking_manager.php');
}

if ($report=="gr" && $logged_role == 'admin') {
    include('spesta/edit.php');
}

if ($report=="stats" && $logged_role == 'admin') {
    include('fromstats.php');
}

if ($report=="weekstats" && $logged_role == 'admin') {
    include('weekstats.php');
}


if ($report=="search" && $logged_role == 'admin') {
    include('searchengine.php');
}

if ($report=="padfile" && $logged_role == 'admin') {
    include('padfile.php');
}

if ($report=="comments" && $logged_role == 'admin') {
    include('comments.php');
}

if ($report=="trymedia" && $logged_role == 'admin') {
    include('trymedia.php');
}

if ($report=="updatelogos" && $logged_role == 'admin') {
    include('updatelogos.php');
}

if ($report=="sitemap" && $logged_role == 'admin') {
    include('sitemap.php');
}

if ($report=="developers" && $logged_role == 'admin') {
    include('developers.php');
}

if ($report=="subscribes" && $logged_role == 'admin') {
    include('subscribes.php');
}

if ($report=="language" && $logged_role == 'admin') {
    include('language.php');
}

if ($report=="mainmenu" && $logged_role == 'admin') {
    include('mainmenu.php');
}

if ($report=="convert_reflexive" && $logged_role == 'admin') {
    include('convert_reflexive.php');
}

if ($report=="keywords" && $logged_role == 'admin') {
    include('keywords.php');
}


$pgt=microtime();
$pgt=((double)strstr($pgt, ' ') + (double)substr($pgt,0,strpos($pgt,' ')));
$tm=(number_format(($pgt-$ttt),3))." sec."; 
?>
<hr>
<?=$softname?> v.<?=$version;?><br>
Page generation - <?=$tm?>
</body></html>