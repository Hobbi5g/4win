<?

//$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
//$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


global $s_addurl;

if (!isset ($s_addurl)) {
	$z=dirname(__FILE__);
	$z2=$_SERVER['DOCUMENT_ROOT'].dirname ($_SERVER['PHP_SELF']);
	$s_addurl=substr ($z, strpos ($z, $z2)+strlen($z2));
	if ($s_addurl!="") {
		$s_addurl .= "/";
	}
}

	//echo $s_addurl; // "./"

    require_once 'settings.php';


    //require_once 'register_me.php';
	
	global $conn;

	include_once('../../app/const.php');
	require_once 'DB.php';
	require_once '../../app/class-database.php';
	require_once '../../app/class-markdown.php';
	require_once '../../app/class-helpers.php';

	require_once 'gameinfo.php';
	require_once 'searchlib.php';
	require_once 'specialtxt.php';
	require_once 'tracker.php';
	include_once('routines.php');


	$connection = DatabaseConnection::singleton();
	$conn = $connection->conn;

    //require_once 'mysql.php';

    // siteditor logout
    if (!empty($_GET) && $_GET['l'] == 'logout' && $_GET['p'] == 'logout') {
		setcookie("slogin", null, -1, '/');
		setcookie("spass", null, -1, '/');
		header('Location: /siteditor/');
		exit();
    }

    $try_password = 0;
    $l = $_POST['l']; // login
    $p = $_POST['p']; // password

    if (isset($l)) {

        setcookie("slogin", $l, time()+1000000, "/");
        setcookie("spass", $p, time()+1000000, "/");

		$slogin = $l;
        $spass = $p;
        if (isset($try)) {
			$try_password = $try;
		}


    } else {
        $slogin = $_COOKIE['slogin'];
        $spass = $_COOKIE['spass'];
    }

    $rights='';
	$logged_username = '';

	$rows = $conn->getOne("SELECT COUNT(*) FROM managers WHERE (manager_login = ?) AND (manager_password = ?)", array($slogin, $spass));

	if ($rows == 1) {
		$rights = "verified";

		$data = $conn->getRow("SELECT manager_name, manager_role, relatedgames FROM managers WHERE (manager_login = ?) AND (manager_password = ?)", array($slogin, $spass));

		//$loggeduserid =  $data["manager_id"];
		$logged_username = $data["manager_name"];
		$logged_role = $data["manager_role"];
		$logged_games = $data["relatedgames"];
	}

	$data = array();

    if (($rights=="")) {

?>
<html>
<head>
<title>Login</title>
</head>

<style type=text/css>
input { border: 1px solid #303030;}
.input1 { border: 1px solid #303030;}
td, body {font-family: Tahoma, Verdana, Arial; font-size: 11px; background-color: #f0f0f0;}
.frm {font-family: Courier, Tahoma, Arial; font-size: 12px; background-color: #e0e0e0; border: 1px solid #000000;}
body {scrollbar-base-color: #000066; scrollbar-arrow-color: #ffffff; scrollbar-highlight-color: #FFFFFF; scrollbar-shadow-color: #FFFFFF; scrollbar-face-color: #909090; scrollbar-track-color: #f0f0f0; }
h1 {font-family: Verdana; font-size: 10px; font-weight: bold; display: inline; text-transform: uppercase; letter-spacing: 2px;}
.frm5 {background-color: #e7e7e7; border: 1px dashed #303030;}
.frm4 {background-color: #ffffff; border: 1px dashed #303030; }
.frm6 {background-color: #e7e7e7; border: 1px solid #303030; margin: 0 0 10px 0; }

</style>


<body text="#000000" bgcolor="#ffffff" link="#ff0000" vlink="#500000" alink="#ffffff">

<table width=100% height=100%><tr><td align=center valign=center>

<table cellspacing=3 cellpadding=10>
<tr><td align=left class=frm5><b><font color=red><?if(!$try_password){?>Log In:<?}else{?>Please try again!<?}?></font></b></td></tr>
<tr><td align=right class=frm4>

<form action="./" method=post>
<input type="hidden" name="try" value=1>
Login:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="l" class=frm6><br>
Password:&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" name="p" class=frm6><br><br>
<input name="submit" type="submit" class=frm style="width:100%" value="Login">
</form>

</td></tr><tr><td class=frm5>

<?=$softname?><br>v.<?=$version;?>
</table>

</td></tr></table>

</body>
</html>
<?
        exit(); // adminka only ;)
    }
