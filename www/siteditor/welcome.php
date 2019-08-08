<?
//    echo ("<p><h1>Welcome, operator</h1><p>");
?>

<h1>Добро пожаловать, <?=$logged_username?> &nbsp;&nbsp;&nbsp;(залогинен как '<?=$slogin?>')</h1>
<br><br>Права: <b><?=$logged_role?></b>
<br>Cookies:<br>
<pre>
<?print_r($_COOKIE)?>
</pre>

<br><br>
<SCRIPT type='text/javascript' language='JavaScript' src='http://xslt.alexa.com/site_stats/js/s/a?url=www.games4win.com'></SCRIPT>
<br><br>
<SCRIPT type='text/javascript' language='JavaScript' src='http://xsltcache.alexa.com/traffic_graph/js/g/a/3m?&u=www.games4win.com'></SCRIPT>