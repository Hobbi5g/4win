<?
    include ('login.php');

	global $conn;

    $page = $_POST['page'];
    $report_id = $_GET['id'];

	if (!empty($page) && $page == 'updatelongreport') {

		$report_id = intval($_POST['id']);//echo $report_id;
		$Language = $_POST['Language'];
		$Type = $_POST['Type'];
		$descr = $_POST['descr'];
		//echo $descr ;
		$usewiki = $_POST['usewiki'];
		$source = trim($_POST['source']);
		$screenshot_tag = trim($_POST['screenshot_tag']);
		if (empty($screenshot_tag)) {
			$screenshot_tag = null;
        }

		if ($usewiki == 'true' || empty($wiki)) {

			$descr_wiki = $descr; //
			$descr = Markdown($descr);

			$conn->query("UPDATE longreports SET report = ?, report_wiki = ?, lang = ?, reporttype = ?, source = ?, screenshot_tag = ? WHERE longreportid = ? LIMIT 1",
                array(
                        $descr, $descr_wiki, $Language, $Type, $source, $screenshot_tag,
                        $report_id ) );

		}
		else {
			$conn->query("UPDATE longreports SET report = ?, lang = ?, reporttype = ?, source = ?, screenshot_tag = ?  WHERE longreportid = ? LIMIT 1",
                array($descr, $Language, $Type, $source, $screenshot_tag,
                    $report_id) );
		}
	}

	$dt = $conn->getRow("SELECT * FROM longreports WHERE longreportid = ? LIMIT 1", array($_GET['id']));
	$game = $conn->getRow("SELECT * FROM games WHERE gameid = ?", array($dt['gameid']));

?><html>
<head>
<title><?=$softname?> v.<?=$version;?></title>
    <link rel="stylesheet" href="css/styles.css" />

</head>

<body text="#000000" bgcolor="#f0f0f0" link="#ff0000" vlink="#500000" alink="#ff8080">

<script>

</script>

	ID: <?=$dt['longreportid']?>,<br>
    <a href="/games/<?=$game['stringid']?>/"><?=$game['title']?></a><br>
    <?=$game['stringid']?><br>

 <?if ($page == 'updatelongreport'){?>UPDATED<br><?}?>
 <form action="" method="POST">
	<input type="hidden" name="page" value="updatelongreport">
	<input type="hidden" name="id" value="<?=$report_id?>">
	                      
    This description is:
    <select name="Language" size="1">
        <option value="1" <?if ($dt['lang']=='english'){?>selected<?}?>>English</option>
        <option value="2" <?if ($dt['lang']=='dutch'){?>selected<?}?>>Dutch</option>
        <option value="3" <?if ($dt['lang']=='italian'){?>selected<?}?>>Italian</option>
        <option value="4" <?if ($dt['lang']=='german'){?>selected<?}?>>German</option>
        <option value="5" <?if ($dt['lang']=='french'){?>selected<?}?>>French</option>
        <option value="6" <?if ($dt['lang']=='russian'){?>selected<?}?>>Russian</option>
    </select>

    Type:
    <select name="Type" size="1">
        <option value="additional" <?if ($dt['reporttype']=='additional'){?>selected<?}?>>Additional</option>
        <option value="main" <?if ($dt['reporttype']=='main'){?>selected<?}?>>Main</option>
    </select>

     <br>
     <br>

	<textarea name="descr" id=pstinp class=inp cols=40 rows=30 style="wrap:virtual;width:100%;height:400px"><?if(empty($dt['report_wiki'])){?><?=$dt['report']?><?}else{?><?=$dt['report_wiki']?><?}?></textarea>

    <table>
        <tr>
            <td>Source:</td>
            <td><input type="text" name="source" value="<?=$dt['source']?>" size="60" class="inp"></td>
        </tr>
        </tr>
            <td>Screenshot Tag:</td>
            <td><input type="text" name="screenshot_tag" value="<?=$dt['screenshot_tag']?>" size="50" class="inp"></td>
        </tr>
    </table>

    <br>

	<?if(empty($dt['report_wiki'])){?>
        <input type="hidden" name="wiki" value="true"><input type="checkbox" name="usewiki" value="true">&nbsp;Use wiki formatting<br><br>
    <?}else{?>
        <!--<b>Description:&nbsp;</b><?=htmlspecialchars($dt['report'])?><br><br>-->
    <?}?>

	<input type="submit" name="insert" value="      Update!      ">
 </form>

</body>
</html>