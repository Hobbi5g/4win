<?
    include ($s_addurl."login.php");

	global $conn;
    require_once '../db_conn.php';

	$sSQL = "SELECT * FROM trymediafeed WHERE productid='$id'";
	$data = $conn->getRow($sSQL); 
	//$data = unserialize($data);
	$data = print_r($data, true)

?><html>
<head>
<title><?=$softname?> v.<?=$version;?></title>
<style type=text/css>
.check1 { border: 0px solid #ffffff;}
input { border: 1px solid #303030;}
.input1 { border: 1px solid #303030;}
td, body {font-family: Tahoma, Verdana, Arial; font-size: 11px; background-color: #f0f0f0;}
.frm {font-family: Courier, Tahoma, Arial; font-size: 12px; background-color: #e0e0e0; border: 1px solid #000000;}
body {scrollbar-base-color: #000066; scrollbar-arrow-color: #ffffff; scrollbar-highlight-color: #FFFFFF; scrollbar-shadow-color: #FFFFFF; scrollbar-face-color: #909090; scrollbar-track-color: #f0f0f0; }
h1 {font-family: Verdana; font-size: 10px; font-weight: bold; display: inline; text-transform: uppercase; letter-spacing: 2px;}
.frm5 {background-color: #e7e7e7; border: 1px dashed #303030;}
.frm4 {background-color: #ffffff; border: 1px dashed #303030; }
.frm6 {background-color: #e7e7e7; border: 1px solid #303030; margin: 0 0 2 0; }
.inp{font-family:courier;font-size:12px;background:#f5f5f5;border:1px solid #999999}
.butt{font-family:verdana;font-size:12px;color:#333333;background:#e0e0e0;border:1px outset #cccccc; margin:4 0 10 0;}
a:active {background-color: #ff0000;color:#ffffff;}
</style>

</head>
<body text="#000000" bgcolor="#f0f0f0" link="#ff0000" vlink="#500000" alink="#ff8080">

  
<pre>
<?=$data?>
</pre>

</body>
</html>